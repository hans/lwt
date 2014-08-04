<?php

require_once( 'settings.inc.php' );
require_once( 'connect.inc.php' );
require_once( 'dbutils.inc.php' );
require_once( 'utilities.inc.php' ); 

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
$gglink = makeOpenDictStr(createTheDictLink('*http://translate.google.com/#' . $_GET["sl"] . '/' . $_GET["tl"] . '/',$_GET["text"]), " more...");

pagestart_nobody('');
if (!isset($_GET['sent'])){
echo '<h3>Google Translate:  &nbsp; <span class="red2" id="textToSpeak" style="cursor:pointer" title="Click on expression for pronounciation" onclick="var txt = $(\'#textToSpeak\').text();var audio = new Audio();audio.src =\'tts.php?tl=' . $_GET["sl"] . '&q=\' + txt;audio.play();">' . tohtml($_GET["text"]) . '</span></h3>';
echo '<p>(Click on <img src="icn/tick-button.png" title="Choose" alt="Choose" /> to copy word(s) into above term)<br />&nbsp;</p>';
?>
<script type="text/javascript">
//<![CDATA[
function addTranslation (s) {
	var w = window.parent.frames['ro'];
	if (typeof w == 'undefined') w = window.opener;
	if (typeof w == 'undefined') {
		alert ('Translation can not be copied!');
		return;
	}
	var c = w.document.forms[0].WoTranslation;
	if (typeof c != 'object') {
		alert ('Translation can not be copied!');
		return;
	}
	var oldValue = c.value;
	if (oldValue.trim() == '') {
		c.value = s;
		w.makeDirty();
	}
	else {
		if (oldValue.indexOf(s) == -1) {
			c.value = oldValue + ' / ' + s;
			w.makeDirty();
		}
		else {
			if (confirm('"' + s + '" seems already to exist as a translation.\nInsert anyway?')) { 
				c.value = oldValue + ' / ' + s;
				w.makeDirty();
			}
		}
	}
}
//]]>
</script>
<?php
if ($file != '') {
				$word = trim(strip_tags($file));
				echo '<span class="click" onclick="addTranslation(' . prepare_textdata_js($word) . ');"><img src="icn/tick-button.png" title="Copy" alt="Copy" /> &nbsp; ' . $word . '</span><br /><br />' . $gglink . "\n";
}
}
else echo '<h3>Sentence:</h3><span class="red2">' . tohtml($_GET["text"]) . '</span><br><br><h3>Google Translate:</h3>' . $gglink . '<br><table class="tab2" cellspacing="0" cellpadding="0"><tr><td class="td1bot center" style="border-top-right-radius:inherit;" colspan="1">'. $file . '</td></tr></table>';
pageend();

?>
