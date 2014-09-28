<?php

require_once( 'settings.inc.php' );
require_once( 'connect.inc.php' );
require_once( 'dbutils.inc.php' );
require_once( 'utilities.inc.php' );

$tid=$_REQUEST['tid'];
if(isset ($_REQUEST["lastpos"]))$pos = $_REQUEST["lastpos"];
if (isset($_REQUEST['term'])) {
	$cnt=0;
	$sqltext='insert into ' . $tbpref . 'words (WoLgID, WoTextLC, WoText, ' .
			'WoStatus, WoTranslation, WoSentence, WoRomanization, WoStatusChanged,' .  make_score_random_insert_update('iv') . ') values ';
	$sqlarr=array();
	foreach($_REQUEST['term'] as $row){
		$sqlarr[]= '(' . convert_string_to_sqlsyntax($row['lg']) . ',' . convert_string_to_sqlsyntax(mb_strtolower($row['text'], 'UTF-8')) . ',' . convert_string_to_sqlsyntax($row['text']) . ',' . convert_string_to_sqlsyntax($row['status']) . ',' . (($row['trans']=='')?'"*"': convert_string_to_sqlsyntax($row['trans'])) . ', "", ""' . ', NOW(), ' . make_score_random_insert_update('id') . ')';
		$cnt++;
	}
	$sqltext .= rtrim(implode(',', $sqlarr),',');
	$max = get_first_value('select max(WoID) as value from ' . $tbpref . 'words');
	runsql($sqltext,'');
	pagestart($cnt . ' New Word' . ($cnt!=1?'s':'') . ' Saved',false);
	echo '<p id="displ_message">Updating Texts</p>';
	flush();
	$res = do_mysql_query('select WoID, WoTextLC, WoStatus, WoTranslation from ' . $tbpref . 'words where WoID > ' . $max);
	echo '<script type="text/javascript">var context = window.parent.frames[\'l\'].document;';
	while($record = mysql_fetch_assoc($res)){
		$hex = strToClassName(prepare_textdata($record["WoTextLC"]));
		echo '$(".TERM',$hex,'",context).removeClass("status0").addClass("status',$record["WoStatus"],'").attr("data_wid","',$record["WoID"],'").attr("data_status","',$record["WoStatus"],'").attr("data_trans",',prepare_textdata_js($record["WoTranslation"]),').each(function(){this.title = make_tooltip($(this).text(), $(this).attr(\'data_trans\'), $(this).attr(\'data_rom\'), $(this).attr(\'data_status\'));});',"\n";
	}
	mysql_free_result($res);
	echo "</script>";
	flush();
	do_mysql_query('UPDATE ' . $tbpref . 'textitems2 join ' . $tbpref . 'words on lower(Ti2Text)=WoTextLC AND Ti2WordCount =1 and Ti2LgID=WoLgID and WoID > ' . $max . ' set Ti2WoID = WoID');
	echo "<script type=\"text/javascript\">$('#learnstatus', window.parent.frames['h'].document).html('",addslashes(texttodocount2($tid)),"');$('#displ_message').remove();</script>";
	flush();
}
else {
	pagestart_nobody('Translate New Words');
}
if(isset($pos)){
$sl=$_REQUEST["sl"];
$tl=$_REQUEST["tl"];
$cnt = 0;
$lastpos = '';
$limit = getSettingWithDefault('set-ggl-translation-per-page') + 1;
?>
<style>
body {top:0px ! important;}
td.td1{vertical-align:middle ! important;}
</style>

<script type="text/javascript">
$('h3,h4,title').addClass('notranslate');
$(window).load(function() {
	var myVar = setInterval(function(){
		if( $( ".trans>font" ).length == $( ".trans" ).length){
			$('.trans').each(function() {
				var txt=$(this).text().toLowerCase();
				var cnt= $(this).attr('id').replace('Trans_', '');
				$(this).addClass('notranslate').html('<input type="text" name="term[' + cnt + '][trans]"  value="' + txt + '" maxlength="100" size="35"></input>');
			});
			$('iframe,#google_translate_element').remove();
			selectToggle(true,'form1');
			$('[name^=term]').prop('disabled', false);
			clearInterval(myVar);
		}
	}, 300);
});
$(document).ready( function() {
	$('input[type="checkbox"]').change(function(){
		var v = parseInt($(this).val());
		var e = '[name=term\\[' + v + '\\]\\[text\\]],[name=term\\[' + v + '\\]\\[lg\\]],[name=term\\[' + v + '\\]\\[status\\]],[name=term\\[' + v + '\\]\\[trans\\]]';
		if(this.checked){
			$(e).prop('disabled', false);
			if($('input[type="checkbox"]:checked').length){$('input[type="submit"]').val('Save');}
		}
		else{
			$(e).prop('disabled', true);
			if(!$('input[type="checkbox"]:checked').length){if(!$('input[name="lastpos"]').length)v='End';else v='Next';$('input[type="submit"]').val(v);}
		}
	});
});
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: '<?php echo $sl; ?>', layout: google.translate.TranslateElement.InlineLayout.SIMPLE, includedLanguages: '<?php echo $tl; ?>', autoDisplay: false}, 'google_translate_element');
}
</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<?php
	echo '<form name="form1" action="', $_SERVER['PHP_SELF'], '" method="post"><span class="notranslate"><div id="google_translate_element"></div><table class="tab3" cellspacing="0"><tr class="notranslate"><th class="th1 center" colspan="3"><input type="button" value="Mark All" onclick="$(\'input[type^=submit]\').val(\'Save\');selectToggle(true,\'form1\');$(\'[name^=term]\').prop(\'disabled\', false);" />
<input type="button" value="Mark None" onclick="if(!$(\'input[name^=lastpos]\').length)v=\'End\';else v=\'Next\';$(\'input[type^=submit]\').val(v);selectToggle(false,\'form1\');$(\'[name^=term]\').prop(\'disabled\', true);" /><br /></th></tr><tr class="notranslate"><td class="td1">
Marked Terms: </td><td class="td1">
<select onchange="v=$(this).val();if(v==6){$(\'.markcheck:checked\').each(function(){e=$(\'#Term_\' + $(this).val());e.text(e.text().toLowerCase());$(\'#Text_\' + $(this).val()).val(e.text().toLowerCase());});$(this).prop(\'selectedIndex\',0);return false;}if(v==7){$(\'.markcheck:checked\').each(function(){$(\'#Trans_\' + $(this).val() + \' input\').val(\'*\');});$(this).prop(\'selectedIndex\',0);return false;}$(\'.markcheck:checked\').each(function(){$(\'#Stat_\' + $(this).val()).val(v);});$(this).prop(\'selectedIndex\',0);return false;"><option value="0" selected="selected">[Choose...]</option><option value="1">Set Status To [1]</option><option value="2">Set Status To [2]</option><option value="3">Set Status To [3]</option><option value="4">Set Status To [4]</option><option value="5">Set Status To [5]</option><option value="99">Set Status To [WKn]</option><option value="98">Set Status To [Ign]</option><option value="6">Set To Lowercase</option><option value="7">Delete Translation</option></select></td><td class="td1" style="min-width: 45px;"><input  type="submit" value="Save" /></td></tr></table></span>
<table class="tab3" cellspacing="0"><tr class="notranslate"><th class="th1">Mark</th><th class="th1">Term</th><th class="th1">Translation</th><th class="th1">Status</th></tr>';

$res = do_mysql_query ('select Ti2Text as word,Ti2LgID,Ti2Order from ' . $tbpref . 'textitems2 where Ti2WoID = 0 and Ti2TxID = ' . $tid . ' AND Ti2WordCount =1 and Ti2Order > ' . $pos . ' group by LOWER(Ti2Text) order by Ti2Order limit ' . $limit);
while($record = mysql_fetch_assoc($res)){
	if(++$cnt<$limit){
		$value=tohtml($record['word']);
		echo '<tr><td class="td1 center notranslate"><input name="marked[', $cnt ,']" type="checkbox" class="markcheck" checked="checked" value="', $cnt , '" /></td><td id="Term_', $cnt ,'" class="td1 left notranslate">',$value,'</td><td class="td1 right trans" id="Trans_', $cnt ,'">',$value,'</td><td class="td1 center notranslate"><select id="Stat_', $cnt ,'" name="term[', $cnt ,'][status]"><option value="1" selected="selected">[1]</option><option value="2">[2]</option><option value="3">[3]</option><option value="4">[4]</option><option value="5">[5]</option><option value="99">[WKn]</option><option value="98">[Ign]</option></select><input type="hidden" id="Text_', $cnt ,'" name="term[', $cnt ,'][text]" value="',$value,'" /><input type="hidden" name="term[', $cnt ,'][lg]" value="',tohtml($record['Ti2LgID']),'" /></td></tr>',"\n";
	}
	else $lastpos='<input type="hidden" name="lastpos" value="' . ($record['Ti2Order'] - 1) . '" /><input type="hidden" name="sl" value="' . $sl . '" /><input type="hidden" name="tl" value="' . $tl . '" />';
}
mysql_free_result($res);
echo '</table><input type="hidden" name="tid" value="',$tid,'" />', $lastpos ,'</form>';
}
pageend();
?>
