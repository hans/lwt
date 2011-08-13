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

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

function my_str_getcsv($input) {
  $temp=fopen("php://memory", "rw");
  fwrite($temp, $input);
  fseek($temp, 0);
  $data = fgetcsv($temp);
  fclose($temp);
  return $data;
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
		$overwrite = ($_REQUEST["Over"] == '1');
		$tabs = $_REQUEST["Tab"];
		
		$sqlct = 0;
		$lang = $_REQUEST["LgID"];
		$status = $_REQUEST["WoStatus"];
		$musthavetransl = $status >= 1 && $status <= 5;
		
		$protokoll = '<h4>Import Report (Language: ' . getLanguage($lang) . ', Status: ' . $status . ')</h4><table class="tab1" cellspacing="0" cellpadding="5"><tr><th class="th1">Line</th><th class="th1">Term</th><th class="th1">Translation</th><th class="th1">Romanization</th><th class="th1">Sentence</th><th class="th1">Message</th></tr>';
		
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
  		unset($w,$t,$r,$s);
  		for ($j=0; $j<4; $j++) {
  			if ($k > $j) eval('if (! isset($' . $col[$j] . ')) { $' . $col[$j] . ' = trim($lines[$i][' . $j . ']); }');
  		}
			if (! isset($w)) $w='';
			if (! isset($t)) $t='';
			if (! isset($r)) $r='';
			if (! isset($s)) $s='';
			$w = limitlength($w,250);
			$wl = limitlength(mb_strtolower($w, 'UTF-8'),250);
			$t = limitlength($t,500);
			$r = limitlength($r,100);
			$s = limitlength($s,1000);  					
  		$protokoll .= '<tr><td class="td1 right">' . ($i+1) . '</td><td class="td1">' . tohtml($w) . '</td><td class="td1">' . tohtml($t) . '</td><td class="td1">' . tohtml($r) . '</td><td class="td1">' . tohtml($s) . '</td>';
 			if ( $w != '' && ($t != '' || ($t == '' && (! $musthavetransl)))) {
 				if ($t == '') $t = '*';
 				$excnt = get_first_value('select count(*) as value from words where WoLgID = ' . $lang . ' and WoTextLC=' . convert_string_to_sqlsyntax($wl));
 				if ($excnt > 0 ) { // exists
 					if ($overwrite) { // update
	 					$msg1 = runsql('delete from words where WoLgID = ' . $lang . ' and WoTextLC=' . convert_string_to_sqlsyntax($wl), "Exists, deleted");
	 					$msg2 = runsql('insert into words (WoLgID, WoTextLC, WoText, WoStatus, WoTranslation, WoRomanization, WoSentence, WoStatusChanged,' .  make_score_random_insert_update('iv') . ') values ( ' . $lang . ', ' .
						convert_string_to_sqlsyntax($wl) . ', ' .
						convert_string_to_sqlsyntax($w) . ', ' .
						$status . ', ' .
						convert_string_to_sqlsyntax($t) . ', ' .
						convert_string_to_sqlsyntax($r) . ', ' .
						convert_string_to_sqlsyntax($s) . ', NOW(), ' .  
make_score_random_insert_update('id') . ')',"Imported");
 						$sqlct++;
 						$protokoll .= '<td class="td1">' . tohtml($msg1 . ' / ' . $msg2) . ' (' . $sqlct . ')</td></tr>';
 					}
 					else { // no overwrite
 						$protokoll .= '<td class="td1"><span class="red2">EXISTS, NOT IMPORTED</span></td></tr>';
 					} // no overwrite
 				} // exists
 				else { // exists not
 					$msg1 = runsql('insert into words (WoLgID, WoTextLC, WoText, WoStatus, WoTranslation, WoRomanization, WoSentence, WoStatusChanged,' .  make_score_random_insert_update('iv') . ') values ( ' . $lang . ', ' .
					convert_string_to_sqlsyntax($wl) . ', ' .
					convert_string_to_sqlsyntax($w) . ', ' .
					$status . ', ' .
					convert_string_to_sqlsyntax($t) . ', ' .
					convert_string_to_sqlsyntax($r) . ', ' .
					convert_string_to_sqlsyntax($s) . ', NOW(), ' .  
make_score_random_insert_update('id') . ')',"Imported");
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
	Format:<br />
	C1 FD C2 FD C3 FD C4<br />
	<br /><b>Field Delimiter FD:</b><br />
	<select name="Tab">
	<option value="c" selected="selected">Comma "," [CSV File, LingQ]</option>
	<option value="t">TAB (ASCII 9) [TSV File]</option>
	<option value="h">Hash "#" [Direct Input]</option>
	</select>
	<br />
	<br />
	<b>Column Assignment:</b><br />
	C1: <select name="Col1">
	<option value="w" selected="selected">Term</option>
	<option value="t">Translation</option>
	<option value="r">Romanization</option>
	<option value="s">Sentence</option>
	<option value="x">Don't import</option>
	</select><br />
	C2: <select name="Col2">
	<option value="w">Term</option>
	<option value="t" selected="selected">Translation</option>
	<option value="r">Romanization</option>
	<option value="s">Sentence</option>
	<option value="x">Don't import</option>
	</select><br />
	C3: <select name="Col3">
	<option value="w">Term</option>
	<option value="t">Translation</option>
	<option value="r">Romanization</option>
	<option value="s" selected="selected">Sentence</option>
	<option value="x">Don't import</option>
	</select><br />
	C4: <select name="Col4">
	<option value="w">Term</option>
	<option value="t">Translation</option>
	<option value="r">Romanization</option>
	<option value="s">Sentence</option>
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
	You must also specify the<br />translation, if status<br />is set to 1 .. 5.<br />
	Romanization <br />and sentence are optional.
	</td>
	<td class="td1">
	Either specify a <b>File to upload</b>:<br />
	<input name="thefile" type="file" /><br/><br/>
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