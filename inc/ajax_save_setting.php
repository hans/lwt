<?php

/**************************************************************
Call: ajax_save_setting.php?k=[key]&v=[value]
Save a Setting (k/v)
***************************************************************/

require_once 'inc/session_utility.php';

$k = getreq('k');
$v = getreq('v');
saveSetting($k, $v);

?>