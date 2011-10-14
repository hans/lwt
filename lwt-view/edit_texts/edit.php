<h4>
    Edit Text
    <a target="_blank" href="info.htm#howtotext"><img src="icn/question-frame.png" title="Help" alt="Help" /></a>
</h4>

<form class="validate" action="<?php echo $_SERVER['PHP_SELF']; ?>#rec<?php echo $_REQUEST['chg']; ?>" method="post">
    <input type="hidden" name="TxID" value="<?php echo $_REQUEST['chg']; ?>" />

    <table class="tab3" cellspacing="0" cellpadding="5">
        <tr>
            <td class="td1 right">Language:</td>
            <td class="td1">
                <select name="TxLgID" class="notempty setfocus">
                    <?php echo get_languages_selectoptions($record['TxLgID'],"[Choose...]"); ?>
                </select>

                <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
            </td>
        </tr>
        <tr>
            <td class="td1 right">Title:</td>
            <td class="td1">
                <input type="text" class="notempty" name="TxTitle" value="<?php echo tohtml($record['TxTitle']); ?>" maxlength="200" size="60" />
                <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
            </td>
        </tr>
        <tr>
            <td class="td1 right">Text:</td>
            <td class="td1">
                <textarea <?php echo getScriptDirectionTag($record['TxLgID']); ?> name="TxText" class="notempty checkbytes" data_maxlength="65000" data_info="Text" cols="60" rows="20">
                    <?php echo tohtml($record['TxText']); ?>
                </textarea>

                <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
            </td>
        </tr>
        <tr>
            <td class="td1 right">Tags:</td>
            <td class="td1">
                <?php echo getTextTags($_REQUEST['chg']); ?>
            </td>
        </tr>
        <tr>
            <td class="td1 right">Audio-URI:</td>
            <td class="td1">
                <input type="text" name="TxAudioURI" value="<?php echo tohtml($record['TxAudioURI']); ?>" maxlength="200" size="60" />
                <span id="mediaselect"><?php echo selectmediapath('TxAudioURI'); ?></span>
            </td>
        </tr>
        <tr>
            <td class="td1 right" colspan="2">
                <input type="button" value="Cancel" onclick="location.href='edit_texts.php#rec<?php echo $_REQUEST['chg']; ?>';" />
                <input type="submit" name="op" value="Check" />
                <input type="submit" name="op" value="Change" />
                <input type="submit" name="op" value="Change and Open" />
            </td>
        </tr>
    </table>
</form>
