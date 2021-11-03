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
Call: insert_word_ignore.php?tid=[textid]&ord=[textpos]
Ignore single word (new term with status 98)
***************************************************************/

require_once( 'settings.inc.php' );
require_once( 'connect.inc.php' );
require_once( 'dbutils.inc.php' );
require_once( 'utilities.inc.php' );

$translation = '*'; 
if($_REQUEST['status']==1){
	$qs = http_build_query(array("ie" => "utf-8","sl" => $_GET["sl"],"tl" => $_GET["tl"], "text" => $_GET["text"]));
	$ctx = stream_context_create(array("http"=>array("method"=>"GET","header"=>"Referer: \r\n")));
	$file = file_get_contents("http://translate.google.com/?".$qs, false, $ctx);

	header('Pragma: no-cache');
	header('Expires: 0');
	$cs1 = strpos ($file,'content="text/html; charset=');
	$cs1 = strpos ($file,'=',$cs1+10)+1;
	$cs2 = strpos ($file,'"',$cs1)-$cs1;
	$charset = substr($file,$cs1,$cs2);
	$pos = strpos ($file,'result_box');
	$pos = strpos ($file,'span title="',$pos);
	$pos2 = strpos ($file,'>',$pos);
	$pos3 = strpos ($file,'<',$pos2);
	$len = $pos3 - $pos2 -1;
	$file = substr($file,$pos2+1,$len);
	if(!empty($charset))$file = mb_convert_encoding ($file,"UTF-8",$charset);
	if ($file != '') {
		$translation = trim(strip_tags($file));
		if($translation == $_GET["text"])$translation = '*';
	}
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
			mysql_query ('UPDATE ' . $tbpref . 'textitems2 SET Ti2WoID = ' . $wid . ' WHERE Ti2LgID = ' . $langid . ' AND LOWER(Ti2Text) =' . $wordlc);
			$hex = strToClassName(prepare_textdata(mb_strtolower($_REQUEST['text'], 'UTF-8')));


pagestart("Term: " . $word,false);

?>
<script type="text/javascript">
//<![CDATA[
var context = window.parent.frames['l'].document;
var contexth = window.parent.frames['h'].document;
var title = make_tooltip(<?php echo prepare_textdata_js($_REQUEST['text']); ?>,<?php echo prepare_textdata_js($translation); ?>,'','<?php echo $_REQUEST["status"]; ?>');
$('.TERM<?php echo $hex; ?>', context).removeClass('status0').addClass('status<?php echo $_REQUEST["status"]; ?> word<?php echo $wid; ?>').attr('data_status','<?php echo $_REQUEST["status"]; ?>').attr('data_wid','<?php echo $wid; ?>').attr('title',title);
$('#learnstatus', contexth).html('<?php echo texttodocount2($_REQUEST['tid']); ?>');
window.parent.frames['l'].focus();
window.parent.frames['l'].setTimeout('cClick()', 100);
//]]>
</script>
<?php

pageend();

?>
