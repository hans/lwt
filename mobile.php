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
LWT Mobile 
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

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
	<li><a href="#notyetimpl">???</a></li>
	<li><a href="#notyetimpl">???</a></li>
	<li><a href="#about">About</a></li>
</ul>

<div id="about" title="About">
	<p style="text-align:center">
This is "Learning With Texts" (LWT) for Mobile Devices<br />Version <?php echo get_version(); ?><br /><br />"Learning with Texts" (LWT) is released into the Public Domain. This applies worldwide. In case this is not legally possible, any entity is granted the right to use this work for any purpose, without any conditions, unless such conditions are required by law.<br /><br /> Developed with the <a href="http://code.google.com/p/iui/" target="_self">iUI Framework</a>.<br /><br /><b>Back to<br/><a href="index.php" target="_self">LWT Standard Version</a></b>
	</p>
</div>

<div id="notyetimpl" title="Sorry...">
    <p style="text-align:center">Not yet implemented!</p>
</div>

</body>
</html>