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
Analyse DB tables and offer management functions
***************************************************************/

include "settings.inc.php";
include "connect.inc.php";
include "utilities.inc.php";

if (isset($_REQUEST['delpref'])) {
	if($_REQUEST['delpref'] !== '-') {
		$dummy = runsql('DROP TABLE ' . $_REQUEST['delpref'] . 'archivedtexts','');
		$dummy = runsql('DROP TABLE ' . $_REQUEST['delpref'] . 'archtexttags','');
		$dummy = runsql('DROP TABLE ' . $_REQUEST['delpref'] . 'languages','');
		$dummy = runsql('DROP TABLE ' . $_REQUEST['delpref'] . 'sentences','');
		$dummy = runsql('DROP TABLE ' . $_REQUEST['delpref'] . 'tags','');
		$dummy = runsql('DROP TABLE ' . $_REQUEST['delpref'] . 'tags2','');
		$dummy = runsql('DROP TABLE ' . $_REQUEST['delpref'] . 'textitems','');
		$dummy = runsql('DROP TABLE ' . $_REQUEST['delpref'] . 'texts','');
		$dummy = runsql('DROP TABLE ' . $_REQUEST['delpref'] . 'texttags','');
		$dummy = runsql('DROP TABLE ' . $_REQUEST['delpref'] . 'words','');
		$dummy = runsql('DROP TABLE ' . $_REQUEST['delpref'] . 'wordtags', '');
		$dummy = runsql('DROP TABLE ' . $_REQUEST['delpref'] . 'settings', '');
		if ($_REQUEST['delpref'] == $tbpref) {
			$tbpref = "";
			LWTTableSet ("current_table_prefix", $tbpref);
		}
	}
}

elseif (isset($_REQUEST['newpref'])) {
	$tbpref = $_REQUEST['newpref'];
	LWTTableSet ("current_table_prefix", $tbpref);
	header("Location: index.php");
	exit(); 
}

elseif (isset($_REQUEST['prefix'])) {
	if($_REQUEST['prefix'] !== '-') {
		$tbpref = $_REQUEST['prefix'];
		LWTTableSet ("current_table_prefix", $tbpref);
		header("Location: index.php");
		exit(); 
	}
}

pagestart('Select, Create or Delete a Table set',true);

if ($fixed_tbpref) {
?>
	<p>&nbsp;<br />Not possible!<br/>Reason: $tbpref is set to a fixed value in <i>connect.inc.php</i>. Please remove the definition <b>$tbpref = '<?php echo $tbpref; ?>';</b> in <i>connect.inc.php</i>.</p>
	<p><input type="button" value="&lt;&lt; Back" onclick="history.back();" /></p>
<?php	
} else {

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
<option value="<?php echo tohtml($value); ?>"><?php echo ($value == '' ? '* Default Table Set *' : tohtml($value)); ?></option>
<?php
}
?>
</select> 
<input type="submit" name="op" value="Start LWT" />
</form>

<p>&nbsp;</p><hr /><p>&nbsp;</p>

<form name="f2" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return check_table_prefix(document.f2.newpref.value);">
<b>CREATE</b> a new table set: <input type="text" name="newpref" value="" maxlength="20" size="20" />
<input type="submit" name="op" value="Create new table set and start LWT" />
</form>

<p>&nbsp;</p><hr /><p>&nbsp;</p>

<form name="f3" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return confirm('DELETING A TABLE SET!!\n\nAre you sure?');">
<b>DELETE</b> an existent table set: <select name="delpref">
<option value="-" selected="selected">[Choose...]</option>
<?php
foreach ($prefix as $value) {
	if ( $value != '') {
?>
<option value="<?php echo tohtml($value); ?>"><?php echo tohtml($value); ?></option>
<?php
	}
}
?>
</select> 
<input type="submit" name="op" value="Delete table set" />
</form>
<p>(You cannot delete the Default Table Set.)
<?php

}

pageend();

?>