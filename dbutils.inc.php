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
Database Utility Functions
***************************************************************/

// -------------------------------------------------------------

function do_mysql_query($sql) {
	$res = mysql_query($sql);
	if ($res == FALSE) {
		echo '</select></p></div><div style="padding: 1em; color:red; font-size:120%; background-color:#CEECF5;">' .
			'<p><b>Fatal Error in SQL Query:</b> ' . 
			tohtml($sql) . 
			'</p>' . 
			'<p><b>Error Code &amp; Message:</b> [' . 
			mysql_errno() . 
			'] ' . 
			tohtml(mysql_error()) . 
			"</p></div><hr /><pre>Backtrace:\n\n";
		debug_print_backtrace ();
		echo '</pre><hr />';
		die('</body></html>');
	}
	else
		return $res;
}

// -------------------------------------------------------------

function runsql($sql, $m, $sqlerrdie = TRUE) {
	if ($sqlerrdie)
		$res = do_mysql_query($sql);
	else
		$res = mysql_query($sql);		
	if ($res == FALSE) {
		$message = "Error: " . mysql_error();
	} else {
		$num = mysql_affected_rows();
		$message = (($m == '') ? $num : ($m . ": " . $num));
	}
	return $message;
}

// -------------------------------------------------------------

function get_first_value($sql) {
	$res = do_mysql_query($sql);		
	$record = mysql_fetch_assoc($res);
	if ($record) 
		$d = $record["value"];
	else
		$d = NULL;
	mysql_free_result($res);
	return $d;
}

// -------------------------------------------------------------

?>