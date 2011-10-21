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

/**
 * Set debug options if necessary.
 */
error_reporting(LWT_DEBUG ? E_ALL : 0);
ini_set('display_errors', LWT_DEBUG);
ini_set('display_startup_errors', LWT_DEBUG);
ini_set('html_errors', LWT_DEBUG);

require_once LWT_INCLUDE . 'utilities.php';

require_once LWT_INCLUDE . 'database.php';
db_connect();

@ini_set('max_execution_time', '600');  // 10 min.
@set_time_limit(600);  // 10 min.

@ini_set('memory_limit', '999M');

$err = @session_start();
if ($err == FALSE)
    die('SESSION error (Impossible to start a PHP session)');

/**
 * Run the sanitization process.
 *
 * Sanitize input globals - $_GET, $_POST, $_COOKIE, ... and make things just a
 * bit safer.
 */
require_once LWT_INCLUDE . 'sanitize.php';
sanitize_init();

require_once LWT_INCLUDE . 'template.php';
require_once LWT_INCLUDE . 'input.php';

?>