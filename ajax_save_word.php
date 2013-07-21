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
Call: ajax_save_word.php?wordid=..&langid=..&worddata=..
      wordid = Word#, 0 = insert, >0 = update
      langid = Language#
      worddata = json encoded assoc. array of word data:
      WoText, WoStatus, WoTranslation, WoSentence, WoRomanization
Save word 
***************************************************************/

require_once( 'settings.inc.php' );
require_once( 'connect.inc.php' );
require_once( 'dbutils.inc.php' );
require_once( 'utilities.inc.php' );

$message = 0;

$wordid = $_POST['wordid'] + 0; // Word#, 0 = insert, >0 = update 
$langid = $_POST['langid'] + 0; // Language#
$worddataraw = stripTheSlashesIfNeeded($_POST['worddata']);
$worddata = json_decode ($worddataraw, TRUE);

$text = trim(prepare_textdata($worddata["WoText"]));
$textlc = mb_strtolower($text, 'UTF-8');
$status = $worddata["WoStatus"] + 0;
$translation_raw = repl_tab_nl($worddata["WoTranslation"]);
if ( $translation_raw == '' ) 
	$translation = '*';
else 
	$translation = $translation_raw;
$sentence = repl_tab_nl($worddata["WoSentence"]);
$rom = repl_tab_nl($worddata["WoRomanization"]);

$message = 0;

if ( $wordid == 0 ) {
	$message = runsql(
		'insert into ' . $tbpref . 'words (WoLgID, WoTextLC, WoText, ' .
		'WoStatus, WoTranslation, WoSentence, WoRomanization, WoStatusChanged,' .  make_score_random_insert_update('iv') . ') values( ' . 
		$langid . ', ' .
		convert_string_to_sqlsyntax($textlc) . ', ' .
		convert_string_to_sqlsyntax($text) . ', ' .
		$status . ', ' .
		convert_string_to_sqlsyntax($translation) . ', ' .
		convert_string_to_sqlsyntax($sentence) . ', ' .
		convert_string_to_sqlsyntax($rom) . ', ' .
		'NOW()' . ', ' .  
		make_score_random_insert_update('id') . ')', 
		"") + 0;
	if ($message == 1) $wordid = get_last_key();
}
else {
	$oldstatus = get_first_value('SELECT WoStatus as value FROM ' . $tbpref . 'words where WoID = ' . $wordid);
	if (isset($oldstatus)) {
		$oldstatus = 0 + $oldstatus;
		$statuschanged = '';
		if ($oldstatus != $status) $statuschanged = 'WoStatus = ' .	$status . 
			', WoStatusChanged = NOW(), ';
		$oldwordlc = get_first_value('SELECT WoTextLC as value FROM ' . $tbpref . 'words where WoID = ' . $wordid);
		if (! isset($oldwordlc)) $oldwordlc = '';
		if ($oldwordlc == $textlc) {
			$message = runsql('update ' . $tbpref . 'words set ' .
				'WoText = ' . convert_string_to_sqlsyntax($text) . ', ' .
				'WoTranslation = ' . convert_string_to_sqlsyntax($translation) . ', ' .
				'WoSentence = ' . convert_string_to_sqlsyntax($sentence) . ', ' .
				'WoRomanization = ' . convert_string_to_sqlsyntax($rom) . ', ' .
				$statuschanged . 
				make_score_random_insert_update('u') . 
				' where WoID = ' . $wordid, "") + 0;
		}
	}
}

if ($message == 1 && $wordid > 0) {
	runsql("DELETE from " . $tbpref . "wordtags WHERE WtWoID =" . $wordid,'');
	if (isset($worddata["WoTags"])) {
		if (is_array($worddata["WoTags"])) {
			$cnt = count($worddata["WoTags"]);
			if ($cnt > 0 ) {
				for ($i=0; $i<$cnt; $i++) {
					$tag = $worddata["WoTags"][$i];
					if(! in_array($tag, $_SESSION['TAGS'])) {
						runsql('insert into ' . $tbpref . 'tags (TgText) values(' . 
						convert_string_to_sqlsyntax($tag) . ')', "");
					}
					runsql('insert into ' . $tbpref . 'wordtags (WtWoID, WtTgID) select ' . $wordid . ', TgID from ' . $tbpref . 'tags where TgText = ' . convert_string_to_sqlsyntax($tag), "");
				}
				get_tags(1);  // refresh tags cache
			}
		}
	}
}

$msgarray = array($message, $wordid, $worddataraw);
echo json_encode($msgarray); 
usleep(500000);
 

?>