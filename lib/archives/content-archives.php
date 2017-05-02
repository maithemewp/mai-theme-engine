<?php

/**
 * Use appropriately sized images for grid archives.
 * This is not in 'genesis_options' filter because it calls
 * genesis_site_layout() which will create an infinite loop.
 *
 * @return  void
 */
// add_filter( 'genesis_pre_get_option_image_size', 'mai_flex_entry_image_size' );
function mai_flex_entry_image_size( $size ) {

    // Bail if not a flex loop
    if ( ! mai_is_flex_loop() ) {
        return $size;
    }

    // Get image size by layout
    $image_size = mai_get_flex_entry_image_size_by_columns( mai_get_columns() );
    // Add filter so devs can easily change image size
    $image_size = apply_filters( 'mai_flex_entry_image_size', $image_size );

    // Return the image size
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
