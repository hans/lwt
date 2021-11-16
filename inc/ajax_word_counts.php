<?php

/*************************************************************
Call: ajax_word_counts.php?id=[textid1,textid2,...]
Calculating Word Counts, Ajax call in edit_texts.php
***************************************************************/

require_once 'inc/session_utility.php';


textwordcount($_POST["id"]);

?>
