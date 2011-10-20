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

?>