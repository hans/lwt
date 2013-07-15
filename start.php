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
Call: start.php
Analyse DB tables, select Table Set, start LWT
***************************************************************/

require_once( 'settings.inc.php' );
require_once( 'connect.inc.php' );
require_once( 'dbutils.inc.php' );
require_once( 'utilities.inc.php' );

if ($fixed_tbpref) {
	header("Location: index.php");
	exit(); 
}

if (isset($_REQUEST['prefix'])) {
	if($_REQUEST['prefix'] !== '-') {
		$tbpref = $_REQUEST['prefix'];
		LWTTableSet ("current_table_prefix", $tbpref);
		header("Location: index.php");
		exit(); 
	}
}

$prefix = getprefixes();

if (count($prefix) == 0) {
	$tbpref = '';
	LWTTableSet ("current_table_prefix", $tbpref);
	header("Location: index.php");
	exit(); 
}

pagestart('Select Table Set',false);

?>

<table class="tab1" style="width: auto;" cellspacing="0" cellpadding="5">

<tr>
<th class="th1">
<form name="f1" class="inline" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p>Select: <select name="prefix">
<option value="" <?php echo ($tbpref == '' ? 'selected="selected"': ''); ?>>Default Table Set</option>
<?php
foreach ($prefix as $value) {
?>
<option value="<?php echo tohtml($value); ?>" <?php echo (substr($tbpref,0,-1) == $value ? 'selected="selected"': ''); ?>><?php echo tohtml($value); ?></option>
<?php
}
?>
</select> 
</p>
<p class="center"><input type="submit" name="op" value="Start LWT" />
</p>
</form>
</th>
</tr>

</table>

<?php

pageend();

?>