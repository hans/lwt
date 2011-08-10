<?php

/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions, 
unless such conditions are required by law.

Developed by J. Pierre in 2011.
***************************************************************/

/**************************************************************
Call: save_setting_redirect.php?k=[key]&v=[value]&u=[RedirURI]
Save a Setting (k/v) and redirect to URI u
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

$k = getreq('k');
$v = getreq('v');
$u = getreq('u');

if($k == 'currentlanguage') {
	unset($_SESSION['currenttextpage']);
	unset($_SESSION['currenttextquery']);
	unset($_SESSION['currentwordpage']);
	unset($_SESSION['currentwordquery']);
	unset($_SESSION['currentwordstatus']);
	unset($_SESSION['currentwordtext']);
	unset($_SESSION['currentarchivepage']);
	unset($_SESSION['currentarchivequery']);
	saveSetting('currenttext','');
}

saveSetting($k,$v);
header("Location: " . $u);
exit(); 
?>