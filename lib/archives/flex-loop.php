<?php

// Flex loop opening html
add_action( 'genesis_before_while', 'mai_do_flex_loop_open', 100 );
function mai_do_flex_loop_open() {
    // Bail if not a flex loop
    if ( ! mai_is_flex_loop_layout() ) {
        return;
    }
    $attributes['class'] = 'row gutter-30';
    printf( '<div %s>', genesis_attr( 'flex-row', $attributes ) );
}

// Flex loop closing html
add_action( 'genesis_after_endwhile', 'mai_do_flex_loop_close' );
function mai_do_flex_loop_close() {
    // Bail if not a flex loop
    if ( ! mai_is_flex_loop_layout() ) {
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
    $layout = genesis_site_layout();
    // Bail if not a flex loop
    if ( ! mai_is_flex_loop_layout( $layout ) ) {
        return $classes;
    }
    // Get the classes by layout
    $flex_classes = mai_get_flex_entry_classes_by( 'layout', $layout );
    // Add filter so devs can change these classes easily
    $flex_classes = apply_filters( 'mai_flex_entry_classes', $flex_classes );
    // Add the classes to the post array
    $classes[]    = $flex_classes;
    return $classes;
}

/**
 * Use appropriately sized images for grid archives.
 * This is not in 'genesis_options' filter because it calls
 * genesis_site_layout() which will create an infinite loop.
 *
 * @return  void
 */
add_filter( 'genesis_pre_get_option_image_size', 'mai_flex_entry_image_size' );
function mai_flex_entry_image_size( $size ) {

    $layout = genesis_site_layout();

    // Bail if not a grid archive
    if ( ! mai_is_flex_loop_layout( $layout ) ) {
        return $size;
    }
    // Get image size by layout
    $image_size = mai_get_flex_entry_image_size_by_layout( $layout );
    // Add filter so devs can easily change image size
    $image_size = apply_filters( 'mai_flex_entry_image_size', $image_size );
    return $image_size;
}

/**
 * Change grid archive options
 * To override image size or content_archive (or others) in your template
 * use the 'genesis_options' filter with a priority over 10
 *
 * add_filter( 'genesis_options', function( $args ) {
 *     $args['content_archive']       = 'full';
 *     $args['content_archive_limit'] = '200';
 *     return $args;
 * });
 *
 * @param   array  $args  Full array of Genesis options
 *
 * @return  array  The altered array
 */
// add_filter( 'genesis_options', 'mai_flex_loop_genesis_options' );
function mai_flex_loop_genesis_options( $args ) {
    $args['content_archive_thumbnail'] = true;
    $args['image_alignment']           = 'none';
    return $args;
}
