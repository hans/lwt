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
Edit/New term (with AUTOSAVE)
***************************************************************/

// new: $wid=0, $term='newterm or empty', $lang>0
// exists: $wid>0 

if ( $source == 'new_word' ) {
	// text=..&lang=..
	$lang = getreq('lang') + 0;
	$tid = getreq('text') + 0;
	$wid = 0;
	$term = '';
}
elseif  ( $source == 'edit_tword' ) {
	// wid=..
	$wid = getreq('wid');
	if ($wid == '') my_die("Term ID missing!");
	$wid = $wid + 0;
}
elseif  ( $source == 'edit_mword' ) {
	// tid=..&ord=..&wid=..      edit 
  // tid=..&ord=..&txt=......  new
	$tid = getreq('tid') + 0;
	$wid = getreq('wid');
	if ($wid == '') {	
		$wid = 0;
		$lang = get_first_value("select TxLgID as value from " . $tbpref . "texts where TxID = " . $tid);
		$term = prepare_textdata(getreq('txt'));
		$wid = get_first_value("select WoID as value from " . $tbpref . "words where WoLgID = " . $lang . " and WoTextLC = " . convert_string_to_sqlsyntax(mb_strtolower($term, 'UTF-8'))); 
		if (! isset($wid)) $wid = 0;
	} else {
		$wid = $wid + 0;
	}
}
else {  // == 'edit_word'
	// tid=..&ord=..        ... new word  
  // tid=..&ord=..&wid=.. ... edit word 
	$tid = getreq('tid') + 0;
	$ord = getreq('ord') + 0;
	$wid = getreq('wid');
	if ($wid == '') {
		$wid = 0;	
		$sql = 'select TiText, TiLgID from ' . $tbpref . 'textitems where TiTxID = ' . $tid . ' and TiWordCount = 1 and TiOrder = ' . $ord;
		$res = do_mysql_query($sql);
		$record = mysql_fetch_assoc($res);
		if ($record) {
			$term = $record['TiText'];
			$lang = $record['TiLgID'];
		} else {
			my_die("Cannot access Term and Language in edit_word.php");
		}
		mysql_free_result($res);
		$wid = get_first_value("select WoID as value from " . $tbpref . "words where WoLgID = " . $lang . " and WoTextLC = " . convert_string_to_sqlsyntax(mb_strtolower($term, 'UTF-8'))); 
		if (! isset($wid)) $wid = 0;
	} else {
		$wid = $wid + 0;
	}
}

if ( $wid > 0 ) {
	$sql = 'select WoLgID, WoText, WoStatus, WoTranslation, WoSentence, WoRomanization from ' . $tbpref . 'words where WoID = ' . $wid;
	$res = do_mysql_query($sql);
	$record = mysql_fetch_assoc($res);
	if ($record) {
		$lang = $record['WoLgID'] + 0;
		$term = $record['WoText'];
		$translation = $record['WoTranslation'];
		$sentence = $record['WoSentence'];
		$rom = $record['WoRomanization'];
		$status = $record['WoStatus'] + 0;
	} else {
		my_die("Cannot access Term " . $wid);
	}
	mysql_free_result($res);
} else {
	$seid = get_first_value("select TiSeID as value from " . $tbpref . "textitems where TiTxID = " . $tid . " and TiWordCount = 1 and TiOrder = " . $ord);
	$sent = getSentence($seid, mb_strtolower($term, 'UTF-8'), (int) getSettingWithDefault('set-term-sentence-count'));
	$translation = "";
	$sentence = repl_tab_nl($sent[1]);
	$rom = "";
	$status = 1;
}

$scrdir = getScriptDirectionTag($lang);

pagestart_nobody('');

?>

<script type="text/javascript">
//<![CDATA[

function autoSave() {
	$('#status').text('> Changed <');
	var arr = {
	'WoText': $('#WoText').val(),
	'WoTranslation': $('#WoTranslation').val(),
	'WoTags': $('#termtags').tagit('assignedTags'),
	'WoRomanization': $('#WoRomanization').val(),
	'WoSentence': $('#WoSentence').val(),
	'WoStatus': $('input[name=WoStatus]::checked').val()
	}
	var thedata = JSON.stringify(arr);
	$.post('ajax_save_word.php', 
		{ 
			wordid: $('#WoID').val(), 
			langid: $('#WoLgID').val(), 
			worddata : thedata
		}, 
		function(data) { 
			var res = eval('(' + data + ')');
			if (res[0] == 1) $('#status').text('> Saved <');
			else $('#status').text('> Save Error! <');
			// alert('RESULT: ' + res[0] + ' / ' + res[1] + ' / ' + res[2]);
		} 
	);
}

function tagChanged(event, ui) {
	if (! ui.duringInitialization) autoSave();
	return true;
}

$(document).ready( function() {
	$('#termtags').tagit({afterTagAdded: tagChanged, afterTagRemoved: tagChanged});
	$('#texttags').tagit({afterTagAdded: tagChanged, afterTagRemoved: tagChanged}); 
	$('input,checkbox,textarea,radio,select').bind('change',autoSave);
} ); 
	
//]]>
</script>
	
<form name="editword" id="editword">
<input type="hidden" name="WoID" id="WoID" value="<?php echo $wid; ?>" />
<input type="hidden" name="WoLgID" id="WoLgID" value="<?php echo $lang; ?>" />
<table class="tab2" cellspacing="0" cellpadding="5">
<tr>
<td class="td1 right">Term:</td>
<td class="td1"><input <?php echo $scrdir; ?> class="notempty" type="text" name="WoText" id="WoText" value="<?php echo tohtml($term); ?>" maxlength="250" size="35" /> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<tr>
<td class="td1 right">Translation:</td>
<td class="td1"><textarea name="WoTranslation" id="WoTranslation" class="setfocus textarea-noreturn checklength" data_maxlength="500" data_info="Translation" cols="35" rows="3"><?php echo tohtml($translation); ?></textarea></td>
</tr>
<tr>
<td class="td1 right">Tags:</td>
<td class="td1">
<?php echo getWordTags($wid); ?>
</td>
</tr>
<tr>
<td class="td1 right">Romaniz.:</td>
<td class="td1"><input type="text" name="WoRomanization" id="WoRomanization" maxlength="100" size="35" value="<?php echo tohtml($rom); ?>" /></td>
</tr>
<tr>
<td class="td1 right">Sentence<br />Term in {...}:</td>
<td class="td1"><textarea <?php echo $scrdir; ?> name="WoSentence" id="WoSentence" class="textarea-noreturn checklength" data_maxlength="1000" data_info="Sentence" cols="35" rows="3"><?php echo tohtml($sentence); ?></textarea></td>
</tr>
<tr>
<td class="td1 right">Status:</td>
<td class="td1">
<?php echo get_wordstatus_radiooptions($status); ?>
</td>
</tr>
<tr>
<td class="td1 right" colspan="2"><b><span id="status" class="red2"></span></b> &nbsp; &nbsp; &nbsp; &nbsp;  
<?php echo createDictLinksInEditWin($lang,$term,'document.forms[0].WoSentence',1); ?>
</td>
</tr>
</table>
</form>
<div id="exsent"><span class="click" onclick="do_ajax_show_sentences(<?php echo $lang; ?>, <?php echo prepare_textdata_js(mb_strtolower($term, 'UTF-8')) . ', ' . prepare_textdata_js("document.forms['editword'].WoSentence"); ?>);"><img src="icn/sticky-notes-stack.png" title="Show Sentences" alt="Show Sentences" /> Show Sentences</span></div>	

<?php

pageend();

?>