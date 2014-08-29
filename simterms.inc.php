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
PHP Utility Functions to calculate similar terms of a term
***************************************************************/

// -------------------------------------------------------------

function letterPairs($str) {
	$numPairs = mb_strlen($str) - 1;
	$pairs = array();
	for ($i = 0; $i < $numPairs; $i ++) {
		$pairs[$i] = mb_substr($str, $i, 2);
	}
	return $pairs;
}

function wordLetterPairs($str) {
	$allPairs = array();
	$words = explode(' ', $str);
	for ($w = 0; $w < count($words); $w ++) {
		$pairsInWord = letterPairs($words[$w]);
		for ($p = 0; $p < count($pairsInWord); $p ++) {
			$allPairs[$pairsInWord[$p]] = $pairsInWord[$p];
		}
	}
	return array_values($allPairs);
}

function getSimilarityRanking($str1, $str2) {
	// Returns SimilarityRanking of two UTF-8 strings $str1 and $str2
	// Source http://www.catalysoft.com/articles/StrikeAMatch.html
	// Source http://stackoverflow.com/questions/653157
	$pairs1 = wordLetterPairs($str1);
	$pairs2 = wordLetterPairs($str2);
	$union = count($pairs1) + count($pairs2);
	$intersection = count(array_intersect($pairs1, $pairs2));
	return (2.0 * $intersection) / $union;
}

// -------------------------------------------------------------

function get_similar_terms($lang_id, $compared_term, $max_count, 
	$min_ranking) {
	// For a language $lang_id and a term $compared_term (UTF-8), 
	// return an array with $max_count wordids with a similarity ranking 
	// >= $min_ranking, sorted decending. 
	// If string is already in database, it will be excluded in results.
	global $tbpref;
	$compared_term_lc = mb_strtolower($compared_term, 'UTF-8');
	$sql = "select WoID, WoTextLC from " . $tbpref . "words where WoLgID = " . $lang_id . " AND WoTextLC <> " . convert_string_to_sqlsyntax($compared_term_lc);
	$res = do_mysql_query($sql);
	$termlsd = array();
	while ($record = mysql_fetch_assoc($res)) {
		$termlsd[$record["WoID"]] = getSimilarityRanking($compared_term_lc, $record["WoTextLC"]);
	}
	mysql_free_result($res);
	arsort($termlsd, SORT_NUMERIC);
	$r = array();
	$i = 0;
	foreach ($termlsd as $key => $val) {
		if ($i >= $max_count) break;
		if ($val < $min_ranking) break;
  	$i++;
  	$r[$i] = $key;
	}
	return $r;
}

// -------------------------------------------------------------

function print_similar_terms($termarr) {
	// Get Term and translation of terms in termid array (calculated 
	// in function get_similar_terms(...)) as string for echo
	global $tbpref;
	$rarr = array();
	foreach ($termarr as $termid) {
		$sql = "select WoText, WoTranslation from " . $tbpref . "words where WoID = " . $termid;
		$res = do_mysql_query($sql);
		if ($record = mysql_fetch_assoc($res)) {
    	$rarr[] = tohtml($record["WoText"] . " = " . $record["WoTranslation"]) . "<br />";
		}
		mysql_free_result($res);
	}
	//sort($rarr);
	return '<span class="smaller">' . implode($rarr) . "</span>";
}

// -------------------------------------------------------------

?>