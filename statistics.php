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
Call: statistics.php
Display statistics
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

pagestart('My Statistics',true);

?>
<h4>Breakdown by Language and Term Status<br />(Click on numbers to see the list of terms)</h4>
<table class="tab3" cellspacing="0" cellpadding="5">
<tr>
<th class="th1">Language</th>
<th class="th1">Total<br /></th>
<th class="th1">Active<br />(1..5)</th>
<th class="th1">Learning<br />(1..4)</th>
<th class="th1">Unknown<br />(1)</th>
<th class="th1">Learning<br />(2)</th>
<th class="th1">Learning<br />(3)</th>
<th class="th1">Learning<br />(4)</th>
<th class="th1">Learned<br />(5)</th>
<th class="th1">Well<br />Known<br />(99)</th>
<th class="th1">Known<br />(5+99)</th>
<th class="th1">Ign.<br />(98)</th>
</tr>
<?php

$sql = 'SELECT LgID, LgName FROM languages ORDER BY LgName';
$res = mysql_query($sql);		
if ($res == FALSE) die("Invalid Query: $sql");
while ($dsatz = mysql_fetch_assoc($res)) {
	$lang = $dsatz['LgID'];
	$s1 = get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus = 1');
	$s2 = get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus = 2');
	$s3 = get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus = 3');
	$s4 = get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus = 4');
	$s5 = get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus = 5');
	$s98 = get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus = 98');
	$s99 = get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus = 99');
	$all = $s1 + $s2 + $s3 + $s4 + $s5 + $s98 + $s99;
	echo '<tr>';
	echo '<td class="td1">' . tohtml($dsatz['LgName']) . '</td>';
	echo '<td class="td1 center"><a href="edit_words.php?page=1&amp;text=&amp;query=&amp;filterlang=' . $lang . '&amp;status="><b>' . $all . '</b></a></td>';
	echo '<td class="td1 center"><a href="edit_words.php?page=1&amp;text=&amp;query=&amp;filterlang=' . $lang . '&amp;status=15"><b>' . ($s1+$s2+$s3+$s4+$s5) . '</b></a></td>';
	echo '<td class="td1 center"><a href="edit_words.php?page=1&amp;text=&amp;query=&amp;filterlang=' . $lang . '&amp;status=14"><b>' . ($s1+$s2+$s3+$s4) . '</b></a></td>';
	echo '<td class="td1 center"><span class="status1">&nbsp;<a href="edit_words.php?page=1&amp;text=&amp;query=&amp;filterlang=' . $lang . '&amp;status=1">' . $s1 . '</a>&nbsp;</span></td>';
	echo '<td class="td1 center"><span class="status2">&nbsp;<a href="edit_words.php?page=1&amp;text=&amp;query=&amp;filterlang=' . $lang . '&amp;status=2">' . $s2 . '</a>&nbsp;</span></td>';
	echo '<td class="td1 center"><span class="status3">&nbsp;<a href="edit_words.php?page=1&amp;text=&amp;query=&amp;filterlang=' . $lang . '&amp;status=3">' . $s3 . '</a>&nbsp;</span></td>';
	echo '<td class="td1 center"><span class="status4">&nbsp;<a href="edit_words.php?page=1&amp;text=&amp;query=&amp;filterlang=' . $lang . '&amp;status=4">' . $s4 . '</a>&nbsp;</span></td>';
	echo '<td class="td1 center"><span class="status5">&nbsp;<a href="edit_words.php?page=1&amp;text=&amp;query=&amp;filterlang=' . $lang . '&amp;status=5">' . $s5 . '</a>&nbsp;</span></td>';
	echo '<td class="td1 center"><span class="status99">&nbsp;<a href="edit_words.php?page=1&amp;text=&amp;query=&amp;filterlang=' . $lang . '&amp;status=99">' . $s99 . '</a>&nbsp;</span></td>';
	echo '<td class="td1 center"><span class="status5stat">&nbsp;<a href="edit_words.php?page=1&amp;text=&amp;query=&amp;filterlang=' . $lang . '&amp;status=599"><b>' . ($s5 + $s99) . '</b></a>&nbsp;</span></td>';
	echo '<td class="td1 center"><span class="status98">&nbsp;<a href="edit_words.php?page=1&amp;text=&amp;query=&amp;filterlang=' . $lang . '&amp;status=98"><b>' . $s98 . '</b></a>&nbsp;</span></td>';
	echo '</tr>';
}
mysql_free_result($res);
$s1 = get_first_value('select count(WoID) as value from words where WoStatus = 1');
$s2 = get_first_value('select count(WoID) as value from words where WoStatus = 2');
$s3 = get_first_value('select count(WoID) as value from words where WoStatus = 3');
$s4 = get_first_value('select count(WoID) as value from words where WoStatus = 4');
$s5 = get_first_value('select count(WoID) as value from words where WoStatus = 5');
$s98 = get_first_value('select count(WoID) as value from words where WoStatus = 98');
$s99 = get_first_value('select count(WoID) as value from words where WoStatus = 99');
$all = $s1 + $s2 + $s3 + $s4 + $s5 + $s98 + $s99;
echo '<tr>';
echo '<th class="th1"><b>TOTAL</b></th>';
echo '<th class="th1 center"><a href="edit_words.php?page=1&amp;text=&amp;query=&amp;filterlang=&amp;status="><b>' . $all . '</b></a></th>';
echo '<th class="th1 center"><a href="edit_words.php?page=1&amp;text=&amp;query=&amp;filterlang=&amp;status=15"><b>' . ($s1+$s2+$s3+$s4+$s5) . '</b></a></th>';
echo '<th class="th1 center"><a href="edit_words.php?page=1&amp;text=&amp;query=&amp;filterlang=&amp;status=14"><b>' . ($s1+$s2+$s3+$s4) . '</b></a></th>';
echo '<th class="th1 center"><span class="status1">&nbsp;<a href="edit_words.php?page=1&amp;text=&amp;query=&amp;filterlang=&amp;status=1"><b>' . $s1 . '</b></a>&nbsp;</span></th>';
echo '<th class="th1 center"><span class="status2">&nbsp;<a href="edit_words.php?page=1&amp;text=&amp;query=&amp;filterlang=&amp;status=2"><b>' . $s2 . '</b></a>&nbsp;</span></th>';
echo '<th class="th1 center"><span class="status3">&nbsp;<a href="edit_words.php?page=1&amp;text=&amp;query=&amp;filterlang=&amp;status=3"><b>' . $s3 . '</b></a>&nbsp;</span></th>';
echo '<th class="th1 center"><span class="status4">&nbsp;<a href="edit_words.php?page=1&amp;text=&amp;query=&amp;filterlang=&amp;status=4"><b>' . $s4 . '</b></a>&nbsp;</span></th>';
echo '<th class="th1 center"><span class="status5">&nbsp;<a href="edit_words.php?page=1&amp;text=&amp;query=&amp;filterlang=&amp;status=5"><b>' . $s5 . '</b></a>&nbsp;</span></th>';
echo '<th class="th1 center"><span class="status99">&nbsp;<a href="edit_words.php?page=1&amp;text=&amp;query=&amp;filterlang=&amp;status=99"><b>' . $s99 . '</b></a>&nbsp;</span></th>';
echo '<th class="th1 center"><span class="status5stat">&nbsp;<a href="edit_words.php?page=1&amp;text=&amp;query=&amp;filterlang=&amp;status=599"><b>' . ($s5 + $s99) . '</b></a>&nbsp;</span></th>';
echo '<th class="th1 center"><span class="status98">&nbsp;<a href="edit_words.php?page=1&amp;text=&amp;query=&amp;filterlang=&amp;status=98"><b>' . $s98 . '</b></a>&nbsp;</span></th>';
echo '</tr>';
?>
</table>

<h4>Breakdown by Language and Time Range<br />(Terms created (C), Terms changed status = Activity (A), Terms set to "Known" (K))</h4>
<table class="tab3" cellspacing="0" cellpadding="5">
<tr>
<th class="th1" rowspan="2">Language</th>
<th class="th1" colspan="3">Today</th>
<th class="th1" colspan="3">Yesterday</th>
<th class="th1" colspan="3">Last 7 d</th>
<th class="th1" colspan="3">Last 30 d</th>
<th class="th1" colspan="3">Last 365 d</th>
<th class="th1" colspan="3">All Time</th>
</tr>
<tr>
<th class="th1">C</th>
<th class="th1">A</th>
<th class="th1">K</th>
<th class="th1">C</th>
<th class="th1">A</th>
<th class="th1">K</th>
<th class="th1">C</th>
<th class="th1">A</th>
<th class="th1">K</th>
<th class="th1">C</th>
<th class="th1">A</th>
<th class="th1">K</th>
<th class="th1">C</th>
<th class="th1">A</th>
<th class="th1">K</th>
<th class="th1">C</th>
<th class="th1">A</th>
<th class="th1">K</th>
</tr>
<?php
$sql = 'SELECT LgID, LgName FROM languages ORDER BY LgName';
$res = mysql_query($sql);		
if ($res == FALSE) die("Invalid Query: $sql");
while ($dsatz = mysql_fetch_assoc($res)) {
	$lang = $dsatz['LgID'];
	echo '<tr>';
	echo '<td class="td1">' . tohtml($dsatz['LgName']) . '</td>';
	
	echo '<td class="td1 center"><span class="status1">&nbsp;' . get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus between 1 and 5 and cast(WoCreated as date) = curdate()') . '&nbsp;</span></td>';
	echo '<td class="td1 center"><span class="status3">&nbsp;' . get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus between 1 and 5 and cast(WoStatusChanged as date) = curdate()') . '&nbsp;</span></td>';
	echo '<td class="td1 center"><span class="status5stat">&nbsp;' . get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus in (5, 99) and cast(WoStatusChanged as date) = curdate()') . '&nbsp;</span></td>';
	
	echo '<td class="td1 center"><span class="status1">&nbsp;' . get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus between 1 and 5 and cast(WoCreated as date) = subdate(curdate(), \'1 day\')') . '&nbsp;</span></td>';
	echo '<td class="td1 center"><span class="status3">&nbsp;' . get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus between 1 and 5 and cast(WoStatusChanged as date) = subdate(curdate(), \'1 day\')') . '&nbsp;</span></td>';
	echo '<td class="td1 center"><span class="status5stat">&nbsp;' . get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus in (5, 99) and cast(WoStatusChanged as date) = subdate(curdate(), \'1 day\')') . '&nbsp;</span></td>';
	
	echo '<td class="td1 center"><span class="status1">&nbsp;' . get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus between 1 and 5 and cast(WoCreated as date) between subdate(curdate(), \'6 day\') and curdate()') . '&nbsp;</span></td>';
	echo '<td class="td1 center"><span class="status3">&nbsp;' . get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus between 1 and 5 and cast(WoStatusChanged as date) between subdate(curdate(), \'6 day\') and curdate()') . '&nbsp;</span></td>';
	echo '<td class="td1 center"><span class="status5stat">&nbsp;' . get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus in (5, 99) and cast(WoStatusChanged as date) between subdate(curdate(), \'6 day\') and curdate()') . '&nbsp;</span></td>';
	
	echo '<td class="td1 center"><span class="status1">&nbsp;' . get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus between 1 and 5 and cast(WoCreated as date) between subdate(curdate(), \'29 day\') and curdate()') . '&nbsp;</span></td>';
	echo '<td class="td1 center"><span class="status3">&nbsp;' . get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus between 1 and 5 and cast(WoStatusChanged as date) between subdate(curdate(), \'29 day\') and curdate()') . '&nbsp;</span></td>';
	echo '<td class="td1 center"><span class="status5stat">&nbsp;' . get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus in (5, 99) and cast(WoStatusChanged as date) between subdate(curdate(), \'29 day\') and curdate()') . '&nbsp;</span></td>';
	
	echo '<td class="td1 center"><span class="status1">&nbsp;' . get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus between 1 and 5 and cast(WoCreated as date) between subdate(curdate(), \'364 day\') and curdate()') . '&nbsp;</span></td>';
	echo '<td class="td1 center"><span class="status3">&nbsp;' . get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus between 1 and 5 and cast(WoStatusChanged as date) between subdate(curdate(), \'364 day\') and curdate()') . '&nbsp;</span></td>';
	echo '<td class="td1 center"><span class="status5stat">&nbsp;' . get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus in (5, 99) and cast(WoStatusChanged as date) between subdate(curdate(), \'364 day\') and curdate()') . '&nbsp;</span></td>';
	
	echo '<td class="td1 center"><span class="status1">&nbsp;' . get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus between 1 and 5') . '&nbsp;</span></td>';
	echo '<td class="td1 center"><span class="status3">&nbsp;' . get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus between 1 and 5') . '&nbsp;</span></td>';
	echo '<td class="td1 center"><span class="status5stat">&nbsp;' . get_first_value('select count(WoID) as value from words where WoLgID = ' . $lang . ' and WoStatus in (5, 99)') . '&nbsp;</span></td>';
	
	echo '</tr>';
}
mysql_free_result($res);
echo '<tr>';
echo '<th class="th1"><b>TOTAL</b></th>';

echo '<th class="th1 center"><b><span class="status1">&nbsp;' . get_first_value('select count(WoID) as value from words where WoStatus between 1 and 5 and cast(WoCreated as date) = curdate()') . '&nbsp;</span></b></th>';
echo '<th class="th1 center"><b><span class="status3">&nbsp;' . get_first_value('select count(WoID) as value from words where WoStatus in (1,2,3,4,5,99) and cast(WoStatusChanged as date) = curdate()') . '&nbsp;</span></b></th>';
echo '<th class="th1 center"><b><span class="status5stat">&nbsp;' . get_first_value('select count(WoID) as value from words where WoStatus in (5, 99) and cast(WoStatusChanged as date) = curdate()') . '&nbsp;</span></b></th>';

echo '<th class="th1 center"><b><span class="status1">&nbsp;' . get_first_value('select count(WoID) as value from words where WoStatus between 1 and 5 and cast(WoCreated as date) = subdate(curdate(), \'1 day\')') . '&nbsp;</span></b></th>';
echo '<th class="th1 center"><b><span class="status3">&nbsp;' . get_first_value('select count(WoID) as value from words where WoStatus in (1,2,3,4,5,99) and cast(WoStatusChanged as date) = subdate(curdate(), \'1 day\')') . '&nbsp;</span></b></th>';
echo '<th class="th1 center"><b><span class="status5stat">&nbsp;' . get_first_value('select count(WoID) as value from words where WoStatus in (5, 99) and cast(WoStatusChanged as date) = subdate(curdate(), \'1 day\')') . '&nbsp;</span></b></th>';

echo '<th class="th1 center"><b><span class="status1">&nbsp;' . get_first_value('select count(WoID) as value from words where WoStatus between 1 and 5 and cast(WoCreated as date) between subdate(curdate(), \'6 day\') and curdate()') . '&nbsp;</span></b></th>';
echo '<th class="th1 center"><b><span class="status3">&nbsp;' . get_first_value('select count(WoID) as value from words where WoStatus in (1,2,3,4,5,99) and cast(WoStatusChanged as date) between subdate(curdate(), \'6 day\') and curdate()') . '&nbsp;</span></b></th>';
echo '<th class="th1 center"><b><span class="status5stat">&nbsp;' . get_first_value('select count(WoID) as value from words where WoStatus in (5, 99) and cast(WoStatusChanged as date) between subdate(curdate(), \'6 day\') and curdate()') . '&nbsp;</span></b></th>';

echo '<th class="th1 center"><b><span class="status1">&nbsp;' . get_first_value('select count(WoID) as value from words where WoStatus between 1 and 5 and cast(WoCreated as date) between subdate(curdate(), \'29 day\') and curdate()') . '&nbsp;</span></b></th>';
echo '<th class="th1 center"><b><span class="status3">&nbsp;' . get_first_value('select count(WoID) as value from words where WoStatus in (1,2,3,4,5,99) and cast(WoStatusChanged as date) between subdate(curdate(), \'29 day\') and curdate()') . '&nbsp;</span></b></th>';
echo '<th class="th1 center"><b><span class="status5stat">&nbsp;' . get_first_value('select count(WoID) as value from words where WoStatus in (5, 99) and cast(WoStatusChanged as date) between subdate(curdate(), \'29 day\') and curdate()') . '&nbsp;</span></b></th>';

echo '<th class="th1 center"><b><span class="status1">&nbsp;' . get_first_value('select count(WoID) as value from words where WoStatus between 1 and 5 and cast(WoCreated as date) between subdate(curdate(), \'364 day\') and curdate()') . '&nbsp;</span></b></th>';
echo '<th class="th1 center"><b><span class="status3">&nbsp;' . get_first_value('select count(WoID) as value from words where WoStatus in (1,2,3,4,5,99) and cast(WoStatusChanged as date) between subdate(curdate(), \'364 day\') and curdate()') . '&nbsp;</span></b></th>';
echo '<th class="th1 center"><b><span class="status5stat">&nbsp;' . get_first_value('select count(WoID) as value from words where WoStatus in (5, 99) and cast(WoStatusChanged as date) between subdate(curdate(), \'364 day\') and curdate()') . '&nbsp;</span></b></th>';

echo '<th class="th1 center"><b><span class="status1">&nbsp;' . get_first_value('select count(WoID) as value from words where WoStatus between 1 and 5') . '&nbsp;</span></b></th>';
echo '<th class="th1 center"><b><span class="status3">&nbsp;' . get_first_value('select count(WoID) as value from words where WoStatus in (1,2,3,4,5,99)') . '&nbsp;</span></b></th>';
echo '<th class="th1 center"><b><span class="status5stat">&nbsp;' . get_first_value('select count(WoID) as value from words where WoStatus in (5, 99)') . '&nbsp;</span></b></th>';

echo '</tr>';
?>
</table> 
<p><input type="button" value="&lt;&lt; Back" onclick="location.href='index.php';" /></p>
<?php

pageend();

?>