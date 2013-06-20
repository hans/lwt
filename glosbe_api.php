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

pagestart_nobody('');
$titeltext = '<a href="http://glosbe.com/' . $from . '/' . $dest . '/' . $phrase . '" target="_blank">Glosbe Dictionary (' . tohtml($from) . "-" . tohtml($dest) . "):  &nbsp; <span class=\"red2\">" . tohtml($phrase) . "</span></a>";
echo '<h3>' . $titeltext . '</h3>';
echo '<p>(Click on <img src="icn/tick-button.png" title="Choose" alt="Choose" /> to copy word(s) into above term)<br />&nbsp;</p>';

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

	if (count($data['tuc']) > 0) {
	
		$i = 0;

		echo "<p>\n";
		foreach ($data['tuc'] as &$value) {
			$word = '';
			if (isset($value['phrase'])) {
				if (isset($value['phrase']['text']))
					$word = $value['phrase']['text'];
			} else if (isset($value['meanings'])) {
				if (isset($value['meanings'][0]['text']))
					$word = $value['meanings'][0]['text'];
			}
			if ($word != '') {
				echo '<span class="click" onclick=""><img src="icn/tick-button.png" title="Copy" alt="Copy" /></span> &nbsp;' . $word . '<br />' . "\n";
				$i++;
			}
		}
		echo "</p>";
		/*
		echo "\n<hr />\n<pre>\n";
		print_r($data['tuc']);
		echo "</pre>\n";
		*/
		if ($i) {
		echo '<p>&nbsp;<br/>' . $i . ' translation' . ($i==1 ? '' : 's') . ' retrieved via <a href="http://glosbe.com/a-api" target="_blank">Glosbe API</a>.</p>';
		}
		
	} else {
		
		echo '<p>No translations found (' . tohtml($from) . '-' . tohtml($dest) . ').<br />&nbsp;</p>';
		
		if ($dest != "en" && $from != "en") {
		
			$ok = FALSE;
		
			$dest = "en";
			$titeltext = '<a href="http://glosbe.com/' . $from . '/' . $dest . '/' . $phrase . '" target="_blank">Glosbe Dictionary (' . tohtml($from) . "-" . tohtml($dest) . "):  &nbsp; <span class=\"red2\">" . tohtml($phrase) . "</span></a>";
			echo '<hr /><p>&nbsp;</p><h3>' . $titeltext . '</h3>';

			$glosbe_data = file_get_contents('http://glosbe.com/gapi/translate?from=' . urlencode($from) . '&dest=' . urlencode($dest) . '&format=json&phrase=' . urlencode($phrase));

			if(! ($glosbe_data === FALSE)) {

				$data = json_decode ($glosbe_data, true);
				if ( isset($data['phrase']) ) {
					$ok = (($data['phrase'] == $phrase) && (isset($data['tuc'])));
				}

			}

			if ( $ok ) {

				if (count($data['tuc']) > 0) {
	
					$i = 0;

					echo "<p>&nbsp;<br />\n";
					foreach ($data['tuc'] as &$value) {
						$word = '';
						if (isset($value['phrase'])) {
							if (isset($value['phrase']['text']))
								$word = $value['phrase']['text'];
						} else if (isset($value['meanings'])) {
							if (isset($value['meanings'][0]['text']))
								$word = $value['meanings'][0]['text'];
						}
						if ($word != '') {
							echo '<span class="click" onclick=""><img src="icn/tick-button.png" title="Copy" alt="Copy" /></span> &nbsp;' . $word . '<br />' . "\n";
							$i++;
						}
					}
					echo "</p>";
					if ($i) {
					echo '<p>&nbsp;<br/>' . $i . ' translation' . ($i==1 ? '' : 's') . ' retrieved via <a href="http://glosbe.com/a-api" target="_blank">Glosbe API</a>.</p>';
					}
		
				} else {
	
					echo '<p>&nbsp;<br/>No translations found (' . tohtml($from) . '-' . tohtml($dest) . ').</p>';
		
				}
	
			} else {

				echo '<p>&nbsp;<br/>Retrieval error (' . tohtml($from) . '-' . tohtml($dest) . ').</p>';

			}
		}
	
	}
	
} else {

	echo '<p>Retrieval error (' . tohtml($from) . '-' . tohtml($dest) . ').</p>';

}

pageend();

?>