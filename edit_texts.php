<?php


/**
 * \file
 * \brief Manage active texts
 * 
 * Call: edit_texts.php?....
 *     ... markaction=[opcode] ... do actions on marked texts
 *     ... del=[textid] ... do delete
 *     ... arch=[textid] ... do archive
 *     ... op=Check ... do check
 *     ... op=Save ... do insert new
 *     ... op=Change ... do update
 *     ... op=Save+and+Open ... do insert new and open
 *     ... op=Change+and+Open ... do update and open
 *     ... new=1 ... display new text screen
 *     ... chg=[textid] ... display edit screen
 *     ... filterlang=[langid] ... language filter
 *     ... sort=[sortcode] ... sort
 *     ... page=[pageno] ... page
 *     ... query=[titlefilter] ... title filter
 * 
 * @package Lwt
 * @author  LWT Project <lwt-project@hotmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/edit__texts_8php.html
 * @since   1.0.3
 */

require_once 'inc/session_utility.php';

/**
 * Get the value of $wh_query.
 * 
 * @param string $currentquery     Current database query
 * @param string $currentquerymode
 * @param string $currentregexmode
 * 
 * @return string Content for $wh_query.
 */
function edit_texts_get_wh_query($currentquery, $currentquerymode, $currentregexmode)
{
    $wh_query = $currentregexmode . 'LIKE ';
    if ($currentregexmode == '') {
        $wh_query .= convert_string_to_sqlsyntax(
            str_replace("*", "%", mb_strtolower($currentquery, 'UTF-8'))
        );
    } else {
        $wh_query .= convert_string_to_sqlsyntax($currentquery);
    }

    switch($currentquerymode){
    case 'title,text':
        $wh_query = ' and (TxTitle ' . $wh_query . ' or TxText ' . $wh_query . ')';
        break;
    case 'title':
        $wh_query = ' and (TxTitle ' . $wh_query . ')';
        break;
    case 'text':
        $wh_query = ' and (TxText ' . $wh_query . ')';
        break;
    }
    if ($currentquery!=='') {
        if (
            $currentregexmode !== '' && 
            @mysqli_query(
            $GLOBALS["DBCONNECTION"], 
            'SELECT "test" RLIKE ' . convert_string_to_sqlsyntax($currentquery)
            ) === false
        ) {
            $currentquery = '';
            $wh_query = '';
            unset($_SESSION['currentwordquery']);
            if (isset($_REQUEST['query'])) { 
                echo '<p id="hide3" style="color:red;text-align:center;">+++ Warning: Invalid Search +++</p>'; 
            }
        }
    } else { 
        $wh_query = ''; 
    }
    return $wh_query;
}

/**
 * Return the value for $wh_tag.
 * 
 * @param string|int $currentlang Current language ID
 * 
 * @return string Content for $wh_tag.
 */
function edit_texts_get_wh_tag($currentlang)
{
    $wh_tag1 = null;
    $wh_tag2 = null;
    $currenttag1 = validateTextTag(processSessParam("tag1", "currenttexttag1", '', 0), $currentlang);
    $currenttag2 = validateTextTag(processSessParam("tag2", "currenttexttag2", '', 0), $currentlang);
    $currenttag12 = processSessParam("tag12", "currenttexttag12", '', 0);
    if ($currenttag1 == '' && $currenttag2 == '') {
        return '';
    }
    if ($currenttag1 != '') {
        if ($currenttag1 == -1) {
            $wh_tag1 = "group_concat(TtT2ID) IS NULL"; 
        }
        else {
            $wh_tag1 = "concat('/',group_concat(TtT2ID separator '/'),'/') like '%/" . $currenttag1 . "/%'"; 
        }
    }
    if ($currenttag2 != '') {
        if ($currenttag2 == -1) {
            $wh_tag2 = "group_concat(TtT2ID) IS NULL"; 
        }
        else {
            $wh_tag2 = "concat('/',group_concat(TtT2ID separator '/'),'/') like '%/" . $currenttag2 . "/%'"; 
        }
    }
    if ($currenttag1 != '' && $currenttag2 == '') {
        return " having (" . $wh_tag1 . ') '; 
    }
    if ($currenttag2 != '' && $currenttag1 == '') {
        return " having (" . $wh_tag2 . ') ';
    } 
    return " having ((" . $wh_tag1 . ($currenttag12 ? ') AND (' : ') OR (') . $wh_tag2 . ')) ';
}

/**
 * When a mark action is in use, do the action.
 * 
 * @param string $maraction  Type of action
 * @param array  $marked     Texts marked.
 * @param string $actiondata Values to insert to the database
 * 
 * @return string[2] Massage and number of rows edited.
 * 
 * @global string $tbpref Database table prefix
 */
function edit_texts_mark_action($markaction, $marked, $actiondata)
{
    global $tbpref;
    $message = "Multiple Actions: 0";
    if (!isset($marked) || !is_array($marked)) {
        return array($message, null);
    }
    $l = count($marked);
    if ($l == 0) {
        return array($message, null);
    }
    $list = "(" . $marked[0];
    for ($i=1; $i<$l; $i++) { 
        $list .= "," . $marked[$i]; 
    }
    $list .= ")";

    if ($markaction == 'del') {
        $message3 = runsql(
            'delete from ' . $tbpref . 'textitems2 where Ti2TxID in ' . $list, 
            "Text items deleted"
        );
        $message2 = runsql(
            'delete from ' . $tbpref . 'sentences where SeTxID in ' . $list, 
            "Sentences deleted"
        );
        $message1 = runsql(
            'delete from ' . $tbpref . 'texts where TxID in ' . $list, 
            "Texts deleted"
        );
        $message = $message1 . " / " . $message2 . " / " . $message3;
        adjust_autoincr('texts', 'TxID');
        adjust_autoincr('sentences', 'SeID');
        runsql(
            "DELETE " . $tbpref . "texttags 
            FROM (
                " . $tbpref . "texttags 
                LEFT JOIN " . $tbpref . "texts 
                ON TtTxID = TxID
            ) 
            WHERE TxID IS NULL", 
            ''
        );
    } elseif ($markaction == 'arch') {
        runsql(
            'delete from ' . $tbpref . 'textitems2 where Ti2TxID in ' . $list, 
            ""
        );
        runsql(
            'delete from ' . $tbpref . 'sentences where SeTxID in ' . $list, 
            ""
        );
        $count = 0;
        $sql = "select TxID from " . $tbpref . "texts where TxID in " . $list;
        $res = do_mysqli_query($sql);
        while ($record = mysqli_fetch_assoc($res)) {
            $id = $record['TxID'];
            $count += (int)runsql(
                'insert into ' . $tbpref . 'archivedtexts (
                    AtLgID, AtTitle, AtText, AtAnnotatedText, AtAudioURI, AtSourceURI
                ) 
                select TxLgID, TxTitle, TxText, TxAnnotatedText, TxAudioURI, TxSourceURI 
                from ' . $tbpref . 'texts where TxID = ' . $id, 
                ""
            );
            $aid = get_last_key();
            runsql(
                'insert into ' . $tbpref . 'archtexttags (AgAtID, AgT2ID) 
                select ' . $aid . ', TtT2ID 
                from ' . $tbpref . 'texttags 
                where TtTxID = ' . $id, 
                ""
            );
        }
        mysqli_free_result($res);
        $message = 'Text(s) archived: ' . $count;
        runsql('delete from ' . $tbpref . 'texts where TxID in ' . $list, "");
        runsql("DELETE " . $tbpref . "texttags 
        FROM (
            " . $tbpref . "texttags 
            LEFT JOIN " . $tbpref . "texts 
            on TtTxID = TxID
        ) 
        WHERE TxID IS NULL", '');
        adjust_autoincr('texts', 'TxID');
        adjust_autoincr('sentences', 'SeID');
    } elseif ($markaction == 'addtag' ) {
        $message = addtexttaglist($actiondata, $list);
    } elseif ($markaction == 'deltag' ) {
        removetexttaglist($actiondata, $list);
        header("Location: edit_texts.php");
        exit();
    } elseif ($markaction == 'setsent') {
        $count = 0;
        $sql = "select WoID, WoTextLC, min(Ti2SeID) as SeID 
        from " . $tbpref . "words, " . $tbpref . "textitems2 
        where Ti2LgID = WoLgID and Ti2WoID = WoID and Ti2TxID in " . $list . " and 
        ifnull(WoSentence,'') not like concat('%{',WoText,'}%') 
        group by WoID order by WoID, min(Ti2SeID)";

        $res = do_mysqli_query($sql);
        while ($record = mysqli_fetch_assoc($res)) {
            $sent = getSentence(
                $record['SeID'], 
                $record['WoTextLC'], 
                (int) getSettingWithDefault('set-term-sentence-count')
            );
            $count += (int) runsql(
                'UPDATE ' . $tbpref . 'words 
                SET WoSentence = ' . convert_string_to_sqlsyntax(repl_tab_nl($sent[1])) . ' 
                WHERE WoID = ' . $record['WoID'], 
                ''
            );
        }
        mysqli_free_result($res);
        $message = 'Term Sentences set from Text(s): ' . $count;
    } elseif ($markaction == 'setactsent') {
        $count = 0;
        $sql = "SELECT WoID, WoTextLC, MIN(Ti2SeID) AS SeID 
        FROM " . $tbpref . "words, " . $tbpref . "textitems2 
        WHERE Ti2LgID = WoLgID AND WoStatus != 98 AND WoStatus != 99 AND 
        Ti2WoID = WoID AND Ti2TxID IN " . $list . " AND 
        IFNULL(WoSentence,'') NOT LIKE CONCAT('%{',WoText,'}%') 
        GROUP BY WoID 
        ORDER BY WoID, MIN(Ti2SeID)";

        $res = do_mysqli_query($sql);
        while ($record = mysqli_fetch_assoc($res)) {
            $sent = getSentence(
                $record['SeID'], 
                $record['WoTextLC'], 
                (int) getSettingWithDefault('set-term-sentence-count')
            );
            $count += (int) runsql(
                'update ' . $tbpref . 'words 
                set WoSentence = ' . convert_string_to_sqlsyntax(repl_tab_nl($sent[1])) . ' 
                where WoID = ' . $record['WoID'], 
                ''
            );
        }
        mysqli_free_result($res);
        $message = 'Term Sentences set from Text(s): ' . $count;
    } elseif ($markaction == 'rebuild') {
        $count = 0;
        $sql = "select TxID, TxLgID from " . $tbpref . "texts where TxID in " . $list;
        $res = do_mysqli_query($sql);
        while ($record = mysqli_fetch_assoc($res)) {
            $id = (int)$record['TxID'];
            runsql(
                'delete from ' . $tbpref . 'sentences where SeTxID = ' . $id, 
                "Sentences deleted"
            );
            runsql(
                'delete from ' . $tbpref . 'textitems2 where Ti2TxID = ' . $id, 
                "Text items deleted"
            );
            adjust_autoincr('sentences', 'SeID');
            splitCheckText(
                get_first_value(
                    'select TxText as value from ' . $tbpref . 'texts where TxID = ' . $id
                ),
                $record['TxLgID'], $id 
            );
            $count++;
        }
        mysqli_free_result($res);
        $message = 'Text(s) reparsed: ' . $count;
    } elseif ($markaction == 'test' ) {
        $_SESSION['testsql'] = ' ' . $tbpref . 'words, ' . $tbpref . 'textitems2 
        WHERE Ti2LgID = WoLgID AND Ti2WoID = WoID AND Ti2TxID IN ' . $list . ' ';
        header("Location: do_test.php?selection=1");
        exit();
    }
    return array($message, $message1);
}


/**
 * Delete an existing text.
 * 
 * @param string $txid Text ID
 * 
 * @return string Texts, sentences, and text items deleted. 
 * 
 * @global string $tbpref Database table prefix
 */
function edit_texts_delete($txid)
{
    global $tbpref;
    $message3 = runsql(
        'DELETE FROM ' . $tbpref . 'textitems2 where Ti2TxID = ' . $txid,
        "Text items deleted"
    );
    $message2 = runsql(
        'DELETE FROM ' . $tbpref . 'sentences where SeTxID = ' . $txid,
        "Sentences deleted"
    );
    $message1 = runsql(
        'DELETE FROM ' . $tbpref . 'texts where TxID = ' . $txid,
        "Texts deleted"
    );
    $message = $message1 . " / " . $message2 . " / " . $message3;
    adjust_autoincr('texts', 'TxID');
    adjust_autoincr('sentences', 'SeID');
    runsql(
        "DELETE {$tbpref}texttags 
        FROM (
            {$tbpref}texttags 
            LEFT JOIN {$tbpref}texts 
            ON TtTxID = TxID
        ) 
        WHERE TxID IS NULL", 
        ''
    );
    return $message;

}

/**
 * Archive a text.
 * 
 * @param int $txid text ID
 * 
 * @return string Number of archives saved, texts deleted, sentences deleted, 
 *                text items deleted.
 * 
 * @global string $tbpref Database table prefix
 */
function edit_texts_archive($txid)
{
    global $tbpref;
    $message3 = runsql(
        "DELETE FROM {$tbpref}textitems2 WHERE Ti2TxID = $txid",
        "Text items deleted"
    );
    $message2 = runsql(
        "DELETE FROM {$tbpref}sentences WHERE SeTxID = $txid",
        "Sentences deleted"
    );
    $message4 = runsql(
        "INSERT INTO {$tbpref}archivedtexts (
            AtLgID, AtTitle, AtText, AtAnnotatedText, AtAudioURI, AtSourceURI
        ) SELECT TxLgID, TxTitle, TxText, TxAnnotatedText, TxAudioURI, TxSourceURI 
        FROM {$tbpref}texts 
        WHERE TxID = $txid", 
        "Archived Texts saved"
    );
    $id = get_last_key();
    runsql(
        "INSERT INTO {$tbpref}archtexttags (AgAtID, AgT2ID) 
        SELECT $id, TtT2ID 
        FROM {$tbpref}texttags 
        WHERE TtTxID = $txid", 
        ""
    );
    $message1 = runsql(
        "DELETE FROM {$tbpref}texts WHERE TxID = $txid", 
        "Texts deleted"
    );
    $message = $message4 . " / " . $message1 . " / " . $message2 . " / " . $message3;
    adjust_autoincr('texts', 'TxID');
    adjust_autoincr('sentences', 'SeID');
    runsql(
        "DELETE {$tbpref}texttags 
        FROM (
            {$tbpref}texttags 
            LEFT JOIN {$tbpref}texts 
            ON TtTxID = TxID
        ) 
        WHERE TxID IS NULL", 
        ''
    );
    return $message;
}

/**
 * Do an operation on texts.
 * 
 * @param string $op           Operation name
 * @param string $message1     Number of texts edited
 * @param int    $no_pagestart If you don't want a page 
 * 
 * @return string Edition message (number of rows edited)
 * 
 * @global string $tbpref Database table prefix
 */
function edit_texts_do_operation($op, $message1, $no_pagestart)
{
    global $tbpref;
    if (strlen(prepare_textdata($_REQUEST['TxText'])) > 65000) {
        $message = "Error: Text too long, must be below 65000 Bytes";
        $currentlang = (int) validateLang(
            processDBParam("filterlang", 'currentlanguage', '', 0)
        );
        if ($no_pagestart) { 
            pagestart('My ' . getLanguage($currentlang) . ' Texts', true); 
        }
        return $message;
    }


    // CHECK

    if ($op == 'Check') {
        echo '<p>
            <input type="button" value="&lt;&lt; Back" onclick="history.back();" />
        </p>';
        echo splitCheckText(
            remove_soft_hyphens($_REQUEST['TxText']), $_REQUEST['TxLgID'], -1
        );
        echo '<p>
            <input type="button" value="&lt;&lt; Back" onclick="history.back();" />
        </p>';
        pageend();
        exit();
    }

    // INSERT

    elseif (substr($op, 0, 4) == 'Save') {
        runsql(
            'insert into ' . $tbpref . 'texts (
                TxLgID, TxTitle, TxText, TxAnnotatedText, 
                TxAudioURI, TxSourceURI
            ) values( ' . 
            $_REQUEST["TxLgID"] . ', ' . 
            convert_string_to_sqlsyntax($_REQUEST["TxTitle"]) . ', ' . 
            convert_string_to_sqlsyntax(remove_soft_hyphens($_REQUEST["TxText"])) . ", '', " .
            convert_string_to_sqlsyntax($_REQUEST["TxAudioURI"]) . ', ' .
            convert_string_to_sqlsyntax($_REQUEST["TxSourceURI"]) . ')', "Saved"
        );
        $id = get_last_key();
        saveTextTags($id);
    } elseif (substr($op, 0, 6) == 'Change') {
        // UPDATE
        /*
        $oldtext = get_first_value(
            'SELECT TxText AS value 
            FROM ' . $tbpref . 'texts 
            WHERE TxID = ' . $_REQUEST["TxID"]
        );
        (convert_string_to_sqlsyntax(remove_soft_hyphens($_REQUEST["TxText"])) 
        != convert_string_to_sqlsyntax($oldtext));
        */
        runsql(
            'update ' . $tbpref . 'texts set ' .
            'TxLgID = ' . $_REQUEST["TxLgID"] . ', ' .
            'TxTitle = ' . convert_string_to_sqlsyntax($_REQUEST["TxTitle"]) . ', ' .
            'TxText = ' . convert_string_to_sqlsyntax(remove_soft_hyphens($_REQUEST["TxText"])) . ', ' .
            'TxAudioURI = ' . convert_string_to_sqlsyntax($_REQUEST["TxAudioURI"]) . ', ' .
            'TxSourceURI = ' . convert_string_to_sqlsyntax($_REQUEST["TxSourceURI"]) . ' ' .
            'where TxID = ' . $_REQUEST["TxID"], "Updated"
        );
        $id = (int) $_REQUEST["TxID"];
        saveTextTags($id);
    }

    $message2 = runsql(
        'delete from ' . $tbpref . 'sentences where SeTxID = ' . $id,
        "Sentences deleted"
    );
    $message3 = runsql(
        'delete from ' . $tbpref . 'textitems2 where Ti2TxID = ' . $id,
        "Textitems deleted"
    );
    adjust_autoincr('sentences', 'SeID');

    splitCheckText(
        get_first_value(
            'SELECT TxText AS value FROM ' . $tbpref . 'texts 
            WHERE TxID = ' . $id
        ),
        $_REQUEST["TxLgID"], $id 
    );

    $message = $message1 . " / " . $message2 . " / " . $message3 . 
    " / Sentences added: " . get_first_value(
        "SELECT COUNT(*) AS value 
        FROM {$tbpref}sentences 
        WHERE SeTxID = $id"
    ) . 
    " / Text items added: " . get_first_value(
        "SELECT COUNT(*) AS value 
        FROM {$tbpref}textitems2 
        WHERE Ti2TxID = $id"
    );

    if (substr($op, -8) == "and Open") {
        header('Location: do_text.php?start=' . $id);
        exit();
    }
    return $message;
}

/**
 * Create a window to make a new text in the target language.
 * 
 * @param int $lid Language ID
 * 
 * @return void
 */
function edit_texts_new($lid)
{
    ?>

<h4>
    New Text <a target="_blank" href="docs/info.html#howtotext">
    <img src="icn/question-frame.png" title="Help" alt="Help" /></a> 
</h4>
<script type="text/javascript" src="js/unloadformcheck.js" charset="utf-8"></script>
<form class="validate" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <table class="tab3" cellspacing="0" cellpadding="5">
        <tr>
            <td class="td1 right">Language:</td>
            <td class="td1">
                <select name="TxLgID" class="notempty setfocus">
                    <?php
                    echo get_languages_selectoptions($lid, '[Choose...]');
                    ?>
                </select>
                <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
            </td>
        </tr>
        <tr>
            <td class="td1 right">Title:</td>
            <td class="td1">
                <input type="text" class="notempty checkoutsidebmp" data_info="Title" name="TxTitle" value="" maxlength="200" size="60" />
                <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
            </td>
        </tr>
        <tr>
            <td class="td1 right">Text:<br /><br />(max.<br />65,000<br />bytes)</td>
            <td class="td1">
                <textarea name="TxText" class="notempty checkbytes checkoutsidebmp" data_maxlength="65000" data_info="Text" cols="60" rows="20"></textarea>
                <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
            </td>
        </tr>
        <tr>
            <td class="td1 right">Source URI:</td>
            <td class="td1">
                <input type="text" class="checkurl checkoutsidebmp" data_info="Source URI" name="TxSourceURI" value="" maxlength="1000" size="60" />
            </td>
        </tr>
        <tr>
            <td class="td1 right">Tags:</td>
            <td class="td1">
            <?php echo getTextTags(0); ?>
            </td>
        </tr>
        <tr>
            <td class="td1 right" title="A soundtrack or a video to be display while reading">
                Media URI:
            </td>
            <td class="td1">
                <input type="text" class="checkoutsidebmp" data_info="Audio-URI" name="TxAudioURI" value="" maxlength="200" size="60" />        
                <span id="mediaselect">
                    <?php echo selectmediapath('TxAudioURI'); ?>
                </span>        
            </td>
        </tr>
        <?php if (isset($YT_API_KEY)) {
            include_once 'text_from_yt.php';
        } ?>
        <tr>
            <td class="td1 right" colspan="2">
                <input type="button" value="Cancel" onclick="{resetDirty(); location.href='edit_texts.php';}" />
                <input type="submit" name="op" value="Check" />
                <input type="submit" name="op" value="Save" />
                <input type="submit" name="op" value="Save and Open" />
            </td>
        </tr>
    </table>
</form>

<p class="smallgray">
    Import of a <b>long text</b>, without audio, with splitting it up into smaller texts:
</p>
<p>
    <input type="button" value="Long Text Import" onclick="location.href='long_text_import.php';" /> 
</p>
    <?php
}

/**
 * Create the main window to edit an existing text.
 * 
 * @param int $txid Text ID
 * 
 * @return void
 * 
 * @global string $tbpref Database table prefix
 */
function edit_texts_change($txid)
{
    global $tbpref;
    $sql = "SELECT TxLgID, TxTitle, TxText, TxAudioURI, TxSourceURI, LENGTH(TxAnnotatedText) AS annotlen 
    FROM {$tbpref}texts 
    WHERE TxID = {$txid}";
    $res = do_mysqli_query($sql);
    if ($record = mysqli_fetch_assoc($res)) {

        ?>

<h4>
    Edit Text <a target="_blank" href="docs/info.html#howtotext">
    <img src="icn/question-frame.png" title="Help" alt="Help" />
    </a>
</h4>
<script type="text/javascript" src="js/unloadformcheck.js" charset="utf-8"></script>
<form class="validate" action="<?php echo $_SERVER['PHP_SELF']; ?>#rec<?php echo $txid; ?>" method="post">
    <input type="hidden" name="TxID" value="<?php echo $txid; ?>" />
    <table class="tab3" cellspacing="0" cellpadding="5">
        <tr>
            <td class="td1 right">Language:</td>
            <td class="td1">
                <select name="TxLgID" class="notempty setfocus">
                <?php
                echo get_languages_selectoptions($record['TxLgID'], "[Choose...]");
                ?>
                </select> 
                <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
            </td>
        </tr>
        <tr>
            <td class="td1 right">Title:</td>
            <td class="td1">
                <input type="text" class="notempty checkoutsidebmp" data_info="Title" name="TxTitle" value="<?php echo tohtml($record['TxTitle']); ?>" maxlength="200" size="60" />
                <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
        </tr>
        <tr>
            <td class="td1 right">Text:<br /><br />(max.<br />65,000<br />bytes)</td>
            <td class="td1">
            <textarea <?php echo getScriptDirectionTag($record['TxLgID']); ?> name="TxText" class="notempty checkbytes checkoutsidebmp" data_maxlength="65000" data_info="Text" cols="60" rows="20">
                <?php echo tohtml($record['TxText']); ?>
            </textarea> 
            <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" />
            </td>
        </tr>
        <tr>
            <td class="td1 right">Ann.Text:</td>
            <td class="td1">
                <?php echo ($record['annotlen'] ? '<img src="icn/tick.png" title="With Improved Annotation" alt="With Improved Annotation" /> Exists - May be partially or fully lost if you change the text!<br /><input type="button" value="Print/Edit..." onclick="location.href=\'print_impr_text.php?text=' . $txid . '\';" />' : '<img src="icn/cross.png" title="No Improved Annotation" alt="No Improved Annotation" /> - None | <input type="button" value="Create/Print..." onclick="location.href=\'print_impr_text.php?edit=1&amp;text=' . $txid . '\';" />'); ?>
            </td>
        </tr>
        <tr>
            <td class="td1 right">Source URI:</td>
            <td class="td1"><input type="text" class="checkurl checkoutsidebmp" data_info="Source URI" name="TxSourceURI" value="<?php echo tohtml($record['TxSourceURI']); ?>" maxlength="1000" size="60" /></td>
        </tr>
        <tr>
            <td class="td1 right">Tags:</td>
            <td class="td1">
                <?php echo getTextTags($txid); ?>
            </td>
        </tr>
        <tr>
            <td class="td1 right" title="A soundtrack or a video to be display while reading">
                Media URI:
            </td>
            <td class="td1">
                <input type="text" class="checkoutsidebmp" data_info="Audio-URI" name="TxAudioURI" value="<?php echo tohtml($record['TxAudioURI']); ?>" maxlength="200" size="60" /> 
                <span id="mediaselect">
                    <?php echo selectmediapath('TxAudioURI'); ?>
                </span>        
            </td>
        </tr>
        <tr>
            <td class="td1 right" colspan="2">
                <input type="button" value="Cancel" onclick="{resetDirty(); location.href='edit_texts.php#rec<?php echo $txid; ?>';}" />
                <input type="submit" name="op" value="Check" />
                <input type="submit" name="op" value="Change" />
                <input type="submit" name="op" value="Change and Open" />
            </td>
        </tr>
    </table>
</form>

        <?php

    }
    mysqli_free_result($res);
}

/**
 * Do the filters form for texts display.
 * 
 * @param string $currentlang Current language ID
 * @param int    $recno 
 * @param int    $currentpage Current page number
 * @param int    $pages       Total number of pages
 * 
 * @return void
 */
function edit_texts_filters_form($currentlang, $recno, $currentpage, $pages)
{
    $currentquery = processSessParam("query", "currenttextquery", '', 0);
    $currentquerymode = processSessParam(
        "query_mode", "currenttextquerymode", 'title,text', 0
    );
    $currentregexmode = getSettingWithDefault("set-regex-mode");
    $currenttag1 = validateTextTag(
        processSessParam("tag1", "currenttexttag1", '', 0), 
        $currentlang
    );
    $currenttag2 = validateTextTag(
        processSessParam("tag2", "currenttexttag2", '', 0), 
        $currentlang
    );
    $currentsort = processDBParam("sort", 'currenttextsort', '1', 1);
    $currenttag12 = processSessParam("tag12", "currenttexttag12", '', 0);
    ?>
<form name="form1" action="#" onsubmit="document.form1.querybutton.click(); return false;">
    <table class="tab1" cellspacing="0" cellpadding="5">
        <tr>
            <th class="th1" colspan="4">Filter <img src="icn/funnel.png" title="Filter" alt="Filter" />&nbsp;
            <input type="button" value="Reset All" onclick="resetAll('edit_texts.php');" /></th>
        </tr>
        <tr>
            <td class="td1 center" colspan="2">
                Language:
                <select name="filterlang" onchange="{setLang(document.form1.filterlang,'edit_texts.php');}"><?php echo get_languages_selectoptions($currentlang, '[Filter off]'); ?></select>
            </td>
            <td class="td1 center" colspan="2">
                <select name="query_mode" onchange="{val=document.form1.query.value;mode=document.form1.query_mode.value; location.href='edit_texts.php?page=1&amp;query=' + val + '&amp;query_mode=' + mode;}">
                    <option value="title,text"<?php 
                    if($currentquerymode=="title,text") { 
                        echo ' selected="selected"'; 
                    } ?>>Title &amp; Text</option>
                    <option disabled="disabled">------------</option>
                    <option value="title"<?php 
                    if($currentquerymode=="title") { 
                        echo ' selected="selected"'; 
                    } ?>>Title</option>
                    <option value="text"<?php 
                    echo ($currentquerymode=="text" ? ' selected="selected"' : '');
                    ?>>Text</option>
                </select>
                <?php
                if($currentregexmode=='') { 
                    echo '<span style="vertical-align: middle"> (Wildc.=*): </span>'; 
                } elseif ($currentregexmode=='r') { 
                    echo '<span style="vertical-align: middle"> RegEx Mode: </span>';
                } else { 
                    echo '<span style="vertical-align: middle"> RegEx(CS) Mode: </span>'; 
                }?>
                <input type="text" name="query" value="<?php echo tohtml($currentquery); ?>" maxlength="50" size="15" />&nbsp;
                <input type="button" name="querybutton" value="Filter" onclick="{val=document.form1.query.value;val=encodeURIComponent(val); location.href='edit_texts.php?page=1&amp;query=' + val;}" />&nbsp;
                <input type="button" value="Clear" onclick="{location.href='edit_texts.php?page=1&amp;query=';}" />
            </td>
        </tr>
        <tr>
            <td class="td1 center" colspan="2" nowrap="nowrap">
                Tag #1:
                <select name="tag1" onchange="{val=document.form1.tag1.options[document.form1.tag1.selectedIndex].value; location.href='edit_texts.php?page=1&amp;tag1=' + val;}">
                    <?php echo get_texttag_selectoptions($currenttag1, $currentlang); ?>
                </select>
            </td>
            <td class="td1 center" nowrap="nowrap">
                Tag #1 .. 
                <select name="tag12" onchange="{val=document.form1.tag12.options[document.form1.tag12.selectedIndex].value; location.href='edit_texts.php?page=1&amp;tag12=' + val;}">
                    <?php echo get_andor_selectoptions($currenttag12); ?>
                </select> .. Tag #2
            </td>
            <td class="td1 center" nowrap="nowrap">
                Tag #2:
                <select name="tag2" onchange="{val=document.form1.tag2.options[document.form1.tag2.selectedIndex].value; location.href='edit_texts.php?page=1&amp;tag2=' + val;}">
                    <?php echo get_texttag_selectoptions($currenttag2, $currentlang); ?>
                </select>
            </td>
        </tr>
        <?php 
        if($recno > 0) {
            ?>
        <tr>
            <th class="th1" colspan="2" nowrap="nowrap">
                <?php echo $recno; ?> Text<?php echo ($recno==1?'':'s'); ?>
            </th>
            <th class="th1" colspan="1" nowrap="nowrap">
                <?php makePager($currentpage, $pages, 'edit_texts.php', 'form1'); ?>
            </th>
            <th class="th1" colspan="1" nowrap="nowrap">
                Sort Order:
                <select name="sort" onchange="{val=document.form1.sort.options[document.form1.sort.selectedIndex].value; location.href='edit_texts.php?page=1&amp;sort=' + val;}">
                    <?php echo get_textssort_selectoptions($currentsort); ?>
                </select>
            </th>
        </tr>
            <?php 
        }
        ?>
    </table>
</form>
    <?php
}

/**
 * Make links to navigate to other pages if necessary.
 * 
 * @param int $recno Record number
 * 
 * @return void
 */
function edit_texts_other_pages($recno)
{

    $maxperpage = (int) getSettingWithDefault('set-texts-per-page');

    $pages = $recno == 0 ? 0 : (intval(($recno-1) / $maxperpage) + 1);

    if ($pages <= 0) { 
        return;
    }

    $currentpage = processSessParam("page", "currenttextpage", '1', 1);
    if ($currentpage < 1) { 
        $currentpage = 1; 
    }
    if ($currentpage > $pages) { 
        $currentpage = $pages; 
    }
    ?>
<table class="tab1" cellspacing="0" cellpadding="5">
    <tr>
        <th class="th1" nowrap="nowrap">
            <?php echo $recno; ?> Text<?php echo ($recno==1?'':'s'); ?>
        </th>
        <th class="th1" nowrap="nowrap">
            <?php makePager($currentpage, $pages, 'edit_texts.php', 'form2'); ?>
        </th>
    </tr>
</table>
    <?php
}

/**
 * Display the content of a table row for text edition.
 * 
 * @param array<string, string>                         $txrecord    
 * Various information about the text should contain 'TxID' at least.
 * @param string                                        $currentlang 
 * Current language ID
 * @param array<int<0, 5>|98|99, array<string, string>> $statuses
 * List of statuses WITH unknown words (status 0)
 * 
 * @return void
 */
function edit_texts_show_text_row($txrecord, $currentlang, $statuses)
{
    $txid = $txrecord['TxID'];
    /*
    Added by merge and makes statistics crash. To be removed?
    if ($showCounts) {
    flush();
    echo "ID" . $record['TxID'];
    $txttotalwords = textwordcount($record['TxID']);
    $txtworkedwords = textworkcount($record['TxID']);
    $txtworkedexpr = textexprcount($record['TxID']);
    $txtworkedall = $txtworkedwords + $txtworkedexpr;
    $txttodowords = $txttotalwords - $txtworkedwords;
    $percentunknown = 0;
    if ($txttotalwords != 0) {
    $percentunknown = 
    round(100*$txttodowords/$txttotalwords,0);
    if ($percentunknown > 100) $percentunknown = 100;
    if ($percentunknown < 0) $percentunknown = 0;
    }
    }
    */
    if (isset($record['TxAudioURI'])) {
        $audio = trim($record['TxAudioURI']);
    } else {
        $audio = ''; 
    }
    echo '<tr>
        <td class="td1 center">
            <a name="rec' . $txid . '">
                <input name="marked[]" class="markcheck" type="checkbox" value="' . $txid . '" ' . checkTest($txid, 'marked') . ' />
            </a>
        </td>
        <td nowrap="nowrap" class="td1 center">&nbsp;
            <a href="do_text.php?start=' . $txid . '">
                <img src="icn/book-open-bookmark.png" title="Read" alt="Read" />
            </a>&nbsp; 
            <a href="do_test.php?text=' . $txid . '">
                <img src="icn/question-balloon.png" title="Test" alt="Test" />
            </a>&nbsp;
        </td>
        <td nowrap="nowrap" class="td1 center">&nbsp;
            <a href="print_text.php?text=' . $txid . '">
                <img src="icn/printer.png" title="Print" alt="Print" />
            </a>&nbsp; 
            <a href="' . $_SERVER['PHP_SELF'] . '?arch=' . $txid . '">
                <img src="icn/inbox-download.png" title="Archive" alt="Archive" />
            </a>&nbsp; 
            <a href="' . $_SERVER['PHP_SELF'] . '?chg=' . $txid . '">
                <img src="icn/document--pencil.png" title="Edit" alt="Edit" />
            </a>&nbsp; 
            <span class="click" onclick="if (confirmDelete()) location.href=\'' . $_SERVER['PHP_SELF'] . '?del=' . $txid . '\';">
                <img src="icn/minus-button.png" title="Delete" alt="Delete" />
            </span>&nbsp;
        </td>';
    if ($currentlang == '') { 
        echo '<td class="td1 center">' . tohtml($txrecord['LgName']) . '</td>'; 
    }

    // title
    echo '<td class="td1 center">' . tohtml($txrecord['TxTitle']) . ' 
        <span class="smallgray2">' . tohtml($txrecord['taglist']) . '</span> &nbsp;' ; 
    if ($audio != '') { 
        echo '<img src="' . get_file_path('icn/speaker-volume.png') . '" title="With Audio" alt="With Audio" />';
    } 
    if (isset($txrecord['TxSourceURI']) && substr(trim($txrecord['TxSourceURI']), 0, 1)!='#') {
        echo ' <a href="' . $txrecord['TxSourceURI'] . '" target="_blank">
            <img src="'.get_file_path('icn/chain.png').'" title="Link to Text Source" alt="Link to Text Source" />
        </a>';
    }
    if ($txrecord['annotlen']) {
        echo ' <a href="print_impr_text.php?text=' . $txid . '">
            <img src="icn/tick.png" title="Annotated Text available" alt="Annotated Text available" />
        </a>';
    }
    echo '</td>';

    echo '<!-- total + composition -->
    <td class="td1 center">
        <span title="Total" id="total_' . $txid . '"></span>
    </td>
    <td class="td1 center">
        <span title="Saved" data_id="' . $txid . '">
            <a class="status4" id="saved_' . $txid . '" 
            href="edit_words.php?page=1&amp;query=&amp;status=&amp;tag12=0&amp;tag2=&amp;tag1=&amp;text_mode=0&amp;text=' . $txid . '">
            </a>
        </span>
    </td>
    <!-- unknown count -->
    <td class="td1 center">
        <span title="Unknown" class="status0" id="todo_' . $txid . '"></span>
    </td>
    <!-- unknown percent (added) -->
    <td class="td1 center">
        <span title="Unknown (%)" id="unknownpercent_' . $txid . '"></span>
    </td>
    <!-- chart -->
    <td class="td1 center">
        <ul class="barchart">';

    $i = array(0,1,2,3,4,5,99,98);
    foreach ($i as $cnt) {
        echo '<li class="bc' . $cnt . 
        ' "title="' . $statuses[$cnt]["name"] . ' (' . $statuses[$cnt]["abbr"] . 
        ')" style="border-top-width: 25px;">
            <span id="stat_' . $cnt . '_' . $txid .'">0</span>
        </li>';
    }
    echo '      </ul>
        </td>
    </tr>';
}

/**
 * Main form for displaying multiple texts.
 * 
 * @param string $currentlang Current language ID
 * @param int    $showCounts  Number of items to show
 * @param string $sql         SQL string to execute
 * @param int    $recno       Record number
 * 
 * @return void
 * 
 * @global int $debug Display debug information. 
 */
function edit_texts_texts_form($currentlang, $showCounts, $sql, $recno)
{
    global $debug;
    
    ?>
<form name="form2" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="data" value="" />
<table class="tab1" cellspacing="0" cellpadding="5">
    <tr><th class="th1" colspan="2">Multi Actions <img src="icn/lightning.png" title="Multi Actions" alt="Multi Actions" /></th></tr>
    <tr>
        <td class="td1 center">
            <input type="button" value="Mark All" onclick="selectToggle(true,'form2');" />
            <input type="button" value="Mark None" onclick="selectToggle(false,'form2');" />
        </td>
        <td class="td1 center">
            Marked Texts:&nbsp;
            <select name="markaction" id="markaction" disabled="disabled" onchange="multiActionGo(document.form2, document.form2.markaction);"><?php echo get_multipletextactions_selectoptions(); ?></select>
        </td>
    </tr>
</table>
<table class="sortable tab1" cellspacing="0" cellpadding="5">
<thead class="test_class_to_delete">
    <tr>
        <th class="th1 sorttable_nosort">Mark</th>
        <th class="th1 sorttable_nosort">Read<br />&amp;&nbsp;Test</th>
        <th class="th1 sorttable_nosort">Actions</th>
        <?php if ($currentlang == '') { 
            echo '<th class="th1 clickable">Lang.</th>'; 
        } ?>
        <th class="th1 clickable">
            Title [Tags] / Audio:&nbsp;
            <img src="<?php print_file_path('icn/speaker-volume.png'); ?>" title="With Audio" alt="With Audio" />, 
            Src.Link:&nbsp;<img src="<?php print_file_path('icn/chain.png'); ?>" title="Source Link available" alt="Source Link available" />, 
            Ann.Text:&nbsp;<img src="icn/tick.png" title="Annotated Text available" alt="Annotated Text available" />
        </th>
        <th class="th1 sorttable_numeric clickable">
            Total<br />Words<br />
            <div class="wc_cont">
                <span id="total" data_wo_cnt="<?php echo substr($showCounts, 0, 1); ?>"></span>
            </div>
        </th>
        <th class="th1 sorttable_numeric clickable">
            Saved<br />Wo+Ex<br />
            <div class="wc_cont">
                <span id="saved" data_wo_cnt="<?php echo substr($showCounts, 1, 1); ?>"></span>
            </div>
        </th>
        <th class="th1 sorttable_numeric clickable">
            Unkn.<br />Words<br />
            <div class="wc_cont">
                <span id="unknown" data_wo_cnt="<?php echo substr($showCounts, 2, 1); ?>"></span>
            </div>
        </th>
        <th class="th1 sorttable_numeric clickable">
            Unkn.<br />Perc.<br />
            <div class="wc_cont">
                <span id="unknownpercent" data_wo_cnt="<?php echo substr($showCounts, 3, 1); ?>"></span>
            </div>
        </th>
        <th class="th1 sorttable_numeric clickable">
            Status<br />Charts<br />
            <div class="wc_cont">
                <span id="chart" data_wo_cnt="<?php echo substr($showCounts, 4, 1); ?>"></span>
            </div>
        </th>
    </tr>
</thead>
<tbody>
    <?php
    if ($debug) { 
        echo $sql; 
    }
    $statuses = get_statuses();
    $statuses[0]["name"] = 'Unknown';
    $statuses[0]["abbr"] = 'Ukn';
    $res = do_mysqli_query($sql);
    $showCounts = (int)getSettingWithDefault('set-show-text-word-counts');
    while ($record = mysqli_fetch_assoc($res)) {
        edit_texts_show_text_row($record, $currentlang, $statuses);
    }
    mysqli_free_result($res);

    ?>
</tbody>
</table>


    <?php 
    edit_texts_other_pages($recno);
    ?>
</form>
    <?php
}

/**
 * Main display for the edit text functionality.
 * 
 * @param string $message Message to display.
 * 
 * @return void
 * 
 * @global string $tbpref Database table prefix
 * @global int    $debug  Debug mode active or not
 */
function edit_texts_display($message)
{
    global $tbpref, $debug;

    // Page, Sort, etc.

    $currentlang = validateLang(processDBParam("filterlang",'currentlanguage','',0));
    $currentsort = processDBParam("sort", 'currenttextsort', '1', 1);

    $currentpage = processSessParam("page", "currenttextpage", '1', 1);
    $currentquery = processSessParam("query", "currenttextquery", '', 0);
    $currentquerymode = processSessParam(
        "query_mode", "currenttextquerymode", 'title,text', 0
    );
    $currentregexmode = getSettingWithDefault("set-regex-mode");

    $wh_lang = ($currentlang != '') ? (' and TxLgID=' . $currentlang) : '';

    $wh_query = edit_texts_get_wh_query(
        $currentquery, $currentquerymode, $currentregexmode
    );

    if ($currentquery!=='' && $currentregexmode!=='') {
        if (@mysqli_query(
            $GLOBALS["DBCONNECTION"], 
            'SELECT "test" RLIKE ' . convert_string_to_sqlsyntax($currentquery)
        )===false
        ) {
            $currentquery = '';
        }
    }

    $wh_tag = edit_texts_get_wh_tag($currentlang);

    echo error_message_with_hide($message, 0);

    $sql = "SELECT COUNT(*) AS value 
    FROM (
        SELECT TxID 
        FROM (
            {$tbpref}texts 
            LEFT JOIN {$tbpref}texttags 
            ON TxID = TtTxID
        ) WHERE (1=1) {$wh_lang}{$wh_query}
        GROUP BY TxID {$wh_tag}
    ) AS dummy";
    $recno = (int) get_first_value($sql);
    if ($debug) { 
        echo $sql . ' ===&gt; ' . $recno; 
    }

    $maxperpage = (int) getSettingWithDefault('set-texts-per-page');

    $pages = $recno == 0 ? 0 : (intval(($recno-1) / $maxperpage) + 1);

    if ($currentpage < 1) { 
        $currentpage = 1; 
    }
    if ($currentpage > $pages) { 
        $currentpage = $pages; 
    }
    $limit = 'LIMIT ' . (($currentpage-1) * $maxperpage) . ',' . $maxperpage;

    $sorts = array('TxTitle','TxID desc','TxID asc');
    $lsorts = count($sorts);
    if ($currentsort < 1) { 
        $currentsort = 1; 
    }
    if ($currentsort > $lsorts) { 
        $currentsort = $lsorts; 
    }

    ?>

<link rel="stylesheet" type="text/css" href="<?php print_file_path('css/css_charts.css');?>" />
<p>
    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?new=1">
        <img src="icn/plus-button.png" title="New" alt="New" />
        New Text ...
    </a> &nbsp; | &nbsp;
    <a href="long_text_import.php">
        <img src="icn/plus-button.png" title="Long Text Import" alt="Long Text Import" /> 
        Long Text Import ...
    </a> &nbsp; | &nbsp;
    <a href="do_feeds.php?page=1&amp;check_autoupdate=1">
        <img src="icn/plus-button.png" title="RSS Import" alt="RSS Import" /> 
        Newsfeed Import ...
    </a>
</p>
    <?php
    edit_texts_filters_form($currentlang, $recno, $currentpage, $pages);

    if ($recno==0) {
        ?>
    <p>No texts found.</p>
        <?php
    } else {
        $showCounts = getSettingWithDefault('set-show-text-word-counts');
        if(strlen($showCounts)!=5) { 
            $showCounts = "11111"; 
        }
        $sql = "SELECT TxID, TxTitle, LgName, TxAudioURI, TxSourceURI, 
        LENGTH(TxAnnotatedText) AS annotlen,
        IFNULL(
            CONCAT(
                '[', 
                group_concat(DISTINCT T2Text ORDER BY T2Text separator ', '),
                ']'),
            ''
        ) AS taglist
        FROM (
            ({$tbpref}texts LEFT JOIN {$tbpref}texttags ON TxID = TtTxID) 
            LEFT JOIN {$tbpref}tags2 ON T2ID = TtT2ID
        ), {$tbpref}languages
        WHERE LgID=TxLgID {$wh_lang}{$wh_query} 
        GROUP BY TxID $wh_tag
        ORDER BY {$sorts[$currentsort-1]} 
        {$limit}";
        edit_texts_texts_form($currentlang, $showCounts, $sql, $recno);
        ?>
<script type="text/javascript">
    var WORDCOUNTS = '', SUW = SHOWUNIQUE = <?php echo intval($showCounts, 2); ?>;

    /**
     * Prepare the action so that a click switches between 
     * unique word count and total word count.
     * 
     * @returns {undefined}
     */
    function prepare_word_count_click() {
        $('#total,#saved,#unknown,#chart,#unknownpercent')
        .on('click', function( event ) {
            $(this).attr('data_wo_cnt',parseInt($(this).attr('data_wo_cnt'))^1);
            word_count_click();
            event.stopImmediatePropagation();
        }).attr('title',"u: Unique Word Counts\nt: Total  Word  Counts");
        do_ajax_word_counts();
    }

    /**
     * Save the settings about unique/total words count.
     * 
     * @returns {undefined}
     */
    function save_text_word_count_settings() {
        if (SUW == SHOWUNIQUE) {
            return;
        }
        const a = $('#total').attr('data_wo_cnt') + 
        $('#saved').attr('data_wo_cnt') + 
        $('#unknown').attr('data_wo_cnt') + 
        $('#unknownpercent').attr('data_wo_cnt') + 
        $('#chart').attr('data_wo_cnt');
        $.ajax({
            type: "POST",
            url:'inc/ajax_save_setting.php', 
            data: {k: 'set-show-text-word-counts', v: a}, 
            async:false
        });
    }

    $(document).ready(prepare_word_count_click);
    $(window).on('beforeunload', save_text_word_count_settings);
</script>
        <?php

    }

    ?>

<p>
    <input type="button" value="Archived Texts" onclick="location.href='edit_archivedtexts.php?query=&amp;page=1';" />
</p>

    <?php

}

/**
 * Main function for displaying the edit_texts page.
 * 
 * @return void
 */
function edit_texts_do_page()
{
    $currentlang = validateLang(processDBParam("filterlang",'currentlanguage','',0));
    $no_pagestart = getreq('markaction') == 'test' || 
    getreq('markaction') == 'deltag' || 
    substr(getreq('op'), -8) == 'and Open';

    if (!$no_pagestart) {
        pagestart('My ' . getLanguage($currentlang) . ' Texts', true);
    }
    $message = '';

    // MARK ACTIONS

    $message1 = null;
    if (isset($_REQUEST['markaction'])) {
        list($message, $message1) = edit_texts_mark_action(
            $_REQUEST['markaction'], $_REQUEST['marked'], getreq('data')
        );
    }
    if (isset($_REQUEST['del'])) {
        // DEL
        $message = edit_texts_delete((int) getreq('del'));
    } elseif (isset($_REQUEST['arch'])) {
        // ARCH
        $message = edit_texts_archive((int) getreq('arch'));
    } elseif (isset($_REQUEST['op'])) {
        // INS/UPD
        $message = edit_texts_do_operation($_REQUEST['op'],$message1,$no_pagestart);
    }

    if (isset($_REQUEST['new'])) {
        // NEW
        edit_texts_new($currentlang);
    } elseif (isset($_REQUEST['chg'])) {
        // CHG
        edit_texts_change(getreq('chg'));
    } else {
        // DISPLAY
        edit_texts_display($message);
    }

    pageend();
}

edit_texts_do_page();
?>
