<?php
/**
 * \file
 * \brief Add a translation to term.
 * 
 * Call: inc/ajax_add_term_transl.php
 * 
 * @package Lwt
 * @author  LWT Project <lwt-project@hotmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/ajax__add__term__transl_8php.html
 * @since   1.5.0
 */

require_once __DIR__ . '/session_utility.php';

chdir('..');

$wid = (int)$_POST['id'];
$data = trim($_POST['data']); // translation
$text = trim($_POST['text']); // only wid=0 (new)
$lang = (int)$_POST['lang']; // only wid=0 (lang-id)

// Save data
$success = "";

function add_new_term_transl($text, $lang, $data) 
{
    global $tbpref;
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
        do_mysqli_query(
            'UPDATE ' . $tbpref . 'textitems2 
            SET Ti2WoID = ' . $wid . ' 
            WHERE Ti2LgID = ' . $lang . ' AND LOWER(Ti2Text) =' . convert_string_to_sqlsyntax_notrim_nonull($textlc)
        );
        return $textlc;
    }
    return "";
}

function edit_term_transl($wid, $oldtrans, $data)
{
    global $tbpref;
    $oldtrans = get_first_value(
        "SELECT WoTranslation AS value 
        FROM " . $tbpref . "words 
        WHERE WoID = " . $wid
    );
    
    $oldtransarr = preg_split('/[' . get_sepas()  . ']/u', $oldtrans);
    array_walk($oldtransarr, 'trim_value');
    
    if (! in_array($data, $oldtransarr)) {
        if ((trim($oldtrans) == '') || (trim($oldtrans) == '*')) {
            $oldtrans = $data;
        } else {
            $oldtrans .= ' ' . get_first_sepa() . ' ' . $data;
        }
        runsql(
            'update ' . $tbpref . 'words set ' .
            'WoTranslation = ' . convert_string_to_sqlsyntax($oldtrans) . ' where WoID = ' . $wid, ""
        );
    }
    return get_first_value(
        "SELECT WoTextLC AS value 
        FROM " . $tbpref . "words 
        WHERE WoID = " . $wid
    );
}

if ($wid == 0) {
    $success = add_new_term_transl($text, $lang, $data);
} else if(get_first_value("SELECT COUNT(WoID) AS value FROM " . $tbpref . "words WHERE WoID = " . $wid) == 1) {
    $success = edit_term_transl($wid, $oldtrans, $data);
}

echo $success;

?>
