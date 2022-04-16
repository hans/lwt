<?php

/**
 * \file
 * \brief Setting all unknown words to Well Known (99)
 * 
 * Call: all_words_wellknown.php?text=[textid] 
 *                              (mark all words as well-known)
 *       all_words_wellknown.php?text=[textid]&status=[statusint] 
 *                              (mark with a specific status, normally 98 or 99)
 * 
 * @package Lwt
 * @author  LWT Project <lwt-project@hotmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/all__words__wellknown_8php.html
 * @since   1.0.3
 */

require_once 'inc/session_utility.php';

/**
 * Make the SQL query for all words in the text.
 * 
 * @param int $txid Text id
 * 
 * @return mysqli_result|true SQL query.
 */
function all_words_wellknown_get_words($txid)
{
    global $tbpref;
    $sql = "SELECT DISTINCT Ti2Text, LOWER(Ti2Text) AS Ti2TextLC
    FROM ( 
        {$tbpref}textitems2 
        LEFT JOIN {$tbpref}words 
        ON LOWER(Ti2Text) = WoTextLC AND Ti2LgID = WoLgID
    ) 
    WHERE WoID IS NULL AND Ti2WordCount = 1 AND Ti2TxID = $txid 
    ORDER BY Ti2Order";
    return do_mysqli_query($sql);
}

/**
 * For each word, add the word to the database.
 * 
 * @param int    $status New word status
 * @param string $term   Word to mark
 * @param string $termlc Same as $term, but in lowercase.
 * @param int    $langid Language ID
 * 
 * @return array<int, string> Number of rows edited and a javascript query.
 */
function all_words_wellknown_process_word($status, $term, $termlc, $langid)
{
    global $tbpref;
    $message = runsql(
        'INSERT INTO ' . $tbpref . 'words (
            WoLgID, WoText, WoTextLC, WoStatus, WoStatusChanged,' 
            . make_score_random_insert_update('iv') . 
        ') 
        VALUES( ' . 
            $langid . ', ' . 
            convert_string_to_sqlsyntax($term) . ', ' . 
            convert_string_to_sqlsyntax($termlc) . ', ' . $status . ' , NOW(), ' .  
            make_score_random_insert_update('id') .
        ')', 
        ''
    );
    if (!is_numeric($message)) {
        my_die('ERROR: Could not modify words! Message: ' + $message);
    }
    if ((int)$message == 0) {
        error_message_with_hide(
            'WARNING: No rows modified! Message: ' + $message, 
            false
        );
    }
    $rows = (int) $message;
    $wid = get_last_key();
    $javascript = '';
    if (getSettingWithDefault('set-tooltip-mode') == 1 && $rows > 0) {
        $javascript .= "title = make_tooltip(" . 
        prepare_textdata_js($term) . ", '*', '', '" . $status . 
        "');";
    }
    $javascript .= "$('.TERM" . strToClassName($termlc) . "', context)
    .removeClass('status0')
    .addClass('status" . $status . " word" . $wid . "')
    .attr('data_status', '" . $status . "')
    .attr('data_wid', '" . $wid . "')
    .attr('title', title);";
    return array($rows, $javascript);
}

/**
 * Main processing loop to mark all words of a text with a new status.
 * 
 * @param int $txid   Text ID
 * @param int $status New status to apply to all words.
 * 
 * @return array<int,string> Number of edited words, 
 *                           and JavaScript query to change their display  
 */
function all_words_wellknown_main_loop($txid, $status)
{
    global $tbpref;
    $langid = get_first_value(
        "SELECT TxLgID AS value 
        FROM " . $tbpref . "texts 
        WHERE TxID = " . $txid
    );
    $javascript = "var title='';";
    $count = 0;
    $res = all_words_wellknown_get_words($txid);
    while ($record = mysqli_fetch_assoc($res)) {
        list($modified_rows, $new_js) = all_words_wellknown_process_word(
            $status, $record['Ti2Text'], $record['Ti2TextLC'], $langid
        );
        $javascript .= $new_js;
        $count += $modified_rows;
    }
    mysqli_free_result($res);
    return array($count, $javascript);
}

/**
 * Display the number of edited words.
 * 
 * @param int $status New status
 * @param int $count  Number of edited words. 
 * 
 * @return void
 */
function all_words_wellknown_count_terms($status, $count)
{
    if ($status == 98) {
        echo "<p>OK, you ignore all " . $count . " word(s)!</p>"; 
    } else {
        echo "<p>OK, you know all " . $count . " word(s) well!</p>"; 
    }
}

/**
 * Execute JavaScript to change the display of all words.
 * 
 * @param int $txid       Text ID
 * @param int $javascript JavaScript-formatted string.
 * 
 * @return void
 */
function all_words_wellknown_javascript($txid, $javascript)
{
    ?>
<script type="text/javascript">
    //<![CDATA[
    const context = window.parent.document;
    <?php echo $javascript; ?> 
    $('#learnstatus', context)
    .html('<?php echo addslashes(texttodocount2($txid)); ?>');
    window.parent.setTimeout('cClick()', 1000);
    //]]>
</script>
    <?php
}

/**
 * Make the main content of the page for all well-known words.
 * 
 * @param int $txid   Text ID
 * @param int $status New status to apply to words.
 * 
 * @return void
 */
function all_words_wellknown_content($txid, $status)
{
    list($count, $javascript) = all_words_wellknown_main_loop($txid, $status);
    all_words_wellknown_count_terms($status, $count);
    all_words_wellknown_javascript($txid, $javascript);
}

/**
 * Make a full HTML page for all well-known words.
 * 
 * @param int $txid   Text ID
 * @param int $status New status to apply to words.
 * 
 * @return void
 */
function all_words_wellknown_full($txid, $status) 
{
    if ($status == 98) {
        pagestart("Setting all blue words to Ignore", false); 
    } else {
        pagestart("Setting all blue words to Well-known", false); 
    }
    all_words_wellknown_content($txid, $status);
    pageend();
}

if (isset($_REQUEST['text'])) {
    all_words_wellknown_full(
        (int) $_REQUEST['text'], 
        isset($_REQUEST['stat']) ? (int) $_REQUEST['stat'] : 99
    );
}


?>
