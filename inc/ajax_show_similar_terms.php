<?php

/**
 * \file
 * \brief Show similar terms
 * 
 * Call: ajax_show_similar_terms?...
 *    ... lang=[langid] ... language
 *    ... word=[word] ... word
 * 
 * @package Lwt
 * @author  LWT Project <lwt-project@hotmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/ajax__show__similar__terms_8php.html
 * @since   1.5.18
 */

require_once __DIR__ . '/simterms.php';

chdir('..');

echo print_similar_terms((int)$_POST['lang'], $_POST['word']);

?>
