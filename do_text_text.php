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
Call: do_text_text.php?text=[textid]
Show text header frame
***************************************************************/

require_once( 'settings.inc.php' );
require_once( 'connect.inc.php' );
require_once( 'dbutils.inc.php' );
require_once( 'utilities.inc.php' );

$sql = 'select TxLgID, TxTitle from ' . $tbpref . 'texts where TxID = ' . $_REQUEST['text'];
$res = do_mysql_query($sql);
$record = mysql_fetch_assoc($res);
$title = $record['TxTitle'];
$langid = $record['TxLgID'];
mysql_free_result($res);

pagestart_nobody(tohtml($title));

$sql = 'select LgName, LgDict1URI, LgDict2URI, LgGoogleTranslateURI, LgTextSize, LgRemoveSpaces, LgRightToLeft from ' . $tbpref . 'languages where LgID = ' . $langid;
$res = do_mysql_query($sql);
$record = mysql_fetch_assoc($res);
$wb1 = isset($record['LgDict1URI']) ? $record['LgDict1URI'] : "";
$wb2 = isset($record['LgDict2URI']) ? $record['LgDict2URI'] : "";
$wb3 = isset($record['LgGoogleTranslateURI']) ? $record['LgGoogleTranslateURI'] : "";
$textsize = $record['LgTextSize'];
$removeSpaces = $record['LgRemoveSpaces'];
$rtlScript = $record['LgRightToLeft'];
mysql_free_result($res);

$showAll = getSettingZeroOrOne('showallwords',1);

?>
<script type="text/javascript">
//<![CDATA[
TEXTPOS = -1;
OPENED = 0;
WBLINK1 = '<?php echo $wb1; ?>';
WBLINK2 = '<?php echo $wb2; ?>';
WBLINK3 = '<?php echo $wb3; ?>';
RTL = <?php echo $rtlScript; ?>;
TID = '<?php echo $_REQUEST['text']; ?>';
ADDFILTER = '<?php echo makeStatusClassFilter(getSettingWithDefault('set-text-visit-statuses-via-key')); ?>';
$(document).ready( function() {
	$('.word').each(word_each_do_text_text);
	$('.mword').each(mword_each_do_text_text);
	$('.word').click(word_click_event_do_text_text);
	$('.mword').click(mword_click_event_do_text_text);
	$('.word').dblclick(word_dblclick_event_do_text_text);
	$('.mword').dblclick(word_dblclick_event_do_text_text);
	$(document).keydown(keydown_event_do_text_text);
});
//]]>
</script>
<?php

echo '<div id="thetext" ' .  ($rtlScript ? 'dir="rtl"' : '') . '><p style="' . ($removeSpaces ? 'word-break:break-all;' : '') . 
'font-size:' . $textsize . '%;line-height: 1.4; margin-bottom: 10px;">';

$currcharcount = 0;

$sql = 'SELECT TiWordCount AS Code, TiText, TiTextLC, TiOrder, TiIsNotWord, CHAR_LENGTH( TiText ) AS TiTextLength, WoID, WoText, WoTextLC, WoStatus, WoTranslation, WoRomanization, IFNULL( CONCAT(  \' [\', GROUP_CONCAT( DISTINCT TgText ORDER BY TgText SEPARATOR  \', \' ) ,  \']\' ) ,  \'\' ) AS taglist FROM ( ' . $tbpref . 'textitems LEFT JOIN ( ( ' . $tbpref . 'words LEFT JOIN ' . $tbpref . 'wordtags ON WoID = WtWoID ) LEFT JOIN ' . $tbpref . 'tags ON TgID = WtTgID ) ON ( TiTextLC = WoTextLC ) AND ( TiLgID = WoLgID ) ) WHERE TiTxID = ' . $_REQUEST['text'] . ' GROUP BY TiOrder, TiWordCount ORDER BY TiOrder ASC , TiWordCount DESC';

$titext = array('','','','','','','','','','','');
$hideuntil = -1;
$hidetag = '';

$res = do_mysql_query($sql);

while ($record = mysql_fetch_assoc($res)) {  // MAIN LOOP

	$actcode = $record['Code'] + 0;
	$spanid = 'ID-' . $record['TiOrder'] . '-' . $actcode;

	if ( $hideuntil > 0  ) {
		if ( $record['TiOrder'] <= $hideuntil )
			$hidetag = ' hide';
		else {
			$hideuntil = -1;
			$hidetag = '';
		}
	}				
	
	if ($record['TiIsNotWord'] != 0) {  // NOT A TERM
	
		echo '<span id="' . $spanid . '" class="' . 
			$hidetag . '">' . 
			str_replace(
			"¶",
			'<br />',
			tohtml($record['TiText'])) . '</span>';
			
	}  // $record['TiIsNotWord'] != 0  --  NOT A TERM
	
	/////////////////////////////////////////////////
	
	else {   // $record['TiIsNotWord'] == 0  -- A TERM
	
		if ($actcode > 1) {   // A MULTIWORD FOUND
		
			$titext[$actcode] = $record['TiText'];
			
			if (isset($record['WoID'])) {  // MULTIWORD FOUND - DISPLAY (Status 1-5, display)
			
				if (! $showAll) {
					if ($hideuntil == -1) {
						$hideuntil = $record['TiOrder'] + ($record['Code'] - 1) * 2;
					}
				}
								
?><span id="<?php echo $spanid; ?>" class="<?php echo $hidetag; ?> click mword <?php echo ($showAll ? 'mwsty' : 'wsty'); ?> <?php echo 'order'. $record['TiOrder']; ?> <?php echo 'word'. $record['WoID']; ?> <?php echo 'status'. $record['WoStatus']; ?> TERM<?php echo strToClassName($record['TiTextLC']); ?>" data_pos="<?php echo $currcharcount; ?>" data_order="<?php echo $record['TiOrder']; ?>" data_wid="<?php echo $record['WoID']; ?>" data_trans="<?php echo tohtml(repl_tab_nl($record['WoTranslation']) . $record['taglist']); ?>" data_rom="<?php echo tohtml($record['WoRomanization']); ?>" data_status="<?php echo $record['WoStatus']; ?>"  data_code="<?php echo $record['Code']; ?>" data_text="<?php echo tohtml($record['TiText']); ?>"><?php echo ($showAll ? ('&nbsp;' . $record['Code'] . '&nbsp;') : tohtml($record['TiText'])); ?></span><?php	

			}
			
			////////////////////////////////////////////////
			
			else {  // MULTIWORD PLACEHOLDER - NO DISPLAY 
			
?><span id="<?php echo $spanid; ?>" class="click mword <?php echo ($showAll ? 'mwsty' : 'wsty'); ?> hide <?php echo 'order'. $record['TiOrder']; ?> TERM<?php echo strToClassName($record['TiTextLC']); ?>" data_pos="<?php echo $currcharcount; ?>" data_order="<?php echo $record['TiOrder']; ?>" data_wid="" data_trans="" data_rom="" data_status="" data_code="<?php echo $record['Code']; ?>" data_text="<?php echo tohtml($record['TiText']); ?>"><?php echo ($showAll ? ('&nbsp;' . $record['Code'] . '&nbsp;') : tohtml($record['TiText'])); ?></span><?php	

			}   // MULTIWORD PLACEHOLDER - NO DISPLAY 
			
		} // ($actcode > 1) -- A MULTIWORD FOUND

		////////////////////////////////////////////////
		
		else {  // ($actcode == 1)  -- A WORD FOUND
		
			if (isset($record['WoID'])) {  // WORD FOUND STATUS 1-5,98,99

?><span id="<?php echo $spanid; ?>" class="<?php echo $hidetag; ?> click word wsty <?php echo 'word'. $record['WoID']; ?> <?php echo 'status'. $record['WoStatus']; ?> TERM<?php echo strToClassName($record['TiTextLC']); ?>" data_pos="<?php echo $currcharcount; ?>" data_order="<?php echo $record['TiOrder']; ?>" data_wid="<?php echo $record['WoID']; ?>" data_trans="<?php echo tohtml(repl_tab_nl($record['WoTranslation']) . $record['taglist']); ?>" data_rom="<?php echo tohtml($record['WoRomanization']); ?>" data_status="<?php echo $record['WoStatus']; ?>" data_mw2="<?php echo tohtml($titext[2]); ?>" data_mw3="<?php echo tohtml($titext[3]); ?>" data_mw4="<?php echo tohtml($titext[4]); ?>" data_mw5="<?php echo tohtml($titext[5]); ?>" data_mw6="<?php echo tohtml($titext[6]); ?>" data_mw7="<?php echo tohtml($titext[7]); ?>" data_mw8="<?php echo tohtml($titext[8]); ?>" data_mw9="<?php echo tohtml($titext[9]); ?>"><?php echo tohtml($record['TiText']); ?></span><?php	

			}   // WORD FOUND STATUS 1-5,98,99
			
			////////////////////////////////////////////////
			
			else {    // NOT A WORD AND NOT A MULTIWORD FOUND - STATUS 0
			
?><span id="<?php echo $spanid; ?>" class="<?php echo $hidetag; ?> click word wsty status0 TERM<?php echo strToClassName($record['TiTextLC']); ?>" data_pos="<?php echo $currcharcount; ?>" data_order="<?php echo $record['TiOrder']; ?>" data_trans="" data_rom="" data_status="0" data_wid="" data_mw2="<?php echo tohtml($titext[2]); ?>" data_mw3="<?php echo tohtml($titext[3]); ?>" data_mw4="<?php echo tohtml($titext[4]); ?>" data_mw5="<?php echo tohtml($titext[5]); ?>" data_mw6="<?php echo tohtml($titext[6]); ?>" data_mw7="<?php echo tohtml($titext[7]); ?>" data_mw8="<?php echo tohtml($titext[8]); ?>" data_mw9="<?php echo tohtml($titext[9]); ?>"><?php echo tohtml($record['TiText']); ?></span><?php	

			}  // NOT A WORD AND NOT A MULTIWORD FOUND - STATUS 0
			
			$titext = array('','','','','','','','','','','');
			
		}  // ($actcode == 1)  -- A WORD FOUND
		
	} // $record['TiIsNotWord'] == 0  -- A TERM
	
	if ($actcode == 1) $currcharcount += $record['TiTextLength']; 
	
} // while ($record = mysql_fetch_assoc($res))  -- MAIN LOOP

mysql_free_result($res);
echo '<span id="totalcharcount" class="hide">' . $currcharcount . '</span></p><p style="font-size:' . $textsize . '%;line-height: 1.4; margin-bottom: 300px;">&nbsp;</p></div>';

pageend();

?>