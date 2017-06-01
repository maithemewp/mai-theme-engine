<?php

/**
 * Helper function for getting the script/style `.min` suffix for minified files.
 *
 * @return string
 */
function mai_get_suffix() {
    $debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
    return $debug ? '' : '.min';
}

/**
 * Sanitize a string or array of classes.
 *
 * @param   string|array  $classes   The classes to sanitize.
 *
 * @return  string  Space-separated, sanitized classes.
 */
function mai_sanitize_html_classes( $classes ) {
    if ( ! is_array( $classes ) ) {
        $classes = explode( ' ', $classes );
    }
    return implode( ' ', array_unique( array_map( 'sanitize_html_class', array_map( 'trim', $classes ) ) ) );
}

/**
 * Sanitize a string or array of keys.
 *
 * @param   string|array  $keys   The keys to sanitize.
 *
 * @return  array  Array of sanitized keys.
 */
function mai_sanitize_keys( $keys ) {
    if ( ! is_array( $keys ) ) {
        $keys = explode( ',', $keys );
    }
    return array_map( 'sanitize_key', array_map( 'trim', array_filter($keys) ) );
}

/**
 * Kind of a gross function to run do_action in output buffering
 * and return the content of that hook.
 *
 * @param   string  $hook  The hook name to run.
 *
 * @return  string|HTML
 */
function mai_get_do_action( $hook ) {
    // Start buffer
    ob_start();
    // Add new hook
    do_action( $hook );
    // End buffer
    return ob_get_clean();
}

/**
 * Check if a string starts with another string.
 *
 * @link    http://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
 *
 * @param   string  $haystack  The string to check against.
 * @param   string  $needle    The string to check if starts with.
 *
 * @return  bool
 */
function mai_starts_with( $haystack, $needle ) {
    $length = strlen( $needle );
    return ( $needle === substr( $haystack, 0, $length ) );
}

/**
 * Check if a string ends with another string.
 *
 * @link    http://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
 *
 * @param   string  $haystack  The string to check against.
 * @param   string  $needle    The string to check if starts with.
 *
 * @return  bool
 */
function mai_ends_with( $haystack, $needle ) {
    $length = strlen($needle);
    if ( 0 == $length ) {
        return true;
    }
    return ( $needle === substr( $haystack, -$length ) );
}
