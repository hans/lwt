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

function pagestart_nobody($page_title, $extra_css = '') {
	global $debug;
	@header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
	@header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
	@header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );
	@header( 'Pragma: no-cache' );

  render('header');
}

function pagestart($page_title, $close) {
	global $debug;

	pagestart_nobody($page_title);
  render('header_body');
}

function pageend() {
	global $debug;
	if ($debug) showRequest();

  render('footer');
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