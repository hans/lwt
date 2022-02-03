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

$sql = 'SELECT DISTINCT Ti2Text, Ti2TextLC 
FROM (' . 
    $tbpref . 'textitems2 
    LEFT JOIN ' . $tbpref . 'words ON (Ti2TextLC = WoTextLC) AND (Ti2LgID = WoLgID)
) 
WHERE Ti2IsNotWord = 0 AND WoID IS NULL AND Ti2WordCount = 1 AND Ti2TxID = ' . $_REQUEST['text'] . ' 
ORDER BY Ti2Order';
$res = do_mysqli_query($sql);
$count = 0;
$javascript = "var title='';";
$sqlarr = null;
while ($record = mysqli_fetch_assoc($res)) {
    $term = $record['Ti2Text'];
    $termlc = $record['Ti2TextLC'];    
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
    const context = window.parent.document;
    <?php echo $javascript; ?> 
    $('#learnstatus', context).html('<?php echo addslashes(texttodocount2($_REQUEST['text'])); ?>');
    window.parent.setTimeout('cClick()', 1000);
    //]]>
</script>
<?php

pageend();

?>
