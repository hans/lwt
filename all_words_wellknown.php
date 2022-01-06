<?php

/**
 * \file
 * \brief Setting all unknown words to Well Known (99)
 * 
 * Call: all_words_wellknown.php?text=[textid]
 * 
 * @package Lwt
 * @author  LWT Project <lwt-project@hotmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/all__words__wellknown_8php.html
 * @since   1.0.3
 */

require_once 'inc/session_utility.php';

$status = $_REQUEST['stat'];
$langid = get_first_value(
    "SELECT TxLgID AS value 
    FROM " . $tbpref . "texts 
    WHERE TxID = " . $_REQUEST['text']
);

if ($status == 98) {
    pagestart("Setting all blue words to Ignore", false); 
} else if ($status == 99) {
    pagestart("Setting all blue words to Well-known", false); 
}

$sql = 'SELECT DISTINCT TiText, TiTextLC 
FROM (' . $tbpref . 'textitems 
LEFT JOIN ' . $tbpref . 'words ON (TiTextLC = WoTextLC) AND (TiLgID = WoLgID)) 
WHERE TiIsNotWord = 0 AND WoID IS NULL AND TiWordCount = 1 AND TiTxID = ' . $_REQUEST['text'] . ' 
ORDER BY TiOrder';
$res = do_mysqli_query($sql);
$count = 0;
$javascript = "var title='';";
$sqlarr = null;
while ($record = mysqli_fetch_assoc($res)) {
    $term = $record['TiText'];    
    $termlc = $record['TiTextLC'];    
    $count1 = (int)runsql(
        'INSERT INTO ' . $tbpref . 'words (
            WoLgID, WoText, WoTextLC, WoStatus, WoStatusChanged,' 
            . make_score_random_insert_update('iv') . 
        ') 
        values( ' . 
            $langid . ', ' . 
            convert_string_to_sqlsyntax($term) . ', ' . 
            convert_string_to_sqlsyntax($termlc) . ', 1, '.$status.' , NOW(), ' .  
            make_score_random_insert_update('id') 
        . ')', 
        ''
    );
    $wid = get_last_key();
    $sqlarr[]= ' WHEN ' . convert_string_to_sqlsyntax_notrim_nonull($termlc) . ' THEN ' . $wid;
    if (getSettingWithDefault('set-tooltip-mode') == 1 && $count1 > 0) {
        $javascript .= "title = make_tooltip(" . prepare_textdata_js($term) . ",'*','','".$status."');";
    }
    $javascript .= "$('.TERM" . strToClassName($termlc) . "', context)
    .removeClass('status0')
    .addClass('status".$status." word" . $wid . "')
    .attr('data_status', '".$status."')
    .attr('data_wid', '" . $wid . "')
    .attr('title', title);";
    $count += $count1;
}
mysqli_free_result($res);

if ($status == 98) {
    echo "<p>OK, you ignore all " . $count . " word(s)!</p>"; 
} else if($status == 99) {
    echo "<p>OK, you know all " . $count . " word(s) well!</p>"; 
}

?>
<script type="text/javascript">
    //<![CDATA[
    var contexth = window.parent.frames['h'].document;
    <?php echo $javascript; ?> 
    $('#learnstatus', contexth).html('<?php echo addslashes(texttodocount2($_REQUEST['text'])); ?>');
    window.parent.frames['l'].setTimeout('cClick()', 1000);
    //]]>
</script>
<?php

pageend();

?>
