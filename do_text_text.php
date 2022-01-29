<?php

/**
 * \file
 * \brief Show text header frame
 * 
 * Call: do_text_text.php?text=[textid]
 * 
 * @author LWT Project <lwt-project@hotmail.com>
 * @since  1.0.3
 */

require_once 'inc/session_utility.php';

/**
 * Get the record for this text in the database.
 * 
 * @param  string $textid ID of the text
 * @return array{TxLgID: int, TxTitle: string, TxAnnotatedText: string, TxPosition: int}|false|null Record corresponding to this text.
 * 
 * @global string $tbpref Table name prefix
 * @since  2.0.3-fork
 */
function getTextData($textid)
{
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
 * @param  int $langid Language ID as defined in the database.
 * @return array{LgName: string, LgDict1URI: string, LgDict2URI: string, LgGoogleTranslateURI: string,
 * LgTextSize: int, LgRemoveSpaces: int, LgRightToLeft: int}|false|null Record corresponding to this language.
 * @global string $tbpref Table name prefix
 * @since  2.0.3-fork
 */
function getLanguagesSettings($langid)
{
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

/**
 * Print the output when the word is a term.
 *
 * @param int                   $actcode       Action code, > 1 for multiword
 * @param int                   $showAll       Show all words or not
 * @param int                   $hideuntil
 * @param string                $spanid        ID for this span element
 * @param int                   $currcharcount Current number of caracters
 * @param array<string, string> $record Various data
 * 
 * @return int New $hideuntil number
 * 
 * @since 2.0.3-fork
 */
function echoTerm(
    $actcode, $showAll, $hideuntil, $spanid, $hidetag, $currcharcount, $record
): int {
    if ($actcode > 1) {   
        // A MULTIWORD FOUND

        //$titext[$actcode] = $record['TiText'];

        // MULTIWORD FOUND - DISPLAY (Status 1-5, display)
        if (isset($record['WoID'])) {
            if (!$showAll && $hideuntil == -1) {             
                $hideuntil = (int)$record['Ti2Order'] + ((int)$record['Code'] - 1) * 2;
            }

            echo '<span id="' . $spanid . '" 
            class="' . $hidetag . ' click mword ' . 
            ($showAll ? 'mwsty' : 'wsty') .  
            'order' . $record['Ti2Order'] .
            'word' . $record['WoID'] . 
            'status' . $record['WoStatus'] . 
            ' TERM' . strToClassName($record['TiTextLC']) . 
            ' data_pos="' . $currcharcount . '" 
            data_order="' . $record['Ti2Order'] . '" 
            data_wid="' . $record['WoID'] . '" 
            data_trans="' . tohtml(
                repl_tab_nl($record['WoTranslation']) 
                . getWordTagList($record['WoID'], ' ', 1, 0)
            ) . '" 
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
            data_order="' . $record['Ti2Order'] . '" 
            data_wid="' . $record['WoID'] . '" 
            data_trans="' . tohtml(
                repl_tab_nl(
                    $record['WoTranslation']
                ) . getWordTagList(
                    $record['WoID'], 
                    ' ', 1, 0
                )
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
            data_order="' . $record['Ti2Order'] . '" 
            data_trans="" data_rom="" data_status="0" 
            data_wid="">' . tohtml($record['TiText']) . '</span>';
        } 

        //$titext = array('','','','','','','','','','','');

    }  // ($actcode == 1)  -- A WORD FOUND
    return $hideuntil;
}

/**
 * Process each word (can be punction, term, etc...)
 *
 * @param string[] $record        Record information
 * @param 0|1      $showAll       Show all words or not
 * @param int      $currcharcount Current number of caracters 
 * 
 * @return int New number of caracters
 * @since 2.0.3-fork
 */
function wordProcessor($record, $showAll, $currcharcount): int
{
    $hideuntil = -1;
    $cnt = 1;
    $sid = 0;

    if ($sid != $record['Ti2SeID']) {
        if ($sid != 0) {
            echo '</span>';
        }
        $sid = $record['Ti2SeID'];
        echo '<span id="sent_', $sid, '">';
    }
    $actcode = (int)$record['Code'];
    $spanid = 'ID-' . $record['Ti2Order'] . '-' . $actcode;

    // Check if work should be hidden
    $hidetag = '';
    if ($hideuntil > 0) {
        if ($record['Ti2Order'] <= $hideuntil) {
            $hidetag = ' hide'; 
        }
        else {
            $hideuntil = -1;
            $hidetag = '';
        }
    }

    if ($cnt < $record['Ti2Order']) {
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
        $hideuntil = echoTerm(
            $actcode, $showAll, $hideuntil, $spanid, $hidetag, $currcharcount, $record
        );
    } // $record['TiIsNotWord'] == 0  -- A TERM

    if ($actcode == 1) { 
        $currcharcount += $record['TiTextLength']; 
        $cnt++;
    }

    return $currcharcount;
}

/**
 * Get all words and start the iterate over them.
 *
 * @param string $textid ID of the text 
 * @param 0|1 $showAll Show all words or not
 * 
 * @return void
 * 
 * @global string $tbpref Table name prefix
 *
 * @since 2.0.3-fork
 */
function mainWordLoop($textid, $showAll): void
{
    global $tbpref;
    $currcharcount = 0;
    
    $sql = 
    'SELECT
     CASE WHEN `Ti2WordCount`>0 THEN Ti2WordCount ELSE 1 END as Code,
     CASE WHEN CHAR_LENGTH(Ti2Text)>0 THEN Ti2Text ELSE `WoText` END as TiText,
     CASE WHEN CHAR_LENGTH(Ti2Text)>0 THEN lower(Ti2Text) ELSE `WoTextLC` END as TiTextLC,
     Ti2Order, Ti2SeID, 
     CASE WHEN `Ti2WordCount`>0 THEN 0 ELSE 1 END as TiIsNotWord,
     CASE 
        WHEN CHAR_LENGTH(Ti2Text)>0 
        THEN CHAR_LENGTH(Ti2Text) 
        ELSE CHAR_LENGTH(`WoTextLC`) 
        END
     AS TiTextLength, 
     WoID, WoText, WoStatus, WoTranslation, WoRomanization
     FROM (' 
       . $tbpref . 'textitems2
        LEFT JOIN ' . $tbpref . 'words
        ON (Ti2WoID = WoID)
     )
     WHERE Ti2TxID = ' . $textid . '
     ORDER BY Ti2Order asc, Ti2WordCount desc';
    
    $res = do_mysqli_query($sql);

    // Loop over words and punctuation
    while ($record = mysqli_fetch_assoc($res)) {
        $currcharcount = wordProcessor($record, $showAll, $currcharcount);
    } // while ($record = mysql_fetch_assoc($res))  -- MAIN LOOP
    
    mysqli_free_result($res);
    echo '<span id="totalcharcount" class="hide">' . $currcharcount . '</span>';
}

/**
 * Prepare style for showing word status. Write a now STYLE object
 *
 * @since 2.0.3-fork
 */
function prepareStyle($showLearning, $mode_trans, $textsize, $ann_exists): void
{
    $displaystattrans = getSettingWithDefault('set-display-text-frame-term-translation');
    $pseudo_element = ($mode_trans<3) ? 'after' : 'before';
    $data_trans = $ann_exists ? 'data_ann' : 'data_trans';
    $stat_arr = array(1, 2, 3, 4, 5, 98, 99);
    $ruby = $mode_trans==2 || $mode_trans==4;

    echo '<style>';
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
    echo '</style>';
}

/**
 * Print JavaScript-formatted content.
 *
 * @since 2.0.3-fork
 */
function do_text_javascript($var_array): void
{
    ?>
<script type="text/javascript">
    //<![CDATA[

    /// Map global variables as a JSON object
    const vars = <?php echo json_encode($var_array); ?>;

    // Set global variables
    for (const key in vars) {
        window[key] = vars[key];
    }
    LANG = WBLINK3.replace(/.*[?&]sl=([a-zA-Z\-]*)(&.*)*$/, "$1");
    TEXTPOS = -1;
    OPENED = 0;
    // Change the language of the current frame
    if (LANG && LANG != WBLINK3) {
        $("html").attr('lang', LANG);
    }

    if (JQ_TOOLTIP) {
        $(function () {
            $('#overDiv').tooltip();
            $('#thetext').tooltip_wsty_init();
        });
    }

    $(document).ready(prepareTextInteractions);
    $(document).ready(goToLastPosition);
    $(window).on('beforeunload', saveCurrentPosition);
    //]]>
</script>
    <?php
}

/**
 * Main function for displaying sentences. It will print HTML content.
 *
 * @param string $textid    ID of the requiered text
 * @param bool   $only_body If true, only show the inner body. If false, create a complete HTML document. 
 */
function do_text_text_content($textid, $only_body=true): void
{
    // Text settings
    $record = getTextData($textid);
    $title = $record['TxTitle'];
    $langid = $record['TxLgID'];
    $ann = $record['TxAnnotatedText'];
    $ann_exists = strlen($ann) > 0;
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

    if (!$only_body) {
        // Start the page with a HEAD and opens a BODY tag 
        pagestart_nobody($title);
    }
    ?>
    <script type="text/javascript" src="js/jquery.hoverIntent.js" charset="utf-8"></script>
    <script type="text/javascript" src="js/user_interactions.js" charset="utf-8"></script>
    <?php 
    $visit_status = getSettingWithDefault('set-text-visit-statuses-via-key');
    if ($visit_status == '') {
        $visit_status = '0';
    }
    $var_array = array(
        // Change globals from jQuery hover
        'ANN_ARRAY' => json_decode(annotation_to_json($ann)),
        'DELIMITER' => tohtml(
            str_replace(
                array('\\',']','-','^'), 
                array('\\\\','\\]','\\-','\\^'), 
                getSettingWithDefault('set-term-translation-delimiters')
            )
        ),
        'WBLINK1' => $wb1,
        'WBLINK2' => $wb2,
        'WBLINK3' => $wb3,
        'RTL' => $rtlScript,
        'TID' => $textid,
        'ADDFILTER' => makeStatusClassFilter((int)$visit_status),
        'JQ_TOOLTIP' => getSettingWithDefault('set-tooltip-mode') == 2 ? 1 : 0,
        // Add new globals
        'ANNOTATIONS_MODE' => $mode_trans,
        'POS' => $pos
    );
    do_text_javascript($var_array);
    echo prepareStyle($showLearning, $mode_trans, $textsize, $ann_exists);
    ?>

    <div id="thetext" <?php echo ($rtlScript ? 'dir="rtl"' : '') ?>>
        <p style="margin-bottom: 10px;
            <?php echo $removeSpaces ? 'word-break:break-all;' : ''; ?>
            font-size: <?php echo $textsize; ?>%; 
            line-height: <?php echo $ruby?'1':'1.4'; ?>;"
        >
            <!-- Start displaying words -->
            <?php mainWordLoop($textid, $showAll); ?></span>
        </p>
        <p style="font-size:<?php echo $textsize; ?>%;line-height: 1.4; margin-bottom: 300px;">&nbsp;</p>
    </div>
    <?php 
    if (!$only_body) { 
        pageend(); 
    }
    flush();
}

// This code runs when calling this script, be careful!
if (isset($_REQUEST['text'])) {
    do_text_text_content($_REQUEST['text'], false);
}
?>
