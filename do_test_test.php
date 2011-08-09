<?php

/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions, 
unless such conditions are required by law.
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

$donttestthis = getsess('lastwordtested');
if ($donttestthis != '') 
	$donttestthis = ' AND WoID != ' . $donttestthis . ' ';

pagestart_nobody('','html, body { width:100%; height:100%; } html {display:table;} body { display:table-cell; vertical-align:middle; } #body { max-width:95%; margin:0 auto; }');

$cntlang = get_first_value('select count(distinct WoLgID) as value from ' . $testsql);
if ($cntlang > 1) {
	echo '<p>Sorry - The selected terms are in ' . $cntlang . ' languages, but tests are only possible in one language at a time.</p>';
	pageend();
	exit();
}

$num = get_first_value('select count(distinct WoID) as value from ' . $testsql);
if ($num > 0) {

	$lang = get_first_value('select WoLgID as value from ' . $testsql . ' limit 1');
	
	$sql = 'select LgName, LgDict1URI, LgDict2URI, LgGoogleTranslateURI, LgGoogleTTSURI, LgTextSize, LgRemoveSpaces, LgRegexpWordCharacters from languages where LgID = ' . $lang;
	$res = mysql_query($sql);		
	if ($res == FALSE) die("<p>Invalid query: $sql</p>");
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
	
	$sql = 'SELECT WoID, WoText, WoTextLC, WoTranslation, WoRomanization, WoSentence, (ifnull(WoSentence,\'\') not like concat(\'%{\',WoText,\'}%\')) as notvalid, WoStatus, DATEDIFF( NOW( ), WoStatusChanged ) AS Days, ' . getsqlscoreformula (2) . ' AS Score FROM ' . $testsql . ' AND WoStatus BETWEEN 1 AND 5 AND WoTranslation != \'\' AND WoTranslation != \'*\' ' . $donttestthis . ' order by 10, rand() limit 1';
	// echo $sql;
	$res = mysql_query($sql);		
	if ($res == FALSE) die("<p>Invalid query: $sql</p>");
	$num = mysql_num_rows($res);
	if ($num != 0 ) {
		$dsatz = mysql_fetch_assoc($res);
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
	}
	mysql_free_result($res);

} // if ($num > 0)

?>

<div id="body">
<?php

if ($num == 0) {  // nothing found
	
	echo '<p class="center"><img src="img/ok.png" alt="Done!" /><br /><br /><span class="red2">Nothing to test here!</span></p>';
	
} else {  // found
	
	if ($score > 0) {  // found but score > 0 
		echo '<p class="center"><img src="img/ok.png" alt="Done!" /><br /><br /><span class="red2">Done! - This term is not due today!</span></p><hr noshade size=1 /><p>&nbsp;</p>';
	}
	
	/*
	elseif ( $score == 0 && $days == 0 ) {  // found but score = 0 && days == 0
		echo '<p class="center">The following terms are not due because their status has been already changed or set today.</p><hr noshade size=1 /><p>&nbsp;</p>';
	}
	*/
	
	// echo $days . "/" . $status . "/" . $score;
	
	// Find sentence:	

	if ( $nosent)	{
		$num = 0;
		$notvalid = 1;
	}
	else { // $nosent == FALSE
		$pass = 0;
		$sentexcl = '';
		while ( $pass < 3 ) {
			$pass++;
			// echo "( $pass / $notvalid ) ";
			$sql = 'SELECT DISTINCT SeID FROM sentences, textitems WHERE TiTextLC = ' . convert_string_to_sqlsyntax($wordlc) . $sentexcl . ' AND SeID = TiSeID AND SeLgID = ' . $lang . ' order by rand() limit 1';
			$res = mysql_query($sql);		
			if ($res == FALSE) die("<p>Invalid query: $sql</p>");
			$num = mysql_num_rows($res);
			if ($num != 0 ) {
				$dsatz = mysql_fetch_assoc($res);
				$seid = $dsatz['SeID'];
				if (AreUnknownWordsInSentence ($seid)) {
					// echo ' UNKN ';
					$sentexcl = ' AND SeID != ' . $seid . ' ';
					$num = 0;
					// not yet found, $num == 0
				}
				else {
					// echo ' OK ';
					$sent = getSentence($seid, $wordlc,	(int) getSettingWithDefault('set-test-sentence-count'));
					$sent = $sent[1];
					$pass = 3;
					// found, $num == 1
				}
			} 
			else {
				$pass = 3;
				// no sent. take term sent. $num == 0
			}
			mysql_free_result($res);
		} // while ( $pass < 3 )
	}  // $nosent == FALSE

	// No sentence found.
	if ($num == 0 ) {
		// take term sent. if valid
		if ($notvalid) $sent = '{' . $word . '}';
	}
	
	$cleansent = trim(str_replace("{", '', str_replace("}", '', $sent)));
	// echo $cleansent;
	
	echo '<p style="' . ($removeSpaces ? 'word-break:break-all;' : '') . 'font-size:' . $textsize . '%;line-height: 1.4; text-align:center; margin-bottom:300px;">';
	$l = mb_strlen($sent,'utf-8');
	$r = '';
	$save = '';
	$on = 0;
	$_SESSION['lastwordtested'] = $wid;
	
	for ($i=0; $i < $l; $i++) {  // go thru sent
		$c = mb_substr($sent, $i, 1, 'UTF-8');
		if ($c == '}') {
			$r .= ' <span style="word-break:normal;" class="click todo word wsty word' . $wid . '" data_wid="' . $wid . '" data_trans="' . tohtml($trans) . '" data_text="' . tohtml($word) . '" data_rom="' . tohtml($roman) . '" data_sent="' . tohtml($cleansent) . '" data_status="' . $status . '" data_todo="1"';
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
	
?>
</p></div>

<script type="text/javascript">
//<![CDATA[
$(function(){

	var wblink1='<?php echo $wb1; ?>';
	var wblink2='<?php echo $wb2; ?>';
	var wblink3='<?php echo $wb3; ?>';
	var wblink4='<?php echo $wb4; ?>';
	
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

} // $num != 0

pageend();

?>