<?php
/**
 * Tag functions.
 *
 * @package LWT
 * @since 2.0
 */

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
    $name = mysql_real_escape_string($name);

    $id = get_first_value("SELECT T2ID AS value
        FROM tags2
        WHERE T2Text = " . $name);

    if ( !isset($id) ) {
        if ( $create_if_not_exists ) {
            $insert = mysql_query("INSERT INTO tags2 ( T2Text )
                VALUES ( '$name' )");
            if ( !$insert ) return false;

            $id = get_last_key();
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

    return mysql_query("INSERT INTO texttags ( TtTxID, TtT2ID )
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

    $sql_list = join(', ', array_map(function($tag_id) {
                return "(" . (int)$tag_id . ", $id";
            }, $tags));

    $success = true
        && mysql_query("DELETE FROM texttags
               WHERE TtTxID = " . $id)
        && mysql_query("INSERT INTO texttags ( TtTxID, TtT2ID )
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

    $res = mysql_query("DELETE FROM texttags
        WHERE TtT2ID = " . $id . " AND TtTxID IN " . $sql_list);

    return $res !== FALSE;
}

?>