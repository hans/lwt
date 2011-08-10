<?php

/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions, 
unless such conditions are required by law.

Developed by J. Pierre in 2011.
***************************************************************/

/**************************************************************
Call: check_text.php?...
			op=Check ... do the check
Check (parse & split) a Text (into sentences/words)
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

pagestart('Check a Text',true);

if (isset($_REQUEST['op'])) {
	
	echo '<p><input type="button" value="&lt;&lt; Back" onclick="history.back();" /></p>';
	echo checkText($_REQUEST['TxText'], $_REQUEST['TxLgID']);
	echo '<p><input type="button" value="&lt;&lt; Back" onclick="history.back();" /></p>';

} else {

?>
<form class="validate" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<table class="tab3" cellspacing="0" cellpadding="5">
<tr>
<td class="td1 right">Language:</td>
<td class="td1">
<select name="TxLgID" class="notempty setfocus">
<?php
echo get_languages_selectoptions(getSetting('currentlanguage'),'[Choose...]');
?>
</select> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
</td>
</tr>
<tr>
<td class="td1 right">Text:</td>
<td class="td1">
<textarea name="TxText" class="notempty" cols="60" rows="20"></textarea> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
</td>
</tr>
<tr>
<td class="td1 right" colspan="2">
<input type="button" value="&lt;&lt; Back" onclick="location.href='index.php';" /> 
<input type="submit" name="op" value="Check" /></td>
</tr>
</table>
</form>
<?php

}

pageend();

?>