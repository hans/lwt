<?php

/**************************************************************
"Learning with Texts" (LWT) is free and unencumbered software 
released into the PUBLIC DOMAIN.

Anyone is free to copy, modify, publish, use, compile, sell, or
distribute this software, either in source code form or as a
compiled binary, for any purpose, commercial or non-commercial,
and by any means.

In jurisdictions that recognize copyright laws, the author or
authors of this software dedicate any and all copyright
interest in the software to the public domain. We make this
dedication for the benefit of the public at large and to the 
detriment of our heirs and successors. We intend this 
dedication to be an overt act of relinquishment in perpetuity
of all present and future rights to this software under
copyright law.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE 
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE
AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS BE LIABLE 
FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN 
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN 
THE SOFTWARE.

For more information, please refer to [http://unlicense.org/].
***************************************************************/

/**************************************************************
Call: delete_mword.php?wid=[wordid]&tid=[textid]
Delete an expression 
***************************************************************/

require_once( 'settings.inc.php' );
require_once( 'connect.inc.php' );
require_once( 'dbutils.inc.php' );
require_once( 'utilities.inc.php' );

$showAll = getSettingZeroOrOne('showallwords',1);

$tid = $_REQUEST['tid'];
$wid = $_REQUEST['wid'];
$word = get_first_value("select WoText as value from " . $tbpref . "words where WoID = " . $wid);
pagestart("Term: " . $word, false);
$m1 = runsql('delete from ' . $tbpref . 'words where WoID = ' . $wid, '');
adjust_autoincr('words','WoID');
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
$.ajax({type: "POST",url:"ajax_save_thumbnail.php", data: { url: "DEL", woid: <?php echo $wid; ?> }, async:false});
//]]>
</script>
<?php

pageend();

?>
