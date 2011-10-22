<?php
/**
 * Functions for processing input.
 *
 * @package LWT
 * @subpackage Input
 * @since 2.0
 */

/**
 * Fetch a request parameter, with optional fallback values.
 *
 * @since 2.0
 *
 * @param string $key Request parameter key
 * @param string $fallback_type
 *   Type of fallback (if none, pass NULL).
 *   Possible values: session, request, db.
 * @param string $fallback_key optional
 *   If the request parameter is not set, fall back on the value assigned to
 *   this key in the data container corresponding to $fallback_type.
 *   To skip checking for a fallback parameter and directly return the $default
 *   parameter, pass NULL. Default: NULL.
 * @param string $default optional
 *   If neither the request key nor the fallback key are set, use this value.
 *   Defaults to NULL.
 * @param bool $is_int
 *   If this parameter is true, the fetched value will be cast as an integer
 *   before being returned. Default: FALSE.
 * @return mixed
 */
function get_parameter($key, $fallback_type = NULL, $fallback_key = NULL,
                       $default = NULL, $is_int = FALSE) {
    $result = $default;

    if ( isset($_REQUEST[$key]) ) {
        $result = stripslashes(trim($_REQUEST[$key]));
    } else if ( is_string($fallback_type) && is_string($fallback_key) ) {
        switch ( $fallback_type ) {
        case 'session':
            if ( isset($_SESSION[$fallback_key]) )
                $result = stripslashes(trim($_SESSION[$session_key]));

            break;
        case 'db':
            $db_value = getSetting($fallback_key);

            if ( $db_value )
                $result = $db_value;

            break;
        }
    }

    if ( $is_int )
        $result = (int)$result;

    return $result;
}

/**
 * Filters for different types of input data.
 *
 * @since 2.0
 *
 * @global array $input_filters
 */
$input_filters = array('language' => array('integer' => TRUE,
                                           'count_sql' => 'SELECT COUNT(LgID) AS value
                                                           FROM languages
                                                           WHERE LgID = %d'),
                       'text' => array('integer' => TRUE,
                                       'count_sql' => 'SELECT COUNT(TxID AS value
                                                       FROM texts
                                                       WHERE TxID = %d)'),
                       'tag' => array('integer' => TRUE,
                                      'count_sql' => 'SELECT ( %d IN (
                                                          SELECT TgID
                                                          FROM words, tags, wordtags
                                                          WHERE TgID = WtTgID
                                                              AND WtWoID = WoID
                                                          GROUP BY TgID
                                                          ORDER BY TgText ) )
                                                      AS value'),
                       'arch_text_tag' => array('integer' => TRUE,
                                                'count_sql' => 'SELECT ( %d IN (
                                                                    SELECT T2ID
                                                                    FROM archivedtexts, tags2, archtexttags
                                                                    WHERE T2ID = AgT2ID
                                                                        AND AgAtID = AtID
                                                                    GROUP BY T2ID
                                                                    ORDER BY T2Text ) )
                                                                AS value'),
                       'text_tag' => array('integer' => TRUE,
                                           'count_sql' => 'SELECT ( %d IN (
                                                               SELECT T2ID
                                                               FROM texts, tags2, texttags
                                                               WHERE T2ID = TtT2ID
                                                                   AND TtTxID = TxID
                                                               GROUP BY T2ID
                                                               ORDER BY T2Text ) )
                                                           AS value'));

/**
 * Filter input data.
 *
 * @since 2.0
 *
 * @param string $type Input data type.
 * @param mixed $data
 * @return mixed Filtered $data on success, or NULL on failure
 */
function filter($type, $data) {
    global $input_filters;

    if ( !isset($input_filters[$type]) )
        return NULL;

    $filter = $input_filters[$type];

    if ( isset($filter['integer']) && $filter['integer'] ) {
        if ( is_numeric($data) )
            $data = (int)$data;
        else
            return NULL;
    }

    if ( isset($filter['count_sql']) ) {
        $conn = Propel::getConnection(TextPeer::DATABASE_NAME);

        $stmt = $conn->prepare($filter['count_sql']);
        $stmt->execute(array(':id' => $data));
        $test = $stmt->fetchColumn();

        if ( $test === 0 ) return NULL;
    }

    return $data;
}

?>