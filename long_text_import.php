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
	
	if ($_REQUEST['op'] == 'Split Text') {
		
		$langid = $_REQUEST["LgID"];
		$title = $_REQUEST["TxTitle"];
		$paragraph_handling = $_REQUEST["paragraph_handling"];
		$maxsent = $_REQUEST["maxsent"];
		$source_uri = $_REQUEST["TxSourceURI"];
		
		if ( isset($_FILES["thefile"]) && $_FILES["thefile"]["tmp_name"] != "" && $_FILES["thefile"]["error"] == 0 ) {
			$lines = file($_FILES["thefile"]["tmp_name"], FILE_IGNORE_NEW_LINES);
		} else {
			$lines = explode("\n",prepare_textdata($_REQUEST["Upload"]));
		}
		$count_lines = count($lines);
		
		if ($count_lines == 0 || ($count_lines == 1 && trim($lines[0]) == '')) {
			$message = "Error: No text specified!";
			echo error_message_with_hide($message,0);
		}
		else {
			echo $count_lines . " lines";
		}
	}

} else {

?>

	<form enctype="multipart/form-data" class="validate" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
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
	<textarea name="Upload" cols="60" rows="25"></textarea> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
	</td>
	</tr>
	<tr>
	<td class="td1 right">Paragraphs:</td>
	<td class="td1">
	<select name="paragraph_handling">
	<option value="1" selected="selected">ONE Newline = Paragraph ends</option>
	<option value="2">TWO Newlines = Paragraph ends; single Newline -&gt; SPACE</option>
	</select>
	<img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
	</td>
	</tr>
	<tr>
	<td class="td1 right">Sent./Text:</td>
	<td class="td1"><input type="text" class="notempty posintnumber"  data_info="Sentences per Text" name="maxsent" value="50" maxlength="3" size="3" /> ← Max. Sentences per text, and max. 65,000 bytes per text. <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
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
	<td class="td1 right" colspan="2"><input type="button" value="Cancel" onclick="location.href='index.php';" /> &nbsp; | &nbsp; <input type="submit" name="op" value="Split Text" />
	</td>
	</tr>
	</table>
	</form>

<?php

}

pageend();

?>