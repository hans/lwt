<?php
/**
 * \file
 * \brief Core utility functions that do not require a complete session.
 */

 require __DIR__ . '/settings.php';


/** 
 * Return LWT version for humans
 * 
 * Version is hardcoded in this function.
 * For instance 1.6.31 (October 03 2016)
 *
 * @global bool $debug If true adds a red "DEBUG"
 */
function get_version() 
{
    global $debug;
    $version = '2.0.2 (September 07 2021)'; 
    if ($debug) {
        $version .= ' <span class="red">DEBUG</span>';
    }
    return $version;
}

/** 
 * Return a machine readable version number.
 * 
 * For instance v001.006.031
 */
function get_version_number() 
{
    $r = 'v';
    $v = get_version();
    $pos = strpos($v, ' ', 0);
    if ($pos === false) { 
        my_die('Wrong version: '. $v); 
    }
    $vn = preg_split("/[.]/", substr($v, 0, $pos));
    if (count($vn) < 3) { 
        my_die('Wrong version: '. $v); 
    }
    for ($i=0; $i<3; $i++) { 
        $r .= substr('000' . $vn[$i], -3); 
    }
    return $r;  // 'vXXXYYYZZZ' when version = x.y.z
}

/**
 * Make the script crash and prints an error message
 *
 * @param string $text Error text to output
 */
function my_die($text) 
{
    echo '</select></p></div><div style="padding: 1em; color:red; font-size:120%; background-color:#CEECF5;">' .
    '<p><b>Fatal Error:</b> ' . 
    tohtml($text) . 
    "</p></div><hr /><pre>Backtrace:\n\n";
    debug_print_backtrace();
    echo '</pre><hr />';
    echo '<a href="https://github.com/HugoFara/lwt/issues/new/choose">Signal this issue.</a>';
    die('</body></html>');
}

/**
 * Display the main menu of navigation as a dropdown
 */
function quickMenu() 
{
?>

<script type="text/javascript" src="js/user_interactions.js" charset="utf-8"></script>
<select id="quickmenu" onchange="quickMenuRedirection(value)">
<option value="" selected="selected">[Menu]</option>
<option value="index">Home</option>
<option value="edit_texts">Texts</option>
<option value="edit_archivedtexts">Text Archive</option>
<option value="edit_texttags">Text Tags</option>
<option value="edit_languages">Languages</option>
<option value="edit_words">Terms</option>
<option value="edit_tags">Term Tags</option>
<option value="statistics">Statistics</option>
<option value="check_text">Text Check</option>
<option value="long_text_import">Long Text Import</option>
<option value="rss_import">Newsfeed Import</option>
<option value="upload_words">Term Import</option>
<option value="backup_restore">Backup/Restore</option>
<option value="settings">Settings</option>
<option value="INFO">Help</option>
</select>
<?php
}


/**
 * Write a page header and start writing its body.
 * 
 * @param  string $titletext Title of the page
 * @param  bool   $close 
 * @global bool $debug Show a DEBUG span if true
 */
function pagestart($titletext, $close) 
{
    global $debug;
    pagestart_nobody($titletext);
    echo '<h4>';
    if ($close) { 
        echo '<a href="index.php" target="_top">'; 
    }
    echo_lwt_logo();
    echo "<span>LWT</span>";
    if ($close) {
        echo '</a><span>&nbsp; | &nbsp;';
        quickMenu();
        echo '</span>';
    }
    echo '</h4><h3>' . $titletext . ($debug ? ' <span class="red">DEBUG</span>' : '') . '</h3>';
    echo "<p>&nbsp;</p>";
} 


/**
 * Add a closing body tag.
 * 
 * @param  string $titletext Title of the page
 * @param  string $addcss    Some CSS to be embed in a style tag
 * @global bool $debug Show the requests if true
 * @global float $dspltime Total execution time since the PHP session started
 */
function pageend() 
{
    global $debug, $dspltime;
    if ($debug) { 
        showRequest(); 
    }
    if ($dspltime) { 
        echo "\n<p class=\"smallgray2\">" . 
        round(get_execution_time(), 5) . " secs</p>\n"; 
    }
    echo '</body></html>';
} 

/**
 * Debug function only.
 *
 * @param  any    $var  A printed variable to debug
 * @param  string $text Echoed text in HTML page
 * @global bool $debug This functions doesn't do anything is $debug is false.
 */
function echodebug($var,$text) 
{
    global $debug;
    if ($debug) { 
        echo "<pre> **DEBUGGING** " . tohtml($text) . ' = [[[';
        print_r($var);
        echo "]]]\n--------------</pre>";
    }
}

/**
 * Start a standard page with a complete header and a non-closed body.
 * 
 * @param string $titletext Title of the page
 * @param string $addcss Some CSS to be embed in a style tag
 * @global bool $debug Show the requests if true
 * @global string $tbpref The database table prefix if true
 */

function pagestart_nobody($titletext, $addcss='') 
{
    global $debug;
    global $tbpref;
    @header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
    @header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    @header('Cache-Control: no-cache, must-revalidate, max-age=0');
    @header('Pragma: no-cache');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    
<!-- ***********************************************************
"Learning with Texts" (LWT) is free and unencumbered software 
released into the PUBLIC DOMAIN.

Anyone is free to copy, modify, publish, use, compile, sell, or
distribute this software, either in source code form or as a
compiled binary, for any purpose, commercial or non-commercial,
and by any means.

In jurisdictions that recognize copyright laws, the author or
authors of this software dedicate any and all copyright
interest in the software to the public domain. We make this
dedication for the benefit of the public at large and to the 
detriment of our heirs and successors. We intend this 
dedication to be an overt act of relinquishment in perpetuity
of all present and future rights to this software under
copyright law.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE 
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE
AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS BE LIABLE 
FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN 
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN 
THE SOFTWARE.

For more information, please refer to [http://unlicense.org/].
************************************************************ -->

    <meta name="viewport" content="width=900" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="apple-touch-icon" href="<?php print_file_path('img/apple-touch-icon-57x57.png');?>" />
    <link rel="apple-touch-icon" sizes="72x72" href="<?php print_file_path('img/apple-touch-icon-72x72.png');?>" />
    <link rel="apple-touch-icon" sizes="114x114" href="<?php print_file_path('img/apple-touch-icon-114x114.png');?>" />
    <link rel="apple-touch-startup-image" href="img/apple-touch-startup.png" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    
    <link rel="stylesheet" type="text/css" href="<?php print_file_path('css/jquery-ui.css');?>" />
    <link rel="stylesheet" type="text/css" href="<?php print_file_path('css/jquery.tagit.css');?>" />
    <link rel="stylesheet" type="text/css" href="<?php print_file_path('css/styles.css');?>" />
    <style type="text/css">
    <?php echo $addcss . "\n"; ?>
    </style>
    
    <script type="text/javascript" src="js/jquery.js" charset="utf-8"></script>
    <script type="text/javascript" src="js/jquery.scrollTo.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="js/jquery-ui.min.js"  charset="utf-8"></script>
    <script type="text/javascript" src="js/tag-it.js" charset="utf-8"></script>
    <script type="text/javascript" src="js/jquery.jeditable.mini.js" charset="utf-8"></script>
    <script type="text/javascript" src="js/sorttable/sorttable.js" charset="utf-8"></script>
    <script type="text/javascript" src="js/countuptimer.js" charset="utf-8"></script>
    <script type="text/javascript" src="js/overlib/overlib_mini.js" charset="utf-8"></script>
    <!-- URLBASE : "<?php echo tohtml(url_base()); ?>" -->
    <!-- TBPREF  : "<?php if (isset($tbpref)) {
        echo tohtml($tbpref); 
   } ?>" -->
    <script type="text/javascript">
    //<![CDATA[
    <?php echo "var STATUSES = " . json_encode(get_statuses()) . ";\n"; ?>
    <?php echo "var TAGS = " . json_encode(get_tags()) . ";\n"; ?>
    <?php echo "var TEXTTAGS = " . json_encode(get_texttags()) . ";\n"; ?>
    //]]>
    </script>
    <script type="text/javascript" src="js/pgm.js" charset="utf-8"></script>
    <script type="text/javascript" src="js/jq_pgm.js" charset="utf-8"></script>
    
    <title>LWT :: <?php echo $titletext; ?></title>
</head>
<body>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<?php
    flush();
if ($debug) { 
    showRequest(); 
}
}

/**
 * Replace the first occurence of $needle in $haystack by $replace
 * 
 * @param  string $needle   Text to replace
 * @param  string $replace  Text to replace by
 * @param  string $haystack Input string
 * @return string String with replaced text
 */
function str_replace_first($needle, $replace, $haystack) 
{
    if ($needle === '') {
        return $haystack; 
    }
    $pos = strpos($haystack, $needle);
    if ($pos !== false) {
        return substr_replace($haystack, $replace, $pos, strlen($needle));
    }
    return $haystack;
}

/**
 * Convert annotations in a JSON format.
 * 
 * @param  string $ann Annotations.
 * @return string A JSON-encoded version of the annotations
 */
function annotation_to_json($ann) 
{
    if ($ann == '') {
        return "{}"; 
    }
    $arr = array();
    $items = preg_split('/[\n]/u', $ann);
    foreach ($items as $item) {
        $vals = preg_split('/[\t]/u', $item);
        if (count($vals) > 3 && $vals[0] >= 0 && $vals[2] > 0) {
            $arr[$vals[0]-1] = array($vals[1], $vals[2], $vals[3]);
        }
    }
    return json_encode($arr);
}

?>