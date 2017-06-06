<?php

// Add featured image to single posts
add_action( 'genesis_before_entry', 'mai_do_entry_featured_image' );
function mai_do_entry_featured_image() {

    // Bail if auto display is not enabled
    if ( ! mai_is_display_featured_image_enabled() ) {
        return;
    }

    // Post types to display featured image on
    $post_types = array( 'page', 'post' );

    // Bail if not a post type we want to display image on, or if there is no featured image
    if ( ! ( is_singular( $post_types ) && has_post_thumbnail() ) ) {
        return;
    }

    // Bail if hide featured image is checked
    if ( get_post_meta( get_the_ID(), 'mai_hide_featured_image', true ) ) {
        return;
    }

    echo '<div class="featured-image">';
        echo genesis_get_image( array(
                'format' => 'html',
                'size'   => 'featured',
                'attr'   => array(
                    'class' => 'wp-post-image',
                )
            ));
	echo '</div>';

	$caption = get_post(get_post_thumbnail_id())->post_excerpt;
	if ( $caption ) {
		echo '<span class="image-caption">' . $caption . '</span>';
	}

}

/**
 * Add checkbox to save auto-display featured image meta
 *
 * @since  1.0.0
 *
 * @return void
 */
add_filter( 'admin_post_thumbnail_html', 'mai_hide_featured_image_checkbox');
function mai_hide_featured_image_checkbox( $featured_image_field ) {
    global $typenow;

	// Get the post types. This matches the output in mai_do_entry_featured_image
	$post_types = array( 'page', 'post' );

	// Remove attachments
	unset( $post_types['attachment'] );

	// Bail if not viewing a public post type
    if ( ! in_array( $typenow, $post_types ) ) {
    	return $featured_image_field;
    }

    // Build our new field
	$new_field = '';
	$new_field .= '<p>';
	$new_field .= sprintf( '<input type="checkbox" id="mai_hide_featured_image" name="mai_hide_featured_image" %s>', checked( get_post_meta( get_the_ID(), 'mai_hide_featured_image', true ), true, false ) );
	$new_field .= sprintf( '<label for="mai_hide_featured_image">%s</label>', __( 'Hide featured image', 'mai-pro-engine' ) );
	$new_field .= '</p>';

	// Return the new field
	return $new_field . $featured_image_field;
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

	// Get all public post types
	$post_types = get_post_types( array('public' => true ), 'names' );

	// Remove attachments
	unset( $post_types['attachment'] );

	// Bail if not saving a public post type
    if ( ! in_array( $post->post_type, $post_types ) ) {
    	return;
    }

	$display = isset($_POST[ 'mai_hide_featured_image' ]) ? $_POST[ 'mai_hide_featured_image' ] : false;
    // Convert to 1/0
    $value   = absint( filter_var( $display, FILTER_VALIDATE_BOOLEAN ) );

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
