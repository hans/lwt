<?php

/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions, 
unless such conditions are required by law.

Developed by J. Pierre in 2011.
***************************************************************/

/**************************************************************
Call: do_test_test.php?type=[testtype]&lang=[langid]
Call: do_test_test.php?type=[testtype]&text=[textid]
Call: do_test_test.php?type=[testtype]&selection=1  
			(SQL via $_SESSION['testsql'])
Show test frame
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

$p = '';

if (isset($_REQUEST['selection']) && isset($_SESSION['testsql'])) {
	$testsql = $_SESSION['testsql']; 
}

elseif (isset($_REQUEST['lang'])) {
	$testsql = ' words where WoLgID = ' . $_REQUEST['lang'] . ' '; 
}

elseif (isset($_REQUEST['text'])) {
	$testsql = ' words, textitems where TiLgID = WoLgID and TiTextLC = WoTextLC and TiTxID = ' . $_REQUEST['text'] . ' ';
}

else die("Called with wrong parameters");

$testtype = getreq('type') + 0;
if ($testtype < 1) $testtype=1;
if ($testtype > 5) $testtype=5;
$nosent = 0;
if ($testtype > 3) {
	$testtype = $testtype - 3;
	$nosent = 1;
}

$totaltests = $_SESSION['testtotal'];
$wrong = $_SESSION['testwrong'];
$correct = $_SESSION['testcorrect'];

pagestart_nobody('','html, body { width:100%; height:100%; } html {display:table;} body { display:table-cell; vertical-align:middle; } #body { max-width:95%; margin:0 auto; }');

$cntlang = get_first_value('select count(distinct WoLgID) as value from ' . $testsql);
if ($cntlang > 1) {
	echo '<p>Sorry - The selected terms are in ' . $cntlang . ' languages, but tests are only possible in one language at a time.</p>';
	pageend();
	exit();
}

?>
<div id="body">
<?php

$count = get_first_value('SELECT count(distinct WoID) as value FROM ' . $testsql . ' AND WoStatus BETWEEN 1 AND 5 AND WoTranslation != \'\' AND WoTranslation != \'*\' AND WoTodayScore < 0');
if ($debug) echo 'DEBUG - COUNT TO TEST: ' . $count . '<br />';
$notyettested = $count;

if ($count <= 0) {

	$count2 = get_first_value('SELECT count(distinct WoID) as value FROM ' . $testsql . ' AND WoStatus BETWEEN 1 AND 5 AND WoTranslation != \'\' AND WoTranslation != \'*\' AND WoTomorrowScore < 0');
	
	echo '<p class="center"><img src="img/ok.png" alt="Done!" /><br /><br /><span class="red2">Nothing ' . ($totaltests ? 'more ' : '') . 'to test here!<br /><br />Tomorrow you\'ll find here ' . $count2 . ' test' . ($count2 == 1 ? '' : 's') . '!</span></p></div>';
	$count = 0;

} else {

	$lang = get_first_value('select WoLgID as value from ' . $testsql . ' limit 1');
	
	$sql = 'select LgName, LgDict1URI, LgDict2URI, LgGoogleTranslateURI, LgGoogleTTSURI, LgTextSize, LgRemoveSpaces, LgRegexpWordCharacters from languages where LgID = ' . $lang;
	$res = mysql_query($sql);		
	if ($res == FALSE) die("Invalid query: $sql");
	$dsatz = mysql_fetch_assoc($res);
	$wb1 = isset($dsatz['LgDict1URI']) ? $dsatz['LgDict1URI'] : "";
	$wb2 = isset($dsatz['LgDict2URI']) ? $dsatz['LgDict2URI'] : "";
	$wb3 = isset($dsatz['LgGoogleTranslateURI']) ? $dsatz['LgGoogleTranslateURI'] : "";
	$wb4 = isset($dsatz['LgGoogleTTSURI']) ? $dsatz['LgGoogleTTSURI'] : "";
	$textsize = $dsatz['LgTextSize'];
	$removeSpaces = $dsatz['LgRemoveSpaces'];
	$regexword = $dsatz['LgRegexpWordCharacters'];
	$langname = $dsatz['LgName'];
	mysql_free_result($res);
	
	// Find the next word to test
	
	$pass = 0;
	$num = 0;
	while ($pass < 2) {
		$pass++;
		$sql = 'SELECT DISTINCT WoID, WoText, WoTextLC, WoTranslation, WoRomanization, WoSentence, (ifnull(WoSentence,\'\') not like concat(\'%{\',WoText,\'}%\')) as notvalid, WoStatus, DATEDIFF( NOW( ), WoStatusChanged ) AS Days, WoTodayScore AS Score FROM ' . $testsql . ' AND WoStatus BETWEEN 1 AND 5 AND WoTranslation != \'\' AND WoTranslation != \'*\' AND WoTodayScore < 0 ' . ($pass == 1 ? 'AND WoRandom > RAND()' : '') . ' order by WoTodayScore, WoRandom LIMIT 1';
		if ($debug) echo 'DEBUG TEST-SQL: ' . $sql . '<br />';
		$res = mysql_query($sql);		
		if ($res == FALSE) die("Invalid query: $sql");
		$dsatz = mysql_fetch_assoc($res);
		if ( $dsatz ) {
			$num = 1;
			$wid = $dsatz['WoID'];
			$word = $dsatz['WoText'];
			$wordlc = $dsatz['WoTextLC'];
			$trans = repl_tab_nl($dsatz['WoTranslation']);
			$roman = $dsatz['WoRomanization'];
			$sent = repl_tab_nl($dsatz['WoSentence']);
			$notvalid = $dsatz['notvalid'];
			$status = $dsatz['WoStatus'];
			$days = $dsatz['Days'];
			$score = $dsatz['Score'];
			$pass = 2;
		}
		mysql_free_result($res);
	}
	
	if ($num == 0) {
	
		// should not occur but...
		echo '<p class="center"><img src="img/ok.png" alt="Done!" /><br /><br /><span class="red2">Nothing to test here!</span></p></div>';
		$count = 0;
		
	} else {

		if ( $nosent)	{  // No sent. mode 4+5
			$num = 0;
			$notvalid = 1;
		}
		else { // $nosent == FALSE, mode 1-3
			$pass = 0;
			$sentexcl = '';
			while ( $pass < 3 ) {
				$pass++;
				if ($debug) echo "DEBUG search sent: pass: $pass <br />";
				$sql = 'SELECT DISTINCT SeID FROM sentences, textitems WHERE TiTextLC = ' . convert_string_to_sqlsyntax($wordlc) . $sentexcl . ' AND SeID = TiSeID AND SeLgID = ' . $lang . ' order by rand() limit 1';
				$res = mysql_query($sql);		
				if ($res == FALSE) die("Invalid query: $sql");
				$dsatz = mysql_fetch_assoc($res);
				if ( $dsatz ) {  // random sent found
					$num = 1;
					$seid = $dsatz['SeID'];
					if (AreUnknownWordsInSentence ($seid)) {
						if ($debug) echo "DEBUG sent: $seid has unknown words<br />";
						$sentexcl = ' AND SeID != ' . $seid . ' ';
						$num = 0;
						// not yet found, $num == 0 (unknown words in sent)
					} else {
						// echo ' OK ';
						$sent = getSentence($seid, $wordlc,	(int) getSettingWithDefault('set-test-sentence-count'));
						$sent = $sent[1];
						if ($debug) echo "DEBUG sent: $seid OK: $sent <br />";
						$pass = 3;
						// found, $num == 1
					}
				} else {  // no random sent found
					$num = 0;
					$pass = 3;
					if ($debug) echo "DEBUG no random sent found<br />";
					// no sent. take term sent. $num == 0
				}
				mysql_free_result($res);
			} // while ( $pass < 3 )
		}  // $nosent == FALSE
	
		if ($num == 0 ) {
			// take term sent. if valid
			if ($notvalid) $sent = '{' . $word . '}';
			if ($debug) echo "DEBUG not found, use sent = $sent<br />";
		}
		
		$cleansent = trim(str_replace("{", '', str_replace("}", '', $sent)));
		// echo $cleansent;
		
		echo '<p id="thetest" style="' . ($removeSpaces ? 'word-break:break-all;' : '') . 'font-size:' . $textsize . '%;line-height: 1.4; text-align:center; margin-bottom:300px;">';
		$l = mb_strlen($sent,'utf-8');
		$r = '';
		$save = '';
		$on = 0;
		
		for ($i=0; $i < $l; $i++) {  // go thru sent
			$c = mb_substr($sent, $i, 1, 'UTF-8');
			if ($c == '}') {
				$r .= ' <span style="word-break:normal;" class="click todo todosty word wsty word' . $wid . '" data_wid="' . $wid . '" data_trans="' . tohtml($trans) . '" data_text="' . tohtml($word) . '" data_rom="' . tohtml($roman) . '" data_sent="' . tohtml($cleansent) . '" data_status="' . $status . '" data_todo="1"';
				if ($testtype ==3) $r .= ' title="' . tohtml($trans) . '"'; 
				$r .= '>';
				if ($testtype == 2) {
					if ($nosent) $r .= tohtml($trans);
					else $r .= '[' . tohtml($trans) . ']';
				}
				elseif ($testtype == 3) 
					$r .= tohtml(str_replace("{", '[', str_replace("}", ']', 
					mask_term_in_sentence('{' . $save . '}',
					$regexword)	)));
				else 
					$r .= tohtml($save);
				$r .= '</span> ';
				$on = 0;
			}
			elseif ($c == '{') {
				$on = 1;
				$save = '';
			}
			else {
				if ( $on ) $save .= $c;
				else $r .= tohtml($c);
			}
		} // for: go thru sent
		
		echo $r;  // Show Sentence
	}
	
?>

</p></div>

<script type="text/javascript">
//<![CDATA[
$(function(){

	var wblink1='<?php echo $wb1; ?>';
	var wblink2='<?php echo $wb2; ?>';
	var wblink3='<?php echo $wb3; ?>';
	var wblink4='<?php echo $wb4; ?>';
	var opened = 0;
	
	$(document).keydown(function(e) {
		if (e.which == 32 && opened == 0) {  // 1st space show sol.
			$('.word').click();
			cClick();
			opened = 1;
			return;
		}
		if (e.which == 32 && opened == 1) {  // space: show box
			$('.word').click();
			opened = 2;
			return;
		}
		if (e.which == 32 && opened == 2) {  // space: hide box
			cClick();
			opened = 1;
			return;
		}
		if (e.which == 38 && opened > 0) {  // up : status+1
			window.parent.frames['ro'].location.href = 
				'set_test_status.php?wid=<?php echo $wid; ?>&stchange=1';
			return;
		}
		if (e.which == 40 && opened > 0) {  // down : status-1
			window.parent.frames['ro'].location.href = 
				'set_test_status.php?wid=<?php echo $wid; ?>&stchange=-1';
			return;
		}
		for (var i=1; i<=5; i++) {
			if ((e.which == (48+i) || e.which == (96+i)) && opened > 0) {  // 1,.. : status=i
				window.parent.frames['ro'].location.href = 
					'set_test_status.php?wid=<?php echo $wid; ?>&status=' + i;
				return;
			}
		}
		if (e.which == 73 && opened > 0) {  // I : status=98
			window.parent.frames['ro'].location.href = 
				'set_test_status.php?wid=<?php echo $wid; ?>&status=98';
			return;
		}
		if (e.which == 87 && opened > 0) {  // W : status=99
			window.parent.frames['ro'].location.href = 
				'set_test_status.php?wid=<?php echo $wid; ?>&status=99';
			return;
		}
		if (e.which == 69 && opened > 0) {  // E : EDIT
			window.parent.frames['ro'].location.href = 
				'edit_tword.php?wid=<?php echo $wid; ?>';
			return;
		}
	});
	
	$('.word').click(function() {
		run_overlib_test(
			wblink1,wblink2,wblink3,wblink4,
			$(this).attr('data_wid'),
			$(this).attr('data_text'),
			$(this).attr('data_trans'),
			$(this).attr('data_rom'),
			$(this).attr('data_status'),
			$(this).attr('data_sent'),
			$(this).attr('data_todo'));
		$('.todo').text(<?php echo prepare_textdata_js ( $testtype==1 ? ( $nosent ? ($trans) : (' [' . $trans . '] ')) : $save ); ?>);
		return false;
	});
	
	window.parent.frames['ru'].location.href='empty.htm';
	window.parent.frames['ro'].setTimeout('location.href=\'empty.htm\';',
	parseInt('<?php echo getSettingWithDefault('set-test-edit-frame-waiting-time'); ?>',10));
	
});
//]]>
</script>

<?php
} 

$wrong = $_SESSION['testwrong'];
$correct = $_SESSION['testcorrect'];
$totaltests = $wrong + $correct + $notyettested;
$totaltestsdiv = 1;
if ($totaltests > 0) $totaltestsdiv = 1.0/$totaltests;
$l_notyet = round(($notyettested * $totaltestsdiv)*100,0);
$b_notyet = ($l_notyet == 0) ? '' : 'borderl';
$l_wrong = round(($wrong * $totaltestsdiv)*100,0);
$b_wrong = ($l_wrong == 0) ? '' : 'borderl';
$l_correct = round(($correct * $totaltestsdiv)*100,0);
$b_correct = ($l_correct == 0) ? 'borderr' : 'borderl borderr';

?>

<script type="text/javascript">
//<![CDATA[
window.onload = function() { new CountUp(<?php echo gmmktime() . ', ' . $_SESSION['teststart']; ?>, 'timer', <?php echo ($count ? 0 : 1); ?>); }
//]]>
</script>

<div id="footer">
<img src="icn/clock.png" title="Elapsed Time" alt="Elapsed Time" />
<span id="timer" title="Elapsed Time"></span>
&nbsp; &nbsp; &nbsp; 
<img class="<?php echo $b_notyet; ?>" src="icn/test_notyet.png" title="Not yet tested" alt="Not yet tested" height="10" width="<?php echo $l_notyet; ?>" /><img class="<?php echo $b_wrong; ?>" src="icn/test_wrong.png" title="Wrong" alt="Wrong" height="10" width="<?php echo $l_wrong; ?>" /><img class="<?php echo $b_correct; ?>" src="icn/test_correct.png" title="Correct" alt="Correct" height="10" width="<?php echo $l_correct; ?>" />
&nbsp; &nbsp; &nbsp; 
<span title="Total number of tests"><?php echo $totaltests; ?></span>
= 
<span class="todosty" title="Not yet tested"><?php echo $notyettested; ?></span>
+ 
<span class="donewrongsty" title="Wrong"><?php echo $wrong; ?></span>
+ 
<span class="doneoksty" title="Correct"><?php echo $correct; ?></span>
</div>

<?php

pageend();

?>