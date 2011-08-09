<?php

/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions, 
unless such conditions are required by law.
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

// set_word_status?tid=..&wid=..&status=1..5/98/99

$tid = $_REQUEST['tid'];
$wid = $_REQUEST['wid'];
$status = $_REQUEST['status'];

$sql = 'SELECT WoText, WoTranslation, WoRomanization FROM words where WoID = ' . $wid;
$res = mysql_query($sql);		
if ($res == FALSE) die("<p>Invalid query: $sql</p>");
$num = mysql_num_rows($res);
if ($num != 0 ) {
	$dsatz = mysql_fetch_assoc($res);
	$wort = $dsatz['WoText'];
	$trans = repl_tab_nl($dsatz['WoTranslation']);
	$roman = $dsatz['WoRomanization'];
}
mysql_free_result($res);

pagestart("Term: " . $wort, false);

$m1 = runsql('update words set WoStatus = ' . 
	$_REQUEST['status'] . ', WoStatusChanged = NOW() where WoID = ' . $wid, 'Status changed');

echo '<p>OK, this term has status "' . tohtml(get_status_name($status)) . '" [' . tohtml(get_status_abbr($status)) . '] from now!</p>';

?>
<script type="text/javascript">
//<![CDATA[
var context = window.parent.frames['l'].document;
var contexth = window.parent.frames['h'].document;
var status = '<?php echo $status; ?>';
var title = make_tooltip(<?php echo prepare_textdata_js($wort); ?>, <?php echo prepare_textdata_js($trans); ?>, <?php echo prepare_textdata_js($roman); ?>, status);
$('.word<?php echo $wid; ?>', context).removeClass('status98 status99 status1 status2 status3 status4 status5').addClass('status<?php echo $status; ?>').attr('data_status','<?php echo $status; ?>').attr('title',title);
$('#learnstatus', contexth).html('<?php echo texttodocount2($tid); ?>');
window.parent.frames['l'].setTimeout('cClick()', 100);
//]]>
</script>
<?php

pageend();

?> 

