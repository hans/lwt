<?php

 /**
 * \file
 * \brief Delete an expression
 * 
 * Call: delete_mword.php?wid=[wordid]&tid=[textid]
 * 
 * @package Lwt
 * @author  LWT Project <lwt-project@hotmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/delete__mword_8php.html
 * @since   1.0.3
 */

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
var context = window.parent.document;
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
$('#learnstatus', context).html('<?php echo addslashes(texttodocount2($tid)); ?>');
window.parent.focus();
window.parent.setTimeout('cClick()', 100);
//]]>
</script>
<?php

pageend();

?>
