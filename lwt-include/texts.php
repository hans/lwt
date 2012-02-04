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
 * @return int Created text's ID or NULL on failure
 */
function create_text(array $properties) {
    $data = array('TxLgID' => (int)$properties['TxLgID'],
                  'TxTitle' => $properties['TxTitle'],
                  'TxText' => db_text_prepare($properties['TxText']),
                  'TxAudioURI' => $properties['TxAudioURI']);

    $id = db_insert('texts', $data);
    if ( $id === NULL ) return NULL;

    $tags = array_map('load_tag', $properties['TxTags']);
    $success = add_tags_to_text($id, $tags);

    return $success ? $id : NULL;
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

    $data = array('TxID' => (int)$id,
                  'TxLgID' => (int)$properties['TxLgID'],
                  'TxTitle' => $properties['TxTitle'],
                  'TxText' => db_text_prepare($properties['TxText']),
                  'TxAudioURI' => $properties['TxAudioURI']);

    $res = db_execute("UPDATE texts SET
            TxLgID = :TxLgID, TxTitle = :TxTitle, TxText = :TxText,
            TxAudioURI = :TxAudioURI
        WHERE TxID = :TxID", $data);

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
    global $lwt_db;

    $success = delete_texts_data($ids)
        && db_execute("DELETE FROM texts
            WHERE TxID IN $sql_list")
        && db_execute("DELETE texttags
            FROM ( texttags LEFT JOIN texts ON TtTxID = TxID )
            WHERE TxID IS NULL");

    return $success;
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

    $success = db_execute("DELETE FROM textitems
            WHERE TiTxID IN $sql_list")
        && db_execute("DELETE FROM sentences
            WHERE SeTxID IN $sql_list");

    return $success;
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
    global $lwt_db;
    $ids = array_map('intval', $ids);

    $success = true;
    foreach ( $ids as $id ) {
        $success = db_execute('INSERT INTO archivedtexts
            ( AtLgID, AtTitle, AtText, AtAudioURI )
            SELECT TxLgId, TxTitle, TxText, TxAudioURI
                FROM texts
                WHERE TxID = ?', $id) && $success;

        $arch_id = get_last_key();
        $success = db_execute('INSERT INTO archtexttags ( AgAtID, AgT2ID )
            SELECT ' . $arch_id . ', TtT2ID
                FROM texttags
                WHERE TtTxId = ?', $id) && $success;
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
    $texts = db_get_rows("SELECT TxID, TxLgID, TxText
        FROM texts
        WHERE TxID IN " . $sql_list);

    foreach ( $texts as $text ) {
        splitText($text['TxText'], $text['TxLgID'], $text['TxID']);
    }
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

    $records = db_get_rows("SELECT WoId, WoTextLC, MIN(TiSeID) AS SeID
        FROM words, textitems
        WHERE TiLgID = WoLgID
            AND TiTextLC = WoTextLC
            AND TiTxID IN " . $sql_list . "
            AND IFNULL(WoSentence, '') NOT LIKE CONCAT('%{', WoText, '}%')
        GROUP BY WoID
        ORDER BY WoID, MIN(TiSeID)");

    $sentence_mode = (int)getSettingWithDefault('set-term-sentence-count');

    foreach ( $records as $record ) {
        $sentence = getSentence($record['SeID'], $record['WoTextLC'], $sentence_mode);

        db_execute("UPDATE words
            SET WoSentence = " . db_text_prepare(repl_tab_nl($sentence[1])) . "
            WHERE WoID = " . (int)$record['WoID']);
    }
}

?>