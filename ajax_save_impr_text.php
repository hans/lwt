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
Call: ajax_save_impr_text.php?data=[value]
Save Improved Annotation
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

$textid = $_POST['id'] + 0;
$elem = $_POST['elem'];
$stringdata = stripTheSlashesIfNeeded($_POST['data']);
$data = json_decode ($stringdata);

$val = $data->{$elem};
if(substr($elem,0,2) == "rg") {
	if($val == "") $val = $data->{'tx' . substr($elem,2)}; 
}
$line = substr($elem,2) + 0;

// Save data
$success = "NOTOK";
$ann = get_first_value("select TxAnnotatedText as value from texts where TxID = " . $textid);
$items = preg_split('/[\n]/u', $ann);
if (count($items) >= $line) {
	$vals = preg_split('/[\t]/u', $items[$line-1]);
	if ($vals[0] == 1 && count($vals) == 4) {
		$vals[3] = $val;
		$items[$line-1] = implode("\t", $vals);
		$dummy = runsql('update texts set ' .
	'TxAnnotatedText = ' . convert_string_to_sqlsyntax(implode("\n",$items)) . ' where TxID = ' . $textid, "");
		$success = "OK";
	}
}

// Only for debug
echo 'Line ' . $line . ' changed: ' . $val . " / " . $success;

?>