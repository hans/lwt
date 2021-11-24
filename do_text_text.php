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

$sql 
    = '
    SELECT TxLgID, TxTitle, TxAnnotatedText, TxPosition 
    FROM ' . $tbpref . 'texts
    WHERE TxID = ' . $_REQUEST['text'];
$res = do_mysqli_query($sql);
$record = mysqli_fetch_assoc($res);
$title = $record['TxTitle'];
$langid = $record['TxLgID'];
$ann = $record['TxAnnotatedText'];
$ann_exists = (strlen($ann) > 0);
$pos = $record['TxPosition'];
mysqli_free_result($res);

pagestart_nobody(tohtml($title));

$sql 
= 
    'SELECT LgName, LgDict1URI, LgDict2URI, LgGoogleTranslateURI, 
    LgTextSize, LgRemoveSpaces, LgRightToLeft
    FROM ' . $tbpref . 'languages
    WHERE LgID = ' . $langid;
$res = do_mysqli_query($sql);
$record = mysqli_fetch_assoc($res);
$wb1 = isset($record['LgDict1URI']) ? $record['LgDict1URI'] : "";
$wb2 = isset($record['LgDict2URI']) ? $record['LgDict2URI'] : "";
$wb3 = isset($record['LgGoogleTranslateURI']) ? $record['LgGoogleTranslateURI'] : "";
$textsize = $record['LgTextSize'];
$removeSpaces = $record['LgRemoveSpaces'];
$rtlScript = $record['LgRightToLeft'];
mysqli_free_result($res);

$showAll = getSettingZeroOrOne('showallwords', 1);
$showLearning = getSettingZeroOrOne('showlearningtranslations', 1);

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
if (LANG && LANG != WBLINK3) 
    $("html").attr('lang',LANG);
RTL = <?php echo $rtlScript; ?>;
TID = '<?php echo $_REQUEST['text']; ?>';
ADDFILTER = '<?php 
echo makeStatusClassFilter(
    getSettingWithDefault('set-text-visit-statuses-via-key')
); ?>';
<?php if(getSettingWithDefault('set-tooltip-mode') == 2) { ?>
JQ_TOOLTIP = 1;
$(function() {
    $( '#overDiv' ).tooltip();
    $( "#thetext" ).tooltip_wsty_init();
});
<?php 
}
else { 
    echo 'JQ_TOOLTIP = 0;'; 
}
$mode_trans=getSettingWithDefault('set-text-frame-annotation-position');
    ?>
$(document).ready(function () {
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
});
$(document).ready(function() {
    var pos = <?php
    if ($pos>0) {
        ?>
        $(".wsty[data_pos=' . $pos . ']").not(".hide").eq(0);
        if (pos.attr("data_pos") === undefined) {
            pos = $(".wsty").not(".hide").filter(function() {
                return $(this).attr("data_pos") <= <?php echo $pos; ?>;
            }).eq(-1);
        }
        <?php
    }
    else { 
        echo '0;'; 
    }
?>
    $(document).scrollTo(pos);
    window.focus();
    window.setTimeout('overlib()', 10);
    window.setTimeout('cClick()', 100);
});

$(window).on('beforeunload', function() {
    var pos=0;
    var top=$(window).scrollTop()-$('.wsty').not('.hide').eq(0).height();
    $('.wsty').not('.hide').each(function() {
        if ($(this).offset().top>=top){
            pos=$(this).attr('data_pos');
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
});
//]]>
</script>
<?php
$data_trans = $ann_exists ? 'data_ann' : 'data_trans';
$pseudo_element = ($mode_trans<3) ? 'after' : 'before';
$ruby = ($mode_trans==2 || $mode_trans==4) ? 1 : 0;
$displaystattrans = getSettingWithDefault('set-display-text-frame-term-translation');
echo "<style>\n";
$stat_arr = array(1,2,3,4,5,98,99);
if ($showLearning) {
    foreach ($stat_arr as $value) {
        if (checkStatusRange($value, $displaystattrans)) {
            echo '.wsty.status', $value, ':', 
            $pseudo_element, ',.tword.content', $value, ':', 
            $pseudo_element,'{content: attr(',$data_trans,');}',
            "\n",'.tword.content', $value,':', 
            $pseudo_element,'{color:rgba(0,0,0,0)}',"\n"; 
        }
    }
}
if ($ruby) {
    echo '.wsty {', ($mode_trans==4?'margin-top: 0.2em;':'margin-bottom: 0.2em;'),
        'text-align: center;display: inline-block;',
        ($mode_trans==2?'vertical-align: top;':''),
        '}',"\n";
}
if ($ruby) { 
    echo '.wsty:', $pseudo_element, 
    '{display: block !important;', 
        ($mode_trans==2?'margin-top: -0.05em;':'margin-bottom:  -0.15em;'),
    '}',"\n"; 
}
$ann_textsize = array(100 => 50, 150 => 50,200 => 40, 250 => 25);
echo '.tword:', $pseudo_element, 
',.wsty:', $pseudo_element, 
'{', ($ruby?'text-align: center;':''), 
    'font-size:' . $ann_textsize[$textsize] . '%;', 
    ($mode_trans==1?'margin-left: 0.2em;':''), 
    ($mode_trans==3?'margin-right: 0.2em;':''), 
    ($ann_exists ? '' : '
    overflow: hidden; 
    white-space: nowrap;
    text-overflow: ellipsis;
    display: inline-block;
    vertical-align: -25%;'),
    '}',"\n",'.hide{display:none !important;}.tword:',
    $pseudo_element,($ruby?',.word:':',.wsty:'),
    $pseudo_element,'{max-width:15em;}</style>';

echo '<div 
id="thetext" ' . 
($rtlScript ? 'dir="rtl"' : '') . '>
<p style="' . ($removeSpaces ? 'word-break:break-all;' : '') .
'font-size:' . $textsize . '%;line-height: ',($ruby?'1':'1.4'),'; margin-bottom: 10px;">';

$currcharcount = 0;

$sql = 
'SELECT
 CASE WHEN `Ti2WordCount`>0 THEN Ti2WordCount ELSE 1 END as Code,
 CASE WHEN CHAR_LENGTH(Ti2Text)>0 THEN Ti2Text ELSE `WoText` END as TiText,
 CASE WHEN CHAR_LENGTH(Ti2Text)>0 THEN lower(Ti2Text) ELSE `WoTextLC` END as TiTextLC,
 Ti2Order as TiOrder, Ti2SeID as TiSeID, 
 CASE WHEN `Ti2WordCount`>0 THEN 0 ELSE 1 END as TiIsNotWord,
 CASE WHEN CHAR_LENGTH(Ti2Text)>0 THEN CHAR_LENGTH(Ti2Text) ELSE CHAR_LENGTH(`WoTextLC`) END
 AS TiTextLength, WoID, WoText, WoStatus, WoTranslation, WoRomanization
 FROM (' 
   . $tbpref . 'textitems2
    LEFT JOIN ' . $tbpref . 'words
    ON (Ti2WoID = WoID)
 )
 WHERE Ti2TxID = ' . $_REQUEST['text'] . '
 ORDER BY Ti2Order asc, Ti2WordCount desc';

$hideuntil = -1;
$hidetag = '';
$cnt = 1;
$sid = 0;

$res = do_mysqli_query($sql);

// Loop over words and punctuation
while ($record = mysqli_fetch_assoc($res)) {
    if ($sid != $record['TiSeID']) {
        if($sid != 0) {
            echo '</span>';
        }
        $sid = $record['TiSeID'];
        echo '<span id="sent_',$sid,'">';
    }
    $actcode = $record['Code'] + 0;
    $spanid = 'ID-' . $record['TiOrder'] . '-' . $actcode;

    if ($hideuntil > 0) {
        if ($record['TiOrder'] <= $hideuntil) {
            $hidetag = ' hide'; 
        }
        else {
            $hideuntil = -1;
            $hidetag = '';
        }
    }

    if ($cnt<$record['TiOrder']) {
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

        if ($actcode > 1) {   // A MULTIWORD FOUND

            //$titext[$actcode] = $record['TiText'];

            // MULTIWORD FOUND - DISPLAY (Status 1-5, display)
            if (isset($record['WoID'])) {
                if (! $showAll) {
                    if ($hideuntil == -1) {
                        $hideuntil = $record['TiOrder'] + ($record['Code'] - 1) * 2;
                    }
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

    } // $record['TiIsNotWord'] == 0  -- A TERM

    if ($actcode == 1) { 
        $currcharcount += $record['TiTextLength']; 
        $cnt++;
    }

} // while ($record = mysql_fetch_assoc($res))  -- MAIN LOOP

mysqli_free_result($res);
echo '</span>
    <span id="totalcharcount" class="hide">' . $currcharcount . '</span></p>
    <p style="font-size:' . $textsize . '%;line-height: 1.4; margin-bottom: 300px;">&nbsp;</p>
</div>';

pageend();

?>
