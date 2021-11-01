<?php

/**************************************************************
"Learning with Texts" (LWT) is free and unencumbered software 
released into the PUBLIC DOMAIN.

Anyone is free to copy, modify, publish, use, compile, sell, or
distribute this software, either in source code form or as a
compiled binary, for any purpose, commercial or non-commercial,
and by any means.

In jurisdictions that recognize copyright laws, the author or
authors of this software dedicate any and all copyright
interest in the software to the public domain. We make this
dedication for the benefit of the public at large and to the 
detriment of our heirs and successors. We intend this 
dedication to be an overt act of relinquishment in perpetuity
of all present and future rights to this software under
copyright law.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE 
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE
AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS BE LIABLE 
FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN 
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN 
THE SOFTWARE.

For more information, please refer to [http://unlicense.org/].
***************************************************************/

/**************************************************************
Call: ajax_check_regexp.php?....
      ... regex=regular_expression
returns error message or empty string
***************************************************************/

require_once( 'settings.inc.php' );
require_once( 'connect.inc.php' );
require_once( 'dbutils.inc.php' );
require_once( 'utilities.inc.php' );


$regex = $_REQUEST['regex'];
$old_error = error_reporting(0); // Turn off error reporting
$err = 0;

if('MECAB'== strtoupper(trim($regex))){
	$mecab = get_mecab_path();
	$conf = shell_exec($mecab . " -P");
	$conf_data = array();
	foreach (explode("\n", $conf) as $cLine) {
		if($cLine){
			list ($cKey, $cValue) = explode(":", $cLine, 2);
			$conf_data[$cKey] = trim($cValue);
		}
	}

	if(isset($conf_data['dicdir'])){
		$dic = shell_exec($mecab . " -D");
		$dic_data = array();
		foreach (explode("\n", $dic) as $cLine) {
			if($cLine){
				list ($cKey, $cValue) = explode(":", $cLine, 2);
				$dic_data[$cKey] = trim($cValue);
			}
		}
		if($dic_data["charset"]!="UTF-8")echo "ERROR\n\nWRONG ENCODING!\nMeCab Dictionary must compiled with UTF-8!\n";
	}
	else {
		echo "ERROR\n\nCould not find '" . $mecab . "'\n";
	}
}
else{
	$match = preg_match('/[' . $regex . ']/u','test');
	if ($match === false) {
		$err = 1;
	}
	else if(mysqli_query($GLOBALS["DBCONNECTION"], 'select "test" rlike ' . convert_regexp_to_sqlsyntax('['.$regex.']'))===false){
		$err = 1;
	}
}
if($err == 1) echo "ERROR\n\nIncorrect Syntax of Field 'Word Regexp Characters'";
error_reporting($old_error);  // Set error reporting to old level

?>
