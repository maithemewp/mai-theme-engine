<?php

// Flex loop opening html
add_action( 'genesis_before_while', 'mai_do_flex_loop_open', 100 );
function mai_do_flex_loop_open() {
    // Bail if not a flex loop
    if ( ! mai_is_flex_loop() ) {
        return;
    }
    $attributes['class'] = 'row gutter-30';
    printf( '<div %s>', genesis_attr( 'flex-row', $attributes ) );
}

// Flex loop closing html
add_action( 'genesis_after_endwhile', 'mai_do_flex_loop_close' );
function mai_do_flex_loop_close() {
    // Bail if not a flex loop
    if ( ! mai_is_flex_loop() ) {
        return;
    }
    echo '</div>';
}

// Filter the post classes
add_filter( 'post_class', 'mai_add_flex_entry_post_classes' );
function mai_add_flex_entry_post_classes( $classes ) {
    // Bail if not the main query
    global $wp_query;
    if ( ! $wp_query->is_main_query() ) {
        return $classes;
    }

    // Bail if not a flex loop
    if ( ! mai_is_flex_loop() ) {
        return $classes;
    }

    // Get the classes by layout
    $flex_classes = mai_get_flex_entry_classes_by_columns( mai_get_columns() );
    // Add filter so devs can change these classes easily
    $flex_classes = apply_filters( 'mai_flex_entry_classes', $flex_classes );
    // Add the classes to the post array
    $classes[]    = $flex_classes;

    // Return the classes
    return $classes;
}
