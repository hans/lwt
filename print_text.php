<?php

/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions, 
unless such conditions are required by law.

Developed by J. P. in 2011, 2012, 2013.
***************************************************************/

/**************************************************************
Call: print_text.php?text=[textid]&...
			... ann=[annotationcode] ... ann. filter 
      ... status=[statuscode] ... status filter   
Print a text
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

function output_text($saveterm,$saverom,$savetrans,$savetags,
	$show_rom,$show_trans,$show_tags,$annplcmnt) {
	if ($show_tags) {
		if ($savetrans == '' && $savetags != '') 
			$savetrans = '* ' . $savetags;
		else
			$savetrans = trim($savetrans . ' ' . $savetags);
	}
	if ($show_rom && $saverom == '') $show_rom = 0;
	if ($show_trans && $savetrans == '') $show_trans = 0;
	if ($annplcmnt == 1) {
		if ($show_rom || $show_trans) {
			echo ' ';
			if ($show_trans) 
				echo '<span class="anntrans">' . tohtml($savetrans) . '</span> ';
			if ($show_rom  && (! $show_trans)) 
				echo '<span class="annrom">' . tohtml($saverom) . '</span> ';
			if ($show_rom && $show_trans) 
				echo '<span class="annrom" dir="ltr">[' . tohtml($saverom) . ']</span> ';
			echo ' <span class="annterm">';
		}	
		echo tohtml($saveterm);
		if ($show_rom || $show_trans)
			echo '</span> ';
	} elseif ($annplcmnt == 2) {
		if ($show_rom || $show_trans) {
			echo ' <ruby><rb><span class="anntermruby">' . tohtml($saveterm) . '</span></rb><rt> ';
			if ($show_trans) 
				echo '<span class="anntransruby">' . tohtml($savetrans) . '</span> ';
			if ($show_rom  && (! $show_trans)) 
				echo '<span class="annromrubysolo">' . tohtml($saverom) . '</span> ';
			if ($show_rom && $show_trans) 
				echo '<span class="annromruby" dir="ltr">[' . tohtml($saverom) . ']</span> ';
			echo '</rt></ruby> ';
		}	else {
			echo tohtml($saveterm);
		}
	} else /* 0 or other */ {
		if ($show_rom || $show_trans)
			echo ' <span class="annterm">';
		echo tohtml($saveterm);
		if ($show_rom || $show_trans) {
			echo '</span> ';
			if ($show_rom  && (! $show_trans)) 
				echo '<span class="annrom">' . tohtml($saverom) . '</span>';
			if ($show_rom && $show_trans) 
				echo '<span class="annrom" dir="ltr">[' . tohtml($saverom) . ']</span> ';
			if ($show_trans) 
				echo '<span class="anntrans">' . tohtml($savetrans) . '</span>';
			echo ' ';
		}	
	} 
}

$textid = getreq('text')+0;
if($textid==0) {
	header("Location: edit_texts.php");
	exit();
}

$ann = getreq('ann');
if ($ann == '') $ann = getSetting('currentprintannotation');
if ($ann == '') $ann = 3;
$show_rom = $ann & 2; 
$show_trans = $ann & 1; 
$show_tags = $ann & 4; 

$status = getreq('status');
if($status == '') $status = getSetting('currentprintstatus');
if($status == '') $status = 14;
$whstatus = ' and (' . makeStatusCondition('WoStatus', $status) . ') ';

$annplcmnt = getreq('annplcmnt');
if($annplcmnt == '') $annplcmnt = getSetting('currentprintannotationplacement');
if($annplcmnt == '') $annplcmnt = 0;

$sql = 'select TxLgID, TxTitle from texts where TxID = ' . $textid;
$res = mysql_query($sql);		
if ($res == FALSE) die("Invalid Query: $sql");
$record = mysql_fetch_assoc($res);
$title = $record['TxTitle'];
$langid = $record['TxLgID'];
mysql_free_result($res);

$sql = 'select LgTextSize, LgRemoveSpaces, LgRightToLeft from languages where LgID = ' . $langid;
$res = mysql_query($sql);		
if ($res == FALSE) die("Invalid Query: $sql");
$record = mysql_fetch_assoc($res);
$textsize = $record['LgTextSize'];
$removeSpaces = $record['LgRemoveSpaces'];
$rtlScript = $record['LgRightToLeft'];
mysql_free_result($res);

saveSetting('currenttext',$textid);
saveSetting('currentprintannotation',$ann);
saveSetting('currentprintstatus',$status);
saveSetting('currentprintannotationplacement',$annplcmnt);

pagestart_nobody('Print');

echo '<div id="noprint">';

echo '<h4>';
echo '<a href="edit_texts.php" target="_top">';
echo '<img src="img/lwt_icon.png" class="lwtlogo" alt="Logo" />Learning with Texts';
echo '</a>&nbsp; | &nbsp;';
quickMenu();
echo '&nbsp; | &nbsp;<a href="do_text.php?start=' . $textid . '" target="_top"><img src="icn/book-open-bookmark.png" title="Read" alt="Read" /></a> &nbsp;<a href="do_test.php?text=' . $textid . '" target="_top"><img src="icn/question-balloon.png" title="Test" alt="Test" /></a> &nbsp;<a target="_top" href="edit_texts.php?chg=' . $textid . '"><img src="icn/document--pencil.png" title="Edit Text" alt="Edit Text" /></a>';
echo '</h4><h3>PRINT&nbsp;▶ ' . tohtml($title) . '</h3>';

echo "<p id=\"printoptions\">Terms with <b>status(es)</b> <select id=\"status\" onchange=\"{val=document.getElementById('status').options[document.getElementById('status').selectedIndex].value;location.href='print_text.php?text=" . $textid . "&amp;status=' + val;}\">";
echo get_wordstatus_selectoptions($status, true, true, false); 
echo "</select> ...<br />will be <b>annotated</b> with "; 
echo "<select id=\"ann\" onchange=\"{val=document.getElementById('ann').options[document.getElementById('ann').selectedIndex].value;location.href='print_text.php?text=" . $textid . "&amp;ann=' + val;}\">";
echo "<option value=\"0\"" . get_selected(0,$ann) . ">Nothing</option>";
echo "<option value=\"1\"" . get_selected(1,$ann) . ">Translation</option>";
echo "<option value=\"5\"" . get_selected(5,$ann) . ">Translation &amp; Tags</option>";
echo "<option value=\"2\"" . get_selected(2,$ann) . ">Romanization</option>";
echo "<option value=\"3\"" . get_selected(3,$ann) . ">Romanization &amp; Translation</option>";
echo "<option value=\"7\"" . get_selected(7,$ann) . ">Romanization, Translation &amp; Tags</option>";
echo "</select><select id=\"annplcmnt\" onchange=\"{val=document.getElementById('annplcmnt').options[document.getElementById('annplcmnt').selectedIndex].value;location.href='print_text.php?text=" . $textid . "&amp;annplcmnt=' + val;}\">";
echo "<option value=\"0\"" . get_selected(0,$annplcmnt) . ">behind</option>";
echo "<option value=\"1\"" . get_selected(1,$annplcmnt) . ">in front of</option>";
echo "<option value=\"2\"" . get_selected(2,$annplcmnt) . ">above (ruby)</option>";
echo "</select> the term.<br />";
echo "<input type=\"button\" value=\"Print it!\" onclick=\"window.print();\" />  (only the text below the line)</p></div> <!-- noprint -->";
echo "<div id=\"print\"" . ($rtlScript ? ' dir="rtl"' : '') . ">";
echo '<p style="' . ($removeSpaces ? 'word-break:break-all;' : '') . 'font-size:' . $textsize . '%;line-height: 1.35; margin-bottom: 10px; ">' . tohtml($title) . '<br /><br />';

$sql = 'select TiWordCount as Code, TiText, TiOrder, TiIsNotWord, WoID, WoTranslation, WoRomanization from (textitems left join words on (TiTextLC = WoTextLC) and (TiLgID = WoLgID) ' . $whstatus . ') where TiTxID = ' . $textid . ' and (not (TiWordCount > 1 and WoID is null)) order by TiOrder asc, TiWordCount desc';

$saveterm = '';
$savetrans = '';
$saverom = '';
$savetags = '';
$until = 0;

$res = mysql_query($sql);		
if ($res == FALSE) die("Invalid Query: $sql");

while ($record = mysql_fetch_assoc($res)) {

	$actcode = $record['Code'] + 0;
	$order = $record['TiOrder'] + 0;
	
	if ( $order <= $until ) {
		continue;
	}
	if ( $order > $until ) {
		output_text($saveterm,$saverom,$savetrans,$savetags,
			$show_rom,$show_trans,$show_tags,$annplcmnt);
		$saveterm = '';
		$savetrans = '';
		$saverom = '';
		$savetags = '';
		$until = $order;
	}
	if ($record['TiIsNotWord'] != 0) {
		echo str_replace(
			"¶",
			'</p><p style="' . ($removeSpaces ? 'word-break:break-all;' : '') . 'font-size:' . $textsize . '%;line-height: 1.3; margin-bottom: 10px;">',
			tohtml($record['TiText']));
	}
	else {
		$until = $order + 2 * ($actcode-1);                
		$saveterm = $record['TiText'];
		$savetrans = '';
		$savetags = '';
		if(isset($record['WoID'])) {
			$savetrans = $record['WoTranslation'];
			$savetags = getWordTagList($record['WoID'],'',1,0);
			if ($savetrans == '*') $savetrans = '';
		}
		$saverom = trim(isset($record['WoRomanization']) ?
			$record['WoRomanization'] : "");
	}
} // while
mysql_free_result($res);
output_text($saveterm,$saverom,$savetrans,$savetags,
	$show_rom,$show_trans,$show_tags,$annplcmnt);
echo "</p></div>";

pageend();
?>