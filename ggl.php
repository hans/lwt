<?php

require_once 'utilities.inc.php';
require_once 'googleTimeToken.php' ;
require_once 'googleTranslateClass.php' ;

$tl=$_GET["tl"];
$sl=$_GET["sl"];
$text=$_GET["text"];

header('Pragma: no-cache');
header('Expires: 0');

if(trim($text)!='') {
    $file = GoogleTranslate::staticTranslate($text, $sl, $tl, getGoogleTimeToken());

    $gglink = makeOpenDictStr(createTheDictLink('*http://translate.google.com/#' . $sl . '/' . $tl . '/', $text), " more...");

    pagestart_nobody('');
    if (!isset($_GET['sent'])) {
        echo '<h3>Google Translate:  &nbsp; <span class="red2" id="textToSpeak" style="cursor:pointer" title="Click on expression for pronunciation" onclick="var txt = $(\'#textToSpeak\').text();var audio = new Audio();audio.src =\'tts.php?tl=' . $sl . '&q=\' + txt;audio.play();">' . tohtml($text) . '</span> <img id="del_translation" src="icn/broom.png" style="cursor:pointer" title="Empty Translation Field" onclick="deleteTranslation ();"></img></h3>';
        echo '<p>(Click on <img src="icn/tick-button.png" title="Choose" alt="Choose" /> to copy word(s) into above term)<br />&nbsp;</p>';
    ?>
    <script type="text/javascript" src="js/translation_api.js" charset="utf-8"></script>
    <script type="text/javascript">
    //<![CDATA[
    $(document).ready( function() {
     var w = window.parent.frames['ro'];
     if (typeof w == 'undefined') w = window.opener;
     if (typeof w == 'undefined')$('#del_translation').remove();
    });

    //]]>
    </script>
    <?php
    foreach($file as $word){
        echo '<span class="click" onclick="addTranslation(' . prepare_textdata_js($word) . ');"><img src="icn/tick-button.png" title="Copy" alt="Copy" /> &nbsp; ' . $word . '</span><br />';
    }
    if (!empty($file)) {
        echo '<br />' . $gglink . "\n";
    }

    echo '&nbsp;<hr />&nbsp;<form action="ggl.php" method="get">Unhappy?<br/>Change term: 
	<input type="text" name="text" maxlength="250" size="15" value="' . tohtml($text) . '">
	<input type="hidden" name="sl" value="' . tohtml($sl) . '">
	<input type="hidden" name="tl" value="' . tohtml($tl) . '">
	<input type="submit" value="Translate via Google Translate">
	</form>';
    }
    else { echo '<h3>Sentence:</h3><span class="red2">' . tohtml($text) . '</span><br><br><h3>Google Translate:</h3>' . $gglink . '<br><table class="tab2" cellspacing="0" cellpadding="0"><tr><td class="td1bot center" colspan="1">'. $file[0] . '</td></tr></table>'; 
    }
}
else {
    pagestart_nobody('');
    echo "<p class=\"msgblue\">Term is not set!</p>";
}
pageend();

?>
