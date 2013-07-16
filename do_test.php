<?php

/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions, 
unless such conditions are required by law.

Developed by J. P. in 2011, 2012, 2013.
***************************************************************/

/**************************************************************
Call: do_test.php?lang=[langid]
Call: do_test.php?text=[textid]
Call: do_test.php?selection=1  (SQL via $_SESSION['testsql'])
Start a test (frameset)
***************************************************************/

require_once( 'settings.inc.php' );
require_once( 'connect.inc.php' );
require_once( 'dbutils.inc.php' );
require_once( 'utilities.inc.php' );
require_once( 'php-mobile-detect/Mobile_Detect.php' );

$detect = new Mobile_Detect;
$mobileDisplayMode = getSettingWithDefault('set-mobile-display-mode') + 0;
$mobile = (($mobileDisplayMode == 0 && $detect->isMobile()) || ($mobileDisplayMode == 2));

$p = '';
if (isset($_REQUEST['selection']) && isset($_SESSION['testsql'])) 
	$p = "selection=" . $_REQUEST['selection']; 
if (isset($_REQUEST['lang'])) 
	$p = "lang=" . $_REQUEST['lang']; 
if (isset($_REQUEST['text'])) 
	$p = "text=" . $_REQUEST['text']; 

if ($p != '') {

	framesetheader('Test');
	
	if ( $mobile ) {

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

<div id="frame-h">
	<iframe id="frame-h-2" src="do_test_header.php?<?php echo $p; ?>" scrolling="yes" name="h"></iframe>
</div>
<div id="frame-ro">
	<iframe id="frame-ro-2" src="empty.htm" scrolling="yes" name="ro"></iframe>
</div>
<div id="frame-l">
	<iframe  id="frame-l-2" src="empty.htm" scrolling="yes" name="l"></iframe>
</div>
<div id="frame-ru">
	<iframe id="frame-ru-2" src="empty.htm" scrolling="yes" name="ru"></iframe>
</div>

<?php 

	} else {
	
?>

<frameset cols="<?php echo tohtml(getSettingWithDefault('set-test-l-framewidth-percent')); ?>%,*">
	<frameset rows="<?php echo tohtml(getSettingWithDefault('set-test-h-frameheight')); ?>,*">
		<frame src="do_test_header.php?<?php echo $p; ?>" scrolling="auto" name="h" />			
		<frame src="empty.htm" scrolling="auto" name="l" />
	</frameset>	
	<frameset rows="<?php echo tohtml(getSettingWithDefault('set-test-r-frameheight-percent')); ?>%,*">
		<frame src="empty.htm" scrolling="auto" name="ro" />
		<frame src="empty.htm" scrolling="auto" name="ru" />
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