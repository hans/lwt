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
Call: print_impr_text.php?text=[textid]&...
			... edit=1 ... edit mode 
Print/Edit an improved annotated text
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

function process_term($nonterm, $term, $trans, $wordid) {
	$r = '';
	if ($nonterm != '') $r = $r . "0\t" . $nonterm . "\n";
	if ($term != '') $r = $r . "1\t" . $term . "\t" . trim($wordid) . "\t" . get_first_translation($trans) . "\n";
	return $r;
}

function get_first_translation($trans) {
	static $sepa;
	if (!$sepa) {
		$sepa = preg_quote(getSettingWithDefault('set-term-translation-delimiters'),'/');
	}
	$arr = preg_split('/[' . $sepa  . ']/u', $trans);
	if (count($arr) < 1) return '';
	return trim($arr[0]);
}

$textid = getreq('text')+0;
$editmode = getreq('edit')+0;
$ann_exists = ((get_first_value("select length(TxAnnotatedText) as value from texts where TxID = " . $textid) + 0) > 0);

if($textid==0) {
	header("Location: edit_texts.php");
	exit();
}

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

pagestart_nobody('Print');

echo '<div id="noprint">';

echo '<h4>';
echo '<a href="edit_texts.php" target="_top">';
echo '<img src="img/lwt_icon.png" class="lwtlogo" alt="Logo" />Learning with Texts';
echo '</a>&nbsp; | &nbsp;';
quickMenu();
echo '&nbsp; | &nbsp;<a href="do_text.php?start=' . $textid . '" target="_top"><img src="icn/book-open-bookmark.png" title="Read" alt="Read" /></a> &nbsp;<a href="do_test.php?text=' . $textid . '" target="_top"><img src="icn/question-balloon.png" title="Test" alt="Test" /></a> &nbsp;<a href="print_text.php?text=' . $textid . '" target="_top"><img src="icn/printer.png" title="Print" alt="Print" /> &nbsp;<a target="_top" href="edit_texts.php?chg=' . $textid . '"><img src="icn/document--pencil.png" title="Edit Text" alt="Edit Text" /></a>';
echo '</h4><h3>PRINT&nbsp;▶ ' . tohtml($title) . '</h3>';

echo "<p id=\"printoptions\">";
if($editmode) {
	echo "<input type=\"button\" value=\"Finish Editing and Display/Print...\" onclick=\"location.href='print_impr_text.php?text=" . $textid . "';\" />";
} else {
	echo "<input type=\"button\" value=\"Edit Annotations...\" onclick=\"location.href='print_impr_text.php?edit=1&amp;text=" . $textid . "';\" /> &nbsp; | &nbsp; ";
	echo "<input type=\"button\" value=\"Print\" onclick=\"window.print();\" />  (only the text below the line)";
}
echo "</p></div> <!-- noprint -->";

if ( $editmode ) {  // Edit Mode

	if ( ! $ann_exists ) {  // No Ann., Create...
	
		$ann = '';
	
		$sql = 'select TiWordCount as Code, TiText, TiOrder, TiIsNotWord, WoID, WoTranslation from (textitems left join words on (TiTextLC = WoTextLC) and (TiLgID = WoLgID)) where TiTxID = ' . $textid . ' and (not (TiWordCount > 1 and WoID is null)) order by TiOrder asc, TiWordCount desc';
		
		$savenonterm = '';
		$saveterm = '';
		$savetrans = '';
		$savewordid = '';
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
				$ann = $ann . process_term($savenonterm, $saveterm, $savetrans, $savewordid);
				$savenonterm = '';
				$saveterm = '';
				$savetrans = '';
				$savewordid = '';
				$until = $order;
			}
			if ($record['TiIsNotWord'] != 0) {
				$savenonterm = $savenonterm . $record['TiText'];
			}
			else {
				$until = $order + 2 * ($actcode-1);                
				$saveterm = $record['TiText'];
				$savetrans = '';
				if(isset($record['WoID'])) {
					$savetrans = $record['WoTranslation'];
					$savewordid = $record['WoID'];
				}
			}
		} // while
		mysql_free_result($res);
		$ann = $ann . process_term($savenonterm, $saveterm, $savetrans, $savewordid);
		
		$dummy = runsql('update texts set ' .
			'TxAnnotatedText = ' . convert_string_to_sqlsyntax($ann) . ' where TxID = ' . $textid, "");
			
		$ann_exists = ((get_first_value("select length(TxAnnotatedText) as value from texts where TxID = " . $textid) + 0) > 0);
		
	}
	
	if ( ! $ann_exists ) {  // No Ann., not possible
	
		echo "<p>No Annotation found, and creation not possible.</p>";
	
	} else { // Ann. exists, set up for editing.
	
	
	}

}

else {  // Print Mode

	echo "<div id=\"print\"" . ($rtlScript ? ' dir="rtl"' : '') . ">";
	
	echo '<p style="' . ($removeSpaces ? 'word-break:break-all;' : '') . 'font-size:' . $textsize . '%;line-height: 1.35; margin-bottom: 10px; ">' . tohtml($title) . '<br /><br />';

	$ann = get_first_value("select TxAnnotatedText as value from texts where TxID = " . $textid);
	
	$items = preg_split('/[\n]/u', $ann);
	
	foreach ($items as $item) {
		$vals = preg_split('/[\t]/u', $item);
		if ($vals[0] == 1) {
			$trans = '';
			if (count($vals) > 3) $trans = $vals[3];
			if ($trans == '*') $trans = '[' . $vals[1] . ']';
			echo '<ruby><rb><span class="anntermruby">' . tohtml($vals[1]) . '</span></rb><rt><span class="anntransruby">' . tohtml($trans) . '</span></rt></ruby> ';
		} else {
			echo str_replace(
			"¶",
			'</p><p style="' . ($removeSpaces ? 'word-break:break-all;' : '') . 'font-size:' . $textsize . '%;line-height: 1.3; margin-bottom: 10px;">',
			tohtml($vals[1]));
		}
	}
	
	echo "</p></div>";

}

pageend();

?>
