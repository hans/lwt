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

chdir('..');

saveSetting(
    getreq('k'), 
    getreq('v')
);

?>