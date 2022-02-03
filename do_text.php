<?php

/**
 * \file
 * \brief Start Reading a text (frameset)
 * 
 * Call: do_text.php?text=[textid]
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
 * Get text ID (if possible).
 * 
 * Text ID if first looked at int the 'text' parameter. If not found, then look at 'start'.
 * 
 * @return int|null Text ID or null
 */
function get_text_id() {
    if (isset($_REQUEST['text']) && is_integer($_REQUEST['text'])) {
        return (int)$_REQUEST['text'];
    }
    if (isset($_REQUEST['start']) && is_integer($_REQUEST['start'])) {
        return (int)$_REQUEST['start'];
    }
    return null;
}

/**
 * Echo the page content for the mobile version of do_text.
 * 
 * @param int $textid Text ID
 * 
 * @return void
 */
function do_text_mobile_content($textid) {
?>
<div id="frame-h">
    <iframe id="frame-h-2" 
    src="do_text_header.php?text=<?php echo $textid; ?>" 
    scrolling="yes" name="h">
    </iframe>
</div>
<div id="frame-ro">
<iframe id="frame-ro-2" src="empty.html" scrolling="yes" name="ro"></iframe>
</div>
<div id="frame-l">
    <iframe id="frame-l-2" 
    src="do_text_text.php?text=<?php echo $textid; ?>" scrolling="yes" name="l">
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
 * @param int         $textid Text ID
 * @param string|null $audio  Audio URI
 * 
 * @return void
 */
function do_text_desktop_content($textid, $audio) {
?>
<div style="width: 95%; height: 100%;">
    <div id="frame-h">
        <?php do_text_header_content($textid, true); ?>
    </div>
    <hr />
    <div id="frame-l">
        <?php do_text_text_content($textid, true); ?>
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
 * @param int  $textit Text ID
 * @param bool $mobile Set to true if you want the mobile version of the page.
 * 
 * @return void
 * 
 * @global string $tbpref Database table prefix.
 */
function do_text_page($textid, $mobile)
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
        do_text_mobile_content($textid);
    } else {
        // Not mobile
        do_text_desktop_content($textid, $audio);
    }
    pageend();
}

if (get_text_id() !== null) {
    do_text_page(get_text_id(), is_mobile());
} else {
    // Document not ready
    header("Location: edit_texts.php");
    exit();
}

?>