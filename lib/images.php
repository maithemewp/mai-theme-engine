<?php

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

	// Get all public post types
	$post_types = get_post_types( array('public' => true ), 'names' );

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
	$new_field .= sprintf( '<label for="mai_hide_featured_image">%s</label>', __( 'Hide featured image', 'maitheme' ) );
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
	$value 	 = ( 'on' == $display ) ? true : false;

	// Save our meta field
    update_post_meta( $post_id, 'mai_hide_featured_image', $value );
}
