<?php

/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions, 
unless such conditions are required by law.

Developed by J.P. in 2011, 2012.
***************************************************************/

/**************************************************************
Call: ajax_update_media_select.php
Updating media select in edit_texts.php
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

echo selectmediapath('TxAudioURI');

?>