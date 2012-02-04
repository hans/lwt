<?php
/**
 * Tag functions.
 *
 * @package LWT
 * @since 2.0
 */

/**
 * Create a tag.
 *
 * @since 2.0
 *
 * @param array $properties
 * @return int Created tag's ID
 */
function create_tag(array $properties) {

}

/**
 * Delete a set of tags by ID.
 *
 * @since 2.0
 *
 * @param array $ids
 * @return bool success
 */
function delete_tags($ids) {
    $list = '(' . implode(',', $ids) . ')';

    $success = db_execute('DELETE FROM tags2 WHERE T2ID IN ?', $list)
        && purge_tag_data();

    return $success;
}

/**
 * Delete a single tag by ID.
 *
 * @see delete_tags
 * @since 2.0
 *
 * @param int $id
 * @return bool success
 */
function delete_tag($id) {
    return delete_tags(array($id));
}

/**
 * Purge leftover tag data (e.g., associations between texts and tags that no
 * longer exist).
 *
 * @since 2.0
 *
 * @return bool success
 */
function purge_tag_data() {
    $success = db_execute('DELETE texttags
            FROM ( texttags LEFT JOIN tags2 ON TtT2ID = T2ID )
            WHERE T2ID IS NULL')
        && db_execute('DELETE archtexttags
            FROM ( archtexttags LEFT JOIN tags2 ON AgT2ID = T2ID )
            WHERE T2ID IS NULL');

    return $success;
}

/**
 * Get a tag ID by the tag's name. If the tag doesn't exist, create it and
 * return the ID of the new object.
 *
 * @since 2.0
 *
 * @param string $name
 * @param boolean $create_if_not_exists
 *   If true, the requested tag will be created if one with the given name
 *   does not already exist.
 * @return int Tag ID
 */
function load_tag($name, $create_if_not_exists = true) {
    $id = get_first_value("SELECT T2ID AS value
        FROM tags2
        WHERE T2Text = ?", $name);

    if ( !isset($id) ) {
        if ( $create_if_not_exists ) {
            $id = db_insert('tags2', array('T2Text' => $name));
            if ( !$insert ) return false;
        } else {
            return null;
        }
    }

    return $id;
}

/**
 * Add a tag to a set of texts.
 *
 * @since 2.0
 *
 * @param int $id Tag ID
 * @param array $texts Array of text IDs
 * @return boolean Success
 */
function add_tag_to_texts($id, array $texts) {
    $id = (int)$id;

    // Build a list of SQL relations for insertion - (tag_id, text_id)
    $sql_list = join(', ', array_map(function($text_id) {
                return "($id, " . (int)$text_id . ")";
            }, $texts));

    return db_execute("INSERT INTO texttags ( TtTxID, TtT2ID )
        VALUES " . $sql_list) !== FALSE;
}

/**
 * Add a set of tags to a text.
 *
 * @since 2.0
 *
 * @param int $id Text ID
 * @param array $tags Array of tag IDs
 * @return boolean Success
 */
function add_tags_to_text($id, array $tags) {
    $id = (int)$id;

    $sql_list = join(', ', array_map(function($tag_id) use ($id) {
                return "(" . (int)$tag_id . ", $id)";
            }, $tags));

    $success = true
        && db_execute("DELETE FROM texttags
               WHERE TtTxID = ?", $id)
        && db_execute("INSERT INTO texttags ( TtTxID, TtT2ID )
               VALUES " . $sql_list);

    return $success;
}

/**
 * Remove a tag from a set of texts.
 *
 * @since 2.0
 *
 * @param int $id Tag ID
 * @param array $texts Array of text IDs
 * @return boolean Success
 */
function remove_tag_from_texts($id, $texts) {
    $id = (int)$id;
    $sql_list = '(' . join(',', array_map('intval', $texts)) . ')';

    $res = db_execute("DELETE FROM texttags
        WHERE TtT2ID = ? AND TtTxID IN " . $sql_list, $id);

    return $res !== FALSE;
}

?>