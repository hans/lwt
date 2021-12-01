<?php

/**************************************************************
Call: all_words_wellknown.php?text=[textid]
Setting all unknown words to Well Known (99)
 ***************************************************************/

require_once 'inc/session_utility.php';

$status=$_REQUEST['stat'];
$langid = get_first_value("select TxLgID as value from " . $tbpref . "texts where TxID = " . $_REQUEST['text']);

if($status==98) {
    pagestart("Setting all blue words to Ignore", false); 
}
if($status==99) {
    pagestart("Setting all blue words to Well-known", false); 
}

$sql = 'select distinct TiText, TiTextLC from (' . $tbpref . 'textitems left join ' . $tbpref . 'words on (TiTextLC = WoTextLC) and (TiLgID = WoLgID)) where TiIsNotWord = 0 and WoID is null and TiWordCount = 1 and TiTxID = ' . $_REQUEST['text'] . ' order by TiOrder';
$res = do_mysqli_query($sql);
$count = 0;
$javascript = "var title='';";
while ($record = mysqli_fetch_assoc($res)) {
    $term = $record['TiText'];    
    $termlc = $record['TiTextLC'];    
    $count1 = 0 + runsql(
        'insert into ' . $tbpref . 'words (WoLgID, WoText, WoTextLC, WoStatus, WoStatusChanged,' .  make_score_random_insert_update('iv') . ') values( ' . 
        $langid . ', ' . 
        convert_string_to_sqlsyntax($term) . ', ' . 
        convert_string_to_sqlsyntax($termlc) . ', 1, '.$status.' , NOW(), ' .  
        make_score_random_insert_update('id') . ')', ''
    );
    $wid = get_last_key();
    $sqlarr[]= ' WHEN ' . convert_string_to_sqlsyntax_notrim_nonull($termlc) . ' THEN ' . $wid;
    if($tooltip_mode == 1) {
        if ($count1 > 0 ) { 
            $javascript .= "title = make_tooltip(" . prepare_textdata_js($term) . ",'*','','".$status."');"; 
        } 
    }
    $javascript .= "$('.TERM" . strToClassName($termlc) . "', context).removeClass('status0').addClass('status".$status." word" . $wid . "').attr('data_status','".$status."').attr('data_wid','" . $wid . "').attr('title',title);";
    $count += $count1;
}
mysqli_free_result($res);

if($status==98) {
    echo "<p>OK, you ignore all " . $count . " word(s)!</p>"; 
}
if($status==99) {
    echo "<p>OK, you know all " . $count . " word(s) well!</p>"; 
}

?>
<script type="text/javascript">
//<![CDATA[
var context = window.parent.frames['l'].document;
var contexth = window.parent.frames['h'].document;
<?php echo $javascript; ?> 
$('#learnstatus', contexth).html('<?php echo addslashes(texttodocount2($_REQUEST['text'])); ?>');
window.parent.frames['l'].setTimeout('cClick()', 1000);
//]]>
</script>
<?php

pageend();

?>
