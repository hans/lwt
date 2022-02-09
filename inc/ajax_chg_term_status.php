<?php
/**
 * \file
 * \brief Change term status (Table Test)
 * 
 * Call: inc/ajax_chg_term_status.php
 * 
 * @package Lwt
 * @author  LWT Project <lwt-project@hotmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/ajax__chg__term__status_8php.html
 * @since   1.5.4
 */

require_once __DIR__ . '/session_utility.php';

chdir('..');
$wid = $_REQUEST['id'];
$up = $_REQUEST['data'];

$tempstatus = get_first_value(
    'SELECT WoStatus as value FROM ' . $tbpref . 'words 
    WHERE WoID = ' . $wid
);


function get_new_status($oldstatus, $up) {
    $currstatus = $oldstatus;
    if ($up == 1) {
        $currstatus += 1; // 98,1,2,3,4,5 => 99,2,3,4,5,6
        if ($currstatus == 99 ) { 
            $currstatus = 1;  // 98->1
        }
        if ($currstatus == 6 ) { 
            $currstatus = 99;  // 5->99 
        }    
    } else {
        $currstatus -= 1; // 1,2,3,4,5,99 => 0,1,2,3,4,98
        if ($currstatus == 98) {
            $currstatus = 5;  // 99->5
        }
        if ($currstatus == 0 ) {
            $currstatus = 98;  // 1->98
        }    
    }
    return $currstatus;
} 

function update_word_status($wid, $currstatus)
{
    global $tbpref;
    if (($currstatus >= 1 && $currstatus <= 5) || $currstatus == 99 || $currstatus == 98 ) {
        $m1 = (int)runsql(
            'update ' . $tbpref . 'words set WoStatus = ' . 
            $currstatus . ', WoStatusChanged = NOW(),' . make_score_random_insert_update('u') . ' where WoID = ' . $wid, ''
        );
        if ($m1 == 1) {
            $currstatus = get_first_value('SELECT WoStatus as value FROM ' . $tbpref . 'words where WoID = ' . $wid);
            if (!isset($currstatus)) {
                //echo '';
            }
            return make_status_controls_test_table(1, $currstatus, $wid);
        }
    } else {
        return '';
    }
}

if (!isset($tempstatus)) {
    //echo '';
    return;
}
$currstatus = get_new_status((int)$tempstatus, (int)$up);
echo update_word_status($wid, $currstatus);


?>