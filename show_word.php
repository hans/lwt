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
Call: show_word.php?wid=...
Show term
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

pagestart_nobody('Term');

$wid = getreq('wid');

if ($wid == '') die ('Word not found');

$sql = 'select WoText, WoTranslation, WoSentence, WoRomanization, WoStatus from words where WoID = ' . $wid;
$res = mysql_query($sql);		
if ($res == FALSE) die("Invalid Query: $sql");
if ($dsatz = mysql_fetch_assoc($res)) {

	$transl = repl_tab_nl($dsatz['WoTranslation']);
	if($transl == '*') $transl='';

?>


<table class="tab2" cellspacing="0" cellpadding="5">
<tr>
<td class="td1 right" style="width:30px;"><b>Term:</b></td>
<td class="td1" style="font-size:150%;"><b><?php echo tohtml($dsatz['WoText']); ?></b></td>
</tr>
<tr>
<td class="td1 right">Translation:</td>
<td class="td1"><?php echo tohtml($transl); ?></td>
</tr>
<tr>
<td class="td1 right">Romaniz.:</td>
<td class="td1"><?php echo tohtml($dsatz['WoRomanization']); ?></td>
</tr>
<tr>
<td class="td1 right">Sentence<br />Term in {...}:</td>
<td class="td1"><?php echo tohtml($dsatz['WoSentence']); ?></textarea></td>
</tr>
<tr>
<td class="td1 right">Status:</td>
<td class="td1"><?php echo get_colored_status_msg($dsatz['WoStatus']); ?></span>
</td>
</tr>
</table>

<script type="text/javascript">
//<![CDATA[
window.parent.frames['l'].focus();
window.parent.frames['l'].setTimeout('cClick()', 100);
//]]>
</script>

<?php
}

mysql_free_result($res);

pageend();

?>