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
Debug switch / Display PHP error settings
Set script time limit
Start a PHP session
***************************************************************/

$debug = 0;  // 1 = debugging on, 0 = .. off
$dspltime = 0;  // 1 = display time on, 0 = .. off

if ($debug) {
	@error_reporting(E_ALL);
	@ini_set('display_errors','1');
	@ini_set('display_startup_errors','1');
} else {
	@error_reporting(0);
	@ini_set('display_errors','0');
	@ini_set('display_startup_errors','0');
}

@ini_set('max_execution_time', '600');  // 10 min.
@set_time_limit(600);  // 10 min.

@ini_set('memory_limit', '999M');  

$err = @session_start();
if ($err == FALSE) 
	die('SESSION error (Impossible to start a PHP session)');

?>