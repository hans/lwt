<?php
/**
 * \file
 * \brief Save Improved Annotation
 * 
 * Call: inc/ajax_save_impr_text.php
 * 
 * @author LWT Project <lwt-project@hotmail.com>
 * @since  1.5.0
 */

require_once __DIR__ . '/session_utility.php';

$textid = (int)$_POST['id'];
$elem = $_POST['elem'];
$stringdata = $_POST['data'];
$data = json_decode($stringdata);

$val = $data->{$elem};
if(substr($elem, 0, 2) == "rg") {
    if($val == "") { $val = $data->{'tx' . substr($elem, 2)}; 
    } 
}
$line = (int)substr($elem, 2);

// Save data
$success = "NOTOK";
$ann = get_first_value("select TxAnnotatedText as value from " . $tbpref . "texts where TxID = " . $textid);
$items = preg_split('/[\n]/u', $ann);
if (count($items) >= $line) {
    $vals = preg_split('/[\t]/u', $items[$line-1]);
    if ($vals[0] > -1 && count($vals) == 4) {
        $vals[3] = $val;
        $items[$line-1] = implode("\t", $vals);
        runsql(
            'update ' . $tbpref . 'texts set ' .
            'TxAnnotatedText = ' . convert_string_to_sqlsyntax(implode("\n", $items)) . ' where TxID = ' . $textid, ""
        );
        $success = "OK";
    }
}

// error_log ("ajax_save_impr_text / " . $success . " / " . $stringdata);

echo $success;

?>
