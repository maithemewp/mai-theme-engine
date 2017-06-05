<?php

// Add singular body class to the head
add_filter( 'body_class', 'mai_singular_body_class' );
function mai_singular_body_class( $classes ) {
    if ( ! is_singular() ) {
    	return $classes;
    }
    $classes[] = 'singular';
    return $classes;
}

/**
 * Add no-js body class to the head.
 * This get's changed to "js" if JS is enabled.
 *
 * @link https://www.paulirish.com/2009/avoiding-the-fouc-v3/
 */
add_filter('body_class', 'mai_js_detection_body_class');
function mai_js_detection_body_class($classes) {
    $classes[] = 'no-js';
    return $classes;
}

/**
 * Remove the .no-js class from the html element via JS.
 * This allows styles targetting browsers without JS.
 *
 * Props Sal Ferrarello for introducing me to this solution.
 *
 * @link https://www.paulirish.com/2009/avoiding-the-fouc-v3/
 */
add_action( 'genesis_before', 'mai_js_detection_script', 1 );
function mai_js_detection_script() {
	echo "<script>(function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)</script>";
}
