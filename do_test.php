<?php


/**
 * \file
 * \brief Start a test (frameset)
 * 
 * Call: do_test.php?lang=[langid]
 * Call: do_test.php?text=[textid]
 * Call: do_test.php?selection=1  (SQL via $_SESSION['testsql'])
 * 
 * @package Lwt
 * @author LWT Project <lwt-project@hotmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @since  1.0.3
 */

require_once 'inc/session_utility.php';
require_once 'vendor/mobiledetect/mobiledetectlib/Mobile_Detect.php' ;

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
 * Find the appropiate property to add to the test.
 * It uses requests provided to the page.
 * 
 * @return string Some URL property
 */
function get_test_property()
{
    if (isset($_REQUEST['selection']) && isset($_SESSION['testsql'])) { 
        return "selection=" . $_REQUEST['selection']; 
    } 
    if (isset($_REQUEST['lang'])) { 
        return "lang=" . $_REQUEST['lang']; 
    } 
    if (isset($_REQUEST['text'])) { 
        return "text=" . $_REQUEST['text']; 
    } 
    return '';
}

/**
 * Echo the CSS and JS content for the mobile test page.
 * 
 * @return void
 * 
 * @deprecated 2.1.1-fork
 * @deprecated was not respecting the single responsibility principle, 
 * use do_test_mobile_css and do_test_mobile_js instead.
 */
function do_test_mobile_css_and_js() 
{
    do_test_mobile_css();
    do_test_mobile_js();
}

/**
 * Echo the CSS content for mobile test page.
 * 
 * @return void
 */
function do_test_mobile_css() 
{
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
 * Echo the JS content for the mobile test page.
 * 
 * @return void
 */
function do_test_mobile_js() {

?>    
<script type="text/javascript" src="js/jquery.js" charset="utf-8"></script>

<script type="text/javascript">
   //<![CDATA[
   function rsizeIframes() {
    var h_height = <?php echo getSettingWithDefault('set-test-h-frameheight'); ?> + 10;
    var lr_perc = <?php echo getSettingWithDefault('set-test-l-framewidth-percent'); ?>;
    var r_perc = <?php echo getSettingWithDefault('set-test-r-frameheight-percent'); ?>;
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
 * Make the content of the mobile page.
 * 
 * @param string $property URL property
 * 
 * @return void
 */
function do_test_mobile_page_content($property) 
{
    ?>
<div id="frame-h">
    <iframe id="frame-h-2" 
    src="do_test_header.php?<?php echo $property; ?>" scrolling="yes" name="h">
    </iframe>
</div>
<div id="frame-ro">
    <iframe id="frame-ro-2" src="empty.html" scrolling="yes" name="ro"></iframe>
</div>
<div id="frame-l">
    <iframe  id="frame-l-2" src="empty.html" scrolling="yes" name="l"></iframe>
</div>
<div id="frame-ru">
    <iframe id="frame-ru-2" src="empty.html" scrolling="yes" name="ru"></iframe>
</div>
        <?php 
}

/**
 * Make the mobile test page.
 * 
 * @param string $property URL property for HEADER
 * 
 * @return void
 */
function do_test_mobile_page($property) 
{
    do_test_mobile_css();
    do_test_mobile_js();
    do_test_mobile_page_content($property);
}

/**
 * Make the desktop test page
 * 
 * @param string $property URL property for HEADER
 * 
 * @return void
 */
function do_test_desktop_page($property) 
{
?>
<!--<frameset border="3" bordercolor="" cols="<?php echo tohtml(getSettingWithDefault('set-test-l-framewidth-percent')); ?>%,*">
    <frameset rows="<?php echo tohtml(getSettingWithDefault('set-test-h-frameheight')); ?>,*">
        <frame src="do_test_header.php?<?php echo $property; ?>" scrolling="auto" name="h" />            
        <frame src="empty.html" scrolling="auto" name="l" />
    </frameset>    
    <frameset rows="<?php echo tohtml(getSettingWithDefault('set-test-r-frameheight-percent')); ?>%,*">
        <frame src="empty.html" scrolling="auto" name="ro" />
        <frame src="empty.html" scrolling="auto" name="ru" />
    </frameset>
    <noframes><body><p>Sorry - your browser does not support frames.</p></body></noframes>
</frameset>-->

<div style="width: 95%; height: 100%;" onclick="setTimeout(hideRightFrames, 1000);">
    <div id="frame-h">
        <?php
    require_once 'do_test_header.php';
    //start_test_header_page();
        ?>
    </div>
    <hr />
    <div id="frame-l">
        <?php
    require_once 'do_test_test.php';
    //do_test_test_content($_REQUEST['start'], true);
        ?>
    </div>
</div>
<div id="frames-r" style="position: fixed; top: 0; right: -50%; width: 50%; height: 99%;">
    <iframe src="empty.html" scrolling="auto" name="ro" style="height: 50%; width: 100%;">
        Your browser doesn't support iFrames, update it!
    </iframe>
    <iframe src="empty.html" scrolling="auto" name="ru" style="height: 50%; width: 100%;">
        Your browser doesn't support iFrames, update it!
    </iframe>
</div>
<?php
}

/**
 * Start the test page.
 * 
 * @param string $p Some property to add to the URL of do_test_test.php.
 * @param bool   $mobile Set to true to use mobile mode.
 * 
 * @return void
 */
function do_test_page($p, $mobile)
{
    //framesetheader('Test');
    pagestart_nobody('Test');
    
    if ($mobile && false) {
        do_test_mobile_page($p);
    } else {
        do_test_desktop_page($p);
    }

    pageend();
}


/**
 * Main function to try to start a test page.
 *
 * If unsifficiant arguments are provided to
 * the page, the page will be redirected to
 * edit_texts.php.
 */
function try_start_test($p): void
{
    if ($p != '') {
        do_test_page($p, is_mobile());
    } else {
        header("Location: edit_texts.php");
        exit();
    }
}

if (get_test_property() != '') {
    try_start_test(get_test_property());
}
?>
