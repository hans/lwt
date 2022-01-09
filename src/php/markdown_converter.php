<?php
/**
 * \file
 * \brief Simple Mardown to HTML utility file.
 * 
 * @package Lwt
 * @author  HugoFara <hugo.farajallah@protonmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/markdown__converter_8php.html
 * @since   2.0.3-fork
 */
require __DIR__ . '/../../vendor/autoload.php';

use League\CommonMark\GithubFlavoredMarkdownConverter;

/**
 * Convert a markdown file to HTML and return the result.
 * 
 * @param string $file_path Full path for the file to use, including extension.
 * 
 * @return string An HTML-formatted string
 */
function markdown_converter($file_path) 
{
    $converter = new GithubFlavoredMarkdownConverter();
    $markdown = file_get_contents($file_path);
    return (string)$converter->convertToHtml($markdown);
}
?>