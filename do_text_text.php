<?php

/**
 * \file
 * \brief Show text header frame
 * 
 * Call: do_text_text.php?text=[textid]
 * 
 * @author LWT Project <lwt-project@notmail.com>
 * @since 1.0.3
*/

require_once 'inc/session_utility.php';

/**
 * Get the record for this text in the database.
 * 
 * @param string $textid ID of the text
 * @return string[]|null|false Record corresponding to this text.
 * @global string $tbpref Table name prefix
 * @since 2.0.3-fork
 */
function getTextData($textid) {
    global $tbpref;
    $sql = 
    'SELECT TxLgID, TxTitle, TxAnnotatedText, TxPosition 
    FROM ' . $tbpref . 'texts
    WHERE TxID = ' . $textid;
    $res = do_mysqli_query($sql);
    $record = mysqli_fetch_assoc($res);
    mysqli_free_result($res);
    return $record;
}


/**
 * Return the settings relative to this language.
 * 
 * @param string $langid Language ID as defined in the database.
 * @global string $tbpref Table name prefix
 * @return string[]|null|false Record corresponding to this language.
 * @since 2.0.3-fork
 * 
 */
function getLanguagesSettings($langid) {
    global $tbpref;
    $sql = 
    'SELECT LgName, LgDict1URI, LgDict2URI, LgGoogleTranslateURI, 
    LgTextSize, LgRemoveSpaces, LgRightToLeft
    FROM ' . $tbpref . 'languages
    WHERE LgID = ' . $langid;
    $res = do_mysqli_query($sql);
    $record = mysqli_fetch_assoc($res);
    mysqli_free_result($res);
    return $record;
}

// Text settings
$record = getTextData($_REQUEST['text']);
$title = $record['TxTitle'];
$langid = $record['TxLgID'];
$ann = $record['TxAnnotatedText'];
$ann_exists = (strlen($ann) > 0);
$pos = $record['TxPosition'];

// Language settings
$record = getLanguagesSettings($langid);
$wb1 = isset($record['LgDict1URI']) ? $record['LgDict1URI'] : "";
$wb2 = isset($record['LgDict2URI']) ? $record['LgDict2URI'] : "";
$wb3 = isset($record['LgGoogleTranslateURI']) ? $record['LgGoogleTranslateURI'] : "";
$textsize = $record['LgTextSize'];
$removeSpaces = $record['LgRemoveSpaces'];
$rtlScript = $record['LgRightToLeft'];

// User settings
$showAll = getSettingZeroOrOne('showallwords', 1);
$showLearning = getSettingZeroOrOne('showlearningtranslations', 1);

/**
 * @var int $mode_trans Annotation position between 0 and 4
 */
$mode_trans = getSettingWithDefault('set-text-frame-annotation-position');
/**
 * @var bool $ruby Ruby annotations
 */
$ruby = $mode_trans==2 || $mode_trans==4;

// Start the page
pagestart_nobody(tohtml($title));

?>
<script type="text/javascript" src="js/jquery.hoverIntent.js" charset="utf-8">
</script>
<script type="text/javascript">
//<![CDATA[
ANN_ARRAY = <?php echo annotation_to_json($ann); ?>;
DELIMITER = '<?php 
echo tohtml(
    str_replace(
        array('\\',']','-','^'), 
        array('\\\\','\\]','\\-','\\^'), 
        getSettingWithDefault('set-term-translation-delimiters')
    )
); ?>';
TEXTPOS = -1;
OPENED = 0;
WBLINK1 = '<?php echo $wb1; ?>';
WBLINK2 = '<?php echo $wb2; ?>';
WBLINK3 = '<?php echo $wb3; ?>';
LANG = WBLINK3.replace(/.*[?&]sl=([a-zA-Z\-]*)(&.*)*$/, "$1");
if (LANG && LANG != WBLINK3) {
    $("html").attr('lang', LANG);
}
RTL = <?php echo $rtlScript; ?>;
TID = '<?php echo $_REQUEST['text']; ?>';
ADDFILTER = '<?php 
echo makeStatusClassFilter(
    getSettingWithDefault('set-text-visit-statuses-via-key')
); ?>';
JQ_TOOLTIP = <?php echo getSettingWithDefault('set-tooltip-mode') == 2 ? 1 : 0 ?>;

if (JQ_TOOLTIP) {
    $(function () {
        $('#overDiv').tooltip();
        $('#thetext').tooltip_wsty_init();
    });
}

/** 
 * Prepare the interaction events
 * @since 2.0.3-fork
 */
function prepareInteractions() {
    $('.word').each(word_each_do_text_text);
    $('.mword').each(mword_each_do_text_text);
    $('.word').click(word_click_event_do_text_text);
    $('#thetext').on('selectstart','span',false).on(
        'mousedown','.wsty',
        {annotation: <?php echo $mode_trans; ?>}, 
        mword_drag_n_drop_select);
    $('#thetext').on('click', '.mword', mword_click_event_do_text_text);
    $('.word').dblclick(word_dblclick_event_do_text_text);
    $('#thetext').on('dblclick', '.mword', word_dblclick_event_do_text_text);
    $(document).keydown(keydown_event_do_text_text);
    $('#thetext').hoverIntent(
        {
            over: word_hover_over, 
            out: word_hover_out, 
            interval: 150, 
            selector:".wsty,.mwsty"
        }
    );
}


/** 
 * Scroll to a specific reading position
 * @since 2.0.3-fork
 */
function goToLastPosition() {
    // Last registered position to go to
    const lookPos = <?php echo $pos; ?>;
    // Position to scroll to
    let pos = 0;
    if (lookPos > 0) {
        let posObj = $(".wsty[data_pos=" + lookPos + "]").not(".hide").eq(0);
        if (posObj.attr("data_pos") === undefined) {
            pos = $(".wsty").not(".hide").filter(function() {
                return $(this).attr("data_pos") <= lookPos;
            }).eq(-1);
        }
    }
    $(document).scrollTo(pos);
    window.focus();
    window.setTimeout('overlib()', 10);
    window.setTimeout('cClick()', 100);
}

/**
 * Save the current reading position.
 * @since 2.0.3-fork
 */
function saveCurrentPosition() {
    var pos = 0;
    var top = $(window).scrollTop()-$('.wsty').not('.hide').eq(0).height();
    $('.wsty').not('.hide').each(function() {
        if ($(this).offset().top >= top){
            pos = $(this).attr('data_pos');
            return false;
        }
    });
    $.ajax(
        {
            type: "POST",
            url:'inc/ajax_save_text_position.php', 
            data: { 
                id: '<?php echo $_REQUEST['text']; ?>', 
                position: pos 
            }, 
            async: false
        }
    );
}

$(document).ready(prepareInteractions);
$(document).ready(goToLastPosition);

$(window).on('beforeunload', saveCurrentPosition);
//]]>
</script>
<?php

/**
 * Print the output when the word is a term.
 * 
 * @since 2.0.3-fork
 */
function echoTerm(
    $actcode, $showAll, &$hideuntil, $spanid, $hidetag, $currcharcount, $record
    ) {
    if ($actcode > 1) {   
        // A MULTIWORD FOUND

        //$titext[$actcode] = $record['TiText'];

        // MULTIWORD FOUND - DISPLAY (Status 1-5, display)
        if (isset($record['WoID'])) {
            if (!$showAll && $hideuntil == -1) {             
                $hideuntil = $record['TiOrder'] + ($record['Code'] - 1) * 2;
            }

            echo '<span id="' . $spanid . '" 
            class="' . $hidetag . ' click mword ' . 
            ($showAll ? 'mwsty' : 'wsty') .  
            'order' . $record['TiOrder'] .
            'word' . $record['WoID'] . 
            'status' . $record['WoStatus'] . 
            ' TERM' . strToClassName($record['TiTextLC']) . 
            ' data_pos="' . $currcharcount . '" 
            data_order="' . $record['TiOrder'] . '" 
            data_wid="' . $record['WoID'] . '" 
            data_trans="' . tohtml(repl_tab_nl($record['WoTranslation']) 
            . getWordTagList($record['WoID'], ' ', 1, 0)) . '" 
            data_rom="' . tohtml($record['WoRomanization']) . '" 
            data_status="' . $record['WoStatus'] . '"  
            data_code="' . $record['Code'] . '" 
            data_text="' . tohtml($record['TiText']); '">'; 
            if ($showAll) {
                echo '&nbsp;' . $record['Code'] . '&nbsp;';
            } else {
                echo tohtml($record['TiText']);
            }
            echo '</span>';
        }
    } else {  
        // ($actcode == 1)  -- A WORD FOUND

        if (isset($record['WoID'])) {  // WORD FOUND STATUS 1-5,98,99

            echo '<span 
            id="' . $spanid . '" 
            class="' . $hidetag . 
            ' click word wsty word'. $record['WoID'] . 
            ' status'. $record['WoStatus'] . 
            ' TERM' . strToClassName($record['TiTextLC']) . '" 
            data_pos="' . $currcharcount . '" 
            data_order="' . $record['TiOrder'] . '" 
            data_wid="' . $record['WoID'] . '" 
            data_trans="' . tohtml(
                repl_tab_nl(
                    $record['WoTranslation']) . getWordTagList($record['WoID'], 
                    ' ', 1, 0)
            ) . '" 
            data_rom="' . tohtml($record['WoRomanization']) . '" 
            data_status="' . $record['WoStatus'] . '">' 
            . tohtml($record['TiText']) . 
            '</span>';
        } else {
            // NOT A WORD AND NOT A MULTIWORD FOUND - STATUS 0
            echo '<span 
            id="' . $spanid . '" 
            class="' . $hidetag . 
            ' click word wsty status0 TERM' . strToClassName($record['TiTextLC']) . '" 
            data_pos="' . $currcharcount . '" 
            data_order="' . $record['TiOrder'] . '" 
            data_trans="" data_rom="" data_status="0" 
            data_wid="">' . tohtml($record['TiText']) . '</span>';
        } 

        //$titext = array('','','','','','','','','','','');

    }  // ($actcode == 1)  -- A WORD FOUND

}

/**
 * Process each word (can be punction, term, etc...)
 * 
 * @since 2.0.3-fork
 */
function wordProcessor(&$sid, $record, &$hideuntil, $showAll, &$cnt, &$currcharcount) {
    if ($sid != $record['TiSeID']) {
        if ($sid != 0) {
            echo '</span>';
        }
        $sid = $record['TiSeID'];
        echo '<span id="sent_', $sid, '">';
    }
    $actcode = $record['Code'] + 0;
    $spanid = 'ID-' . $record['TiOrder'] . '-' . $actcode;

    // Check if work should be hidden
    $hidetag = '';
    if ($hideuntil > 0) {
        if ($record['TiOrder'] <= $hideuntil) {
            $hidetag = ' hide'; 
        }
        else {
            $hideuntil = -1;
            $hidetag = '';
        }
    }

    if ($cnt < $record['TiOrder']) {
        echo '<span id="ID-' . $cnt++ . '-1"></span>';
    }
    // The current word is not a term
    if ($record['TiIsNotWord'] != 0) {
        echo '<span id="' . $spanid . '" class="' .
        $hidetag . '">' .
        str_replace(
            "¶",
            '<br />',
            tohtml($record['TiText'])
        ) . '</span>';

    } else {   
        // $record['TiIsNotWord'] == 0  -- A TERM
        echoTerm(
            $actcode, $showAll, $hideuntil, $spanid, $hidetag, $currcharcount, $record
        );
    } // $record['TiIsNotWord'] == 0  -- A TERM

    if ($actcode == 1) { 
        $currcharcount += $record['TiTextLength']; 
        $cnt++;
    }

}

/**
 * Get all words and start the iterate over them.
 * 
 * @global string $tbpref Table name prefix
 * @since 2.0.3-fork
 */
function mainWordLoop($showAll) {
    global $tbpref;
    $currcharcount = 0;
    
    $sql = 
    'SELECT
     CASE WHEN `Ti2WordCount`>0 THEN Ti2WordCount ELSE 1 END as Code,
     CASE WHEN CHAR_LENGTH(Ti2Text)>0 THEN Ti2Text ELSE `WoText` END as TiText,
     CASE WHEN CHAR_LENGTH(Ti2Text)>0 THEN lower(Ti2Text) ELSE `WoTextLC` END as TiTextLC,
     Ti2Order as TiOrder, Ti2SeID as TiSeID, 
     CASE WHEN `Ti2WordCount`>0 THEN 0 ELSE 1 END as TiIsNotWord,
     CASE 
        WHEN CHAR_LENGTH(Ti2Text)>0 
        THEN CHAR_LENGTH(Ti2Text) 
        ELSE CHAR_LENGTH(`WoTextLC`) 
        END
     AS TiTextLength, WoID, WoText, WoStatus, WoTranslation, WoRomanization
     FROM (' 
       . $tbpref . 'textitems2
        LEFT JOIN ' . $tbpref . 'words
        ON (Ti2WoID = WoID)
     )
     WHERE Ti2TxID = ' . $_REQUEST['text'] . '
     ORDER BY Ti2Order asc, Ti2WordCount desc';
    
    $hideuntil = -1;
    $cnt = 1;
    $sid = 0;
    
    $res = do_mysqli_query($sql);

    // Loop over words and punctuation
    while ($record = mysqli_fetch_assoc($res)) {
        wordProcessor($sid, $record, $hideuntil, $showAll, $cnt, $currcharcount);
    
    } // while ($record = mysql_fetch_assoc($res))  -- MAIN LOOP
    
    mysqli_free_result($res);
}

/**
 * Prepare style for showing word status.
 * 
 * @return string CSS-formatted style
 * @since 2.0.3-fork
 */
function prepareStyle($showLearning, $mode_trans, $textsize, $ann_exists, $ruby) {
    $displaystattrans = getSettingWithDefault('set-display-text-frame-term-translation');
    $pseudo_element = ($mode_trans<3) ? 'after' : 'before';
    $data_trans = $ann_exists ? 'data_ann' : 'data_trans';
    $stat_arr = array(1, 2, 3, 4, 5, 98, 99);
    if ($showLearning) {
        foreach ($stat_arr as $value) {
            if (checkStatusRange($value, $displaystattrans)) {
                echo '.wsty.status', $value, ':', 
                $pseudo_element, ',.tword.content', $value, ':', 
                $pseudo_element,'{content: attr(',$data_trans,');}';
                echo '.tword.content', $value,':', 
                $pseudo_element,'{color:rgba(0,0,0,0)}',"\n"; 
            }
        }
    }
    if ($ruby) {
        echo '.wsty {', 
            ($mode_trans==4?'margin-top: 0.2em;':'margin-bottom: 0.2em;'),
            'text-align: center;
            display: inline-block;',
            ($mode_trans==2?'vertical-align: top;':''),
            '}',"\n";
            
        echo '.wsty:', $pseudo_element, 
        '{
            display: block !important;',
            ($mode_trans==2?'margin-top: -0.05em;':'margin-bottom: -0.15em;'),
        '}',"\n"; 
    }
    $ann_textsize = array(100 => 50, 150 => 50, 200 => 40, 250 => 25);
    echo '.tword:', $pseudo_element, 
    ',.wsty:', $pseudo_element, 
    '{', 
        ($ruby?'text-align: center;':''), 
        'font-size:' . $ann_textsize[$textsize] . '%;', 
        ($mode_trans==1 ? 'margin-left: 0.2em;':''), 
        ($mode_trans==3 ? 'margin-right: 0.2em;':''), 
        ($ann_exists ? '' : '
        overflow: hidden; 
        white-space: nowrap;
        text-overflow: ellipsis;
        display: inline-block;
        vertical-align: -25%;'),
    '}';
    
    echo '.hide {'.
        'display:none !important;
    }';
    echo '.tword:',
    $pseudo_element, ($ruby?',.word:':',.wsty:'),
    $pseudo_element, '{max-width:15em;}';
}

echo "<style>\n";
echo prepareStyle($showLearning, $mode_trans, $textsize, $ann_exists, $ruby);
echo '</style>';

echo '<div 
id="thetext" ' . 
($rtlScript ? 'dir="rtl"' : '') . '>
<p style="' . ($removeSpaces ? 'word-break:break-all;' : '') .
'font-size:' . $textsize . '%;line-height: ',($ruby?'1':'1.4'),'; margin-bottom: 10px;">';

// Start displaying words
mainWordLoop($showAll);

echo '</span>
    <span id="totalcharcount" class="hide">' . $currcharcount . '</span></p>
    <p style="font-size:' . $textsize . '%;line-height: 1.4; margin-bottom: 300px;">&nbsp;</p>
</div>';

pageend();

?>
