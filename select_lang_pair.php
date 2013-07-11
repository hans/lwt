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
Call: upload_words.php?....
      ... op=Import ... do the import 
Import terms from file or Text area
***************************************************************/

require_once( 'settings.inc.php' );
require_once( 'connect.inc.php' );
require_once( 'dbutils.inc.php' );
require_once( 'utilities.inc.php' );
require_once( 'langdefs.inc.php' );

function get_wizard_selectoptions() {
	global $langDefs;
	$r = "<option value=\"\" selected=\"selected\">[Choose...]</option>";
	$keys = array_keys($langDefs);
	foreach ($keys as $item) {
		$r .= "<option value=\"" . $item . "\">" . $item . "</option>";
	}
	return $r;
}

pagestart_nobody('Language Settings Wizard','body {background-color: #FFFACD;}');

?>

<script type="text/javascript">
//<![CDATA[

<?php echo "var LANGDEFS = " . json_encode($langDefs) . ";\n"; ?>

function wizard_go() {
	var l1 = $('#l1').val();
	var l2 = $('#l2').val();
	if (l1 == '') {
		alert ('Please choose your native language (L1)!');
		return;
	}
	if (l2 == '') {
		alert ('Please choose your language you want to read/study (L2)!');
		return;
	}
	if (l2 == l1) {
		alert ('L1 L2 Languages must not be equal!');
		return;
	}
	var w = window.opener;
	if (typeof w == 'undefined') {
		alert ('Language setting cannot be set. Please try again.');
		wizard_exit();
	}
	var context = w.document;
	$('input[name="LgName"]',context).val(l2);	
	$('input[name="LgDict1URI"]',context).val(
		'glosbe_api.php?from=' + LANGDEFS[l2][0] + '&dest=' + 
		LANGDEFS[l1][0] + '&phrase=###'
		);	
	$('input[name="LgGoogleTranslateURI"]',context).val(
		'*http://translate.google.com/?ie=UTF-8&sl=' + 
		LANGDEFS[l2][1] + '&tl=' + LANGDEFS[l1][1] + '&text=###'
		);	
	$('select[name="LgTextSize"]',context).val(LANGDEFS[l2][2] ? 200 : 150);	
	$('input[name="LgRegexpSplitSentences"]',context).val(LANGDEFS[l2][4]);	
	$('input[name="LgRegexpWordCharacters"]',context).val(LANGDEFS[l2][3]);	
	$('select[name="LgSplitEachChar"]',context).val(LANGDEFS[l2][5] ? 1 : 0);	
	$('select[name="LgRemoveSpaces"]',context).val(LANGDEFS[l2][6] ? 1 : 0);	
	$('select[name="LgRightToLeft"]',context).val(LANGDEFS[l2][7] ? 1 : 0);	
	wizard_exit();
}

function wizard_exit() {
	window.close();
}

//]]>
</script>

<div class="center">

<p class="wizard">
<img src="icn/wizard.png" title="Language Settings Wizard" alt="Language Settings Wizard" />
</p>

<h3 class="wizard">
Language Settings Wizard
</h3>

<p class="wizard">
<b>My Native language is:</b>
<br />
L1: 
<select name="l1" id="l1">
<?php echo get_wizard_selectoptions(); ?>
</select>
</p>

<p class="wizard">
<b>I want to study:</b>
<br />
L2: 
<select name="l2" id="l2">
<?php echo get_wizard_selectoptions(); ?>
</select>
</p>

<p class="wizard">
<input type="button" value="Set Language Settings" onclick="wizard_go();" />
</p>

<p class="wizard">
<input type="button" value="Cancel" onclick="wizard_exit();" />
</p>

</div>

<?php

pageend();

?>