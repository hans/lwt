<?php

/**
 * \file
 * Responsible for drawing the header when reading texts
 * 
 * @author https://github.com/HugoFara/lwt/graphs/contributors GitHub contributors
 * 
 * Call: do_text_header.php?text=[textid]
 */

require_once 'inc/session_utility.php';
// To get the BCP 47 language tag
require_once 'langdefs.inc.php' ;

$textid = getreq('text');
$sql = 'select LgName, TxLgID, TxText, TxTitle, TxAudioURI, TxSourceURI, TxAudioPosition 
from ' . $tbpref . 'texts 
join ' . $tbpref . 'languages 
on TxLgID = LgID 
where TxID = ' . $textid;
$res = do_mysqli_query($sql);
$record = mysqli_fetch_assoc($res);

$audio = $record['TxAudioURI'];
if(!isset($audio)) { $audio=''; 
}
$audio=trim($audio);

$title = $record['TxTitle'];
$sourceURI = $record['TxSourceURI'];
$langid = $record['TxLgID'];
$pos = $record['TxAudioPosition'];
mysqli_free_result($res);

saveSetting('currenttext', $textid);

pagestart_nobody(tohtml($title), $addcss = 'html, body {margin-bottom:0;}');
echo '<h4>';
echo '<a href="edit_texts.php" target="_top">';
echo_lwt_logo();
echo 'LWT';
echo '</a>&nbsp; | &nbsp;';
quickMenu();
echo getPreviousAndNextTextLinks($textid, 'do_text.php?start=', false, '&nbsp; | &nbsp;');
echo '&nbsp; | &nbsp;<a href="do_test.php?text=' . $textid 
. '" target="_top"><img src="icn/question-balloon.png" title="Test" alt="Test" /></a> &nbsp;
<a href="print_text.php?text=' . $textid . '" target="_top">
<img src="icn/printer.png" title="Print" alt="Print" />' 
. get_annotation_link($textid) . ' &nbsp;
<a target="_top" href="edit_texts.php?chg=' . $textid . '">
<img src="icn/document--pencil.png" title="Edit Text" alt="Edit Text" /></a>&nbsp; | &nbsp;
<a href="new_word.php?text=' . $textid . '&amp;lang=' . $langid . '" target="ro">
<img src="icn/sticky-note--plus.png" title="New Term" alt="New Term" /></a>';
echo '</h4><table><tr><td><h3>READ&nbsp;▶</h3></td>
<td class="width99pc"><h3>' . tohtml($title) . (isset($sourceURI) && substr(trim($sourceURI), 0, 1)!='#' ? ' <a href="' . $sourceURI . '" target="_blank"><img src="'.get_file_path('icn/chain.png').'" title="Text Source" alt="Text Source" /></a>' : '') . '</h3></td></tr></table>';

$showAll = getSettingZeroOrOne('showallwords', 1);
$showLearning = getSettingZeroOrOne('showlearningtranslations', 1);

?>
<script type="text/javascript">
    /// Main object for text-to-speech interaction with SpeechSynthesisUtterance
    var text_reader = {
        /// The text to read
        text: `<?php echo htmlentities(phonetic_reading($record["TxText"], $langDefs[$record["LgName"]][1])); ?>`,

        /// {string} ISO code for the language
        lang: '<?php global $langDefs; echo htmlentities($langDefs[$record["LgName"]][1]); ?>',

        /// {string} Rate at wich the speech is done
        rate: 0.8,

        /**
         * Reads a text using the browser text reader.
         */
        readTextAloud: function () {
            var msg = new SpeechSynthesisUtterance();
            msg.text = this.text;
            msg.lang = this.lang;
            msg.rate = this.rate;
            window.speechSynthesis.speak(msg);
        },

    };

    // Check browser compatibility before reading
    function init_reading() {
        if (!('speechSynthesis' in window)) {
            alert('Your browser is not compatible with speechSynthesis!');
        } else {
            text_reader.readTextAloud();
        }
    }
</script>
<table class="width99pc">
    <tr>
        <td>TO DO:
            <span id="learnstatus"><?php echo texttodocount2($_REQUEST['text']); ?></span>
        </td>
        <td 
        title="[Show All] = ON: ALL terms are shown, and all multi-word terms are shown as superscripts before the first word. The superscript indicates the number of words in the multi-word term.
[Show All] = OFF: Multi-word terms now hide single words and shorter or overlapping multi-word terms.">Show All&nbsp;<input type="checkbox" id="showallwords" <?php echo get_checked($showAll); ?> /></td>
        <td 
        title="[Learning Translations] = ON: Terms with Learning Level&nbsp;1 display their translations under the term.
[Learning Translations] = OFF: No translations are shown in the reading mode.">
Learning Translations&nbsp;
<input type="checkbox" id="showlearningtranslations" <?php echo get_checked($showLearning); ?> /></td>
    <td id="thetextid" class="hide"><?php echo $textid; ?></td>
    <td><button id="readTextButton">Read in browser</button></td>
</tr>

<?php

makeMediaPlayer($audio, $pos);

?>
</table>
<script type="text/javascript">
<?php if ($audio != '') { ?>
$(window).on('beforeunload', function() {
    var pos = $("#jquery_jplayer_1").data("jPlayer").status.currentTime;
    $.ajax({
        type: "POST",
        url:'ajax_save_text_position.php', 
        data: { 
            id: '<?php echo $_REQUEST['text']; ?>', 
            audioposition: pos
        }, 
        async: false
    });
});
<?php 
} ?>
    // We need to capture the event manually for Chrome
    $(document).ready(function() {
        $('#readTextButton').click(init_reading)
    });
</script>
<?php
pageend();

?>
