<?php

// Add custom body class to the head
add_filter( 'body_class', 'mai_global_body_class' );
function mai_global_body_class( $classes ) {
    $classes[] = 'no-js';
    if ( is_singular() ) {
        $classes[] = 'singular';
    }
    return $classes;
}
