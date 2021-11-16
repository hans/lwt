<?php

/**
 * \file
 * \brief Show sentences in edit_texts.php, etc.
 * 
 * Call: inc/ajax_show_sentences.php?...
 *    ... lang=[langid] ... language
 *    ... word=[word] ... word in lowercase
 *    ... sentctl=[sentctl] ... sentence js control
 * 
 * @author LWT Project <lwt-project@notmail.com>
 * @since  1.2.0
 */

require_once __DIR__ . '/session_utility.php';

$lang = $_POST['lang'] + 0;
$word = stripTheSlashesIfNeeded($_POST['word']);
$wid = stripTheSlashesIfNeeded($_POST['woid']);
$ctl = stripTheSlashesIfNeeded($_POST['ctl']);

echo get20Sentences($lang, $word, $wid, $ctl, (int) getSettingWithDefault('set-term-sentence-count'));

?>