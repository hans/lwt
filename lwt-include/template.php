<?php

/**
 * Render a view.
 *
 * @param string $template optional
 *   Name of template to render.
 *
 *   If this value is not provided, the template name is generated from the
 *   basename of the current request URI.
 *
 *   For example, if the request is at /index.php, the default template to load
 *   would be 'index'.
 */
function render($template = NULL) {
    if ( $template === NULL )
        $template = pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME);

    require LWT_BASE . DIRECTORY_SEPARATOR . 'lwt-view'
        . DIRECTORY_SEPARATOR . $template . '.php';
}

function framesetheader($title) {
	@header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
	@header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
	@header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );
	@header( 'Pragma: no-cache' );
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<!-- ***********************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions,
unless such conditions are required by law.

Developed by J. Pierre in 2011.
************************************************************ -->

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Learning with Texts :: <?php echo tohtml($title); ?></title>
</head>
<?php
}

function pagestart_nobody($titeltext, $addcss='') {
	global $debug;
	@header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
	@header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
	@header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );
	@header( 'Pragma: no-cache' );
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<!-- ***********************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions,
unless such conditions are required by law.

Developed by J. Pierre in 2011.
************************************************************ -->

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />

	<meta name="viewport" content="width=900" />
	<link rel="apple-touch-icon" href="img/apple-touch-icon-57x57.png" />
	<link rel="apple-touch-icon" sizes="72x72" href="img/apple-touch-icon-72x72.png" />
	<link rel="apple-touch-icon" sizes="114x114" href="img/apple-touch-icon-114x114.png" />
	<link rel="apple-touch-startup-image" href="img/apple-touch-startup.png">
	<meta name="apple-mobile-web-app-capable" content="yes" />

	<link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="css/jquery.tagit.css">
	<link rel="stylesheet" type="text/css" href="css/tagit.ui-zendesk.css">
	<link rel="stylesheet" type="text/css" href="css/styles.css">
	<style type="text/css">
	<?php echo $addcss . "\n"; ?>
	</style>

	<script type="text/javascript" src="js/jquery.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/jquery.scrollTo.min.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/jquery-ui.min.js"  charset="utf-8"></script>
	<script type="text/javascript" src="js/tag-it.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/sorttable/sorttable.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/countuptimer.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/overlib/overlib_mini.js" charset="utf-8"></script>
	<script type="text/javascript">
	//<![CDATA[
	<?php echo "var STATUSES = " . json_encode(get_statuses()) . ";\n"; ?>
	<?php echo "var TAGS = " . json_encode(get_tags()) . ";\n"; ?>
	<?php echo "var TEXTTAGS = " . json_encode(get_texttags()) . ";\n"; ?>
	//]]>
	</script>
	<script type="text/javascript" src="js/pgm.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/jq_pgm.js" charset="utf-8"></script>

	<title>Learning with Texts :: <?php echo $titeltext; ?></title>
</head>
<body>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<?php
}

function pagestart($titeltext,$close) {
	global $debug;
	pagestart_nobody($titeltext);
	echo '<h4>';
	if ($close) echo '<a href="index.php" target="_top">';
	echo '<img class="lwtlogo" src="img/lwt_icon.png" alt="Logo" />Learning with Texts';
	if ($close) {
		echo '</a>&nbsp; | &nbsp;';
		quickMenu();
	}
	echo '</h4><h3>' . $titeltext . ($debug ? ' <span class="red">DEBUG</span>' : '') . '</h3>';
	echo "<p>&nbsp;</p>";
}

function pageend() {
	global $debug;
	if ($debug) showRequest();
?></body></html><?php
}

function quickMenu() {
?><select id="quickmenu" onchange="{var qm = document.getElementById('quickmenu'); var val=qm.options[qm.selectedIndex].value; qm.selectedIndex=0; if (val != '') { if (val == 'INFO') {top.location.href='info.htm';} else {top.location.href = val + '.php';}}}">
<option value="" selected="selected">[Menu]</option>
<option value="index">Home</option>
<option value="edit_texts">Texts</option>
<option value="edit_archivedtexts">Archive</option>
<option value="edit_texttags">Text Tags</option>
<option value="edit_languages">Languages</option>
<option value="edit_words">Terms</option>
<option value="edit_tags">Term Tags</option>
<option value="statistics">Statistics</option>
<option value="check_text">Text Check</option>
<option value="upload_words">Import</option>
<option value="backup_restore">Backup</option>
<option value="settings">Settings</option>
<option value="INFO">Help</option>
</select><?php
}

function makePager ($currentpage, $pages, $script, $formname) {
	if ($currentpage > 1) {
?>
&nbsp; &nbsp;<a href="<?php echo $script; ?>?page=1"><img src="icn/control-stop-180.png" title="First Page" alt="First Page" /></a>&nbsp;
<a href="<?php echo $script; ?>?page=<?php echo $currentpage-1; ?>"><img  src="icn/control-180.png" title="Previous Page" alt="Previous Page" /></a>&nbsp;
<?php
	} else {
?>
&nbsp; &nbsp;<img src="icn/placeholder.png" alt="-" />&nbsp;
<img src="icn/placeholder.png" alt="-" />&nbsp;
<?php
	}
?>
Page
<?php
	if ($pages==1) echo '1';
	else {
?>
<select name="page" onchange="{val=document.<?php echo $formname; ?>.page.options[document.<?php echo $formname; ?>.page.selectedIndex].value; location.href='<?php echo $script; ?>?page=' + val;}"><?php echo get_paging_selectoptions($currentpage, $pages); ?></select>
<?php
	}
	echo ' of ' . $pages . '&nbsp; ';
	if ($currentpage < $pages) {
?>
<a href="<?php echo $script; ?>?page=<?php echo $currentpage+1; ?>"><img src="icn/control.png" title="Next Page" alt="Next Page" /></a>&nbsp;
<a href="<?php echo $script; ?>?page=<?php echo $pages; ?>"><img src="icn/control-stop.png" title="Last Page" alt="Last Page" /></a>&nbsp; &nbsp;
<?php
	} else {
?>
<img src="icn/placeholder.png" alt="-" />&nbsp;
<img src="icn/placeholder.png" alt="-" />&nbsp; &nbsp;
<?php
	}
}

?>