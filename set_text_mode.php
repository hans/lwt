<?php

/**
 * \file
 * \brief Change the text display mode
 * 
 * Call: set_text_mode.php?text=[textid]&mode=0/1&showLeaning=0/1
 * 
 * @author https://sourceforge.net/projects/lwt/ LWT Project
 * @since 1.0.3.1
 */

require_once 'inc/session_utility.php';

$tid = getreq('text') + 0;
$showAll = getreq('mode') + 0;
saveSetting('showallwords', $showAll);
$previousShowLearning = getSettingZeroOrOne('showlearningtranslations', 1);
$showLearning = getreq('showLearning');
saveSetting('showlearningtranslations', $showLearning);

pagestart("Text Display Mode changed", false);

echo '<p><span id="waiting"><img src="'.get_file_path('icn/waiting.gif').'" alt="Please wait" title="Please wait" />&nbsp;&nbsp;Please wait ...</span>';
flush();
?>

<script type="text/javascript">
//<![CDATA[
var method = <?php echo ($showLearning != $previousShowLearning ? '1' : '0'); ?>;  // 0 (jquery) or 1 (reload)
if (method) {
	window.parent.frames['l'].location.reload();
} else {
	var context = window.parent.frames['l'].document;
<?php
/* 
(jquery, deact.)

$sql = 'select TiWordCount as Code, TiText, TiOrder, TiIsNotWord, WoID from (' . $tbpref . 'textitems left join ' . $tbpref . 'words on (TiTextLC = WoTextLC) and (TiLgID = WoLgID)) where TiTxID = ' . $tid . ' order by TiOrder asc, TiWordCount desc';

$res = do_mysqli_query($sql);
$hideuntil = -1;
$hidetag = "removeClass('hide');";

while ($record = mysqli_fetch_assoc($res)) {  // MAIN LOOP
	$actcode = $record['Code'] + 0;
	$t = $record['TiText'];
	$order = $record['TiOrder'] + 0;
	$notword = $record['TiIsNotWord'] + 0;
	$termex = isset($record['WoID']);
	$spanid = 'ID-' . $order . '-' . $actcode;

	if ( $hideuntil > 0 ) {
		if ( $order <= $hideuntil )
			$hidetag = "addClass('hide');";
		else {
			$hideuntil = -1;
			$hidetag = "removeClass('hide');";
		}
	}
	
	if ($notword != 0) {  // NOT A TERM
		echo "$('#" . $spanid . "',context)." . $hidetag . "\n";
	}  
	
	else {   // A TERM
		if ($actcode > 1) {   // A MULTIWORD FOUND
			if ($termex) {  // MULTIWORD FOUND - DISPLAY
				if (! $showAll) {
					if ($hideuntil == -1) {
						$hideuntil = $order + ($actcode - 1) * 2;
					}
				}
				echo "$('#" . $spanid . "',context)." .
					($showAll ? ("html('&nbsp;" . $actcode . "&nbsp;')") : ('text(' . prepare_textdata_js($t) . ')')) .
					".removeClass('mwsty wsty').addClass('" .
					($showAll ? 'mwsty' : 'wsty') . "')." . 
					$hidetag . "\n";
			}
			else {  // MULTIWORD PLACEHOLDER - NO DISPLAY 
				echo "$('#" . $spanid . "',context)." .
					($showAll ? ("html('&nbsp;" . $actcode . "&nbsp;')") : ('text(' . prepare_textdata_js($t) . ')')) .
					".removeClass('mwsty wsty').addClass('" .
					($showAll ? 'mwsty' : 'wsty') . " hide');\n";
			}  
		} // ($actcode > 1) -- A MULTIWORD FOUND
		else {  // ($actcode == 1)  -- A WORD FOUND
			echo "$('#" . $spanid . "',context)." . $hidetag . "\n";
		}  // ($actcode == 1)  -- A WORD FOUND
	} // $record['TiIsNotWord'] == 0  -- A TERM
} // while ($record = mysqli_fetch_assoc($res))  -- MAIN LOOP
mysqli_free_result($res);

(jquery, deact.) 
*/
?>
}
$('#waiting').html('<b>OK -- </b>');
//]]>
</script>

<?php

if ($showAll == 1) {
    echo '<b><i>Show All</i></b> is set to <b>ON</b>.<br /><br />ALL terms are now shown, and all multi-word terms are shown as superscripts before the first word. The superscript indicates the number of words in the multi-word term.<br /><br />To concentrate more on the multi-word terms and to display them without superscript, set <i>Show All</i> to OFF.</p>'; 
} else {
    echo '<b><i>Show All</i></b> is set to <b>OFF</b>.<br /><br />Multi-word terms now hide single words and shorter or overlapping multi-word terms. The creation and deletion of multi-word terms can be a bit slow in long texts.<br /><br />To  manipulate ALL terms, set <i>Show All</i> to ON.</p>'; 
}

echo "<br /><br />";

if ($showLearning == 1) {
    echo '<b><i>Learning Translations</i></b> is set to <b>ON</b>.<br /><br />Terms that have Learning Level&nbsp;1 will show their translations beneath the term in the reading mode.<br /><br />To hide the translations, set <i>Learning Translations</i> to OFF.</p>'; 
} else {
    echo '<b><i>Learning Translations</i></b> is set to <b>OFF</b>.<br /><br />No translations will be shown directly in the reading window.<br /><br />To see translations for terms with Learning Level&nbsp;1 underneath the terms in the reading window, set <i>Learning Translations</i> to ON.</p>'; 
}

pageend();

?>
