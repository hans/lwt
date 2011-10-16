<?php

/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions,
unless such conditions are required by law.

Developed by J. Pierre in 2011.
***************************************************************/

/**************************************************************
Call: index.php
LWT Start Screen / Main Menu / Home
***************************************************************/

if (! file_exists ('lwt-config.php')) die ('Fatal Error, cannot find file: "lwt-config.php". Please rename lwt-config.sample.php to lwt-config.php and update the connection information.');

require 'lwt-startup.php';

$currentlang = getSetting('currentlanguage');
$currenttext = getSetting('currenttext');

$langcnt = get_first_value('select count(*) as value from languages');

if ($langcnt > 0 ) {
    $txttit = get_first_value('select TxTitle as value from texts where TxID=' . (int)$currenttext);

    if ( isset($txttit) ) {
        $txtlng = get_first_value("SELECT TxLgId AS value FROM texts WHERE TxID = " . (int)$currenttext);
        $lngname = getLanguage($txtlng);
    }
}

flush();
optimizedb();

$mb = get_first_value("SELECT round(sum(data_length+index_length)/1024/1024,1) as value FROM information_schema.TABLES where table_schema = " . convert_string_to_sqlsyntax(LWT_DB_NAME) . " GROUP BY table_schema");

render('index', compact('currentlang', 'currenttext', 'langcnt', 'txttit', 'lngname', 'mb', 'dbname', 'server'));

?>
