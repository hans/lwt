<?php


/**************************************************************
Call: ajax_save_text_position.php
Save text and/or audio position (Read Text Screen)
***************************************************************/

require_once 'inc/session_utility.php';

$textid = $_REQUEST['id'];
$position = (isset($_REQUEST['position']))?$_REQUEST['position']:null;
$audioposition = (isset($_REQUEST['audioposition']))?$_REQUEST['audioposition']:null;

if(isset($_REQUEST['position'])) {
    $dummy = runsql('update ' . $tbpref . 'texts set TxPosition = ' . $position . ' where TxID = ' . $textid, ""); 
}
else if(isset($_REQUEST['audioposition'])) {
    $dummy = runsql('update ' . $tbpref . 'texts set TxAudioPosition = ' . $audioposition . ' where TxID = ' . $textid, ""); 
}
?>
