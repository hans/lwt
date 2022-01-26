<?php

/**
 * \file
 * \brief Display an improved annotated text (frame set)
 * 
 * Call: display_impr_text.php?text=[textid]
 * 
 * @package Lwt
 * @author  LWT Project <lwt-project@hotmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/display__impr__text_8php.html
 * @since   1.5.0
 */

require_once 'inc/session_utility.php'; 
require_once 'inc/mobile_interactions.php';

function do_mobile_display_impr_text($audio) {
    do_frameset_mobile_css();
    do_frameset_mobile_js($audio);

    ?>

<div id="frame-h">
    <iframe id="frame-h-2" src="display_impr_text_header.php?text=<?php echo $_REQUEST['text']; ?>" scrolling="yes" name="header">
    </iframe>
</div>
<div id="frame-l">
    <iframe id="frame-l-2" src="display_impr_text_text.php?text=<?php echo $_REQUEST['text']; ?>" scrolling="yes" name="text">
    </iframe>
</div>

    <?php 
}

function do_desktop_display_impr_text($audio) {
    
    ?>

<!--
<frameset border="3" bordercolor="" rows="<?php 
if (isset($audio)) { 
    echo (int)getSettingWithDefault('set-text-h-frameheight-with-audio')-90;
} else { 
    echo (int)getSettingWithDefault('set-text-h-frameheight-no-audio')-90;
} ?>,*">
    <frame src="display_impr_text_header.php?text=<?php echo $_REQUEST['text']; ?>" scrolling="no" name="header" />            
    <frame src="display_impr_text_text.php?text=<?php echo $_REQUEST['text']; ?>" scrolling="auto" name="text" />
</frameset>
<noframes><body><p>Sorry - your browser does not support frames.</p></body></noframes>
</frameset>
</html>-->
<div style="width: 95%; height: 100%;">
    <div id="frame-h">
        <?php require_once 'display_impr_text_header.php' ?>
    </div>
    <hr />
    <div id="frame-l">
        <?php require_once 'display_impr_text_text.php'; ?>
    </div>
</div>
    <?php
}

function do_display_impr_text_page() {
    global $tbpref;

    $audio = get_first_value(
        'SELECT TxAudioURI AS value FROM ' . $tbpref . 'texts 
        WHERE TxID = ' . $_REQUEST['text']
    );
    pagestart_nobody('Display');
    //framesetheader('Display');

    if (is_mobile() && false) {
        do_mobile_display_impr_text($audio);
    } else {
        do_desktop_display_impr_text($audio);
    }

    pageend();
}

if (isset($_REQUEST['text'])) {
    do_display_impr_text_page();
} else {
    header("Location: edit_texts.php");
    exit();
}

?>