<?php
/**
 * \file
 * \brief Save Improved Annotation
 * 
 * Call: inc/ajax_save_impr_text.php
 * 
 * @package Lwt
 * @author  LWT Project <lwt-project@hotmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/ajax__save__impr__text_8php.html
 * @since   1.5.0
 */

require_once __DIR__ . '/session_utility.php';

chdir('..');
$textid = (int)$_POST['id'];
$elem = $_POST['elem'];
$stringdata = $_POST['data'];
$data = json_decode($stringdata);

$val = $data->{$elem};
if (substr($elem, 0, 2) == "rg") {
    if($val == "") { 
        $val = $data->{'tx' . substr($elem, 2)}; 
    } 
}
$line = (int)substr($elem, 2);

// Save data
function save_impr_text_data($textid, $line, $val)
{
    global $tbpref;
    $success = "NOTOK";
    $ann = get_first_value(
        "SELECT TxAnnotatedText AS value 
        FROM " . $tbpref . "texts 
        WHERE TxID = " . $textid
    );
    $items = preg_split('/[\n]/u', $ann);
    if (count($items) >= $line) {
        $vals = preg_split('/[\t]/u', $items[$line-1]);
        if ($vals[0] > -1 && count($vals) == 4) {
            $vals[3] = $val;
            $items[$line-1] = implode("\t", $vals);
            runsql(
                'UPDATE ' . $tbpref . 'texts 
                SET TxAnnotatedText = ' . convert_string_to_sqlsyntax(implode("\n", $items)) . ' 
                WHERE TxID = ' . $textid, ""
            );
            $success = "OK";
        }
    }
    return $success;
}

$success = save_impr_text_data($textid, $line, $val);

// error_log ("ajax_save_impr_text / " . $success . " / " . $stringdata);

echo $success;

?>
