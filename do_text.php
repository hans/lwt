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

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php"; 

if (isset($_REQUEST['start'])) {
	
	$audio = get_first_value('select TxAudioURI as value from ' . $tbpref . 'texts where TxID = ' . $_REQUEST['start']);
	
	framesetheader('Read');

?>
<frameset cols="<?php echo tohtml(getSettingWithDefault('set-text-l-framewidth-percent')); ?>%,*">
	<frameset rows="<?php echo (isset($audio) ? getSettingWithDefault('set-text-h-frameheight-with-audio') : getSettingWithDefault('set-text-h-frameheight-no-audio') ); ?>,*">
		<frame src="do_text_header.php?text=<?php echo $_REQUEST['start']; ?>" scrolling="no" name="h" />			
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

else {

	header("Location: edit_texts.php");
	exit();

}

?>