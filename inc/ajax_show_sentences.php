<?php


/**************************************************************
Call: ajax_show_sentences.php?...
      ... lang=[langid] ... language
      ... word=[word] ... word in lowercase
      ... sentctl=[sentctl] ... sentence js control
Show sentences in edit_texts.php, etc.
***************************************************************/

require_once 'inc/session_utility.php';

$lang = $_POST['lang'] + 0;
$word = stripTheSlashesIfNeeded($_POST['word']);
$wid = stripTheSlashesIfNeeded($_POST['woid']);
$ctl = stripTheSlashesIfNeeded($_POST['ctl']);

echo get20Sentences($lang, $word, $wid, $ctl, (int) getSettingWithDefault('set-term-sentence-count'));

?>