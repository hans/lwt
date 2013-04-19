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

$textid = getreq('text')+0;
$editmode = getreq('edit')+0;
$delmode = getreq('del')+0;
$ann = get_first_value("select TxAnnotatedText as value from texts where TxID = " . $textid);
$ann_exists = (strlen($ann) > 0);
if ($ann_exists) {
	$ann = recreate_save_ann($textid, $ann);
	$ann_exists = (strlen($ann) > 0);
}

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

$sql = 'select TxLgID, TxTitle, TxAudioURI from texts where TxID = ' . $textid;
$res = mysql_query($sql);		
if ($res == FALSE) die("Invalid Query: $sql");
$record = mysql_fetch_assoc($res);
$title = $record['TxTitle'];
$langid = $record['TxLgID'];
$audio = $record['TxAudioURI'];
if(! isset($audio)) $audio='';
$audio = trim($audio);
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
	echo "<input type=\"button\" value=\"Print\" onclick=\"window.print();\" />";
	echo " &nbsp; | &nbsp; ";
	echo "<input type=\"button\" value=\"Display" . (($audio != '') ? ' with Audio Player' : '') . " in new Window\" onclick=\"window.open('display_impr_text.php?text=" . $textid . "');\" />";
}
echo "</p></div> <!-- noprint -->";

// --------------------------------------------------------

if ( $editmode ) {  // Edit Mode

	if ( ! $ann_exists ) {  // No Ann., Create...
		$ann = create_save_ann($textid);
		$ann_exists = (strlen($ann) > 0);
	}
	
	if ( ! $ann_exists ) {  // No Ann., not possible
		echo '<p>No annotated text found, and creation seems not possible.</p>';
	} else { // Ann. exists, set up for editing.
		echo "\n";
?>
<p class="smallgray3 noprint"><img id="explainlogo" src="icn/question-frame.png" title="Show explainations" alt="Show explainations" class="click" onclick="$('#explain').show(); $('#explainlogo').hide();" /> <span id="explain" style="display:none;"><b>A few explanations:</b>  Within the <i>"Improved Annotated Text - Edit Mode"</i>, you can <b>select</b> here the most suitable of the term translations by clicking on one of the <b>radio buttons</b>. To be able to do this, multiple translations must be delimited by one of the delimiters specified in the LWT <a href="settings.php">Settings</a> (currently: <?php echo tohtml(getSettingWithDefault('set-term-translation-delimiters')); ?>). You can also <b>type in a new translation</b> into the <b>text box</b> at the end (this does <b>not</b> change your saved term translation), or you may <b>change your term</b> by clicking on the <b>yellow icon</b> or add a translation by clicking on the <b>green "+" icon</b> (this <b>does</b> change your saved term translation), and select it afterwards. It's not possible to create new terms here - please do this in the read screen. Changing the language settings (e.g. the word characters) may have the effect that you have to start from scratch. So, <b>the best time for the creation</b> of an improved annotated text (an interlinear text for reading or printing) is <b>after</b> you have read the text completely and created <b>all</b> terms and expressions.<br /><b>Warning: If you change the text, you will lose the saved improved annotated text! <br />All changes you do here are saved automatically in the background!</b></span></p>
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
	echo '<div class="noprint"><input type="button" value="Display/Print Mode" onclick="location.href=\'print_impr_text.php?text=' . $textid . '\';" /></div>';

}

else {  // Print Mode

	echo "<div id=\"print\"" . ($rtlScript ? ' dir="rtl"' : '') . ">";
	
	echo '<p style="' . ($removeSpaces ? 'word-break:break-all;' : '') . 'font-size:' . $textsize . '%;line-height: 1.35; margin-bottom: 10px; ">' . tohtml($title) . '<br /><br />';
	
	$items = preg_split('/[\n]/u', $ann);
	
	foreach ($items as $item) {
		$vals = preg_split('/[\t]/u', $item);
		if ($vals[0] > -1) {
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
