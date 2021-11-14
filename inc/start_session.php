<?php
/** 
 * \file
 * \brief Start a PHP session.
 * 
 * @author https://github.com/HugoFara/ HugoFara
*/

// Get globals
require 'kernel_utility.php';

function set_error_reporting($dsplerrors) 
{
    if ($dsplerrors) {
        @error_reporting(E_ALL);
        @ini_set('display_errors', '1');
        @ini_set('display_startup_errors', '1');
    } else {
        @error_reporting(0);
        @ini_set('display_errors', '0');
        @ini_set('display_startup_errors', '0');
    }
}

function set_configuration_options() 
{
    // Set script time limit
    @ini_set('max_execution_time', '600');  // 10 min.
    @set_time_limit(600);  // 10 min.

    @ini_set('memory_limit', '999M');
}  

function start_session() 
{
    // session isn't started
    $err = @session_start();
    if ($err === false) { 
        my_die('SESSION error (Impossible to start a PHP session)'); 
    }
    if(session_id() == '') {
        my_die('SESSION ID empty (Impossible to start a PHP session)'); 
    }
    if (!isset($_SESSION)) {
        my_die('SESSION array not set (Impossible to start a PHP session)'); 
    }
}

function start_session_main() 
{
    set_error_reporting($GLOBALS['$dsplerrors']);
    set_configuration_options();
    // Start a PHP session if not one already exists
    if (session_id() == '') {
        start_session();
    }
}

start_session_main();

?>