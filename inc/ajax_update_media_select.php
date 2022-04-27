<?php


/**
 * \file
 * \brief Updating media select in edit_texts.php
 * 
 * Call: inc/ajax_update_media_select.php
 * 
 * @package Lwt
 * @author  LWT Project <lwt-project@hotmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/ajax__update__media__select_8php.html
 * @since   1.1.0
 */

require_once __DIR__ . '/session_utility.php';

/**
 * Change the current working directory and find media path
 */
function do_ajax_update_media_select() {
    chdir('..');
    return selectmediapath('TxAudioURI');
}

echo do_ajax_update_media_select(); 

?>