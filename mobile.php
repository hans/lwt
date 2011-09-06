<?php

/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions, 
unless such conditions are required by law.

Developed by J. Pierre in 2011.
***************************************************************/

/**************************************************************
Call: mobile.php?...
			...action=1&lang=[langid] ... Language menu
			...action=2&lang=[langid] ... Texts in a language
			...action=3&lang=[langid]&text=[textid] ... Sentences of a text
LWT Mobile 
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

/**************************************************************/

if (isset($_REQUEST["action"])) {  // Action

	$action = $_REQUEST["action"] + 0; // Action code

	/* -------------------------------------------------------- */

	if ($action == 1) { 
	
		$lang = $_REQUEST["lang"];
		$langname = getLanguage($lang);

		?>
		
		<ul id="<?php echo $action . '-' . $lang; ?>" title="<?php echo tohtml($langname); ?>">
			<li class="group"><?php echo tohtml($langname); ?> Texts</li>
			<li><a href="mobile.php?action=2&amp;lang=<?php echo $lang; ?>">All <?php echo tohtml($langname); ?> Texts</a></li>					
			<li><a href="mobile.php#notyetimpl">Text Tags</a></li>					
			<li class="group"><?php echo tohtml($langname); ?> Terms</li>
			<li><a href="mobile.php#notyetimpl">All <?php echo tohtml($langname); ?> Terms</a></li>					
			<li><a href="mobile.php#notyetimpl">Term Tags</a></li>					
		</ul>
		
		<?php
	
	} // $action == 1
	
	/* -------------------------------------------------------- */
	
	elseif ($action == 2) { 
	
		$lang = $_REQUEST["lang"];
		$langname = getLanguage($lang);
		$sql = 'select TxID, TxTitle from texts where TxLgID = ' . $lang . 
		' order by TxTitle';
		$res = mysql_query($sql);		
		if ($res == FALSE) die("Invalid Query: $sql");

		?>

		<ul id="<?php echo $action . '-' . $lang; ?>" title="All <?php echo tohtml($langname); ?> Texts">

		<?php

		while ($record = mysql_fetch_assoc($res)) {
			echo '<li><a href="mobile.php?action=3&amp;lang=' . 
				$lang . '&amp;text=' . $record["TxID"] . '">' .
				tohtml($record["TxTitle"]) . '</a></li>';	
		}

		?>

		</ul>
		<?php
		mysql_free_result($res);
	
	} // $action == 2
	
	/* -------------------------------------------------------- */
	
	elseif ($action == 3) { 
	
		$lang = $_REQUEST["lang"];
		$text = $_REQUEST["text"];
		$texttitle = get_first_value('select TxTitle as value from texts where TxID = ' . $text);
		$textaudio = get_first_value('select TxAudioURI as value from texts where TxID = ' . $text);
		$sql = 'select SeID, SeText from sentences where SeTxID = ' . $text . ' order by SeOrder';
		$res = mysql_query($sql);		
		if ($res == FALSE) die("Invalid Query: $sql");

		?>

		<ul id="<?php echo $action . '-' . $_REQUEST["text"]; ?>" title="<?php echo tohtml($texttitle); ?>">

		<?php

		if (isset($textaudio) && trim($textaudio) != '') {

		?>

		<li class="group">Audio</li>
		<li>Play: <audio src="<?php echo trim($textaudio); ?>" controls></audio></li>

		<?php

		}

		?>

		<li class="group">Text</li>

		<?php
		
		while ($record = mysql_fetch_assoc($res)) {
			if (trim($record["SeText"]) != '¶')
			 echo '<li><a href="mobile.php#notyetimpl">' .
				tohtml($record["SeText"]) . '</a></li>';	
		}

		?>
		
		</ul>

		<?php
		
		mysql_free_result($res);
	
	} // $action == 3
	
	/* -------------------------------------------------------- */
	
} // isset($_REQUEST["action"])

/**************************************************************/

else {  // No Action = Start screen

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="content-language" content="en" />
<title>Mobile LWT</title>
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
<link rel="apple-touch-icon" href="img/apple-touch-icon-57x57.png" />
<link rel="apple-touch-icon" sizes="72x72" href="img/apple-touch-icon-72x72.png" />
<link rel="apple-touch-icon" sizes="114x114" href="img/apple-touch-icon-114x114.png" />
<link rel="apple-touch-startup-image" href="img/apple-touch-startup.png">
<meta name="apple-touch-fullscreen" content="YES" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<style type="text/css" media="screen">
@import "./iui/iui.css";
</style>
<script type="text/javascript" src="./iui/iui.js" charset="utf-8"></script>
</head>
<body>

<div class="toolbar">
	<h1 id="pageTitle"></h1>
	<a id="backButton" class="button" href="#"></a>
	<a class="button" href="mobile.php" target="_self">Home</a>
</div>

<ul id="home" title="Mobile LWT" selected="true">
	<li class="group">Languages</li>
<?php
	$sql = 'select LgID, LgName from languages order by LgName';
	$res = mysql_query($sql);		
	if ($res == FALSE) die("Invalid Query: $sql");
	while ($record = mysql_fetch_assoc($res)) {
		echo '<li><a href="mobile.php?action=1&amp;lang=' . $record["LgID"] . '">' .
			tohtml($record["LgName"]) . '</a></li>';	
	}
	mysql_free_result($res);
?>
	<li class="group">Other</li>
	<li><a href="#about">About</a></li>
</ul>

<div id="about" title="About">
	<p style="text-align:center; margin-top:50px;">
This is "Learning With Texts" (LWT) for Mobile Devices<br />Version <?php echo get_version(); ?><br /><br />"Learning with Texts" (LWT) is released into the Public Domain. This applies worldwide. In case this is not legally possible, any entity is granted the right to use this work for any purpose, without any conditions, unless such conditions are required by law.<br /><br /> Developed with the <a href="http://code.google.com/p/iui/" target="_self">iUI Framework</a>.<br /><br /><b>Back to<br/><a href="index.php" target="_self">LWT Standard Version</a></b>
	</p>
</div>

<div id="notyetimpl" title="Sorry...">
	<p style="text-align:center; margin-top:50px;">Not yet implemented!</p>
</div>

</body>
</html>

<?php
	
} // No Action = Start screen

?>