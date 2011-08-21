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
Call: edit_words.php?....
      ... markaction=[opcode] ... do actions on marked terms
      ... allaction=[opcode] ... do actions on all terms
      ... del=[wordid] ... do delete
      ... op=Save ... do insert new 
      ... op=Change ... do update
      ... new=1&lang=[langid] ... display new term screen 
      ... chg=[wordid] ... display edit screen 
      ... filterlang=[langid] ... language filter 
      ... sort=[sortcode] ... sort 
      ... page=[pageno] ... page  
      ... query=[termtextfilter] ... term text filter   
      ... status=[statuscode] ... status filter   
      ... text=[textid] ... text filter   
      ... tag1=[tagid] ... tag filter 1   
      ... tag2=[tagid] ... tag filter 2   
      ... tag12=0/1 ... tag1-tag2 OR=0, AND=1   
Manage terms
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

$currentlang = validateLang(processDBParam("filterlang",'currentlanguage','',0));
$currentsort = processDBParam("sort",'currentwordsort','1',1);

$currentpage = processSessParam("page","currentwordpage",'1',1);
$currentquery = processSessParam("query","currentwordquery",'',0);
$currentstatus = processSessParam("status","currentwordstatus",'',0);
$currenttext = validateText(processSessParam("text","currentwordtext",'',0));
$currenttag1 = validateText(processSessParam("tag1","currentwordtag1",'',0));
$currenttag2 = validateText(processSessParam("tag2","currentwordtag2",'',0));
$currenttag12 = validateText(processSessParam("tag12","currentwordtag12",'',0));

$wh_lang = ($currentlang != '') ? (' and WoLgID=' . $currentlang ) : '';
$wh_stat = ($currentstatus != '') ? (' and ' . makeStatusCondition('WoStatus', $currentstatus)) : '';
$wh_query = convert_string_to_sqlsyntax(str_replace("*","%",$currentquery));
$wh_query = ($currentquery != '') ? (' and (WoText like ' . $wh_query . ' or WoRomanization like ' . $wh_query . ' or WoTranslation like ' . $wh_query . ')') : '';
if ($currenttag1 == '' && $currenttag2 == '')
	$wh_tag = '';
else {
	if ($currenttag1 != '' && $currenttag2 == '') 
		$wh_tag = " having concat('/',group_concat(WtTgID separator '/'),'/') like '%/" . $currenttag1 . "/%'";
	elseif ($currenttag1 == '' && $currenttag2 != '') 
		$wh_tag = " having concat('/',group_concat(WtTgID separator '/'),'/') like '%/" . $currenttag2 . "/%'";
	else 
		$wh_tag = " having concat('/',group_concat(WtTgID separator '/'),'/') like '%/" . $currenttag1 . "/%'" . ($currenttag12 ? ' and ' : ' or ') . "concat('/',group_concat(WtTgID separator '/'),'/') like '%/" . $currenttag2 . "/%'";
}

$no_pagestart = 
	(getreq('markaction') == 'exp') ||
	(getreq('markaction') == 'exp2') ||
	(getreq('markaction') == 'test') ||
	(getreq('allaction') == 'expall') ||
	(getreq('allaction') == 'expall2') ||
	(getreq('allaction') == 'testall');
if (! $no_pagestart) {
	pagestart('My ' . getLanguage($currentlang) . ' Words and Expressions (Terms)',true);
}

$message = '';

// MARK ACTIONS

if (isset($_REQUEST['markaction'])) {
	$markaction = $_REQUEST['markaction'];
	$message = "Multiple Actions: 0";
	if (isset($_REQUEST['marked'])) {
		if (is_array($_REQUEST['marked'])) {
			$l = count($_REQUEST['marked']);
			if ($l > 0 ) {
				$list = "(" . $_REQUEST['marked'][0];
				for ($i=1; $i<$l; $i++) $list .= "," . $_REQUEST['marked'][$i];
				$list .= ")";
				if ($markaction == 'del') {
					$message = runsql('delete from words where WoID in ' . $list, "Deleted");
					adjust_autoincr('words','WoID');
				}
				elseif ($markaction == 'spl1' ) {
					$message = runsql('update words set WoStatus=WoStatus+1, WoStatusChanged = NOW(),' . make_score_random_insert_update('u') . ' where WoStatus in (1,2,3,4) and WoID in ' . $list, "Updated Status (+1)");
				}
				elseif ($markaction == 'smi1' ) {
					$message = runsql('update words set WoStatus=WoStatus-1, WoStatusChanged = NOW(),' . make_score_random_insert_update('u') . ' where WoStatus in (2,3,4,5) and WoID in ' . $list, "Updated Status (-1)");
				}
				elseif ($markaction == 's5' ) {
					$message = runsql('update words set WoStatus=5, WoStatusChanged = NOW(),' . make_score_random_insert_update('u') . ' where WoID in ' . $list, "Updated Status (=5)");
				}
				elseif ($markaction == 's1' ) {
					$message = runsql('update words set WoStatus=1, WoStatusChanged = NOW(),' . make_score_random_insert_update('u') . ' where WoID in ' . $list, "Updated Status (=1)");
				}
				elseif ($markaction == 's99' ) {
					$message = runsql('update words set WoStatus=99, WoStatusChanged = NOW(),' . make_score_random_insert_update('u') . ' where WoID in ' . $list, "Updated Status (=99)");
				}
				elseif ($markaction == 's98' ) {
					$message = runsql('update words set WoStatus=98, WoStatusChanged = NOW(),' . make_score_random_insert_update('u') . ' where WoID in ' . $list, "Updated Status (=98)");
				}
				elseif ($markaction == 'today' ) {
					$message = runsql('update words set WoStatusChanged = NOW(),' . make_score_random_insert_update('u') . ' where WoID in ' . $list, "Updated Status Date (= Now)");
				}
				elseif ($markaction == 'exp' ) {
					anki_export('select distinct WoID, LgRegexpWordCharacters, LgName, WoText, WoTranslation, WoRomanization, WoSentence from words, languages where WoLgID = LgID AND WoTranslation != \'\' AND WoTranslation != \'*\' and WoSentence like concat(\'%{\',WoText,\'}%\') and WoID in ' . $list);
				}
				elseif ($markaction == 'exp2' ) {
					tsv_export('select distinct WoID, LgName, WoText, WoTranslation, WoRomanization, WoSentence, WoStatus from words, languages where WoLgID = LgID and WoID in ' . $list);
				}
				elseif ($markaction == 'test' ) {
					$_SESSION['testsql'] = ' words where WoID in ' . $list . ' ';
					header("Location: do_test.php?selection=1");
					exit();
				}
			}
		}
	}
}


// ALL ACTIONS 

if (isset($_REQUEST['allaction'])) {
	$allaction = $_REQUEST['allaction'];
	
	if ($allaction == 'delall') {
		if ($currenttext == '') {
			$message = runsql('delete from words where (1=1)' . $wh_lang . $wh_stat .  $wh_query . $wh_tag, "Deleted");
		} else {
			$message = runsql('delete words from words, textitems where TiLgID = WoLgID and TiTextLC = WoTextLC and TiTxID = ' . $currenttext . $wh_lang . $wh_stat . $wh_query . $wh_tag, "Deleted");
		}
	}
	
	elseif ($allaction == 'spl1all' || $allaction == 'smi1all' || $allaction == 's5all' || $allaction == 's1all' || $allaction == 's99all' || $allaction == 's98all' || $allaction == 'todayall') {
		if ($currenttext == '') {
			$sql = 'select distinct WoID from (words left JOIN wordtags ON WoID = WtWoID) where 1=1 ' . $wh_lang . $wh_stat . $wh_query . $wh_tag;
		} else {
			$sql = 'select distinct WoID from (words left JOIN wordtags ON WoID = WtWoID), textitems where TiLgID = WoLgID and TiTextLC = WoTextLC and TiTxID = ' . $currenttext . $wh_lang . $wh_stat . $wh_query . $wh_tag;
		}
		$cnt=0;
		$res = mysql_query($sql);
		if ($res == FALSE) die("Invalid Query: $sql");
		while ($dsatz = mysql_fetch_assoc($res)) {
			$id = $dsatz['WoID'];
			$message='0';
			if ($allaction == 'spl1all' ) {
				$message = runsql('update words set WoStatus=WoStatus+1, WoStatusChanged = NOW(),' . make_score_random_insert_update('u') . ' where WoStatus in (1,2,3,4) and WoID = ' . $id, "");
			}
			elseif ($allaction == 'smi1all' ) {
				$message = runsql('update words set WoStatus=WoStatus-1, WoStatusChanged = NOW(),' . make_score_random_insert_update('u') . ' where WoStatus in (2,3,4,5) and WoID = ' . $id, "");
			}
			elseif ($allaction == 's5all' ) {
				$message = runsql('update words set WoStatus=5, WoStatusChanged = NOW(),' . make_score_random_insert_update('u') . ' where WoID = ' . $id, "");
			}
			elseif ($allaction == 's1all' ) {
				$message = runsql('update words set WoStatus=1, WoStatusChanged = NOW(),' . make_score_random_insert_update('u') . ' where WoID = ' . $id, "");
			}
			elseif ($allaction == 's99all' ) {
				$message = runsql('update words set WoStatus=99, WoStatusChanged = NOW(),' . make_score_random_insert_update('u') . ' where WoID = ' . $id, "");
			}
			elseif ($allaction == 's98all' ) {
				$message = runsql('update words set WoStatus=98, WoStatusChanged = NOW(),' . make_score_random_insert_update('u') . ' where WoID = ' . $id, "");
			}
			elseif ($allaction == 'todayall' ) {
				$message = runsql('update words set WoStatusChanged = NOW(),' . make_score_random_insert_update('u') . ' where WoID = ' . $id, "");
			}
			$cnt += (int)$message;
		}
		mysql_free_result($res);
		$message = "Status changed in $cnt Terms";
	}

	elseif ($allaction == 'expall' ) {
		if ($currenttext == '') {
			anki_export('select distinct WoID, LgRegexpWordCharacters, LgName, WoText, WoTranslation, WoRomanization, WoSentence from (words left JOIN wordtags ON WoID = WtWoID), languages where WoLgID = LgID AND WoTranslation != \'\' AND WoTranslation != \'*\' and WoSentence like concat(\'%{\',WoText,\'}%\') ' . $wh_lang . $wh_stat . $wh_query . $wh_tag);
		} else {
			anki_export('select distinct WoID, LgRegexpWordCharacters, LgName, WoText, WoTranslation, WoRomanization, WoSentence from (words left JOIN wordtags ON WoID = WtWoID), languages, textitems where WoLgID = LgID AND WoTranslation != \'\' AND WoTranslation != \'*\' and WoSentence like concat(\'%{\',WoText,\'}%\') and TiLgID = WoLgID and TiTextLC = WoTextLC and TiTxID = ' . $currenttext . $wh_lang . $wh_stat . $wh_query . $wh_tag);
		}
	}
	
	elseif ($allaction == 'expall2' ) {
		if ($currenttext == '') {
			tsv_export('select distinct WoID, LgName, WoText, WoTranslation, WoRomanization, WoSentence, WoStatus from (words left JOIN wordtags ON WoID = WtWoID), languages where WoLgID = LgID ' . $wh_lang . $wh_stat .  $wh_query . $wh_tag);
		} else {
			tsv_export('select distinct WoID, LgName, WoText, WoTranslation, WoRomanization, WoSentence, WoStatus from (words left JOIN wordtags ON WoID = WtWoID), languages, textitems where WoLgID = LgID and TiLgID = WoLgID and TiTextLC = WoTextLC and TiTxID = ' . $currenttext . $wh_lang . $wh_stat . $wh_query . $wh_tag);
		}
	}
	
	elseif ($allaction == 'testall' ) {
		if ($currenttext == '') {
			$_SESSION['testsql'] = ' (words left JOIN wordtags ON WoID = WtWoID) where (1=1) ' . $wh_lang . $wh_stat . $wh_query . $wh_tag . ' ';
		} else {
			$_SESSION['testsql'] = ' (words left JOIN wordtags ON WoID = WtWoID), textitems where TiLgID = WoLgID and TiTextLC = WoTextLC and TiTxID = ' . $currenttext . $wh_lang . $wh_stat . $wh_query . $wh_tag . ' ';
		}
		header("Location: do_test.php?selection=1");
		exit();
	}

}

// DEL

elseif (isset($_REQUEST['del'])) {
	$message = runsql('delete from words where WoID = ' . $_REQUEST['del'], "Deleted");
	adjust_autoincr('words','WoID');
}

// INS/UPD

elseif (isset($_REQUEST['op'])) {

	$translation_raw = repl_tab_nl(getreq("WoTranslation"));
	if ( $translation_raw == '' ) $translation = '*';
	else $translation = $translation_raw;
	
	// INSERT
	
	if ($_REQUEST['op'] == 'Save') {
	
		$message = runsql('insert into words (WoLgID, WoTextLC, WoText, ' .
			'WoStatus, WoTranslation, WoSentence, WoRomanization, WoStatusChanged,' .  make_score_random_insert_update('iv') . ') values( ' . 
			$_REQUEST["WoLgID"] . ', ' .
			convert_string_to_sqlsyntax(mb_strtolower($_REQUEST["WoText"], 'UTF-8')) . ', ' .
			convert_string_to_sqlsyntax($_REQUEST["WoText"]) . ', ' .
			$_REQUEST["WoStatus"] . ', ' .
			convert_string_to_sqlsyntax($translation) . ', ' .
			convert_string_to_sqlsyntax(repl_tab_nl($_REQUEST["WoSentence"])) . ', ' .
			convert_string_to_sqlsyntax($_REQUEST["WoRomanization"]) . ', NOW(), ' .  
make_score_random_insert_update('id') . ')', "Saved");
		$wid = get_last_key();
	}	
	
	// UPDATE
	
	else {

		$oldstatus = $_REQUEST["WoOldStatus"];
		$newstatus = $_REQUEST["WoStatus"];
		$xx = '';
		if ($oldstatus != $newstatus) $xx = ', WoStatus = ' .	$newstatus . ', WoStatusChanged = NOW()';
		$wid = $_REQUEST["WoID"] + 0;
		$message = runsql('update words set WoText = ' . 
			convert_string_to_sqlsyntax($_REQUEST["WoText"]) . ', WoTextLC = ' . 
			convert_string_to_sqlsyntax(mb_strtolower($_REQUEST["WoText"], 'UTF-8')) . ', WoTranslation = ' . 
			convert_string_to_sqlsyntax($translation) . ', WoSentence = ' . 
			convert_string_to_sqlsyntax(repl_tab_nl($_REQUEST["WoSentence"])) . ', WoRomanization = ' .
			convert_string_to_sqlsyntax($_REQUEST["WoRomanization"]) . $xx . ',' . make_score_random_insert_update('u') . ' where WoID = ' . $_REQUEST["WoID"],
			"Updated");
	}
	
	saveWordTags($wid);

}

// NEW

if (isset($_REQUEST['new']) && isset($_REQUEST['lang'])) {
	
	?>

	<h4>New Term</h4>
	<form name="newword" class="validate" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<input type="hidden" name="WoLgID" value="<?php echo $_REQUEST['lang']; ?>" />
	<table class="tab3" cellspacing="0" cellpadding="5">
	<tr>
	<td class="td1 right">Language:</td>
	<td class="td1"><?php echo tohtml(getLanguage($_REQUEST['lang'])); ?></td>
	</tr>
	<tr>
	<td class="td1 right">Term:</td>
	<td class="td1"><input class="notempty" type="text" name="WoText" value="" maxlength="250" size="40" /> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
	</tr>
	<tr>
	<td class="td1 right">Translation:</td>
	<td class="td1"><textarea class="textarea-noreturn checklength" data_maxlength="500" data_info="Translation" name="WoTranslation" cols="40" rows="3"></textarea></td>
	</tr>
	<tr>
	<td class="td1 right">Tags:</td>
	<td class="td1">
	<?php echo getWordTags(0); ?>
	</td>
	</tr>
	<tr>
	<td class="td1 right">Romaniz.:</td>
	<td class="td1"><input type="text" name="WoRomanization" value="" maxlength="100" size="40" /></td>
	</tr>
	<tr>
	<td class="td1 right">Sentence<br />Term in {...}:</td>
	<td class="td1"><textarea name="WoSentence" cols="40" rows="3" class="textarea-noreturn checklength" data_maxlength="1000" data_info="Sentence"></textarea></td>
	</tr>
	<tr>
	<td class="td1 right">Status:</td>
	<td class="td1">
	<?php echo get_wordstatus_radiooptions(1); ?>
	</td>
	</tr>
	<tr>
	<td class="td1 right" colspan="2">  &nbsp;
		<?php echo createDictLinksInEditWin2($_REQUEST['lang'],'document.forms[\'newword\'].WoSentence','document.forms[\'newword\'].WoText'); ?>
		&nbsp; &nbsp;
	<input type="button" value="Cancel" onclick="location.href='edit_words.php';" /> 
	<input type="submit" name="op" value="Save" /></td>
	</tr>
	</table>
	</form>
	
	<?php
	
}

// CHG

elseif (isset($_REQUEST['chg'])) {
	
	$sql = 'select * from words, languages where LgID = WoLgID and WoID = ' . $_REQUEST['chg'];
	$res = mysql_query($sql);		
	if ($res == FALSE) die("Invalid Query: $sql");
	if ($dsatz = mysql_fetch_assoc($res)) {
		
		$wordlc = $dsatz['WoTextLC'];
		$transl = repl_tab_nl($dsatz['WoTranslation']);
		if($transl == '*') $transl='';
	
		?>
	
		<h4>Edit Term</h4>
		<form name="editword" class="validate" action="<?php echo $_SERVER['PHP_SELF']; ?>#rec<?php echo $_REQUEST['chg']; ?>" method="post">
		<input type="hidden" name="WoID" value="<?php echo $dsatz['WoID']; ?>" />
		<input type="hidden" name="WoOldStatus" value="<?php echo $dsatz['WoStatus']; ?>" />
		<table class="tab3" cellspacing="0" cellpadding="5">
		<tr>
		<td class="td1 right">Language:</td>
		<td class="td1"><?php echo tohtml($dsatz['LgName']); ?></td>
		</tr>
		<tr title="Normally only change uppercase/lowercase here!">
		<td class="td1 right">Term:</td>
		<td class="td1"><input class="notempty setfocus" type="text" name="WoText" value="<?php echo tohtml($dsatz['WoText']); ?>" maxlength="250" size="40" /> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
		</tr>
		<tr>
		<td class="td1 right">Translation:</td>
		<td class="td1"><textarea class="textarea-noreturn checklength" data_maxlength="500" data_info="Translation" name="WoTranslation" cols="40" rows="3"><?php echo tohtml($transl); ?></textarea></td>
		</tr>
		<tr>
		<td class="td1 right">Tags:</td>
		<td class="td1">
		<?php echo getWordTags($dsatz['WoID']); ?>
		</td>
		</tr>
		<tr>
		<td class="td1 right">Romaniz.:</td>
		<td class="td1"><input type="text" name="WoRomanization" maxlength="100" size="40" 
		value="<?php echo tohtml($dsatz['WoRomanization']); ?>" /></td>
		</tr>
		<tr>
		<td class="td1 right">Sentence<br />Term in {...}:</td>
		<td class="td1"><textarea class="textarea-noreturn checklength" data_maxlength="1000" data_info="Sentence" name="WoSentence" cols="40" rows="3"><?php echo tohtml(repl_tab_nl($dsatz['WoSentence'])); ?></textarea></td>
		</tr>
		<tr>
		<td class="td1 right">Status:</td>
		<td class="td1">
		<?php echo get_wordstatus_radiooptions($dsatz['WoStatus']); ?>
		</td>
		</tr>
		<tr>
		<td class="td1 right" colspan="2">  &nbsp;
		<?php echo createDictLinksInEditWin2($dsatz['WoLgID'],'document.forms[\'editword\'].WoSentence','document.forms[\'editword\'].WoText'); ?>
		&nbsp; &nbsp;
		<input type="button" value="Cancel" onclick="location.href='edit_words.php#rec<?php echo $_REQUEST['chg']; ?>';" /> 
		<input type="submit" name="op" value="Change" /></td>
		</tr>
		</table>
		</form>
		<div id="exsent"><span class="click" onclick="do_ajax_show_sentences(<?php echo $dsatz['LgID']; ?>, <?php echo prepare_textdata_js($wordlc) . ', ' . prepare_textdata_js("document.forms['editword'].WoSentence"); ?>);"><img src="icn/sticky-notes-stack.png" title="Show Sentences" alt="Show Sentences" /> Show Sentences</span></div>	
<?php
	}
	mysql_free_result($res);
}

// DISPLAY

else {
	
	echo error_message_with_hide($message,0);

	if ($currenttext == '') {
		$recno = get_first_value('select count(*) as value from (select WoID from (words left JOIN wordtags ON WoID = WtWoID) where (1=1) ' . $wh_lang . $wh_stat .  $wh_query . ' group by WoID ' . $wh_tag .') as dummy');
	} else {
		$recno = get_first_value('select count(*) as value from (select WoID from (words left JOIN wordtags ON WoID = WtWoID), textitems where TiLgID = WoLgID and TiTextLC = WoTextLC and TiTxID = ' . $currenttext . $wh_lang . $wh_stat . $wh_query . ' group by WoID ' . $wh_tag .') as dummy');
	}
	
	$maxperpage = getSettingWithDefault('set-terms-per-page');

	$pages = $recno == 0 ? 0 : (intval(($recno-1) / $maxperpage) + 1);
	
	if ($currentpage < 1) $currentpage = 1;
	if ($currentpage > $pages) $currentpage = $pages;
	$limit = 'LIMIT ' . (($currentpage-1) * $maxperpage) . ',' . $maxperpage;

	$sorts = array('WoTextLC','lower(WoTranslation)','WoID desc','WoStatus, WoTextLC','WoTodayScore');
	$lsorts = count($sorts);
	if ($currentsort < 1) $currentsort = 1;
	if ($currentsort > $lsorts) $currentsort = $lsorts;
	

	if ($currentlang != '') {
?>
<p><a href="<?php echo $_SERVER['PHP_SELF']; ?>?new=1&amp;lang=<?php echo $currentlang; ?>"><img src="icn/plus-button.png" title="New" alt="New" /> New <?php echo tohtml(getLanguage($currentlang)); ?> Term ...</a></p>
<?php
	} else {
?>
<p><img src="icn/plus-button.png" title="New" alt="New" /> New Term? - Set Language Filter first ...</p>
<?php
	}
?>

<form name="form1" action="#" onsubmit="document.form1.querybutton.click(); return false;">
<table class="tab1" cellspacing="0" cellpadding="5">
<tr>
<th class="th1" colspan="4">Filter <img src="icn/funnel.png" title="Filter" alt="Filter" />&nbsp;
<input type="button" value="Reset All" onclick="resetAll('edit_words.php');" /></th>
</tr>
<tr>
<td class="td1 center" colspan="2">
Language:
<select name="filterlang" onchange="{setLang(document.form1.filterlang,'edit_words.php');}"><?php	echo get_languages_selectoptions($currentlang,'[Filter off]'); ?></select>
</td>
<td class="td1 center" colspan="2">
Text:
<select name="text" onchange="{val=document.form1.text.options[document.form1.text.selectedIndex].value; location.href='edit_words.php?page=1&amp;text=' + val;}"><?php echo get_texts_selectoptions($currentlang,$currenttext); ?></select>
</td>
</tr>
<tr>
<td class="td1 center" colspan="2" nowrap="nowrap">
Status:
<select name="status" onchange="{val=document.form1.status.options[document.form1.status.selectedIndex].value; location.href='edit_words.php?page=1&amp;status=' + val;}"><?php echo get_wordstatus_selectoptions($currentstatus,true,false); ?></select>
</td>
<td class="td1 center" colspan="2" nowrap="nowrap">
Term, Rom., Transl. (Wildc.=*):
<input type="text" name="query" value="<?php echo tohtml($currentquery); ?>" maxlength="50" size="15" />&nbsp;
<input type="button" name="querybutton" value="Filter" onclick="{val=document.form1.query.value; location.href='edit_words.php?page=1&amp;query=' + val;}" />&nbsp;
<input type="button" value="Clear" onclick="{location.href='edit_words.php?page=1&amp;query=';}" />
</td>
</tr>
<tr>
<td class="td1 center" colspan="2" nowrap="nowrap">
Tag #1:
<select name="tag1" onchange="{val=document.form1.tag1.options[document.form1.tag1.selectedIndex].value; location.href='edit_words.php?page=1&amp;tag1=' + val;}"><?php echo get_tag_selectoptions($currenttag1); ?></select>
</td>
<td class="td1 center" nowrap="nowrap">
Tag #1 .. <select name="tag12" onchange="{val=document.form1.tag12.options[document.form1.tag12.selectedIndex].value; location.href='edit_words.php?page=1&amp;tag12=' + val;}"><?php echo get_andor_selectoptions($currenttag12); ?></select> .. Tag #2
</td>
<td class="td1 center" nowrap="nowrap">
Tag #2:
<select name="tag2" onchange="{val=document.form1.tag2.options[document.form1.tag2.selectedIndex].value; location.href='edit_words.php?page=1&amp;tag2=' + val;}"><?php echo get_tag_selectoptions($currenttag2); ?></select>
</td>
</tr>
<?php if($recno > 0) { ?>
<tr>
<th class="th1" nowrap="nowrap">
<?php echo $recno; ?> Term<?php echo ($recno==1?'':'s'); ?>
</th><th class="th1" colspan="2" nowrap="nowrap">
<?php makePager ($currentpage, $pages, 'edit_words.php', 'form1'); ?>
</th><th class="th1" nowrap="nowrap">
Sort Order:
<select name="sort" onchange="{val=document.form1.sort.options[document.form1.sort.selectedIndex].value; location.href='edit_words.php?page=1&amp;sort=' + val;}"><?php echo get_wordssort_selectoptions($currentsort); ?></select>
</th></tr>
<?php } ?>
</table>
</form>

<?php
if ($recno==0) {
?>
<p>No terms found.</p>
<?php
} else {
?>
<form name="form2" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<table class="tab1" cellspacing="0" cellpadding="5">
<tr><th class="th1 center" colspan="2">
Multi Actions <img src="icn/lightning.png" title="Multi Actions" alt="Multi Actions" />
</th></tr>
<tr><td class="td1 center" colspan="2">
<b>ALL</b> <?php echo ($recno == 1 ? '1 Term' : $recno . ' Terms'); ?>:&nbsp; 
<select name="allaction" onchange="allActionGo(document.form2, document.form2.allaction,<?php echo $recno; ?>);"><?php echo get_allwordsactions_selectoptions(); ?></select>
</td></tr>
<tr><td class="td1 center">
<input type="button" value="Mark All" onclick="selectToggle(true,'form2');" />
<input type="button" value="Mark None" onclick="selectToggle(false,'form2');" />
</td>
<td class="td1 center">Marked Terms:&nbsp; 
<select name="markaction" id="markaction" disabled="disabled" onchange="multiActionGo(document.form2, document.form2.markaction);"><?php echo get_multiplewordsactions_selectoptions(); ?></select>
</td></tr></table>

<table class="sortable tab1"  cellspacing="0" cellpadding="5">
<tr>
<th class="th1 sorttable_nosort">Mark</th>
<th class="th1 sorttable_nosort">Act.</th>
<th class="th1 clickable">Lang.</th>
<th class="th1 clickable">Term /<br />Romanization</th>
<th class="th1 clickable">Translation [Tags]</th>
<th class="th1 sorttable_nosort">Se.<br />?</th>
<th class="th1 sorttable_numeric clickable">Stat./<br />Days</th>
<th class="th1 sorttable_numeric clickable">Score<br />%</th>
</tr>

<?php

if ($currenttext == '') {
	$sql = 'select WoID, WoText, WoTranslation, WoRomanization, ifnull(WoSentence,\'\') like concat(\'%{\',WoText,\'}%\') as SentOK, WoStatus, LgName, DATEDIFF( NOW( ) , WoStatusChanged ) AS Days, WoTodayScore AS Score, WoTomorrowScore AS Score2, ifnull(concat(\'[\',group_concat(distinct TgText order by TgText separator \', \'),\']\'),\'\') as taglist from ((words left JOIN wordtags ON WoID = WtWoID) left join tags on TgID = WtTgID), languages where WoLgID = LgID ' . $wh_lang . $wh_stat .  $wh_query . ' group by WoID ' . $wh_tag . ' order by ' . $sorts[$currentsort-1] . ' ' . $limit;
} else {
	$sql = 'select distinct WoID, WoText, WoTranslation, WoRomanization, ifnull(WoSentence,\'\') like \'%{%}%\' as SentOK, WoStatus, LgName, DATEDIFF( NOW( ) , WoStatusChanged ) AS Days, WoTodayScore AS Score, WoTomorrowScore AS Score2, ifnull(concat(\'[\',group_concat(distinct TgText order by TgText separator \', \'),\']\'),\'\') as taglist from ((words left JOIN wordtags ON WoID = WtWoID) left join tags on TgID = WtTgID), languages, textitems where TiLgID = WoLgID and TiTextLC = WoTextLC and TiTxID = ' . $currenttext . ' and WoLgID = LgID ' . $wh_lang . $wh_stat . $wh_query . ' group by WoID ' . $wh_tag . ' order by ' . $sorts[$currentsort-1] . ' ' . $limit;
}
if ($debug) echo $sql;

$res = mysql_query($sql);		
if ($res == FALSE) die("Invalid Query: $sql");
while ($dsatz = mysql_fetch_assoc($res)) {
	$days = $dsatz['Days'];
	if ( $dsatz['WoStatus'] > 5 ) $days="-";
	$score = $dsatz['Score'];
	if ( $score < 0 ) $score='<span class="scorered">0 <img src="icn/status-busy.png" title="Test today!" alt="Test today!" /></span>';
	else $score='<span class="scoregreen">' . floor($score) . ($dsatz['Score2'] < 0 ? ' <img src="icn/status-away.png" title="Test tomorrow!" alt="Test tomorrow!" />' : ' <img src="icn/status.png" title="-" alt="-" />') . '</span>';
	echo '<tr>';
	echo '<td class="td1 center"><a name="rec' . $dsatz['WoID'] . '"><input name="marked[]" type="checkbox" class="markcheck" value="' . $dsatz['WoID'] . '" ' . checkTest($dsatz['WoID'], 'marked') . ' /></a></td>';
	echo '<td class="td1 center" nowrap="nowrap">&nbsp;<a href="' . $_SERVER['PHP_SELF'] . '?chg=' . $dsatz['WoID'] . '"><img src="icn/document--pencil.png" title="Edit" alt="Edit" /></a>&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?del=' . $dsatz['WoID'] . '"><img src="icn/minus-button.png" title="Delete" alt="Delete" /></a>&nbsp;</td>';
	echo '<td class="td1 center">' . tohtml($dsatz['LgName']) . '</td>';
	echo '<td class="td1 ">' . tohtml($dsatz['WoText'] . ($dsatz['WoRomanization']!='' ? (' / ' . $dsatz['WoRomanization']) : '')) . '</td>';
	echo '<td class="td1 ">' . tohtml(repl_tab_nl($dsatz['WoTranslation'])) . ' <span class="smallgray2">' . tohtml($dsatz['taglist']) . '</span></td>';
	echo '<td class="td1 center"><b>' . ($dsatz['SentOK']!=0 ? '<img src="icn/status.png" title="Yes" alt="Yes" />' : '<img src="icn/status-busy.png" title="No" alt="No" />') . '</b></td>';
	echo '<td class="td1 center" title="' . tohtml(get_status_name($dsatz['WoStatus'])) . '">' . tohtml(get_status_abbr($dsatz['WoStatus'])) . ($dsatz['WoStatus'] < 98 ? '/' . $days : '') . '</td>';
	echo '<td class="td1 center" nowrap="nowrap">' . $score . '</td>';
	echo '</tr>';
}
mysql_free_result($res);

?>
</table>
</form>

<?php if( $pages > 1) { ?>
<table class="tab1" cellspacing="0" cellpadding="5">
<tr>
<th class="th1" nowrap="nowrap">
<?php echo $recno; ?> Term<?php echo ($recno==1?'':'s'); ?>
</th><th class="th1" nowrap="nowrap">
<?php makePager ($currentpage, $pages, 'edit_words.php', 'form1'); ?>
</th></tr></table>
<?php } ?>

<?php
}

}

pageend();

?>