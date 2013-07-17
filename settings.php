<?php

/**************************************************************
"Learning with Texts" (LWT) is free and unencumbered software 
released into the PUBLIC DOMAIN.

Anyone is free to copy, modify, publish, use, compile, sell, or
distribute this software, either in source code form or as a
compiled binary, for any purpose, commercial or non-commercial,
and by any means.

In jurisdictions that recognize copyright laws, the author or
authors of this software dedicate any and all copyright
interest in the software to the public domain. We make this
dedication for the benefit of the public at large and to the 
detriment of our heirs and successors. We intend this 
dedication to be an overt act of relinquishment in perpetuity
of all present and future rights to this software under
copyright law.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE 
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE
AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS BE LIABLE 
FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN 
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN 
THE SOFTWARE.

For more information, please refer to [http://unlicense.org/].
***************************************************************/

/**************************************************************
Call: settings.php?....
      ... op=Save ... do save 
      ... op=Reset ... do reset to defaults 
Preferences / Settings 
***************************************************************/

require_once( 'settings.inc.php' );
require_once( 'connect.inc.php' );
require_once( 'dbutils.inc.php' );
require_once( 'utilities.inc.php' );

pagestart('Settings/Preferences',true);
$message = '';

if (isset($_REQUEST['op'])) {

	if ($_REQUEST['op'] == 'Save') {

		saveSetting('set-text-h-frameheight-no-audio',
		$_REQUEST['set-text-h-frameheight-no-audio']);
	
		saveSetting('set-text-h-frameheight-with-audio',
		$_REQUEST['set-text-h-frameheight-with-audio']);
	
		saveSetting('set-text-l-framewidth-percent',
		$_REQUEST['set-text-l-framewidth-percent']);
	
		saveSetting('set-text-r-frameheight-percent',
		$_REQUEST['set-text-r-frameheight-percent']);
	
		saveSetting('set-test-h-frameheight',
		$_REQUEST['set-test-h-frameheight']);
		
		saveSetting('set-test-l-framewidth-percent',
		$_REQUEST['set-test-l-framewidth-percent']);
	
		saveSetting('set-test-r-frameheight-percent',
		$_REQUEST['set-test-r-frameheight-percent']);
	
		saveSetting('set-player-skin-name',
		$_REQUEST['set-player-skin-name']);
	
		saveSetting('set-test-main-frame-waiting-time',
		$_REQUEST['set-test-main-frame-waiting-time']);
	
		saveSetting('set-test-edit-frame-waiting-time',
		$_REQUEST['set-test-edit-frame-waiting-time']);

		saveSetting('set-test-sentence-count',
		$_REQUEST['set-test-sentence-count']);
	
		saveSetting('set-term-sentence-count',
		$_REQUEST['set-term-sentence-count']);
	
		saveSetting('set-archivedtexts-per-page',
		$_REQUEST['set-archivedtexts-per-page']);
	
		saveSetting('set-texts-per-page',
		$_REQUEST['set-texts-per-page']);
	
		saveSetting('set-terms-per-page',
		$_REQUEST['set-terms-per-page']);
	
		saveSetting('set-tags-per-page',
		$_REQUEST['set-tags-per-page']);
	
		saveSetting('set-show-text-word-counts',
		$_REQUEST['set-show-text-word-counts']);
	
		saveSetting('set-text-visit-statuses-via-key',
		$_REQUEST['set-text-visit-statuses-via-key']);
	
		saveSetting('set-term-translation-delimiters',
		$_REQUEST['set-term-translation-delimiters']);
		
		saveSetting('set-mobile-display-mode',
		$_REQUEST['set-mobile-display-mode']);

	
		$message = 'Settings saved';
	
	} else {
	
		$dummy = runsql("delete from " . $tbpref . "settings where StKey like 'set-%'",''); 
	
		$message = 'All Settings reset to default values';
	
	}

}

echo error_message_with_hide($message,1);

?>
<script type="text/javascript" src="js/unloadformcheck.js" charset="utf-8"></script>
<form class="validate" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<table class="tab3" cellspacing="0" cellpadding="5">
<!-- ******************************************************* -->
<tr>
<th class="th1">Group</th>
<th class="th1">Description</th>
<th class="th1" colspan="2">Value</th>
</tr>
<!-- ******************************************************* -->
<tr>
<th class="th1 center" rowspan="5">Read Text<br />Screen</th>
<td class="td1 center">Height of left top frame<br /><b>without</b> audioplayer</td>
<td class="td1 center">
<input class="notempty right setfocus" type="text" 
name="set-text-h-frameheight-no-audio" 
value="<?php echo tohtml(getSettingWithDefault('set-text-h-frameheight-no-audio')); ?>" maxlength="3" size="3" /><br />Pixel </td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Height of left top frame<br /><b>with</b> audioplayer</td>
<td class="td1 center">
<input class="notempty right" type="text" 
name="set-text-h-frameheight-with-audio" 
value="<?php echo tohtml(getSettingWithDefault('set-text-h-frameheight-with-audio')); ?>" maxlength="3" size="3" /><br />Pixel </td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Width of left frames</td>
<td class="td1 center">
<input class="notempty right" type="text" 
name="set-text-l-framewidth-percent" 
value="<?php echo tohtml(getSettingWithDefault('set-text-l-framewidth-percent')); ?>" maxlength="2" size="2" /><br />Percent </td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Height of right top frame</td>
<td class="td1 center">
<input class="notempty right" type="text" 
name="set-text-r-frameheight-percent" 
value="<?php echo tohtml(getSettingWithDefault('set-text-r-frameheight-percent')); ?>" maxlength="2" size="2" /><br />Percent </td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Audioplayer Skin</td>
<td class="td1 center">
<select name="set-player-skin-name" class="notempty">
<?php
echo get_audioplayer_selectoptions(
getSettingWithDefault('set-player-skin-name'));
?>
</select>
</td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<th class="th1 center middle" rowspan="5">Test<br />Screen</th>
<td class="td1 center">Height of left top frame</td>
<td class="td1 center">
<input class="notempty right" type="text" 
name="set-test-h-frameheight" 
value="<?php echo tohtml(getSettingWithDefault('set-test-h-frameheight')); ?>" maxlength="3" size="3" /><br />Pixel </td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Width of left frames</td>
<td class="td1 center">
<input class="notempty right" type="text" 
name="set-test-l-framewidth-percent" 
value="<?php echo tohtml(getSettingWithDefault('set-test-l-framewidth-percent')); ?>" maxlength="2" size="2" /><br />Percent </td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Height of right top frame</td>
<td class="td1 center">
<input class="notempty right" type="text" 
name="set-test-r-frameheight-percent" 
value="<?php echo tohtml(getSettingWithDefault('set-test-r-frameheight-percent')); ?>" maxlength="2" size="2" /><br />Percent </td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Waiting time after assessment<br />to display next test<br /></td>
<td class="td1 center">
<input class="notempty right" type="text" 
name="set-test-main-frame-waiting-time" 
value="<?php echo tohtml(getSettingWithDefault('set-test-main-frame-waiting-time')); ?>" maxlength="4" size="4" /><br />Milliseconds </td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Waiting Time <br />to clear the message/edit frame </td>
<td class="td1 center">
<input class="notempty right" type="text" 
name="set-test-edit-frame-waiting-time" 
value="<?php echo tohtml(getSettingWithDefault('set-test-edit-frame-waiting-time')); ?>" maxlength="8" size="8" /><br />Milliseconds </td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<th class="th1 center">Frame Set<br />Display Mode</th>
<td class="td1 center">Select how frame sets are<br />displayed on different devices</td>
<td class="td1 center">
<select name="set-mobile-display-mode">
<?php
echo get_mobile_display_mode_selectoptions(
getSettingWithDefault('set-mobile-display-mode'), true, true, true);
?>
</select>
</td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<th class="th1 center">Reading</th>
<td class="td1 center">Visit only saved terms with status(es)...<br />(via keystrokes RIGHT, SPACE, LEFT, etc.)</td>
<td class="td1 center">
<select name="set-text-visit-statuses-via-key">
<?php
echo get_wordstatus_selectoptions(
getSettingWithDefault('set-text-visit-statuses-via-key'), true, true, true);
?>
</select>
</td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<th class="th1 center">Testing</th>
<td class="td1 center">Number of sentences <br />displayed from text, if available</td>
<td class="td1 center">
<select name="set-test-sentence-count" class="notempty">
<?php
echo get_sentence_count_selectoptions(
getSettingWithDefault('set-test-sentence-count'));
?>
</select>
</td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<th class="th1 center">Term Sentence<br />Generation</th>
<td class="td1 center">Number of sentences <br />generated from text, if available</td>
<td class="td1 center">
<select name="set-term-sentence-count" class="notempty">
<?php
echo get_sentence_count_selectoptions(
getSettingWithDefault('set-term-sentence-count'));
?>
</select>
</td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<th class="th1 center">Term<br />Translations</th>
<td class="td1 center">List of characters that<br />delimit different translations<br />(used in annotation selection)</td>
<td class="td1 center">
<input class="notempty center" type="text" 
name="set-term-translation-delimiters" 
value="<?php echo tohtml(getSettingWithDefault('set-term-translation-delimiters')); ?>" maxlength="8" size="8" /></td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<th class="th1 center" rowspan="5">Text, Term &amp;<br />Tag Tables</th>
<td class="td1 center">Texts per Page</td>
<td class="td1 center">
<input class="notempty right" type="text" 
name="set-texts-per-page" 
value="<?php echo tohtml(getSettingWithDefault('set-texts-per-page')); ?>" maxlength="4" size="4" /> </td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Show Word Counts of Texts immediately<br />(<b>"No"</b> loads a long text table faster)</td>
<td class="td1 center">
<select name="set-show-text-word-counts" class="notempty">
<?php
echo get_yesno_selectoptions(
getSettingWithDefault('set-show-text-word-counts'));
?>
</select>
</td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Archived Texts per Page</td>
<td class="td1 center">
<input class="notempty right" type="text" 
name="set-archivedtexts-per-page" 
value="<?php echo tohtml(getSettingWithDefault('set-archivedtexts-per-page')); ?>" maxlength="4" size="4" /></td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Terms per Page</td>
<td class="td1 center">
<input class="notempty right" type="text" 
name="set-terms-per-page" 
value="<?php echo tohtml(getSettingWithDefault('set-terms-per-page')); ?>" maxlength="4" size="4" /></td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Tags per Page</td>
<td class="td1 center">
<input class="notempty right" type="text" 
name="set-tags-per-page" 
value="<?php echo tohtml(getSettingWithDefault('set-tags-per-page')); ?>" maxlength="4" size="4" /></td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 right" colspan="4"> 
<input type="button" value="&lt;&lt; Back" onclick="{resetDirty(); location.href='index.php';}" />&nbsp; &nbsp; | &nbsp; &nbsp;
<input type="button" value="Reset all settings to default" onclick="{resetDirty(); location.href='settings.php?op=reset';}" />&nbsp; &nbsp; | &nbsp; &nbsp;
<input type="submit" name="op" value="Save" /></td>
</tr>
<!-- ******************************************************* -->
</table>
</form>

<?php

pageend();

?>