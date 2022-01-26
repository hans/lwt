<?php


/**
 * \file
 * \brief Start a test (frameset)
 * 
 * Call: do_test.php?lang=[langid]
 * Call: do_test.php?text=[textid]
 * Call: do_test.php?selection=1  (SQL via $_SESSION['testsql'])
 * 
 * @package Lwt
 * @author  LWT Project <lwt-project@hotmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/do__test_8php.html
 * @since   1.0.3
 */

require_once 'inc/session_utility.php';
require_once 'inc/mobile_interactions.php';
require_once 'do_test_header.php';    
require_once 'do_test_test.php';
require_once 'do_test_table.php';

/**
 * Find the L2 language name.
 * 
 * @return string Language name
 * 
 * @global string $tbpref Database table prefix
 */
function get_l2_language_name()
{
    global $tbpref;

    $lang = 'L2';
    if (getreq('lang') != '') {
        $langid = (int) getreq('lang');
        $lang = (string) get_first_value(
            'SELECT LgName AS value FROM ' . $tbpref . 'languages 
            WHERE LgID = ' . $langid . '
            LIMIT 1'
        ); 
    } else if (getreq('text') != '') {
        $textid = (int) getreq('text');
        $lang = (string) get_first_value(
            'SELECT LgName AS value 
            FROM ' . $tbpref . 'texts
            NATURAL JOIN ' . $tbpref . 'languages
            WHERE TxID = ' . $textid . '
            LIMIT 1'
        );
    } else if (getreq('selection')) { 
        $testsql = $_SESSION['testsql'];
        $cntlang = get_first_value(
            'SELECT count(distinct WoLgID) AS value FROM ' . $testsql
        );
        if ($cntlang == 1) {
            $lang = (string) get_first_value(
                'SELECT LgName AS value 
                FROM ' . $tbpref . 'languages, ' . $testsql . ' AND LgID = WoLgID 
                LIMIT 1'
            ); 
        }

    }

    return $lang;
}

/**
 * Find the appropiate property to add to the test.
 * It uses requests provided to the page.
 * 
 * @return string Some URL property
 */
function get_test_property()
{
    if (isset($_REQUEST['selection']) && isset($_SESSION['testsql'])) { 
        return "selection=" . $_REQUEST['selection']; 
    } 
    if (isset($_REQUEST['lang'])) { 
        return "lang=" . $_REQUEST['lang']; 
    } 
    if (isset($_REQUEST['text'])) { 
        return "text=" . $_REQUEST['text']; 
    } 
    return '';
}

/**
 * Make the content of the mobile page.
 * 
 * @param string $property URL property
 * 
 * @return void
 */
function do_test_mobile_page_content($property) 
{
    ?>
<div id="frame-h">
    <iframe id="frame-h-2" 
    src="do_test_header.php?<?php echo $property; ?>" scrolling="yes" name="h">
    </iframe>
</div>
<div id="frame-ro">
    <iframe id="frame-ro-2" src="empty.html" scrolling="yes" name="ro"></iframe>
</div>
<div id="frame-l">
    <iframe  id="frame-l-2" src="empty.html" scrolling="yes" name="l"></iframe>
</div>
<div id="frame-ru">
    <iframe id="frame-ru-2" src="empty.html" scrolling="yes" name="ru"></iframe>
</div>
        <?php 
}

/**
 * Make the mobile test page.
 * 
 * @param string $property URL property for HEADER
 * 
 * @return void
 */
function do_test_mobile_page($property) 
{
    do_frameset_mobile_css();
    do_frameset_mobile_js();
    do_test_mobile_page_content($property);
}

/**
 * Make the desktop test page
 * 
 * @param string $property URL property for HEADER
 * 
 * @return void
 */
function do_test_desktop_page($property) 
{
    $language = get_l2_language_name();
?>
<div style="width: 95%; height: 100%;" onclick="setTimeout(hideRightFrames, 1000);">
    <div id="frame-h">
        <?php
    start_test_header_page($language);
        ?>
    </div>
    <hr />
    <div id="frame-l">
        <?php
    if (getreq('type') == 'table') {
        do_test_table();
    } else {
        do_test_test_content();
    }
        ?>
    </div>
</div>
<div id="frames-r" style="position: fixed; top: 0; right: -100%; width: 100%; height: 100%;" 
onclick="hideRightFrames();">
    <!-- iFrames wrapper for events -->
    <div style="margin-left: 50%; height: 99%;">
        <iframe src="empty.html" scrolling="auto" name="ro" style="height: 50%; width: 100%;">
            Your browser doesn't support iFrames, update it!
        </iframe>
        <iframe src="empty.html" scrolling="auto" name="ru" style="height: 50%; width: 100%;">
            Your browser doesn't support iFrames, update it!
        </iframe>
    </div>
</div>
<?php
}

/**
 * Start the test page.
 * 
 * @param string $p Some property to add to the URL of do_test_test.php.
 * @param bool   $mobile Set to true to use mobile mode.
 * 
 * @return void
 */
function do_test_page($p, $mobile)
{
    pagestart_nobody('Test');
    
    if ($mobile && false) {
        do_test_mobile_page($p);
    } else {
        do_test_desktop_page($p);
    }

    pageend();
}


/**
 * Main function to try to start a test page.
 *
 * If unsifficiant arguments are provided to
 * the page, the page will be redirected to
 * edit_texts.php.
 */
function try_start_test($p): void
{
    if ($p != '') {
        do_test_page($p, is_mobile());
    } else {
        header("Location: edit_texts.php");
        exit();
    }
}

if (get_test_property() != '') {
    try_start_test(get_test_property());
}
?>
