<?php

/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions, 
unless such conditions are required by law.
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

// insert_word_ignore.php?tid=..&ord=..

$wort = get_first_value("select TiText as value from textitems where TiWordCount = 1 and TiTxID = " . $_REQUEST['tid'] . " and TiOrder = " . $_REQUEST['ord']);

$wortlc =	mb_strtolower($wort, 'UTF-8');

$sprid = get_first_value("select TxLgID as value from texts where TxID = " . $_REQUEST['tid']);

pagestart("Term: " . $wort,false);

$m1 = runsql('insert into words (WoLgID, WoText, WoTextLC, WoStatus, WoStatusChanged) values( ' . 
$sprid . ', ' . 
convert_string_to_sqlsyntax($wort) . ', ' . 
convert_string_to_sqlsyntax($wortlc) . ', 98, NOW() )','Term added');
$wid = get_last_key();

echo "<p>OK, this term will be ignored!</p>";

$hex = strToClassName($wortlc);

?>
<script type="text/javascript">
//<![CDATA[
var context = window.parent.frames['l'].document;
var contexth = window.parent.frames['h'].document;
var title = make_tooltip(<?php echo prepare_textdata_js($wort); ?>,'','','98');
$('.TERM<?php echo $hex; ?>', context).removeClass('status0').addClass('status98 word<?php echo $wid; ?>').attr('data_status','98').attr('data_wid','<?php echo $wid; ?>').attr('title',title);
$('#learnstatus', contexth).html('<?php echo texttodocount2($_REQUEST['tid']); ?>');
window.parent.frames['l'].setTimeout('cClick()', 100);
//]]>
</script>
<?php

pageend();

?> 

