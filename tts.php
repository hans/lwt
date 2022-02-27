<?php

/**
 * \file
 * \Utility for calling system speech synthesizer
 * 
 * @author chaosarium
 * @since 2.2.2-fork (February 13 2022)
 */

$tts_lang = 'fr-CA';
$tts_rate = 1.0;

?>

<script>

    console.log('tts_lang: ' + '<?php echo $tts_lang; ?>')
    console.log('tts_rate: ' + '<?php echo $tts_rate; ?>')

    function readTextAloud(text) {
        console.log('trying to read: ' + text)
        var msg = new SpeechSynthesisUtterance()
        msg.text = text
        msg.lang = "<?php echo $tts_lang;?>"
        msg.rate = "<?php echo $tts_rate;?>"
        window.speechSynthesis.speak(msg)
    }

    function applyTTS() {
        document.querySelectorAll('.textToSpeak, #textToSpeak, span.click.word.wsty').forEach(item => {
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

</script>



