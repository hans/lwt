<?php

/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions,
unless such conditions are required by law.

Developed by J. Pierre in 2011.
***************************************************************/

/**************************************************************
Call: edit_texts.php?....
      ... markaction=[opcode] ... do actions on marked texts
      ... del=[textid] ... do delete
      ... arch=[textid] ... do archive
      ... op=Check ... do check
      ... op=Save ... do insert new
      ... op=Change ... do update
      ... op=Save+and+Open ... do insert new and open
      ... op=Change+and+Open ... do update and open
      ... new=1 ... display new text screen
      ... chg=[textid] ... display edit screen
      ... filterlang=[langid] ... language filter
      ... sort=[sortcode] ... sort
      ... page=[pageno] ... page
      ... query=[titlefilter] ... title filter
Manage active texts
***************************************************************/

require 'lwt-startup.php';
require_once LWT_INCLUDE . 'texts.php';
require_once LWT_INCLUDE . 'tags.php';

// Page, Sort, etc.

$filter = array('language' => validateLang(get_parameter("filterlang", 'db', 'currentlanguage', '')),
                'sort' => get_parameter("sort", 'db', 'currenttextsort', 1, TRUE),
                'page' => get_parameter('page', 'session', 'currenttextpage', 1, TRUE),
                'query' => get_parameter('query', 'session', 'currenttextquery', ''),
                'tag12' => get_parameter('tag12', 'session', 'currenttexttag12', ''));

$filter['tag1'] = validateTextTag(get_parameter('tag1', 'session', 'currenttexttag1', ''), $filter['language']);
$filter['tag2'] = validateTextTag(get_parameter('tag2', 'session', 'currenttexttag2', ''), $filter['language']);

$wh_lang = ($filter['language'] != '') ? (' and TxLgID=' . $filter['language']) : '';
$wh_query = convert_string_to_sqlsyntax(str_replace("*","%",mb_strtolower($filter['query'], 'UTF-8')));
$wh_query = ($filter['query'] != '') ? (' and TxTitle like ' . $wh_query) : '';

if ($filter['tag1'] == '' && $filter['tag2'] == '')
    $wh_tag = '';
else {
    if ($filter['tag1'] != '') {
        if ($filter['tag1'] == -1)
            $wh_tag1 = "group_concat(TtT2ID) IS NULL";
        else
            $wh_tag1 = "concat('/',group_concat(TtT2ID separator '/'),'/') like '%/" . $filter['tag1'] . "/%'";
    }
    if ($filter['tag2'] != '') {
        if ($filter['tag2'] == -1)
            $wh_tag2 = "group_concat(TtT2ID) IS NULL";
        else
            $wh_tag2 = "concat('/',group_concat(TtT2ID separator '/'),'/') like '%/" . $filter['tag2'] . "/%'";
    }
    if ($filter['tag1'] != '' && $filter['tag2'] == '')
        $wh_tag = " having (" . $wh_tag1 . ') ';
    elseif ($filter['tag2'] != '' && $filter['tag1'] == '')
        $wh_tag = " having (" . $wh_tag2 . ') ';
    else
        $wh_tag = " having ((" . $wh_tag1 . ($filter['tag12'] ? ') AND (' : ') OR (') . $wh_tag2 . ')) ';
}

$no_pagestart = (getreq('markaction') == 'test' || getreq('markaction') == 'deltag' || substr(getreq('op'),-8) == 'and Open');

if (! $no_pagestart) {
    $page_title = 'My ' . getLanguage($filter['language']) . ' Texts';
}

$message = '';

// MARK ACTIONS

if ( isset($_REQUEST['markaction'], $_REQUEST['marked']) && is_array($_REQUEST['marked']) ) {
    $markaction = $_REQUEST['markaction'];
    $marked = $_REQUEST['marked'];
    $action_data = stripslashes(getreq('data'));
    $message = "Multiple Actions: 0";

    $l = count($marked);
    if ( $l > 0 ) {
        switch ( $markaction ) {
        case 'del':
            delete_texts($marked);
            $message = "Text items, sentences, and texts deleted.";

            break;
        case 'arch':
            archive_texts($marked);
            $message = 'Text(s) archived: ' . $l;

            break;
        case 'addtag':
            add_tag_to_texts(load_tag($action_data), $marked);
            $message = "Tag added to " . $l . " texts";

            break;
        case 'deltag':
            $tag = load_tag($action_data);
            if ( $tag )
                remove_tag_from_texts($tag, $marked);

            $message = "Tag removed from " . $l . " texts";
            header("Location: edit_texts.php");
            exit();

            break;
        case 'setsent':
            associate_text_words_with_sentences($marked);
            $message = 'Term Sentences set from Text(s): ' . $l;

            break;
        case 'rebuild':
            reparse_texts($marked);
            $message = 'Text(s) re-parsed: ' . $l;

            break;
        case 'test':
            $_SESSION['testsql'] = ' words, textitems WHERE TiLgID = WoLgID AND TiTextLC = WoTextLC AND TiTxID IN ' . $list . ' ';
            header("Location: do_test.php?selection=1");
            break;
        }
    }
}

// DEL

if (isset($_REQUEST['del'])) {
    delete_text($_REQUEST['del']);
    $message = 'Text items, sentences, and text deleted.';
} elseif (isset($_REQUEST['arch'])) {
    archive_text($_REQUEST['arch']);
    $message = 'Archived texts saved / Text items deleted / Sentences deleted';
} elseif (isset($_REQUEST['op'])) {
    if (strlen(prepare_textdata($_REQUEST['TxText'])) > 65000) {
        $message = "Error: Text too long, must be below 65000 Bytes";
        if ($no_pagestart) pagestart('My ' . getLanguage($filter['language']) . ' Texts',true);
    } else {
        $id = 0;

        switch ( $_REQUEST['op'] ) {
        case 'Check':
            $result = checkText($_REQUEST['TxText'], $_REQUEST['TxLgId']);
            render('texts/check', compact('result', 'page_title'));
            die();

            break;
        case 'Save':
            $id = create_text(array('TxLgID' => $_REQUEST['TxLgID'],
                              'TxTitle' => $_REQUEST['TxTitle'],
                              'TxText' => $_REQUEST['TxText'],
                              'TxAudioURI' => $_REQUEST['TxAudioURI'],
                              'TxTags' => $_REQUEST['TextTags']['TagList']));

            break;
        case 'Change':
        case 'Change and Open':
            $id = $_REQUEST['TxID'];

            update_text($id,
                        array('TxLgID' => $_REQUEST['TxLgID'],
                              'TxTitle' => $_REQUEST['TxTitle'],
                              'TxText' => $_REQUEST['TxText'],
                              'TxAudioURI' => $_REQUEST['TxAudioURI'],
                              'TxTags' => $_REQUEST['TextTags']['TagList']));
        }

        reparse_text($id);

        $message = "Sentences added: "
            . get_first_value('SELECT COUNT(*) AS value FROM sentences WHERE SeTxID = ' . $id)
            . " / Text items added: "
            . get_first_value('SELECT COUNT(*) AS value FROM textitems WHERE TiTxID = ' . $id);

        if ( $_REQUEST['op'] == 'Change and Open' ) {
            header('Location: do_text.php?start=' . $id);
            exit();
        }
    }
}

if (isset($_REQUEST['new'])) {
    render('texts/new', compact('currentlang', 'page_title'));
} elseif (isset($_REQUEST['chg'])) {
    $sql = 'select TxLgID, TxTitle, TxText, TxAudioURI from texts where TxID = ' . $_REQUEST['chg'];
    $res = mysql_query($sql);
    if ($res == FALSE) die("Invalid Query: $sql");
    if ($record = mysql_fetch_assoc($res)) {
      render('texts/edit', compact('record', 'page_title'));
    }

    mysql_free_result($res);
} else {

    echo error_message_with_hide($message,0);

    $sql = 'SELECT COUNT(*) AS value
      FROM
          ( SELECT TxID
            FROM ( texts LEFT JOIN texttags ON TxID = TtTxID )
            WHERE ( 1 = 1 ) ' . $wh_lang . $wh_query . '
            GROUP BY TxID ' . $wh_tag . ' ) AS dummy';

    $recno = get_first_value($sql);
    if (LWT_DEBUG) echo $sql . ' ===&gt; ' . $recno;

    $maxperpage = getSettingWithDefault('set-texts-per-page');

    $pages = $recno == 0 ? 0 : (intval(($recno-1) / $maxperpage) + 1);

    if ($filter['page'] < 1) $filter['page'] = 1;
    if ($filter['page'] > $pages) $filter['page'] = $pages;
    $limit = 'LIMIT ' . max(0, (($filter['page']-1) * $maxperpage)) . ',' . $maxperpage;

    $sorts = array('TxTitle','TxID desc');
    $lsorts = count($sorts);
    if ($filter['sort'] < 1) $filter['sort'] = 1;
    if ($filter['sort'] > $lsorts) $filter['sort'] = $lsorts;

  //


  $sql = 'SELECT TxID, TxTitle, LgName, TxAudioURI, IFNULL(CONCAT(\'[\', GROUP_CONCAT(DISTINCT T2Text ORDER BY T2Text SEPARATOR \', \'),\']\'),\'\') AS taglist
      FROM (
          ( texts LEFT JOIN texttags ON TxID = TtTxID )
              LEFT JOIN tags2 ON T2ID = TtT2ID ),
          languages
      WHERE LgID = TxLgID ' . $wh_lang . $wh_query . '
      GROUP BY TxID ' . $wh_tag . '
      ORDER BY ' . $sorts[$filter['sort']-1] . ' ' . $limit;

  if (LWT_DEBUG) echo $sql;
  $res = mysql_query($sql);
  if ($res == FALSE) die("Invalid Query: $sql");
  $showCounts = getSettingWithDefault('set-show-text-word-counts')+0;

  $records = array();
  while ($record = mysql_fetch_assoc($res)) {
      if ( $showCounts ) {
          $record['total_words'] = textwordcount($record['TxID']);
          $record['worked_words'] = textworkcount($record['TxID']);
          $record['worked_expr'] = textexprcount($record['TxID']);
          $record['worked_all'] = $record['worked_words'] + $record['worked_expr'];
          $record['todo_words'] = $record['total_words'] - $record['worked_words'];

          $record['percent_unknown'] = 0;
          if ( $record['total_words'] != 0 ) {
              $record['percent_unknown'] = round(100 * $record['todo_words'] / $record['total_words'], 0);

              /**
               * Percent unknown must be 0 <= x <= 100
               */
              $record['percent_unknown'] = min(100, max(0, $record['percent_unknown']));
          }
      }

      $record['audio'] = ( isset($record['TxAudioURI']) ? trim($record['TxAudioURI']) : '' );

      $records[] = $record;
  }

  mysql_free_result($res);

  render('texts/display',
         compact('filter', 'recno', 'records', 'pages', 'showCounts',
                 'page_title'));
}
?>