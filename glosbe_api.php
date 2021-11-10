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
Call: glosbe_api.php?from=...&dest=...&phrase=...
      ... from=L2 language code (see Glosbe)
      ... dest=L1 language code (see Glosbe)
      ... phrase=... word or expression to be translated by 
                     Glosbe API (see http://glosbe.com/a-api)

Call Glosbe Translation API, analyze and present JSON results
for easily filling the "new word form"
***************************************************************/

require_once 'inc/session_utility.php';

$from = trim(stripTheSlashesIfNeeded($_REQUEST["from"]));
$dest = trim(stripTheSlashesIfNeeded($_REQUEST["dest"]));
$destorig = $dest;
$phrase = mb_strtolower(trim(stripTheSlashesIfNeeded($_REQUEST["phrase"])), 'UTF-8');
$ok = false;

pagestart_nobody('');
$titletext = '<a href="http://glosbe.com/' . $from . '/' . $dest . '/' . $phrase . '">Glosbe Dictionary (' . tohtml($from) . "-" . tohtml($dest) . "):  &nbsp; <span class=\"red2\">" . tohtml($phrase) . "</span></a>";
echo '<h3>' . $titletext . ' <img id="del_translation" src="icn/broom.png" title="Empty Translation Field" style="cursor:pointer" onclick="deleteTranslation ();"></img></h3>';
echo '<p>(Click on <img src="icn/tick-button.png" title="Choose" alt="Choose" /> to copy word(s) into above term)<br />&nbsp;</p>';

?>
<script type="text/javascript" src="js/translation_api.js" charset="utf-8"></script>
<script type="text/javascript">
//<![CDATA[
$(document).ready( function() {
<?php
if($from=='' or $dest=='') {
    echo '$("body").html("<p class=\"red\">There seems to be something wrong with the Glosbe API!</p><p class=\"red\">Please check the dictionaries in the Language Settings!</p>"); ';
}
else if($phrase=='') {
    echo '$("body").html("<p class=\"msgblue\">Term is not set!</p>");';
}
else{
?>
    var w = window.parent.frames['ro'];
    if (typeof w == 'undefined') w = window.opener;
    if (typeof w == 'undefined')$('#del_translation').remove();
    getGlosbeTranslation(<?php echo "'" ,urlencode($phrase) ,"','",$from,"','",$dest,"'"; ?>);
<?php 
}     ?>
});
//]]>
</script>
<p id="translations"></p>
<?php

echo '&nbsp;<form action="glosbe_api.php" method="get">Unhappy?<br/>Change term: 
<input type="text" name="phrase" maxlength="250" size="15" value="' . tohtml($phrase) . '">
<input type="hidden" name="from" value="' . tohtml($from) . '">
<input type="hidden" name="dest" value="' . tohtml($destorig) . '">
<input type="submit" value="Translate via Glosbe">
</form>';

pageend();

?>
