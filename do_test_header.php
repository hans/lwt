<?php


/**
 * \file
 * Show test header frame
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

$p = '';
$message = '';

if (isset($_REQUEST['selection']) && isset($_SESSION['testsql'])) { 
    $p = "selection=" . $_REQUEST['selection']; 
    $testsql = $_SESSION['testsql'];
    $totalcount = get_first_value('select count(distinct WoID) as value from ' . $testsql);
    $title = 'Selected ' . $totalcount . ' Term' . ($totalcount==1 ? '' : 's');
    $cntlang = get_first_value('select count(distinct WoLgID) as value from ' . $testsql);
    if ($cntlang > 1) { 
        $message = 'Error: The selected terms are in ' . $cntlang . ' languages, but tests are only possible in one language at a time.'; 
    }
    else { 
        $title .= ' in ' . get_first_value('select LgName as value from ' . $tbpref . 'languages, ' . $testsql . ' and LgID = WoLgID limit 1'); 
    }
}

if (isset($_REQUEST['lang'])) {
    $langid = getreq('lang');
    $p = "lang=" . $langid; 
    $title = "All Terms in " . get_first_value('select LgName as value from ' . $tbpref . 'languages where LgID = ' . $langid);
    $testsql = ' ' . $tbpref . 'words where WoLgID = ' . $langid . ' ';
}

if (isset($_REQUEST['text'])) {
    $textid = getreq('text');
    $p = "text=" . $textid; 
    $title = get_first_value('select TxTitle as value from ' . $tbpref . 'texts where TxID = ' . $textid);
    saveSetting('currenttext', $_REQUEST['text']);
    $testsql = ' ' . $tbpref . 'words, ' . $tbpref . 'textitems2 where Ti2LgID = WoLgID and Ti2WoID = WoID and Ti2TxID = ' . $textid . ' ';
}

if ($p == '') { my_die("do_test_header.php called with wrong parameters"); 
}

$totalcountdue = get_first_value('SELECT count(distinct WoID) as value FROM ' . $testsql . ' AND WoStatus BETWEEN 1 AND 5 AND WoTranslation != \'\' AND WoTranslation != \'*\' AND WoTodayScore < 0');
$totalcount = get_first_value('SELECT count(distinct WoID) as value FROM ' . $testsql . ' AND WoStatus BETWEEN 1 AND 5 AND WoTranslation != \'\' AND WoTranslation != \'*\'');

pagestart_nobody(tohtml($title), $addcss = 'html, body {margin-bottom:0;}');
echo '<h4>';
echo '<a href="edit_texts.php" target="_top">';
echo_lwt_logo();
echo 'LWT';
echo '</a>&nbsp; | &nbsp;';
quickMenu();
if (substr($p, 0, 4) == 'text') {
    echo getPreviousAndNextTextLinks($textid, 'do_test.php?text=', false, '&nbsp; | &nbsp;');
    echo '&nbsp; | &nbsp;<a href="do_text.php?start=' . $textid . '" target="_top"><img src="icn/book-open-bookmark.png" title="Read" alt="Read" /></a> &nbsp;<a href="print_text.php?text=' . $textid . '" target="_top"><img src="icn/printer.png" title="Print" alt="Print" /></a>' . get_annotation_link($textid);
}
echo '</h4><table><tr><td><h3>TEST&nbsp;▶</h3></td><td class="width99pc"><h3>' . tohtml($title) . ' (Due: ' . $totalcountdue . ' of ' . $totalcount . ')</h3></td></tr><tr><td colspan="2">';

$_SESSION['teststart'] = time() + 2;
$_SESSION['testcorrect'] = 0;
$_SESSION['testwrong'] = 0;
$_SESSION['testtotal'] = $totalcountdue;

if ($message != '') {
    echo error_message_with_hide($message, 1);
}

else {  // OK

    ?>
<p style="margin-bottom:0;">
<input type="button" value="..[L2].." onclick="{parent.frames['ro'].location.href='empty.html'; parent.frames['ru'].location.href='empty.html'; parent.frames['l'].location.href='do_test_test.php?type=1&amp;<?php echo $p; ?>';}" />
<input type="button" value="..[L1].." onclick="{parent.frames['ro'].location.href='empty.html'; parent.frames['ru'].location.href='empty.html';  parent.frames['l'].location.href='do_test_test.php?type=2&amp;<?php echo $p; ?>';}" />
<input type="button" value="..[••].." onclick="{parent.frames['ro'].location.href='empty.html'; parent.frames['ru'].location.href='empty.html';   parent.frames['l'].location.href='do_test_test.php?type=3&amp;<?php echo $p; ?>';}" /> &nbsp; | &nbsp; 
<input type="button" value="[L2]" onclick="{parent.frames['ro'].location.href='empty.html'; parent.frames['ru'].location.href='empty.html'; parent.frames['l'].location.href='do_test_test.php?type=4&amp;<?php echo $p; ?>';}" />
<input type="button" value="[L1]" onclick="{parent.frames['ro'].location.href='empty.html'; parent.frames['ru'].location.href='empty.html';   parent.frames['l'].location.href='do_test_test.php?type=5&amp;<?php echo $p; ?>';}" /> &nbsp; | &nbsp; 
<input type="button" value="Table" onclick="{parent.frames['ro'].location.href='empty.html'; parent.frames['ru'].location.href='empty.html'; parent.frames['l'].location.href='do_test_table.php?<?php echo $p; ?>';}" />
</p></td></tr></table>
    <?php

}

pageend();

?>
