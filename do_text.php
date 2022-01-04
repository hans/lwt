<?php

/**
 * \file
 * \brief Start Reading a text (frameset)
 * 
 * Call: do_text.php?start=[textid]
 *      Create the main window when reading texts.
 * 
 * @author LWT Project <lwt-project@hotmail.com>
 * @since  1.0.3
 */

require_once 'inc/session_utility.php'; 
require_once 'vendor/mobiledetect/mobiledetectlib/Mobile_Detect.php';

$detect = new Mobile_Detect;
$mobileDisplayMode = (int)getSettingWithDefault('set-mobile-display-mode');
$mobile = ($mobileDisplayMode == 0 && $detect->isMobile()) || $mobileDisplayMode == 2;

if (isset($_REQUEST['start'])) {
    
    $audio = get_first_value(
        'SELECT TxAudioURI AS value 
        FROM ' . $tbpref . 'texts 
        WHERE TxID = ' . $_REQUEST['start']
    );
    
    //framesetheader('Read');
    pagestart_nobody(
        tohtml('Read'), 
        '.resizable
    {
     min-height: 30px;
     min-width: 30px;
     resize: both;
     overflow: auto;
     max-height: fit-content;
     max-width: fit-content;
    }'
    );
    
    if ($mobile ) {

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
    
    <div id="frame-h">
        <iframe id="frame-h-2" src="do_text_header.php?text=<?php echo $_REQUEST['start']; ?>" scrolling="yes" name="h"></iframe>
    </div>
    <div id="frame-ro">
    <iframe id="frame-ro-2" src="empty.html" scrolling="yes" name="ro"></iframe>
    </div>
    <div id="frame-l">
        <iframe id="frame-l-2" src="do_text_text.php?text=<?php echo $_REQUEST['start']; ?>" scrolling="yes" name="l"></iframe>
    </div>
    <div id="frame-ru">
        <iframe id="frame-ru-2" src="empty.html" scrolling="yes" name="ru"></iframe>
    </div>

        <?php 

    } 
    else 
    {
        // Not mobile
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
    <div class="resizable" style="width: 65%;">
        <div id="frame-h">
            <?php
            require_once 'do_text_header.php';
            do_text_header_content($_REQUEST['start'], true);
            ?>
        </div>
        <hr />
        <div id="frame-l">
            <?php
            require_once 'do_text_text.php';
            do_text_text_content($_REQUEST['start'], true);
            ?>
        </div>
    </div>
    <div class="resizable" style="position: fixed; top: 0; right: 5px; width: 35%; height: 99%;">
        <iframe src="empty.html" scrolling="auto" name="ro" style="height: 50%; width: 100%;">
            Your browser doesn't support iFrames, update it!
        </iframe>
        <iframe src="empty.html" scrolling="auto" name="ru" style="height: 50%; width: 100%;">
            Your browser doesn't support iFrames, update it!
        </iframe>
    </div>
    </body>
</html>
        <?php

    }

}
else 
{
    // Document not ready
    header("Location: edit_texts.php");
    exit();
}

?>