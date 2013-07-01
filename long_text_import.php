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
			$data = file_get_contents($_FILES["thefile"]["tmp_name"]);
			$data = str_replace("\r\n","\n",$data);
		} else {
			$data = prepare_textdata($_REQUEST["Upload"]);
		}
		$data = trim($data);
		
		if((0 + $paragraph_handling) == 2) {
			$data = preg_replace('/\n\s*?\n/u', '¶', $data);
			$data = str_replace("\n"," ",$data);
			$data = str_replace("¶","\n",$data);
		}
		
		if ($data == "") {
			$message = "Error: No text specified!";
			echo error_message_with_hide($message,0);
		}
		else {
			$sent_array = splitCheckText($data, $langid, -2);
			$texts = array();
			$text_index = 0;
			$texts[$text_index] = array();
			$cnt = 0;
			$bytes = 0;
			foreach ($sent_array as $item) {
				$item_len = strlen($item)+1;
				if ($item != '¶') $cnt++;
				if (($cnt <= $maxsent) && (($bytes+$item_len) < 65000)) {
					$texts[$text_index][] = $item;
					$bytes += $item_len;
				} else {
					$text_index++;
					$texts[$text_index] = array($item);
					$cnt = 1;
					$bytes = $item_len;
				}
			}

?>
			<form enctype="multipart/form-data"  action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<table class="tab3" cellspacing="0" cellpadding="5">
			<tr>
			<td class="td1" colspan="2">
			<?php echo "This long text will be split into " . count($texts) . " shorter text(s) - as follows:"; ?>
			</td>
			</tr>
			<tr>
			<td class="td1 right" colspan="2"><input type="button" value="Cancel" onclick="location.href='index.php';" /> &nbsp; | &nbsp; <input type="button" value="Go Back" onclick="history.back();" /> &nbsp; | &nbsp; <input type="submit" name="op" value="Create the <?php echo count($texts); ?> Text(s)" />
			</td>
			</tr>
<?php
			$textno = 0;
			foreach ($texts as $item) {
				$textno++;
				$textstring = str_replace("¶","\n",implode(" ",$item));
				$bytes = strlen($textstring);
?>			
			<tr>
			<td class="td1 right"><b>Text <?php echo $textno; ?>:</b><br /><br />L=<?php echo $bytes; ?></td>
			<td class="td1">
			<textarea <?php echo getScriptDirectionTag($langid); ?> name="text[<?php echo $textno; ?>]" cols="60" rows="20"><?php echo str_replace("¶","\n",implode(" ",$item)); ?></textarea>
			</td>
			</tr>
<?php
			}
?>
		</table>
		</form>
<?php
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