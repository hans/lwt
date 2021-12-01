<?php

/**
 * \file
 * \brief Display an improved annotated text (frame set)
 * 
 * Call: display_impr_text.php?text=[textid]
 * 
 * @author LWT Project <lwt-project@hotmail.com>
 * @since  1.5.0
 */

require_once 'inc/session_utility.php'; 
require_once 'vendor/mobiledetect/mobiledetectlib/Mobile_Detect.php' ;

$detect = new Mobile_Detect;
$mobileDisplayMode = getSettingWithDefault('set-mobile-display-mode') + 0;
$mobile = (($mobileDisplayMode == 0 && $detect->isMobile()) || ($mobileDisplayMode == 2));

if (isset($_REQUEST['text'])) {
    
    $audio = get_first_value('select TxAudioURI as value from ' . $tbpref . 'texts where TxID = ' . $_REQUEST['text']);
    
    framesetheader('Display');

    if ($mobile ) {

        ?>

    <style type="text/css"> 
    body {
     background-color: #cccccc;
     margin: 0;
     overflow: hidden;
    }
    #frame-h, #frame-l {
     position:absolute; 
     overflow:scroll; 
     -webkit-overflow-scrolling: touch;
    }
    #frame-h-2, #frame-l-2 {
     display:inline-block;    
    }
    </style> 
    
    <script type="text/javascript" src="js/jquery.js" charset="utf-8"></script>
    
    <script type="text/javascript">
   //<![CDATA[
   function rsizeIframes() {
    var h_height = <?php echo (isset($audio) ? getSettingWithDefault('set-text-h-frameheight-with-audio') : getSettingWithDefault('set-text-h-frameheight-no-audio')); ?> - 80;
    var w = $(window).width();
    var h = $(window).height();
    var l_height = h - h_height;
    $('#frame-h').width(w-5).height(h_height-5).
        css('top',0).css('left',0);
    $('#frame-h-2').width('100%').height('100%').
        css('top',0).css('left',0);
    $('#frame-l').width(w-5).height(l_height-5).
        css('top',h_height).css('left',0);
    $('#frame-l-2').width('100%').height('100%').
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
    <iframe id="frame-h-2" src="display_impr_text_header.php?text=<?php echo $_REQUEST['text']; ?>" scrolling="yes" name="header"></iframe>
</div>
<div id="frame-l">
    <iframe id="frame-l-2" src="display_impr_text_text.php?text=<?php echo $_REQUEST['text']; ?>" scrolling="yes" name="text"></iframe>
</div>

        <?php 

    } else {
    
        ?>

   <frameset border="3" bordercolor="" rows="<?php echo (isset($audio) ? getSettingWithDefault('set-text-h-frameheight-with-audio')-90 : getSettingWithDefault('set-text-h-frameheight-no-audio')-90 ); ?>,*">
    <frame src="display_impr_text_header.php?text=<?php echo $_REQUEST['text']; ?>" scrolling="no" name="header" />            
    <frame src="display_impr_text_text.php?text=<?php echo $_REQUEST['text']; ?>" scrolling="auto" name="text" />
</frameset>
<noframes><body><p>Sorry - your browser does not support frames.</p></body></noframes>
</frameset>
</html>
        <?php

    }

}

else {

    header("Location: edit_texts.php");
    exit();

}

?>