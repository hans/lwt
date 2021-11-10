<?php

/**************************************************************
Call: delete_word.php?wid=[wordid]&tid=[textid]
Delete a word
***************************************************************/

require_once 'inc/session_utility.php';

$tid = $_REQUEST['tid'];
$wid = $_REQUEST['wid'];
$term = get_first_value("select WoText as value from " . $tbpref . "words where WoID = " . $wid);
pagestart("Term: " . $term, false);
$m1 = runsql('delete from ' . $tbpref . 'words where WoID = ' . $wid, '');
adjust_autoincr('words', 'WoID');
runsql("UPDATE  " . $tbpref . "textitems2 SET Ti2WoID  = 0 WHERE Ti2WordCount=1 AND Ti2WoID  = " .$wid, '');
echo "<p>OK, term deleted, now unknown (" . $m1 . ").</p>";

?>
<script type="text/javascript">
//<![CDATA[
var context = window.parent.frames['l'].document;
var contexth = window.parent.frames['h'].document;
var title = window.parent.frames['l'].JQ_TOOLTIP?'':make_tooltip(<?php echo prepare_textdata_js($_REQUEST["WoText"]); ?>,trans,roman,status);
$('.word<?php echo $wid; ?>', context).removeClass('status99 status98 status1 status2 status3 status4 status5 word<?php echo $wid; ?>').addClass('status0').attr('data_status','0').attr('data_trans','').attr('data_rom','').attr('data_wid','').attr('title',title).removeAttr("data_img");
$('#learnstatus', contexth).html('<?php echo addslashes(texttodocount2($tid)); ?>');
window.parent.frames['l'].focus();
window.parent.frames['l'].setTimeout('cClick()', 100);
//]]>
</script>
<?php

pageend();

?>
