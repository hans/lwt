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
Call: delete_mword.php?wid=[wordid]&tid=[textid]
Delete an expression
***************************************************************/

require 'lwt-startup.php';

$showAll = getSetting('showallwords');
$showAll = ($showAll == '' ? 1 : (((int) $showAll != 0) ? 1 : 0));

$tid = $_REQUEST['tid'];
$wid = $_REQUEST['wid'];
$word = get_first_value("select WoText as value from words where WoID = " . $wid);
pagestart("Term: " . $word, false);
$m1 = runsql('delete from words where WoID = ' . $wid, '');
adjust_autoincr('words','WoID');

echo "<p>OK, term deleted (" . $m1 . ").</p>";

?>
<script type="text/javascript">
//<![CDATA[
var context = window.parent.frames['l'].document;
var contexth = window.parent.frames['h'].document;
$('.word<?php echo $wid; ?>', context).removeClass('status1 status2 status3 status4 status5 status98 status99 word<?php echo $wid; ?>').addClass('hide').attr('data_status','').attr('data_trans','').attr('data_rom','').attr('data_wid','').attr('title','');
$('#learnstatus', contexth).html('<?php echo texttodocount2($tid); ?>');
<?php
if (! $showAll) echo refreshText($word,$tid);
?>
window.parent.frames['l'].focus();
window.parent.frames['l'].setTimeout('cClick()', 100);
//]]>
</script>
<?php

pageend();

?>