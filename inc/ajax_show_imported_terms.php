<?php
/**
 * \file
 * \brief Launch an AJAX query to show imported terms
 * 
 * Call: inc/ajax_show_imported_terms?last_update=[last_update]&page=[page number]&count=[count]&rt=[rtl]
 *  
 * @package Lwt
 * @author  andreask7 <andreask7@users.noreply.github.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/ajax__show__imported__terms_8php.html
 * @since   1.6.0-fork
 */


require_once __DIR__ . '/session_utility.php';

/**
 * Get the list of imported terms and start the display.
 * 
 * @param int $recno          Record number
 * @param int $currentpage    Current page
 * @param string $last_update Last update
 * 
 * @return string SQL-formatted query to limit the number of results
 */
function get_imported_terms($recno, $currentpage, $last_update)
{
    $maxperpage = 100;
    
    $pages = intval(($recno-1) / $maxperpage) + 1;
        
    if ($currentpage < 1) { 
        $currentpage = 1; 
    }
    if ($currentpage > $pages) { 
        $currentpage = $pages; 
    }
    $limit = ' LIMIT ' . (($currentpage-1) * $maxperpage) . ',' . $maxperpage;
    ?>
<table class="tab1"  cellspacing="0" cellpadding="2">
    <tr>
        <th class="th1" colspan="2" nowrap="nowrap">
            <span id="recno"><?php echo $recno; ?></span> 
            Term<?php echo ($recno==1?'':'s'); ?>
        </th>
        <th class="th1" colspan="1" nowrap="nowrap">
            <?php
    if ($currentpage > 1) {
            ?>
            &nbsp; &nbsp;
            <img src="icn/control-stop-180.png" title="First Page" alt="First Page" onclick="$('#res_data').load('inc/ajax_show_imported_terms.php',{'last_update':'<?php echo $last_update; ?>','count':$('#recno').text(),'page':'1'}); return false;" />
            &nbsp;
            <img  src="icn/control-180.png" title="Previous Page" alt="Previous Page" onclick="$('#res_data').load('inc/ajax_show_imported_terms.php',{'last_update':'<?php echo $last_update; ?>','count':$('#recno').text(),'page':'<?php echo $currentpage-1; ?>'}); return false;" />
            &nbsp;
            <?php
    } else {
            ?>
            &nbsp; &nbsp;
            <img src="<?php print_file_path('icn/placeholder.png');?>" alt="-" />&nbsp;
            <img src="<?php print_file_path('icn/placeholder.png');?>" alt="-" />&nbsp;
            <?php
    }
            ?>
            Page
            <?php
    if ($pages==1) { 
        echo '1'; 
    } else {
            ?>
            <select name="page" onchange="{val=document.form1.page.options[document.form1.page.selectedIndex].value;$('#res_data').load('inc/ajax_show_imported_terms.php',{'last_update':'<?php echo $last_update; ?>','count':$('#recno').text(),'page':val}); return false;}">
                <?php echo get_paging_selectoptions($currentpage, $pages); ?>
            </select>
            <?php
    }
    echo ' of ' . $pages . '&nbsp; ';
    if ($currentpage < $pages) { 
            ?>
            <img src="icn/control.png" title="Next Page" alt="Next Page" onclick="$('#res_data').load('inc/ajax_show_imported_terms.php',{'last_update':'<?php echo $last_update; ?>','count':$('#recno').text(),'page':'<?php echo $currentpage+1; ?>'}); return false;" />&nbsp;
            <img src="icn/control-stop.png" title="Last Page" alt="Last Page" onclick="$('#res_data').load('inc/ajax_show_imported_terms.php',{'last_update':'<?php echo $last_update; ?>','count':$('#recno').text(),'page':'<?php echo $pages; ?>'}); return false;" />&nbsp; &nbsp;
            <?php 
    } else {
            ?>
            <img src="<?php print_file_path('icn/placeholder.png');?>" alt="-" />
            &nbsp;
            <img src="<?php print_file_path('icn/placeholder.png');?>" alt="-" />
            &nbsp; &nbsp; 
            <?php
    }
            ?>
        </th>
    </table>
    <?php
    return $limit;
}

/**
 * Show the imported terms.
 * 
 * @param string $last_update Last update
 * @param string $limit       SQL-formatted query to limit the number of results
 */
function show_imported_terms($last_update, $limit, $rtl)
{
    global $tbpref;
    echo '<table class="sortable tab1"  cellspacing="0" cellpadding="5">
    <tr>';
    ?>
        <th class="th1 clickable">Term /<br />Romanization</th>
        <th class="th1 clickable">Translation</th>
        <th class="th1 sorttable_nosort">Tags</th>
        <th class="th1 sorttable_nosort">Se.</th>
        <th class="th1 sorttable_numeric clickable">Status</th>
    <?php
    $sql = 'SELECT WoID, WoText, WoTranslation, WoRomanization, WoSentence, 
    IFNULL(WoSentence, \'\') LIKE CONCAT(\'%{\', WoText, \'}%\') AS SentOK, 
    WoStatus, 
    IFNULL(
        CONCAT(
            \'[\', 
            group_concat(DISTINCT TgText ORDER BY TgText separator \', \'),
            \']\'
        ), \'\'
    ) AS taglist 
    FROM (
        (' . $tbpref . 'words LEFT JOIN ' . $tbpref . 'wordtags ON WoID = WtWoID) 
        LEFT JOIN ' . $tbpref . 'tags ON TgID = WtTgID
    ) 
    WHERE WoStatusChanged > ' . convert_string_to_sqlsyntax($last_update) . ' 
    GROUP BY WoID ' . $limit;
    $res = do_mysqli_query($sql);
    while ($record = mysqli_fetch_assoc($res)) {
        echo '<tr>';
        echo '<td class="td1">
            <span' . ($rtl ? ' dir="rtl" ' : '') . '>' . tohtml($record['WoText']) . '</span>' . 
            ($record['WoRomanization'] != '' ? (' / <span id="roman' . $record['WoID'] . '" class="edit_area clickedit">' . 
            tohtml(repl_tab_nl($record['WoRomanization'])) . '</span>') : 
            (' / <span id="roman' . $record['WoID'] . '" class="edit_area clickedit">*</span>')) . 
        '</td>';
        echo '<td class="td1">
            <span id="trans' . $record['WoID'] . '" class="edit_area clickedit">' . tohtml(repl_tab_nl($record['WoTranslation'])) . '</span>
        </td>';
        echo '<td class="td1"><span class="smallgray2">' . tohtml($record['taglist']) . '</span></td>';
        echo '<td class="td1 center">
            <b>' . 
                ($record['SentOK']!=0 ? '<img src="icn/status.png" title="' . tohtml($record['WoSentence']) . '" alt="Yes" />' : 
                '<img src="icn/status-busy.png" title="(No valid sentence)" alt="No" />') . 
            '</b>
        </td>';
        echo '<td class="td1 center" title="' . tohtml(get_status_name($record['WoStatus'])) . '">' . tohtml(get_status_abbr($record['WoStatus'])) . '</td>';
        echo "</tr>\n";
    }
    mysqli_free_result($res);
    echo "</table>";
    ?>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.edit_area').editable(
                'inline_edit.php', 
                { 
                    type      : 'textarea',
                    indicator : '<img src="icn/indicator.gif">',
                    tooltip   : 'Click to edit...',
                    submit    : 'Save',
                    cancel    : 'Cancel',
                    rows      : 3,
                    cols      : 35
                }
            );
        });
    </script>;
    <?php
}

/**
 * Show the imported terms.
 * 
 * @param string $last_update Last update
 * @param int    $currentpage Current number of the page
 * @param int    $recno       Number of record
 * @param bool   $rtl         True if this language is right-to-left
 * 
 * @return void
 */
function do_ajax_show_imported_terms($last_update, $currentpage, $recno, $rtl)
{
    chdir('..');
    if($recno > 0) { 
        $limit = get_imported_terms($recno, $currentpage, $last_update);
        show_imported_terms($last_update, $limit, $rtl);
    } else if ($recno==0) {
        echo '<p>No terms imported.</p>';
    }
}

if (
    isset($_REQUEST['last_update']) && isset($_REQUEST['page']) && 
    isset($_REQUEST['count']) && isset($_REQUEST['rtl'])
    ) {
    do_ajax_show_imported_terms(
        $_REQUEST['last_update'], 
        (int)$_REQUEST['page'], 
        (int)$_REQUEST['count'], 
        (bool)$_REQUEST['rtl']
    );
}

?>
