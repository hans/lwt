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
Call: upload_words.php?....
      ... op=Import ... do the import 
Import terms from file or Text area
***************************************************************/

require_once( 'settings.inc.php' );
require_once( 'connect.inc.php' );
require_once( 'dbutils.inc.php' );
require_once( 'utilities.inc.php' );

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
	global $tbpref;
	if(! in_array($item,$_SESSION['TAGS'])) {
		runsql('insert into ' . $tbpref . 'tags (TgText) values(' . convert_string_to_sqlsyntax($item) . ')', "");
		get_tags($refresh = 1);
	}
	runsql('insert ignore into ' . $tbpref . 'wordtags (WtWoID, WtTgID) select ' . $wid . ', TgID from ' . $tbpref . 'tags where TgText = ' . convert_string_to_sqlsyntax($item), "");
}

pagestart('Import Terms',true);
$message = '';

// Import

if (isset($_REQUEST['op'])) {
	
	// INSERT
	
	if ($_REQUEST['op'] == 'Import') {
		$overwrite = $_REQUEST["Over"];
		$tabs = $_REQUEST["Tab"];
		$lang = $_REQUEST["LgID"];
		$status = $_REQUEST["WoStatus"];
		$sql = "select * from " . $tbpref . "languages where LgID=" . $lang;
		$res = do_mysql_query($sql);
		$record = mysql_fetch_assoc($res);
		$termchar = $record['LgRegexpWordCharacters'];
		$splitEachChar = $record['LgSplitEachChar'];
		$rtl = $record['LgRightToLeft'];
		$last_update = get_first_value("select max(WoStatusChanged) as value from " . $tbpref . "words");

		$col[1] = $_REQUEST["Col1"];
		$col[2] = $_REQUEST["Col2"];
		$col[3] = $_REQUEST["Col3"];
		$col[4] = $_REQUEST["Col4"];
		$col[5] = $_REQUEST["Col5"];
		$col=array_unique($col);

		$fields = array("txt"=>0,"tr"=>0,"ro"=>0,"se"=>0,"tl"=>0);
		$file_upl= isset($_FILES["thefile"]) && $_FILES["thefile"]["tmp_name"] != "" && $_FILES["thefile"]["error"] == 0;

		$max = max(array_keys($col));
		for ($j=1; $j<=$max; $j++) {
			if(!isset($col[$j]))$col[$j]='@dummy';
			else{
				switch ($col[$j]){
					case 'w':
						$col[$j]='WoText';
						$fields["txt"]=$j;
						break;
					case 't':
						$col[$j]='WoTranslation';
						$fields["tr"]=$j;
						break;
					case 'r':
						$col[$j]='WoRomanization';
						$fields["ro"]=$j;
						break;
					case 's':
						$col[$j]='WoSentence';
						$fields["se"]=$j;
						break;
					case 'g':
						$col[$j]='@taglist';
						$fields["tl"]=$j;
						break;
					case 'x':
						if($j==$max)unset($col[$j]);
						else $col[$j]='@dummy';
						break;
				}
			}
		}
		if ($fields["txt"]>0){
			$columns='(' . rtrim(implode(',', $col),',') . ')';
			if ($tabs == 'h')
				$tabs = ' FIELDS TERMINATED BY \'#\' ENCLOSED BY \'"\' LINES TERMINATED BY \'\\n\' ';
	  		elseif ($tabs == 'c') 
	 			$tabs = ' FIELDS TERMINATED BY \',\' ENCLOSED BY \'"\' LINES TERMINATED BY \'\\n\' ';
			else
				$tabs = ' FIELDS TERMINATED BY \'\\t\' ENCLOSED BY \'"\' LINES TERMINATED BY \'\\n\' ';
			if ($_REQUEST["IgnFirstLine"] == '1')$tabs.='IGNORE 1 LINES ';
			if ( $file_upl )$file_name= $_FILES["thefile"]["tmp_name"];
			else{
				$file_name = tempnam(sys_get_temp_dir(), "LWT");
				$temp = fopen($file_name, "w");
				fwrite($temp, prepare_textdata($_REQUEST["Upload"]));
				fseek($temp, 0);
				fclose($temp);
			}
			$sql= 'LOAD DATA LOCAL INFILE \''. $file_name .'\'';
			//$sql.= ($overwrite)?' REPLACE':(' IGNORE') ;
			if($fields["tl"]==0 and $overwrite==0){
				$sql.= ' IGNORE INTO TABLE ' . $tbpref . 'words ' . $tabs . $columns ;
				$sql.= ' SET WoLgID =  ' . $lang . ', WoTextLC = LOWER(WoText), WoWordCount = CASE WHEN WoText REGEXP \'^[' . $termchar . ']+$\' THEN 1 ELSE 0 END, WoStatus = ' . $status . ', WoStatusChanged = NOW(), ' . make_score_random_insert_update('u');
				runsql($sql ,'');
			}
			else{
				runsql('SET GLOBAL max_heap_table_size = 1024 * 1024 * 1024 * 2','');
				runsql('CREATE TEMPORARY TABLE IF NOT EXISTS ' . $tbpref . 'numbers( n  tinyint(3) unsigned NOT NULL)','');
				runsql("INSERT IGNORE INTO " . $tbpref . "numbers(n) VALUES ('1'),('2'),('3'),('4'),('5'),('6'),('7'),('8'),('9')",'');
				$sql.= ' INTO TABLE ' . $tbpref . 'tempwords ' . $tabs . $columns . ' SET WoTextLC = LOWER(WoText)';
				if($fields["tl"]!=0) $sql.= ', WoTaglist = REPLACE(@taglist," ",",")';
				runsql($sql ,'');
				if($overwrite!=3){
					$sql=($overwrite!=0)?'INSERT ':('INSERT IGNORE ');
					$sql.= ' INTO ' . $tbpref . 'words (WoTextLC , WoText, WoTranslation, WoRomanization, WoSentence, WoStatus, WoStatusChanged, WoLgID, WoWordCount,' .  make_score_random_insert_update('iv')  .') ';
$sql.= 'select *, ' . $lang . ' as LgID, CASE WHEN WoText REGEXP \'^[' . $termchar . ']+$\' THEN 1 ELSE 0 END as WordCount, ' . make_score_random_insert_update('id') . ' from (select WoTextLC , WoText, WoTranslation, WoRomanization, WoSentence, ' . $status . ' as WoStatus, NOW() as WoStatusChanged from ' . $tbpref . 'tempwords) as tw';
					//if($overwrite==1)$sql.= ' ON DUPLICATE KEY UPDATE ' . $tbpref . 'words.WoTranslation = tw.WoTranslation, ' . $tbpref . 'words.WoRomanization = tw.WoRomanization, ' . $tbpref . 'words.WoSentence = tw.WoSentence, ' . $tbpref . 'words.WoStatus = tw.WoStatus, ' . $tbpref . 'words.WoStatusChanged = tw.WoStatusChanged';
					if($overwrite==1)$sql.= ' ON DUPLICATE KEY UPDATE ' . ($fields["tr"]?$tbpref . 'words.WoTranslation = tw.WoTranslation, ':'') . ($fields["ro"]?$tbpref . 'words.WoRomanization = tw.WoRomanization, ':'') . ($fields["se"]?$tbpref . 'words.WoSentence = tw.WoSentence, ':'') . $tbpref . 'words.WoStatus = tw.WoStatus, ' . $tbpref . 'words.WoStatusChanged = tw.WoStatusChanged';
					if($overwrite==2)$sql.= ' ON DUPLICATE KEY UPDATE ' . $tbpref . 'words.WoTranslation = case when ' . $tbpref . 'words.WoTranslation = "*" then tw.WoTranslation else ' . $tbpref . 'words.WoTranslation end, ' . $tbpref . 'words.WoRomanization = case when ' . $tbpref . 'words.WoRomanization IS NULL then tw.WoRomanization else ' . $tbpref . 'words.WoRomanization end, ' . $tbpref . 'words.WoSentence = case when ' . $tbpref . 'words.WoSentence IS NULL then tw.WoSentence else ' . $tbpref . 'words.WoSentence end, ' . $tbpref . 'words.WoStatusChanged = case when ' . $tbpref . 'words.WoSentence IS NULL or ' . $tbpref . 'words.WoRomanization IS NULL or ' . $tbpref . 'words.WoTranslation = "*" then tw.WoStatusChanged else ' . $tbpref . 'words.WoStatusChanged end';
				}
				else{
					$sql = 'UPDATE ' . $tbpref . 'words AS a JOIN ' . $tbpref . 'tempwords AS b ON a.WoTextLC = b.WoTextLC SET a.WoTranslation = CASE WHEN b.WoTranslation = "" or b.WoTranslation = "*" THEN a.WoTranslation ELSE b.WoTranslation END, a.WoRomanization = CASE WHEN b.WoRomanization IS NULL or b.WoRomanization = "" THEN a.WoRomanization ELSE b.WoRomanization END, a.WoSentence = CASE WHEN b.WoSentence IS NULL or b.WoSentence = "" THEN a.WoSentence ELSE b.WoSentence END, a.WoStatusChanged = CASE WHEN (b.WoTranslation = "" or b.WoTranslation = "*") and (b.WoRomanization IS NULL or b.WoRomanization = "") and (b.WoSentence IS NULL or b.WoSentence = "") THEN a.WoStatusChanged ELSE NOW() END';
				}
				runsql($sql ,'');
				if($fields["tl"]!=0){
				runsql('insert ignore into ' . $tbpref . 'tags (TgText) select name from (SELECT ' . $tbpref . 'tempwords.WoTextLC, SUBSTRING_INDEX(SUBSTRING_INDEX(' . $tbpref . 'tempwords.WoTaglist, \',\', ' . $tbpref . 'numbers.n), \',\', -1) name FROM ' . $tbpref . 'numbers INNER JOIN ' . $tbpref . 'tempwords ON CHAR_LENGTH(' . $tbpref . 'tempwords.WoTaglist)-CHAR_LENGTH(REPLACE(' . $tbpref . 'tempwords.WoTaglist, \',\', \'\'))>=' . $tbpref . 'numbers.n-1 ORDER BY WoTextLC, n) A','');
				runsql('INSERT IGNORE INTO ' . $tbpref . 'wordtags select WoID,TgID from (SELECT ' . $tbpref . 'tempwords.WoTextLC, SUBSTRING_INDEX(SUBSTRING_INDEX(' . $tbpref . 'tempwords.WoTaglist, \',\', ' . $tbpref . 'numbers.n), \',\', -1) name FROM ' . $tbpref . 'numbers INNER JOIN ' . $tbpref . 'tempwords ON CHAR_LENGTH(' . $tbpref . 'tempwords.WoTaglist)-CHAR_LENGTH(REPLACE(' . $tbpref . 'tempwords.WoTaglist, \',\', \'\'))>=' . $tbpref . 'numbers.n-1 ORDER BY WoTextLC, n) A,' . $tbpref . 'tags,' . $tbpref . 'words where name=TgText and A.WoTextLC=' . $tbpref . 'words.WoTextLC and WoLgID=' . $lang,'');
				}
				runsql('DROP TABLE ' . $tbpref . 'numbers','');
				runsql("truncate " . $tbpref . "tempwords" ,'');
				if($fields["tl"]!=0)get_tags(1);
			}
			if ( !$file_upl ) {unlink($file_name);}
			$mwords = get_first_value("select count(*) as value from " . $tbpref . "words where WoCreated > " . convert_string_to_sqlsyntax($last_update));
			if($mwords > 40){
				runsql('delete from ' . $tbpref . 'sentences where SeLgID = ' . $lang, 
						"Sentences deleted");
				runsql('delete from ' . $tbpref . 'textitems2 where Ti2LgID = ' . $lang, 
						"Text items deleted");
				adjust_autoincr('sentences','SeID');
				$sql = "select TxID, TxText from " . $tbpref . "texts where TxLgID = " . $lang . " order by TxID";
				$res = do_mysql_query($sql);
				$cntrp = 0;
				while ($record = mysql_fetch_assoc($res)) {
					$txtid = $record["TxID"];
					$txttxt = $record["TxText"];
					splitCheckText($txttxt, $lang, $txtid );
					$cntrp++;
				}
				mysql_free_result($res);
				//$message .= " / Reparsed texts: " . $cntrp;
			}
			elseif($mwords!=0){
				$sqlarr=array();
				$twidarr=array();
				$wocountarr=array();
				$res = do_mysql_query("select WoID, WoTextLC from " . $tbpref . "words where WoCreated > " . convert_string_to_sqlsyntax($last_update));
				while ($record = mysql_fetch_assoc($res)) {
					$wid = $record['WoID'];
					$textlc = $record['WoTextLC'];
					$wis = $textlc;
					if ($splitEachChar) {
						$textlc = preg_replace('/([^\s])/u', "$1 ", $textlc);
					}
					$len=preg_match_all('/([' . $termchar . ']+)/u',$textlc,$ma);
					if($len > 1){
						$wocountarr[]= ' WHEN ' . $wid . ' THEN ' . $len;
					}

						$notermchar='/[^' . $termchar . '](' . $textlc . ')[^' . $termchar . ']/ui';
						$sql = "SELECT * FROM " . $tbpref . "sentences where SeLgID = " . $lang . " and SeText like '%" . mysql_real_escape_string($wis) . "%'";
						$result = do_mysql_query($sql);
						while($record2 = mysql_fetch_assoc($result)){
							$string= ' ' . ($splitEachChar?preg_replace('/([^\s])/u', "$1 ", $record2['SeText']):$record2['SeText']) . ' ';
							$txtid =$record2['SeTxID'];
							$sentid =$record2['SeID'];
							$last_pos = strripos ( $string , $textlc );
							$sentoffset = preg_match('/[^' . $termchar . ']/ui', mb_substr($string,1,1, 'UTF-8'));
							while($last_pos!==false){
								$matches=array();
								if($splitEachChar || preg_match ( $notermchar, $string, $matches, 0, $last_pos - 1)==1){
									$string = substr ( $string, 0, $last_pos );
									$cnt = preg_match_all('/([' . $termchar . ']+)/u',$string,$ma);
									$pos=2*$cnt+$record2['SeFirstPos'] + $sentoffset;
								if($len!=1){
									$txt='';
									if(!($matches[1]==$textlc))$txt=$splitEachChar?$wis:$matches[1];
									$sqlarr[] = '(' . $wid . ',' . $lang . ',' . $txtid . ',' . $sentid . ',' . $pos . ',' . $len . ',' . convert_string_to_sqlsyntax_notrim_nonull($txt) . ')';
								}
								else{
									$twidarr[] = ' WHEN Ti2SeID = ' . $sentid . ' AND Ti2Order = ' . $pos . ' THEN ' . $wid;
								}
									$last_pos = strripos ( $string , $textlc );
								}
								else{
									$string = substr ( $string, 0, $last_pos );
									$last_pos = strripos ( $string , $textlc );
								}
							}
						}

				}
				mysql_free_result($result);
				mysql_free_result($res);
				if(!empty($sqlarr)){
				$sqltext = 'INSERT INTO ' . $tbpref . 'textitems2 (Ti2WoID,Ti2LgID,Ti2TxID,Ti2SeID,Ti2Order,Ti2WordCount,Ti2Text) VALUES ';
				$sqltext .= rtrim(implode(',', $sqlarr),',');
				mysql_query ($sqltext);
				}
				if(!empty($twidarr)){
				$sqltext = "UPDATE  " . $tbpref . "textitems2 SET Ti2WoID  = CASE ";
				$sqltext .= implode(' ', $twidarr) . ' ELSE 0 END where Ti2WoID=0 and Ti2WordCount = 1 and Ti2LgID = ' . $lang;
				mysql_query ($sqltext);
				}
				if(!empty($wocountarr)){
				$sqltext = "UPDATE  " . $tbpref . "words SET WoWordCount  = CASE WoID";
				$sqltext .= implode(' ', $wocountarr) . ' END where WoWordCount=0 and WoLgID = ' . $lang;
				mysql_query ($sqltext);
				}
			}
			$recno = get_first_value('select count(*) as value from ' . $tbpref . 'words where WoStatusChanged > ' . convert_string_to_sqlsyntax($last_update));
?>
<form name="form1" action="#" onsubmit="$('#res_data').load('ajax_show_imported_terms.php',{'last_update':'<?php echo $last_update; ?>','count':$('#recno').text(),'page':document.form1.page.options[document.form1.page.selectedIndex].value}); return false;"><div id="res_data"><table class="tab1"  cellspacing="0" cellpadding="2">
<?php
echo "</table></div></form>";
?>
<script type="text/javascript">
$('#res_data').load('ajax_show_imported_terms.php',{'last_update':'<?php echo $last_update; ?>','rtl':'<?php echo $rtl; ?>','count':'<?php echo $recno; ?>','page':'1'});
</script>
<?php

		}
		else if ($fields["tl"]>0){

			$columns='';
			for ($j=1; $j<=$fields["tl"]; $j++) {
				$columns.= ($j==1?'(':',') . ($j==$fields["tl"]?'@taglist':'@dummy');
			}
			$columns.= ')';
			if ($tabs == 'h')
				$tabs = ' FIELDS TERMINATED BY \'#\' ENCLOSED BY \'"\' LINES TERMINATED BY \'\\n\' ';
			elseif ($tabs == 'c')
				$tabs = ' FIELDS TERMINATED BY \',\' ENCLOSED BY \'"\' LINES TERMINATED BY \'\\n\' ';
			else
				$tabs = ' FIELDS TERMINATED BY \'\\t\' ENCLOSED BY \'"\' LINES TERMINATED BY \'\\n\' ';
			if ($_REQUEST["IgnFirstLine"] == '1')$tabs.='IGNORE 1 LINES ';
			if ( $file_upl )$file_name= $_FILES["thefile"]["tmp_name"];
			else{
				$file_name = tempnam(sys_get_temp_dir(), "LWT");
				$temp = fopen($file_name, "w");
				fwrite($temp, prepare_textdata($_REQUEST["Upload"]));
				fseek($temp, 0);
				fclose($temp);
			}
			$sql= 'LOAD DATA LOCAL INFILE \''. $file_name .'\' IGNORE INTO TABLE ' . $tbpref . 'tempwords ' . $tabs . $columns . ' SET WoTextLC = REPLACE(@taglist," ",",")';
			runsql($sql ,'');
			runsql('CREATE TEMPORARY TABLE IF NOT EXISTS ' . $tbpref . 'numbers( n  tinyint(3) unsigned NOT NULL)','');
			runsql("INSERT IGNORE INTO " . $tbpref . "numbers(n) VALUES ('1'),('2'),('3'),('4'),('5'),('6'),('7'),('8'),('9')",'');
			runsql('insert ignore into ' . $tbpref . 'tags (TgText) select name from (SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(' . $tbpref . 'tempwords.WoTextLC, \',\', ' . $tbpref . 'numbers.n), \',\', -1) name FROM ' . $tbpref . 'numbers INNER JOIN ' . $tbpref . 'tempwords ON CHAR_LENGTH(' . $tbpref . 'tempwords.WoTextLC)-CHAR_LENGTH(REPLACE(' . $tbpref . 'tempwords.WoTextLC, \',\', \'\'))>=' . $tbpref . 'numbers.n-1 ORDER BY WoTextLC, n) A','');
			runsql('DROP TABLE ' . $tbpref . 'numbers','');
			runsql("truncate " . $tbpref . "tempwords" ,'');
			get_tags(1);
			if ( !$file_upl ) {unlink($file_name);}
		}
	} // $_REQUEST['op'] == 'Import'
	
	else {
		$message = 'Error: Wrong Operation: ' . $_REQUEST['op'];
		echo error_message_with_hide($message,0);
	}

} else {

?>

	<form enctype="multipart/form-data" class="validate" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" >
	<table class="tab3" cellspacing="0" cellpadding="5">
	<tr>
	<td class="td1 center"><b>Language:</b></td>
	<td class="td1" style="border-top-right-radius:inherit;">
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
	<br /><b>Ignore first line</b>: 
	<select name="IgnFirstLine">
	<option value="0" selected="selected">No</option>
	<option value="1">Yes</option>
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
	<br /><b>Import Modus</b>:<br />
	<select name="Over">
	<option value="0" title="- don't overwrite existent terms&#x000A;- import new terms" selected="selected">Import only new terms</option>
	<option value="1" title="- overwrite existent terms&#x000A;- import new terms">Replace all fields</option>
	<option value="2" title="- update only empty fields&#x000A;- import new terms">Update empty fields</option>
	<option value="3" title="- overwrite existing terms with new not empty values&#x000A;- don't import new terms">No new terms</option>
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
	<textarea name="Upload" cols="60" rows="30"></textarea>
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
