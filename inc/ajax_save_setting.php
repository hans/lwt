<?php

/**
 * \file
 * \brief Save a Setting (k/v)
 * 
 * Call: inc/ajax_save_setting.php?k=[key]&v=[value]
 * 
 * @author LWT Project <lwt-project@hotmail.com>
 * @since  1.2.1
 */

require_once __DIR__ . '/session_utility.php';

$k = getreq('k');
$v = getreq('v');
saveSetting($k, $v);

?>