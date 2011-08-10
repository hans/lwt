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
Call: set_text_mode.php?text=[textid]&mode=0/1
Change the text display mode
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php"; 

$tid = getreq('text') + 0;
$showAll = getreq('mode') + 0;
saveSetting('showallwords',$showAll);

pagestart("Text Display Mode changed", false);

echo '<p><span id="waiting"><img src="icn/waiting.gif" alt="Please wait" title="Please wait" />&nbsp;&nbsp;Please wait ...</span>';
flush();
?>

<script type="text/javascript">
//<![CDATA[
var method = 1;  // 0 (jquery, deactivated, too slow) or 1 (reload) 
if (method) window.parent.frames['l'].location.reload();
else {
var context = window.parent.frames['l'].document;
<?php
/**************************************************************
(jquery, deact.)

$sql = 'select TiWordCount as Code, TiText, TiOrder, TiIsNotWord, WoID from (textitems left join words on (TiTextLC = WoTextLC) and (TiLgID = WoLgID)) where TiTxID = ' . $tid . ' order by TiOrder asc, TiWordCount desc';

$res = mysql_query($sql);		
if ($res == FALSE) die("Invalid Query: $sql");
$hideuntil = -1;
$hidetag = "removeClass('hide');";

while ($dsatz = mysql_fetch_assoc($res)) {  // MAIN LOOP
	$actcode = $dsatz['Code'] + 0;
	$t = $dsatz['TiText'];
	$order = $dsatz['TiOrder'] + 0;
	$notword = $dsatz['TiIsNotWord'] + 0;
	$termex = isset($dsatz['WoID']);
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
	} // $dsatz['TiIsNotWord'] == 0  -- A TERM
} // while ($dsatz = mysql_fetch_assoc($res))  -- MAIN LOOP
mysql_free_result($res);

(jquery, deact.) 
***************************************************************/
?>
}
$('#waiting').html('<b>OK -- </b>');
//]]>
</script>

<?php

if ($showAll == 1) 
	echo '<b><i>Show All</i></b> is set to <b>ON</b>.<br /><br />ALL terms are now shown, and all multi-word terms are shown as superscripts before the first word. The superscript indicates the number of words in the multi-word term.<br /><br />To concentrate more on the multi-word terms and to display them without superscript, set <i>Show All</i> to OFF.</p>';
else
	echo '<b><i>Show All</i></b> is set to <b>OFF</b>.<br /><br />Multi-word terms now hide single words and shorter or overlapping multi-word terms. The creation and deletion of multi-word terms can be a bit slow in long texts.<br /><br />To  manipulate ALL terms, set <i>Show All</i> to ON.</p>';

pageend();

?>