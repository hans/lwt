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
Call: do_text_text.php?text=[textid]
Show text header frame
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

$sql = 'select TxLgID, TxTitle from texts where TxID = ' . $_REQUEST['text'];
$res = mysql_query($sql);		
if ($res == FALSE) die("Invalid Query: $sql");
$dsatz = mysql_fetch_assoc($res);
$titel = $dsatz['TxTitle'];
$sprid = $dsatz['TxLgID'];
mysql_free_result($res);

pagestart_nobody(tohtml($titel));

$sql = 'select LgName, LgDict1URI, LgDict2URI, LgGoogleTranslateURI, LgGoogleTTSURI, LgTextSize, LgRemoveSpaces from languages where LgID = ' . $sprid;
$res = mysql_query($sql);		
if ($res == FALSE) die("Invalid Query: $sql");
$dsatz = mysql_fetch_assoc($res);
$wb1 = isset($dsatz['LgDict1URI']) ? $dsatz['LgDict1URI'] : "";
$wb2 = isset($dsatz['LgDict2URI']) ? $dsatz['LgDict2URI'] : "";
$wb3 = isset($dsatz['LgGoogleTranslateURI']) ? $dsatz['LgGoogleTranslateURI'] : "";
$wb4 = isset($dsatz['LgGoogleTTSURI']) ? $dsatz['LgGoogleTTSURI'] : "";
$textsize = $dsatz['LgTextSize'];
$removeSpaces = $dsatz['LgRemoveSpaces'];
mysql_free_result($res);

$showAll = getSetting('showallwords');
$showAll = ($showAll == '' ? 1 : (((int) $showAll != 0) ? 1 : 0));

?>
<script type="text/javascript">
//<![CDATA[
TEXTPOS = -1;
OPENED = 0;
WBLINK1 = '<?php echo $wb1; ?>';
WBLINK2 = '<?php echo $wb2; ?>';
WBLINK3 = '<?php echo $wb3; ?>';
WBLINK4 = '<?php echo $wb3; ?>';
TID = '<?php echo $_REQUEST['text']; ?>';
ADDFILTER = '<?php echo makeStatusClassFilter(getSettingWithDefault('set-text-visit-statuses-via-key')); ?>';
$(document).ready( function() {
	$('.word').each(word_each_do_text_text);
	$('.mword').each(mword_each_do_text_text);
	$('.word').click(word_click_event_do_text_text);
	$('.mword').click(mword_click_event_do_text_text);
	$(document).keydown(keydown_event_do_text_text);
});
//]]>
</script>
<?php

echo '<div id="thetext"><p style="' . ($removeSpaces ? 'word-break:break-all;' : '') . 
'font-size:' . $textsize . '%;line-height: 1.4; margin-bottom: 10px;">';

$sql = 'select TiWordCount as Code, TiText, TiTextLC, TiOrder, TiIsNotWord, WoID, WoText, WoTextLC, WoStatus, WoTranslation, WoRomanization from (textitems left join words on (TiTextLC = WoTextLC) and (TiLgID = WoLgID)) where TiTxID = ' . $_REQUEST['text'] . ' order by TiOrder asc, TiWordCount desc';

$titext = array('','','','','','','','','','','');
$hideuntil = -1;
$hidetag = '';

$res = mysql_query($sql);		
if ($res == FALSE) die("Invalid Query: $sql");

while ($dsatz = mysql_fetch_assoc($res)) {  // MAIN LOOP

	$actcode = $dsatz['Code'] + 0;
	$spanid = 'ID-' . $dsatz['TiOrder'] . '-' . $actcode;

	if ( $hideuntil > 0  ) {
		if ( $dsatz['TiOrder'] <= $hideuntil )
			$hidetag = ' hide';
		else {
			$hideuntil = -1;
			$hidetag = '';
		}
	}				
	
	if ($dsatz['TiIsNotWord'] != 0) {  // NOT A TERM
	
		echo '<span id="' . $spanid . '" class="' . 
			$hidetag . '">' . 
			str_replace(
			"¶",
			'<br />',
			tohtml($dsatz['TiText'])) . '</span>';
			
	}  // $dsatz['TiIsNotWord'] != 0  --  NOT A TERM
	
	/////////////////////////////////////////////////
	
	else {   // $dsatz['TiIsNotWord'] == 0  -- A TERM
	
		if ($actcode > 1) {   // A MULTIWORD FOUND
		
			$titext[$actcode] = $dsatz['TiText'];
			
			if (isset($dsatz['WoID'])) {  // MULTIWORD FOUND - DISPLAY (Status 1-5, display)
			
				if (! $showAll) {
					if ($hideuntil == -1) {
						$hideuntil = $dsatz['TiOrder'] + ($dsatz['Code'] - 1) * 2;
					}
				}
				
?><span id="<?php echo $spanid; ?>" class="<?php echo $hidetag; ?> click mword <?php echo ($showAll ? 'mwsty' : 'wsty'); ?> <?php echo 'order'. $dsatz['TiOrder']; ?> <?php echo 'word'. $dsatz['WoID']; ?> <?php echo 'status'. $dsatz['WoStatus']; ?> TERM<?php echo strToClassName($dsatz['TiTextLC']); ?>" data_order="<?php echo $dsatz['TiOrder']; ?>" data_wid="<?php echo $dsatz['WoID']; ?>" data_trans="<?php echo tohtml(isset($dsatz['WoTranslation']) ? ($dsatz['WoTranslation']=='*' ? "" : repl_tab_nl($dsatz['WoTranslation'])) : ""); ?>" data_rom="<?php echo tohtml($dsatz['WoRomanization']); ?>" data_status="<?php echo $dsatz['WoStatus']; ?>"  data_code="<?php echo $dsatz['Code']; ?>" data_text="<?php echo tohtml($dsatz['TiText']); ?>"><?php echo ($showAll ? ('&nbsp;' . $dsatz['Code'] . '&nbsp;') : tohtml($dsatz['TiText'])); ?></span><?php	

			}
			
			////////////////////////////////////////////////
			
			else {  // MULTIWORD PLACEHOLDER - NO DISPLAY 
			
?><span id="<?php echo $spanid; ?>" class="click mword <?php echo ($showAll ? 'mwsty' : 'wsty'); ?> hide <?php echo 'order'. $dsatz['TiOrder']; ?> TERM<?php echo strToClassName($dsatz['TiTextLC']); ?>" data_order="<?php echo $dsatz['TiOrder']; ?>" data_wid="" data_trans="" data_rom="" data_status="" data_code="<?php echo $dsatz['Code']; ?>" data_text="<?php echo tohtml($dsatz['TiText']); ?>"><?php echo ($showAll ? ('&nbsp;' . $dsatz['Code'] . '&nbsp;') : tohtml($dsatz['TiText'])); ?></span><?php	

			}   // MULTIWORD PLACEHOLDER - NO DISPLAY 
			
		} // ($actcode > 1) -- A MULTIWORD FOUND

		////////////////////////////////////////////////
		
		else {  // ($actcode == 1)  -- A WORD FOUND
		
			if (isset($dsatz['WoID'])) {  // WORD FOUND STATUS 1-5,98,99
			
?><span id="<?php echo $spanid; ?>" class="<?php echo $hidetag; ?> click word wsty <?php echo 'word'. $dsatz['WoID']; ?> <?php echo 'status'. $dsatz['WoStatus']; ?> TERM<?php echo strToClassName($dsatz['TiTextLC']); ?>" data_order="<?php echo $dsatz['TiOrder']; ?>" data_wid="<?php echo $dsatz['WoID']; ?>" data_trans="<?php echo tohtml(isset($dsatz['WoTranslation']) ? ($dsatz['WoTranslation']=='*' ? "" : repl_tab_nl($dsatz['WoTranslation'])) : ""); ?>" data_rom="<?php echo tohtml($dsatz['WoRomanization']); ?>" data_status="<?php echo $dsatz['WoStatus']; ?>" data_mw2="<?php echo tohtml($titext[2]); ?>" data_mw3="<?php echo tohtml($titext[3]); ?>" data_mw4="<?php echo tohtml($titext[4]); ?>" data_mw5="<?php echo tohtml($titext[5]); ?>" data_mw6="<?php echo tohtml($titext[6]); ?>" data_mw7="<?php echo tohtml($titext[7]); ?>" data_mw8="<?php echo tohtml($titext[8]); ?>" data_mw9="<?php echo tohtml($titext[9]); ?>"><?php echo tohtml($dsatz['TiText']); ?></span><?php	

			}   // WORD FOUND STATUS 1-5,98,99
			
			////////////////////////////////////////////////
			
			else {    // NOT A WORD AND NOT A MULTIWORD FOUND - STATUS 0
			
?><span id="<?php echo $spanid; ?>" class="<?php echo $hidetag; ?> click word wsty status0 TERM<?php echo strToClassName($dsatz['TiTextLC']); ?>" data_order="<?php echo $dsatz['TiOrder']; ?>" data_trans="" data_rom="" data_status="0" data_wid="" data_mw2="<?php echo tohtml($titext[2]); ?>" data_mw3="<?php echo tohtml($titext[3]); ?>" data_mw4="<?php echo tohtml($titext[4]); ?>" data_mw5="<?php echo tohtml($titext[5]); ?>" data_mw6="<?php echo tohtml($titext[6]); ?>" data_mw7="<?php echo tohtml($titext[7]); ?>" data_mw8="<?php echo tohtml($titext[8]); ?>" data_mw9="<?php echo tohtml($titext[9]); ?>"><?php echo tohtml($dsatz['TiText']); ?></span><?php	

			}  // NOT A WORD AND NOT A MULTIWORD FOUND - STATUS 0
			
			$titext = array('','','','','','','','','','','');
			
		}  // ($actcode == 1)  -- A WORD FOUND
		
	} // $dsatz['TiIsNotWord'] == 0  -- A TERM
	
} // while ($dsatz = mysql_fetch_assoc($res))  -- MAIN LOOP

mysql_free_result($res);
echo '</p><p style="font-size:' . $textsize . '%;line-height: 1.4; margin-bottom: 300px;">&nbsp;</p></div>';

pageend();

?>