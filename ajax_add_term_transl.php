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

function trim_value(&$value) 
{ 
	$value = trim($value); 
}

$wid = $_POST['id'] + 0;
$data = trim(stripTheSlashesIfNeeded($_POST['data']));

// Save data
$success = "NOTOK";

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
