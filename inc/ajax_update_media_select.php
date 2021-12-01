<?php


/**
 * \file
 * \brief Updating media select in edit_texts.php
 * 
 * Call: inc/ajax_update_media_select.php
 * 
 * @author LWT Project <lwt-project@hotmail.com>
 * @since  1.1.0
 */

require_once __DIR__ . '/session_utility.php';

echo selectmediapath('TxAudioURI');

?>