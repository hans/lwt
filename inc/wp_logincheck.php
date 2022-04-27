<?php

/**
 * \file
 * \brief WordPress Login Check
 * To be inserted in "connect.inc.php" when LWT used with WordPress
 */

 require_once 'start_session.php';

if (isset($_SESSION['LWT-WP-User'])) {
    $tbpref = $_SESSION['LWT-WP-User'];
} else {
    $url = '';
    if ($_SERVER['REQUEST_METHOD']=='GET') {
        $url = $_SERVER['REQUEST_URI'];
    } else if (isset($_SERVER['HTTP_REFERER'])) {
        $url = $_SERVER['HTTP_REFERER'];
    }
    $parts = explode('/', $url);
    $url = end($parts);
    header("Location: ./wp_lwt_start.php?rd=". urlencode($url));
    exit;
}

?>