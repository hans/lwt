<?php
/**
 * Functions dealing with input sanitization.
 *
 * @package LWT
 * @subpackage Sanitization
 * @since 2.0
 */

/**
 * Run the sanitization process.
 *
 * @since 2.0
 */
function sanitize_init() {
    disable_magic_quotes();

    // Sanitize input globals.
    list($_GET, $_POST, $_COOKIE, $_SERVER) =
        sanitize($_GET, $_POST, $_COOKIE, $_SERVER);

    // $_REQUEST should only contain $_GET and $_POST.
    $_REQUEST = array_merge($_GET, $_POST);
}

/**
 * Disable magic quotes and revert any of its effects.
 *
 * @since 2.0
 */
function disable_magic_quotes() {
    if ( function_exists('set_magic_quotes_runtime') )
        set_magic_quotes_runtime(false);

    @ini_set('magic_quotes_sybase', 0);

    // If input has already been sanitized, revert its effects.
    if ( function_exists('get_magic_quotes_gpc')
         && get_magic_quotes_gpc() === 1 ) {

        list($_GET, $_POST, $_COOKIE) =
            stripslashes_deep($_GET, $_POST, $_COOKIE);
    }
}

/**
 * Sanitize strings and / or arrays of strings.
 *
 * @since 2.0
 *
 * @param mixed $var1,...
 *   Arrays and strings to sanitize
 * @return mixed List of sanitized variables (usually). If only one parameter
 *   was passed, then this single sanitized parameter will be returned.
 */
function sanitize() {
    $todo = func_get_args();

    $result = array_map(function($var) {
            if ( is_array($var) ) {
                return array_map('sanitize', $var);
            } else if ( is_string($var) ) {
                return addslashes($var);
            } else {
                return $var;
            }
        }, $todo);

    return ( count($todo) === 1
             ? reset($result)
             : $result );
}

/**
 * Strip slashes from strings or arrays / objects containing strings.
 *
 * @since 2.0
 *
 * @example
 *   list($array, $obj) = stripslashes_deep($array, $obj);
 *
 * @param mixed $var1,...
 *   Variables to strip.
 * @return array List of stripped variables.
 */
function stripslashes_deep() {
    return array_map(function($var) {

            // Dispatch based on data type
            if ( is_array($var) ) {
                return array_map('stripslashes_deep', $var);
            } else if ( is_object($var) ) {
                $obj_vars = get_object_vars($var);

                foreach ( $obj_vars as $key => $data ) {
                    $var->{$key} = stripslashes_deep($data);
                }
            } else if ( is_string($var) ) {
                return stripslashes($var);
            } else {
                return $var;
            }

        }, func_get_args());
}