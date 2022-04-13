<?php

/**
 * \file
 * \brief Utility for calling system speech synthesizer
 * 
 * @package Lwt
 * @author chaosarium <leonluleonlu@gmail.com>
 *          HugoFara <Hugo.Farajallah@protonmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @since 2.2.2-fork
 */

require_once 'inc/session_utility.php';
require_once 'inc/langdefs.php';

/**
 * String to population a SELECT tag.
 * 
 * @return string HTML-formatted string
 * 
 * @global array $langDefs List of all languages.
 */
function tts_language_options()
{
    global $langDefs;
    $output = '';
    foreach (get_languages() as $language => $language_id) {
        /** Two-letter language code from from language name (e. g. : "English" = > "en" ) */
        $languageCode = $langDefs[$language][1];
        $output .= '<option value="' . $languageCode . '">' . 
        $language . 
        '</option>';
    }
    return $output;
}

/**
 * Prepare a from for all the TTS settings.
 * 
 * @return void
 */
function tts_settings_form()
{
?>    
<form class="validate" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <table class="tab3" cellspacing="0" cellpadding="5">
        <tr>
            <th class="th1">Group</th>
            <th class="th1">Description</th>
            <th class="th1" colspan="2">Value</th>
        </tr>
        <tr>
            <th class="th1 center" rowspan="2">Language</th>
            <td class="td1 center">Language code</td>
            <td class="td1 center">
            <select name="LgID" id="get-language" class="notempty" onchange="populateVoiceList();">
                <?php echo tts_language_options(); ?>
            </select>
            </td>
            <td class="td1 center">
                <img src="<?php print_file_path("icn/status-busy.png") ?>" title="Field must not be empty" alt="Field must not be empty" />
            </td>
        </tr>
        <tr>
            <td class="td1 center">Region (depending on your browser)</td>
            <td class="td1 center">
                <select name="LgRegName" id="region-code" class="notempty">
                </select>
            </td>
            <td class="td1 center">
                <img src="<?php print_file_path("icn/status-busy.png") ?>" title="Field must not be empty" alt="Field must not be empty" />
            </td>
        </tr>
        <tr>
            <th class="th1 center" rowspan="2">Voice</th>
            <td class="td1 center">Reading Rate</td>
            <td class="td1 center">
                <input type="range" name="LgTTSRate" min="0.5" max="2" value="1" step="0.1" id="rate">
            </td>
            <td class="td1 center">
                <img src="<?php print_file_path("icn/status.png") ?>" />
            </td>
        </tr>
        <tr>
            <td class="td1 center">Pitch</td>
            <td class="td1 center">
                <input type="range" name="LgTTSPitch" min="0" max="2" value="1" step="0.1" id="pitch">
            </td>
            <td class="td1 center">
                <img src="<?php print_file_path("icn/status.png") ?>" />
            </td>
        </tr>
        <tr>
            <?php tts_demo(); ?>
        </tr>
        <tr>
            <td class="td1 right" colspan="4">
                <input type="button" value="Cancel" onclick="{resetDirty(); location.href='tts.php';}" /> 
                <input type="submit" name="op" value="Save" />
            </td>
        </tr>
    </table>
</form>
<?php
}

/**
 * Prepare a demo for TTS.
 * 
 * @return void
 */
function tts_demo()
{
?>
<th class="th1 center">Demo</th>
<td class="td1 center" colspan="2">
    <textarea id="tts-demo" title="Enter your text here" style="width: 95%;">
    Lorem ipsum dolor sit amet...
    </textarea>
</td>
<td class="td1 right">
    <button onclick="readingDemo();">Read</button>
</td>
<?php
}

/**
 * Prepare the JavaScript content for text-to-speech.
 * 
 * @return void
 */
function tts_js()
{
?>
    <script type="text/javascript" charset="utf-8">
        /**
         * Get the language country code from the page. 
         * 
         * @returns {string} Language code (e. g. "en")
         */
        function getLanguageCode()
        {
            return $('#get-language')[0].value;
        }

        /**
         * Get the language region code from the page.
         * 
         * @returns {string} Region code (e. g. "US")
         */
        function getRegionCode()
        {
            return $('#region-code')[0].value;
        }

        /** 
         * Gather data in the page to read the demo.
         * 
         * @returns {undefined}
         */
        function readingDemo()
        {
            let lang = 
            readTextAloud(
                $('#tts-demo')[0].value,
                getLanguageCode + (getRegionCode() ? '-' + getRegionCode() : ''),
                $('#rate')[0].value,
                $('#pitch')[0].value
            );
        }

        /**
         * Populate the languages region list.
         * 
         * @returns {undefined}
         */
        function populateVoiceList() {
            voices = window.speechSynthesis.getVoices();
            $('#region-code')[0].innerHTML = '';
            const languageCode = getLanguageCode();
            for (i = 0; i < voices.length ; i++) {
                if (voices[i].lang != languageCode && !voices[i].default)
                    continue;
                let option = document.createElement('option');
                option.textContent = voices[i].name;

                if (voices[i].default) {
                    option.textContent += ' -- DEFAULT';
                }

                option.setAttribute('data-lang', voices[i].lang);
                option.setAttribute('data-name', voices[i].name);
                $('#region-code')[0].appendChild(option);
            }
        }

        $(populateVoiceList);
    </script>
<?php
}

/**
 * Make only a partial, embadable page for text-to-speech settings.
 * 
 * @return void
 */
function tts_settings_minimal_page()
{
    tts_settings_form();
    tts_demo();
    tts_js();
}

/**
 * Make the complete HTML page for text-to-speech settings.
 * 
 * @return void
 */
function tts_settings_full_page()
{
    pagestart('Text-to-Speech Settings', true);
    tts_settings_minimal_page();
    pageend();
}

function tts_save_settings()
{
    global $tbpref;
    // Get old values
    $sql = 
    "SELECT * 
    FROM " . $tbpref . "languages 
    WHERE LgID = " . $_REQUEST["LgID"];
    $res = do_mysqli_query($sql);
    $record = mysqli_fetch_assoc($res);
    if ($record == false) {
        my_die("Cannot access language data: $sql"); 
    }
    $oldLgRegID = $record['LgRegName'];
    $oldLgTTSRate = $record['LgTTSRate'];
    $oldLgTTSPitch = $record['LgTTSPitch'];
    $message = runsql(
        'UPDATE ' . $tbpref . 'languages SET ' . 
        'LgRegName = ' . convert_string_to_sqlsyntax($_REQUEST["LgRegName"]) . ', ' . 
        'LgTTSRate = ' . convert_string_to_sqlsyntax($_REQUEST["LgTTSRate"]) . ', ' .
        'LgTTSPitch = ' . convert_string_to_sqlsyntax($_REQUEST["LgTTSPitch"]) . 
        ' WHERE LgID = ' . $_REQUEST["LgID"], 
        'Updated'
    );
    mysqli_free_result($res);
}

if ($_REQUEST['op'] == 'Change') {
    tts_save_settings();
} else {
    tts_settings_full_page();
}
?>



