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
Call: do_text.php?start=[textid]
Start Reading a text (frameset)
***************************************************************/

require_once( 'settings.inc.php' );
require_once( 'connect.inc.php' );
require_once( 'dbutils.inc.php' );
require_once( 'utilities.inc.php' ); 
require_once( 'php-mobile-detect/Mobile_Detect.php' );
$detect = new Mobile_Detect;

if (isset($_REQUEST['start'])) {
	
	$audio = get_first_value('select TxAudioURI as value from ' . $tbpref . 'texts where TxID = ' . $_REQUEST['start']);
	
	framesetheader('Read');
	
	$tabletMode = getSettingWithDefault('set-text-test-tablet-mode')+0;
	
	if ( $detect->isMobile() ) {

?>

	<style type="text/css"> 
	body {
		background-color: #888888;
		margin: 0;
		overflow: hidden;
	}
	#frame-h {
		position:absolute; 
		border: 1px solid #000000;	
	}
	#frame-l {
		position:absolute; 
		border: 1px solid #000000;	
	}
	#frame-ro {
		position:absolute; 
		border: 1px solid #000000;	
	}
	#frame-ru {
		position:absolute; 
		border: 1px solid #000000;	
	}
	</style> 
	<script type="text/javascript" src="js/jquery.js" charset="utf-8"></script>
	<script type="text/javascript">
//<![CDATA[
	function rsizeIframes() {
		var h_height = <?php echo (isset($audio) ? getSettingWithDefault('set-text-h-frameheight-with-audio') : getSettingWithDefault('set-text-h-frameheight-no-audio')); ?> + 10;
		var lr_perc = <?php echo getSettingWithDefault('set-text-l-framewidth-percent'); ?>;
		var r_perc = <?php echo getSettingWithDefault('set-text-r-frameheight-percent'); ?>;
		var w = $(window).width();
		var h = $(window).height();
		var l_width = w*lr_perc/100;
		var r_width = w - l_width;
		var l_height = h - h_height;
		var ro_height = h*r_perc/100;
		var ru_height = h - ro_height;
		$('#frame-h').width(l_width-5).height(h_height-5).css('top',0).css('left',0);
			$('#frame-h-2').width('100%').height('100%').css('top',0).css('left',0);
		$('#frame-l').width((l_width-5)).height(l_height-5).css('top',h_height).css('left',0);
			$('#frame-l-2').width('100%').height('100%').css('top',0).css('left',0);
		$('#frame-ro').width(r_width-5).height(ro_height-5).css('top',0).css('left',l_width);
			$('#frame-ro-2').width('100%').height('100%').css('top',0).css('left',0);
		$('#frame-ru').width(r_width-5).height(ru_height-5).css('top',ro_height).css('left',l_width);
			$('#frame-ru-2').width('100%').height('100%').css('top',0).css('left',0);
	}

	function init() {
		rsizeIframes();
		$(window).resize(rsizeIframes);
	}
	
	$(document).ready(init);
//]]>
</script> 
<div id="frame-h" style="overflow:scroll; -webkit-overflow-scrolling: touch;">
	<iframe id="frame-h-2" src="do_text_header.php?text=<?php echo $_REQUEST['start']; ?>" scrolling="yes" name="h" style="display:inline-block;"></iframe>
</div>
<div id="frame-ro" style="overflow:scroll; -webkit-overflow-scrolling: touch;">
<iframe id="frame-ro-2" src="empty.htm" scrolling="yes" name="ro" style="display:inline-block;"></iframe>
</div>
<div id="frame-l" style="overflow:scroll; -webkit-overflow-scrolling: touch;">
	<iframe  id="frame-l-2" src="do_text_text.php?text=<?php echo $_REQUEST['start']; ?>" scrolling="yes" name="l" style="display:inline-block; overflow:scroll;"></iframe>
</div>
<div id="frame-ru" style="overflow:scroll; -webkit-overflow-scrolling: touch;">
	<iframe id="frame-ru-2" src="empty.htm" scrolling="yes" name="ru" style="display:inline-block;"></iframe>
</div>

<?php 

	} else {
	
?>

<frameset cols="<?php echo tohtml(getSettingWithDefault('set-text-l-framewidth-percent')); ?>%,*">
	<frameset rows="<?php echo (isset($audio) ? getSettingWithDefault('set-text-h-frameheight-with-audio') : getSettingWithDefault('set-text-h-frameheight-no-audio') ); ?>,*">
		<frame src="do_text_header.php?text=<?php echo $_REQUEST['start']; ?>" scrolling="auto" name="h" />			
		<frame src="do_text_text.php?text=<?php echo $_REQUEST['start']; ?>" scrolling="auto" name="l" />
	</frameset>
	<frameset rows="<?php echo tohtml(getSettingWithDefault('set-text-r-frameheight-percent')); ?>%,*">
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