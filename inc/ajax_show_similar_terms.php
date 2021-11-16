<?php

/**
 * \file
 * Show similar terms
 * 
 * Call: ajax_show_similar_terms?...
 *    ... lang=[langid] ... language
 *    ... word=[word] ... word
 * 
 * @since 1.5.18
 */

require_once 'inc/simterms.php';

echo print_similar_terms(
    $_POST['lang'] + 0, 
    stripTheSlashesIfNeeded($_POST['word'])
);

?>
