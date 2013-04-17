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
			... edit=1 ... edit own annotation 
			... del=1  ... delete own annotation 
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
	$arr = preg_split('/[' . get_sepas()  . ']/u', $trans);
	if (count($arr) < 1) return '';
	$r = trim($arr[0]);
	if ($r == '*') $r ="";
	return $r;
}

$textid = getreq('text')+0;
$editmode = getreq('edit')+0;
$delmode = getreq('del')+0;
$savemode = getreq('op') . '';
$ann = get_first_value("select TxAnnotatedText as value from texts where TxID = " . $textid);
$ann_exists = (strlen($ann) > 0);

if($textid==0) {
	header("Location: edit_texts.php");
	exit();
}

if ( $delmode ) {  // Delete
	if ( $ann_exists ) $dummy = runsql('update texts set ' .
			'TxAnnotatedText = ' . convert_string_to_sqlsyntax("") . ' where TxID = ' . $textid, "");
	$ann_exists = ((get_first_value("select length(TxAnnotatedText) as value from texts where TxID = " . $textid) + 0) > 0);
	if ( ! $ann_exists ) {
		header("Location: print_text.php?text=" . $textid);
		exit();
	}
}

if ($savemode == "Save") {
		// Save data and print
		$items = preg_split('/[\n]/u', $ann);
		$i = 0;
		foreach ($items as $item) {
			$i++;
			$vals = preg_split('/[\t]/u', $item);
			if ($vals[0] == 1) {
				$newtran = "";
				if(isset($_REQUEST['rg'][$i])) {
					$newtran = $_REQUEST['rg'][$i];
				} 
				if(trim($newtran) == "" && isset($_REQUEST['tx'][$i])) {
						$newtran = $_REQUEST['tx'][$i];
				}
				$c = count($vals);
				if($c == 2) {
					$vals[2] = ''; $vals[3] = $newtran;
				} elseif ($c > 2) {
					$vals[3] = $newtran;
				} 
				$items[$i-1] = implode("\t",$vals);
			}
		}
		$dummy = runsql('update texts set ' .
			'TxAnnotatedText = ' . convert_string_to_sqlsyntax(implode("\n",$items)) . ' where TxID = ' . $textid, "");
		header("Location: print_impr_text.php?text=" . $textid);
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

echo '<div class="noprint">';

echo '<h4>';
echo '<a href="edit_texts.php" target="_top">';
echo '<img src="img/lwt_icon.png" class="lwtlogo" alt="Logo" />Learning with Texts';
echo '</a>&nbsp; | &nbsp;';
quickMenu();
echo '&nbsp; | &nbsp;<a href="do_text.php?start=' . $textid . '" target="_top"><img src="icn/book-open-bookmark.png" title="Read" alt="Read" /></a> &nbsp;<a href="do_test.php?text=' . $textid . '" target="_top"><img src="icn/question-balloon.png" title="Test" alt="Test" /></a> &nbsp;<a href="print_text.php?text=' . $textid . '" target="_top"><img src="icn/printer.png" title="Print" alt="Print" /> &nbsp;<a target="_top" href="edit_texts.php?chg=' . $textid . '"><img src="icn/document--pencil.png" title="Edit Text" alt="Edit Text" /></a>';
echo '</h4><h3>PRINT&nbsp;▶ ' . tohtml($title) . '</h3>';

echo "<p id=\"printoptions\"><b>Improved Annotated Text";

if($editmode) {
	echo " (Edit Mode)</b><br /><input type=\"button\" value=\"Display/Print Mode\" onclick=\"location.href='print_impr_text.php?text=" . $textid . "';\" />\n";
} else {
	echo " (Display/Print Mode)</b><br /><input type=\"button\" value=\"Edit\" onclick=\"location.href='print_impr_text.php?edit=1&amp;text=" . $textid . "';\" />";
	echo " &nbsp; | &nbsp; ";
	echo "<input type=\"button\" value=\"Delete\" onclick=\"if (confirm ('Are you sure?')) location.href='print_impr_text.php?del=1&amp;text=" . $textid . "';\" /> ";
	echo " &nbsp; | &nbsp; ";
	echo "<input type=\"button\" value=\"Print\" onclick=\"window.print();\" />  (only the text below the line)";
}
echo "</p></div> <!-- noprint -->";

// --------------------------------------------------------

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
			
		$ann_exists = (strlen($ann) > 0);
		
	}
	
	if ( ! $ann_exists ) {  // No Ann., not possible
	
		echo '<p>No annotated text found, and creation not possible.</p>';
	
	} else { // Ann. exists, set up for editing.
	
		echo "\n";
?>
<p class="smallgray3 noprint"><img id="explainlogo" src="icn/question-frame.png" title="Show explainations" alt="Show explainations" class="click" onclick="$('#explain').show(); $('#explainlogo').hide();" /> <span id="explain" style="display:none;"><b>A few explanations:</b>  Within the <i>"Improved Annotated Text - Edit Mode"</i>, you can <b>select</b> the most suitable of the term translations by clicking on one of the <b>radio buttons</b>. To be able to do this, multiple translations must be delimited by one of the delimiters specified in the LWT <a href="settings.php">Settings</a> (currently: <?php echo tohtml(getSettingWithDefault('set-term-translation-delimiters')); ?>). You can also <b>type in a new translation</b> into the <b>text box</b> at the end (this does <b>not</b> change your saved term translation!), or you may <b>change your term</b> by clicking on the <b>yellow icon</b> or add a translation by clicking on the <b>green "+" icon</b>, and select it. It's not possible to create new terms here - new terms will have no effect unless you start from scratch. Changing the language settings (e.g. the word characters) has no effect unless you start from scratch. So, <b>the best time for the creation</b> of an improved annotated text (an interlinear text for reading or printing) is <b>after</b> you have read the text completely and created <b>all</b> terms and expressions.<br /><b>Warning: If you change the text, you will lose the saved improved annotated text! <br />All changes you do here are saved automatically in the background!</b></span></p>
<?php
		echo '<div data_id="' . $textid . '" id="editimprtextdata"></div>';
		echo "\n";
?>
	<script type="text/javascript">
	//<![CDATA[
	$(document).ready( function() {
	do_ajax_edit_impr_text(0);
	} ); 
	//]]>
	</script>
<?php
		
	}

?>
	
<?php	
	echo '<div class="noprint"><input type="button" value="Display/Print Mode" onclick="location.href=\'print_impr_text.php?text=' . $textid . '\';" /></div>';

}

else {  // Print Mode

	echo "<div id=\"print\"" . ($rtlScript ? ' dir="rtl"' : '') . ">";
	
	echo '<p style="' . ($removeSpaces ? 'word-break:break-all;' : '') . 'font-size:' . $textsize . '%;line-height: 1.35; margin-bottom: 10px; ">' . tohtml($title) . '<br /><br />';
	
	$items = preg_split('/[\n]/u', $ann);
	
	foreach ($items as $item) {
		$vals = preg_split('/[\t]/u', $item);
		if ($vals[0] == 1) {
			$trans = '';
			if (count($vals) > 3) $trans = $vals[3];
			if ($trans == '*') $trans = $vals[1];
			echo ' <ruby><rb><span class="anntermruby">' . tohtml($vals[1]) . '</span></rb><rt><span class="anntransruby2">' . tohtml($trans) . '</span></rt></ruby> ';
		} else {
			echo str_replace(
			"¶",
			'</p><p style="' . ($removeSpaces ? 'word-break:break-all;' : '') . 'font-size:' . $textsize . '%;line-height: 1.3; margin-bottom: 10px;">',
			" " . tohtml($vals[1]) . " ");
		}
	}
	
	echo "</p></div>";

}

pageend();

?>
