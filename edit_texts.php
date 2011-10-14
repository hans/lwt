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

// Page, Sort, etc.

$filter = array('language' => validateLang(processDBParam("filterlang",'currentlanguage','',0)),
                'sort' => processDBParam("sort",'currenttextsort','1',1),
                'page' => processSessParam("page","currenttextpage",'1',1),
                'query' => processSessParam("query","currenttextquery",'',0),
                'tag12' => processSessParam("tag12","currenttexttag12",'',0));

$filter['tag1'] = validateTextTag(processSessParam("tag1","currenttexttag1",'',0), $filter['language']);
$filter['tag2'] = validateTextTag(processSessParam("tag2","currenttexttag2",'',0), $filter['language']);

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
	pagestart('My ' . getLanguage($filter['language']) . ' Texts',true);
}

$message = '';

// MARK ACTIONS

if (isset($_REQUEST['markaction'])) {
	$markaction = $_REQUEST['markaction'];
	$actiondata = stripTheSlashesIfNeeded(getreq('data'));
	$message = "Multiple Actions: 0";
	if (isset($_REQUEST['marked'])) {
		if (is_array($_REQUEST['marked'])) {
			$l = count($_REQUEST['marked']);
			if ($l > 0 ) {
				$list = "(" . $_REQUEST['marked'][0];
				for ($i=1; $i<$l; $i++) $list .= "," . $_REQUEST['marked'][$i];
				$list .= ")";

				if ($markaction == 'del') {
					$message3 = runsql('delete from textitems where TiTxID in ' . $list, "Text items deleted");
					$message2 = runsql('delete from sentences where SeTxID in ' . $list, "Sentences deleted");
					$message1 = runsql('delete from texts where TxID in ' . $list, "Texts deleted");
					$message = $message1 . " / " . $message2 . " / " . $message3;
					adjust_autoincr('texts','TxID');
					adjust_autoincr('sentences','SeID');
					adjust_autoincr('textitems','TiID');
					runsql("DELETE texttags FROM (texttags LEFT JOIN texts on TtTxID = TxID) WHERE TxID IS NULL",'');
				}

				elseif ($markaction == 'arch') {
					runsql('delete from textitems where TiTxID in ' . $list, "");
					runsql('delete from sentences where SeTxID in ' . $list, "");
					$count = 0;
					$sql = "select TxID from texts where TxID in " . $list;
					$res = mysql_query($sql);
					if ($res == FALSE) die("Invalid Query: $sql");
					while ($record = mysql_fetch_assoc($res)) {
						$id = $record['TxID'];
						$count += (0 + runsql('insert into archivedtexts (AtLgID, AtTitle, AtText, AtAudioURI) select TxLgID, TxTitle, TxText, TxAudioURI from texts where TxID = ' . $id, ""));
						$aid = get_last_key();
						runsql('insert into archtexttags (AgAtID, AgT2ID) select ' . $aid . ', TtT2ID from texttags where TtTxID = ' . $id, "");
					}
					mysql_free_result($res);
					$message = 'Text(s) archived: ' . $count;
					runsql('delete from texts where TxID in ' . $list, "");
					runsql("DELETE texttags FROM (texttags LEFT JOIN texts on TtTxID = TxID) WHERE TxID IS NULL",'');
					adjust_autoincr('texts','TxID');
					adjust_autoincr('sentences','SeID');
					adjust_autoincr('textitems','TiID');
				}

				elseif ($markaction == 'addtag' ) {
					$message = addtexttaglist($actiondata,$list);
				}

				elseif ($markaction == 'deltag' ) {
					$message = removetexttaglist($actiondata,$list);
					header("Location: edit_texts.php");
					exit();
				}

				elseif ($markaction == 'setsent') {
					$count = 0;
					$sql = "select WoID, WoTextLC, min(TiSeID) as SeID from words, textitems where TiLgID = WoLgID and TiTextLC = WoTextLC and TiTxID in " . $list . " and ifnull(WoSentence,'') not like concat('%{',WoText,'}%') group by WoID order by WoID, min(TiSeID)";
					$res = mysql_query($sql);
					if ($res == FALSE) die("Invalid Query: $sql");
					while ($record = mysql_fetch_assoc($res)) {
						$sent = getSentence($record['SeID'], $record['WoTextLC'], (int) getSettingWithDefault('set-term-sentence-count'));
						$count += runsql('update words set WoSentence = ' . convert_string_to_sqlsyntax(repl_tab_nl($sent[1])) . ' where WoID = ' . $record['WoID'], '');
					}
					mysql_free_result($res);
					$message = 'Term Sentences set from Text(s): ' . $count;
				}

				elseif ($markaction == 'rebuild') {
					$count = 0;
					$sql = "select TxID, TxLgID from texts where TxID in " . $list;
					$res = mysql_query($sql);
					if ($res == FALSE) die("Invalid Query: $sql");
					while ($record = mysql_fetch_assoc($res)) {
						$id = $record['TxID'];
						$message2 = runsql('delete from sentences where SeTxID = ' . $id, "Sentences deleted");
						$message3 = runsql('delete from textitems where TiTxID = ' . $id, "Text items deleted");
						adjust_autoincr('sentences','SeID');
						adjust_autoincr('textitems','TiID');
						splitText(
							get_first_value(
								'select TxText as value from texts where TxID = ' . $id),
								$record['TxLgID'], $id );
						$count++;
					}
					mysql_free_result($res);
					$message = 'Text(s) re-parsed: ' . $count;
				}

				elseif ($markaction == 'test' ) {
					$_SESSION['testsql'] = ' words, textitems where TiLgID = WoLgID and TiTextLC = WoTextLC and TiTxID in ' . $list . ' ';
					header("Location: do_test.php?selection=1");
					exit();
				}

			}
		}
	}
}

// DEL

if (isset($_REQUEST['del'])) {
	$message3 = runsql('delete from textitems where TiTxID = ' . $_REQUEST['del'],
		"Text items deleted");
	$message2 = runsql('delete from sentences where SeTxID = ' . $_REQUEST['del'],
		"Sentences deleted");
	$message1 = runsql('delete from texts where TxID = ' . $_REQUEST['del'],
		"Texts deleted");
	$message = $message1 . " / " . $message2 . " / " . $message3;
	adjust_autoincr('texts','TxID');
	adjust_autoincr('sentences','SeID');
	adjust_autoincr('textitems','TiID');
	runsql("DELETE texttags FROM (texttags LEFT JOIN texts on TtTxID = TxID) WHERE TxID IS NULL",'');
}

// ARCH

elseif (isset($_REQUEST['arch'])) {
	$message3 = runsql('delete from textitems where TiTxID = ' . $_REQUEST['arch'],
		"Text items deleted");
	$message2 = runsql('delete from sentences where SeTxID = ' . $_REQUEST['arch'],
		"Sentences deleted");
	$message4 = runsql('insert into archivedtexts (AtLgID, AtTitle, AtText, AtAudioURI) select TxLgID, TxTitle, TxText, TxAudioURI from texts where TxID = ' . $_REQUEST['arch'], "Archived Texts saved");
	$id = get_last_key();
	runsql('insert into archtexttags (AgAtID, AgT2ID) select ' . $id . ', TtT2ID from texttags where TtTxID = ' . $_REQUEST['arch'], "");
	$message1 = runsql('delete from texts where TxID = ' . $_REQUEST['arch'], "Texts deleted");
	$message = $message4 . " / " . $message1 . " / " . $message2 . " / " . $message3;
	adjust_autoincr('texts','TxID');
	adjust_autoincr('sentences','SeID');
	adjust_autoincr('textitems','TiID');
	runsql("DELETE texttags FROM (texttags LEFT JOIN texts on TtTxID = TxID) WHERE TxID IS NULL",'');
}

// INS/UPD

elseif (isset($_REQUEST['op'])) {

	if (strlen(prepare_textdata($_REQUEST['TxText'])) > 65000) {
		$message = "Error: Text too long, must be below 65000 Bytes";
		if ($no_pagestart) pagestart('My ' . getLanguage($filter['language']) . ' Texts',true);
	}

	else {

		// CHECK

		if ($_REQUEST['op'] == 'Check') {
        $result = checkText($_REQUEST['TxText'], $_REQUEST['TxLgId']);
        render('texts/check', compact('result'));
        die();
		}

		// INSERT

		elseif (substr($_REQUEST['op'],0,4) == 'Save') {
			$message1 = runsql('insert into texts (TxLgID, TxTitle, TxText, TxAudioURI) values( ' .
			$_REQUEST["TxLgID"] . ', ' .
			convert_string_to_sqlsyntax($_REQUEST["TxTitle"]) . ', ' .
			convert_string_to_sqlsyntax($_REQUEST["TxText"]) . ', ' .
			convert_string_to_sqlsyntax($_REQUEST["TxAudioURI"]) . ' ' .
			')', "Saved");
			$id = get_last_key();
			saveTextTags($id);
		}

		// UPDATE

		elseif (substr($_REQUEST['op'],0,6) == 'Change') {
			$message1 = runsql('update texts set ' .
			'TxLgID = ' . $_REQUEST["TxLgID"] . ', ' .
			'TxTitle = ' . convert_string_to_sqlsyntax($_REQUEST["TxTitle"]) . ', ' .
			'TxText = ' . convert_string_to_sqlsyntax($_REQUEST["TxText"]) . ', ' .
			'TxAudioURI = ' . convert_string_to_sqlsyntax($_REQUEST["TxAudioURI"]) . ' ' .
			'where TxID = ' . $_REQUEST["TxID"], "Updated");
			$id = $_REQUEST["TxID"];
			saveTextTags($id);
		}

		$message2 = runsql('delete from sentences where SeTxID = ' . $id,
			"Sentences deleted");
		$message3 = runsql('delete from textitems where TiTxID = ' . $id,
			"Textitems deleted");
		adjust_autoincr('sentences','SeID');
		adjust_autoincr('textitems','TiID');

		splitText(
			get_first_value(
				'select TxText as value from texts where TxID = ' . $id),
			$_REQUEST["TxLgID"], $id );

		$message = $message1 . " / " . $message2 . " / " . $message3 . " / Sentences added: " . get_first_value('select count(*) as value from sentences where SeTxID = ' . $id) . " / Text items added: " . get_first_value('select count(*) as value from textitems where TiTxID = ' . $id);

		if(substr($_REQUEST['op'],-8) == "and Open") {
			header('Location: do_text.php?start=' . $id);
			exit();
		}

	}

}

if (isset($_REQUEST['new'])) {
    render('texts/new', compact('currentlang'));
} elseif (isset($_REQUEST['chg'])) {
	$sql = 'select TxLgID, TxTitle, TxText, TxAudioURI from texts where TxID = ' . $_REQUEST['chg'];
	$res = mysql_query($sql);
	if ($res == FALSE) die("Invalid Query: $sql");
	if ($record = mysql_fetch_assoc($res)) {
      render('texts/edit', compact('record'));
	}

	mysql_free_result($res);
} else {

	echo error_message_with_hide($message,0);

	$sql = 'select count(*) as value from (select TxID from (texts left JOIN texttags ON TxID = TtTxID) where (1=1) ' . $wh_lang . $wh_query . ' group by TxID ' . $wh_tag . ') as dummy';
	$recno = get_first_value($sql);
	if ($debug) echo $sql . ' ===&gt; ' . $recno;

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

  if ($debug) echo $sql;
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
         compact('filter', 'recno', 'records', 'pages', 'showCounts'));
}
?>