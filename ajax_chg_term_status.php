<?php

/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions, 
unless such conditions are required by law.

Developed by J. P. in 2011, 2012, 2013.
***************************************************************/

/**************************************************************
Call: ajax_chg_term_status.php
Change term status (Table Test)
***************************************************************/

include "settings.inc.php";
include "connect.inc.php";
include "utilities.inc.php";

$wid = $_REQUEST['id'];
$up = $_REQUEST['data'];

$currstatus = get_first_value('SELECT WoStatus as value FROM ' . $tbpref . 'words where WoID = ' . $wid);

if (! isset($currstatus)) {
	echo '';
}

else {
	$currstatus = $currstatus + 0;
	if ($up == 1) {
		$currstatus += 1;
		if ( $currstatus > 5 ) $currstatus = 99; 
	} else {
		$currstatus -= 1;
		if ( $currstatus == 98 ) $currstatus = 5;
		if ( $currstatus <= 1 ) $currstatus = 1;
	}

	if ( ($currstatus >= 1 && $currstatus <= 5) || $currstatus <= 99 ) {
		$m1 = runsql('update ' . $tbpref . 'words set WoStatus = ' . 
			$currstatus . ', WoStatusChanged = NOW(),' . make_score_random_insert_update('u') . ' where WoID = ' . $wid, '') + 0;
		if ($m1 == 1) {
			$currstatus = get_first_value('SELECT WoStatus as value FROM ' . $tbpref . 'words where WoID = ' . $wid);
			if (! isset($currstatus)) {
				echo '';
			}
			echo make_status_controls_test_table(1, $currstatus, $wid);
		}
	} else {
		echo '';
	}
}

?>