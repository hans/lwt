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
Call: ajax_add_term_transl.php
Add a translation to term
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

$wid = $_POST['id'] + 0;
$data = trim(stripTheSlashesIfNeeded($_POST['data'])); // translation
$text = trim(stripTheSlashesIfNeeded($_POST['text'])); // only wid=0 (new)
$lang = $_POST['lang'] + 0; // only wid=0 (lang-id)

// Save data
$success = "NOTOK";

if ($wid == 0) {
	$textlc = mb_strtolower($text, 'UTF-8');
	$dummy = runsql('insert into words (WoLgID, WoTextLC, WoText, ' .
		'WoStatus, WoTranslation, WoSentence, WoRomanization, WoStatusChanged,' .  make_score_random_insert_update('iv') . ') values( ' . 
		$lang . ', ' .
		convert_string_to_sqlsyntax($textlc) . ', ' .
		convert_string_to_sqlsyntax($text) . ', 1, ' .		
		convert_string_to_sqlsyntax($data) . ', ' .
		convert_string_to_sqlsyntax('') . ', ' .
		convert_string_to_sqlsyntax('') . ', NOW(), ' .  
		make_score_random_insert_update('id') . ')', "");
	if ($dummy == 1) $success = "OK";	
	echo $success;
	exit;
}

if(get_first_value("select count(WoID) as value from words where WoID = " . $wid) == 1) {

	$oldtrans = get_first_value("select WoTranslation as value from words where WoID = " . $wid);
	
	$oldtransarr = preg_split('/[' . get_sepas()  . ']/u', $oldtrans);
	array_walk($oldtransarr, 'trim_value');
	
	if (! in_array($data, $oldtransarr)) {
		if ((trim($oldtrans) == '') || (trim($oldtrans) == '*')) {
			$oldtrans = $data;
		} else {
			$oldtrans .= ' ' . get_first_sepa() . ' ' . $data;
		}
		$dummy = runsql('update words set ' .
			'WoTranslation = ' . convert_string_to_sqlsyntax($oldtrans) . ' where WoID = ' . $wid, "");
	}
	$success = "OK";	
}

echo $success;

?>
