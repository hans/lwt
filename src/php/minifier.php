<?php
/**
 * \file
 * \brief JS and CSS minifier.
 * 
 * Use this script to minify JS and CSS files from src/js and src/css to js/ and 
 * css/.
 * 
 * @author HugoFara <hugo.farajallah@protonmail.com>
 * @since  2.0.3-fork
 */
require __DIR__ . '/../../vendor/autoload.php';
use MatthiasMullie\Minify;

/**
 * Minify a JavaScript file and outputs the result to js/
 * 
 * @param string $path       Input file path with extension.
 * @param string $outputPath Output file path with extension
 * 
 * @return string Minified content
 * 
 * @since 2.0.3-fork
 */
function minifyJS($path, $outputPath) 
{
    $minifier = new Minify\JS();
    $minifier->add($path);

    // we can even add another file, they'll then be
    // joined in 1 output file
    // $sourcePath2 = '/path/to/second/source/js/file.js';
    // $minifier->add($sourcePath2);

    // save minified file to disk
    $minifier->minify($outputPath);

    // or just output the content
    return $minifier->minify();
}

/**
 * Minify a JavaScript file and outputs the result to css/
 * 
 * @param string $path       Input file path with extension.
 * @param string $outputPath Output file path with extension
 * 
 * @return string Minified content
 * 
 * @since 2.0.3-fork
 */
function minifyCSS($path, $outputPath) 
{
    $minifier = new Minify\CSS();
    $minifier->add($path);

    // we can even add another file, they'll then be
    // joined in 1 output file
    // $sourcePath2 = '/path/to/second/source/css/file.css';
    // $minifier->add($sourcePath2);

    // save minified file to disk
    $minifier->minify($outputPath);

    // or just output the content
    return $minifier->minify();
}

/**
 * Minify all JavaScript files
 * 
 * @global array<string> $jsFiles All the file to be minified
 * 
 * @return void
 * 
 * @since 2.0.3-fork
 */
function minifyAllJS() 
{
    global $jsFiles;
    foreach ($jsFiles as $path) {
        $name = basename($path);
        if (file_exists($path)) {
            minifyJS($path, 'js/' . $name);
        }
    }
}

/**
 * Minify all Cascading-Style Sheet files
 * 
 * @global array<string> $cssFiles All the file to be minified
 * 
 * @return void
 * 
 * @since 2.0.3-fork
 */
function minifyAllCSS() 
{
    global $cssFiles;
    foreach ($cssFiles as $path) {
        $name = basename($path);
        if (file_exists($path)) {
            minifyCSS($path, 'css/' . $name);
        }
    }
}

/**
 * @var array<string> All the paths of JS files to be minified
 */
$jsFiles = array(
    'src/js/audio_controller.js', 'src/js/third_party/countuptimer.js', 
    'src/js/third_party/floating.js', 'src/js/jq_feedwizard.js', 
    'src/js/jq_pgm.js', 'src/js/pgm.js', 
    'src/js/translation_api.js', 'src/js/unloadformcheck.js',
    'src/js/third_party/sorttable.js', 'src/js/user_interactions.js', 

    // Packages integrated by composer (dev mode)
    'vendor/happyworm/jplayer/src/javascript/jplayer/jquery.jplayer.js',
    'vendor/aehlke/tag-it/js/tag-it.js'
);

/**
 * @var array<string> All the paths of CSS files to be minified
 */
$cssFiles = array(
    'src/css/css_charts.css', 'src/css/feed_wizard.css', 'src/css/gallery.css', 
    'src/css/jplayer.css', 'src/css/jquery-ui.css',
    'src/css/styles.css',

    // Packages integrated by composer (dev mode)
    'vendor/aehlke/tag-it/css/jquery.tagit.css'
);
?>