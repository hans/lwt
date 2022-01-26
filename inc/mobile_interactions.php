<?php
/**
 * \file
 * \brief Handle interactions with mobile platforms
 * 
 * @package Lwt
 * @author  LWT Project <lwt-project@hotmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/mobile__interactions_8php.html
 * @since   2.2.0
 */

require_once __DIR__ . '/../vendor/mobiledetect/mobiledetectlib/Mobile_Detect.php';
require_once __DIR__ . '/database_connect.php';

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
 * Echo the CSS content for mobile frameset page.
 * 
 * @return void
 */
function do_frameset_mobile_css() 
{
?>
<style type="text/css"> 
    body {
        background-color: #cccccc;
        margin: 0;
        overflow: hidden;
    }
    #frame-h, #frame-l, #frame-ro, #frame-ru {
        position: absolute; 
        overflow: scroll; 
        -webkit-overflow-scrolling: touch;
    }
    #frame-h-2, #frame-l-2, #frame-ro-2, #frame-ru-2 {
        display: inline-block;    
    }
</style>
<?php
}


/**
 * Echo the JS code for the mobile version of a frameset page.
 * 
 * @param string|null $audio Audio URI
 * 
 * @return void
 */
function do_frameset_mobile_js($audio=null) {

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

 ?>