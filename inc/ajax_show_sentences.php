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
 * @package Lwt
 * @author  LWT Project <lwt-project@hotmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/ajax__show__imported__terms_8php.html
 * @since   1.2.0
 */

require_once __DIR__ . '/session_utility.php';

chdir('..');
$lang = (int)$_POST['lang'];
$word = $_POST['word'];
$wid = $_POST['woid'];
$ctl = $_POST['ctl'];

echo get20Sentences(
    $lang, $word, $wid, $ctl, 
    (int) getSettingWithDefault('set-term-sentence-count')
);
?>