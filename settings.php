<?php

/**************************************************************
Call: settings.php?....
      ... op=Save ... do save 
      ... op=Reset ... do reset to defaults 
Preferences / Settings 
 ***************************************************************/

require_once 'inc/session_utility.php';
if (isset($_REQUEST['op'])) {

    if ($_REQUEST['op'] == 'Save') {

        saveSetting(
            'set-theme-dir',
            $_REQUEST['set-theme-dir']
        );
    }
    else {    
        runsql("delete from " . $tbpref . "settings where StKey like 'set-%'", '');
    }
}
pagestart('Settings/Preferences', true);
$message = '';

if (isset($_REQUEST['op'])) {

    if ($_REQUEST['op'] == 'Save') {

        saveSetting(
            'set-text-h-frameheight-no-audio',
            $_REQUEST['set-text-h-frameheight-no-audio']
        );
    
        saveSetting(
            'set-text-h-frameheight-with-audio',
            $_REQUEST['set-text-h-frameheight-with-audio']
        );
    
        saveSetting(
            'set-text-l-framewidth-percent',
            $_REQUEST['set-text-l-framewidth-percent']
        );
    
        saveSetting(
            'set-text-r-frameheight-percent',
            $_REQUEST['set-text-r-frameheight-percent']
        );
    
        saveSetting(
            'set-test-h-frameheight',
            $_REQUEST['set-test-h-frameheight']
        );
        
        saveSetting(
            'set-test-l-framewidth-percent',
            $_REQUEST['set-test-l-framewidth-percent']
        );
    
        saveSetting(
            'set-test-r-frameheight-percent',
            $_REQUEST['set-test-r-frameheight-percent']
        );
    
        saveSetting(
            'set-words-to-do-buttons',
            $_REQUEST['set-words-to-do-buttons']
        );
    
        saveSetting(
            'set-tooltip-mode',
            $_REQUEST['set-tooltip-mode']
        );
    
        saveSetting(
            'set-ggl-translation-per-page',
            $_REQUEST['set-ggl-translation-per-page']
        );
    
        saveSetting(
            'set-test-main-frame-waiting-time',
            $_REQUEST['set-test-main-frame-waiting-time']
        );
    
        saveSetting(
            'set-test-edit-frame-waiting-time',
            $_REQUEST['set-test-edit-frame-waiting-time']
        );

        saveSetting(
            'set-test-sentence-count',
            $_REQUEST['set-test-sentence-count']
        );
    
        saveSetting(
            'set-term-sentence-count',
            $_REQUEST['set-term-sentence-count']
        );
    
        saveSetting(
            'set-tts',
            $_REQUEST['set-tts']
        );
    
        saveSetting(
            'set-archivedtexts-per-page',
            $_REQUEST['set-archivedtexts-per-page']
        );
    
        saveSetting(
            'set-texts-per-page',
            $_REQUEST['set-texts-per-page']
        );
    
        saveSetting(
            'set-terms-per-page',
            $_REQUEST['set-terms-per-page']
        );
    
        saveSetting(
            'set-regex-mode',
            $_REQUEST['set-regex-mode']
        );
    
        saveSetting(
            'set-tags-per-page',
            $_REQUEST['set-tags-per-page']
        );
    
        saveSetting(
            'set-articles-per-page',
            $_REQUEST['set-articles-per-page']
        );
    
        saveSetting(
            'set-feeds-per-page',
            $_REQUEST['set-feeds-per-page']
        );
    
        saveSetting(
            'set-max-articles-with-text',
            $_REQUEST['set-max-articles-with-text']
        );
    
        saveSetting(
            'set-max-articles-without-text',
            $_REQUEST['set-max-articles-without-text']
        );
    
        saveSetting(
            'set-max-texts-per-feed',
            $_REQUEST['set-max-texts-per-feed']
        );
    
        saveSetting(
            'set-text-visit-statuses-via-key',
            $_REQUEST['set-text-visit-statuses-via-key']
        );
    
        saveSetting(
            'set-display-text-frame-term-translation',
            $_REQUEST['set-display-text-frame-term-translation']
        );

        saveSetting(
            'set-text-frame-annotation-position',
            $_REQUEST['set-text-frame-annotation-position']
        );
        
        saveSetting(
            'set-term-translation-delimiters',
            $_REQUEST['set-term-translation-delimiters']
        );
        
        saveSetting(
            'set-mobile-display-mode',
            $_REQUEST['set-mobile-display-mode']
        );

        saveSetting(
            'set-similar-terms-count',
            $_REQUEST['set-similar-terms-count']
        );
    
        $message = 'Settings saved';
    
    } else {
        
        $message = 'All Settings reset to default values';
    
    }

}

echo error_message_with_hide($message, 1);

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
<th class="th1 center" rowspan="1">Appearance</th>
<td class="td1 center">Theme</td>
<td class="td1 center">
<select name="set-theme-dir" class="notempty">
<?php
echo get_themes_selectoptions(
    getSettingWithDefault('set-theme-dir')
);
?>
</select>
</td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<th class="th1 center" rowspan="7">Read Text<br />Screen</th>
<td class="td1 center">Height of left top frame<br /><b>without</b> audioplayer</td>
<td class="td1 center">
<input class="notempty posintnumber right setfocus" type="text" 
name="set-text-h-frameheight-no-audio" data_info="Height of left top frame without audioplayer" value="<?php echo tohtml(getSettingWithDefault('set-text-h-frameheight-no-audio')); ?>" maxlength="3" size="3" /><br />Pixel </td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Height of left top frame<br /><b>with</b> audioplayer</td>
<td class="td1 center">
<input class="notempty posintnumber right" type="text" 
name="set-text-h-frameheight-with-audio" data_info="Height of left top frame with audioplayer" 
value="<?php echo tohtml(getSettingWithDefault('set-text-h-frameheight-with-audio')); ?>" maxlength="3" size="3" /><br />Pixel </td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Width of left frames</td>
<td class="td1 center">
<input class="notempty posintnumber right" type="text" 
name="set-text-l-framewidth-percent" data_info="Width of left frames" 
value="<?php echo tohtml(getSettingWithDefault('set-text-l-framewidth-percent')); ?>" maxlength="2" size="2" /><br />Percent </td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Height of right top frame</td>
<td class="td1 center">
<input class="notempty posintnumber right" type="text" 
name="set-text-r-frameheight-percent"  data_info="Height of right top frame" 
value="<?php echo tohtml(getSettingWithDefault('set-text-r-frameheight-percent')); ?>" maxlength="2" size="2" /><br />Percent </td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Button(s) for "words to do"</td>
<td class="td1 center">
<select name="set-words-to-do-buttons" class="notempty">
<?php 
echo get_words_to_do_buttons_selectoptions(
    getSettingWithDefault('set-words-to-do-buttons')
);
?>
</select>
</td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Tooltips</td>
<td class="td1 center">
<select name="set-tooltip-mode" class="notempty">
<?php
echo get_tooltip_selectoptions(
    getSettingWithDefault('set-tooltip-mode')
);
?>
</select>
</td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">New Term Translations per Page</td>
<td class="td1 center">
<input class="notempty posintnumber right" type="text" 
name="set-ggl-translation-per-page"  data_info="New Term Translations per Page" 
value="<?php echo tohtml(getSettingWithDefault('set-ggl-translation-per-page')); ?>" maxlength="4" size="4" /></td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<th class="th1 center middle" rowspan="5">Test<br />Screen</th>
<td class="td1 center">Height of left top frame</td>
<td class="td1 center">
<input class="notempty posintnumber right" type="text" 
name="set-test-h-frameheight" data_info="Height of left top frame" 
value="<?php echo tohtml(getSettingWithDefault('set-test-h-frameheight')); ?>" maxlength="3" size="3" /><br />Pixel </td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Width of left frames</td>
<td class="td1 center">
<input class="notempty posintnumber right" type="text" 
name="set-test-l-framewidth-percent"  data_info="Width of left frames" 
value="<?php echo tohtml(getSettingWithDefault('set-test-l-framewidth-percent')); ?>" maxlength="2" size="2" /><br />Percent </td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Height of right top frame</td>
<td class="td1 center">
<input class="notempty posintnumber right" type="text" 
name="set-test-r-frameheight-percent"  data_info="Height of right top frame"  
value="<?php echo tohtml(getSettingWithDefault('set-test-r-frameheight-percent')); ?>" maxlength="2" size="2" /><br />Percent </td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Waiting time after assessment<br />to display next test<br /></td>
<td class="td1 center">
<input class="notempty zeroposintnumber right" type="text" 
name="set-test-main-frame-waiting-time" data_info="Waiting time after assessment to display next test" 
value="<?php echo tohtml(getSettingWithDefault('set-test-main-frame-waiting-time')); ?>" maxlength="4" size="4" /><br />Milliseconds </td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Waiting Time <br />to clear the message/edit frame </td>
<td class="td1 center">
<input class="notempty zeroposintnumber right" type="text" 
name="set-test-edit-frame-waiting-time"  data_info="Waiting Time to clear the message/edit frame" 
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
    getSettingWithDefault('set-mobile-display-mode') // , true, true, true what is it???
);
?>
</select>
</td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<th class="th1 center" rowspan="3">Reading</th>
<td class="td1 center">Visit only saved terms with status(es)...<br />(via keystrokes RIGHT, SPACE, LEFT, etc.)</td>
<td class="td1 center">
<select name="set-text-visit-statuses-via-key">
<?php
echo get_wordstatus_selectoptions(
    getSettingWithDefault('set-text-visit-statuses-via-key'), true, true, true
);
?>
</select>
</td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Display translations of terms with status(es)</td>
<td class="td1 center">
<select name="set-display-text-frame-term-translation">
<?php
echo get_wordstatus_selectoptions(
    getSettingWithDefault('set-display-text-frame-term-translation'), true, true, true
);
?>
</select>
</td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Position of translations</td>
<td class="td1 center">
<select name="set-text-frame-annotation-position" class="notempty">
<?php
echo get_annotation_position_selectoptions(
    getSettingWithDefault('set-text-frame-annotation-position')
);
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
    getSettingWithDefault('set-test-sentence-count')
);
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
    getSettingWithDefault('set-term-sentence-count')
);
?>
</select>
</td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<th class="th1 center">Similar<br />Terms</th>
<td class="td1 center">Similar terms to be displayed<br />while adding/editing a term</td>
<td class="td1 center">
<input class="notempty zeroposintnumber right" type="text" 
name="set-similar-terms-count"  data_info="Similar terms to be displayed while adding/editing a term" 
value="<?php echo tohtml(getSettingWithDefault('set-similar-terms-count')); ?>" maxlength="1" size="1" /></td>
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
<th class="th1 center">Text to Speech</th>
<td class="td1 center">Save Audio Files to Disk</td>
<td class="td1 center">
<select name="set-tts" class="notempty">
<?php
echo get_yesno_selectoptions(
    getSettingWithDefault('set-tts')
);
?>
</select>
</td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<th class="th1 center" rowspan="7">Text, Term,<br />Newsfeed &amp;<br />Tag Tables</th>
<td class="td1 center">Texts per Page</td>
<td class="td1 center">
<input class="notempty posintnumber right" type="text" 
name="set-texts-per-page"  data_info="Texts per Page" 
value="<?php echo tohtml(getSettingWithDefault('set-texts-per-page')); ?>" maxlength="4" size="4" /> </td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Archived Texts per Page</td>
<td class="td1 center">
<input class="notempty posintnumber right" type="text" 
name="set-archivedtexts-per-page"  data_info="Archived Texts per Page" 
value="<?php echo tohtml(getSettingWithDefault('set-archivedtexts-per-page')); ?>" maxlength="4" size="4" /></td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Terms per Page</td>
<td class="td1 center">
<input class="notempty posintnumber right" type="text" 
name="set-terms-per-page" data_info="Terms per Page" 
value="<?php echo tohtml(getSettingWithDefault('set-terms-per-page')); ?>" maxlength="4" size="4" /></td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Tags per Page</td>
<td class="td1 center">
<input class="notempty posintnumber right" type="text" 
name="set-tags-per-page"  data_info="Tags per Page" 
value="<?php echo tohtml(getSettingWithDefault('set-tags-per-page')); ?>" maxlength="4" size="4" /></td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Feed Articles per Page</td>
<td class="td1 center">
<input class="notempty posintnumber right" type="text" 
name="set-articles-per-page" data_info="Feed Articles per Page" 
value="<?php echo tohtml(getSettingWithDefault('set-articles-per-page')); ?>" maxlength="4" size="4" /></td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Feeds per Page</td>
<td class="td1 center">
<input class="notempty posintnumber right" type="text" 
name="set-feeds-per-page" data_info="Feeds per Page" 
value="<?php echo tohtml(getSettingWithDefault('set-feeds-per-page')); ?>" maxlength="4" size="4" /></td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Query Mode</td>
<td class="td1 center">
<select name="set-regex-mode">
<?php
echo get_regex_selectoptions(
    getSettingWithDefault('set-regex-mode')
);
?>
</select></td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<th class="th1 center" rowspan="3">Newsfeeds</th>
<td class="td1 center">Max Articles per Feed <b>with</b> cached text</td>
<td class="td1 center">
<input class="notempty posintnumber right" type="text" 
name="set-max-articles-with-text" data_info="Max Articles per Feed with cached text" 
value="<?php echo tohtml(getSettingWithDefault('set-max-articles-with-text')); ?>" maxlength="4" size="4" /></td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Max Articles per Feed <b>without</b> cached text</td>
<td class="td1 center">
<input class="notempty posintnumber right" type="text" 
name="set-max-articles-without-text" data_info="Max Articles per Feed without cached text" 
value="<?php echo tohtml(getSettingWithDefault('set-max-articles-without-text')); ?>" maxlength="4" size="4" /></td>
<td class="td1 center"><img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
</tr>
<!-- ******************************************************* -->
<tr>
<td class="td1 center">Max Texts per Feed<br />(older Texts are moved into "Text Archive")</td>
<td class="td1 center">
<input class="notempty posintnumber right" type="text" 
name="set-max-texts-per-feed" data_info="Max Texts per Feed" 
value="<?php echo tohtml(getSettingWithDefault('set-max-texts-per-feed')); ?>" maxlength="4" size="4" /></td>
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
