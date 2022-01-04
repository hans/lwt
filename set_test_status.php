<?php

/**************************************************************
Call: set_test_status.php?wid=[wordid]&stchange=+1/-1
      set_test_status.php?wid=[wordid]&status=1..5/98/99
Change status of term while testing
 ***************************************************************/

require_once 'inc/session_utility.php';

$status = (int)getreq('status');
$wid = (int)getreq('wid');

$oldstatus = (int)get_first_value("select WoStatus as value from " . $tbpref . "words where WoID = " . $wid);

$oldscore = (int)get_first_value('select greatest(0,round(WoTodayScore,0)) AS value from ' . $tbpref . 'words where WoID = ' . $wid);

if (getreq('stchange') != '') {
    $stchange = $status - $oldstatus;
    if ($stchange <= 0) { 
        $stchange=-1; 
    }
    if ($stchange > 0) { 
        $stchange=1; 
    }
    
} else {
    $stchange = (int)getreq('stchange');
    $status = $oldstatus + $stchange;
    if ($status < 1) { 
        $status=1; 
    }
    if ($status > 5) { 
        $status=5; 
    }
    
}

$word = get_first_value("select WoText as value from " . $tbpref . "words where WoID = " . $wid);
pagestart("Term: " . $word, false);

$m1 = runsql(
    'update ' . $tbpref . 'words set WoStatus = ' . 
    $status . ', WoStatusChanged = NOW(),' . make_score_random_insert_update('u') . ' where WoID = ' . $wid, 'Status changed'
);
    
$newscore = (int)get_first_value('select greatest(0,round(WoTodayScore,0)) AS value from ' . $tbpref . 'words where WoID = ' . $wid);

if ($oldstatus == $status) {
    echo '<p>Status ' . get_colored_status_msg($status) . ' not changed.</p>'; 
}
else {
    echo '<p>Status changed from ' . get_colored_status_msg($oldstatus) . ' to ' . get_colored_status_msg($status) . '.</p>'; 
}

echo "<p>Old score was " . $oldscore . ", new score is now " . $newscore . ".</p>";

$totaltests = $_SESSION['testtotal'];
$wrong = $_SESSION['testwrong'];
$correct = $_SESSION['testcorrect'];
$notyettested = $totaltests - $correct - $wrong;
if ($notyettested > 0 ) {
    if ($stchange >= 0 ) { 
        $_SESSION['testcorrect']++; 
    }
    else {
        $_SESSION['testwrong']++; 
    }
}        

?>
<script type="text/javascript">
    //<![CDATA[
    var context = window.parent.frames['l'].document;
    $('.word<?php echo $wid; ?>', context).removeClass('todo todosty').addClass('done<?php echo ($stchange >= 0 ? 'ok' : 'wrong'); ?>sty').attr('data_status','<?php echo $status; ?>').attr('data_todo','0');
    <?php
    $waittime = (int)getSettingWithDefault('set-test-main-frame-waiting-time');
    if ($waittime <= 0) {
        ?>
    window.parent.frames['l'].location.reload();
        <?php
    } else {
        ?>
    setTimeout('window.parent.frames[\'l\'].location.reload();', <?php echo $waittime; ?>);
        <?php
    }
    ?>
    //]]>
</script>
<?php

pageend();

?>