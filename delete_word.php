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
Call: delete_word.php?wid=[wordid]&tid=[textid]
Delete a word
***************************************************************/

require 'lwt-startup.php';

$tid = $_REQUEST['tid'];
$wid = $_REQUEST['wid'];
$term = get_first_value("SELECT WoText FROM words WHERE WoID = " . $wid);

pagestart("Term: " . $term, false);
$m1 = db_execute('DELETE FROM words WHERE WoID = ?', $wid);

echo "<p>OK, term deleted, now unknown (" . $m1 . ").</p>";

?>
<script type="text/javascript">
//<![CDATA[
var context = window.parent.frames['l'].document;
var contexth = window.parent.frames['h'].document;
var title = make_tooltip(<?php echo prepare_textdata_js($term); ?>,'','','');
$('.word<?php echo $wid; ?>', context).removeClass('status99 status98 status1 status2 status3 status4 status5 word<?php echo $wid; ?>').addClass('status0').attr('data_status','0').attr('data_trans','').attr('data_rom','').attr('data_wid','').attr('title',title);
$('#learnstatus', contexth).html('<?php echo texttodocount2($tid); ?>');
window.parent.frames['l'].focus();
window.parent.frames['l'].setTimeout('cClick()', 100);
//]]>
</script>
<?php

pageend();

?>