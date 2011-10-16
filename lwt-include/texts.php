<?php
/**
 * Functions for operating on text entities.
 *
 * @package LWT
 * @since 2.0
 */

/**
 * Delete a set of texts.
 *
 * @since 2.0
 *
 * @param array $ids Array of text IDs
 * @return boolean Success
 */
function delete_texts(array $ids) {
    $sql_list = '(' . join(',', array_map('intval', $ids)) . ')';

    /**
     * Delete all text items and sentences associated with the text object.
     */
    $res_items = mysql_query("DELETE FROM textitems
        WHERE TiTxID IN $sql_list");

    $res_sentences = mysql_query("DELETE FROM sentences
        WHERE SeTxID IN $sql_list");

    $res_texts = mysql_query("DELETE FROM texts
        WHERE TxID IN $sql_list");

    $res_tags = mysql_query("DELETE texttags
        FROM ( texttags LEFT JOIN texts ON TtTxID = TxID )
        WHERE TxID IS NULL");

    return $res_items !== FALSE
        && $res_sentences !== FALSE
        && $res_texts !== FALSE
        && $res_tags !== FALSE;
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

?>