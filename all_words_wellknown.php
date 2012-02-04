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
Call: all_words_wellknown.php?text=[textid]
Setting all unknown words to Well Known (99)
***************************************************************/

require 'lwt-startup.php';

$langid = get_first_value("select TxLgID as value from texts where TxID = " . $_REQUEST['text']);

pagestart("Setting all blue words to Well-known",false);

$records = db_get_rows('SELECT DISTINCT TiText, TiTextLC
    FROM ( textitems
           LEFT JOIN words ON TiTextLC = WoTextLC
               AND TiLgID = WoLgID )
    WHERE TiIsNotWord = 0 AND WoID IS NULL AND TiWordCount = 1
        AND TiTxID = ?
    ORDER BY TiOrder', $_REQUEST['text']);

$count = 0;
$javascript = "var title='';";

$insert_word = $lwt_db->prepare('INSERT INTO words
    ( WoLgID, WoText, WoTextLC, WoStatus, WoStatusChanged, WoTodayScore,
      WoTomorrowScore, WoRandom )
    VALUES ( :language_id, :term, :term_lc, 99, NOW(), :today_score,
             :tomorrow_score, RAND() )');

foreach ( $records as $record ) {
    $term = $record['TiText'];
    $termlc = $record['TiTextLC'];

    $insert_word->execute(array('language_id' => $langid,
                                'term' => $term,
                                'term_lc' => $termlc,
                                'today_score' => getsqlscoreformula(2),
                                'tomorrow_score' => getsqlscoreformula(3)));

    $wid = get_last_key();

    $javascript .= "title = make_tooltip(" . prepare_textdata_js($term) . ",'*','','99');";
		$javascript .= "$('.TERM" . strToClassName($termlc) . "', context).removeClass('status0').addClass('status99 word" . $wid . "').attr('data_status','99').attr('data_wid','" . $wid . "').attr('title',title);";

    $count += 1;
}

echo "<p>OK, you know all " . $count . " word(s) well!</p>";

?>
<script type="text/javascript">
    //<![CDATA[
    var context = window.parent.frames['l'].document;
var contexth = window.parent.frames['h'].document;
<?php echo $javascript; ?>
$('#learnstatus', contexth).html('<?php echo texttodocount2($_REQUEST['text']); ?>');
window.parent.frames['l'].setTimeout('cClick()', 1000);
//]]>
</script>
<?php

pageend();

?>