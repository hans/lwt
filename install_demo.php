<?php

/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions, 
unless such conditions are required by law.

Developed by J. P. in 2011, 2012, 2013.
***************************************************************/

/**************************************************************
Call: install_demo.php
Install LWT Demo Database
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

$message = '';

// RESTORE DEMO

if (isset($_REQUEST['install'])) {
	$file = getcwd() . '/install_demo_db.sql.gz';
	if ( file_exists($file) ) {
		$handle = gzopen ($file, "r");
		if ($handle === FALSE) {
			$message = "Error: File ' . $file . ' could not be opened";
		} // $handle not OK
		else { // $handle OK
			$lines = 0;
			$ok = 0;
			$errors = 0;
			$drops = 0;
			$inserts = 0;
			$creates = 0;
			$start = 1;
			while (! gzeof($handle)) {
				$sql_line = trim(
					str_replace("\r","",
					str_replace("\n","",
					gzgets($handle, 99999))));
				if ($sql_line != "") {
					if($start) {
						if (strpos($sql_line,"-- lwt-backup-") === false ) {
							$message = "Error: Invalid Restore file (possibly not created by LWT backup)";
							break;
						}
						$start = 0;
						continue;
					}
					if ( substr($sql_line,0,3) !== '-- ' ) {
						$res = mysql_query(insert_prefix_in_sql($sql_line));
						$lines++;
						if ($res == FALSE) $errors++;
						else {
							$ok++;
							if (substr($sql_line,0,11) == "INSERT INTO") $inserts++;
							elseif (substr($sql_line,0,10) == "DROP TABLE") $drops++;
							elseif (substr($sql_line,0,12) == "CREATE TABLE") $creates++;
						}
						// echo $ok . " / " . tohtml(insert_prefix_in_sql($sql_line)) . "<br />";
					}
				}
			} // while (! feof($handle))
			gzclose ($handle);
			if ($errors == 0) {
				runsql('TRUNCATE ' . $tbpref . 'sentences','');
				runsql('TRUNCATE ' . $tbpref . 'textitems','');
				adjust_autoincr('sentences','SeID');
				adjust_autoincr('textitems','TiID');
				$sql = "select TxID, TxLgID from " . $tbpref . "texts";
				$res = mysql_query($sql);		
				if ($res == FALSE) die("Invalid Query: $sql");
				while ($record = mysql_fetch_assoc($res)) {
					$id = $record['TxID'];
					splitText(
						get_first_value('select TxText as value from ' . $tbpref . 'texts where TxID = ' . $id), $record['TxLgID'], $id );
				}
				mysql_free_result($res);
				optimizedb();
				$message = "Success: Demo Database restored - " .
				$lines . " queries - " . $ok . " successful (" . $drops . "/" . $creates . " tables dropped/created, " . $inserts . " records added), " . $errors . " failed.";
			} else {
				$message = "Error: Demo Database NOT restored - " .
				$lines . " queries - " . $ok . " successful (" . $drops . "/" . $creates . " tables dropped/created, " . $inserts . " records added), " . $errors . " failed.";
			}
		} // $handle OK
	} // restore file specified
	else {
		$message = "Error: File ' . $file . ' does not exist";
	}
} 

pagestart('Install LWT Demo Database',true);

echo error_message_with_hide($message,1);

$langcnt = get_first_value('select count(*) as value from ' . $tbpref . 'languages');

if ($tbpref == '') 
	$prefinfo = "(No Table Prefix)";
else
	$prefinfo = "(Table Prefix: <i>" . tohtml($tbpref) . "</i>)";

?>
<form enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return confirm('Are you sure?');">
<table class="tab3" cellspacing="0" cellpadding="5">
<tr>
<th class="th1 center">Install Demo</th>
<td class="td1">
<p class="smallgray2">
The database <i><?php echo tohtml($dbname); ?></i> <?php echo $prefinfo; ?> will be replaced by the LWT demo database.

<?php 
if ($langcnt > 0 ) { 
	?>
	<br /><b>The existent database will be overwritten!</b>
	<?php 
} 
?>

</p>
<p class="right">&nbsp;<br /><span class="red2">YOU MAY LOSE DATA - BE CAREFUL: &nbsp; &nbsp; &nbsp;</span> 
<input type="submit" name="install" value="Install LWT demo database" /></p>
</td>
</tr>
<tr>
<td class="td1 right" colspan="2"> 
<input type="button" value="&lt;&lt; Back to LWT Main Menu" onclick="location.href='index.php';" /></td>
</tr>
</table>
</form>

<?php

pageend();

?>