<?php

/**
 * Add checkbox to save auto-display featured image meta
 *
 * @since  1.0.0
 *
 * @return void
 */
add_filter( 'admin_post_thumbnail_html', 'mai_hide_featured_image_checkbox', 10, 3 );
function mai_hide_featured_image_checkbox( $content, $post_id, $thumbnail_id ) {

    // Don't show the field if no image
    if ( ! $thumbnail_id ) {
        return $content;
    }

    global $typenow;

	// Get the post types. This matches the output in mai_do_entry_featured_image
	$post_types = array( 'page', 'post' );

	// Remove attachments
	unset( $post_types['attachment'] );

	// Bail if not viewing a public post type
    if ( ! in_array( $typenow, $post_types ) ) {
    	return $content;
    }

    // Build our new field
	$new_field = sprintf( '<p><label for="mai_hide_featured_image"><input type="checkbox" id="mai_hide_featured_image" name="mai_hide_featured_image" %s>%s</label></p>',
        checked( get_post_meta( $post_id, 'mai_hide_featured_image', true ), true, false ),
        __( 'Hide featured image', 'mai-pro-engine' )
    );

	// Return the new field
    return $new_field . $content;

}

/**
 * Save auto-display featured image meta
 *
 * @since  1.0.0
 *
 * @param  int   	$post_id 	The post ID.
 * @param  object   $post 		The post object.
 *
 * @return void
 */
add_action( 'save_post', 'mai_save_hide_featured_image_checkbox', 10, 2 );
function mai_save_hide_featured_image_checkbox( $post_id, $post ) {

    // Bail if we don't have a value at all
    if ( ! isset( $_POST[ 'mai_hide_featured_image' ] ) ) {
        return;
    }

	// Get all public post types
	$post_types = get_post_types( array( 'public' => true ), 'names' );

	// Remove attachments
	unset( $post_types['attachment'] );

	// Bail if not saving a public post type
    if ( ! in_array( $post->post_type, $post_types ) ) {
    	return;
    }

    // Convert to 1/0
    $value = absint( filter_var( $_POST[ 'mai_hide_featured_image' ], FILTER_VALIDATE_BOOLEAN ) );

	// Save our meta field
    update_post_meta( $post_id, 'mai_hide_featured_image', $value );
}

// Add our image sizes to the media chooser
add_filter( 'image_size_names_choose', 'mai_do_media_chooser_sizes' );
function mai_do_media_chooser_sizes( $sizes ) {
    $addsizes = array(
        'featured'     => __( 'Featured'),
        'one-half'     => __( 'One Half'),
        'one-third'    => __( 'One Third'),
        'one-fourth'   => __( 'One Fourth'),
    );
    $newsizes = array_merge( $sizes, $addsizes );
    return $newsizes;
}

/**
 * Remove unsupported FlexGrid gallery options from admin
 *
 * @return void
 */
add_action( 'admin_head', 'mai_remove_unsupported_flexgrid_gallery_options' );
function mai_remove_unsupported_flexgrid_gallery_options() {
    echo '<style type="text/css">
        .gallery-settings .columns option[value="5"],
        .gallery-settings .columns option[value="7"],
        .gallery-settings .columns option[value="8"],
        .gallery-settings .columns option[value="9"] {
            display:none !important;
            visibility: hidden !important;
        }
    </style>';
}
