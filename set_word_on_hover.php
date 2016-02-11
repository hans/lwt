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

***************************************************************/

require_once( 'settings.inc.php' );
require_once( 'connect.inc.php' );
require_once( 'dbutils.inc.php' );
require_once( 'utilities.inc.php' );
require_once( 'googleTranslateClass.php' );

$translation = '*'; 
if($_REQUEST['status']==1){
	$tl=$_GET["tl"];
	$sl=$_GET["sl"];
	$text=$_GET["text"];

	$tl_array = GoogleTranslate::staticTranslate($text,$sl,$tl);
	if($tl_array) $translation = $tl_array[0];
	if($translation == $_GET["text"])$translation = '*';

	header('Pragma: no-cache');
	header('Expires: 0');
}

$word = convert_string_to_sqlsyntax($_REQUEST['text']);
$wordlc = convert_string_to_sqlsyntax(mb_strtolower($_REQUEST['text'], 'UTF-8'));

$langid = get_first_value("select TxLgID as value from " . $tbpref . "texts where TxID = " . $_REQUEST['tid']);

			$message = runsql('insert into ' . $tbpref . 'words (WoLgID, WoTextLC, WoText, ' .
				'WoStatus, WoTranslation, WoSentence, WoRomanization, WoStatusChanged,' .  make_score_random_insert_update('iv') . ') values( ' . 
				$langid . ', ' .
				$wordlc . ', ' .
				$word . ', ' .
				$_REQUEST["status"] . ', ' .
				convert_string_to_sqlsyntax($translation) . ', "", "", NOW(), ' .  
make_score_random_insert_update('id') . ')', "Term saved");
			$wid = get_last_key();
			do_mysqli_query ('UPDATE ' . $tbpref . 'textitems2 SET Ti2WoID = ' . $wid . ' WHERE Ti2LgID = ' . $langid . ' AND LOWER(Ti2Text) =' . $wordlc);
			$hex = strToClassName(prepare_textdata(mb_strtolower($_REQUEST['text'], 'UTF-8')));


pagestart("New Term: " . $word,false);

echo '<p>Status: ' . get_colored_status_msg($_REQUEST['status']) . '</p><br />';
if($translation != '*')echo '<p>Translation: <b>' . tohtml($translation)  . '</b></p>';

?>
<script type="text/javascript">
//<![CDATA[
var context = window.parent.frames['l'].document;
var contexth = window.parent.frames['h'].document;
var title = window.parent.frames['l'].JQ_TOOLTIP?'':make_tooltip(<?php echo prepare_textdata_js($_REQUEST['text']); ?>,<?php echo prepare_textdata_js($translation); ?>,'','<?php echo $_REQUEST["status"]; ?>');
$('.TERM<?php echo $hex; ?>', context).removeClass('status0').addClass('status<?php echo $_REQUEST["status"]; ?> word<?php echo $wid; ?>').attr('data_status','<?php echo $_REQUEST["status"]; ?>').attr('data_wid','<?php echo $wid; ?>').attr('title',title).attr('data_trans','<?php echo tohtml($translation); ?>');
$('#learnstatus', contexth).html('<?php echo addslashes(texttodocount2($_REQUEST['tid'])); ?>');
window.parent.frames['l'].focus();
window.parent.frames['l'].setTimeout('cClick()', 100);
//]]>
</script>
<?php

pageend();

?>
