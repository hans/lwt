<?php

/**
 * \file
 * \brief Show test header frame
 * 
 * Call: do_test_header.php?lang=[langid]
 * Call: do_test_header.php?text=[textid]
 * Call: do_test_header.php?selection=1  
 *      (SQL via $_SESSION['testsql'])
 * 
 * @author https://sourceforge.net/projects/lwt/ LWT Project
 * @since  1.0.3
 */

require_once 'inc/session_utility.php';

/**
 * Set useful data for the test using SQL query.
 * 
 * @param string &$title Title to be overwritten
 * @param string &$p Property URL to be overwritten
 * 
 * @return string SQL query to use
 * 
 * @since 2.0.5-fork
 * 
 * @global string $tbpref Database table prefix
 */
function get_sql_test_data(&$title, &$p)
{
    global $tbpref;
    $p = "selection=" . $_REQUEST['selection']; 
    $testsql = $_SESSION['testsql'];
    $totalcount = get_first_value('SELECT count(distinct WoID) AS value FROM ' . $testsql);
    $title = 'Selected ' . $totalcount . ' Term' . ($totalcount==1 ? '' : 's');
    $cntlang = get_first_value('SELECT count(distinct WoLgID) AS value FROM ' . $testsql);
    if ($cntlang > 1) {
        $message = 'Error: The selected terms are in ' . $cntlang . ' languages, ' . 
        'but tests are only possible in one language at a time.'; 
        echo error_message_with_hide($message, 1);
        return '';
    }
    $title .= ' in ' . get_first_value(
        'SELECT LgName AS value 
        FROM ' . $tbpref . 'languages, ' . $testsql . ' AND LgID = WoLgID 
        LIMIT 1'
    ); 
    return $testsql;
}

/**
 * Set useful data for the test using language.
 * 
 * @param string &$title Title to be overwritten
 * @param string &$p Property URL to be overwritten
 * 
 * @return string SQL query to use
 * 
 * @since 2.0.5-fork
 * 
 * @global string $tbpref Database table prefix
 */
function get_lang_test_data(&$title, &$p) 
{
    global $tbpref;
    $langid = getreq('lang');
    $p = "lang=" . $langid; 
    $title = "All Terms in " . get_first_value(
        'SELECT LgName AS value FROM ' . $tbpref . 'languages WHERE LgID = ' . $langid
    );
    $testsql = ' ' . $tbpref . 'words WHERE WoLgID = ' . $langid . ' ';
    return $testsql;
}

/**
 * Set useful data for the test using text.
 * 
 * @param string &$title Title to be overwritten
 * @param string &$p Property URL to be overwritten
 * 
 * @return string SQL query to use
 * 
 * @since 2.0.5-fork
 * 
 * @global string $tbpref Database table prefix
 */
function get_text_test_data(&$title, &$p)
{
    global $tbpref;
    $textid = getreq('text');
    $p = "text=" . $textid; 
    $title = get_first_value(
        'SELECT TxTitle AS value FROM ' . $tbpref . 'texts WHERE TxID = ' . $textid
    );
    saveSetting('currenttext', $_REQUEST['text']);
    $testsql = 
    ' ' . $tbpref . 'words, ' . $tbpref . 'textitems2 
    WHERE Ti2LgID = WoLgID AND Ti2WoID = WoID AND Ti2TxID = ' . $textid . ' ';
    return $testsql;
}

/**
 * Return the words count for this test.
 * 
 * @param string $testsql SQL query for this test.
 * 
 * @return string[2] Total words due and total words learning
 */
function get_test_counts($testsql) 
{
    $totalcountdue = get_first_value(
        'SELECT count(distinct WoID) AS value 
        FROM ' . $testsql . ' AND WoStatus BETWEEN 1 AND 5 
        AND WoTranslation != \'\' AND WoTranslation != \'*\' AND WoTodayScore < 0'
    );
    $totalcount = get_first_value(
        'SELECT count(distinct WoID) AS value 
        FROM ' . $testsql . ' AND WoStatus BETWEEN 1 AND 5 AND WoTranslation != \'\' 
        AND WoTranslation != \'*\''
    );
    return array($totalcountdue, $totalcount);
}

/**
 * Make the header row for tests.
 * 
 * @param string $p URL property to use
 * 
 * @return void
 * 
 * @since 2.0.5-fork
 */
function do_test_header_row($p)
{
    ?>
<h4>
    <a href="edit_texts.php" target="_top">
    <?php echo_lwt_logo(); ?>
    LWT</a>&nbsp; | &nbsp;
    <?php 
    quickMenu();
    // This part only works if $textid is set
    if (substr($p, 0, 4) == 'text') {
        $textid = getreq('text');
        echo getPreviousAndNextTextLinks($textid, 'do_test.php?text=', false, '&nbsp; | &nbsp;');
        ?>
    &nbsp; | &nbsp;<a href="do_text.php?start=<?php echo $textid; ?>" target="_top">
        <img src="icn/book-open-bookmark.png" title="Read" alt="Read" />
    </a> &nbsp;
    <a href="print_text.php?text=<?php echo $textid; ?>" target="_top">
        <img src="icn/printer.png" title="Print" alt="Print" />
    </a>
        <?php
        echo get_annotation_link($textid);
    } 
    ?>
</h4>
    <?php
}

/**
 * Make the header content for tests.
 * 
 * @param string $title Page title
 * @param string $p URL property to use
 * @param string $totalcountdue Number of words due for today
 * @param string $totalcount Total number of words.
 * 
 * @return void
 * 
 * @since 2.0.5-fork
 */
function do_test_header_content($title, $p, $totalcountdue, $totalcount)
{
    ?>
<table>
    <tr>
        <td>
            <h3>TEST&nbsp;▶</h3>
        </td>
        <td class="width99pc">
            <h3>
                <?php echo tohtml($title) . ' (Due: ' . $totalcountdue . ' of ' . $totalcount . ')'; ?>
            </h3>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <p style="margin-bottom:0;">
                <input type="button" value="..[L2].." onclick="{parent.frames['ro'].location.href='empty.html'; parent.frames['ru'].location.href='empty.html'; parent.frames['l'].location.href='do_test_test.php?type=1&amp;<?php echo $p; ?>';}" />
                <input type="button" value="..[L1].." onclick="{parent.frames['ro'].location.href='empty.html'; parent.frames['ru'].location.href='empty.html';  parent.frames['l'].location.href='do_test_test.php?type=2&amp;<?php echo $p; ?>';}" />
                <input type="button" value="..[••].." onclick="{parent.frames['ro'].location.href='empty.html'; parent.frames['ru'].location.href='empty.html';   parent.frames['l'].location.href='do_test_test.php?type=3&amp;<?php echo $p; ?>';}" /> &nbsp; | &nbsp; 
                <input type="button" value="[L2]" onclick="{parent.frames['ro'].location.href='empty.html'; parent.frames['ru'].location.href='empty.html'; parent.frames['l'].location.href='do_test_test.php?type=4&amp;<?php echo $p; ?>';}" />
                <input type="button" value="[L1]" onclick="{parent.frames['ro'].location.href='empty.html'; parent.frames['ru'].location.href='empty.html';   parent.frames['l'].location.href='do_test_test.php?type=5&amp;<?php echo $p; ?>';}" /> &nbsp; | &nbsp; 
                <input type="button" value="Table" onclick="{parent.frames['ro'].location.href='empty.html'; parent.frames['ru'].location.href='empty.html'; parent.frames['l'].location.href='do_test_table.php?<?php echo $p; ?>';}" />
            </p>
        </td>
    </tr>
</table>
    <?php
}

/**
 * Set useful data for the test.
 * 
 * @param string &$title Title to be overwritten
 * @param string &$p Property URL to be overwritten
 * 
 * @return string[2] Total words due and total words learning
 * 
 * @since 2.0.5-fork
 */
function get_test_data(&$title, &$p)
{

    if (isset($_REQUEST['selection']) && isset($_SESSION['testsql'])) { 
        $testsql = get_sql_test_data($title, $p);
    } else if (isset($_REQUEST['lang'])) {
        $testsql = get_lang_test_data($title, $p);
    } else if (isset($_REQUEST['text'])) {
        $testsql = get_text_test_data($title, $p);
    } else { 
        $testsql = '';
        $p = '';
        $title = 'Request Error!';
        pagestart($title, true);
        my_die("do_test_header.php called with wrong parameters"); 
    }
    return get_test_counts($testsql);
}

/**
 * Do the header for test page.
 * 
 * @param string $title Page title
 * @param string $p URL property to use
 * @param string $totalcountdue Number of words due for today
 * @param string $totalcount Total number of words.
 * 
 * @return void
 * 
 * @since 2.0.5-fork
 */
function do_test_header_page($title, $p, $totalcountdue, $totalcount)
{

    pagestart_nobody(tohtml($title), $addcss = 'html, body {margin-bottom:0;}');

    $_SESSION['teststart'] = time() + 2;
    $_SESSION['testcorrect'] = 0;
    $_SESSION['testwrong'] = 0;
    $_SESSION['testtotal'] = $totalcountdue;


    do_test_header_row($p);
    do_test_header_content($title, $p, $totalcountdue, $totalcount);

    pageend();
}

if (
    (isset($_REQUEST['selection']) && isset($_SESSION['testsql'])) || 
    (isset($_REQUEST['lang'])) || 
    (isset($_REQUEST['text']))
    ) {
    $counts = get_test_data($title, $p);
    $totalcountdue = $counts[0];
    $totalcount = $counts[1];
    do_test_header_page($title, $p, $totalcountdue, $totalcount);
} 

?>
