<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
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
    <link rel="apple-touch-startup-image" href="img/apple-touch-startup.png" />
    <meta name="apple-mobile-web-app-capable" content="yes" />

    <link rel="stylesheet" type="text/css" href="css/jquery-ui.css" />
    <link rel="stylesheet" type="text/css" href="css/jquery.tagit.css" />
    <link rel="stylesheet" type="text/css" href="css/tagit.ui-zendesk.css" />
    <link rel="stylesheet" type="text/css" href="css/styles.css" />

    <?php if ( isset($extra_css) ): ?>
        <style type="text/css">
            <?php echo $extra_css . "\n"; ?>
        </style>
    <?php endif; ?>

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

    <title>Learning with Texts :: <?php echo $page_title; ?></title>
</head>
<body>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
