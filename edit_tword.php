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
Call: edit_tword.php?....
      ... op=Change ... do update
      ... wid=[wordid] ... display edit screen  
Edit term while testing
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

$translation_raw = repl_tab_nl(getreq("WoTranslation"));
if ( $translation_raw == '' ) $translation = '*';
else $translation = $translation_raw;

// UPDATE

if (isset($_REQUEST['op'])) {
	
	$textlc = trim(prepare_textdata($_REQUEST["WoTextLC"]));
	$text = trim(prepare_textdata($_REQUEST["WoText"]));
	
	if (mb_strtolower($text, 'UTF-8') == $textlc) {
	
		// UPDATE
		
		if ($_REQUEST['op'] == 'Change') {
			
			$titeltext = "Edit Term: " . tohtml(prepare_textdata($_REQUEST["WoTextLC"]));
			pagestart_nobody($titeltext);
			echo '<h4><span class="bigger">' . $titeltext . '</span></h4>';
			
			$oldstatus = $_REQUEST["WoOldStatus"];
			$newstatus = $_REQUEST["WoStatus"];
			$xx = '';
			if ($oldstatus != $newstatus) $xx = ', WoStatus = ' .	$newstatus . ', WoStatusChanged = NOW()';
		
			$message = runsql('update words set WoText = ' . 
			convert_string_to_sqlsyntax($_REQUEST["WoText"]) . ', WoTranslation = ' . 
			convert_string_to_sqlsyntax($translation) . ', WoSentence = ' . 
			convert_string_to_sqlsyntax(repl_tab_nl($_REQUEST["WoSentence"])) . ', WoRomanization = ' .
			convert_string_to_sqlsyntax($_REQUEST["WoRomanization"]) . $xx . ',' . make_score_random_insert_update('u') . ' where WoID = ' . $_REQUEST["WoID"], "Updated");
			$wid = $_REQUEST["WoID"];
			
		}  // $_REQUEST['op'] == 'Change'

	} // (mb_strtolower($text, 'UTF-8') == $textlc)
	
	else { // (mb_strtolower($text, 'UTF-8') != $textlc)
	
		$titeltext = "New/Edit Term: " . tohtml(prepare_textdata($_REQUEST["WoTextLC"]));
		pagestart_nobody($titeltext);
		echo '<h4><span class="bigger">' . $titeltext . '</span></h4>';		
		$message = 'Error: Term in lowercase must be exactly = "' . $textlc . '", please go back and correct this!'; 
		echo error_message_with_hide($message,0);
		pageend();
		exit();
	
	}

?>
	
<p>OK: <?php echo tohtml($message); ?></p>

<script type="text/javascript">
//<![CDATA[
var context = window.parent.frames['l'].document;
var woid = <?php echo prepare_textdata_js($wid); ?>;
var wotext = <?php echo prepare_textdata_js($_REQUEST["WoText"]); ?>;
var status = <?php echo prepare_textdata_js($_REQUEST["WoStatus"]); ?>;
var trans = <?php echo prepare_textdata_js($translation_raw); ?>;
var roman = <?php echo prepare_textdata_js($_REQUEST["WoRomanization"]); ?>;
$('.word' + woid, context).attr('data_text',wotext).attr('data_trans',trans).attr('data_rom',roman).attr('data_status',status);
window.parent.frames['l'].focus();
window.parent.frames['l'].setTimeout('cClick()', 100);
//]]>
</script>
	
<?php

} // if (isset($_REQUEST['op']))

// FORM

else {  // if (! isset($_REQUEST['op']))

	$wid = getreq('wid');
	
	if ($wid == '') die("Error: Term ID missing");
	
	$sql = 'select WoText, WoLgID, WoTranslation, WoSentence, WoRomanization, WoStatus from words where WoID = ' . $wid;
	$res = mysql_query($sql);		
	if ($res == FALSE) die("Invalid Query: $sql");
	$dsatz = mysql_fetch_assoc($res);
	if ( $dsatz ) {
		$wort = $dsatz['WoText'];
		$lang = $dsatz['WoLgID'];
		$transl = repl_tab_nl($dsatz['WoTranslation']);
		if($transl == '*') $transl='';
		$sentence = repl_tab_nl($dsatz['WoSentence']);
		$rom = $dsatz['WoRomanization'];
		$status = $dsatz['WoStatus'];
	} else {
		die("Error: No results");
	}
	mysql_free_result($res);
	
	$wortlc =	mb_strtolower($wort, 'UTF-8');
	$titeltext = "Edit Term: " . tohtml($wort);
	pagestart_nobody($titeltext);

?>
	
<form name="editword" class="validate" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="WoID" value="<?php echo $wid; ?>" />
<input type="hidden" name="WoOldStatus" value="<?php echo $status; ?>" />
<input type="hidden" name="WoTextLC" value="<?php echo tohtml($wortlc); ?>" />
<table class="tab2" cellspacing="0" cellpadding="5">
<tr title="Only change uppercase/lowercase!">
<td class="td1 right"><b>Edit Term:</b></td>
<td class="td1"><input class="notempty" type="text" name="WoText" value="<?php echo tohtml($wort); ?>" maxlength="250" size="35" /> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<tr>
<td class="td1 right">Translation:</td>
<td class="td1"><textarea name="WoTranslation" class="setfocus textarea-noreturn checklength" data_maxlength="500" data_info="Translation" cols="35" rows="3"><?php echo tohtml($transl); ?></textarea></td>
</tr>
<tr>
<td class="td1 right">Romaniz.:</td>
<td class="td1"><input type="text" name="WoRomanization" maxlength="100" size="35" value="<?php echo tohtml($rom); ?>" /></td>
</tr>
<tr>
<td class="td1 right">Sentence<br />Term in {...}:</td>
<td class="td1"><textarea name="WoSentence" class="textarea-noreturn checklength" data_maxlength="1000" data_info="Sentence" cols="35" rows="3"><?php echo tohtml($sentence); ?></textarea></td>
</tr>
<tr>
<td class="td1 right">Status:</td>
<td class="td1">
<?php echo get_wordstatus_radiooptions($status); ?>
</td>
</tr>
<tr>
<td class="td1 right" colspan="2">
<?php echo createDictLinksInEditWin($lang,$wort,'document.forms[0].WoSentence',1); ?>
&nbsp; &nbsp; &nbsp; 
<input type="submit" name="op" value="Change" /></td>
</tr>
</table>
</form>
		
		<?php
		echo get20Sentences($lang, $wortlc, 'document.forms[\'editword\'].WoSentence', (int) getSettingWithDefault('set-term-sentence-count'));

} // if (! isset($_REQUEST['op']))

pageend();

?>