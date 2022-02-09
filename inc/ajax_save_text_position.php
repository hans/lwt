<?php

/**
 * \file
 * \brief Save text and/or audio position (Read Text Screen)
 * 
 * Call: inc/ajax_save_text_position.php
 * 
 * @package Lwt
 * @author  andreask7 <andreask7@users.noreply.github.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/ajax__save__setting_8php.html
 * @since   1.6.0-fork
 */

require_once __DIR__ . '/session_utility.php';

chdir('..');
$textid = (int)$_REQUEST['id'];

function save_text_position($textid, $position)
{
    global $tbpref;
    runsql(
        'UPDATE ' . $tbpref . 'texts 
        SET TxPosition = ' . $position . ' 
        WHERE TxID = ' . $textid, 
        ""
    ); 
}

function save_audio_position($textid, $audioposition) 
{
    global $tbpref;
    runsql(
        'UPDATE ' . $tbpref . 'texts 
        SET TxAudioPosition = ' . $audioposition . ' 
        WHERE TxID = ' . $textid, 
        ""
    ); 
}

if (getreq('position')) {
    save_text_position(
        (int)$textid, 
        is_numeric(getreq($position)) ? (int)getreq($position) : null
    );
} else if (getreq('audioposition')) {
    save_audio_position(
        (int)$textid, 
        is_numeric(getreq($audioposition)) ? (int)getreq($audioposition) : null
    );
}
?>
