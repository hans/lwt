<?php

/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions, 
unless such conditions are required by law.

Developed by J. P. in 2011, 2012, 2013.
***************************************************************/

/**************************************************************
Call: do_test_table.php?lang=[langid]
Call: do_test_test.php?text=[textid]
Call: do_test_test.php?&selection=1  
			(SQL via $_SESSION['testsql'])
Show test frame with vocab table
***************************************************************/

include "settings.inc.php";
include "connect.inc.php";
include "utilities.inc.php";

$p = '';

if (isset($_REQUEST['selection']) && isset($_SESSION['testsql'])) {
	$testsql = $_SESSION['testsql']; 
}

elseif (isset($_REQUEST['lang'])) {
	$testsql = ' ' . $tbpref . 'words where WoLgID = ' . $_REQUEST['lang'] . ' '; 
}

elseif (isset($_REQUEST['text'])) {
	$testsql = ' ' . $tbpref . 'words, ' . $tbpref . 'textitems where TiLgID = WoLgID and TiTextLC = WoTextLC and TiTxID = ' . $_REQUEST['text'] . ' ';
}

else die("Called with wrong parameters");

pagestart_nobody('','html, body { margin:3px; padding:0; }');

$cntlang = get_first_value('select count(distinct WoLgID) as value from ' . $testsql);
if ($cntlang > 1) {
	echo '<p>Sorry - The selected terms are in ' . $cntlang . ' languages, but tests are only possible in one language at a time.</p>';
	pageend();
	exit();
}

$lang = get_first_value('select WoLgID as value from ' . $testsql . ' limit 1');
$rtlScript = get_first_value('select LgRightToLeft as value from ' . $tbpref . 'languages where LgID = ' . $lang);
$span1 = ($rtlScript ? '<span dir="rtl">' : '');
$span2 = ($rtlScript ? '</span>' : '');
$lpar = ($rtlScript ? '[' : '[');
$rpar = ($rtlScript ? ']' : ']');
$regexword = get_first_value('select LgRegexpWordCharacters as value from ' . $tbpref . 'languages where LgID = ' . $lang);

?>
<script type="text/javascript">
//<![CDATA[
$(function() {
$('#cbTerm').click(function() {
	if($('#cbTerm').is(':checked')) 
		$('td:nth-child(2)').css('color', 'black').css('cursor', 'auto');
	else 
		$('td:nth-child(2)').css('color', 'white').css('cursor', 'pointer');
});
$('#cbRom').click(function() {
	if($('#cbRom').is(':checked')) 
		$('td:nth-child(4),th:nth-child(4)').show();
	else 
		$('td:nth-child(4),th:nth-child(4)').hide();
});
$('#cbTrans').click(function() {
	if($('#cbTrans').is(':checked')) 
		$('td:nth-child(3)').css('color', 'black').css('cursor', 'auto');
	else 
		$('td:nth-child(3)').css('color', 'white').css('cursor', 'pointer');
});
$('#cbSentence').click(function() {
	if($('#cbSentence').is(':checked'))
		$('td:nth-child(5),th:nth-child(5)').show();
	else 
		$('td:nth-child(5),th:nth-child(5)').hide();
});
$('#cbStatus').click(function() {
	if($('#cbStatus').is(':checked')) 
		$('td:nth-child(1),th:nth-child(1)').show();
	else 
		$('td:nth-child(1),th:nth-child(1)').hide();
});
$('td').click(function() {
	$(this).css('color', 'black').css('cursor', 'auto');
});
$('td:nth-child(5),th:nth-child(5)').hide();
$('td:nth-child(4),th:nth-child(4)').hide();
$('td').css('background-color', 'white');
});
//]]>
</script>
<p>
<input type="checkbox" id="cbStatus" checked="checked" /> Status
<input type="checkbox" id="cbTerm" checked="checked" /> Term
<input type="checkbox" id="cbTrans" checked="checked" /> Translation
<input type="checkbox" id="cbRom" /> Romanization
<input type="checkbox" id="cbSentence" /> Sentence
</p>

<table class="sortable tab1" style="width:auto;" cellspacing="0" cellpadding="5">
<tr>
<th class="th1 clickable">Status</th>
<th class="th1 clickable">Term</th>
<th class="th1 clickable">Translation</th>
<th class="th1 clickable">Romanization</th>
<th class="th1 clickable">Sentences</th>
</tr>
<?php

$sql = 'SELECT DISTINCT WoID, WoText, WoTranslation, WoRomanization, WoSentence, WoStatus, WoTodayScore As Score FROM ' . $testsql . ' AND WoStatus BETWEEN 1 AND 5 AND WoTranslation != \'\' AND WoTranslation != \'*\' order by WoTodayScore, WoRandom';
if ($debug) echo $sql;
$res = mysql_query($sql);		
if ($res == FALSE) die("Invalid Query: $sql");
while ($record = mysql_fetch_assoc($res)) {
	$sent = tohtml(repl_tab_nl($record["WoSentence"]));
	$sent1 = str_replace("{", ' <b>' . $lpar, str_replace("}", $rpar . '</b> ', 
		mask_term_in_sentence($sent,$regexword)));
	$score = $record['Score'];
	if ( $score < 0 ) 
		$scoret = '<span class="red2">' . $record['WoStatus'] . '</span>';
	else
		$scoret = $record['WoStatus'];
	if ( $record['WoStatus'] < 5 ) 
		$plus = '<img src="icn/plus.png" title="+" alt="+" />';
	else
		$plus = '<img src="icn/placeholder.png" title="" alt="" />';
	if ( $record['WoStatus'] > 1 ) 
		$minus = '<img src="icn/minus.png" title="-" alt="-" />';
	else
		$minus = '<img src="icn/placeholder.png" title="" alt="" />';
?>
<tr>
<td class="td1 center"><?php echo $minus . ' ' . $scoret . ' ' . $plus; ?></td>
<td class="td1 center"><?php echo $span1 . tohtml($record['WoText']) . $span2; ?></td>
<td class="td1 center"><?php echo tohtml($record['WoTranslation']); ?></td>
<td class="td1 center"><?php echo tohtml($record['WoRomanization']); ?></td>
<td class="td1 center"><?php echo $span1 . $sent1 . $span2; ?></td>
</tr>
<?php
}
mysql_free_result($res);

?>
</table>
<?php

pageend();

?>