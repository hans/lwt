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

// delete_word.php?wid=..&tid=..

$tid = $_REQUEST['tid'];
$wid = $_REQUEST['wid'];
$wort = get_first_value("select WoText as value from words where WoID = " . $wid);
pagestart("Term: " . $wort, false);
$m1 = runsql('delete from words where WoID = ' . $wid, '');
adjust_autoincr('words','WoID');

echo "<p>OK, term deleted, now unknown (" . $m1 . ").</p>";

?>
<script type="text/javascript">
//<![CDATA[
var context = window.parent.frames['l'].document;
var contexth = window.parent.frames['h'].document;
var title = make_tooltip(<?php echo prepare_textdata_js($wort); ?>,'','','');
$('.word<?php echo $wid; ?>', context).removeClass('status99 status98 status1 status2 status3 status4 status5 word<?php echo $wid; ?>').addClass('status0').attr('data_status','0').attr('data_trans','').attr('data_rom','').attr('data_wid','').attr('title',title);
$('#learnstatus', contexth).html('<?php echo texttodocount2($tid); ?>');
window.parent.frames['l'].setTimeout('cClick()', 100);
//]]>
</script>
<?php

pageend();

?> 
