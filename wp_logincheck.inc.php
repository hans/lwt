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
WordPress Login Check
To be inserted in "connect.inc.php" when used with WordPress
***************************************************************/

if (! isset($_COOKIE['LWT-WP-User'])) {
	header("Location: ../wp-login.php?redirect_to=./lwtstart.php");
	exit;
} else {
	$tbpref = $_COOKIE['LWT-WP-User'];
}

?>