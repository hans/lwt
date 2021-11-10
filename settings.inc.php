<?php
/**
 * Proceed to the general settings and start a session.
 */

// Debug switch / Display PHP error settings

/** 
 * @var int $debug 
 * 1 = debugging on, 0 = .. off 
 */
$debug = 0;     
/** 
 * @var int $dsplerrors 
 * 1 = display all errors on, 0 = .. off 
 */   
$dsplerrors = 0;
/** 
 * @var int $dspltime 
 * 1 = display time on, 0 = .. off 
 */
$dspltime = 0;

if ($dsplerrors) {
    @error_reporting(E_ALL);
    @ini_set('display_errors', '1');
    @ini_set('display_startup_errors', '1');
} else {
    @error_reporting(0);
    @ini_set('display_errors', '0');
    @ini_set('display_startup_errors', '0');
}
// Set script time limit
@ini_set('max_execution_time', '600');  // 10 min.
@set_time_limit(600);  // 10 min.

@ini_set('memory_limit', '999M');  

// Start a PHP session if not one already exists
if (session_id() == '') {
    // session isn't started
    $err = @session_start();
    if ($err === false) { 
        die('SESSION error (Impossible to start a PHP session)'); 
    }
    if(session_id() == '') {
        die('SESSION ID empty (Impossible to start a PHP session)'); 
    }
    if (!isset($_SESSION)) {
        die('SESSION array not set (Impossible to start a PHP session)'); 
    }
}

?>