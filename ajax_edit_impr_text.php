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
Call: ajax_edit_impr_text.php?id=[textid]
Display table for Improved Annotation (Edit Mode), 
Ajax call in print_impr_text.php
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

function make_trans($i, $wid, $trans) {
	$trans = trim($trans);
	if (is_numeric($wid)) {
		$alltrans = get_first_value("select WoTranslation as value from words where WoID = " . $wid);
		$transarr = preg_split('/[' . get_sepas()  . ']/u', $alltrans);
		$r = "";
		$set = false;
		foreach ($transarr as $t) {
			$tt = trim($t);
			if (($tt == '*') || ($tt == '')) continue;
			if ((! $set) && ($tt == $trans)) {
				$set = true;
				$r .= '<input class="impr-ann-radio" checked="checked" type="radio" name="rg' . $i . '" value="' . tohtml($tt) . '" />&nbsp;' . tohtml($tt) . ' &nbsp; ';
			} else {
				$r .= '<input class="impr-ann-radio" type="radio" name="rg' . $i . '" value="' . tohtml($tt) . '" />&nbsp;' . tohtml($tt) . ' &nbsp; ';
			}
		}
		if (! $set) {
			$r .= '<input class="impr-ann-radio" checked="checked" type="radio" name="rg' . $i . '" value="" />&nbsp;<input class="impr-ann-text" type="text" name="tx' . $i . '" value="' . tohtml($trans) . '" />';
		} else {
			$r .= '<input class="impr-ann-radio" type="radio" name="rg' . $i . '" value="" />&nbsp;<input class="impr-ann-text" type="text" name="tx' . $i . '" value="" />';
		}
		return $r;
	}
	return '<input checked="checked" type="radio" name="rg' . $i . '" value="" />&nbsp;<input class="impr-ann-text" type="text" name="tx' . $i . '" value="' . tohtml($trans) . '" />';
}

$textid = $_REQUEST["id"] + 0;

$sql = 'select TxLgID, TxTitle from texts where TxID = ' . $textid;
$res = mysql_query($sql);		
if ($res == FALSE) die("Invalid Query: $sql");
$record = mysql_fetch_assoc($res);
$title = $record['TxTitle'];
$langid = $record['TxLgID'];
mysql_free_result($res);

$ann = get_first_value("select TxAnnotatedText as value from texts where TxID = " . $textid);
$ann_exists = (strlen($ann) > 0);
$r = '<form action="" method="post"><table class="tab1" cellspacing="0" cellpadding="5"><tr>';
$r .= '<th class="th1 center">Non-Term</th>';
$r .= '<th class="th1 center">Term</th>';
$r .= '<th class="th1 center">Term Translations (Delim.: ' . tohtml(getSettingWithDefault('set-term-translation-delimiters')) . ')<br /><input type="button" value="Reload" onclick="do_ajax_edit_impr_text(\'\');" /></th>';
$r .= '<th class="th1 center">Edit<br />Term</th>';
$r .= '<th class="th1 center">Dict</th>';
$r .= '</tr>';
$nonterms = "";
$items = preg_split('/[\n]/u', $ann);
$i = 0;
foreach ($items as $item) {
	$i++;
	$vals = preg_split('/[\t]/u', $item);
	if ($vals[0] == 1) {
		$id = '';
		$trans = '';
		if (count($vals) > 2) $id = $vals[2];
		if (count($vals) > 3) $trans = $vals[3];
		$r .= '<tr><td class="td1 center">';
		if(trim($nonterms) != "") 
			$r .= str_replace("¶", '<img src="icn/new_line.png" title="New Line" alt="New Line" />', tohtml($nonterms)); 
		else 
			$r .= '&nbsp;';
		$r .= '</td><td class="td1 center"><span id="term' . $i . '">';
		$r .= tohtml($vals[1]);
		$r .= '</span></td><td class="td1">';
		$r .= make_trans($i, $id, $trans);
		$r .= '</td><td class="td1 center">';
		if ($id == '') {
			$r .= '&nbsp;';
		} else {
			$r .= '<a name="rec' . $i . '"></a><span class="click" onclick="oewin(\'edit_word.php?fromAnn=' . $i . '&amp;wid=' . $id . '\');"><img src="icn/sticky-note--pencil.png" title="Edit Term" alt="Edit Term" /></span>';
		}
		$r .= '</td><td class="td1 center" nowrap="nowrap">';
		$r .= makeDictLinks($langid,prepare_textdata_js($vals[1]));
		$r .= '</td></tr>';
		$nonterms = "";
	} else {
		$nonterms .= $vals[1];
	}
}
if ($nonterms != "") {
	$r .= '<tr><td class="td1 center">';
	if(trim($nonterms) != "") 
		$r .= str_replace("¶", '<img src="icn/new_line.png" title="New Line" alt="New Line" />', tohtml($nonterms)); 
	else 
		$r .= '&nbsp;';
	$r .= '</td><td class="td1 center">&nbsp;</td><td class="td1">&nbsp;</td><td class="td1">&nbsp;</td><td class="td1">&nbsp;</td></tr>';
}
$r .= '<tr><th class="th1 center">Non-Term</th>';
$r .= '<th class="th1 center">Term</th>';
$r .= '<th class="th1 center">Term Translations (Delim.: ' . tohtml(getSettingWithDefault('set-term-translation-delimiters')) . ')<br /><input type="button" value="Reload" onclick="do_ajax_edit_impr_text(\'bottom\');" /><a name="bottom"></a></th>';
$r .= '<th class="th1 center">Edit<br />Term</th>';
$r .= '<th class="th1 center">Dict</th>';
$r .= '</tr></table></form>' . "\n";
$r .= '<script type="text/javascript">' . "\n";
$r .= '//<![CDATA[' . "\n";
$r .= '$(document).ready( function() {' . "\n";
$r .= "$('input.impr-ann-text').change(changeImprAnnText);\n";
$r .= "$('input.impr-ann-radio').change(changeImprAnnRadio);\n";
$r .= '} );' . "\n";
$r .= '//]]>' . "\n";
$r .= '</script>' . "\n";
echo $r;

?>