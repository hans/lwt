<?php

/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions,
unless such conditions are required by law.

Developed by J. Pierre in 2011.
***************************************************************/

/**************************************************************
Call: all_words_wellknown.php?text=[textid]
Setting all unknown words to Well Known (99)
***************************************************************/

require 'lwt-startup.php';

$langid = get_first_value("select TxLgID as value from texts where TxID = " . $_REQUEST['text']);

pagestart("Setting all blue words to Well-known",false);

$sql = 'select distinct TiText, TiTextLC from (textitems left join words on (TiTextLC = WoTextLC) and (TiLgID = WoLgID)) where TiIsNotWord = 0 and WoID is null and TiWordCount = 1 and TiTxID = ' . $_REQUEST['text'] . ' order by TiOrder';
$res = mysql_query($sql);
if ($res == FALSE) die("Invalid Query: $sql");
$count = 0;
$javascript = "var title='';";
while ($record = mysql_fetch_assoc($res)) {
	$term = $record['TiText'];
	$termlc = $record['TiTextLC'];
	$count1 = 0 + runsql('insert into words (WoLgID, WoText, WoTextLC, WoStatus, WoStatusChanged,' .  make_score_random_insert_update('iv') . ') values( ' .
	$langid . ', ' .
	convert_string_to_sqlsyntax($term) . ', ' .
	convert_string_to_sqlsyntax($termlc) . ', 99 , NOW(), ' .
make_score_random_insert_update('id') . ')','');
	$wid = get_last_key();
	if ($count1 > 0 )
		$javascript .= "title = make_tooltip(" . prepare_textdata_js($term) . ",'*','','99');";
		$javascript .= "$('.TERM" . strToClassName($termlc) . "', context).removeClass('status0').addClass('status99 word" . $wid . "').attr('data_status','99').attr('data_wid','" . $wid . "').attr('title',title);";
	$count += $count1;
}
mysql_free_result($res);

echo "<p>OK, you know all " . $count . " word(s) well!</p>";

?>
<script type="text/javascript">
//<![CDATA[
var context = window.parent.frames['l'].document;
var contexth = window.parent.frames['h'].document;
<?php echo $javascript; ?>
$('#learnstatus', contexth).html('<?php echo texttodocount2($_REQUEST['text']); ?>');
window.parent.frames['l'].setTimeout('cClick()', 1000);
//]]>
</script>
<?php

pageend();

?>