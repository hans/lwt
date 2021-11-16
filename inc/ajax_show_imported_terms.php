<?php
/**
 * \file
 * \brief Launch an AJAX query to show imported terms
 *  
 * @author andreask7 <andreask7@users.noreply.github.com>
 * @since  1.6.0-fork
 */


require_once __DIR__ . '/session_utility.php';


$last_update=$_REQUEST['last_update'];
$currentpage=$_REQUEST['page'];
    $recno = $_REQUEST['count'];
$rtl = $_REQUEST['rtl'];
    $maxperpage = 100;

    $pages = intval(($recno-1) / $maxperpage) + 1;
    
if ($currentpage < 1) { $currentpage = 1; 
}
if ($currentpage > $pages) { $currentpage = $pages; 
}
    $limit = ' LIMIT ' . (($currentpage-1) * $maxperpage) . ',' . $maxperpage;
if($recno > 0) { ?>
<table class="tab1"  cellspacing="0" cellpadding="2"><tr>
<th class="th1" colspan="2" nowrap="nowrap"><span id="recno">
<?php echo $recno; ?></span> Term<?php echo ($recno==1?'':'s'); ?>
</th><th class="th1" colspan="1" nowrap="nowrap">
<?php
if ($currentpage > 1) { 
?>
&nbsp; &nbsp;<img src="icn/control-stop-180.png" title="First Page" alt="First Page" onclick="$('#res_data').load('inc/ajax_show_imported_terms.php',{'last_update':'<?php echo $last_update; ?>','count':$('#recno').text(),'page':'1'}); return false;" />&nbsp;
<img  src="icn/control-180.png" title="Previous Page" alt="Previous Page" onclick="$('#res_data').load('inc/ajax_show_imported_terms.php',{'last_update':'<?php echo $last_update; ?>','count':$('#recno').text(),'page':'<?php echo $currentpage-1; ?>'}); return false;" />&nbsp;
<?php
} else {
?>
&nbsp; &nbsp;<img src="<?php print_file_path('icn/placeholder.png');?>" alt="-" />&nbsp;
<img src="<?php print_file_path('icn/placeholder.png');?>" alt="-" />&nbsp;
<?php
} 
?>
Page
<?php
if ($pages==1) { echo '1'; 
}
else {
?>
<select name="page" onchange="{val=document.form1.page.options[document.form1.page.selectedIndex].value;$('#res_data').load('inc/ajax_show_imported_terms.php',{'last_update':'<?php echo $last_update; ?>','count':$('#recno').text(),'page':val}); return false;}"><?php echo get_paging_selectoptions($currentpage, $pages); ?></select>
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
<img src="<?php print_file_path('icn/placeholder.png');?>" alt="-" />&nbsp;
<img src="<?php print_file_path('icn/placeholder.png');?>" alt="-" />&nbsp; &nbsp; 
<?php
}
 echo '</th></table>';
}
if ($recno==0) {
?>
<p>No terms imported.</p>
<?php
} else {
?>
<table class="sortable tab1"  cellspacing="0" cellpadding="5">
<tr>
<th class="th1 clickable">Term /<br />Romanization</th>
<th class="th1 clickable">Translation</th>
<th class="th1 sorttable_nosort">Tags</th>
<th class="th1 sorttable_nosort">Se.</th>
<th class="th1 sorttable_numeric clickable">Status</th>
<?php
$sql = 'select WoID, WoText, WoTranslation, WoRomanization, WoSentence, ifnull(WoSentence,\'\') like concat(\'%{\',WoText,\'}%\') as SentOK, WoStatus, ifnull(concat(\'[\',group_concat(distinct TgText order by TgText separator \', \'),\']\'),\'\') as taglist from ((' . $tbpref . 'words left JOIN ' . $tbpref . 'wordtags ON WoID = WtWoID) left join ' . $tbpref . 'tags on TgID = WtTgID) where WoStatusChanged > ' . convert_string_to_sqlsyntax($last_update) . ' group by WoID ' . $limit;
$res = do_mysqli_query($sql);
$cnt=0;
while ($record = mysqli_fetch_assoc($res)) {
    echo '<tr>';
    echo '<td class="td1"><span';
    echo ($rtl ? ' dir="rtl" ' : '') . '>' . tohtml($record['WoText']) . '</span>' . ($record['WoRomanization'] != '' ? (' / <span id="roman' . $record['WoID'] . '" class="edit_area clickedit">' . tohtml(repl_tab_nl($record['WoRomanization'])) . '</span>') : (' / <span id="roman' . $record['WoID'] . '" class="edit_area clickedit">*</span>')) . '</td>';
    echo '<td class="td1"><span id="trans' . $record['WoID'] . '" class="edit_area clickedit">' . tohtml(repl_tab_nl($record['WoTranslation'])) . '</span></td>';
    echo '<td class="td1"><span class="smallgray2">' . tohtml($record['taglist']) . '</span></td>';
    echo '<td class="td1 center"><b>' . ($record['SentOK']!=0 ? '<img src="icn/status.png" title="' . tohtml($record['WoSentence']) . '" alt="Yes" />' : '<img src="icn/status-busy.png" title="(No valid sentence)" alt="No" />') . '</b></td>';
    echo '<td class="td1 center" title="' . tohtml(get_status_name($record['WoStatus'])) . '">' . tohtml(get_status_abbr($record['WoStatus'])) . '</td>';
    echo "</tr>\n";
}
mysqli_free_result($res);
echo "</table>";
echo '<script type="text/javascript">';
echo "$(document).ready( function() {
	$('.edit_area').editable('inline_edit.php', 
		{ 
			type      : 'textarea',
			indicator : '<img src=\"icn/indicator.gif\">',
			tooltip   : 'Click to edit...',
			submit    : 'Save',
			cancel    : 'Cancel',
			rows      : 3,
			cols      : 35
		}
	);
});";
echo '</script>';
}
