<?php
/**
 * \file
 * \brief Return theme path.
 * 
 * Call: inc/ajax_get_theme.php?file=[relativefilepath]
 * 
 * @package Lwt
 * @author  HugoFara <hugo.farajallah@protonmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/kernel__utility_8php.html
 * @since   2.2.0-fork
 */
require_once 'session_utility.php';

if (isset($_POST['file'])) {
    print_file_path($_POST['file']);
}

?>
