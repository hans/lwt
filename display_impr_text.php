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
require_once 'display_impr_text_header.php';
require_once 'display_impr_text_text.php';

/**
 * Make the page content to display printed texts on mobile.
 * 
 * @param int    $textid Text ID
 * @param string $audio  Media URI
 * 
 * @return void
 * @deprecated 
 * @since 2.2.0 This function should not longer be used, and should cause issues. Use
 * do_desktop_display_impr_text instead.
 */
function do_mobile_display_impr_text($textid, $audio) {
    do_frameset_mobile_css();
    do_frameset_mobile_js($audio);
    do_frameset_mobile_page_content(
        "display_impr_text_header.php?text=" . $textid, 
        "display_impr_text_text.php?text=" . $textid, 
        false
    );
}

/**
 * Make the main page content to display printed texts for desktop.
 * 
 * @param int    $textid Text ID
 * @param string $audio  Media URI
 * 
 * @return void
 */
function do_desktop_display_impr_text($textid, $audio) {
    
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
        <?php do_diplay_impr_text_header_main($textid);?>
    </div>
    <hr />
    <div id="frame-l">
        <?php do_display_impr_text_text_main($textid); ?>
    </div>
</div>
    <?php
}

/**
 * Do the page to display printed text.
 * 
 * @param int $textid Text ID
 * 
 * @global string $tbpref Database table prefix
 * 
 * @return void
 */
function do_display_impr_text_page($textid) {
    global $tbpref;
    $audio = get_first_value(
        'SELECT TxAudioURI AS value FROM ' . $tbpref . 'texts 
        WHERE TxID = ' . $_REQUEST['text']
    );
    pagestart_nobody('Display');
    //framesetheader('Display');

    if (is_mobile()) {
        do_mobile_display_impr_text($textid, $audio);
    } else {
        do_desktop_display_impr_text($textid, $audio);
    }

    pageend();
}

if (isset($_REQUEST['text'])) {
    do_display_impr_text_page((int) getreq('text'));
} else {
    header("Location: edit_texts.php");
    exit();
}

?>