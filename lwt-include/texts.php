<?php
/**
 * Functions for operating on text entities.
 *
 * @package LWT
 * @since 2.0
 */

require_once LWT_INCLUDE . 'tags.php';

/**
 * Create a new text.
 *
 * @since 2.0
 *
 * @param array $properties
 *   Required: TxLgID, TxTitle, TxText, TxAudioURI
 * @return int Created text's ID
 */
function create_text(array $properties) {
    $res = mysql_query("INSERT INTO texts
            ( TxLgID, TxTitle, TxText, TxAudioURI )
        VALUES (
            " . (int)$properties['TxLgID'] . ",
            " . convert_string_to_sqlsyntax($properties['TxTitle']) . ",
            " . convert_string_to_sqlsyntax($properties['TxText']) . ",
            " . convert_string_to_sqlsyntax($properties['TxAudioURI']) . ")");
    if ( !$res ) return FALSE;

    $id = get_last_key();

    $tags = array_map('load_tag', $properties['TxTags']);
    $success = add_tags_to_text($id, $tags);

    return $success;
}

/**
 * Update a text object.
 *
 * @since 2.0
 *
 * @param int $id
 * @param array $properties
 *   Required: TxLgID, TxTitle, TxText, TxAudioURI
 * @return boolean Success
 */
function update_text($id, array $properties) {
    $id = (int)$id;

    $res = mysql_query("UPDATE texts SET
            TxLgID = " . (int)$properties['TxLgID'] . "
            TxTitle = " . convert_string_to_sqlsyntax($properties['TxTitle']) . "
            TxText = " . convert_string_to_sqlsyntax($properties['TxText']) . "
            TxAudioURI = " . convert_string_to_sqlsyntax($properties['TxAudioURI']) . "
        WHERE TxID = $id");
    if ( !$res ) return FALSE;

    $tags = array_map('load_tag', $properties['TxTags']);
    $success = add_tags_to_text($id, $tags);

    return $success;
}

/**
 * Delete a set of texts.
 *
 * @since 2.0
 *
 * @param array $ids Array of text IDs
 * @return boolean Success
 */
function delete_texts(array $ids) {
    $res_data = delete_texts_data($ids);

    $res_texts = mysql_query("DELETE FROM texts
        WHERE TxID IN $sql_list");

    $res_tags = mysql_query("DELETE texttags
        FROM ( texttags LEFT JOIN texts ON TtTxID = TxID )
        WHERE TxID IS NULL");

    return $res_data !== FALSE
        && $res_texts !== FALSE
        && $res_tags !== FALSE;
}

/**
 * Delete a text object.
 *
 * @see delete_texts
 * @since 2.0
 *
 * @param int $id Text ID
 * @return boolean Success
 */
function delete_text($id) {
    return delete_texts(array($id));
}

/**
 * Delete data associated with each text in a set (text items and sentences).
 * This may be used when texts are deleted or re-parsed.
 *
 * @since 2.0
 *
 * @param array $ids Array of text IDs
 * @return boolean Success
 */
function delete_texts_data(array $ids) {
    $sql_list = '(' . join(',', array_map('intval', $ids)) . ')';

    $res_items = mysql_query("DELETE FROM textitems
        WHERE TiTxID IN $sql_list");

    $res_sentences = mysql_query("DELETE FROM sentences
        WHERE SeTxID IN $sql_list");

    return $res_items !== FALSE
        && $res_sentences !== FALSE;
}

/**
 * Archive a set of texts.
 *
 * @since 2.0
 *
 * @param array $ids Array of text IDs
 * @return boolean Success
 */
function archive_texts(array $ids) {
    $ids = array_map('intval', $ids);

    $success = true;
    foreach ( $ids as $id ) {
        $success = mysql_query('INSERT INTO archivedtexts
            ( AtLgID, AtTitle, AtText, AtAudioURI )
            SELECT TxLgId, TxTitle, TxText, TxAudioURI
                FROM texts
                WHERE TxID = ' . $id) && $success;

        $arch_id = get_last_key();
        $success = mysql_query('INSERT INTO archtexttags ( AgAtID, AgT2ID )
            SELECT ' . $arch_id . ', TtT2ID
                FROM texttags
                WHERE TtTxId = ' . $id) && $success;
    }

    // Delete the old unarchived versions
    $success = delete_texts($ids) && $success;

    return $success;
}

/**
 * Archive a text object.
 *
 * @see archive_texts
 * @since 2.0
 *
 * @param int $id Text ID
 * @return boolean Success
 */
function archive_text($id) {
    return archive_texts(array($id));
}

/**
 * Re-parse a set of texts.
 *
 * @since 2.0
 *
 * @param array $ids Array of text IDs
 */
function reparse_texts(array $ids) {
    // Delete previously parsed data
    delete_texts_data($ids);

    $sql_list = '(' . join(',', array_map('intval', $ids)) . ')';
    $texts = mysql_query("SELECT TxID, TxLgID, TxText
        FROM texts
        WHERE TxID IN " . $sql_list);

    while ( $text = mysql_fetch_assoc($texts) ) {
        splitText($text['TxText'], $text['TxLgID'], $text['TxID']);
    }

    mysql_free_result($texts);
}

/**
 * Reparse a text object.
 *
 * @see reparse_texts
 * @since 2.0
 *
 * @param int $id Text ID
 * @return boolean Success
 */
function reparse_text($id) {
    return reparse_texts(array($id));
}

/**
 * Associate words in a text with their corresponding sentences. Each word's
 * WoSentence field will be updated with its containing sentence (with the
 * specific word highlighted).
 *
 * @since 2.0
 *
 * @param array $ids Array of text IDs
 */
function associate_text_words_with_sentences(array $ids) {
    $sql_list = '(' . join(',', array_map('intval', $ids)) . ')';

    $res = mysql_query("SELECT WoId, WoTextLC, MIN(TiSeID) AS SeID
        FROM words, textitems
        WHERE TiLgID = WoLgID
            AND TiTextLC = WoTextLC
            AND TiTxID IN " . $sql_list . "
            AND IFNULL(WoSentence, '') NOT LIKE CONCAT('%{', WoText, '}%')
        GROUP BY WoID
        ORDER BY WoID, MIN(TiSeID)");

    $sentence_mode = (int)getSettingWithDefault('set-term-sentence-count');

    while ( $record = mysql_fetch_assoc($res) ) {
        $sentence = getSentence($record['SeID'], $record['WoTextLC'], $sentence_mode);

        mysql_query("UPDATE words
            SET WoSentence = " . convert_string_to_sqlsyntax(repl_tab_nl($sentence[1])) . "
            WHERE WoID = " . (int)$record['WoID']);
    }

    mysql_free_result($res);
}

?>