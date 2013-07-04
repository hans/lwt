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
Call: display_impr_text.php?text=[textid]
Display an improved annotated text (frame set)
***************************************************************/

require_once( 'settings.inc.php' );
require_once( 'connect.inc.php' );
require_once( 'dbutils.inc.php' );
require_once( 'utilities.inc.php' ); 

if (isset($_REQUEST['text'])) {
	
	$audio = get_first_value('select TxAudioURI as value from ' . $tbpref . 'texts where TxID = ' . $_REQUEST['text']);
	
	framesetheader('Display');

?>
<frameset rows="<?php echo (isset($audio) ? getSettingWithDefault('set-text-h-frameheight-with-audio')-90 : getSettingWithDefault('set-text-h-frameheight-no-audio')-90 ); ?>,*">
	<frame src="display_impr_text_header.php?text=<?php echo $_REQUEST['text']; ?>" scrolling="no" name="header" />			
	<frame src="display_impr_text_text.php?text=<?php echo $_REQUEST['text']; ?>" scrolling="auto" name="text" />
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