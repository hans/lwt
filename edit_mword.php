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
Call: edit_mword.php?....
      ... op=Save ... do insert new 
      ... op=Change ... do update
      ... tid=[textid]&ord=[textpos]&wid=[wordid] ... edit  
      ... tid=[textid]&ord=[textpos]&txt=[word] ... new or edit
Edit/New Multi-word term (expression)
***************************************************************/

require_once( 'settings.inc.php' );
require_once( 'connect.inc.php' );
require_once( 'dbutils.inc.php' );
require_once( 'utilities.inc.php' );
require_once( 'simterms.inc.php' );

$translation_raw = repl_tab_nl(getreq("WoTranslation"));
if ( $translation_raw == '' ) $translation = '*';
else $translation = $translation_raw;

// INS/UPD

if (isset($_REQUEST['op'])) {
	
	$textlc = trim(prepare_textdata($_REQUEST["WoTextLC"]));
	$text = trim(prepare_textdata($_REQUEST["WoText"]));
	$wis = $textlc;
	
	if (mb_strtolower($text, 'UTF-8') == $textlc) {
	
		// INSERT
		
		if ($_REQUEST['op'] == 'Save') {
	
			$titletext = "New Term: " . tohtml(prepare_textdata($_REQUEST["WoTextLC"]));
			pagestart_nobody($titletext);
			echo '<h4><span class="bigger">' . $titletext . '</span></h4>';
					
			$message = runsql('insert into ' . $tbpref . 'words (WoLgID, WoTextLC, WoText, ' .
				'WoStatus, WoTranslation, WoSentence, WoRomanization, WoStatusChanged,' .  make_score_random_insert_update('iv') . ') values( ' . 
				$_REQUEST["WoLgID"] . ', ' .
				convert_string_to_sqlsyntax($_REQUEST["WoTextLC"]) . ', ' .
				convert_string_to_sqlsyntax($_REQUEST["WoText"]) . ', ' .
				$_REQUEST["WoStatus"] . ', ' .
				convert_string_to_sqlsyntax($translation) . ', ' .
				convert_string_to_sqlsyntax(repl_tab_nl($_REQUEST["WoSentence"])) . ', ' .
				convert_string_to_sqlsyntax($_REQUEST["WoRomanization"]) . ', NOW(), ' .  
make_score_random_insert_update('id') . ')', "Term saved");
			$wid = get_last_key();
			set_word_count();
			$hex = strToClassName(prepare_textdata($_REQUEST["WoTextLC"]));
	
			
		} // $_REQUEST['op'] == 'Save'
		
		// UPDATE
		
		else {  // $_REQUEST['op'] != 'Save'
			
			$titletext = "Edit Term: " . tohtml(prepare_textdata($_REQUEST["WoTextLC"]));
			pagestart_nobody($titletext);
			echo '<h4><span class="bigger">' . $titletext . '</span></h4>';
			
			$oldstatus = $_REQUEST["WoOldStatus"];
			$newstatus = $_REQUEST["WoStatus"];
			$xx = '';
			if ($oldstatus != $newstatus) $xx = ', WoStatus = ' .	$newstatus . ', WoStatusChanged = NOW()';
		
			$message = runsql('update ' . $tbpref . 'words set WoText = ' . 
			convert_string_to_sqlsyntax($_REQUEST["WoText"]) . ', WoTranslation = ' . 
			convert_string_to_sqlsyntax($translation) . ', WoSentence = ' . 
			convert_string_to_sqlsyntax(repl_tab_nl($_REQUEST["WoSentence"])) . ', WoRomanization = ' .
			convert_string_to_sqlsyntax($_REQUEST["WoRomanization"]) . $xx . ',' . make_score_random_insert_update('u') . ' where WoID = ' . $_REQUEST["WoID"], "Updated");
			
			$wid = $_REQUEST["WoID"];
			
		} // $_REQUEST['op'] != 'Save'
		
		saveWordTags($wid);
		
	} // (mb_strtolower($text, 'UTF-8') == $textlc)
	
	else { // (mb_strtolower($text, 'UTF-8') != $textlc)

		$titletext = "New/Edit Term: " . tohtml(prepare_textdata($_REQUEST["WoTextLC"]));
		pagestart_nobody($titletext);
		echo '<h4><span class="bigger">' . $titletext . '</span></h4>';		
		$message = 'Error: Term in lowercase must be exactly = "' . $textlc . '", please go back and correct this!'; 
		echo error_message_with_hide($message,0);
		pageend();
		exit();

	}
	if ($_REQUEST['op'] == 'Save') {
		$lid=$_REQUEST["WoLgID"];
		$sql = "select * from " . $tbpref . "languages where LgID=" . $lid;
		$res = do_mysql_query($sql);
		$record = mysql_fetch_assoc($res);
		$termchar = $record['LgRegexpWordCharacters'];
		$splitEachChar = $record['LgSplitEachChar'];
		$rtlScript = $record['LgRightToLeft'];
		mysql_free_result($res);$appendtext=array();
		if ($splitEachChar) {
			$textlc = preg_replace('/([^\s])/u', "$1 ", $textlc);
		}
		$len = preg_match_all('/([' . $termchar . ']+)/u',$textlc,$ma);
		if($len>1){
			$ti=array();
			$sql = "SELECT * FROM " . $tbpref . "sentences where SeLgID = " . $lid . " and SeText like '%" . mysql_real_escape_string($wis) . "%'";
			$res=do_mysql_query ($sql);
			$notermchar='/[^' . $termchar . '](' . $textlc . ')[^' . $termchar . ']/ui';
			while($record = mysql_fetch_assoc($res)){
				$string= ' ' . ($splitEachChar?preg_replace('/([^\s])/u', "$1 ", $record['SeText']):$record['SeText']) . ' ';
				$txtid =$record['SeTxID'];
				$sentid =$record['SeID'];
				$last_pos = strripos ( $string , $textlc );
				$sentoffset = preg_match('/[^' . $termchar . ']/ui', mb_substr($string,1,1, 'UTF-8'));
				while($last_pos!==false){
					$matches=array();
					if($splitEachChar || preg_match ( $notermchar, $string, $matches, 0, $last_pos - 1)==1){
						$string = substr ( $string, 0, $last_pos );
						$cnt = preg_match_all('/([' . $termchar . ']+)/u',$string,$ma);
						$pos=2*$cnt+$record['SeFirstPos'] + $sentoffset;
						$txt='';
						if($len==1 || !($matches[1]==$textlc))$txt=$splitEachChar?$wis:$matches[1];
						$sqlarr[] = '(' . $wid . ',' . $lid . ',' . $txtid . ',' . $sentid . ',' . $pos . ',' . $len . ',' . convert_string_to_sqlsyntax_notrim_nonull($txt) . ')';
						if($txtid==$_REQUEST["tid"]){
							$sid[$pos]=$record['SeID'];
							if(getSettingZeroOrOne('showallwords', 1)){
								$appendtext[$pos]='&nbsp;' . $len . '&nbsp';
							}
							else $appendtext[$pos]=$splitEachChar?$wis:$matches[1];
						}
						$last_pos = strripos ( $string , $textlc );
					}
					else{
						$string = substr ( $string, 0, $last_pos );
						$last_pos = strripos ( $string , $textlc );
					}
				}
			}
		}
	mysql_free_result($res);	
	$sqltext = 'INSERT INTO ' . $tbpref . 'textitems2 (Ti2WoID,Ti2LgID,Ti2TxID,Ti2SeID,Ti2Order,Ti2WordCount,Ti2Text) VALUES ';
	$sqltext .= rtrim(implode(',', $sqlarr),',');
	mysql_query ($sqltext);
	}

	?>
	
	<p>OK: <?php echo tohtml($message); ?></p>
	
<script type="text/javascript">
//<![CDATA[
var context = window.parent.frames['l'].document;
var contexth = window.parent.frames['h'].document;
var woid = <?php echo prepare_textdata_js($wid); ?>;
var status = <?php echo prepare_textdata_js($_REQUEST["WoStatus"]); ?>;
var trans = <?php echo prepare_textdata_js($translation . getWordTagList($wid,' ',1,0)); ?>;
var roman = <?php echo prepare_textdata_js($_REQUEST["WoRomanization"]); ?>;
var title = make_tooltip(<?php echo prepare_textdata_js($_REQUEST["WoText"]); ?>,trans,roman,status);

<?php
	if ($_REQUEST['op'] == 'Save') {
		?>
		var obj = <?php echo json_encode($appendtext); ?>;
		var sid = <?php echo json_encode($sid); ?>;
		var attrs = ' class="click mword <?php echo getSettingZeroOrOne('showallwords', 1)?'m':''; ?>wsty TERM<?php echo $hex; ?> word' + woid + ' status' + status + '" data_trans="' + trans + '" data_rom="' + roman + '" data_code="<?php echo $len; ?>" data_status="' + status + '" data_wid="' + woid + '" title="' + title + '"';
		for( key in obj ) {
		var text_refresh = 0;
		if($('span[id^="ID-'+ key +'-"]', context).not(".hide").length ){if(!($('span[id^="ID-'+ key +'-"]', context).not(".hide").attr('data_code')><?php echo $len; ?>)){text_refresh = 1;}}
		$('#ID-' + key + '-' + <?php
			echo prepare_textdata_js($len); ?>, context).remove();
			var i = '';
			for(j=<?php echo $len - 1; ?>;j>0;j=j-1){
				if(j==1)i='#ID-' + key + '-1';
				if($('#ID-' + key + '-' + j,context).length){
					i = '#ID-' + key + '-' + j;
					break;
				}
			}
			var ord_class='order' + key;
			$(i, context).before('<span id="ID-' + key + '-' + <?php
			echo prepare_textdata_js($len); ?> + '"' + attrs + '>' + obj[ key ] + '</span>');
			el = $('#ID-' + key + '-' + <?php
			echo prepare_textdata_js($len); ?>, context);
			el.addClass(ord_class).attr('data_order',key);
			var txt = el.nextUntil($('#ID-' + (parseInt(key) + <?php echo $len * 2 -1; ?>) + '-1', context),'[id$="-1"]').map(function() {return $( this ).text();}).get().join( "" );
			var pos = $('#ID-' + key + '-1', context).attr('data_pos');
			el.attr('data_text',txt).attr('data_pos',pos).attr('data_sid',sid[ key ]);
		<?php if(!getSettingZeroOrOne('showallwords', 1)){ ?>
		if(text_refresh == 1){
			refresh_text(el,sid[ key ]);
		}else el.addClass('hide');
		<?php } ?>
		}
		<?php
	} else {
		?>
		$('.word' + woid, context).attr('data_trans',trans).attr('data_rom',roman).attr('title',title).removeClass('status<?php echo $_REQUEST['WoOldStatus']; ?>').addClass('status' + status).attr('data_status',status);
		$('#learnstatus', contexth).html('<?php echo texttodocount2($_REQUEST['tid']); ?>');
		<?php
	}
?>
window.parent.frames['l'].focus();
window.parent.frames['l'].setTimeout('cClick()', 100);
//]]>
</script>
	
<?php

} // if (isset($_REQUEST['op']))

else {  // if (! isset($_REQUEST['op']))

	// edit_mword.php?tid=..&ord=..&wid=..  ODER  edit_mword.php?tid=..&ord=..&txt=..
	
	$wid = getreq('wid');
	
	if ($wid == '') {	
		$lang = get_first_value("select TxLgID as value from " . $tbpref . "texts where TxID = " . $_REQUEST['tid']);
		$term = prepare_textdata(getreq('txt'));
		$termlc =	mb_strtolower($term, 'UTF-8');
		
		$wid = get_first_value("select WoID as value from " . $tbpref . "words where WoLgID = " . $lang . " and WoTextLC = " . convert_string_to_sqlsyntax($termlc)); 
		if (isset($wid)) $term = get_first_value("select WoText as value from " . $tbpref . "words where WoID = " . $wid); 
		
	} else {

		$sql = 'select WoText, WoLgID from ' . $tbpref . 'words where WoID = ' . $wid;
		$res = do_mysql_query($sql);
		$record = mysql_fetch_assoc($res);
		if ( $record ) {
			$term = $record['WoText'];
			$lang = $record['WoLgID'];
		} else {
			my_die("Cannot access Term and Language in edit_mword.php");
		}
		mysql_free_result($res);
		$termlc =	mb_strtolower($term, 'UTF-8');
		
	}
	
	$new = (isset($wid) == FALSE);

	$titletext = ($new ? "New Term" : "Edit Term") . ": " . $term;
	pagestart_nobody($titletext);
?>
<script type="text/javascript" src="js/unloadformcheck.js" charset="utf-8"></script>
<?php
	$scrdir = getScriptDirectionTag($lang);
	
	// NEW
	
	if ($new) {
		$seid = get_first_value("select Ti2SeID as value from " . $tbpref . "textitems2 where Ti2TxID = " . $_REQUEST['tid'] . " and Ti2Order = " . $_REQUEST['ord']);
		$sent = getSentence($seid, $termlc, (int) getSettingWithDefault('set-term-sentence-count'));
			
		?>
	
		<form name="newword" class="validate" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<input type="hidden" name="WoLgID" id="langfield" value="<?php echo $lang; ?>" />
		<input type="hidden" name="WoTextLC" value="<?php echo tohtml($termlc); ?>" />
		<input type="hidden" name="tid" value="<?php echo $_REQUEST['tid']; ?>" />
		<input type="hidden" name="ord" value="<?php echo $_REQUEST['ord']; ?>" />
		<table class="tab2" cellspacing="0" cellpadding="5">
		<tr title="Only change uppercase/lowercase!">
		<td class="td1 right"><b>New Term:</b></td>
		<td class="td1"><input <?php echo $scrdir; ?> class="notempty" type="text" name="WoText" id="wordfield" value="<?php echo tohtml($term); ?>" maxlength="250" size="35" /> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
		</td></tr>
		<?php print_similar_terms_tabrow(); ?>
		<tr>
		<td class="td1 right">Translation:</td>
		<td class="td1"><textarea name="WoTranslation" class="setfocus textarea-noreturn checklength" data_maxlength="500" data_info="Translation" cols="35" rows="3"></textarea></td>
		</tr>
		<tr>
		<td class="td1 right">Tags:</td>
		<td class="td1">
		<?php echo getWordTags(0); ?>
		</td>
		</tr>
		<tr>
		<td class="td1 right">Romaniz.:</td>
		<td class="td1"><input type="text" name="WoRomanization" value="" maxlength="100" size="35" /></td>
		</tr>
		<tr>
		<td class="td1 right">Sentence<br />Term in {...}:</td>
		<td class="td1"><textarea <?php echo $scrdir; ?> name="WoSentence" class="textarea-noreturn checklength" data_maxlength="1000" data_info="Sentence" cols="35" rows="3"><?php echo tohtml(repl_tab_nl($sent[1])); ?></textarea></td>
		</tr>
		<tr>
		<td class="td1 right">Status:</td>
		<td class="td1">
		<?php echo get_wordstatus_radiooptions(1); ?>
		</td>
		</tr>
		<tr>
		<tr>
		<td class="td1 right" colspan="2">
		<?php echo createDictLinksInEditWin($lang,$term,'document.forms[0].WoSentence',1); ?>
		&nbsp; &nbsp; &nbsp; 
		<input type="submit" name="op" value="Save" /></td>
		</tr>
		</table>
		</form>
		<div id="exsent"><span class="click" onclick="do_ajax_show_sentences(<?php echo $lang; ?>, <?php echo prepare_textdata_js($termlc) . ', ' . prepare_textdata_js("document.forms['newword'].WoSentence") . ', ' . $wid; ?>);"><img src="icn/sticky-notes-stack.png" title="Show Sentences" alt="Show Sentences" /> Show Sentences</span></div>	
		<?php
	}
	
	// CHG
	
	else {
		
		$sql = 'select WoTranslation, WoSentence, WoRomanization, WoStatus from ' . $tbpref . 'words where WoID = ' . $wid;
		$res = do_mysql_query($sql);
		if ($record = mysql_fetch_assoc($res)) {
		
			$status = $record['WoStatus'];
			if ($status >= 98) $status = 1;
			$sentence = repl_tab_nl($record['WoSentence']);
			if ($sentence == '') {
				$seid = get_first_value("select Ti2SeID as value from " . $tbpref . "textitems2 where Ti2TxID = " . $_REQUEST['tid'] . " and Ti2Order = " . $_REQUEST['ord']);
				$sent = getSentence($seid, $termlc, (int) getSettingWithDefault('set-term-sentence-count'));
				$sentence = repl_tab_nl($sent[1]);
			}
			$transl = repl_tab_nl($record['WoTranslation']);
			if($transl == '*') $transl='';
			?>
		
			<form name="editword" class="validate" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="hidden" name="WoLgID" id="langfield" value="<?php echo $lang; ?>" />
			<input type="hidden" name="WoID" value="<?php echo $wid; ?>" />
			<input type="hidden" name="WoOldStatus" value="<?php echo $record['WoStatus']; ?>" />
			<input type="hidden" name="WoStatus" value="<?php echo $status; ?>" />
			<input type="hidden" name="WoTextLC" value="<?php echo tohtml($termlc); ?>" />
			<input type="hidden" name="tid" value="<?php echo $_REQUEST['tid']; ?>" />
			<input type="hidden" name="ord" value="<?php echo $_REQUEST['ord']; ?>" />
			<table class="tab2" cellspacing="0" cellpadding="5">
			<tr title="Only change uppercase/lowercase!">
			<td class="td1 right"><b>Edit Term:</b></td>
			<td class="td1" style="border-top-right-radius:inherit;"><input <?php echo $scrdir; ?> class="notempty" type="text" name="WoText" id="wordfield" value="<?php echo tohtml($term); ?>" maxlength="250" size="35" /> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
			</td></tr>
			<?php print_similar_terms_tabrow(); ?>
			<tr>
			<td class="td1 right">Translation:</td>
			<td class="td1"><textarea name="WoTranslation" class="setfocus textarea-noreturn checklength" data_maxlength="500" data_info="Translation" cols="35" rows="3"><?php echo tohtml($transl); ?></textarea></td>
			</tr>
			<tr>
			<td class="td1 right">Tags:</td>
			<td class="td1">
			<?php echo getWordTags($wid); ?>
			</td>
			</tr>
			<tr>
			<td class="td1 right">Romaniz.:</td>
			<td class="td1"><input type="text" name="WoRomanization" maxlength="100" size="35" 
			value="<?php echo tohtml($record['WoRomanization']); ?>" /></td>
			</tr>
			<tr>
			<td class="td1 right">Sentence<br />Term in {...}:</td>
			<td class="td1"><textarea <?php echo $scrdir; ?> name="WoSentence" class="textarea-noreturn checklength" data_maxlength="1000" data_info="Sentence" cols="35" rows="3"><?php echo tohtml($sentence); ?></textarea></td>
			</tr>
			<tr>
			<td class="td1 right">Status:</td>
			<td class="td1">
			<?php echo get_wordstatus_radiooptions($record['WoStatus']); ?>
			</td>
			</tr>
			<tr>
			<td class="td1 right" colspan="2">
			<?php echo createDictLinksInEditWin($lang,$term,'document.forms[0].WoSentence',1); ?>
			&nbsp; &nbsp; &nbsp; 
			<input type="submit" name="op" value="Change" /></td>
			</tr>
			</table>
			</form>
			<div id="exsent"><span class="click" onclick="do_ajax_show_sentences(<?php echo $lang; ?>, <?php echo prepare_textdata_js($termlc) . ', ' . prepare_textdata_js("document.forms['editword'].WoSentence") . ', ' . $wid; ?>);"><img src="icn/sticky-notes-stack.png" title="Show Sentences" alt="Show Sentences" /> Show Sentences</span></div>	
			<?php
		}
		mysql_free_result($res);
	}

}

pageend();

?>
