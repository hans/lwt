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

/**
 * Get the file path using theme.
 * 
 * @param string $filepath Standard file path.
 * 
 * @return string Relative filepath using theme.
 */
function do_ajax_get_theme($filepath)
{
    chdir('..');
    return get_file_path($filepath);
}

if (isset($_GET['file'])) {
    echo do_ajax_get_theme($_GET['file']);
}

?>
