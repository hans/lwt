<?php

/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions, 
unless such conditions are required by law.

Developed by J. P. in 2011, 2012, 2013.
***************************************************************/

/**************************************************************
Call: check_text.php?...
			op=Check ... do the check
Check (parse & split) a Text (into sentences/words)
***************************************************************/

include "settings.inc.php";
include "connect.inc.php";
include "utilities.inc.php";

pagestart('Check a Text',true);

if (isset($_REQUEST['op'])) {

	echo '<p><input type="button" value="&lt;&lt; Back" onclick="history.back();" /></p>';
	if (strlen(prepare_textdata($_REQUEST['TxText'])) > 65000)
		echo "<p>Error: Text too long, must be below 65000 Bytes.</p>";
	else
		echo splitCheckText($_REQUEST['TxText'], $_REQUEST['TxLgID'], -1);
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
<td class="td1 right">Text:<br /><br />(max.<br />65,000<br />bytes)</td>
<td class="td1">
<textarea name="TxText" class="notempty checkbytes" data_maxlength="65000" data_info="Text" cols="60" rows="20"></textarea> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
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