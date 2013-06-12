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
Call: select.php
Analyse DB tables and offer selection of table prefixes, and
start LWT via redirect
IMPORTANT: $tbpref must NOT be set in connect.inc.php
***************************************************************/

include "settings.inc.php";
include "connect.inc.php";
include "utilities.inc.php";

if (isset($_REQUEST['delpref'])) {
	if($_REQUEST['delpref'] !== '-') {
		$dbpref = $_REQUEST['delpref'];
		$dummy = runsql('DROP TABLE ' . $tbpref . 'archivedtexts','');
		$dummy = runsql('DROP TABLE ' . $tbpref . 'archtexttags','');
		$dummy = runsql('DROP TABLE ' . $tbpref . 'languages','');
		$dummy = runsql('DROP TABLE ' . $tbpref . 'sentences','');
		$dummy = runsql('DROP TABLE ' . $tbpref . 'tags','');
		$dummy = runsql('DROP TABLE ' . $tbpref . 'tags2','');
		$dummy = runsql('DROP TABLE ' . $tbpref . 'textitems','');
		$dummy = runsql('DROP TABLE ' . $tbpref . 'texts','');
		$dummy = runsql('DROP TABLE ' . $tbpref . 'texttags','');
		$dummy = runsql('DROP TABLE ' . $tbpref . 'words','');
		$dummy = runsql('DROP TABLE ' . $tbpref . 'wordtags', '');
		$dummy = runsql('DROP TABLE ' . $tbpref . 'settings', '');
		unset($_SESSION['tbpref']);
		unset($_REQUEST['delpref']);
	}
}

elseif (isset($_REQUEST['newpref'])) {
		$_SESSION['tbpref'] = $_REQUEST['newpref'];
		header("Location: index.php");
		exit(); 
}

elseif (isset($_REQUEST['prefix'])) {
	if($_REQUEST['prefix'] !== '-') {
		$_SESSION['tbpref'] = $_REQUEST['prefix'];
		header("Location: index.php");
		exit(); 
	}
}

pagestart('Select a different table set',true);

$prefix = array();
$res = mysql_query("SHOW TABLES LIKE " . convert_string_to_sqlsyntax_nonull('%settings'));
if ($res == FALSE) die("SHOW TABLES error");
while ($row = mysql_fetch_row($res)) 
	$prefix[] = substr($row[0], 0, -8);
mysql_free_result($res);

?>

<form name="f1" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<b>SELECT</b> an existent table set: <select name="prefix">
<option value="-" selected="selected">[Choose...]</option>
<?php
foreach ($prefix as $value) {
?>
<option value="<?php echo tohtml($value); ?>"><?php echo ($value == '' ? 'No Table Prefix' : tohtml($value)); ?></option>
<?php
}
?>
</select> 
<input type="submit" name="op" value="Start LWT" />
</form>

<p>&nbsp; </p>

<form name="f2" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return check_table_prefix(document.f2.newpref.value);">
<b>CREATE</b> a new table set: <input type="text" name="newpref" value="" maxlength="20" size="20" />
<input type="submit" name="op" value="Create new table set and start LWT" />
</form>

<p>&nbsp; </p>

<form name="f3" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return confirm('DELETING A TABLE SET!!\n\nAre you sure?');">
<b>DELETE</b> an existent table set: <select name="delpref">
<option value="-" selected="selected">[Choose...]</option>
<?php
foreach ($prefix as $value) {
?>
<option value="<?php echo tohtml($value); ?>"><?php echo ($value == '' ? 'No Table Prefix' : tohtml($value)); ?></option>
<?php
}
?>
</select> 
<input type="submit" name="op" value="Delete table set" />
</form>

<?php

pageend();

?>