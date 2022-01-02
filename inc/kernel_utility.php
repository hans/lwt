<?php
/**
 * \file
 * \brief Core utility functions that do not require a complete session.
 * 
 * @package Lwt
 * @author  HugoFara <hugo.farajallah@protonmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/kernel__utility_8php.html
 * @since   2.0.3-fork
 */

 require __DIR__ . '/settings.php';


/** 
 * Return LWT version for humans
 * 
 * Version is hardcoded in this function.
 * For instance 1.6.31 (October 03 2016)
 *
 * @global bool $debug If true adds a red "DEBUG"
 * 
 * @return string Version number HTML-formatted
 */
function get_version() 
{
    global $debug;
    $version = '2.0.4-fork (December 03 2021)'; 
    if ($debug) {
        $version .= ' <span class="red">DEBUG</span>';
    }
    return $version;
}

/** 
 * Return a machine readable version number.
 * 
 * @return string Machine-readable version, for instance v001.006.031
 */
function get_version_number() 
{
    $r = 'v';
    $v = get_version();
    // Escape any detail like "-fork"
    $v = preg_replace('/-\w+\d*/', '', $v);
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
 * Escape special HTML characters.
 * 
 * @param  string $s String to escape.
 * @return string htmlspecialchars($s, ENT_COMPAT, "UTF-8");
 */
function tohtml($s) 
{
    if (!isset($s)) {
        return ''; 
    }
    return htmlspecialchars($s, ENT_COMPAT, "UTF-8");
}

/**
 * Strip the slashes for PHP up to 5. No effect in PHP >7
 * 
 */
function stripTheSlashesIfNeeded($s) 
{
    if (function_exists("get_magic_quotes_gpc")) {
        if (get_magic_quotes_gpc()) {
            return stripslashes($s); 
        }
        else { 
            return $s; 
        }
    } else {
        return $s;
    }
}

/**
 * Echo debugging informations.
 */
function showRequest() 
{
    $olderr = error_reporting(0);
    echo "<pre>** DEBUGGING **********************************\n";
    echo '$GLOBALS...'; print_r($GLOBALS);
    echo 'get_version_number()...'; echo get_version_number() . "\n";
    echo 'get_magic_quotes_gpc()...'; 
    if (function_exists("get_magic_quotes_gpc")) {
        echo (get_magic_quotes_gpc() ? "TRUE" : "FALSE") . "\n";
    } else {
        echo "NOT EXISTS (FALSE)\n";
    }
    echo "********************************** DEBUGGING **</pre>";
    error_reporting($olderr);
}

/**
 * Get the time since the last call
 * 
 * @return float Time sonce last call
 */
function get_execution_time()
{
    static $microtime_start = null;
    if ($microtime_start === null) {
        $microtime_start = microtime(true);
        return 0.0;
    }
    return microtime(true) - $microtime_start;
}

/**
 * Reload $setting_data if necessary
 * 
 * @return array $setting_data
 */
function get_setting_data() 
{
    static $setting_data;
    if (!$setting_data) {
        $setting_data = array(
        'set-text-h-frameheight-no-audio' => 
        array("dft" => '140', "num" => 1, "min" => 10, "max" => 999),
        'set-text-h-frameheight-with-audio' => 
        array("dft" => '200', "num" => 1, "min" => 10, "max" => 999),
        'set-text-l-framewidth-percent' => 
        array("dft" => '50', "num" => 1, "min" => 5, "max" => 95),
        'set-text-r-frameheight-percent' => 
        array("dft" => '50', "num" => 1, "min" => 5, "max" => 95),
        'set-test-h-frameheight' => 
        array("dft" => '140', "num" => 1, "min" => 10, "max" => 999),
        'set-test-l-framewidth-percent' => 
        array("dft" => '50', "num" => 1, "min" => 5, "max" => 95),
        'set-test-r-frameheight-percent' => 
        array("dft" => '50', "num" => 1, "min" => 5, "max" => 95),
        'set-words-to-do-buttons' => 
        array("dft" => '1', "num" => 0),
        'set-tooltip-mode' => 
        array("dft" => '2', "num" => 0),
        'set-display-text-frame-term-translation' => 
        array("dft" => '1', "num" => 0),
        'set-text-frame-annotation-position' => 
        array("dft" => '2', "num" => 0),
        'set-test-main-frame-waiting-time' => 
        array("dft" => '0', "num" => 1, "min" => 0, "max" => 9999),
        'set-test-edit-frame-waiting-time' => 
        array("dft" => '500', "num" => 1, "min" => 0, "max" => 99999999),
        'set-test-sentence-count' => 
        array("dft" => '1', "num" => 0),
        'set-tts' => 
        array("dft" => '1', "num" => 0),
        'set-term-sentence-count' => 
        array("dft" => '1', "num" => 0),
        'set-archivedtexts-per-page' => 
        array("dft" => '100', "num" => 1, "min" => 1, "max" => 9999),
        'set-texts-per-page' => 
        array("dft" => '10', "num" => 1, "min" => 1, "max" => 9999),
        'set-terms-per-page' => 
        array("dft" => '100', "num" => 1, "min" => 1, "max" => 9999),
        'set-tags-per-page' => 
        array("dft" => '100', "num" => 1, "min" => 1, "max" => 9999),
        'set-articles-per-page' => 
        array("dft" => '10', "num" => 1, "min" => 1, "max" => 9999),
        'set-feeds-per-page' => 
        array("dft" => '50', "num" => 1, "min" => 1, "max" => 9999),
        'set-max-articles-with-text' => 
        array("dft" => '100', "num" => 1, "min" => 1, "max" => 9999),
        'set-max-articles-without-text' => 
        array("dft" => '250', "num" => 1, "min" => 1, "max" => 9999),
        'set-max-texts-per-feed' => 
        array("dft" => '20', "num" => 1, "min" => 1, "max" => 9999),
        'set-ggl-translation-per-page' => 
        array("dft" => '100', "num" => 1, "min" => 1, "max" => 9999),
        'set-regex-mode' => 
        array("dft" => '', "num" => 0),
        'set-theme_dir' => 
        array("dft" => 'themes/default/', "num" => 0),
        'set-text-visit-statuses-via-key' => 
        array("dft" => '', "num" => 0),
        'set-term-translation-delimiters' => 
        array("dft" => '/;|', "num" => 0),
        'set-mobile-display-mode' => 
        array("dft" => '0', "num" => 0),
        'set-similar-terms-count' => 
        array("dft" => '0', "num" => 1, "min" => 0, "max" => 9)
        );
    }
    return $setting_data;
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
 * @param  mixed  $var  A printable variable to debug
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
 * Return an associative array of all possible statuses 
 * 
 * @return array[] Statues, keys are 1, 2, 3, 4, 5, 98, 99. 
 * Values are associative arrays of keys abbr and name 
 */  
function get_statuses() 
{
    static $statuses;
    if (!$statuses) {
        $statuses = array(
        1 => array("abbr" =>   "1", "name" => "Learning"),
        2 => array("abbr" =>   "2", "name" => "Learning"),
        3 => array("abbr" =>   "3", "name" => "Learning"),
        4 => array("abbr" =>   "4", "name" => "Learning"),
        5 => array("abbr" =>   "5", "name" => "Learned"),
        99 => array("abbr" => "WKn", "name" => "Well Known"),
        98 => array("abbr" => "Ign", "name" => "Ignored"),
        );
    }
    return $statuses;
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

/**
 * Get a request when possible. Otherwise, return an empty string.
 * 
 * @param  string $s Request key
 * @return string Trimmed request or empty string
 */
function getreq($s) 
{
    if (isset($_REQUEST[$s]) ) {
        return trim($_REQUEST[$s]);
    } else {
        return ''; 
    }
}

/**
 * Get a session variable when possible. Otherwise, return an empty string.
 * 
 * @param  string $s Session variable key
 * @return string Trimmed sesseion variable or empty string
 */
function getsess($s) 
{
    if (isset($_SESSION[$s]) ) {
        return trim($_SESSION[$s]);
    } else {
        return ''; 
    }
}

/**
 * Get the base URL of the application
 * 
 * @return string base URL
 */
function url_base() 
{
    $url = parse_url("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
    $r = $url["scheme"] . "://" . $url["host"];
    if (isset($url["port"])) { 
        $r .= ":" . $url["port"]; 
    }
    if(isset($url["path"])) {
        $b = basename($url["path"]);
        if (substr($b, -4) == ".php" || substr($b, -4) == ".htm" || substr($b, -5) == ".html") { 
            $r .= dirname($url["path"]); 
        }
        else {
            $r .= $url["path"]; 
        }
    }
    if (substr($r, -1) !== "/") { 
        $r .= "/"; 
    }
    return $r;
}


/**
 * Echo the path of a file using the theme directory. Echo the base file name of file is not found
 * 
 * @param string $filename Filename
 */
function print_file_path($filename)
{
    echo get_file_path($filename);
}

/**
 * Get the path of a file using the theme directory
 * 
 * @param string $filename Filename
 * 
 * @return string string|string[]|null File path if it exists, otherwise the filename
 */
function get_file_path($filename)
{
    $file = getSettingWithDefault('set-theme-dir').preg_replace('/.*\//', '', $filename);
    if (file_exists($file)) { 
        return $file; 
    }
    return $filename;
}

/**
 * Make a random score for a new word.
 * 
 * @param 'iv'|'id'|'u'|string $type Type of insertion
 * 
 * @return string SQL code to use
 */
function make_score_random_insert_update($type) 
{
    // $type='iv'/'id'/'u'
    if ($type == 'iv') {
        return ' WoTodayScore, WoTomorrowScore, WoRandom ';
    } 
    if ($type == 'id') {
        return ' ' . getsqlscoreformula(2) . ', ' . getsqlscoreformula(3) . ', RAND() ';
    } 
    if ($type == 'u') {
        return ' WoTodayScore = ' . getsqlscoreformula(2) . ', WoTomorrowScore = ' . getsqlscoreformula(3) . ', WoRandom = RAND() ';
    } 
    return '';
}

/**
 * SQL formula for computing score.
 * 
 * @param int $method Score for tomorrow (2), the day after it (3) or never (any value).
 * 
 * @return string SQL score coputation string
 */
function getsqlscoreformula($method) 
{
    // 
    // Formula: {{{2.4^{Status}+Status-Days-1} over Status -2.4} over 0.14325248}
        
    if ($method == 3) { 
        return '
        GREATEST(-125, CASE 
            WHEN WoStatus > 5 THEN 100 
            WHEN WoStatus = 1 THEN ROUND(-7 -7 * DATEDIFF(NOW(),WoStatusChanged)) 
            WHEN WoStatus = 2 THEN ROUND(3.4 - 3.5 * DATEDIFF(NOW(),WoStatusChanged)) 
            WHEN WoStatus = 3 THEN ROUND(17.7 - 2.3 * DATEDIFF(NOW(),WoStatusChanged)) 
            WHEN WoStatus = 4 THEN ROUND(44.65 - 1.75 * DATEDIFF(NOW(),WoStatusChanged)) 
            WHEN WoStatus = 5 THEN ROUND(98.6 - 1.4 * DATEDIFF(NOW(),WoStatusChanged)) 
        END)';
    }
    if ($method == 2) { 
        return '
        GREATEST(-125, CASE 
            WHEN WoStatus > 5 THEN 100
            WHEN WoStatus = 1 THEN ROUND(-7 * DATEDIFF(NOW(),WoStatusChanged))
            WHEN WoStatus = 2 THEN ROUND(6.9 - 3.5 * DATEDIFF(NOW(),WoStatusChanged))
            WHEN WoStatus = 3 THEN ROUND(20 - 2.3 * DATEDIFF(NOW(),WoStatusChanged))
            WHEN WoStatus = 4 THEN ROUND(46.4 - 1.75 * DATEDIFF(NOW(),WoStatusChanged))
            WHEN WoStatus = 5 THEN ROUND(100 - 1.4 * DATEDIFF(NOW(),WoStatusChanged))
        END)';
    } 
    return '0';
}


/**
 * Start a standard page with a complete header and a non-closed body.
 * 
 * @param  string $titletext Title of the page
 * @param  string $addcss    Some CSS to be embed in a style tag
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
    <!-- 
        <?php echo file_get_contents("UNLICENSE.md");?> 
    -->
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
    <script type="text/javascript" src="js/sorttable.js" charset="utf-8"></script>
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
?>