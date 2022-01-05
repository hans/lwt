<?php

/**
 * \file
 * \brief Show similar terms
 * 
 * Call: ajax_show_similar_terms?...
 *    ... lang=[langid] ... language
 *    ... word=[word] ... word
 * 
 * @author LWT Project <lwt-project@hotmail.com>
 * @since  1.5.18
 */

require_once __DIR__ . '/simterms.php';

echo print_similar_terms((int) $_POST['lang'],$_POST['word']);

?>
