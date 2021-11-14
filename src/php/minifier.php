<?php
/**
 * \file
 * \brief JS and CSS minifier.
 * 
 * Use this script to minify JS and CSS files from src/js and src/css to js/ and css/.
 * 
 */
require __DIR__ . '/../../vendor/autoload.php';
use MatthiasMullie\Minify;

/**
 * Minify a JavaScript file and outputs the result to js/
 */
function minifyJS($path, $outputPath) {
    $minifier = new Minify\JS($path);

    // we can even add another file, they'll then be
    // joined in 1 output file
    // $sourcePath2 = '/path/to/second/source/js/file.js';
    // $minifier->add($sourcePath2);

    // save minified file to disk
    $minifier->minify($outputPath);

    // or just output the content
    return $minifier->minify();
}

function minifyCSS($path, $outputPath) {
    $minifier = new Minify\JS($path);
    $name = basename($path);

    // we can even add another file, they'll then be
    // joined in 1 output file
    // $sourcePath2 = '/path/to/second/source/css/file.css';
    // $minifier->add($sourcePath2);

    // save minified file to disk
    $minifier->minify($outputPath);

    // or just output the content
    return $minifier->minify();
}

function minifyAllJS() {
    global $jsFiles;
    foreach ($jsFiles as $path) {
        $name = basename($path);
        minifyJS($path, 'js/' . $name);
    }
}

function minifyAllCSS() {
    global $cssFiles;
    foreach ($cssFiles as $path) {
        $name = basename($path);
        minifyCSS($path, 'css/' . $name);
    }
}

$jsFiles = array(
    'src/audio_controller.js', 'src/js/coutuptimer.js', 'src/js/floating.js', 
    'src/js/jq_feedwizard.js', 'src/js/jq_pgm.js', 'src/js/pgm.js', 
    'src/js/user_interactions.js'
);
$cssFiles = array(
    'src/css/css_charts.css', 'src/css/feed_wizard.css', 'src/css/styles.css'
);
?>