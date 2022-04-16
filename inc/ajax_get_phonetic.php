<?php
/**
 * \file
 * \brief Make the phonetic translation of a word.
 * 
 * Call: inc/ajax_get_phonetic.php?text=[text_string]&lang=[language_string]
 * 
 * @package Lwt
 * @author  HugoFara <hugo.farajallah@protonmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/ajax__get__phonetic_8php.html
 * @since   2.3.0-fork
 */
require_once 'session_utility.php';

if (isset($_GET['text']) && isset($_GET['lang'])) {
    echo phonetic_reading(getreq('text'), getreq('lang'));
}

?>
