<?php

/**************************************************************
Call: inline_edit.php?...
...
 ***************************************************************/

require_once 'inc/session_utility.php';

$value = (isset($_POST['value'])) ? $_POST['value'] : "";
$value = trim($value);
$id = (isset($_POST['id'])) ? $_POST['id'] : "";

if (substr($id, 0, 5) == "trans") {
    $id = substr($id, 5);
    if($value == '') { $value='*'; 
    }
    runsql(
        'update ' . $tbpref . 'words set WoTranslation = ' . 
        convert_string_to_sqlsyntax(repl_tab_nl($value)) . ' where WoID = ' . $id,
        ""
    );
    echo get_first_value("select WoTranslation as value from " . $tbpref . "words where WoID = " . $id);
    exit;
}

if (substr($id, 0, 5) == "roman") {
    if ($value == '*') { $value=''; 
    }
    $id = substr($id, 5);
    runsql(
        'update ' . $tbpref . 'words set WoRomanization = ' . 
        convert_string_to_sqlsyntax(repl_tab_nl($value)) . ' where WoID = ' . $id,
        ""
    );
    $value = get_first_value("select WoRomanization as value from " . $tbpref . "words where WoID = " . $id);
    if ($value == '') { 
        echo '*'; 
    }
    else { 
        echo $value; 
    }
    exit;
}

echo "ERROR - please refresh page!";

?>