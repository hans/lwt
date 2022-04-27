<?php

/**
 * \file
 * \brief Calculating Word Counts, Ajax call in edit_texts.php
 * 
 * Call: inc/ajax_word_counts.php?id=[textid1,textid2,...]
 * 
 * @package Lwt
 * @author  LWT Project <lwt-project@hotmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/ajax__word__counts_8php.html
 * @since   1.0.3
 */

require_once __DIR__ . '/session_utility.php';

/**
 * Do the word count for a specific text.
 * 
 * @param int $textid Text ID
 * 
 * @return void
 */
function do_ajax_word_counts($textid)
{
    chdir('..');
    textwordcount($textid);
}

if (isset($_POST["id"])) {
    do_ajax_word_counts((int)$_POST["id"]);
}

?>
