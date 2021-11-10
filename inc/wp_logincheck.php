<?php

/**
 * \file
 * WordPress Login Check
 * To be inserted in "connect.inc.php" when LWT used with WordPress
 */

 require_once 'start_session.php';

if (isset($_SESSION['LWT-WP-User'])) {
    $tbpref = $_SESSION['LWT-WP-User'];
} else {
    header("Location: ./wp_lwt_start.php?rd=". urlencode(end(explode('/', ($_SERVER['REQUEST_METHOD']=='GET')?$_SERVER['REQUEST_URI']:(isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'')))));
    exit;
}

?>