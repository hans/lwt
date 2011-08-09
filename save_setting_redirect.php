<?php

/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions, 
unless such conditions are required by law.
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

// save_setting_redirect.php?k=...&v=...&u=...

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
