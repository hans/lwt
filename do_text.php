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
require_once 'vendor/mobiledetect/mobiledetectlib/Mobile_Detect.php';
require_once 'do_text_header.php';
require_once 'do_text_text.php';

/**
 * Return true if we should use mobile mode.
 * 
 * @return bool Mobile mode shoud be activated or not
 */
function is_mobile()
{
    $detect = new Mobile_Detect;
    $mobileDisplayMode = (int)getSettingWithDefault('set-mobile-display-mode');
    $mobile = (
        ($mobileDisplayMode == 0 && $detect->isMobile()) 
        || $mobileDisplayMode == 2
    );
    return $mobile;
}


/**
 * Echo the CSS for the mobile version of do_text.
 * 
 * @return void
 */
function do_text_mobile_css() {
    ?>

<style type="text/css"> 
    body {
     background-color: #cccccc;
     margin: 0;
     overflow: hidden;
    }
    #frame-h, #frame-l, #frame-ro, #frame-ru {
     position:absolute; 
     overflow:scroll; 
     -webkit-overflow-scrolling: touch;
    }
    #frame-h-2, #frame-l-2, #frame-ro-2, #frame-ru-2 {
     display:inline-block;    
    }
</style>
<?php
}

/**
 * Echo the JS code for the mobile version of do_text.
 * 
 * @param string|null Audio URI
 * 
 * @return void
 */
function do_text_mobile_js($audio) {

?>    
<script type="text/javascript" src="js/jquery.js" charset="utf-8"></script>

<script type="text/javascript">
   //<![CDATA[
   function rsizeIframes() {
        var h_height = <?php 
        if (isset($audio)) {
            getSettingWithDefault('set-text-h-frameheight-with-audio');
        } else {
            getSettingWithDefault('set-text-h-frameheight-no-audio');
        } ?> + 10;
        var lr_perc = <?php echo getSettingWithDefault('set-text-l-framewidth-percent'); ?>;
        var r_perc = <?php echo getSettingWithDefault('set-text-r-frameheight-percent'); ?>;
        var w = $(window).width();
        var h = $(window).height();
        var l_width = w*lr_perc/100;
        var r_width = w - l_width;
        var l_height = h - h_height;
        var ro_height = h*r_perc/100;
        var ru_height = h - ro_height;
        $('#frame-h').width(l_width-5).height(h_height-5).
            css('top',0).css('left',0);
        $('#frame-h-2').width('100%').height('100%').
            css('top',0).css('left',0);
        $('#frame-l').width(l_width-5).height(l_height-5).
            css('top',h_height).css('left',0);
        $('#frame-l-2').width('100%').height('100%').
            css('top',0).css('left',0);
        $('#frame-ro').width(r_width-5).height(ro_height-5).
            css('top',0).css('left',l_width);
        $('#frame-ro-2').width('100%').height('100%').
            css('top',0).css('left',0);
        $('#frame-ru').width(r_width-5).height(ru_height-5).
            css('top',ro_height).css('left',l_width);
        $('#frame-ru-2').width('100%').height('100%').
            css('top',0).css('left',0);
    }

    function init() {
        rsizeIframes();
        $(window).resize(rsizeIframes);
    }

    $(document).ready(init);
    //]]>
</script>

<?php

} 

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
<!--<frameset border="3" bordercolor="" cols="<?php echo tohtml(getSettingWithDefault('set-text-l-framewidth-percent')); ?>%,*">
    <frameset rows="<?php 
    if (isset($audio)) { 
        echo getSettingWithDefault('set-text-h-frameheight-with-audio');
    } else {
        echo getSettingWithDefault('set-text-h-frameheight-no-audio'); 
    } ?>,*">
        <frame src="do_text_header.php?text=<?php echo $_REQUEST['start']; ?>" scrolling="auto" name="h" />
        <frame src="do_text_text.php?text=<?php echo $_REQUEST['start']; ?>" scrolling="auto" name="l" />
    </frameset>
    <<frameset rows="<?php echo tohtml(getSettingWithDefault('set-text-r-frameheight-percent')); ?>%,*">
        <frame src="empty.html" scrolling="auto" name="ro" />
        <frame src="empty.html" scrolling="auto" name="ru" />
    </frameset>
    <noframes><body><p>Sorry - your browser does not support frames.</p></body></noframes>
</frameset>-->
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
        do_text_mobile_css();
        do_text_mobile_js($audio);
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