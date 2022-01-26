<?php

/**
 * \file
 * \brief Start Reading a text (frameset)
 * 
 * Call: do_text.php?start=[textid]
 *      Create the main window when reading texts.
 * 
 * @package Lwt
 * @author  LWT Project <lwt-project@hotmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/do__text_8php.html
 * @since   1.0.3
 */

require_once 'inc/session_utility.php'; 
require_once 'inc/mobile_interactions.php';
require_once 'do_text_header.php';
require_once 'do_text_text.php';

/**
 * Echo the page content for the mobile version of do_text.
 * 
 * @return void
 */
function do_text_mobile_content() {
?>
<div id="frame-h">
    <iframe id="frame-h-2" 
    src="do_text_header.php?text=<?php echo $_REQUEST['start']; ?>" 
    scrolling="yes" name="h">
    </iframe>
</div>
<div id="frame-ro">
<iframe id="frame-ro-2" src="empty.html" scrolling="yes" name="ro"></iframe>
</div>
<div id="frame-l">
    <iframe id="frame-l-2" 
    src="do_text_text.php?text=<?php echo $_REQUEST['start']; ?>" scrolling="yes" name="l">
    </iframe>
</div>
<div id="frame-ru">
    <iframe id="frame-ru-2" src="empty.html" scrolling="yes" name="ru"></iframe>
</div>

<?php 
}

/**
 * Echo the page content for the desktop version of do_text.
 * 
 * @param string|null Audio URI
 * 
 * @return void
 */
function do_text_desktop_content($audio) {
?>
<div style="width: 95%; height: 100%;" onclick="setTimeout(hideRightFrames, 1000);">
    <div id="frame-h">
        <?php do_text_header_content($_REQUEST['start'], true); ?>
    </div>
    <hr />
    <div id="frame-l">
        <?php do_text_text_content($_REQUEST['start'], true); ?>
    </div>
</div>
<div id="frames-r" style="position: fixed; top: 0; right: -100%; width: 100%; height: 100%;" 
onclick="hideRightFrames();">
    <!-- iFrames wrapper for events -->
    <div style="margin-left: 50%; height: 99%;">
        <iframe src="empty.html" scrolling="auto" name="ro" style="height: 50%; width: 100%;">
            Your browser doesn't support iFrames, update it!
        </iframe>
        <iframe src="empty.html" scrolling="auto" name="ru" style="height: 50%; width: 100%;">
            Your browser doesn't support iFrames, update it!
        </iframe>
    </div>
</div>

<?php
}

/**
 * Echo the text page.
 * 
 * @param bool Set to true if you want the mobile version of the page.
 * 
 * @return void
 * 
 * @global string $tbpref Database table prefix.
 */
function do_text_page($mobile)
{
    global $tbpref;

    //framesetheader('Read');
    pagestart_nobody('Read');
    
    $audio = get_first_value(
        'SELECT TxAudioURI AS value 
        FROM ' . $tbpref . 'texts 
        WHERE TxID = ' . $_REQUEST['start']
    );
    
    if ($mobile && false) {
        do_frameset_mobile_css();
        do_frameset_mobile_js($audio);
        do_text_mobile_content();
    } else {
        // Not mobile
        do_text_desktop_content($audio);
    }
    pageend();
}

if (isset($_REQUEST['start'])) {
    do_text_page(is_mobile());
} else {
    // Document not ready
    header("Location: edit_texts.php");
    exit();
}

?>