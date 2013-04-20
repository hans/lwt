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
Call: inline_edit.php?...
...
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

$value = (isset($_POST['value'])) ? $_POST['value'] : "";
$id = (isset($_POST['id'])) ? $_POST['id'] : "";

if (substr($id, 0, 5) == "trans") {
	// usleep(200000);
	$id = substr($id, 5);
	if(trim($value) == '') $value='*';
	$message = runsql('update words set WoTranslation = ' . 
		convert_string_to_sqlsyntax(repl_tab_nl($value)) . ' where WoID = ' . $id,
		"");
	echo get_first_value("select WoTranslation as value from words where WoID = " . $id);
	exit;
}

if (substr($id, 0, 5) == "roman") {
	// usleep(100000);
	$id = substr($id, 5);
	$message = runsql('update words set WoRomanization = ' . 
		convert_string_to_sqlsyntax(repl_tab_nl($value)) . ' where WoID = ' . $id,
		"");
	echo get_first_value("select WoRomanization as value from words where WoID = " . $id);
	exit;
}

echo "ERROR - please refresh page!";

?>