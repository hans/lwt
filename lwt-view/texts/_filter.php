<form name="form1" action="#" onsubmit="document.form1.querybutton.click(); return false;">
    <table class="tab1" cellspacing="0" cellpadding="5">
        <tr>
            <th class="th1" colspan="4">
                Filter <img src="icn/funnel.png" title="Filter" alt="Filter" />&nbsp;
                <input type="button" value="Reset All" onclick="resetAll('<?php echo $_SERVER['PHP_SELF']; ?>');" />
            </th>
        </tr>
        <tr>
            <td class="td1 center" colspan="2">
                Language:
                <select name="filterlang" onchange="{setLang(document.form1.filterlang, '<?php echo $_SERVER['PHP_SELF']; ?>');}">
                    <?php echo get_languages_selectoptions($currentlang,'[Filter off]'); ?>
                </select>
            </td>
            <td class="td1 center" colspan="2">
                Text Title (Wildc.=*):
                <input type="text" name="query" value="<?php echo tohtml($currentquery); ?>" maxlength="50" size="15" />&nbsp;
                <input type="button" name="querybutton" value="Filter" onclick="{val=document.form1.query.value; location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?page=1&amp;query=' + val;}" />&nbsp;
                <input type="button" value="Clear" onclick="{location.href='<?php echo $_SERVER['PHP_SELF']; ?>?page=1&amp;query=';}" />
            </td>
        </tr>
        <tr>
            <td class="td1 center" colspan="2" nowrap="nowrap">
                Tag #1:
                <select name="tag1" onchange="{val=document.form1.tag1.options[document.form1.tag1.selectedIndex].value; location.href='<?php echo $_SERVER['PHP_SELF']; ?>?page=1&amp;tag1=' + val;}">
                    <?php echo get_texttag_selectoptions($currenttag1,$currentlang); ?>
                </select>
            </td>
            <td class="td1 center" nowrap="nowrap">
                Tag #1 ..
                <select name="tag12" onchange="{val=document.form1.tag12.options[document.form1.tag12.selectedIndex].value; location.href='<?php echo $_SERVER['PHP_SELF']; ?>?page=1&amp;tag12=' + val;}">
                    <?php echo get_andor_selectoptions($currenttag12); ?>
                </select>
                .. Tag #2
            </td>
            <td class="td1 center" nowrap="nowrap">
                Tag #2:
                <select name="tag2" onchange="{val=document.form1.tag2.options[document.form1.tag2.selectedIndex].value; location.href='<?php echo $_SERVER['PHP_SELF']; ?>?page=1&amp;tag2=' + val;}">
                    <?php echo get_texttag_selectoptions($currenttag2,$currentlang); ?>
                </select>
            </td>
        </tr>

        <?php if ( $recno > 0 ): ?>
            <tr>
                <th class="th1" colspan="1" nowrap="nowrap">
                    <?php echo $recno; ?> Text<?php echo ($recno==1?'':'s'); ?>
                </th>
                <th class="th1" colspan="2" nowrap="nowrap">
                    <?php makePager ($currentpage, $pages, $_SERVER['PHP_SELF'], 'form1'); ?>
                </th>
                <th class="th1" colspan="1" nowrap="nowrap">
                    Sort Order:
                    <select name="sort" onchange="{val=document.form1.sort.options[document.form1.sort.selectedIndex].value; location.href='<?php echo $_SERVER['PHP_SELF']; ?>?page=1&amp;sort=' + val;}">
                        <?php echo get_textssort_selectoptions($currentsort); ?>
                    </select>
                </th>
            </tr>
        <?php endif; ?>
    </table>
</form>