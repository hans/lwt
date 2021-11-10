<?php

/**************************************************************
Call: set_word_status.php?...
			... tid=[textid]&wid=[wordid]&status=1..5/98/99
Change status of term while reading
***************************************************************/

require_once 'inc/session_utility.php';

$tid = $_REQUEST['tid'];
$wid = $_REQUEST['wid'];
$status = $_REQUEST['status'];

$sql = 'SELECT WoText, WoTranslation, WoRomanization FROM ' . $tbpref . 'words where WoID = ' . $wid;
$res = do_mysqli_query($sql);
$record = mysqli_fetch_assoc($res);
if ($record) {
    $word = $record['WoText'];
    $trans = repl_tab_nl($record['WoTranslation']) . getWordTagList($wid, ' ', 1, 0);
    $roman = $record['WoRomanization'];
} else {
    my_die("Word not found in set_word_status.php"); 
}
mysqli_free_result($res);

pagestart("Term: " . $word, false);

$m1 = runsql(
    'update ' . $tbpref . 'words set WoStatus = ' . 
    $_REQUEST['status'] . ', WoStatusChanged = NOW(),' . make_score_random_insert_update('u') . ' where WoID = ' . $wid, 'Status changed'
);

echo '<p>OK, this term has status ' . get_colored_status_msg($status) . ' from now!</p>';

?>
<script type="text/javascript">
//<![CDATA[
var context = window.parent.frames['l'].document;
var contexth = window.parent.frames['h'].document;
var status = '<?php echo $status; ?>';
var title = window.parent.frames['l'].JQ_TOOLTIP?'':make_tooltip(<?php echo prepare_textdata_js($word); ?>, <?php echo prepare_textdata_js($trans); ?>, <?php echo prepare_textdata_js($roman); ?>, status);
$('.word<?php echo $wid; ?>', context).removeClass('status98 status99 status1 status2 status3 status4 status5').addClass('status<?php echo $status; ?>').attr('data_status','<?php echo $status; ?>').attr('title',title);
$('#learnstatus', contexth).html('<?php echo addslashes(texttodocount2($tid)); ?>');
window.parent.frames['l'].focus();
window.parent.frames['l'].setTimeout('cClick()', 100);
//]]>
</script>
<?php

pageend();

?>
