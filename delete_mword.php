<?php

/**************************************************************
Call: delete_mword.php?wid=[wordid]&tid=[textid]
Delete an expression 
 ***************************************************************/

require_once 'inc/session_utility.php';

$showAll = getSettingZeroOrOne('showallwords', 1);

$tid = $_REQUEST['tid'];
$wid = $_REQUEST['wid'];
$word = get_first_value("select WoText as value from " . $tbpref . "words where WoID = " . $wid);
pagestart("Term: " . $word, false);
$m1 = runsql('delete from ' . $tbpref . 'words where WoID = ' . $wid, '');
adjust_autoincr('words', 'WoID');
runsql('delete from ' . $tbpref . 'textitems2 where Ti2WordCount>1 AND Ti2WoID = ' . $wid, '');

echo "<p>OK, term deleted (" . $m1 . ").</p>";

?>
<script type="text/javascript">
//<![CDATA[
var context = window.parent.frames['l'].document;
var contexth = window.parent.frames['h'].document;
$('.word<?php echo $wid; ?>', context).each(function(){
sid = $(this).parent();
$(this).remove();
<?php 
if (! $showAll) { ?>
$('*',sid).removeClass('hide');
$('.mword', sid).each(function(){
    if($(this).not('.hide').length){
        u= parseInt($(this).attr('data_code')) *2 + parseInt($(this).attr('data_order')) -1;
        $(this).nextUntil('[id^="ID-' + u + '-"]',sid).addClass('hide');
    }
});
    <?php
}
?>

});
$('#learnstatus', contexth).html('<?php echo addslashes(texttodocount2($tid)); ?>');
window.parent.frames['l'].focus();
window.parent.frames['l'].setTimeout('cClick()', 100);
//]]>
</script>
<?php

pageend();

?>
