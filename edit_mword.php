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
Call: edit_mword.php?....
      ... op=Save ... do insert new 
      ... op=Change ... do update
      ... tid=[textid]&ord=[textpos]&wid=[wordid] ... edit  
      ... tid=[textid]&ord=[textpos]&txt=[word] ... new or edit
Edit/New Multi-word term (expression)
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

// INS/UPD

if (isset($_REQUEST['op'])) {
	
	$textlc = trim(prepare_textdata($_REQUEST["WoTextLC"]));
	$text = trim(prepare_textdata($_REQUEST["WoText"]));
	
	if (mb_strtolower($text, 'UTF-8') == $textlc) {
	
		// INSERT
		
		if ($_REQUEST['op'] == 'Save') {
	
			$titeltext = "New Term: " . tohtml(prepare_textdata($_REQUEST["WoTextLC"]));
			pagestart_nobody($titeltext);
			echo '<h4><span class="bigger">' . $titeltext . '</span></h4>';
		
			$message = runsql('insert into words (WoLgID, WoTextLC, WoText, ' .
				'WoStatus, WoTranslation, WoSentence, WoRomanization, WoStatusChanged,' .  make_score_random_insert_update('iv') . ') values( ' . 
				$_REQUEST["WoLgID"] . ', ' .
				convert_string_to_sqlsyntax($_REQUEST["WoTextLC"]) . ', ' .
				convert_string_to_sqlsyntax($_REQUEST["WoText"]) . ', ' .
				$_REQUEST["WoStatus"] . ', ' .
				convert_string_to_sqlsyntax(repl_tab_nl($_REQUEST["WoTranslation"])) . ', ' .
				convert_string_to_sqlsyntax(repl_tab_nl($_REQUEST["WoSentence"])) . ', ' .
				convert_string_to_sqlsyntax($_REQUEST["WoRomanization"]) . ', NOW(), ' .  
make_score_random_insert_update('id') . ')', "Term saved");
			$wid = get_last_key();
			
			$hex = strToClassName(prepare_textdata($_REQUEST["WoTextLC"]));
	
			
		} // $_REQUEST['op'] == 'Save'
		
		// UPDATE
		
		else {  // $_REQUEST['op'] != 'Save'
			
			$titeltext = "Edit Term: " . tohtml(prepare_textdata($_REQUEST["WoTextLC"]));
			pagestart_nobody($titeltext);
			echo '<h4><span class="bigger">' . $titeltext . '</span></h4>';
			
			$oldstatus = $_REQUEST["WoOldStatus"];
			$newstatus = $_REQUEST["WoStatus"];
			$xx = '';
			if ($oldstatus != $newstatus) $xx = ', WoStatus = ' .	$newstatus . ', WoStatusChanged = NOW()';
		
			$message = runsql('update words set WoText = ' . 
			convert_string_to_sqlsyntax($_REQUEST["WoText"]) . ', WoTranslation = ' . 
			convert_string_to_sqlsyntax(repl_tab_nl($_REQUEST["WoTranslation"])) . ', WoSentence = ' . 
			convert_string_to_sqlsyntax(repl_tab_nl($_REQUEST["WoSentence"])) . ', WoRomanization = ' .
			convert_string_to_sqlsyntax($_REQUEST["WoRomanization"]) . $xx . ',' . make_score_random_insert_update('u') . ' where WoID = ' . $_REQUEST["WoID"], "Updated");
			
			$wid = $_REQUEST["WoID"];
			
		} // $_REQUEST['op'] != 'Save'
		
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
var contexth = window.parent.frames['h'].document;
var woid = <?php echo prepare_textdata_js($wid); ?>;
var status = <?php echo prepare_textdata_js($_REQUEST["WoStatus"]); ?>;
var trans = <?php echo prepare_textdata_js(repl_tab_nl($_REQUEST["WoTranslation"])); ?>;
var roman = <?php echo prepare_textdata_js($_REQUEST["WoRomanization"]); ?>;
var title = make_tooltip(<?php echo prepare_textdata_js($_REQUEST["WoText"]); ?>,trans,roman,status);
<?php
	if ($_REQUEST['op'] == 'Save') {
		// new
		$showAll = getSetting('showallwords');
		$showAll = ($showAll == '' ? 1 : (((int) $showAll != 0) ? 1 : 0));
?>
$('.TERM<?php echo $hex; ?>', context).removeClass('hide').addClass('word' + woid + ' ' + 'status' + status).attr('data_trans',trans).attr('data_rom',roman).attr('data_status',status).attr('data_wid',woid).attr('title',title);
$('#learnstatus', contexth).html('<?php echo texttodocount2($_REQUEST['tid']); ?>');
<?php 
		if (! $showAll) echo refreshText($text,$_REQUEST['tid']);
?>
<?php 
	} else {
?>
$('.word' + woid, context).attr('data_trans',trans).attr('data_rom',roman).attr('title',title).removeClass('status<?php echo $_REQUEST['WoOldStatus']; ?>').addClass('status' + status).attr('data_status',status);
$('#learnstatus', contexth).html('<?php echo texttodocount2($_REQUEST['tid']); ?>');
<?php
	}
?>
window.parent.frames['l'].focus();
window.parent.frames['l'].setTimeout('cClick()', 100);
//]]>
</script>
	
<?php

} // if (isset($_REQUEST['op']))

else {  // if (! isset($_REQUEST['op']))

	// edit_mword.php?tid=..&ord=..&wid=..  ODER  edit_mword.php?tid=..&ord=..&txt=..
	
	$wid = getreq('wid');
	
	if ($wid == '') {	
		$lang = get_first_value("select TxLgID as value from texts where TxID = " . $_REQUEST['tid']);
		$wort = prepare_textdata(getreq('txt'));
		$wortlc =	mb_strtolower($wort, 'UTF-8');
		
		$wid = get_first_value("select WoID as value from words where WoLgID = " . $lang . " and WoTextLC = " . convert_string_to_sqlsyntax($wortlc)); 
		if (isset($wid)) $wort = get_first_value("select WoText as value from words where WoID = " . $wid); 
		
	} else {

		$sql = 'select WoText, WoLgID from words where WoID = ' . $wid;
		$res = mysql_query($sql);		
		if ($res == FALSE) die("Invalid Query: $sql");
		$dsatz = mysql_fetch_assoc($res);
		if ( $dsatz ) {
			$wort = $dsatz['WoText'];
			$lang = $dsatz['WoLgID'];
		} else {
			die("Error: No results");
		}
		mysql_free_result($res);
		$wortlc =	mb_strtolower($wort, 'UTF-8');
		
	}
	
	$neu = (isset($wid) == FALSE);

	$titeltext = ($neu ? "New Term" : "Edit Term") . ": " . $wort;
	pagestart_nobody($titeltext);
	
	// NEW
	
	if ($neu) {
		
		$seid = get_first_value("select TiSeID as value from textitems where TiTxID = " . $_REQUEST['tid'] . " and TiOrder = " . $_REQUEST['ord']);
		$sent = getSentence($seid, $wortlc, (int) getSettingWithDefault('set-term-sentence-count'));
			
		?>
	
		<form name="newword" class="validate" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<input type="hidden" name="WoLgID" value="<?php echo $lang; ?>" />
		<input type="hidden" name="WoTextLC" value="<?php echo tohtml($wortlc); ?>" />
		<input type="hidden" name="tid" value="<?php echo $_REQUEST['tid']; ?>" />
		<input type="hidden" name="ord" value="<?php echo $_REQUEST['ord']; ?>" />
		<table class="tab2" cellspacing="0" cellpadding="5">
		<tr title="Only change uppercase/lowercase!">
		<td class="td1 right"><b>New Term:</b></td>
		<td class="td1"><input class="notempty" type="text" name="WoText" value="<?php echo tohtml($wort); ?>" maxlength="250" size="35" /> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
		</tr>
		<tr>
		<td class="td1 right">Translation:</td>
		<td class="td1"><textarea name="WoTranslation" class="notempty setfocus textarea-noreturn" cols="35" rows="3"></textarea> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
		</tr>
		<tr>
		<td class="td1 right">Romaniz.:</td>
		<td class="td1"><input type="text" name="WoRomanization" value="" maxlength="100" size="35" /></td>
		</tr>
		<tr>
		<td class="td1 right">Sentence<br />Term in {...}:</td>
		<td class="td1"><textarea name="WoSentence" class="notempty textarea-noreturn" cols="35" rows="3"><?php echo tohtml(repl_tab_nl($sent[1])); ?></textarea> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
		</tr>
		<tr>
		<td class="td1 right">Status:</td>
		<td class="td1">
		<?php echo get_wordstatus_radiooptions(1); ?>
		</td>
		</tr>
		<tr>
		<tr>
		<td class="td1 right" colspan="2">
		<?php echo createDictLinksInEditWin($lang,$wort,'document.forms[0].WoSentence',1); ?>
		&nbsp; &nbsp; &nbsp; 
		<input type="submit" name="op" value="Save" /></td>
		</tr>
		</table>
		</form>
		
		<?php
		echo get20Sentences($lang, $wortlc, 'document.forms[\'newword\'].WoSentence', (int) getSettingWithDefault('set-term-sentence-count'));
		
	}
	
	// CHG
	
	else {
		
		$sql = 'select WoTranslation, WoSentence, WoRomanization, WoStatus from words where WoID = ' . $wid;
		$res = mysql_query($sql);		
		if ($res == FALSE) die("Invalid Query: $sql");
		if ($dsatz = mysql_fetch_assoc($res)) {
		
			$status = $dsatz['WoStatus'];
			if ($status >= 98) $status = 1;
			$sentence = repl_tab_nl($dsatz['WoSentence']);
			if ($sentence == '') {
				$seid = get_first_value("select TiSeID as value from textitems where TiTxID = " . $_REQUEST['tid'] . " and TiOrder = " . $_REQUEST['ord']);
				$sent = getSentence($seid, $wortlc, (int) getSettingWithDefault('set-term-sentence-count'));
				$sentence = repl_tab_nl($sent[1]);
			}
			$transl = repl_tab_nl($dsatz['WoTranslation']);
			if($transl == '*') $transl='';
			?>
		
			<form name="editword" class="validate" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="hidden" name="WoID" value="<?php echo $wid; ?>" />
			<input type="hidden" name="WoOldStatus" value="<?php echo $dsatz['WoStatus']; ?>" />
			<input type="hidden" name="WoStatus" value="<?php echo $status; ?>" />
			<input type="hidden" name="WoTextLC" value="<?php echo tohtml($wortlc); ?>" />
			<input type="hidden" name="tid" value="<?php echo $_REQUEST['tid']; ?>" />
			<input type="hidden" name="ord" value="<?php echo $_REQUEST['ord']; ?>" />
			<table class="tab2" cellspacing="0" cellpadding="5">
			<tr title="Only change uppercase/lowercase!">
			<td class="td1 right"><b>Edit Term:</b></td>
			<td class="td1"><input class="notempty" type="text" name="WoText" value="<?php echo tohtml($wort); ?>" maxlength="250" size="35" /> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
			</tr>
			<tr>
			<td class="td1 right">Translation:</td>
			<td class="td1"><textarea name="WoTranslation" class="notempty setfocus textarea-noreturn" cols="35" rows="3"><?php echo tohtml($transl); ?></textarea> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
			</tr>
			<tr>
			<td class="td1 right">Romaniz.:</td>
			<td class="td1"><input type="text" name="WoRomanization" maxlength="100" size="35" 
			value="<?php echo tohtml($dsatz['WoRomanization']); ?>" /></td>
			</tr>
			<tr>
			<td class="td1 right">Sentence<br />Term in {...}:</td>
			<td class="td1"><textarea name="WoSentence" class="notempty textarea-noreturn" cols="35" rows="3"><?php echo tohtml($sentence); ?></textarea> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
			</tr>
			<tr>
			<td class="td1 right">Status:</td>
			<td class="td1">
			<?php echo get_wordstatus_radiooptions($dsatz['WoStatus']); ?>
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
			echo get20Sentences($lang, $wortlc,'document.forms[\'editword\'].WoSentence', (int) getSettingWithDefault('set-term-sentence-count'));
	
		}
		mysql_free_result($res);
	}

}

pageend();

?>