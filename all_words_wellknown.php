<?php

/**************************************************************
"Learning with Texts" (LWT) is free and unencumbered software 
released into the PUBLIC DOMAIN.

Anyone is free to copy, modify, publish, use, compile, sell, or
distribute this software, either in source code form or as a
compiled binary, for any purpose, commercial or non-commercial,
and by any means.

In jurisdictions that recognize copyright laws, the author or
authors of this software dedicate any and all copyright
interest in the software to the public domain. We make this
dedication for the benefit of the public at large and to the 
detriment of our heirs and successors. We intend this 
dedication to be an overt act of relinquishment in perpetuity
of all present and future rights to this software under
copyright law.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE 
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE
AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS BE LIABLE 
FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN 
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN 
THE SOFTWARE.

For more information, please refer to [http://unlicense.org/].
***************************************************************/

/**************************************************************
Call: all_words_wellknown.php?text=[textid]
Setting all unknown words to Well Known (99)
***************************************************************/

require_once( 'settings.inc.php' );
require_once( 'connect.inc.php' );
require_once( 'dbutils.inc.php' );
require_once( 'utilities.inc.php' );

$status=$_REQUEST['stat'];
$langid = get_first_value("select TxLgID as value from " . $tbpref . "texts where TxID = " . $_REQUEST['text']);

if($status==98)
	pagestart("Setting all blue words to Ignore",false);
if($status==99)
	pagestart("Setting all blue words to Well-known",false);

$sql = 'select Ti2Text, lower(Ti2Text) as  WoTextLC from (' . $tbpref . 'textitems2 left join ' . $tbpref . 'words on (Ti2WoID = WoID) and (Ti2LgID = WoLgID)) where Ti2WoID = 0 and Ti2WordCount = 1 and Ti2TxID = ' . $_REQUEST['text'] . ' group by lower(Ti2Text) order by Ti2Order';
$res = do_mysql_query($sql);
$count = 0;
$javascript = "var title='';";
$sqlarr = array();
while ($record = mysql_fetch_assoc($res)) {
	$term = $record['Ti2Text'];	
	$termlc = $record['WoTextLC'];
	$count1 = 0 + runsql('insert into ' . $tbpref . 'words (WoLgID, WoText, WoTextLC, WoWordCount, WoStatus, WoStatusChanged,' .  make_score_random_insert_update('iv') . ') values( ' . 
	$langid . ', ' . 
	convert_string_to_sqlsyntax($term) . ', ' . 
	convert_string_to_sqlsyntax($termlc) . ', 1, '.$status.' , NOW(), ' .  
make_score_random_insert_update('id') . ')','');
	$wid = get_last_key();
	$sqlarr[]= ' WHEN ' . convert_string_to_sqlsyntax_notrim_nonull($termlc) . ' THEN ' . $wid;
	if ($count1 > 0 ) 
		$javascript .= "title = make_tooltip(" . prepare_textdata_js($term) . ",'*','','".$status."');";
		$javascript .= "$('.TERM" . strToClassName($termlc) . "', context).removeClass('status0').addClass('status".$status." word" . $wid . "').attr('data_status','".$status."').attr('data_wid','" . $wid . "').attr('title',title);";
	$count += $count1;
}
mysql_free_result($res);
$sqltext = "UPDATE  " . $tbpref . "textitems2 SET Ti2WoID  = CASE lower(Ti2Text)";
$sqltext .= implode(' ', $sqlarr) . ' END where Ti2WordCount=1 and Ti2WoID  = 0 and Ti2LgID=' . $langid;
mysql_query ($sqltext);

if($status==98)
	echo "<p>OK, you ignore all " . $count . " word(s)!</p>";
if($status==99)
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
