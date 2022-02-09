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
 * @link    https://hugofara.github.io/lwt/docs/html/ajax__get__theme_8php.html
 * @since   2.2.0-fork
 */
require_once 'session_utility.php';

chdir('..');
if (isset($_GET['file'])) {
    print_file_path($_GET['file']);
}

?>
