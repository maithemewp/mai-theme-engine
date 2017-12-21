<?php

/**
 * Add checkbox to save auto-display featured image meta
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

	// Check if auto-displaying the featured image.
	$key     = sprintf( 'singular_image_%s', $typenow );
	$display = genesis_get_option( $key );

	// Bail if not displaying.
	if ( ! $display ) {
		return $content;
	}

	// Build our new field
	$new_field = sprintf( '<p><label for="mai_hide_featured_image"><input type="checkbox" id="mai_hide_featured_image" name="mai_hide_featured_image" %s>%s</label></p>',
		checked( (bool) mai_sanitize_one_zero( get_post_meta( $post_id, 'mai_hide_featured_image', true ) ), true, false ),
		__( 'Hide featured image', 'mai-pro-engine' )
	);

	// Return the new field
	return $new_field . $content;
}

/**
 * Save auto-display featured image meta.
 *
 * @param   int     $post_id  The post ID.
 * @param   object  $post     The post object.
 *
 * @return  void
 */
add_action( 'save_post', 'mai_save_hide_featured_image_checkbox', 10, 2 );
function mai_save_hide_featured_image_checkbox( $post_id, $post ) {

	// Bail if we don't have a value at all
	if ( ! isset( $_POST[ 'mai_hide_featured_image' ] ) ) {
		$hide = get_post_meta( $post_id, 'mai_hide_featured_image', true );
		if ( $hide ) {
			delete_post_meta( $post_id, 'mai_hide_featured_image' );
		}
		return;
	}

	// Save our meta field.
	update_post_meta( $post_id, 'mai_hide_featured_image', mai_sanitize_one_zero( $_POST[ 'mai_hide_featured_image' ] ) );
}

/**
 * Add our image sizes to the media chooser.
 *
 * @param   $sizes  The size options.
 *
 * @return  array   Modified size options.
 */
add_filter( 'image_size_names_choose', 'mai_do_media_chooser_sizes' );
function mai_do_media_chooser_sizes( $sizes ) {
	$addsizes = array(
		'featured'   => __( 'Featured', 'mai-pro-engine' ),
		'one-half'   => __( 'One Half', 'mai-pro-engine' ),
		'one-third'  => __( 'One Third', 'mai-pro-engine' ),
		'one-fourth' => __( 'One Fourth', 'mai-pro-engine' ),
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
