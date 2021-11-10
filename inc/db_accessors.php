<?php
/**
 * \file
 * Database Utility Functions
 * 
 * This file is responsible for interacting with the database.
 * 
 * @author https://github.com/HugoFara/lwt/graphs/contributors GitHub contributors
 */

require_once __DIR__ . '/database_connect.php';

/**
 * Do a SQL query to the database. 
 * It is a wrapper for mysqli_query function.
 * 
 * @param string $sql Query using SQL syntax
 */ 
function do_mysqli_query($sql) 
{
    $res = mysqli_query($GLOBALS['DBCONNECTION'], $sql);
    if ($res == false) {
        echo '</select></p></div><div style="padding: 1em; color:red; font-size:120%; background-color:#CEECF5;">' .
        '<p><b>Fatal Error in SQL Query:</b> ' . 
        tohtml($sql) . 
        '</p>' . 
        '<p><b>Error Code &amp; Message:</b> [' . 
        mysqli_errno($GLOBALS['DBCONNECTION']) . 
        '] ' . 
        tohtml(mysqli_error($GLOBALS['DBCONNECTION'])) . 
        "</p></div><hr /><pre>Backtrace:\n\n";
        debug_print_backtrace();
        echo '</pre><hr />';
        die('</body></html>');
    }
    else {
        return $res; 
    }
}

/**
 * Run a SQL query, you can specify its behavior and error message.
 * 
 * @param string $sql       MySQL query
 * @param string $m         Error message ('' to return the number of affected rows)
 * @param bool   $sqlerrdie To die on errors (default = TRUE)
 */
function runsql($sql, $m, $sqlerrdie = true) 
{
    if ($sqlerrdie) {
        $res = do_mysqli_query($sql); 
    }
    else {
        $res = mysqli_query($GLOBALS['DBCONNECTION'], $sql); 
    }        
    if ($res == false) {
        $message = "Error: " . mysqli_error($GLOBALS['DBCONNECTION']);
    } else {
        $num = mysqli_affected_rows($GLOBALS['DBCONNECTION']);
        $message = (($m == '') ? $num : ($m . ": " . $num));
    }
    return $message;
}


?>
