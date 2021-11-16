<?php

/**
 * \file
 * \brief Calculating Word Counts, Ajax call in edit_texts.php
 * 
 * Call: inc/ajax_word_counts.php?id=[textid1,textid2,...]
 * 
 * @author LWT Project <lwt-project@notmail.com>
 * @since  1.0.3
 */

require_once __DIR__ . '/session_utility.php';


textwordcount($_POST["id"]);

?>
