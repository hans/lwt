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
Connect to database
Debug switch / Display PHP error settings
Set script time limit
Start a PHP session
***************************************************************/

require 'lwt-config.php';
require 'lwt-include/database.php';
require 'lwt-include/template.php';
require 'lwt-include/utilities.php';

$err = @mysql_connect($server,$userid,$passwd);
if ($err == FALSE) die('DB connect error (MySQL not running or connection parameters are wrong; start MySQL and/or correct file "connect.inc.php"). Please read the documentation: http://lwt.sf.net');

@mysql_query("SET NAMES 'utf8'");

$err = @mysql_select_db($dbname);
if ($err == FALSE && mysql_errno() == 1049) runsql("CREATE DATABASE `" . $dbname . "` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci",'');

$err = @mysql_select_db($dbname);
if ($err == FALSE) die('DB select error (Cannot find database: "'. $dbname . '" or connection parameter $dbname is wrong; please create database and/or correct file: "connect.inc.php"). Hint: The database can be created by importing the file "dbinstall.sql" within phpMyAdmin. Please read the documentation: http://lwt.sf.net');

// check/update db
check_update_db();

$debug = 0;  // 1 = debugging on, 0 = .. off

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