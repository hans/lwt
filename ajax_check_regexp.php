<?php


/**************************************************************
Call: ajax_check_regexp.php?....
      ... regex=regular_expression
returns error message or empty string
***************************************************************/

require_once 'inc/session_utility.php';


$regex = $_REQUEST['regex'];
$old_error = error_reporting(0); // Turn off error reporting
$err = 0;

if('MECAB'== strtoupper(trim($regex))) {
    $mecab = get_mecab_path();
    $conf = shell_exec($mecab . " -P");
    $conf_data = array();
    foreach (explode("\n", $conf) as $cLine) {
        if($cLine) {
            list ($cKey, $cValue) = explode(":", $cLine, 2);
            $conf_data[$cKey] = trim($cValue);
        }
    }

    if(isset($conf_data['dicdir'])) {
        $dic = shell_exec($mecab . " -D");
        $dic_data = array();
        foreach (explode("\n", $dic) as $cLine) {
            if($cLine) {
                list ($cKey, $cValue) = explode(":", $cLine, 2);
                $dic_data[$cKey] = trim($cValue);
            }
        }
        if($dic_data["charset"]!="UTF-8") { echo "ERROR\n\nWRONG ENCODING!\nMeCab Dictionary must compiled with UTF-8!\n"; 
        }
    }
    else {
        echo "ERROR\n\nCould not find '" . $mecab . "'\n";
    }
}
else{
    $match = preg_match('/[' . $regex . ']/u', 'test');
    if ($match === false) {
        $err = 1;
    }
    else if(mysqli_query($GLOBALS["DBCONNECTION"], 'select "test" rlike ' . convert_regexp_to_sqlsyntax('['.$regex.']'))===false) {
        $err = 1;
    }
}
if($err == 1) { echo "ERROR\n\nIncorrect Syntax of Field 'Word Regexp Characters'"; 
}
error_reporting($old_error);  // Set error reporting to old level

?>
