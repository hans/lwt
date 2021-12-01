<?php
/**
 * \file
 * \brief Change term status (Table Test)
 * 
 * Call: inc/ajax_chg_term_status.php
 * 
 * @author LWT Project <lwt-project@hotmail.com>
 * @since  1.5.4
 */

require_once __DIR__ . '/session_utility.php';

$wid = $_REQUEST['id'];
$up = $_REQUEST['data'];

$currstatus = get_first_value('SELECT WoStatus as value FROM ' . $tbpref . 'words where WoID = ' . $wid);

if (! isset($currstatus)) {
    echo '';
}

else {
    $currstatus = $currstatus + 0;
    if ($up == 1) {
        $currstatus += 1; // 98,1,2,3,4,5 => 99,2,3,4,5,6
        if ($currstatus == 99 ) { $currstatus = 1;  // 98->1
        }     if ($currstatus == 6 ) { $currstatus = 99;  // 5->99 
        }    
    } else {
             $currstatus -= 1; // 1,2,3,4,5,99 => 0,1,2,3,4,98
        if ($currstatus == 98 ) { $currstatus = 5;  // 99->5
        }     if ($currstatus == 0 ) { $currstatus = 98;  // 1->98
        }    
    }

    if (($currstatus >= 1 && $currstatus <= 5) || $currstatus == 99 || $currstatus == 98 ) {
        $m1 = runsql(
            'update ' . $tbpref . 'words set WoStatus = ' . 
            $currstatus . ', WoStatusChanged = NOW(),' . make_score_random_insert_update('u') . ' where WoID = ' . $wid, ''
        ) + 0;
        if ($m1 == 1) {
            $currstatus = get_first_value('SELECT WoStatus as value FROM ' . $tbpref . 'words where WoID = ' . $wid);
            if (! isset($currstatus)) {
                echo '';
            }
            echo make_status_controls_test_table(1, $currstatus, $wid);
        }
    } else {
        echo '';
    }
}

?>