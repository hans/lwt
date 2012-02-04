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
Call: upload_words.php?....
      ... op=Import ... do the import
Import terms from file or Text area
***************************************************************/

require 'lwt-startup.php';

function my_str_getcsv($input) {
  $temp=fopen("php://memory", "rw");
  fwrite($temp, $input);
  fseek($temp, 0);
  $data = fgetcsv($temp);
  fclose($temp);
  return $data;
}

function notempty($var) {
	return(trim($var) != '');
}

function limit20(&$item, $key) {
	$item = mb_substr($item,0,20);
}

function savetag($item, $key, $wid) {
	runsql('insert into tags (TgText) values(' . db_text_prepare($item) . ')', "");
	runsql('insert into wordtags (WtWoID, WtTgID) select ' . $wid . ', TgID from tags where TgText = ' . db_text_prepare($item), "");
}

pagestart('Import Terms',true);
$message = '';

// Import

if (isset($_REQUEST['op'])) {

	// INSERT

	if ($_REQUEST['op'] == 'Import') {

		$col[0] = $_REQUEST["Col1"];
		$col[1] = $_REQUEST["Col2"];
		$col[2] = $_REQUEST["Col3"];
		$col[3] = $_REQUEST["Col4"];
		$col[4] = $_REQUEST["Col5"];
		$overwrite = ($_REQUEST["Over"] == '1');
		$tabs = $_REQUEST["Tab"];

		$sqlct = 0;
		$lang = $_REQUEST["LgID"];
		$status = $_REQUEST["WoStatus"];

		$protokoll = '<h4>Import Report (Language: ' . getLanguage($lang) . ', Status: ' . $status . ')</h4><table class="tab1" cellspacing="0" cellpadding="5"><tr><th class="th1">Line</th><th class="th1">Term</th><th class="th1">Translation</th><th class="th1">Romanization</th><th class="th1">Sentence</th><th class="th1">Tag List</th><th class="th1">Message</th></tr>';

		if ( isset($_FILES["thefile"]) && $_FILES["thefile"]["tmp_name"] != "" && $_FILES["thefile"]["error"] == 0 ) {
			$lines = file($_FILES["thefile"]["tmp_name"], FILE_IGNORE_NEW_LINES);
		}
		else {
			$lines = explode("\n",prepare_textdata($_REQUEST["Upload"]));
		}
		$l = count($lines);
		for ($i=0; $i<$l; $i++) {
  		if ($tabs == 'h')
  			$lines[$i] = explode("#",trim(str_replace("\t", " ",$lines[$i])));
  		elseif ($tabs == 'c')
  			$lines[$i] = my_str_getcsv(trim(str_replace("\t", " ",$lines[$i])));
			else
  			$lines[$i] = explode("\t",trim($lines[$i]));
  		$k = count($lines[$i]);
  		unset($w,$t,$r,$s,$g);
  		for ($j=0; $j<5; $j++) {
  			if ($k > $j) eval('if (! isset($' . $col[$j] . ')) { $' . $col[$j] . ' = trim($lines[$i][' . $j . ']); }');
  		}
			if (! isset($w)) $w='';
			if (! isset($t)) $t='';
			if (! isset($r)) $r='';
			if (! isset($s)) $s='';
			if (! isset($g)) $g='';
			$w = limitlength($w,250);
			$wl = limitlength(mb_strtolower($w, 'UTF-8'),250);
			$t = limitlength($t,500);
			$r = limitlength($r,100);
			$s = limitlength($s,1000);
			$g = explode(",",trim(str_replace(" ", ",",$g)));
			$g = array_filter($g, "notempty");
 			array_walk($g, 'limit20');
  		$protokoll .= '<tr><td class="td1 right">' . ($i+1) . '</td><td class="td1">' . tohtml($w) . '</td><td class="td1">' . tohtml($t) . '</td><td class="td1">' . tohtml($r) . '</td><td class="td1">' . tohtml($s) . '</td><td class="td1">' . implode(", ", $g) . '</td>';
 			if ( $w != '' ) {
 				if ($t == '') $t = '*';
 				$excnt = get_first_value('select count(*) as value from words where WoLgID = ' . $lang . ' and WoTextLC=' . db_text_prepare($wl));
 				if ($excnt > 0 ) { // exists
 					if ($overwrite) { // update
	 					$msg1 = runsql('delete from words where WoLgID = ' . $lang . ' and WoTextLC=' . db_text_prepare($wl), "Exists, deleted");
	 					runsql("DELETE wordtags FROM (wordtags LEFT JOIN words on WtWoID = WoID) WHERE WoID IS NULL",'');
	 					$msg2 = runsql('insert into words (WoLgID, WoTextLC, WoText, WoStatus, WoTranslation, WoRomanization, WoSentence, WoStatusChanged,' .  make_score_random_insert_update('iv') . ') values ( ' . $lang . ', ' .
						db_text_prepare($wl) . ', ' .
						db_text_prepare($w) . ', ' .
						$status . ', ' .
						db_text_prepare($t) . ', ' .
						db_text_prepare($r) . ', ' .
						db_text_prepare($s) . ', NOW(), ' .
make_score_random_insert_update('id') . ')',"Imported");
						$wid = get_last_key();
						array_walk($g,'savetag',$wid);
 						$sqlct++;
 						$protokoll .= '<td class="td1">' . tohtml($msg1 . ' / ' . $msg2) . ' (' . $sqlct . ')</td></tr>';
 					}
 					else { // no overwrite
 						$protokoll .= '<td class="td1"><span class="red2">EXISTS, NOT IMPORTED</span></td></tr>';
 					} // no overwrite
 				} // exists
 				else { // exists not
 					$msg1 = runsql('insert into words (WoLgID, WoTextLC, WoText, WoStatus, WoTranslation, WoRomanization, WoSentence, WoStatusChanged,' .  make_score_random_insert_update('iv') . ') values ( ' . $lang . ', ' .
					db_text_prepare($wl) . ', ' .
					db_text_prepare($w) . ', ' .
					$status . ', ' .
					db_text_prepare($t) . ', ' .
					db_text_prepare($r) . ', ' .
					db_text_prepare($s) . ', NOW(), ' .
make_score_random_insert_update('id') . ')',"Imported");
					$wid = get_last_key();
					array_walk($g,'savetag',$wid);
 					$sqlct++;
 					$protokoll .= '<td class="td1">' . tohtml($msg1) . ' (' . $sqlct . ')' . '</td></tr>';
 				}
 			} // $w != '' && $t != ''
 			else {
  			$protokoll .= '<td class="td1"><span class="red2">NOT IMPORTED (term and/or translation missing)</span></td></tr>';
 			}
		} // for ($i=0; $i<$l; $i++)

		echo '<p class="red">*** Imported terms: ' . $sqlct . ' of ' . $l . ' *** ' . errorbutton('Error') . '</p>';
  	$protokoll .= '</table>';
		echo $protokoll;


	} // $_REQUEST['op'] == 'Import'

	else {
		$message = 'Error: Wrong Operation: ' . $_REQUEST['op'];
		echo error_message_with_hide($message,0);
	}

} else {

?>

	<form enctype="multipart/form-data" class="validate" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="{return confirm ('Did you double-check everything?\nAre you sure?');}">
	<table class="tab3" cellspacing="0" cellpadding="5">
	<tr>
	<td class="td1 center"><b>Language:</b></td>
	<td class="td1">
	<select name="LgID" class="notempty setfocus">
	<?php
	echo get_languages_selectoptions(getSetting('currentlanguage'),'[Choose...]');
	?>
	</select> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
	</td>
	</tr>
	<tr>
	<td class="td1 center"><b>Import Data:</b><br /><br />
	Format per line:<br />
	C1 D C2 D C3 D C4 D C5<br />
	<br /><b>Field Delimiter "D":</b><br />
	<select name="Tab">
	<option value="c" selected="selected">Comma "," [CSV File, LingQ]</option>
	<option value="t">TAB (ASCII 9) [TSV File]</option>
	<option value="h">Hash "#" [Direct Input]</option>
	</select>
	<br />
	<br />
	<b>Column Assignment:</b><br />
	"C1": <select name="Col1">
	<option value="w" selected="selected">Term</option>
	<option value="t">Translation</option>
	<option value="r">Romanization</option>
	<option value="s">Sentence</option>
	<option value="g">Tag List</option>
	<option value="x">Don't import</option>
	</select><br />
	"C2": <select name="Col2">
	<option value="w">Term</option>
	<option value="t" selected="selected">Translation</option>
	<option value="r">Romanization</option>
	<option value="s">Sentence</option>
	<option value="g">Tag List</option>
	<option value="x">Don't import</option>
	</select><br />
	"C3": <select name="Col3">
	<option value="w">Term</option>
	<option value="t">Translation</option>
	<option value="r">Romanization</option>
	<option value="s">Sentence</option>
	<option value="g">Tag List</option>
	<option value="x" selected="selected">Don't import</option>
	</select><br />
	"C4": <select name="Col4">
	<option value="w">Term</option>
	<option value="t">Translation</option>
	<option value="r">Romanization</option>
	<option value="s">Sentence</option>
	<option value="g">Tag List</option>
	<option value="x" selected="selected">Don't import</option>
	</select><br />
	"C5": <select name="Col5">
	<option value="w">Term</option>
	<option value="t">Translation</option>
	<option value="r">Romanization</option>
	<option value="s">Sentence</option>
	<option value="g">Tag List</option>
	<option value="x" selected="selected">Don't import</option>
	</select><br />
	<br /><b>Overwrite existent<br />terms</b>:
	<select name="Over">
	<option value="0" selected="selected">No</option>
	<option value="1">Yes</option>
	</select>
	<br /><br />
	<b>Important:</b><br />
	You must specify the term.<br />
	Translation, romanization, <br />sentence and tag list<br />are optional. The tag list <br />must be separated either<br />by spaces or commas.
	</td>
	<td class="td1">
	Either specify a <b>File to upload</b>:<br />
	<input name="thefile" type="file" /><br /><br />
	<b>Or</b> type in or paste from clipboard (do <b>NOT</b> specify file):<br />
	<textarea name="Upload" cols="60" rows="25"></textarea>
	</td>
	</tr>
	<tr>
	<td class="td1 center"><b>Status</b> for all uploaded terms:</td>
	<td class="td1"><select class="notempty" name="WoStatus"><?php echo get_wordstatus_selectoptions(NULL,false,false); ?></select> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
	</tr>
	<tr>
	<td class="td1 center" colspan="2"><span class="red2">A DATABASE <input type="button" value="BACKUP" onclick="location.href='backup_restore.php';" /> MAY BE ADVISABLE!<br />PLEASE DOUBLE-CHECK EVERYTHING!</span><br /><input type="button" value="&lt;&lt; Back" onclick="location.href='index.php';" /> &nbsp; &nbsp; &nbsp; | &nbsp; &nbsp; &nbsp; <input type="submit" name="op" value="Import" /></td>
	</tr>
	</table>
	</form>

	<p>Sentences should contain the term in curly brackets "... {term} ...".<br />
	If not, such sentences can be automatically created later with the <br />"Set Term Sentences" action in the <input type="button" value="My Texts" onclick="location.href='edit_texts.php?query=&amp;page=1';" /> screen.</p>

<?php

}

pageend();

?>