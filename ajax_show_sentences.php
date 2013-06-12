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
Call: ajax_show_sentences.php?...
      ... lang=[langid] ... language
      ... word=[word] ... word in lowercase
      ... sentctl=[sentctl] ... sentence js control
Show sentences in edit_texts.php, etc.
***************************************************************/

include "settings.inc.php";
include "connect.inc.php";
include "utilities.inc.php";

$lang = $_POST['lang'] + 0;
$word = stripTheSlashesIfNeeded($_POST['word']);
$ctl = stripTheSlashesIfNeeded($_POST['ctl']);

echo get20Sentences($lang,$word,$ctl, (int) getSettingWithDefault('set-term-sentence-count'));

?>