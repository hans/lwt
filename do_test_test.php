<?php

/**
 * \file
 * \brief Show test frame
 * 
 * Call: do_test_test.php?type=[testtype]&lang=[langid]
 * Call: do_test_test.php?type=[testtype]&text=[textid]
 * Call: do_test_test.php?type=[testtype]&selection=1  
 *          (SQL via $_SESSION['testsql'])
 * 
 * @package Lwt
 * @author  LWT Project <lwt-project@hotmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/do__test__test_8php.html
 * @since   1.0.3
 */

require_once 'inc/session_utility.php';
require_once 'inc/langdefs.php';


/**
 * Set sql request for the word test.
 * 
 * @return string SQL request string
 * 
 * @global string $tbpref Table prefix
 */
function get_test_sql()
{
    global $tbpref;
    if (isset($_REQUEST['selection']) && isset($_SESSION['testsql'])) { 
        $testsql = $_SESSION['testsql'];
        $cntlang = get_first_value('SELECT count(distinct WoLgID) AS value FROM ' . $testsql);
        if ($cntlang > 1) {
            //pagestart('', false);
            echo '<p>Sorry - The selected terms are in ' . $cntlang . ' languages, but tests are only possible in one language at a time.</p>';
            //pageend();
            exit();
        }
    } else if (isset($_REQUEST['lang'])) {
        $testsql = ' ' . $tbpref . 'words where WoLgID = ' . $_REQUEST['lang'] . ' ';
    } else if (isset($_REQUEST['text'])) {
        $testsql = ' ' . $tbpref . 'words, ' . $tbpref . 'textitems2 
        WHERE Ti2LgID = WoLgID AND Ti2WoID = WoID AND Ti2TxID = ' . $_REQUEST['text'] . ' ';
    } else { 
        //$testsql = '';
        //$title = 'Request Error!';
        //pagestart($title, true);
        my_die("do_test_test.php called with wrong parameters"); 
    }
    return $testsql;
}

/**
 * Give the test type.
 * 
 * @return int<1, 5> Test type between 1 and 5 (included)
 */
function get_test_type()
{
    $testtype = (int)getreq('type');
    if ($testtype < 1) { 
        $testtype = 1; 
    }
    if ($testtype > 5) { 
        $testtype = 5; 
    }
    return $testtype;
}

/**
 * Prepare the css code for tests.
 * 
 * @return void
 */
function do_test_test_css()
{
    ?>
<style type="text/css">
    html, body {
        width:100%; 
        height:100%; 
    } 

    html {
        display:table;
    } 
    body { 
        display:table-cell; 
        vertical-align:middle; 
    } 
    #body { 
        max-width:95%; 
        margin:0 auto; 
    }
</style>
    <?php
}

/**
 * Output a message for a finished test, with the number of tests for tomorrow.
 * 
 * @param string $testsql    Query used to select words.
 * @param int    $totaltests Total number of tests.
 * 
 * @return void
 */
function do_test_test_finished($testsql, $totaltests)
{
    $count2 = get_first_value(
        'SELECT count(distinct WoID) AS value 
        FROM ' . $testsql . ' AND WoStatus BETWEEN 1 AND 5 
        AND WoTranslation != \'\' AND WoTranslation != \'*\' AND WoTomorrowScore < 0'
    );
    echo '<p class="center">
            <img src="img/ok.png" alt="Done!" />
            <br /><br />
            <span class="red2">
                Nothing ' . ($totaltests ? 'more ' : '') . 'to test here!
                <br /><br />
                Tomorrow you\'ll find here ' . $count2 . ' test' . ($count2 == 1 ? '' : 's') . '!
            </span>
        </p>
    </div>';
}

/**
 * Get a sentence containing the word.
 * 
 * @param int    $wid    The word to test.
 * @param string $lang   ID of the language
 * @param string $wordlc 
 * 
 * @global string $tbpref Table prefix
 * @global int    $debug  Echo the passage number if 1. 
 * 
 * @return array{0: int, 1: int} Sentence with escaped word and a confirmation number if sentence was found.
 */
function do_test_test_sentence($wid, $lang, $wordlc)
{
    global $debug, $tbpref;
    $pass = 0;
    $sentexcl = '';
    $num = null;
    $sent = null;
    while ($pass < 3) {
        $pass++;
        if ($debug) { 
            echo "DEBUG search sent: pass: $pass <br />"; 
        }
        $sql = 'SELECT DISTINCT SeID 
        FROM ' . $tbpref . 'sentences, ' . $tbpref . 'textitems2 
        WHERE Ti2WoID = ' . $wid . $sentexcl . ' AND SeID = Ti2SeID AND SeLgID = ' . $lang . ' 
        ORDER BY rand() LIMIT 1';
        $res = do_mysqli_query($sql);
        $record = mysqli_fetch_assoc($res);
        if ($record) {  // random sent found
            $num = 1;
            $seid = $record['SeID'];
            if (areUnknownWordsInSentence($seid)) {
                if ($debug) { 
                    echo "DEBUG sent: $seid has unknown words<br />"; 
                }
                $sentexcl = ' AND SeID != ' . $seid . ' ';
                $num = 0;
                // not yet found, $num == 0 (unknown words in sent)
            } else {
                // echo ' OK ';
                list($_, $sent) = getSentence(
                    $seid, $wordlc, (int)getSettingWithDefault('set-test-sentence-count')
                );
                if ($debug) { 
                    echo "DEBUG sent: $seid OK: $sent <br />"; 
                }
                $pass = 3;
                // found, $num == 1
            }
        } else {  // no random sent found
            $num = 0;
            $pass = 3;
            if ($debug) { 
                echo "DEBUG no random sent found<br />"; 
            }
            // no sent. take term sent. $num == 0
        }
        mysqli_free_result($res);
    } // while ( $pass < 3 )
    return array($sent, $num);
}

/**
 * Echo the test relative to a word.
 * 
 * @param array  $wo_record Query from the database regarding a word.
 * @param string $sent      Sentence containing the word.
 * @param int    $testtype  Type of test
 * @param int    $nosent    1 if you want to hide sentences.
 * @param string $regexword Regex to select the desired word.
 * 
 * @return array{0: string, 1: string} HTML-escaped and raw text sentences (or word)
 */
function print_term_test($wo_record, $sent, $testtype, $nosent, $regexword)
{
    $wid = $wo_record['WoID'];
    $word = $wo_record['WoText'];
    $trans = repl_tab_nl($wo_record['WoTranslation']) . getWordTagList($wid, ' ', 1, 0);
    $roman = $wo_record['WoRomanization'];
    $status = $wo_record['WoStatus'];

    $cleansent = trim(str_replace("{", '', str_replace("}", '', $sent)));
    $l = mb_strlen($sent, 'utf-8');
    $r = '';
    $save = '';
    $on = 0;
    for ($i=0; $i < $l; $i++) {  // go thru sent
        $c = mb_substr($sent, $i, 1, 'UTF-8');
        if ($c == '}') {
            $r .= ' <span style="word-break:normal;" class="click todo todosty word wsty word' 
            . $wid . 
            '" data_wid="' . $wid . '" data_trans="' . tohtml($trans) . 
            '" data_text="' . tohtml($word) . '" data_rom="' . tohtml($roman) . 
            '" data_sent="' . tohtml($cleansent) . '" data_status="' . $status . 
            '" data_todo="1"';
            if ($testtype ==3) { 
                $r .= ' title="' . tohtml($trans) . '"'; 
            } 
            $r .= '>';
            if ($testtype == 2) {
                if ($nosent) { 
                    $r .= tohtml($trans); 
                }
                else { 
                    $r .= '<span dir="ltr">[' . tohtml($trans) . ']</span>'; 
                }
            }
            elseif ($testtype == 3) { 
                $r .= tohtml(
                    str_replace(
                        "{", '[', str_replace(
                            "}", ']', 
                            mask_term_in_sentence(
                                '{' . $save . '}',
                                $regexword
                            )    
                        )
                    )
                );
            } else { 
                $r .= tohtml($save); 
            }
            $r .= '</span> ';
            $on = 0;
        }
        elseif ($c == '{') {
            $on = 1;
            $save = '';
        }
        else {
            if ($on) { 
                $save .= $c; 
            }
            else { 
                $r .= tohtml($c); 
            }
        }
    } // for: go thru sent
    return array($r, $save);
}

/**
 * Preforms the HTML of the test area.
 *
 * @param string $testsql    SQL query of for the words that should be tested.
 * @param int    $totaltests Total number of tests to do.
 * @param int    $count      Number of tests left.
 * @param int    $testtype   Type of test.
 *
 * @return int Number of tests left to do.
 *
 * @global string $tbpref Table prefix 
 * @global int    $debug  Show the SQL query used if 1.
 *
 * @psalm-return int<0, max>
 */
function prepare_test_area($testsql, $totaltests, $count, $testtype): int
{
    global $tbpref, $debug;
    $nosent = 0;
    if ($testtype > 3) {
        $testtype -= 3;
        $nosent = 1;
    }

    echo '<div id="body">';

    if ($count <= 0) {
        do_test_test_finished($testsql, $totaltests);
        return 0;
    } 

    $lang = get_first_value('SELECT WoLgID AS value FROM ' . $testsql . ' LIMIT 1');
    
    $sql = 'SELECT LgName, LgDict1URI, LgDict2URI, LgGoogleTranslateURI, LgTextSize, 
    LgRemoveSpaces, LgRegexpWordCharacters, LgRightToLeft 
    FROM ' . $tbpref . 'languages WHERE LgID = ' . $lang;
    $res = do_mysqli_query($sql);
    $record = mysqli_fetch_assoc($res);
    $wb1 = isset($record['LgDict1URI']) ? $record['LgDict1URI'] : "";
    $wb2 = isset($record['LgDict2URI']) ? $record['LgDict2URI'] : "";
    $wb3 = isset($record['LgGoogleTranslateURI']) ? $record['LgGoogleTranslateURI'] : "";
    $textsize = $record['LgTextSize'];
    $removeSpaces = $record['LgRemoveSpaces'];
    $regexword = $record['LgRegexpWordCharacters'];
    $rtlScript = $record['LgRightToLeft'];
    mysqli_free_result($res);
    
    // Find the next word to test
    
    $pass = 0;
    $num = 0;
    $notvalid = null;
    $sent = null;
    $wid = null;
    $word = null;
    $wordlc = null;
    while ($pass < 2) {
        $pass++;
        $sql = "SELECT DISTINCT WoID, WoText, WoTextLC, WoTranslation, WoRomanization, 
        WoSentence, WoLgID, 
        (IFNULL(WoSentence,'') NOT LIKE CONCAT('%{',WoText,'}%')) AS notvalid, WoStatus, 
        DATEDIFF( NOW( ), WoStatusChanged ) AS Days, WoTodayScore AS Score 
        FROM " . $testsql . " AND WoStatus BETWEEN 1 AND 5 
        AND WoTranslation != '' AND WoTranslation != '*' 
        AND WoTodayScore < 0 " . ($pass == 1 ? 'AND WoRandom > RAND()' : '') . ' 
        ORDER BY WoTodayScore, WoRandom LIMIT 1';
        if ($debug) { 
            echo 'DEBUG TEST-SQL: ' . $sql . '<br />'; 
        }
        $res = do_mysqli_query($sql);
        $record = mysqli_fetch_assoc($res);
        if ($record) {
            $num = 1;
            $wid = $record['WoID'];
            $word = $record['WoText'];
            $wordlc = $record['WoTextLC'];
            $sent = repl_tab_nl($record['WoSentence']);
            $notvalid = $record['notvalid'];
            $pass = 2;
        }
        mysqli_free_result($res);
    }
    
    if ($num == 0) {
        // should not occur but...
        do_test_test_finished($testsql, $totaltests);
        return 0;
    }

    if ($nosent) {  // No sent. mode 4+5
        $num = 0;
        $notvalid = 1;
    } else { // $nosent == FALSE, mode 1-3
        list($sent, $num) = do_test_test_sentence($wid, $lang, $wordlc);
    }  // $nosent == FALSE

    if ($num == 0) {
        // take term sent. if valid
        if ($notvalid) {
            $sent = '{' . $word . '}'; 
             
        }
        if ($debug) { 
            echo "DEBUG not found, use sent = $sent<br />"; 
        }
    }
    
    
    echo '<p ' . ($rtlScript ? 'dir="rtl"' : '') . 
    ' style="' . ($removeSpaces ? 'word-break:break-all;' : '') . 
    'font-size:' . $textsize . '%;
    line-height: 1.4; text-align:center; margin-bottom:300px;">';
    
    list($r, $save) = print_term_test($record, $sent, $testtype, $nosent, $regexword);
    
    echo $r;  // Show Sentence
    
    do_test_test_javascript_interaction($record, $wb1, $wb2, $wb3, $testtype, $nosent, $save);

    echo '</p></div>';

    return $count;
}

/**
 * Prepare JavaScript code so that you can click on words.
 * 
 * @param array  $wo_record Word record. Associative array with keys 'WoID', 'WoTranslation'.
 * @param string $wb1       URL of the first dictionary.
 * @param string $wb2       URL of the secondary dictionary.
 * @param string $wb3       URL of the google translate dictionary.
 * @param int    $testtype  Type of test
 * @param int    $nosent    1 to use single word instead of sentence.
 * @param string $save      Word or sentence to use for the test
 * 
 * @return void
 * 
 * @global string $tbpref  Database table prefix
 * @global string $angDefs Languages definition array
 */
function do_test_test_javascript_interaction($wo_record, $wb1, $wb2, $wb3, $testtype, $nosent, $save)
{
    global $tbpref, $langDefs;

    $wid = $wo_record['WoID'];
    $trans = repl_tab_nl($wo_record['WoTranslation']) . getWordTagList($wid, ' ', 1, 0);
    $lang = get_first_value(
        'SELECT LgName AS value FROM ' . $tbpref . 'languages
        WHERE LgID = ' . $wo_record['WoLgID'] . '
        LIMIT 1'        
    );
    $abbr = $langDefs[$lang][1];
    $phoneticText = phonetic_reading($wo_record['WoText'], $abbr);
    ?>
<script type="text/javascript">
    //<![CDATA[

    /** 
     * Read the word aloud
     */
    function read_word() {
        if (('speechSynthesis' in window) && 
        document.getElementById('utterance-allowed').checked) {
            const text = <?php echo json_encode($phoneticText); ?>;
            const lang = <?php echo json_encode($abbr); ?>;
            readRawTextAloud(text, lang);
            /*let msg = new SpeechSynthesisUtterance(text);
            msg.text = text;
            msg.lang = lang;
            msg.rate = 0.8;
            speechSynthesis.speak(msg);*/
        }
    }

    WBLINK1 = '<?php echo $wb1; ?>';
    WBLINK2 = '<?php echo $wb2; ?>';
    WBLINK3 = '<?php echo $wb3; ?>';
    LANG = getLangFromDict(WBLINK3);
    if (LANG && LANG != WBLINK3) {
        $("html").attr('lang', LANG);
    }
    SOLUTION = <?php
    if ($testtype == 1) {
        echo prepare_textdata_js($nosent ? $trans : (' [' . $trans . '] '));
    } else {
        echo prepare_textdata_js($save);
    }
    ?>;
    OPENED = 0;
    WID = <?php echo $wid; ?>;
    $(document).ready(function() {
        $(document).on('keydown', keydown_event_do_test_test);
        $('.word')
        .on('click', word_click_event_do_test_test)
        .on('click', read_word);
    });
    //]]>
</script>
    <?php
}

/**
 * Get the data and echoes the footer.
 * 
 * @param int $notyettested Number of words left to be tested.
 * 
 * @return void
 */
function prepare_test_footer($notyettested)
{
    $wrong = $_SESSION['testwrong'];
    $correct = $_SESSION['testcorrect'];
    do_test_footer($notyettested, $wrong, $correct);
}

/**
 * Echoes HTML code for the footer of a words test page.
 * 
 * @param int $notyettested Number of words left to be tested
 * @param int $wrong        Number of failed tests
 * @param int $correct      Number of correct answers.
 * 
 * @return void
 */
function do_test_footer($notyettested, $wrong, $correct)
{
    $totaltests = $wrong + $correct + $notyettested;
    $totaltestsdiv = 1;
    if ($totaltests > 0) { 
        $totaltestsdiv = 1.0/$totaltests; 
    }
    $l_notyet = round(($notyettested * $totaltestsdiv)*100, 0);
    $b_notyet = ($l_notyet == 0) ? '' : 'borderl';
    $l_wrong = round(($wrong * $totaltestsdiv)*100, 0);
    $b_wrong = ($l_wrong == 0) ? '' : 'borderl';
    $l_correct = round(($correct * $totaltestsdiv)*100, 0);
    $b_correct = ($l_correct == 0) ? 'borderr' : 'borderl borderr';
    ?>
<footer id="footer">
    <img src="icn/clock.png" title="Elapsed Time" alt="Elapsed Time" />
    <span id="timer" title="Elapsed Time"></span>
    &nbsp; &nbsp; &nbsp; 
    <img 
    class="<?php echo $b_notyet; ?>" src="<?php print_file_path('icn/test_notyet.png');?>" 
    title="Not yet tested" alt="Not yet tested" height="10" width="<?php echo $l_notyet; ?>" />

    <img class="<?php echo $b_wrong; ?>" src="<?php print_file_path('icn/test_wrong.png');?>" 
    title="Wrong" alt="Wrong" height="10" width="<?php echo $l_wrong; ?>" />

    <img class="<?php echo $b_correct; ?>" src="<?php print_file_path('icn/test_correct.png');?>" 
    title="Correct" alt="Correct" height="10" width="<?php echo $l_correct; ?>" />
    
    &nbsp; &nbsp; &nbsp; 
    <span title="Total number of tests"><?php echo $totaltests; ?></span>
    = 
    <span class="todosty" title="Not yet tested"><?php echo $notyettested; ?></span>
    + 
    <span class="donewrongsty" title="Wrong"><?php echo $wrong; ?></span>
    + 
    <span class="doneoksty" title="Correct"><?php echo $correct; ?></span>
</footer>
    <?php
}

/**
 * Prepare JavaScript code for interacting between the different frames.
 * 
 * @param int $count 1 for timer.
 * 
 * @return void
 */
function do_test_test_javascript($count)
{
    ?>
<script type="text/javascript">
    //<![CDATA[
    const waitTime = <?php 
    echo json_encode((int)getSettingWithDefault('set-test-edit-frame-waiting-time')) 
    ?>;

    $(document).ready(function() {
        window.parent.frames['ru'].location.href='empty.html';
        if (waitTime <= 0) {
            window.parent.frames['ro'].location.href='empty.html';
        } else {
            setTimeout(
                'window.parent.frames["ro"].location.href="empty.html";', 
                waitTime
            );
        }
        new CountUp(
            <?php echo time() . ', ' . $_SESSION['teststart']; ?>, 
            'timer', <?php echo ($count ? 0 : 1); ?>
        );
    });
//]]>
</script>
    <?php
}

/**
 * Do the main content of a test page.
 * 
 * @global int $debug Show debug informations
 * 
 * @return void
 */
function do_test_test_content()
{
    global $debug;
    //pagestart_nobody('');
    do_test_test_css();
    
    $testsql = get_test_sql();
    $totaltests = $_SESSION['testtotal'];
    $testtype = get_test_type();
    $count = get_first_value(
        'SELECT count(distinct WoID) AS value 
        FROM ' . $testsql . ' AND WoStatus BETWEEN 1 AND 5 
        AND WoTranslation != \'\' AND WoTranslation != \'*\' AND WoTodayScore < 0'
    );
    if ($debug) { 
        echo 'DEBUG - COUNT TO TEST: ' . $count . '<br />'; 
    }
    if (!is_numeric($count)) {
        my_die('The number of words left to test is not an integer: "' . $count . '"!');
    }
    $notyettested = (int) $count;

    $count2 = prepare_test_area($testsql, $totaltests, $notyettested, $testtype);
    prepare_test_footer($notyettested);
    do_test_test_javascript($count2);
    //pageend();

}

if (isset($_REQUEST['selection']) || isset($_REQUEST['lang']) || isset($_REQUEST['text'])) {
    //do_test_test_content();
}


?>
