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
Call 1: trans.php?x=1&t=[textid]&i=[textpos]
				GTr translates sentence in Text t, Pos i
Call 2: trans.php?x=2&t=[text]&i=[dictURI]
				translates text t with dict via dict-url i
Get a translation from Web Dictionary
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

$x = $_REQUEST["x"];
$i = stripslashes($_REQUEST["i"]);
$t = stripslashes($_REQUEST["t"]);

if ( $x == 1 ) {
	$sql = 'select SeText, LgGoogleTranslateURI from languages, sentences, textitems where TiSeID = SeID and TiLgID = LgID and TiTxID = ' . $t . ' and TiOrder = ' . $i;
	$res = mysql_query($sql);		
	if ($res == FALSE) die("Invalid Query: $sql");
	$record = mysql_fetch_assoc($res);
	if ($record) {
		$satz = $record['SeText'];
		$trans = isset($record['LgGoogleTranslateURI']) ? $record['LgGoogleTranslateURI'] : "";
		if(substr($trans,0,1) == '*') $trans = substr($trans,1);
	} else {
		die("Error: No results: $sql"); 
	}
	mysql_free_result($res);
	if ($trans != '') {
		/*
		echo "{" . $i . "}<br />";
		echo "{" . $t . "}<br />";
		echo "{" . createTheDictLink($trans,$satz) . "}<br />";
		*/
		header("Location: " . createTheDictLink($trans,$satz));
	}	
	exit();
}	

if ( $x == 2 ) {
	/*
	echo "{" . $i . "}<br />";
	echo "{" . $t . "}<br />";
	echo "{" . createTheDictLink($i,$t) . "}<br />";
	*/
	header("Location: " . createTheDictLink($i,$t));
	exit();
}	

?>