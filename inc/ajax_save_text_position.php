<?php

/**
 * \file
 * \brief Save text and/or audio position (Read Text Screen)
 * 
 * Call: inc/ajax_save_text_position.php
 *  
 * @author andreask7 <andreask7@users.noreply.github.com>
 * @since  1.6.0-fork
 */

require_once __DIR__ . '/session_utility.php';

$textid = $_REQUEST['id'];
$position = (isset($_REQUEST['position']))?$_REQUEST['position']:null;
$audioposition = (isset($_REQUEST['audioposition']))?$_REQUEST['audioposition']:null;

if(isset($_REQUEST['position'])) {
    runsql('update ' . $tbpref . 'texts set TxPosition = ' . $position . ' where TxID = ' . $textid, ""); 
}
else if(isset($_REQUEST['audioposition'])) {
    runsql('update ' . $tbpref . 'texts set TxAudioPosition = ' . $audioposition . ' where TxID = ' . $textid, ""); 
}
?>
