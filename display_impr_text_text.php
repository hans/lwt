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
Call: display_impr_text_text.php?text=[textid]
Display an improved annotated text (text frame)
***************************************************************/

include "settings.inc.php";
include "connect.inc.php";
include "utilities.inc.php";

$textid = getreq('text')+0;
$ann = get_first_value("select TxAnnotatedText as value from ' . $tbpref . 'texts where TxID = " . $textid);
$ann_exists = (strlen($ann) > 0);

if(($textid==0) || (! $ann_exists)) {
	header("Location: edit_texts.php");
	exit();
}

$sql = 'select TxLgID, TxTitle from ' . $tbpref . 'texts where TxID = ' . $textid;
$res = mysql_query($sql);		
if ($res == FALSE) die("Invalid Query: $sql");
$record = mysql_fetch_assoc($res);
$title = $record['TxTitle'];
$langid = $record['TxLgID'];
mysql_free_result($res);

$sql = 'select LgTextSize, LgRemoveSpaces, LgRightToLeft from ' . $tbpref . 'languages where LgID = ' . $langid;
$res = mysql_query($sql);		
if ($res == FALSE) die("Invalid Query: $sql");
$record = mysql_fetch_assoc($res);
$textsize = $record['LgTextSize'];
$removeSpaces = $record['LgRemoveSpaces'];
$rtlScript = $record['LgRightToLeft'];
mysql_free_result($res);

saveSetting('currenttext',$textid);

pagestart_nobody('Display');

?>
<script type="text/javascript">
//<![CDATA[

function click_ann() {
	if($(this).css('color') == 'rgb(200, 220, 240)') {
		$(this).css('color','#006699');
		$(this).css('background-color','white');
	}
	else {
		$(this).css('color','#C8DCF0');
		$(this).css('background-color','#C8DCF0');
	}
}

function click_text() {
	if($(this).css('color') == 'rgb(229, 228, 226)') {
		$(this).css('color','black');
		$(this).css('background-color','white');
	}
	else {
		$(this).css('color','#E5E4E2');
		$(this).css('background-color','#E5E4E2');
	}
}

$(document).ready(function(){
  $('.anntransruby2').click(click_ann);
  $('.anntermruby').click(click_text);
});
//]]>
</script>

<?php

echo "<div id=\"print\"" . ($rtlScript ? ' dir="rtl"' : '') . ">";

echo '<p style="' . ($removeSpaces ? 'word-break:break-all;' : '') . 'font-size:' . $textsize . '%;line-height: 1.35; margin-bottom: 10px; ">';

$items = preg_split('/[\n]/u', $ann);

foreach ($items as $item) {
	$vals = preg_split('/[\t]/u', $item);
	if ($vals[0] > -1) {
		$trans = '';
		$c = count($vals);
		$rom = '';
		if ($c > 2) {
			$wid = $vals[2] + 0;
			$rom = get_first_value("select WoRomanization as value from " . $tbpref . "words where WoID = " . $wid);
			if (! isset($rom)) $rom = '';
		}
		if ($c > 3) $trans = $vals[3];
		if ($trans == '*') $trans = $vals[1];
		echo ' <ruby><rb><span class="click anntermruby" style="color:black;"' . ($rom == '' ? '' : (' title="' . tohtml($rom) . '"')) . '>' . tohtml($vals[1]) . '</span></rb><rt><span class="click anntransruby2">' . tohtml($trans) . '</span></rt></ruby> ';
	} else {
		echo str_replace(
		"¶",
		'</p><p style="' . ($removeSpaces ? 'word-break:break-all;' : '') . 'font-size:' . $textsize . '%;line-height: 1.3; margin-bottom: 10px;">',
		" " . tohtml($vals[1]));
	}
}

echo "</p></div>";

pageend();

?>
