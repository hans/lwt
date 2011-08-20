<?php

/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions, 
unless such conditions are required by law.

Developed by J. Pierre in 2011
***************************************************************/

/**************************************************************
Call: ajax_word_counts.php?id=[textid]
Calculating Word Counts, Ajax call in edit_texts.php
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

$id = $_POST["id"] + 0;

$txttotalwords = textwordcount($id);
$txtworkedwords = textworkcount($id);
$txtworkedexpr = textexprcount($id);
$txtworkedall = $txtworkedwords + $txtworkedexpr;
$txttodowords = $txttotalwords - $txtworkedwords;

$r = array();

$r[] = '<span title="Total">&nbsp;' . $txttotalwords . '&nbsp;</span>'; 
$r[] = '<span title="Saved" class="status4">&nbsp;' . ($txtworkedall > 0 ? '<a href="edit_words.php?page=1&amp;query=&amp;status=&amp;tag12=0&amp;tag2=&amp;tag1=&amp;text=' . $id . '">' . $txtworkedwords . '+' . $txtworkedexpr . '</a>' : '0' ) . '&nbsp;';
$r[] = '<span title="To Do" class="status0">&nbsp;' . $txttodowords . '&nbsp;</span>';

echo json_encode($r);

?>