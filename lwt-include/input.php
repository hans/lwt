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
 * @param string $session_key optional
 *   If the request parameter is not set, fall back on the value in $_SESSION
 *   assigned to this key. To skip checking for a session parameter and
 *   directly return the $default parameter, pass NULL. Default: NULL.
 * @param string $default optional
 *   If neither the request key nor the session key are set, use this value.
 *   Defaults to NULL.
 * @param bool $is_int
 *   If this parameter is true, the fetched value will be cast as an integer
 *   before being returned. Default: FALSE.
 * @return mixed
 */
function get_parameter($key, $session_key = NULL, $default = NULL, $is_int = FALSE) {
    $result = $default;

    if ( isset($_REQUEST[$key]) ) {
        $result = stripslashes(trim($_REQUEST[$key]));
    } else if ( !is_null($session_key) && isset($_SESSION[$session_key]) ) {
        $result = stripslashes(trim($_SESSION[$session_key]));
    }

    if ( $is_int )
        $result = (int)$result;

    return $result;
}

?>