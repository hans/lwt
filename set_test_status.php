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
Call: set_test_status.php?wid=[wordid]&stchange=+1/-1
      set_test_status.php?wid=[wordid]&status=1..5/98/99
Change status of term while testing
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

$stchange = getreq('stchange');
$status = getreq('status');
$wid = getreq('wid') + 0;

$oldstatus = get_first_value("select WoStatus as value from words where WoID = " . $wid) + 0;

$oldscore = get_first_value('select ' . getsqlscoreformula(1) . ' AS value from words where WoID = ' . $wid) + 0;

if ($stchange == '') {

	$status = $status + 0;
	$stchange = $status - $oldstatus;
	if ($stchange < 0) $stchange=-1;
	if ($stchange > 0) $stchange=1;
	
} else {

	$stchange = $stchange + 0;
	$status = $oldstatus + $stchange;
	if ($status < 1) $status=1;
	if ($status > 5) $status=5;
	
}

$word = get_first_value("select WoText as value from words where WoID = " . $wid);
pagestart("Term: " . $word, false);

$m1 = runsql('update words set WoStatus = ' . 
	$status . ', WoStatusChanged = NOW() where WoID = ' . $wid, 'Status changed');
	
$newscore = get_first_value('select ' . getsqlscoreformula(1) . ' AS value from words where WoID = ' . $wid) + 0;

if ($oldstatus == $status)
	echo '<p>Status "' . tohtml(get_status_name($oldstatus)) . '" [' . tohtml(get_status_abbr($oldstatus)) . '] not changed.</p>';
else
	echo '<p>Status changed from "' . tohtml(get_status_name($oldstatus)) . '" [' . tohtml(get_status_abbr($oldstatus)) . '] to "' . tohtml(get_status_name($status)) . '" [' . tohtml(get_status_abbr($status)) . '].</p>';

echo "<p>Old score was " . $oldscore . ", new score is now " . $newscore . ".</p>";

$totaltests = $_SESSION['testtotal'];
$wrong = $_SESSION['testwrong'];
$correct = $_SESSION['testcorrect'];
$notyettested = $totaltests - $correct - $wrong;
if ( $notyettested > 0 ) {
	if ( $stchange >= 0 ) 
		$_SESSION['testcorrect']++;
	else
		$_SESSION['testwrong']++;
}		

?>
<script type="text/javascript">
//<![CDATA[
var context = window.parent.frames['l'].document;
$('.word<?php echo $wid; ?>', context).removeClass('todo todosty').addClass('done<?php echo ($stchange >= 0 ? 'ok' : 'wrong'); ?>sty').attr('data_status','<?php echo $status; ?>').attr('data_todo','0');
window.parent.frames['l'].setTimeout('location.reload();', parseInt('<?php echo getSettingWithDefault('set-test-main-frame-waiting-time'); ?>',10));
//]]>
</script>
<?php

pageend();

?>