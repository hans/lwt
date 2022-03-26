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

function tts_get_languages()
{
    global $tbpref;
    $sql = 
    'SELECT LgID, LgName 
    FROM ' . $tbpref . 'languages 
    WHERE LgName<>"" 
    ORDER BY LgName';
    $res = do_mysqli_query($sql);
    return $res;
    $languages = mysqli_fetch_assoc($res);
    mysqli_free_result($res);
    return $languages;
}

function tts_language_options()
{
    global $tbpref;
    $sql = 
    'SELECT LgID, LgName 
    FROM ' . $tbpref . 'languages 
    WHERE LgName<>""';
    $res = do_mysqli_query($sql);
    $i = 0;
    while ($language = mysqli_fetch_assoc($res) && $i < 100) {
        echo '<option value="' . $language['LgID'] . '">' . 
        $language['LgName'] . 
        '</option>';
        $i++;
    }
    //mysqli_free_result($languages;
    echo '<div>' . $i . '</div>';
    mysqli_free_result($res);
}

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
            <select name="get-language" id="get-language" class="notempty">
                <?php tts_language_options(); ?>
            </select>
            </td>
            <td class="td1 center">
                <img src="<?php print_file_path("icn/status-busy.png") ?>" title="Field must not be empty" alt="Field must not be empty" />
            </td>
        </tr>
        <tr>
            <td class="td1 center">Region (depending on your browser)</td>
            <td class="td1 center">
                <select name="region-code" id="region-code" class="notempty">
                </select>
            </td>
            <td class="td1 center">
                <img src="<?php print_file_path("icn/status-busy.png") ?>" title="Field must not be empty" alt="Field must not be empty" />
            </td>
        </tr>
        <tr>
            <th class="th1 center" rowspan="3">Voice</th>
            <td class="td1 center">Voice Selection</td>
            <td class="td1 center">
            <select name="set-voice" id="set-voice" class="notempty">
            </select>
            </td>
            <td class="td1 center">
                <img src="<?php print_file_path("icn/status-busy.png") ?>" title="Field must not be empty" alt="Field must not be empty" />
            </td>
        </tr>
        <tr>
            <td class="td1 center">Reading Rate</td>
            <td class="td1 center">
                <input type="range" min="0.5" max="2" value="1" step="0.1" id="rate">
            </td>
            <td class="td1 center">
                <img src="<?php print_file_path("icn/status-busy.png") ?>" title="Field must not be empty" alt="Field must not be empty" />
            </td>
        </tr>
        <tr>
            <td class="td1 center">Pitch</td>
            <td class="td1 center">
                <input type="range" min="0" max="2" value="1" step="0.1" id="pitch">
            </td>
            <td class="td1 center">
                <img src="<?php print_file_path("icn/status-busy.png") ?>" title="Field must not be empty" alt="Field must not be empty" />
            </td>
        </tr>
    </table>
</form>    
<?php
}

function tts_demo()
{
?>
<textarea id="tts-demo" title="Enter your text here">
    Lorem ipsum dolor sit amet...
</textarea>
<button onclick="readingDemo();">Read</button>
<?php
}

function tts_settings_full_page()
{
    pagestart('Text-to-Speech Settings', true);
    ?>
    <script type="text/javascript" src="js/user_interactions.js" charset="utf-8"></script>
    <?php
    tts_settings_form();
    tts_demo();
    pageend();
}

tts_settings_full_page();
?>


<script type="text/javascript">

    function readingDemo()
    {
        let lang = 
        readTextAloud(
            $('#tts-demo').text(),
            'la',
            0.8
        );
    }

    function applyTTS() {
        document
        .querySelectorAll('.textToSpeak, #textToSpeak, span.click.word.wsty')
        .forEach(item => {
            console.log("added listener")
            item.addEventListener('click', event => {
                console.log("this is great")
                readTextAloud(item.textContent)
            })
        })
    }

    // from the do_test_test.php implementation
    function read_word() {
        if (('speechSynthesis' in window) && 
        document.getElementById('utterance-allowed').checked) {
            const text = <?php echo json_encode($phoneticText); ?>;
            let msg = new SpeechSynthesisUtterance(text);
            msg.text = text;
            msg.lang = <?php echo json_encode($abbr); ?>;
            msg.rate = 0.8;
            speechSynthesis.speak(msg);
        }
    }

    function populateVoiceList() {
        voices = window.speechSynthesis.getVoices();

        for(i = 0; i < voices.length ; i++) {
            if (voices[i].lang != $('#'))
            let option = document.createElement('option');
            option.textContent = voices[i].name + ' (' + voices[i].lang + ')';

            if(voices[i].default) {
                option.textContent += ' -- DEFAULT';
            }

            option.setAttribute('data-lang', voices[i].lang);
            option.setAttribute('data-name', voices[i].name);
            $('#set-voice')[0].appendChild(option);
        }
    }

    $(populateVoiceList);

</script>



