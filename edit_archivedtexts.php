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
Call: edit_archivedtexts.php?....
      ... markaction=[opcode] ... do actions on marked texts
      ... del=[textid] ... do delete
      ... unarch=[textid] ... do unarchive
      ... op=Change ... do update
      ... chg=[textid] ... display edit screen 
      ... filterlang=[langid] ... language filter 
      ... sort=[sortcode] ... sort 
      ... page=[pageno] ... page  
      ... query=[titlefilter] ... title filter   
Manage archived texts
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

$currentlang = validateLang(processDBParam("filterlang",'currentlanguage','',0));
$currentsort = processDBParam("sort",'currentarchivesort','1',1);

$currentpage = processSessParam("page","currentarchivepage",'1',1);
$currentquery = processSessParam("query","currentarchivequery",'',0);

pagestart('My ' . getLanguage($currentlang) . ' Text Archive',true);

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
					$message = runsql('delete from archivedtexts where AtID in ' . $list, "Archived Texts deleted");
					adjust_autoincr('archivedtexts','AtID');
				} 

				elseif ($markaction == 'unarch') {
					$count = 0;
					$sql = "select AtID, AtLgID from archivedtexts where AtID in " . $list;
					$res = mysql_query($sql);		
					if ($res == FALSE) die("<p>Invalid query: $sql</p>");
					$num = mysql_num_rows($res);
					if ($num != 0 ) {
						while ($dsatz = mysql_fetch_assoc($res)) {
							$ida = $dsatz['AtID'];
							$message2 = runsql('insert into texts (TxLgID, TxTitle, TxText, TxAudioURI) select AtLgID, AtTitle, AtText, AtAudioURI from archivedtexts where AtID = ' . $ida, "Texts added");
							$id = get_last_key();
							splitText(
								get_first_value(
								'select TxText as value from texts where TxID = ' . $id), 
								$dsatz['AtLgID'], 
								$id );	
							$message1 = runsql('delete from archivedtexts where AtID = ' . $ida, "Archived Texts deleted");
							adjust_autoincr('archivedtexts','AtID');
							$count++;
						}
					}
					mysql_free_result($res);
					$message = 'Unarchived Text(s): ' . $count;
				} 
												
			}
		}
	}
}

// DEL

if (isset($_REQUEST['del'])) {
	$message = runsql('delete from archivedtexts where AtID = ' . $_REQUEST['del'], 
		"Archived Texts deleted");
	adjust_autoincr('archivedtexts','AtID');
}

// UNARCH

elseif (isset($_REQUEST['unarch'])) {
	$message2 = runsql('insert into texts (TxLgID, TxTitle, TxText, TxAudioURI) select AtLgID, AtTitle, AtText, AtAudioURI from archivedtexts where AtID = ' . $_REQUEST['unarch'], "Texts added");
	$id = get_last_key();
	splitText(
		get_first_value(
		'select TxText as value from texts where TxID = ' . $id), 
		get_first_value(
		'select TxLgID as value from texts where TxID = ' . $id), 
		$id );	
	$message1 = runsql('delete from archivedtexts where AtID = ' . $_REQUEST['unarch'], "Archived Texts deleted");
	$message = $message1 . " / " . $message2 . " / Sentences added: " . get_first_value('select count(*) as value from sentences where SeTxID = ' . $id) . " / Text items added: " . get_first_value('select count(*) as value from textitems where TiTxID = ' . $id);
	adjust_autoincr('archivedtexts','AtID');
}

// UPD

elseif (isset($_REQUEST['op'])) {
	
	// UPDATE
	
	if ($_REQUEST['op'] == 'Change') {
		$message = runsql('update archivedtexts set ' .
		'AtLgID = ' . $_REQUEST["AtLgID"] . ', ' .
		'AtTitle = ' . convert_string_to_sqlsyntax($_REQUEST["AtTitle"]) . ', ' .
		'AtText = ' . convert_string_to_sqlsyntax($_REQUEST["AtText"]) . ', ' .
		'AtAudioURI = ' . convert_string_to_sqlsyntax($_REQUEST["AtAudioURI"]) . ' ' .
		'where AtID = ' . $_REQUEST["AtID"], "Updated");
		$id = $_REQUEST["AtID"];
	}
	
}

// CHG

if (isset($_REQUEST['chg'])) {
	
	$sql = 'select AtLgID, AtTitle, AtText, AtAudioURI from archivedtexts where AtID = ' . $_REQUEST['chg'];
	$res = mysql_query($sql);		
	if ($res == FALSE) die("<p>Invalid query: $sql</p>");
	if ($dsatz = mysql_fetch_assoc($res)) {

		?>
	
		<h4>Edit Archived Text</h4>
		<form class="validate" action="<?php echo $_SERVER['PHP_SELF']; ?>#rec<?php echo $_REQUEST['chg']; ?>" method="post">
		<input type="hidden" name="AtID" value="<?php echo $_REQUEST['chg']; ?>" />
		<table class="tab3" cellspacing="0" cellpadding="5">
		<tr>
		<td class="td1 right">Language:</td>
		<td class="td1">
		<select name="AtLgID" class="notempty setfocus">
		<?php
		echo get_languages_selectoptions($dsatz['AtLgID'],"[Choose...]");
		?>
		</select> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
		</td>
		</tr>
		<tr>
		<td class="td1 right">Title:</td>
		<td class="td1"><input type="text" class="notempty" name="AtTitle" value="<?php echo tohtml($dsatz['AtTitle']); ?>" maxlength="200" size="60" /> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
		</tr>
		<tr>
		<td class="td1 right">Text:</td>
		<td class="td1">
		<textarea name="AtText" class="notempty" cols="60" rows="20"><?php echo tohtml($dsatz['AtText']); ?></textarea> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
		</td>
		</tr>
		<tr>
		<td class="td1 right">Audio-URI:</td>
		<td class="td1"><input type="text" name="AtAudioURI" value="<?php echo tohtml($dsatz['AtAudioURI']); ?>" maxlength="200" size="60" />
		<?php
		echo selectmediapath('AtAudioURI');
		?>
		</td>
		</tr>
		<tr>
		<td class="td1 right" colspan="2">
		<input type="button" value="Cancel" onclick="location.href='edit_archivedtexts.php#rec<?php echo $_REQUEST['chg']; ?>';" /> 
		<input type="submit" name="op" value="Change" /></td>
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

	$recno = get_first_value('select count(*) as value from archivedtexts where (1=1) ' . (($currentlang != '') ? (' and AtLgID=' . $currentlang) : '') . (($currentquery != '') ? (' and AtTitle like ' . convert_string_to_sqlsyntax(str_replace("*","%",mb_strtolower($currentquery, 'UTF-8')))) : ''));

	$maxperpage = getSettingWithDefault('set-archivedtexts-per-page');

	$pages = $recno == 0 ? 0 : (intval(($recno-1) / $maxperpage) + 1);
	
	if ($currentpage < 1) $currentpage = 1;
	if ($currentpage > $pages) $currentpage = $pages;
	$limit = 'LIMIT ' . (($currentpage-1) * $maxperpage) . ',' . $maxperpage;

	$sorts = array('AtTitle','AtID desc');
	$lsorts = count($sorts);
	if ($currentsort < 1) $currentsort = 1;
	if ($currentsort > $lsorts) $currentsort = $lsorts;
	
?>

<form name="form1" action="#" onsubmit="document.form1.querybutton.click(); return false;">
<table class="tab1" cellspacing="0" cellpadding="5">
<tr>
<th class="th1" colspan="4">Filter <img src="icn/funnel.png" title="Filter" alt="Filter" /></th>
</tr>
<tr>
<td class="td1 center" colspan="2">
Filter Language:
<select name="filterlang" onchange="{setLang(document.form1.filterlang,'edit_archivedtexts.php');}"><?php	echo get_languages_selectoptions($currentlang,'[Filter off]'); ?></select>
</td>
<td class="td1 center" colspan="2">
Filter Title:
<input type="text" name="query" value="<?php echo tohtml($currentquery); ?>" maxlength="50" size="15" />&nbsp;
<input type="button" name="querybutton" value="Filter" onclick="{val=document.form1.query.value; location.href='edit_archivedtexts.php?page=1&amp;query=' + val;}" />&nbsp;
<input type="button" value="Clear" onclick="{location.href='edit_archivedtexts.php?page=1&amp;query=';}" />
</td>
</tr>
<?php if($recno > 0) { ?>
<tr>
<th class="th1" nowrap="nowrap">
<?php echo $recno; ?> Text<?php echo ($recno==1?'':'s'); ?>
</th>
<th class="th1" colspan="2" nowrap="nowrap">
<?php makePager ($currentpage, $pages, 'edit_archivedtexts.php', 'form1'); ?>
</th>
<th class="th1" nowrap="nowrap">
Sort Order:
<select name="sort" onchange="{val=document.form1.sort.options[document.form1.sort.selectedIndex].value; location.href='edit_archivedtexts.php?page=1&amp;sort=' + val;}"><?php echo get_textssort_selectoptions($currentsort); ?></select>
</th></tr>
<?php } ?>
</table>
</form>

<?php
if ($recno==0) {
?>
<p>No archived texts found.</p>
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
<select name="markaction" id="markaction" disabled="disabled" onchange="multiActionGo(document.form2, document.form2.markaction);"><?php echo get_multiplearchivedtextactions_selectoptions(); ?></select>
</td></tr></table>

<table class="sortable tab1" cellspacing="0" cellpadding="5">
<tr>
<th class="th1 sorttable_nosort">Mark</th>
<th class="th1 sorttable_nosort">Actions</th>
<th class="th1 clickable">Lang.</th>
<th class="th1 clickable">Title / Audio?</th>
</tr>

<?php

$sql = 'select AtID, AtTitle, LgName, AtAudioURI from archivedtexts, languages where LgID=AtLgID ' . (($currentlang != '') ? (' and AtLgID=' . $currentlang) : '') . (($currentquery != '') ? (' and AtTitle like ' . convert_string_to_sqlsyntax(str_replace("*","%",mb_strtolower($currentquery, 'UTF-8')))) : '') . ' order by ' . $sorts[$currentsort-1] . ' ' . $limit;

$res = mysql_query($sql);		
if ($res == FALSE) die("<p>Invalid query: $sql</p>");
while ($dsatz = mysql_fetch_assoc($res)) {
	echo '<tr>';
	echo '<td class="td1 center"><a name="rec' . $dsatz['AtID'] . '"><input name="marked[]" class="markcheck"  type="checkbox" value="' . $dsatz['AtID'] . '" ' . checkTest($dsatz['AtID'], 'marked') . ' /></a></td>';
	echo '<td nowrap="nowrap" class="td1 center">&nbsp;<a href="' . $_SERVER['PHP_SELF'] . '?unarch=' . $dsatz['AtID'] . '"><img src="icn/inbox-upload.png" title="Unarchive" alt="Unarchive" /></a>&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?chg=' . $dsatz['AtID'] . '"><img src="icn/document--pencil.png" title="Edit" alt="Edit" /></a>&nbsp; <span class="click" onclick="if (confirm (\'Are you sure?\')) location.href=\'' . $_SERVER['PHP_SELF'] . '?del=' . $dsatz['AtID'] . '\';"><img src="icn/minus-button.png" title="Delete" alt="Delete" /></span>&nbsp;</td>';
	echo '<td class="td1 center">' . tohtml($dsatz['LgName']) . '</td>';
	echo '<td class="td1 center">' . tohtml($dsatz['AtTitle']) . ' &nbsp;'  . (isset($dsatz['AtAudioURI']) ? '<img src="icn/speaker-volume.png" title="Audio" alt="Audio" />' : '') . '</td>';
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
<?php makePager ($currentpage, $pages, 'edit_archivedtexts.php', 'form1'); ?>
</th></tr></table>
<?php } ?>

<?php

}

?>

<p><input type="button" value="Active Texts" onclick="location.href='edit_texts.php?query=&amp;page=1';" /></p>

<?php

}

pageend();

?>