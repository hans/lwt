<?php
/**
 * \file
 * Add a translation to term.
 * 
 * Call: ajax_add_term_transl.php
 * 
 * @author https://sourceforge.net/projects/lwt/ LWT Project
 * @since  1.5.0
 */

require_once 'inc/session_utility.php';

$wid = $_POST['id'] + 0;
$data = trim(stripTheSlashesIfNeeded($_POST['data'])); // translation
$text = trim(stripTheSlashesIfNeeded($_POST['text'])); // only wid=0 (new)
$lang = $_POST['lang'] + 0; // only wid=0 (lang-id)

// Save data
$success = "";

if ($wid == 0) {
    $textlc = mb_strtolower($text, 'UTF-8');
    $dummy = runsql(
        'insert into ' . $tbpref . 'words (WoLgID, WoTextLC, WoText, ' .
        'WoStatus, WoTranslation, WoSentence, WoRomanization, WoStatusChanged,' .  make_score_random_insert_update('iv') . ') values( ' . 
        $lang . ', ' .
        convert_string_to_sqlsyntax($textlc) . ', ' .
        convert_string_to_sqlsyntax($text) . ', 1, ' .        
        convert_string_to_sqlsyntax($data) . ', ' .
        convert_string_to_sqlsyntax('') . ', ' .
        convert_string_to_sqlsyntax('') . ', NOW(), ' .  
        make_score_random_insert_update('id') . ')', ""
    );
    if ($dummy == 1) {
        $wid = get_last_key();
        do_mysqli_query('UPDATE ' . $tbpref . 'textitems2 SET Ti2WoID = ' . $wid . ' WHERE Ti2LgID = ' . $lang . ' AND LOWER(Ti2Text) =' . convert_string_to_sqlsyntax_notrim_nonull($textlc));
        $success = $textlc;
    }
}

else if(get_first_value("select count(WoID) as value from " . $tbpref . "words where WoID = " . $wid) == 1) {

    $oldtrans = get_first_value("select WoTranslation as value from " . $tbpref . "words where WoID = " . $wid);
    
    $oldtransarr = preg_split('/[' . get_sepas()  . ']/u', $oldtrans);
    array_walk($oldtransarr, 'trim_value');
    
    if (! in_array($data, $oldtransarr)) {
        if ((trim($oldtrans) == '') || (trim($oldtrans) == '*')) {
            $oldtrans = $data;
        } else {
            $oldtrans .= ' ' . get_first_sepa() . ' ' . $data;
        }
        $dummy = runsql(
            'update ' . $tbpref . 'words set ' .
            'WoTranslation = ' . convert_string_to_sqlsyntax($oldtrans) . ' where WoID = ' . $wid, ""
        );
    }
    $success = get_first_value("select WoTextLC as value from " . $tbpref . "words where WoID = " . $wid);
}

echo $success;

?>
