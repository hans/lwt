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
 * @return int Tag ID
 */
function load_tag($name) {
    $name = mysql_real_escape_string($name);

    $id = get_first_value("SELECT T2ID AS value
        FROM tags2
        WHERE T2Text = " . $name);

    if ( !isset($id) ) {
        $insert = mysql_query("INSERT INTO tags2 ( T2Text )
            VALUES ( '$name' )");
        if ( !$insert ) return false;

        $id = get_last_key();
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
function add_tag_to_texts($id, $texts) {
    $id = (int)$id;

    // Build a list of SQL relations for insertion - (tag_id, text_id)
    $sql_list = join(', ', array_map(function($text_id) {
                return "($id, " . (int)$text_id . ")";
            }, $texts));

    return mysql_query("INSERT INTO texttags ( TtTxID, TtT2ID )
        VALUES " . $sql_list) !== FALSE;
}

?>