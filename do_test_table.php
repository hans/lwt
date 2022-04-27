<?php

/**
 * \file
 * \brief Show test frame with vocab table
 * 
 * Call: do_test_table.php?lang=[langid]
 * Call: do_test_test.php?text=[textid]
 * Call: do_test_test.php?&selection=1 (SQL via $_SESSION['testsql'])
 * 
 * @package Lwt
 * @author  LWT Project <lwt-project@hotmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/do__test__table_8php.html
 * @since   1.5.4
 */

require_once 'inc/session_utility.php';

/**
 * Set sql request for the word test.
 * 
 * @return string SQL request string
 * 
 * @global string $tbpref Table prefix
 */
function get_test_table_sql()
{
    global $tbpref;
    if (isset($_REQUEST['selection']) && isset($_SESSION['testsql'])) { 
        $testsql = $_SESSION['testsql'];
        $cntlang = get_first_value('SELECT count(distinct WoLgID) AS value FROM ' . $testsql);
        if ($cntlang > 1) {
            //pagestart('', false);
            echo '<p>Sorry - The selected terms are in ' . $cntlang . 
            ' languages, but tests are only possible in one language at a time.</p>';
            //pageend();
            exit();
        }
    } else if (isset($_REQUEST['lang'])) {
        $testsql = ' ' . $tbpref . 'words where WoLgID = ' . $_REQUEST['lang'] . ' ';
    } else if (isset($_REQUEST['text'])) {
        $testsql = ' ' . $tbpref . 'words, ' . $tbpref . 'textitems2 
        WHERE Ti2LgID = WoLgID AND Ti2WoID = WoID AND Ti2TxID = ' . $_REQUEST['text'] . ' ';
    } else { 
        my_die("do_test_table.php called with wrong parameters"); 
    }
    return $testsql;
}


function do_test_table_language_settings($testsql)
{
    global $tbpref;

    $lang = get_first_value('SELECT WoLgID AS value FROM ' . $testsql . ' LIMIT 1');

    if (!isset($lang)) {
        echo '<p class="center">&nbsp;<br />
        Sorry - No terms to display or to test at this time.</p>';
        pageend();
        exit();
    }

    $sql = 'SELECT LgTextSize, LgRegexpWordCharacters, LgRightToLeft 
    FROM ' . $tbpref . 'languages WHERE LgID = ' . $lang;
    $res = do_mysqli_query($sql);
    $record = mysqli_fetch_assoc($res);
    mysqli_free_result($res);
    return $record;
}

function get_test_table_settings() 
{
    $currenttabletestsetting1 = getSettingZeroOrOne('currenttabletestsetting1', 1);
    $currenttabletestsetting2 = getSettingZeroOrOne('currenttabletestsetting2', 1);
    $currenttabletestsetting3 = getSettingZeroOrOne('currenttabletestsetting3', 0);
    $currenttabletestsetting4 = getSettingZeroOrOne('currenttabletestsetting4', 1);
    $currenttabletestsetting5 = getSettingZeroOrOne('currenttabletestsetting5', 0);
    $currenttabletestsetting6 = getSettingZeroOrOne('currenttabletestsetting6', 1);
    return array(
        $currenttabletestsetting1, $currenttabletestsetting2, $currenttabletestsetting3, 
        $currenttabletestsetting4, $currenttabletestsetting5, $currenttabletestsetting6
    );
}

function do_test_table_javascript()
{
?>
<script type="text/javascript">
//<![CDATA[
    $(document).ready( function() {
        $('#cbEdit').change(function() {
            if($('#cbEdit').is(':checked')) {
                $('td:nth-child(1),th:nth-child(1)').show();
                do_ajax_save_setting('currenttabletestsetting1','1');
            } else { 
                $('td:nth-child(1),th:nth-child(1)').hide();
                do_ajax_save_setting('currenttabletestsetting1','0');
            }
            $('th,td').css('border-top-left-radius','').css('border-bottom-left-radius','');
            $('th:visible').eq(0).css('border-top-left-radius','inherit')
            .css('border-bottom-left-radius','0px');
            $('tr:last-child>td:visible').eq(0).css('border-bottom-left-radius','inherit');                    
        });
        
        $('#cbStatus').change(function() {
            if($('#cbStatus').is(':checked')) {
                $('td:nth-child(2),th:nth-child(2)').show();
                do_ajax_save_setting('currenttabletestsetting2','1');
            } else { 
                $('td:nth-child(2),th:nth-child(2)').hide();
                do_ajax_save_setting('currenttabletestsetting2','0');
            }
            $('th,td').css('border-top-left-radius','').css('border-bottom-left-radius','');
            $('th:visible').eq(0).css('border-top-left-radius','inherit').css('border-bottom-left-radius','0px');
            $('tr:last-child>td:visible').eq(0).css('border-bottom-left-radius','inherit');                    
        });
        
        $('#cbTerm').change(function() {
            if($('#cbTerm').is(':checked')) {
                $('td:nth-child(3)').css('color', 'black').css('cursor', 'auto');
                do_ajax_save_setting('currenttabletestsetting3','1');
            } else { 
                $('td:nth-child(3)').css('color', 'white').css('cursor', 'pointer');
                do_ajax_save_setting('currenttabletestsetting3','0');
            }
        });
        
        $('#cbTrans').change(function() {
            if($('#cbTrans').is(':checked')) {
                $('td:nth-child(4)').css('color', 'black').css('cursor', 'auto');
                do_ajax_save_setting('currenttabletestsetting4','1');
            } else {
                $('td:nth-child(4)').css('color', 'white').css('cursor', 'pointer');
                do_ajax_save_setting('currenttabletestsetting4','0');
            }
        });
        
        $('#cbRom').change(function() {
            if($('#cbRom').is(':checked')) {
                $('td:nth-child(5),th:nth-child(5)').show();
                do_ajax_save_setting('currenttabletestsetting5','1');
            } else {
                $('td:nth-child(5),th:nth-child(5)').hide();
                do_ajax_save_setting('currenttabletestsetting5','0');
            }
            $('th,td').css('border-top-right-radius','').css('border-bottom-right-radius','');
            $('th:visible:last').css('border-top-right-radius','inherit');
            $('tr:last-child>td:visible:last').css('border-bottom-right-radius','inherit');                    
        });
        
        $('#cbSentence').change(function() {
            if($('#cbSentence').is(':checked')) {
                $('td:nth-child(6),th:nth-child(6)').show();
                do_ajax_save_setting('currenttabletestsetting6','1');
            } else {
                $('td:nth-child(6),th:nth-child(6)').hide();
                do_ajax_save_setting('currenttabletestsetting6','0');
            }
            $('th,td').css('border-top-right-radius','').css('border-bottom-right-radius','');
            $('th:visible:last').css('border-top-right-radius','inherit');
            $('tr:last-child>td:visible:last').css('border-bottom-right-radius','inherit');                    
        });
        
        $('td').on('click', function() {
            $(this).css('color', 'black').css('cursor', 'auto');
        });
        
        $('td').css('background-color', 'white');
        
        $('#cbEdit').change();
        $('#cbStatus').change();
        $('#cbTerm').change();
        $('#cbTrans').change();
        $('#cbRom').change();
        $('#cbSentence').change();
        
    });
//]]>
</script>
<?php
}


function do_test_table_settings($settings)
{
?>
<p>
    <input type="checkbox" id="cbEdit" <?php echo get_checked($settings[0]); ?> /> Edit
    <input type="checkbox" id="cbStatus" <?php echo get_checked($settings[1]); ?> /> Status
    <input type="checkbox" id="cbTerm" <?php echo get_checked($settings[2]); ?> /> Term
    <input type="checkbox" id="cbTrans" <?php echo get_checked($settings[3]); ?> /> Translation
    <input type="checkbox" id="cbRom" <?php echo get_checked($settings[4]); ?> /> Romanization
    <input type="checkbox" id="cbSentence" <?php echo get_checked($settings[5]); ?> /> Sentence
</p>
<?php
}


function do_test_table_header()
{
?>
    <tr>
        <th class="th1">Ed</th>
        <th class="th1 clickable">Status</th>
        <th class="th1 clickable">Term</th>
        <th class="th1 clickable">Translation</th>
        <th class="th1 clickable">Romanization</th>
        <th class="th1 clickable">Sentence</th>
    </tr>
<?php
}

function do_test_table_table_content($lang_record, $testsql) 
{
    global $debug;

    $textsize = round(((int)$lang_record['LgTextSize']-100)/2, 0)+100;
    
    $regexword = $lang_record['LgRegexpWordCharacters'];
    $rtlScript = $lang_record['LgRightToLeft'];
    $span1 = ($rtlScript ? '<span dir="rtl">' : '');
    $span2 = ($rtlScript ? '</span>' : '');

    $sql = 'SELECT DISTINCT WoID, WoText, WoTranslation, WoRomanization, 
    WoSentence, WoStatus, WoTodayScore As Score 
    FROM ' . $testsql . ' AND WoStatus BETWEEN 1 AND 5 
    AND WoTranslation != \'\' AND WoTranslation != \'*\' 
    ORDER BY WoTodayScore, WoRandom*RAND()';

    if ($debug) { 
        echo $sql; 
    }
    $res = do_mysqli_query($sql);
    while ($record = mysqli_fetch_assoc($res)) {
        do_test_table_row($record, $regexword, $textsize, $span1, $span2);
    }
    mysqli_free_result($res);
}

function do_test_table_row($record, $regexword, $textsize, $span1, $span2)
{
    $sent = tohtml(repl_tab_nl($record["WoSentence"]));
    $sent1 = str_replace(
        "{", ' <b>[', str_replace(
            "}", ']</b> ', 
            mask_term_in_sentence($sent, $regexword)
        )
    );
    ?>
<tr>
    <td class="td1 center" nowrap="nowrap">
        <a 
            href="edit_tword.php?wid=<?php echo $record['WoID']; ?>" target="ro"
            onclick="showRightFrames();"
        >
            <img src="icn/sticky-note--pencil.png" title="Edit Term" alt="Edit Term" />
        </a>
    </td>
    <td class="td1 center" nowrap="nowrap">
        <span id="STAT<?php echo $record['WoID']; ?>">
            <?php echo make_status_controls_test_table($record['Score'], $record['WoStatus'], $record['WoID']); ?>
        </span>
    </td>
    <td class="td1 center" style="font-size:<?php echo $textsize; ?>%;">
        <?php echo $span1; ?>
        <span id="TERM<?php echo $record['WoID']; ?>">
            <?php echo tohtml($record['WoText']); ?>
        </span>
        <?php echo $span2; ?>
    </td>
    <td class="td1 center">
        <span id="TRAN<?php echo $record['WoID']; ?>">
            <?php echo tohtml($record['WoTranslation']); ?>
        </span>
    </td>
    <td class="td1 center">
        <span id="ROMA<?php echo $record['WoID']; ?>">
            <?php echo tohtml($record['WoRomanization']); ?>
        </span>
    </td>
    <td class="td1 center" style="color:#000;">
        <?php echo $span1; ?>
        <span id="SENT<?php echo $record['WoID']; ?>">
        <?php echo $sent1; ?></span><?php echo $span2; ?>
    </td>
</tr>
<?php
}

function do_test_table()
{
    //pagestart_nobody('', 'html, body { margin:3px; padding:0; }');
    $testsql = get_test_table_sql();
    $lang_record = do_test_table_language_settings($testsql);
    $settings = get_test_table_settings();
    do_test_table_javascript();
    do_test_table_settings($settings);

    echo '<table class="sortable tab1" style="width:auto;" cellspacing="0" cellpadding="5">';
    
    do_test_table_header();
    do_test_table_table_content($lang_record, $testsql);
    echo '</table>';

    //pageend();
}
?>
