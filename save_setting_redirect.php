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

require 'lwt-startup.php';

$k = getreq('k');
$v = getreq('v');
$u = getreq('u');

if($k == 'currentlanguage') {

	unset($_SESSION['currenttextpage']);
	unset($_SESSION['currenttextquery']);
	unset($_SESSION['currenttexttag1']);
	unset($_SESSION['currenttexttag2']);
	unset($_SESSION['currenttexttag12']);

	unset($_SESSION['currentwordpage']);
	unset($_SESSION['currentwordquery']);
	unset($_SESSION['currentwordstatus']);
	unset($_SESSION['currentwordtext']);
	unset($_SESSION['currentwordtag1']);
	unset($_SESSION['currentwordtag2']);
	unset($_SESSION['currentwordtag12']);

	unset($_SESSION['currentarchivepage']);
	unset($_SESSION['currentarchivequery']);
	unset($_SESSION['currentarchivetexttag1']);
	unset($_SESSION['currentarchivetexttag2']);
	unset($_SESSION['currentarchivetexttag12']);

	saveSetting('currenttext','');
}

saveSetting($k,$v);
header("Location: " . $u);
exit();
?>