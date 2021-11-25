<?php

/**
 * \file
 * \brief Responsible for drawing the header when reading texts
 * 
 * @author https://github.com/HugoFara/lwt/graphs/contributors GitHub contributors
 * @since  1.0.3
 * 
 * Call: do_text_header.php?text=[textid]
 */

require_once 'inc/session_utility.php';
// To get the BCP 47 language tag
require_once 'inc/langdefs.php' ;

/**
 * Get the text and language data associated with the text.
 * 
 * @param string $textid ID of the text
 * @global string $tbpref Table name prefix
 * @since 2.0.3-fork
*/
function getData($textid) {
    global $tbpref;
    $sql = 
    'SELECT LgName, TxLgID, TxText, TxTitle, TxAudioURI, TxSourceURI, TxAudioPosition 
    FROM ' . $tbpref . 'texts 
    JOIN ' . $tbpref . 'languages 
    ON TxLgID = LgID 
    WHERE TxID = ' . $textid;
    $res = do_mysqli_query($sql);
    $record = mysqli_fetch_assoc($res);
    mysqli_free_result($res);
    return $record;
}

/**
 * Main function for displaying header. It will print HTML content.
 * 
 * @param string $textid ID of the requiered text
 * @param bool $only_body If true, only show the inner body. If false, create a complete HTML document. 
 * @global array $langDefs Language definitions
 */
function do_text_header_content($textid, $only_body=true) {
    global $langDefs;

    $record = getData($textid);
    $media = $record['TxAudioURI'];
    if (!isset($media)) { 
        $media = '';
    }
    $media = trim($media);
    
    $title = $record['TxTitle'];
    $sourceURI = $record['TxSourceURI'];
    $langid = $record['TxLgID'];
    $pos = $record['TxAudioPosition'];
    
    
    // User settings
    $showAll = getSettingZeroOrOne('showallwords', 1);
    $showLearning = getSettingZeroOrOne('showlearningtranslations', 1);
    
    /** @var string $languageCode BCP 47 language tag, for istance "en" */
    $languageCode = $langDefs[$record["LgName"]][1];
    /** @var string $phoneticText Phonetic reading for this text */
    $phoneticText = phonetic_reading($record["TxText"], $languageCode);
    
    saveSetting('currenttext', $textid);

    if (!$only_body) {
        pagestart_nobody(tohtml($title), $addcss='html, body {margin-bottom:0;}');
    }
?>
<h4>
    <a href="edit_texts.php" target="_top">
        <?php echo_lwt_logo(); ?> LWT
    </a>
    &nbsp; | &nbsp;
    <?php 
    quickMenu();
    echo getPreviousAndNextTextLinks(
        $textid, 'do_text.php?start=', false, '&nbsp; | &nbsp;'
    );
    ?>&nbsp; | &nbsp;
    <a href="do_test.php?text=<?php echo $textid; ?>" target="_top">
        <img src="icn/question-balloon.png" title="Test" alt="Test" />
    </a>&nbsp;
    <a href="print_text.php?text=<?php echo $textid; ?>" target="_top">
        <img src="icn/printer.png" title="Print" alt="Print" />
    </a>
    <?php echo get_annotation_link($textid); ?>&nbsp;
    <a target="_top" href="edit_texts.php?chg=<?php echo $textid; ?>">
        <img src="icn/document--pencil.png" title="Edit Text" alt="Edit Text" />
    </a>&nbsp; | &nbsp;
    <a href="new_word.php?text=<?php echo $textid; ?>&amp;lang=<?php echo $langid; ?>" target="ro">
        <img src="icn/sticky-note--plus.png" title="New Term" alt="New Term" />
    </a>
</h4>
<table>
    <tr>
        <td>
            <h3>READ&nbsp;▶</h3>
        </td>
        <td class="width99pc">
            <h3>
                <?php echo tohtml($title);
                if (isset($sourceURI) && substr(trim($sourceURI), 0, 1) != '#') { 
                    ?><a href="<?php echo $sourceURI ?>" target="_blank">
                        <img src="<?php echo get_file_path('icn/chain.png') ?>" title="Text Source" alt="Text Source" />
                    </a>
                    <?php 
                } 
                ?>
            </h3>
        </td>
    </tr>
</table>
<table class="width99pc">
    <tr>
        <td>TO DO:
            <span id="learnstatus"><?php echo texttodocount2($textid); ?></span>
        </td>
        <td 
        title="[Show All] = ON: ALL terms are shown, and all multi-word terms are shown as superscripts before the first word. The superscript indicates the number of words in the multi-word term.
[Show All] = OFF: Multi-word terms now hide single words and shorter or overlapping multi-word terms.">
            Show All&nbsp;
            <input type="checkbox" id="showallwords" <?php echo get_checked($showAll); ?> />
        </td>
        <td 
        title="[Learning Translations] = ON: Terms with Learning Level&nbsp;1 display their translations under the term.
[Learning Translations] = OFF: No translations are shown in the reading mode.">
            Learning Translations&nbsp;
            <input type="checkbox" id="showlearningtranslations" <?php echo get_checked($showLearning); ?> />
        </td>
        <td id="thetextid" class="hide"><?php echo $textid; ?></td>
        <td><button id="readTextButton">Read in browser</button></td>
    </tr>

<?php makeMediaPlayer($media, $pos); ?>

</table>
<script type="text/javascript">

    /// Main object for text-to-speech interaction with SpeechSynthesisUtterance
    var text_reader = {
        /// The text to read
        text: `<?php echo htmlentities($phoneticText); ?>`,

        /// {string} ISO code for the language
        lang: '<?php echo $languageCode; ?>',

        /// {string} Rate at wich the speech is done
        rate: 0.8,

        /**
         * Reads a text using the browser text reader.
         */
        readTextAloud: function () {
            var msg = new SpeechSynthesisUtterance(this.text);
            msg.text = this.text;
            msg.lang = this.lang;
            msg.rate = this.rate;
            window.speechSynthesis.speak(msg);
        },

    };

    // Check browser compatibility before reading
    function init_reading() {
        if (!('speechSynthesis' in window)) {
            alert('Your browser does not support speechSynthesis!');
        } else {
            text_reader.readTextAloud();
        }
    }

    /**
     * Save audio position
     */
    function saveAudioPosition() {
        if ($("#jquery_jplayer_1") === null) {
            return;
        }
        var pos = $("#jquery_jplayer_1").data("jPlayer").status.currentTime;
        $.ajax({
            type: "POST",
            url:'inc/ajax_save_text_position.php', 
            data: { 
                id: '<?php echo $_REQUEST['text']; ?>', 
                audioposition: pos
            }, 
            async: false
        });
    }
    
    $(window).on('beforeunload', saveAudioPosition);

    // We need to capture the text-to-speach event manually for Chrome
    $(document).ready(function() {
        $('#readTextButton').click(init_reading)
    });
</script>
<?php
    if (!$only_body) {
        pageend();
    }
}

// Show the content automatically if text is in the request
if (getreq('text')) {
    do_text_header_content(getreq('text'), false);
}
?>