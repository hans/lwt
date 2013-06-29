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
Call: long_text_import.php?...
			op=...
Long Text Import
***************************************************************/

include "settings.inc.php";
include "connect.inc.php";
include "utilities.inc.php";

pagestart('Long Text Import',true);

$message = '';

if (isset($_REQUEST['op'])) {
	
	echo "<p>Processing not yet implemented.</p>";

} else {

?>

	<form enctype="multipart/form-data" class="validate" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return confirm ('Did you double-check everything?');">
	<table class="tab3" cellspacing="0" cellpadding="5">
	<tr>
	<td class="td1 right">Language:</td>
	<td class="td1">
	<select name="LgID" class="notempty setfocus">
	<?php
	echo get_languages_selectoptions(getSetting('currentlanguage'),'[Choose...]');
	?>
	</select> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /> 
	</td>
	</tr>
	<tr>
	<td class="td1 right">Title:</td>
	<td class="td1"><input type="text" class="notempty" name="TxTitle" value="" maxlength="200" size="60" /> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
	</tr>
	<tr>
	<td class="td1 right">
		Text:
	</td>
	<td class="td1">
	Either specify a <b>File to upload</b>:<br />
	<input name="thefile" type="file" /><br /><br />
	<b>Or</b> type in or paste from clipboard (do <b>NOT</b> specify file):<br />
	<textarea name="Upload" cols="60" rows="25"></textarea>
	</td>
	</tr>
	<tr>
	<td class="td1 right">Source URI:</td>
	<td class="td1"><input type="text" class="checkurl" data_info="Source URI" name="TxSourceURI" value="" maxlength="1000" size="60" /></td>
	</tr>
	<tr>
	<td class="td1 right">Tags:</td>
	<td class="td1">
	<?php echo getTextTags(0); ?>
	</td>
	</tr>
	<tr>
	<td class="td1 right" colspan="2"><input type="button" value="&lt;&lt; Back" onclick="location.href='index.php';" /> &nbsp; &nbsp; &nbsp; | &nbsp; &nbsp; &nbsp; <input type="submit" name="op" value="Import" />
	</td>
	</tr>
	</table>
	</form>

<?php

}

pageend();

?>