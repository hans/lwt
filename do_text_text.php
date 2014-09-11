<?php

/**************************************************************
"Learning with Texts" (LWT) is free and unencumbered software 
released into the PUBLIC DOMAIN.

Anyone is free to copy, modify, publish, use, compile, sell, or
distribute this software, either in source code form or as a
compiled binary, for any purpose, commercial or non-commercial,
and by any means.

In jurisdictions that recognize copyright laws, the author or
authors of this software dedicate any and all copyright
interest in the software to the public domain. We make this
dedication for the benefit of the public at large and to the 
detriment of our heirs and successors. We intend this 
dedication to be an overt act of relinquishment in perpetuity
of all present and future rights to this software under
copyright law.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE 
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE
AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS BE LIABLE 
FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN 
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN 
THE SOFTWARE.

For more information, please refer to [http://unlicense.org/].
***************************************************************/

/**************************************************************
Call: do_text_text.php?text=[textid]
Show text header frame
***************************************************************/

require_once( 'settings.inc.php' );
require_once( 'connect.inc.php' );
require_once( 'dbutils.inc.php' );
require_once( 'utilities.inc.php' );

$sql = 'select TxLgID, TxTitle, TxAnnotatedText, TxPosition from ' . $tbpref . 'texts where TxID = ' . $_REQUEST['text'];
$res = do_mysql_query($sql);
$record = mysql_fetch_assoc($res);
$title = $record['TxTitle'];
$langid = $record['TxLgID'];
$ann = $record['TxAnnotatedText'];
$ann_exists = (strlen($ann) > 0);
$pos = $record['TxPosition'];
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
ANN_ARRAY = <?php echo annotation_to_json($ann); ?>;
DELIMITER = '<?php echo tohtml(str_replace (array('\\',']','-','^'),array('\\\\','\\]','\\-','\\^'),getSettingWithDefault('set-term-translation-delimiters'))); ?>';
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
	$('#thetext').on('selectstart','span',false).on('mousedown','.wsty',mword_drag_n_drop_select);
	$('#thetext').on('click','.mword',mword_click_event_do_text_text);
	$('.word').dblclick(word_dblclick_event_do_text_text);
	$('#thetext').on('dblclick','.mword',word_dblclick_event_do_text_text);
	$('#thetext')
	 .on("mouseenter",".wsty,.mwsty", function() {
	    var v = $(this).attr("class").replace(/.*(TERM[^ ]*)( .*)*/, '$1');
	    $( "." + v ).addClass("hword");
	 })
	 .on("mouseleave",".hword", function() {
	    $( ".hword" ).removeClass("hword");
	 });
	$(document).keydown(keydown_event_do_text_text);
});
//]]>
$(document).ready( function() {
	var pos = <?php
	if($pos>0){
		echo '$(".wsty';
		echo "[data_pos='$pos']";
		echo '").not(".hide").eq(0);';
		echo 'if (typeof pos.attr("data_pos") === "undefined") {pos= $(".wsty").not(".hide").filter(function(){return $(this).attr("data_pos")<='. $pos.';}).eq(-1);}';
	}
	else echo '0;';
?>
	$(document).scrollTo(pos);	
});
$(window).on('beforeunload',function() {
	var pos=0;
	var top=$(window).scrollTop()-$('.wsty').not('.hide').eq(0).height();
	$('.wsty').not('.hide').each(function() {		
		if ($(this).offset().top>=top){
			pos=$(this).attr('data_pos');
			return false;
		}
	});
	$.ajax({type: "POST",url:'ajax_save_text_position.php', data: { id: '<?php echo $_REQUEST['text']; ?>', position: pos }, async:false});
});
</script>
<?php

echo '<div id="thetext" ' .  ($rtlScript ? 'dir="rtl"' : '') . '><p style="' . ($removeSpaces ? 'word-break:break-all;' : '') . 
'font-size:' . $textsize . '%;line-height: 1.4; margin-bottom: 10px;">';

$currcharcount = 0;

$sql = 'select CASE WHEN `Ti2WordCount`>0 THEN Ti2WordCount ELSE 1 END as Code, CASE WHEN CHAR_LENGTH(Ti2Text)>0 THEN Ti2Text ELSE `WoText` END as TiText, CASE WHEN CHAR_LENGTH(Ti2Text)>0 THEN lower(Ti2Text) ELSE `WoTextLC` END as TiTextLC, Ti2Order as TiOrder,Ti2SeID as TiSeID,CASE WHEN `Ti2WordCount`>0 THEN 0 ELSE 1 END as TiIsNotWord, CASE WHEN CHAR_LENGTH(Ti2Text)>0 THEN CHAR_LENGTH(Ti2Text) ELSE CHAR_LENGTH(`WoTextLC`) END as TiTextLength, WoID, WoText, WoStatus, WoTranslation, WoRomanization from (' . $tbpref . 'textitems2 left join ' . $tbpref . 'words on (Ti2WoID = WoID)) where Ti2TxID = ' . $_REQUEST['text'] . ' order by Ti2Order asc, Ti2WordCount desc';

$hideuntil = -1;
$hidetag = '';
$cnt = 1;

$res = do_mysql_query($sql);

while ($record = mysql_fetch_assoc($res)) {  // MAIN LOOP
	
	$data_sid=' data_sid="' . $record['TiSeID'] . '"';
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
	
	if($cnt<$record['TiOrder']){
		echo '<span id="ID-' . $cnt++ . '-1"></span>';
	}
	if ($record['TiIsNotWord'] != 0) {  // NOT A TERM
	
		echo '<span id="' . $spanid . '" class="' . 
			$hidetag . '"' . $data_sid . '>' . 
			str_replace(
			"¶",
			'<br />',
			tohtml($record['TiText'])) . '</span>';
			
	}  // $record['TiIsNotWord'] != 0  --  NOT A TERM
	
	/////////////////////////////////////////////////
	
	else {   // $record['TiIsNotWord'] == 0  -- A TERM
	
		if ($actcode > 1) {   // A MULTIWORD FOUND
		
			//$titext[$actcode] = $record['TiText'];
			
			if (isset($record['WoID'])) {  // MULTIWORD FOUND - DISPLAY (Status 1-5, display)
			
				if (! $showAll) {
					if ($hideuntil == -1) {
						$hideuntil = $record['TiOrder'] + ($record['Code'] - 1) * 2;
					}
				}
								
?><span id="<?php echo $spanid; ?>" class="<?php echo $hidetag; ?> click mword <?php echo ($showAll ? 'mwsty' : 'wsty'); ?> <?php echo 'order'. $record['TiOrder']; ?> <?php echo 'word'. $record['WoID']; ?> <?php echo 'status'. $record['WoStatus']; ?> TERM<?php echo strToClassName($record['TiTextLC']); ?>" data_pos="<?php echo $currcharcount; ?>" data_order="<?php echo $record['TiOrder']; ?>" data_wid="<?php echo $record['WoID']; ?>" data_trans="<?php echo tohtml(repl_tab_nl($record['WoTranslation']) . getWordTagList($record['WoID'],' ',1,0)); ?>" data_rom="<?php echo tohtml($record['WoRomanization']); ?>" data_status="<?php echo $record['WoStatus']; ?>"  data_code="<?php echo $record['Code']; ?>" data_text="<?php echo tohtml($record['TiText']); ?>"<?php echo $data_sid; ?>><?php echo ($showAll ? ('&nbsp;' . $record['Code'] . '&nbsp;') : tohtml($record['TiText'])); ?></span><?php	

			}
						
		} // ($actcode > 1) -- A MULTIWORD FOUND

		////////////////////////////////////////////////
		
		else {  // ($actcode == 1)  -- A WORD FOUND
		
			if (isset($record['WoID'])) {  // WORD FOUND STATUS 1-5,98,99

?><span id="<?php echo $spanid; ?>" class="<?php echo $hidetag; ?> click word wsty <?php echo 'word'. $record['WoID']; ?> <?php echo 'status'. $record['WoStatus']; ?> TERM<?php echo strToClassName($record['TiTextLC']); ?>" data_pos="<?php echo $currcharcount; ?>" data_order="<?php echo $record['TiOrder']; ?>" data_wid="<?php echo $record['WoID']; ?>" data_trans="<?php echo tohtml(repl_tab_nl($record['WoTranslation']) . getWordTagList($record['WoID'],' ',1,0)); ?>" data_rom="<?php echo tohtml($record['WoRomanization']); ?>" data_status="<?php echo $record['WoStatus']; ?>"<?php echo $data_sid; ?>><?php echo tohtml($record['TiText']); ?></span><?php	

			}   // WORD FOUND STATUS 1-5,98,99
			
			////////////////////////////////////////////////
			
			else {    // NOT A WORD AND NOT A MULTIWORD FOUND - STATUS 0
			
?><span id="<?php echo $spanid; ?>" class="<?php echo $hidetag; ?> click word wsty status0 TERM<?php echo strToClassName($record['TiTextLC']); ?>" data_pos="<?php echo $currcharcount; ?>" data_order="<?php echo $record['TiOrder']; ?>" data_trans="" data_rom="" data_status="0" data_wid=""<?php echo $data_sid; ?>><?php echo tohtml($record['TiText']); ?></span><?php	

			}  // NOT A WORD AND NOT A MULTIWORD FOUND - STATUS 0
			
			//$titext = array('','','','','','','','','','','');
			
		}  // ($actcode == 1)  -- A WORD FOUND
		
	} // $record['TiIsNotWord'] == 0  -- A TERM
	
	if ($actcode == 1){ $currcharcount += $record['TiTextLength']; $cnt++;}
	
} // while ($record = mysql_fetch_assoc($res))  -- MAIN LOOP

mysql_free_result($res);
echo '<span id="totalcharcount" class="hide">' . $currcharcount . '</span></p><p style="font-size:' . $textsize . '%;line-height: 1.4; margin-bottom: 300px;">&nbsp;</p></div>';

pageend();

?>
