<?php

/**
 * \file
 * \brief Save a Setting (k/v)
 * 
 * Call: inc/ajax_save_setting.php?k=[key]&v=[value]
 * 
 * @package Lwt
 * @author  LWT Project <lwt-project@hotmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/ajax__save__setting_8php.html
 * @since   1.2.1
 */

require_once __DIR__ . '/session_utility.php';

/**
 * Save a setting.
 * 
 * @param string $key   Setting key
 * @param mixed  $value Setting value
 * 
 * @return void
 */
function do_ajax_save_setting($key, $value) 
{
    chdir('..');

    saveSetting($key, $value);
}

if (isset($_GET['k']) && isset($_GET['v'])) {
    do_ajax_save_setting(getreq('k'), getreq('v'));
}

?>