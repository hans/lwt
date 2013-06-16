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

include "settings.inc.php";
include "connect.inc.php";
include "utilities.inc.php";

$p = '';
if (isset($_REQUEST['selection']) && isset($_SESSION['testsql'])) 
	$p = "selection=" . $_REQUEST['selection']; 
if (isset($_REQUEST['lang'])) 
	$p = "lang=" . $_REQUEST['lang']; 
if (isset($_REQUEST['text'])) 
	$p = "text=" . $_REQUEST['text']; 

if ($p != '') {

	framesetheader('Test');

?>
<frameset id="topframeset" cols="<?php echo tohtml(getSettingWithDefault('set-test-l-framewidth-percent')); ?>%,*">
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

else {

	header("Location: edit_texts.php");
	exit();

}
?>