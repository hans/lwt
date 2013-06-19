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
Call: glosbe_api.php?from=...&dest=...&phrase=...
      ... from=L2 language code (see Glosbe)
      ... dest=L1 language code (see Glosbe)
      ... phrase=... word or expression to be translated by 
                     Glosbe API (see http://glosbe.com/a-api)

Call Glosbe Translation API, analyze and present JSON results
for easily filling the "new word form"
***************************************************************/

include "settings.inc.php";
include "connect.inc.php";
include "utilities.inc.php";

$from = trim(stripTheSlashesIfNeeded($_REQUEST["from"]));
$dest = trim(stripTheSlashesIfNeeded($_REQUEST["dest"]));
$phrase = mb_strtolower(trim(stripTheSlashesIfNeeded($_REQUEST["phrase"])), 'UTF-8');
$ok = FALSE;

if ($from != '' && $dest != '' && $phrase != '') {

	$glosbe_data = file_get_contents('http://glosbe.com/gapi/translate?from=' . urlencode($from) . '&dest=' . urlencode($dest) . '&format=json&phrase=' . urlencode($phrase));

	if(! ($glosbe_data === FALSE)) {

		$data = json_decode ($glosbe_data, true);
		if ( isset($data['phrase']) ) {
			$ok = (($data['phrase'] == $phrase) && (isset($data['tuc'])));
		}
	
	}
	
}

if ( $ok ) {

	echo "<h1>" . tohtml($data['phrase']) . "</h1>\n";

	if (count($data['tuc']) > 0) {

		echo "<p>Translations:</p>\n<ul>\n";
		foreach ($data['tuc'] as &$value) {
			if (isset($value['phrase'])) {
				if (isset($value['phrase']['text']))
					echo "<li>" . $value['phrase']['text'] . "</li>\n";
			} else if (isset($value['meanings'])) {
				if (isset($value['meanings'][0]['text']))
					echo "<li>(" .  $value['meanings'][0]['text'] . ")</li>\n";
			}
		}
		echo "</ul>\n<hr />\n<pre>\n";
		print_r($data['tuc']);
		echo "</pre>\n";
		
	} else {
	
		echo "<p>No translations available.</p>";
	
	}
	
} else {

	echo "<h1>" . tohtml($phrase) . "</h1>\n";
	echo "<p>No data available or retrieval error./p>";

}

?>