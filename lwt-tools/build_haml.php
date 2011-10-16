<?php

require 'lwt-config.php';
require LWT_BASE . DIRECTORY_SEPARATOR . 'lwt-tools' . DIRECTORY_SEPARATOR
    . 'phamlp' . DIRECTORY_SEPARATOR . 'haml' . DIRECTORY_SEPARATOR
    . 'HamlParser.php';

$iter = new RecursiveDirectoryIterator(LWT_BASE . DIRECTORY_SEPARATOR . 'lwt-view');
$parser = new HamlParser(array('ugly' => false, 'style' => 'nested'));

foreach ( new RecursiveIteratorIterator($iter) as $filename ) {
    $pathinfo = pathinfo($filename);

    if ( $pathinfo['extension'] == 'haml' ) {
        $new_path = $pathinfo['dirname'] . DIRECTORY_SEPARATOR
            . $pathinfo['filename'];

        file_put_contents($new_path, $parser->parse($filename));
    }
}

?>