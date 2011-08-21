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
Call: edit_texts.php?....
      ... markaction=[opcode] ... do actions on marked texts
      ... del=[textid] ... do delete
      ... arch=[textid] ... do archive
      ... op=Check ... do check
      ... op=Save ... do insert new 
      ... op=Change ... do update
      ... op=Save+and+Open ... do insert new and open 
      ... op=Change+and+Open ... do update and open
      ... new=1 ... display new text screen 
      ... chg=[textid] ... display edit screen 
      ... filterlang=[langid] ... language filter 
      ... sort=[sortcode] ... sort 
      ... page=[pageno] ... page  
      ... query=[titlefilter] ... title filter   
Manage active texts
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

// Page, Sort, etc. 

$currentlang = validateLang(processDBParam("filterlang",'currentlanguage','',0));
$currentsort = processDBParam("sort",'currenttextsort','1',1);

$currentpage = processSessParam("page","currenttextpage",'1',1);
$currentquery = processSessParam("query","currenttextquery",'',0);

$no_pagestart = (getreq('markaction') == 'test' || substr(getreq('op'),-8) == 'and Open');

if (! $no_pagestart) {
	pagestart('My ' . getLanguage($currentlang) . ' Texts',true);
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
					$message3 = runsql('delete from textitems where TiTxID in ' . $list, "Text items deleted");
					$message2 = runsql('delete from sentences where SeTxID in ' . $list, "Sentences deleted");
					$message1 = runsql('delete from texts where TxID in ' . $list, "Texts deleted");
					$message = $message1 . " / " . $message2 . " / " . $message3;
					adjust_autoincr('texts','TxID');
					adjust_autoincr('sentences','SeID');
					adjust_autoincr('textitems','TiID');
				} 
				
				elseif ($markaction == 'arch') {
					$message3 = runsql('delete from textitems where TiTxID in ' . $list, "Text items deleted");
					$message2 = runsql('delete from sentences where SeTxID in ' . $list, "Sentences deleted");
					$message4 = runsql('insert into archivedtexts (AtLgID, AtTitle, AtText, AtAudioURI) select TxLgID, TxTitle, TxText, TxAudioURI from texts where TxID in ' . $list, "Archived Texts saved");
					$message1 = runsql('delete from texts where TxID in ' . $list, "Texts deleted");
					$message = $message4 . " / " . $message1 . " / " . $message2 . " / " . $message3;
					adjust_autoincr('texts','TxID');
					adjust_autoincr('sentences','SeID');
					adjust_autoincr('textitems','TiID');
				} 
				
				elseif ($markaction == 'setsent') {
					$count = 0;
					$sql = "select WoID, WoTextLC, min(TiSeID) as SeID from words, textitems where TiLgID = WoLgID and TiTextLC = WoTextLC and TiTxID in " . $list . " and ifnull(WoSentence,'') not like concat('%{',WoText,'}%') group by WoID order by WoID, min(TiSeID)";
					$res = mysql_query($sql);		
					if ($res == FALSE) die("Invalid Query: $sql");
					while ($dsatz = mysql_fetch_assoc($res)) {
						$sent = getSentence($dsatz['SeID'], $dsatz['WoTextLC'], (int) getSettingWithDefault('set-term-sentence-count'));
						$count += runsql('update words set WoSentence = ' . convert_string_to_sqlsyntax(repl_tab_nl($sent[1])) . ' where WoID = ' . $dsatz['WoID'], '');
					}
					mysql_free_result($res);
					$message = 'Term Sentences set from Text(s): ' . $count;
				} 
				
				elseif ($markaction == 'rebuild') {
					$count = 0;
					$sql = "select TxID, TxLgID from texts where TxID in " . $list;
					$res = mysql_query($sql);		
					if ($res == FALSE) die("Invalid Query: $sql");
					while ($dsatz = mysql_fetch_assoc($res)) {
						$id = $dsatz['TxID'];
						$message2 = runsql('delete from sentences where SeTxID = ' . $id, "Sentences deleted");
						$message3 = runsql('delete from textitems where TiTxID = ' . $id, "Text items deleted");
						adjust_autoincr('sentences','SeID');
						adjust_autoincr('textitems','TiID');
						splitText(
							get_first_value(
								'select TxText as value from texts where TxID = ' . $id), 
								$dsatz['TxLgID'], $id );
						$count++;
					}
					mysql_free_result($res);
					$message = 'Text(s) re-parsed: ' . $count;
				}
				
				elseif ($markaction == 'test' ) {
					$_SESSION['testsql'] = ' words, textitems where TiLgID = WoLgID and TiTextLC = WoTextLC and TiTxID in ' . $list . ' ';
					header("Location: do_test.php?selection=1");
					exit();
				}
				
			}
		}
	}
}

// DEL

if (isset($_REQUEST['del'])) {
	$message3 = runsql('delete from textitems where TiTxID = ' . $_REQUEST['del'], 
		"Text items deleted");
	$message2 = runsql('delete from sentences where SeTxID = ' . $_REQUEST['del'], 
		"Sentences deleted");
	$message1 = runsql('delete from texts where TxID = ' . $_REQUEST['del'], 
		"Texts deleted");
	$message = $message1 . " / " . $message2 . " / " . $message3;
	adjust_autoincr('texts','TxID');
	adjust_autoincr('sentences','SeID');
	adjust_autoincr('textitems','TiID');
}

// ARCH

elseif (isset($_REQUEST['arch'])) {
	$message3 = runsql('delete from textitems where TiTxID = ' . $_REQUEST['arch'], 
		"Text items deleted");
	$message2 = runsql('delete from sentences where SeTxID = ' . $_REQUEST['arch'], 
		"Sentences deleted");
	$message4 = runsql('insert into archivedtexts (AtLgID, AtTitle, AtText, AtAudioURI) select TxLgID, TxTitle, TxText, TxAudioURI from texts where TxID = ' . $_REQUEST['arch'], "Archived Texts saved");
	$message1 = runsql('delete from texts where TxID = ' . $_REQUEST['arch'], "Texts deleted");
	$message = $message4 . " / " . $message1 . " / " . $message2 . " / " . $message3;
	adjust_autoincr('texts','TxID');
	adjust_autoincr('sentences','SeID');
	adjust_autoincr('textitems','TiID');
}

// INS/UPD

elseif (isset($_REQUEST['op'])) {

	if (strlen(prepare_textdata($_REQUEST['TxText'])) > 65000) {
		$message = "Error: Text too long, must be below 65000 Bytes";
		if ($no_pagestart) pagestart('My ' . getLanguage($currentlang) . ' Texts',true);
	}

	else {
	
		// CHECK
		
		if ($_REQUEST['op'] == 'Check') {
			echo '<p><input type="button" value="&lt;&lt; Back" onclick="history.back();" /></p>';
			echo checkText($_REQUEST['TxText'], $_REQUEST['TxLgID']);
			echo '<p><input type="button" value="&lt;&lt; Back" onclick="history.back();" /></p>';
			pageend();
			exit();
		} 
		
		// INSERT
		
		elseif (substr($_REQUEST['op'],0,4) == 'Save') {
			$message1 = runsql('insert into texts (TxLgID, TxTitle, TxText, TxAudioURI) values( ' . 
			$_REQUEST["TxLgID"] . ', ' . 
			convert_string_to_sqlsyntax($_REQUEST["TxTitle"]) . ', ' . 
			convert_string_to_sqlsyntax($_REQUEST["TxText"]) . ', ' .
			convert_string_to_sqlsyntax($_REQUEST["TxAudioURI"]) . ' ' .
			')', "Saved");
			$id = get_last_key();
		} 
		
		// UPDATE
		
		elseif (substr($_REQUEST['op'],0,6) == 'Change') {
			$message1 = runsql('update texts set ' .
			'TxLgID = ' . $_REQUEST["TxLgID"] . ', ' .
			'TxTitle = ' . convert_string_to_sqlsyntax($_REQUEST["TxTitle"]) . ', ' .
			'TxText = ' . convert_string_to_sqlsyntax($_REQUEST["TxText"]) . ', ' .
			'TxAudioURI = ' . convert_string_to_sqlsyntax($_REQUEST["TxAudioURI"]) . ' ' .
			'where TxID = ' . $_REQUEST["TxID"], "Updated");
			$id = $_REQUEST["TxID"];
		}
		
		$message2 = runsql('delete from sentences where SeTxID = ' . $id, 
			"Sentences deleted");
		$message3 = runsql('delete from textitems where TiTxID = ' . $id, 
			"Textitems deleted");
		adjust_autoincr('sentences','SeID');
		adjust_autoincr('textitems','TiID');
	
		splitText(
			get_first_value(
				'select TxText as value from texts where TxID = ' . $id), 
			$_REQUEST["TxLgID"], $id );
			
		$message = $message1 . " / " . $message2 . " / " . $message3 . " / Sentences added: " . get_first_value('select count(*) as value from sentences where SeTxID = ' . $id) . " / Text items added: " . get_first_value('select count(*) as value from textitems where TiTxID = ' . $id);
		
		if(substr($_REQUEST['op'],-8) == "and Open") {
			header('Location: do_text.php?start=' . $id);
			exit();
		}
	
	}

}

if (isset($_REQUEST['new'])) {

// NEW
	
	?>

	<h4>New Text <a target="_blank" href="info.htm#howtotext"><img src="icn/question-frame.png" title="Help" alt="Help" /></a> </h4>
	<form class="validate" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<table class="tab3" cellspacing="0" cellpadding="5">
	<tr>
	<td class="td1 right">Language:</td>
	<td class="td1">
	<select name="TxLgID" class="notempty setfocus">
	<?php
	echo get_languages_selectoptions($currentlang,'[Choose...]');
	?>
	</select> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
	</td>
	</tr>
	<tr>
	<td class="td1 right">Title:</td>
	<td class="td1"><input type="text" class="notempty" name="TxTitle" value="" maxlength="200" size="60" /> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
	</tr>
	<tr>
	<td class="td1 right">Text:</td>
	<td class="td1">
	<textarea name="TxText" class="notempty checkbytes" data_maxlength="65000" data_info="Text" cols="60" rows="20"></textarea> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
	</td>
	</tr>
	<tr>
	<td class="td1 right">Audio-URI:</td>
	<td class="td1"><input type="text" name="TxAudioURI" value="" maxlength="200" size="60" />		
	<span id="mediaselect"><?php echo selectmediapath('TxAudioURI'); ?></span>		
	</td>
	</tr>
	<tr>
	<td class="td1 right" colspan="2">
	<input type="button" value="Cancel" onclick="location.href='edit_texts.php';" /> 
	<input type="submit" name="op" value="Check" />
	<input type="submit" name="op" value="Save" />
	<input type="submit" name="op" value="Save and Open" />
	</td>
	</tr>
	</table>
	</form>
	
	<?php
	
}

// CHG

elseif (isset($_REQUEST['chg'])) {
	
	$sql = 'select TxLgID, TxTitle, TxText, TxAudioURI from texts where TxID = ' . $_REQUEST['chg'];
	$res = mysql_query($sql);		
	if ($res == FALSE) die("Invalid Query: $sql");
	if ($dsatz = mysql_fetch_assoc($res)) {

		?>
	
		<h4>Edit Text <a target="_blank" href="info.htm#howtotext"><img src="icn/question-frame.png" title="Help" alt="Help" /></a></h4>
		<form class="validate" action="<?php echo $_SERVER['PHP_SELF']; ?>#rec<?php echo $_REQUEST['chg']; ?>" method="post">
		<input type="hidden" name="TxID" value="<?php echo $_REQUEST['chg']; ?>" />
		<table class="tab3" cellspacing="0" cellpadding="5">
		<tr>
		<td class="td1 right">Language:</td>
		<td class="td1">
		<select name="TxLgID" class="notempty setfocus">
		<?php
		echo get_languages_selectoptions($dsatz['TxLgID'],"[Choose...]");
		?>
		</select> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
		</td>
		</tr>
		<tr>
		<td class="td1 right">Title:</td>
		<td class="td1"><input type="text" class="notempty" name="TxTitle" value="<?php echo tohtml($dsatz['TxTitle']); ?>" maxlength="200" size="60" /> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
		</tr>
		<tr>
		<td class="td1 right">Text:</td>
		<td class="td1">
		<textarea name="TxText" class="notempty checkbytes" data_maxlength="65000" data_info="Text" cols="60" rows="20"><?php echo tohtml($dsatz['TxText']); ?></textarea> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
		</td>
		</tr>
		<tr>
		<td class="td1 right">Audio-URI:</td>
		<td class="td1"><input type="text" name="TxAudioURI" value="<?php echo tohtml($dsatz['TxAudioURI']); ?>" maxlength="200" size="60" /> 
		<span id="mediaselect"><?php echo selectmediapath('TxAudioURI'); ?></span>		
		</td>
		</tr>
		<tr>
		<td class="td1 right" colspan="2">
		<input type="button" value="Cancel" onclick="location.href='edit_texts.php#rec<?php echo $_REQUEST['chg']; ?>';" /> 
		<input type="submit" name="op" value="Check" />
		<input type="submit" name="op" value="Change" />
		<input type="submit" name="op" value="Change and Open" />
		</td>
		</tr>
		</table>
		</form>
		
		<?php

	}
	mysql_free_result($res);

}

// DISPLAY

else {

	echo error_message_with_hide($message,0);
	
	$sql = 'select count(*) as value from texts where (1=1) ' . (($currentlang != '') ? (' and TxLgID=' . $currentlang) : '') . (($currentquery != '') ? (' and TxTitle like ' . convert_string_to_sqlsyntax(str_replace("*","%",mb_strtolower($currentquery, 'UTF-8')))) : '');
	$recno = get_first_value($sql);
	if ($debug) echo $sql . ' ===&gt; ' . $recno;

	$maxperpage = getSettingWithDefault('set-texts-per-page');

	$pages = $recno == 0 ? 0 : (intval(($recno-1) / $maxperpage) + 1);
	
	if ($currentpage < 1) $currentpage = 1;
	if ($currentpage > $pages) $currentpage = $pages;
	$limit = 'LIMIT ' . (($currentpage-1) * $maxperpage) . ',' . $maxperpage;

	$sorts = array('TxTitle','TxID desc');
	$lsorts = count($sorts);
	if ($currentsort < 1) $currentsort = 1;
	if ($currentsort > $lsorts) $currentsort = $lsorts;
	
?>

<p>
<a href="<?php echo $_SERVER['PHP_SELF']; ?>?new=1"><img src="icn/plus-button.png" title="New" alt="New" /> New Text ...</a>
</p>

<form name="form1" action="#" onsubmit="document.form1.querybutton.click(); return false;">
<table class="tab1" cellspacing="0" cellpadding="5">
<tr>
<th class="th1" colspan="4">Filter <img src="icn/funnel.png" title="Filter" alt="Filter" />&nbsp;
<input type="button" value="Reset All" onclick="resetAll('edit_texts.php');" /></th>
</tr>
<tr>
<td class="td1 center" colspan="2">
Language:
<select name="filterlang" onchange="{setLang(document.form1.filterlang,'edit_texts.php');}"><?php	echo get_languages_selectoptions($currentlang,'[Filter off]'); ?></select>
</td>
<td class="td1 center" colspan="2">
Text Title:
<input type="text" name="query" value="<?php echo tohtml($currentquery); ?>" maxlength="50" size="15" />&nbsp;
<input type="button" name="querybutton" value="Filter" onclick="{val=document.form1.query.value; location.href='edit_texts.php?page=1&amp;query=' + val;}" />&nbsp;
<input type="button" value="Clear" onclick="{location.href='edit_texts.php?page=1&amp;query=';}" />
</td>
</tr>
<?php if($recno > 0) { ?>
<tr>
<th class="th1" colspan="1" nowrap="nowrap">
<?php echo $recno; ?> Text<?php echo ($recno==1?'':'s'); ?>
</th><th class="th1" colspan="2" nowrap="nowrap">
<?php makePager ($currentpage, $pages, 'edit_texts.php', 'form1'); ?>
</th><th class="th1" colspan="1" nowrap="nowrap">
Sort Order:
<select name="sort" onchange="{val=document.form1.sort.options[document.form1.sort.selectedIndex].value; location.href='edit_texts.php?page=1&amp;sort=' + val;}"><?php echo get_textssort_selectoptions($currentsort); ?></select>
</th></tr>
<?php } ?>
</table>
</form>

<?php
if ($recno==0) {
?>
<p>No texts found.</p>
<?php
} else {
?>
<form name="form2" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<table class="tab1" cellspacing="0" cellpadding="5">
<tr><th class="th1" colspan="2">Multi Actions <img src="icn/lightning.png" title="Multi Actions" alt="Multi Actions" /></th></tr>
<tr><td class="td1 center">
<input type="button" value="Mark All" onclick="selectToggle(true,'form2');" />
<input type="button" value="Mark None" onclick="selectToggle(false,'form2');" />
</td><td class="td1 center">
Marked Texts:&nbsp; 
<select name="markaction" id="markaction" disabled="disabled" onchange="multiActionGo(document.form2, document.form2.markaction);"><?php echo get_multipletextactions_selectoptions(); ?></select>
</td></tr></table>

<table class="sortable tab1" cellspacing="0" cellpadding="5">
<tr>
<th class="th1 sorttable_nosort">Mark</th>
<th class="th1 sorttable_nosort">Read<br />&amp;&nbsp;Test</th>
<th class="th1 sorttable_nosort">Actions</th>
<th class="th1 clickable">Lang.</th>
<th class="th1 clickable">Title / Audio?</th>
<th class="th1 sorttable_numeric clickable">Total<br />Words</th>
<th class="th1 sorttable_numeric clickable">Saved<br />Wo+Ex</th>
<th class="th1 sorttable_numeric clickable">Words<br />To Do</th>
</tr>

<?php

$sql = 'select TxID, TxTitle, LgName, TxAudioURI from texts, languages where LgID=TxLgID ' . (($currentlang != '') ? (' and TxLgID=' . $currentlang) : '') . (($currentquery != '') ? (' and TxTitle like ' . convert_string_to_sqlsyntax(str_replace("*","%",mb_strtolower($currentquery, 'UTF-8')))) : '') . ' order by ' . $sorts[$currentsort-1] . ' ' . $limit;
if ($debug) echo $sql;
$res = mysql_query($sql);		
if ($res == FALSE) die("Invalid Query: $sql");
$showCounts = getSettingWithDefault('set-show-text-word-counts')+0;
while ($dsatz = mysql_fetch_assoc($res)) {
	if ($showCounts) {
		flush();
		$txttotalwords = textwordcount($dsatz['TxID']);
		$txtworkedwords = textworkcount($dsatz['TxID']);
		$txtworkedexpr = textexprcount($dsatz['TxID']);
		$txtworkedall = $txtworkedwords + $txtworkedexpr;
		$txttodowords = $txttotalwords - $txtworkedwords;
	}
	$audio = $dsatz['TxAudioURI'];
	if(!isset($audio)) $audio='';
	$audio=trim($audio);
	echo '<tr>';
	echo '<td class="td1 center"><a name="rec' . $dsatz['TxID'] . '"><input name="marked[]" class="markcheck" type="checkbox" value="' . $dsatz['TxID'] . '" ' . checkTest($dsatz['TxID'], 'marked') . ' /></a></td>';
	echo '<td nowrap="nowrap" class="td1 center">&nbsp;<a href="do_text.php?start=' . $dsatz['TxID'] . '"><img src="icn/book-open-bookmark.png" title="Read" alt="Read" /></a>&nbsp; <a href="do_test.php?text=' . $dsatz['TxID'] . '"><img src="icn/question-balloon.png" title="Test" alt="Test" /></a>&nbsp;</td>';
	echo '<td nowrap="nowrap" class="td1 center">&nbsp;<a href="print_text.php?text=' . $dsatz['TxID'] . '"><img src="icn/printer.png" title="Print" alt="Print" /></a>&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?arch=' . $dsatz['TxID'] . '"><img src="icn/inbox-download.png" title="Archive" alt="Archive" /></a>&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?chg=' . $dsatz['TxID'] . '"><img src="icn/document--pencil.png" title="Edit" alt="Edit" /></a>&nbsp; <span class="click" onclick="if (confirm (\'Are you sure?\')) location.href=\'' . $_SERVER['PHP_SELF'] . '?del=' . $dsatz['TxID'] . '\';"><img src="icn/minus-button.png" title="Delete" alt="Delete" /></span>&nbsp;</td>';
	echo '<td class="td1 center">' . tohtml($dsatz['LgName']) . '</td>';
	echo '<td class="td1 center">' . tohtml($dsatz['TxTitle']) . ' &nbsp;' . (($audio != '') ? '<img src="icn/speaker-volume.png" title="Audio" alt="Audio" />' : '') . '</td>';
	if ($showCounts) {
		echo '<td class="td1 center"><span title="Total">&nbsp;' . $txttotalwords . '&nbsp;</span></td>'; 
		echo '<td class="td1 center"><span title="Saved" class="status4">&nbsp;' . ($txtworkedall > 0 ? '<a href="edit_words.php?page=1&amp;query=&amp;status=&amp;tag12=0&amp;tag2=&amp;tag1=&amp;text=' . $dsatz['TxID'] . '">' . $txtworkedwords . '+' . $txtworkedexpr . '</a>' : '0' ) . '&nbsp;</span></td>';
		echo '<td class="td1 center"><span title="To Do" class="status0">&nbsp;' . $txttodowords . '&nbsp;</span></td>';
	} else {
		echo '<td class="td1 center"><span id="total-' . $dsatz['TxID'] . '"></span></td><td class="td1 center"><span data_id="' . $dsatz['TxID'] . '" id="saved-' . $dsatz['TxID'] . '"><span class="click" onclick="do_ajax_word_counts();"><img src="icn/lightning.png" title="View Word Counts" alt="View Word Counts" /></span></span></td><td class="td1 center"><span id="todo-' . $dsatz['TxID'] . '"></span></td>'; 
	}
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
<?php echo $recno; ?> Text<?php echo ($recno==1?'':'s'); ?>
</th><th class="th1" nowrap="nowrap">
<?php makePager ($currentpage, $pages, 'edit_texts.php', 'form1'); ?>
</th></tr></table>
<?php 
} 

}

?>

<p><input type="button" value="Archived Texts" onclick="location.href='edit_archivedtexts.php?query=&amp;page=1';" /></p>

<?php

}

pageend();

?>