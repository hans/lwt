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
 * Two-letter language code from from language name (e. g. : "English" = > "en" ).
 * 
 * @return string Two-letter language name
 */
function get_language_code($language)
{
    global $langDefs;
    return $langDefs[$language][1];
}

/**
 * Return the language ID from a two-letter language code or a BCP 47 tag.
 * 
 * If two languages have the same country name, only thte first one will be returned.
 * 
 * @param string $code Two letters, or four letters separated with caret ("fr" or "en-US").
 * 
 * @return int Language ID if found, -1 otherwise.
 */
function language_id_from_code($code)
{
    global $langDefs;
    $trimmed = substr($code, 0, 2);
    foreach (get_languages() as $language => $language_id) {
        $elem = $langDefs[$language];
        if ($elem[0] == $trimmed) {
            return $language_id;
        }
    }
    return -1;
}

/**
 * String to population a SELECT tag.
 * 
 * @return string HTML-formatted string
 * 
 * @global array $langDefs List of all languages.
 */
function tts_language_options()
{
    $output = '';
    foreach (get_languages() as $language => $language_id) {
        $languageCode = get_language_code($language);
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
            <select name="LgName" id="get-language" class="notempty" onchange="populateVoiceList();">
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
                <input type="range" name="LgPitch" min="0" max="2" value="1" step="0.1" id="pitch">
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
    $lid = (int) getSetting('currentlanguage');
    $current_language = getLanguage((string) $lid);
?>
<script type="text/javascript" charset="utf-8">
    /** @var Current language being learnt. */
    const CURRENT_LANGUAGE = <?php echo json_encode(get_language_code($current_language)); ?>;

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
     * Set the Text-to-Speech data using cookies
     */
    function presetTTSData()
    {
        //console.log();
        $('#get-language')[0].value = CURRENT_LANGUAGE;
        $('#region-code')[0].value = getCookie('tts[' + CURRENT_LANGUAGE + 'RegName]');
        $('#rate')[0].value = getCookie('tts[' + CURRENT_LANGUAGE + 'Rate]');
        $('#pitch')[0].value = getCookie('tts[' + CURRENT_LANGUAGE + 'Pitch]');
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

    $(presetTTSData);
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
    tts_js();
}

/**
 * Make the complete HTML page for text-to-speech settings.
 * 
 * @return void
 */
function tts_settings_full_page($message)
{
    pagestart('Text-to-Speech Settings', true);
    if ($message != '') {
        error_message_with_hide($message, false);
        if ($_COOKIE[$message]) {
            error_message_with_hide('Text-to-Speech settings saved!', false);
        } else {
            error_message_with_hide('Error: Unable to set cookies', false);
        }
    }
    tts_settings_minimal_page();
    pageend();
}

/**
 * Save the text-to-speech settings as cookies.
 * 
 * @param array $form Inputs from the main form.
 * 
 * @return string A confirmation or an error message
 */
function tts_save_settings($form)
{
    $lgname = $form['LgName'];
    $prefix = 'tts[' . $lgname;
    /* Could be useful if problems with cookies
    $params = session_get_cookie_params();
    'domain' => isset($params['domain']),
    'secure' => isset($params['secure']),
    'httponly' => isset($params['httponly'])
    */
    $cookie_options = array (
        'expires' => strtotime( '+5 years' ),
        'domain' => ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false,
        'path' => '/',
        'samesite' => 'Strict' // None || Lax || Strict
    );
    //setcookie($prefix . ']', $record['LgID'], $cookie_options);
    setcookie($prefix . 'RegName]', $form['LgRegName'], $cookie_options);
    setcookie($prefix . 'Rate]', $form['LgTTSRate'], $cookie_options);
    setcookie($prefix . 'Pitch]', $form['LgPitch'], $cookie_options);
    return $prefix . 'RegName]';
}

$message = '';
if ($_REQUEST['op'] == 'Save') {
    $message = tts_save_settings($_REQUEST);
}
tts_settings_full_page($message);

?>
