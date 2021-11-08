<?php
require_once 'settings.inc.php';
require_once 'connect.inc.php';
require_once 'dbutils.inc.php';
require_once 'utilities.inc.php';
require __DIR__ . '/vendor/autoload.php';
use League\CommonMark\GithubFlavoredMarkdownConverter;

/**
 * Convert a markdown file to HTML and return the result.
 * 
 * @param String $file_path Full path for the file to use, including extension.
 * 
 * @return String An HTML-formatted string
 */
function markdown_converter($file_path) {

    $converter = new GithubFlavoredMarkdownConverter();
    $markdown = file_get_contents($file_path);
    return $converter->convertToHtml($markdown);
}
?>