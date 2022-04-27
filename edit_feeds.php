<?php

require_once 'inc/session_utility.php';

$currentlang = validateLang(processDBParam("filterlang", 'currentlanguage', '', 0));
$currentsort = processDBParam("sort", 'currentmanagefeedssort', '2', 1);
$currentquery = processSessParam("query", "currentmanagefeedsquery", '', 0);
$currentpage = processSessParam("page", "currentmanagefeedspage", '1', 1);
$currentfeed = processSessParam("selected_feed", "currentmanagefeedsfeed", '', 0);
$wh_query = convert_string_to_sqlsyntax(str_replace("*", "%", $currentquery));
$wh_query = ($currentquery != '') ? (' and (NfName like ' . $wh_query . ')') : '';

pagestart('Manage ' . getLanguage($currentlang) . ' Feeds', true);

if(isset($_SESSION['wizard'])) {
    unset($_SESSION['wizard']);
}



if (isset($_REQUEST['markaction'])) {
    if ($_REQUEST['markaction']=='del') {
        $message= runsql('delete from ' . $tbpref . 'feedlinks where FlNfID in(' . $currentfeed . ')', "Article item(s) deleted");
        $message.= runsql('delete from ' . $tbpref . 'newsfeeds where NfID in(' . $currentfeed . ')', " / Newsfeed(s) deleted");
        echo error_message_with_hide($message, 0);unset($message);
    }

    if ($_REQUEST['markaction']=='del_art') {
        $message= runsql('delete from ' . $tbpref . 'feedlinks where FlNfID in(' . $currentfeed . ')', "Article item(s) deleted");
        echo error_message_with_hide($message, 0);unset($message);
        do_mysqli_query('UPDATE ' . $tbpref . 'newsfeeds SET NfUpdate="'.time().'" where NfID in(' . $currentfeed . ')');
    }

    if ($_REQUEST['markaction']=='res_art') {
        $message= runsql('UPDATE ' . $tbpref . 'feedlinks SET FlLink=TRIM(FlLink) where FlNfID in (' . $currentfeed . ')', "Article(s) reset");
        echo error_message_with_hide($message, 0);unset($message);
    }
}
if(isset($_SESSION['feed_loaded'])) {
    foreach($_SESSION['feed_loaded'] as $lf){
        echo "\n<div class=\"msgblue\"><p class=\"hide_message\">+++ ",$lf," +++</p></div>";
    }
    ?>
<script type="text/javascript">
$(".hide_message").delay(2500).slideUp(1000);
</script>
    <?php
    unset($_SESSION['feed_loaded']);
}

if(isset($_REQUEST['update_feed'])) {
    $currentfeed = $_REQUEST['NfID'];
    runsql('UPDATE ' . $tbpref . 'newsfeeds SET NfLgID=' . convert_string_to_sqlsyntax($_REQUEST['NfLgID']) .',NfName=' . convert_string_to_sqlsyntax($_REQUEST['NfName']) .',NfSourceURI=' . convert_string_to_sqlsyntax($_REQUEST['NfSourceURI']) .',NfArticleSectionTags=' . convert_string_to_sqlsyntax($_REQUEST['NfArticleSectionTags']) .',NfFilterTags=' . convert_string_to_sqlsyntax_nonull($_REQUEST['NfFilterTags']) .',NfOptions=' . convert_string_to_sqlsyntax_nonull(rtrim($_REQUEST['NfOptions'], ',')) .' where NfID='.$_REQUEST['NfID'], "");
}

if(isset($_REQUEST['save_feed'])) {
    runsql('insert into ' . $tbpref . 'newsfeeds (NfLgID,NfName,NfSourceURI,NfArticleSectionTags,NfFilterTags,NfOptions) VALUES (' . convert_string_to_sqlsyntax($_REQUEST['NfLgID']) .',' . convert_string_to_sqlsyntax($_REQUEST['NfName']) .',' . convert_string_to_sqlsyntax($_REQUEST['NfSourceURI']) .',' . convert_string_to_sqlsyntax($_REQUEST['NfArticleSectionTags']) .',' . convert_string_to_sqlsyntax_nonull($_REQUEST['NfFilterTags']) .',' . convert_string_to_sqlsyntax_nonull(rtrim($_REQUEST['NfOptions'], ',')) .')', "");
}
if(isset($_REQUEST['load_feed']) || isset($_REQUEST['check_autoupdate']) || (isset($_REQUEST['markaction']) && $_REQUEST['markaction']=='update')) {
    load_feeds($currentfeed);
}    
elseif(isset($_REQUEST['new_feed'])) {
    $result = do_mysqli_query("SELECT LgName,LgID FROM " . $tbpref . "languages where LgName<>'' ORDER BY LgName");
    ?>
<h4>New Feed <a target="_blank" href="docs/info.html#new_feed"><img src="icn/question-frame.png" title="Help" alt="Help" /></a> </h4>
<a href="do_feeds.php?page=1"> My Feeds</a> &nbsp; | &nbsp;
<a href="feed_wizard.php?step=1"><img src="icn/wizard.png" title="new_feed_wizard" alt="new_feed_wizard" /> New Feed Wizard</a>
<br></br>
<form class="validate" action="edit_feeds.php" method="post">
<table class="tab1" cellspacing="0" cellpadding="5">
<tr><td class="td1">Language: </td><td class="td1"><select name="NfLgID">
    <?php
    while($row_l = mysqli_fetch_assoc($result)){
        echo '<option value="' . $row_l['LgID'] . '"';
        if($currentlang===$row_l['LgID']) {
            echo ' selected="selected"';
        }
        echo '>' . $row_l['LgName'] . '</option>';
    }
    mysqli_free_result($result);
    ?>    </select></td></tr>
<tr><td class="td1">
Name: </td><td class="td1"><input class="notempty" style="width:95%" type="text" name="NfName" /> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
</td></tr>
<tr><td class="td1">Newsfeed url: </td><td class="td1"><input class="notempty" style="width:95%" type="text" name="NfSourceURI" /> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
</td></tr>
<tr><td class="td1">Article Section: </td><td class="td1"><input class="notempty" style="width:95%" type="text" name="NfArticleSectionTags" /> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
</td></tr>
<tr><td class="td1">Filter Tags: </td><td class="td1"><input type="text" style="width:95%" name="NfFilterTags" /></td></tr>
<tr><td class="td1">Options: </td>
<td class="td1"><table style="width:100%"><tr><td style="width:35%"><input type="checkbox" name="edit_text" checked="checked" /> Edit Text </td><td><input type="checkbox" name="c_autoupdate" /> Auto Update Interval: <input class="posintnumber" data_info="Auto Update Interval" type="text" size="4" name="autoupdate" disabled />
<select name="autoupdate" disabled><option value="h">Hour(s)</option><option value="d">Day(s)</option><option value="w">Week(s)</option></select></td></tr>
<tr><td><input type="checkbox" name="c_max_links" /> Max. Links: <input class="posintnumber maxint_300" data_info="Max. Links" type="text" size="4" name="max_links" disabled /></td><td><input type="checkbox" name="c_charset" /> Charset: <input type="text" data_info="Charset" size="20" name="charset" disabled /> </td></tr>
<tr><td><input type="checkbox" name="c_max_texts" /> Max. Texts: <input class="posintnumber maxint_30" data_info="Max. Texts" type="text" size="4" name="max_texts" disabled /></td><td><input type="checkbox" name="c_tag" /> Tag: <input type="text" data_info="Tag" size="20" name="tag" disabled /> </td></tr>
<tr><td colspan="2"><input type="checkbox" name="c_article_source" /> Article Source: <input data_info="Article Source" type="text" size="20" name="article_source" disabled /></td></tr>
</table></td></tr>
</table><input type="submit" value="Save" />
<input type="hidden" name="NfOptions" value="" />
<input type="hidden" name="save_feed" value="1" />
<input type="button" value="Cancel" onclick="location.href='edit_feeds.php';" />
</form>
<script type="text/javascript">
$('[name^="c_"]').change(function(){
    if(this.checked){
        $(this).parent().children('input[type="text"]').removeAttr('disabled').addClass("notempty");
        $(this).parent().find('select').removeAttr('disabled');
    }
    else{
        $(this).parent().children('input[type="text"]').attr('disabled','disabled').removeClass("notempty");
        $(this).parent().find('select').attr('disabled','disabled');
    }
});
$('[type="submit"]').on('click', function(){
    var str;
    str=$('[name="edit_text"]:checked').length > 0?"edit_text=1,":"";
    $('[name^="c_"]').each(function(){        
        str+=this.checked ? $(this).parent().children('input[type="text"]').attr('name') + '='
        + $(this).parent().children('input[type="text"]').val()
        +  ($(this).attr('name')=='c_autoupdate' ? $(this).parent().find('select').val() + ',' : ','): '';
    });
    $('input[name="NfOptions"]').val(str);
});
</script>
    <?php
}

elseif(isset($_REQUEST['edit_feed'])) {
    $result = do_mysqli_query("SELECT * FROM " . $tbpref . "newsfeeds WHERE NfID=$currentfeed");
    $row = mysqli_fetch_assoc($result);
    $result = do_mysqli_query("SELECT LgName,LgID FROM " . $tbpref . "languages where LgName<>'' ORDER BY LgName");
    ?>
<h4>Edit Feed <a target="_blank" href="docs/info.html#new_feed"><img src="icn/question-frame.png" title="Help" alt="Help" /></a> </h4>
<a href="do_feeds.php?page=1"> My Feeds</a> &nbsp; | &nbsp;
<a href="feed_wizard.php?step=2&amp;edit_feed=<?php echo $currentfeed;?>"><img src="icn/wizard.png" title="feed_wizard" alt="feed_wizard" /> Feed Wizard</a>
<form class="validate" action="edit_feeds.php" method="post">
<table class="tab1" cellspacing="0" cellpadding="5">
<tr><td class="td1">Language: </td><td class="td1"><select name="NfLgID">
    <?php	
    while($row_l = mysqli_fetch_assoc($result)){
        echo '<option value="' . $row_l['LgID'] . '"';
        if($row['NfLgID']===$row_l['LgID']) {
            echo ' selected="selected"';
        }
        echo '>' . $row_l['LgName'] . '</option>';
    }
    mysqli_free_result($result);
    $auto_upd_i=get_nf_option($row['NfOptions'], 'autoupdate');
    if($auto_upd_i==null) { 
        $auto_upd_v=null; 
    }
    else{
        $auto_upd_v=substr($auto_upd_i, -1);
        $auto_upd_i=substr($auto_upd_i, 0, -1);
    }
    ?>
</select></td></tr>
<tr><td class="td1">
Name: </td><td class="td1"><input class="notempty" style="width:95%" type="text" name="NfName" value="<?php echo tohtml($row['NfName']); ?>" /> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
</td></tr>
<tr><td class="td1">Newsfeed url: </td><td class="td1"><input class="notempty" style="width:95%" type="text" name="NfSourceURI" value="<?php echo tohtml($row['NfSourceURI']); ?>" /> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
</td></tr>
<tr><td class="td1">Article Section: </td><td class="td1"><input class="notempty" style="width:95%" type="text" name="NfArticleSectionTags" value="<?php echo tohtml($row['NfArticleSectionTags']); ?>" /> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
</td></tr>
<tr><td class="td1">Filter Tags: </td><td class="td1"><input type="text" style="width:95%" name="NfFilterTags" value="<?php echo tohtml($row['NfFilterTags']); ?>" /></td></tr>
<tr><td class="td1">Options: </td><td class="td1"><table style="width:100%"><tr><td style="width:35%"><input type="checkbox" name="edit_text"<?php if(get_nf_option($row['NfOptions'], 'edit_text')!==null) { echo ' checked="checked"'; 
} ?> /> Edit Text </td><td><input type="checkbox" name="c_autoupdate"<?php if($auto_upd_i!==null) { 
    echo ' checked="checked"'; 
} ?> /> Auto Update Interval: <input class="posintnumber<?php if(get_nf_option($row['NfOptions'], 'autoupdate')!==null) { 
    echo ' notempty'; 
} ?>" data_info="Auto Update Interval" type="text" size="4" name="autoupdate" value="<?php 
echo $auto_upd_i . '"';
if($auto_upd_i==null) { 
    echo ' disabled'; 
} ?> />
<select name="autoupdate" value="<?php echo $auto_upd_v . '"';if($auto_upd_v==null) { 
    echo ' disabled'; 
} ?>><option value="h"<?php if($auto_upd_v=='h') { 
    echo ' selected="selected"'; 
}?>>Hour(s)</option><option value="d"<?php if($auto_upd_v=='d') {
    echo ' selected="selected"'; 
}?>>Day(s)</option><option value="w"<?php if($auto_upd_v=='w') { 
    echo ' selected="selected"'; 
}?>>Week(s)</option></select></td></tr>
<tr><td><input type="checkbox" name="c_max_links"<?php if(get_nf_option($row['NfOptions'], 'max_links')!==null) { 
    echo ' checked="checked"'; 
} ?> /> Max. Links: <input class="<?php 
if(get_nf_option($row['NfOptions'], 'max_links')!==null) { 
    echo 'notempty '; 
} ?>posintnumber maxint_300" data_info="Max. Links" type="text" size="4" name="max_links" value="<?php echo get_nf_option($row['NfOptions'], 'max_links') . '"';
if(get_nf_option($row['NfOptions'], 'max_links')==null) { 
    echo ' disabled'; 
} ?> /></td><td><input type="checkbox" name="c_charset"<?php 
if(get_nf_option($row['NfOptions'], 'charset')!==null) { 
    echo ' checked="checked"'; 
} ?> /> Charset: <input <?php if(get_nf_option($row['NfOptions'], 'charset')!==null) { 
    echo 'class="notempty" '; 
} ?>type="text" data_info="Charset" size="20" name="charset" value="<?php echo get_nf_option($row['NfOptions'], 'charset') . '"';
if(get_nf_option($row['NfOptions'], 'charset')==null) { 
    echo ' disabled'; 
} ?> /> </td></tr>
<tr><td><input type="checkbox" name="c_max_texts"<?php if(get_nf_option($row['NfOptions'], 'max_texts')!==null) { 
    echo ' checked="checked"'; 
} ?> /> Max. Texts: <input class="<?php if(get_nf_option($row['NfOptions'], 'max_texts')!==null) { 
    echo 'notempty '; 
} ?>posintnumber maxint_30" data_info="Max. Texts" type="text" size="4" name="max_texts" value="<?php echo get_nf_option($row['NfOptions'], 'max_texts') . '"';if(get_nf_option($row['NfOptions'], 'max_texts')==null) { 
    echo ' disabled'; 
} ?> /></td><td><input type="checkbox" name="c_tag"<?php if(get_nf_option($row['NfOptions'], 'tag')!==null) { 
    echo ' checked="checked"'; 
} ?> /> Tag: <input <?php if(get_nf_option($row['NfOptions'], 'tag')!==null) { 
    echo 'class="notempty" '; 
} ?>type="text" data_info="Tag" size="20" name="tag" value="<?php echo get_nf_option($row['NfOptions'], 'tag') . '"';
if(get_nf_option($row['NfOptions'], 'tag')==null) { 
    echo ' disabled'; 
} ?> /> </td></tr>
<tr><td colspan="2"><input type="checkbox" name="c_article_source"<?php if(get_nf_option($row['NfOptions'], 'article_source')!==null) { 
    echo ' checked="checked"'; 
} ?> /> Article Source: <input class="<?php if(get_nf_option($row['NfOptions'], 'article_source')!==null) { 
    echo 'notempty ';
} ?>" data_info="Article Source" type="text" size="20" name="article_source" value="<?php echo get_nf_option($row['NfOptions'], 'article_source') . '"';
if(get_nf_option($row['NfOptions'], 'article_source')==null) { 
    echo ' disabled'; 
} ?> /></td></tr></table>
</td></tr>
</table><input type="submit" value="Update" />
<input type="hidden" name="NfID" value="<?php echo tohtml($row['NfID']); ?>" />
<input type="button" value="Cancel" onclick="location.href='edit_feeds.php';" />
<input type="hidden" name="NfOptions" value="" />
<input type="hidden" name="update_feed" value="1" />
</form>
<script type="text/javascript">
$('[name^="c_"]').change(function(){
    if(this.checked){
        $(this).parent().children('input[type="text"]').removeAttr('disabled').addClass("notempty");
        $(this).parent().find('select').removeAttr('disabled');
    }
    else{
        $(this).parent().children('input[type="text"]').attr('disabled','disabled').removeClass("notempty");
        $(this).parent().find('select').attr('disabled','disabled');
    }
});
$('[type="submit"]').on('click', function(){
    var str;
    str=$('[name="edit_text"]:checked').length > 0?"edit_text=1,":"";
    $('[name^="c_"]').each(function(){        
        str+=this.checked ? $(this).parent().children('input[type="text"]').attr('name') + '='
        + $(this).parent().children('input[type="text"]').val()
        + ($(this).attr('name')=='c_autoupdate' ? $(this).parent().find('select').val() + ',' : ','): '';
    });
    $('input[name="NfOptions"]').val(str);
});
</script>
    <?php	
}

elseif(isset($_REQUEST['multi_load_feed'])) {
    if(!empty($currentlang)) {
        $result = do_mysqli_query("SELECT NfName,NfID,NfUpdate FROM " . $tbpref . "newsfeeds WHERE NfLgID=$currentlang ORDER BY NfUpdate DESC");
    }
    else{
        $result = do_mysqli_query("SELECT NfName,NfID,NfUpdate FROM " . $tbpref . "newsfeeds ORDER BY NfUpdate DESC");
    }
    ?>
<form name="form1" action="do_feeds.php" onsubmit="document.form1.querybutton.click(); return false;">
<table class="tab3"  style="border-left: none;border-top: none; background-color:inherit" cellspacing="0" cellpadding="5">
<tr>
<th class="th1 borderleft" colspan="2">Language:<select name="filterlang" onchange="{setLang(document.form1.filterlang,'edit_feeds.php?multi_load_feed=1%26page=1');}">
    <?php
    echo get_languages_selectoptions($currentlang, '[Filter off]');
    ?>
</select></th>
<th class="th1 borderright" colspan="2">
<input type="button" value="Mark All" onclick="selectToggle(true,'form1');return false;" />
<input type="button" value="Mark None" onclick="selectToggle(false,'form1');return false;" /></th>
</tr><tr>
<td colspan="4" style="padding-left: 0px;padding-right: 0px;border-bottom: none;width: 100%;border-left: none;background-color: transparent;"><table class="sortable tab2" cellspacing="0" cellpadding="5">
<tr>
<th class="th1 sorttable_nosort">Mark</th>
<th class="th1 clickable" colspan="2">Newsfeeds</th>
<th class="th1 sorttable_numeric clickable">Last Update</th>
</tr>
    <?php
    $time=time();
    while($row = mysqli_fetch_assoc($result)){
        $diff= $time - (int) $row['NfUpdate'];
        echo '<tr><td class="td1 center"><input class="markcheck" type="checkbox" name="selected_feed[]" value="' . $row['NfID'] . '" checked="checked" /></td>';
        echo '<td class="td1 center" colspan="2">'.$row['NfName'].'</td><td class="td1 center" sorttable_customkey="'.$diff.'">';
        if($row['NfUpdate']) {
            print_last_feed_update($diff);
        }
        echo '</td></tr>';
    }
    mysqli_free_result($result);
    ?>
</table></td></tr>
<tr>
<th class="th1 borderleft" colspan="3"><input id="map" type="hidden" name="selected_feed" value="" />
<input type="hidden" name="load_feed" value="1" />
<button id="markaction">Update Marked Newsfeeds</button></th>
<th class="th1 borderright"><input type="button" value="Cancel" onclick="location.href='do_feeds.php?selected_feed=0'; return false;" /></th></tr>
</table></form>

<script type="text/javascript">
$( "button" ).on('click', function() {
    $("#map").val( $('input[type="checkbox"]:checked').map(function(){
        return $(this).val();
    }).get().join(", ") );
});

</script>
    <?php
}
else{
    ?>
<a href="do_feeds.php">My Feeds</a><span> &nbsp; | &nbsp;</span>
<a href="edit_feeds.php?new_feed=1"><img src="icn/feed--plus.png" title="new feed" alt="new feed" /> New Feed ...</a>
<form name="form1" action="#" onsubmit="document.form1.querybutton.click(); return false;">
<table class="tab1" cellspacing="0" cellpadding="5"><tr>
<th class="th1" colspan="4">Filter <img src="icn/funnel.png" title="Filter" alt="Filter" />&nbsp;
<input type="button" value="Reset All" onclick="resetAll('edit_feeds.php');" /></th>
</tr>
<tr><td class="td1 center" colspan="2" style="width:30%;">Language:&nbsp;<select name="filterlang" onchange="{setLang(document.form1.filterlang,'edit_feeds.php?manage_feeds=1');}">
    <?php
    echo get_languages_selectoptions($currentlang, '[Filter off]');
    ?>
</select></td><td class="td1 center" colspan="4">
Feed Name (Wildc.=*):
<input type="text" name="query" value="<?php echo tohtml($currentquery); ?>" maxlength="50" size="15" />&nbsp;
<input type="button" name="querybutton" value="Filter" onclick="{val=document.form1.query.value; location.href='edit_feeds.php?page=1&amp;query=' + val;}" />&nbsp;
<input type="button" value="Clear" onclick="{location.href='edit_feeds.php?page=1&amp;query=';}" />
</td></tr></table>

<input id="map" type="hidden" name="selected_feed" value="" />
<table class="tab1" cellspacing="0" cellpadding="5">
<tr><th class="th1" colspan="3">Multi Actions <img src="icn/lightning.png" title="Multi Actions" alt="Multi Actions" /></th></tr>
<tr><td class="td1 center" style="width:30%;">
<input type="button" value="Mark All" onclick="selectToggle(true,'form2');" />
<input type="button" value="Mark None" onclick="selectToggle(false,'form2');" />
</td><td class="td1 center" colspan="2">Marked Newsfeeds:&nbsp;
<select name="markaction" id="markaction" disabled="disabled" onchange="$('#map').val($('input:checked').map(function(){return $(this).val();}).get().join(', '));multiActionGo(document.form1, document.form1.markaction);return false;">
<option value="">[Choose...]</option>
<option disabled="disabled">------------</option>
<option value="update">Update</option>
<option disabled="disabled">------------</option>
<option value="res_art">Reset Unloadable Articles</option>
<option disabled="disabled">------------</option>
<option value="del_art">Delete All Articles</option>
<option disabled="disabled">------------</option>
<option value="del">Delete</option>
</select></td></tr>
    <?php
        $sql = 'select count(*) as value from ' . $tbpref . 'newsfeeds where '; if($currentlang>0) { $sql .= 'NfLgID ='.$currentlang . $wh_query; 
        }else { $sql .= '1=1' . $wh_query; 
        }
        $recno = (int) get_first_value($sql);
        if ($debug) { 
            echo $sql . ' ===&gt; ' . $recno; 
        }
        if($recno) {
            $maxperpage = (int) getSettingWithDefault('set-feeds-per-page');
            $pages = $recno == 0 ? 0 : (intval(($recno-1) / $maxperpage) + 1);
            if ($currentpage < 1) { $currentpage = 1; 
            }
            if ($currentpage > $pages) { $currentpage = $pages; 
            }
                    
            $sorts = array('NfName','NfUpdate DESC','NfUpdate ASC');
            $lsorts = count($sorts);
            if ($currentsort < 1) { $currentsort = 1; 
            }
            if ($currentsort > $lsorts) { $currentsort = $lsorts; 
            }
                echo '<tr><th class="th1" style="width:30%;"> '. $total=$recno .' newsfeeds ';///
            echo '</th><th class="th1">';
            makePager($currentpage, $pages, 'edit_feeds.php', 'form1');
            if(!empty($currentlang)) {
                $result = do_mysqli_query("SELECT * FROM " . $tbpref . "newsfeeds WHERE NfLgID=$currentlang $wh_query ORDER BY " . $sorts[$currentsort-1]);
            }
            else{
                $result = do_mysqli_query("SELECT * FROM " . $tbpref . "newsfeeds WHERE (1=1) $wh_query ORDER BY " . $sorts[$currentsort-1]);
            }
            ?>
        </th>
        <th class="th1" colspan="1" nowrap="nowrap">
        Sort Order:
        <select name="sort" onchange="{val=document.form1.sort.options[document.form1.sort.selectedIndex].value; location.href='edit_feeds.php?page=1&amp;sort=' + val;}"><?php echo get_textssort_selectoptions($currentsort); ?></select>
</th></table></form><form name="form2" action="" method="get">
<table class="sortable tab1" cellspacing="0" cellpadding="5">
<tr>
<th class="th1 sorttable_nosort">Mark</th>
<th class="th1 sorttable_nosort">Actions</th>
<th class="th1 clickable">Newsfeeds</th>
<th class="th1 sorttable_nosort">Options</th>
<th class="th1 sorttable_numeric clickable">Last Update</th>
</tr>
            <?php
            $time=time();    
            while($row = mysqli_fetch_assoc($result)) {
                $diff = $time- (int) $row['NfUpdate'];
                echo '<tr><td class="td1 center"><input type="checkbox" name="marked[]" class="markcheck" value="' . $row['NfID'] . '" /></td>';
                echo '<td style="white-space: nowrap" class="td1 center"><a href="' . $_SERVER['PHP_SELF'] . '?edit_feed=1&amp;selected_feed=' . $row['NfID'] . '"><img src="icn/feed--pencil.png" title="Edit" alt="Edit" /></a>';
                echo '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?manage_feeds=1&amp;load_feed=1&amp;selected_feed=' . $row['NfID'] . '"><span title="Update Feed"><img src="icn/arrow-circle-135.png" alt="-" /></span></a>';
                echo '&nbsp; <a href="' . $row['NfSourceURI'] . '" onclick="window.open(this.href); return false"><img src="icn/external.png" title="Show Feed" alt="Link" /></a>';
                echo '&nbsp; <span class="click" onclick="if (confirm (\'Are you sure?\')) location.href=\'' . $_SERVER['PHP_SELF'] . '?markaction=del&amp;selected_feed=' . $row['NfID'] . '\';"><img src="icn/minus-button.png" title="Delete" alt="Delete" /></span></td>';
                echo '<td class="td1 center">'.$row['NfName'].'</td>';
                echo '<td class="td1 center">'.str_replace(',', ', ', $row['NfOptions']).'</td><td class="td1 center" sorttable_customkey="'.$diff.'">';
                if($row['NfUpdate']) {
                    print_last_feed_update($diff);
                }
                echo '</td></tr>';
            }
            mysqli_free_result($result);
            ?>
</table>
</form>
            <?php
            if($pages > 1) {
                echo '<form name="form3" method="get" action =""><table class="tab1" cellspacing="0" cellpadding="5"><tr><th class="th1" style="width:30%;">';
                echo $total ;
                echo '</th><th class="th1">';
                makePager($currentpage, $pages, 'do_feeds.php', 'form3');
                echo '</th></tr></table></form>';
            }

        }
}
pageend();

?>
