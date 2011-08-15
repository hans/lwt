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
Call: set_word_status.php?...
			... tid=[textid]&wid=[wordid]&status=1..5/98/99
Change status of term while reading
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

$tid = $_REQUEST['tid'];
$wid = $_REQUEST['wid'];
$status = $_REQUEST['status'];

$sql = 'SELECT WoText, WoTranslation, WoRomanization FROM words where WoID = ' . $wid;
$res = mysql_query($sql);		
if ($res == FALSE) die("Invalid Query: $sql");
$dsatz = mysql_fetch_assoc($res);
if ($dsatz) {
	$word = $dsatz['WoText'];
	$trans = repl_tab_nl($dsatz['WoTranslation']);
	$roman = $dsatz['WoRomanization'];
} else {
	die("Error: No results"); 
}
mysql_free_result($res);

pagestart("Term: " . $word, false);

$m1 = runsql('update words set WoStatus = ' . 
	$_REQUEST['status'] . ', WoStatusChanged = NOW(),' . make_score_random_insert_update('u') . ' where WoID = ' . $wid, 'Status changed');

echo '<p>OK, this term has status ' . get_colored_status_msg($status) . ' from now!</p>';

?>
<script type="text/javascript">
//<![CDATA[
var context = window.parent.frames['l'].document;
var contexth = window.parent.frames['h'].document;
var status = '<?php echo $status; ?>';
var title = make_tooltip(<?php echo prepare_textdata_js($word); ?>, <?php echo prepare_textdata_js($trans); ?>, <?php echo prepare_textdata_js($roman); ?>, status);
$('.word<?php echo $wid; ?>', context).removeClass('status98 status99 status1 status2 status3 status4 status5').addClass('status<?php echo $status; ?>').attr('data_status','<?php echo $status; ?>').attr('title',title);
$('#learnstatus', contexth).html('<?php echo texttodocount2($tid); ?>');
window.parent.frames['l'].focus();
window.parent.frames['l'].setTimeout('cClick()', 100);
//]]>
</script>
<?php

pageend();

?>