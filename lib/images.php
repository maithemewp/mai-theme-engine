<?php

// Add featured image to single posts
add_action( 'genesis_before_entry', 'mai_do_entry_featured_image' );
function mai_do_entry_featured_image() {

	// Bail if not a single entry with a featured image.
	if ( ! ( is_singular() && has_post_thumbnail() ) ) {
		return;
	}

	// Get post types to display featured image on.
	$key     = sprintf( 'singular_image_%s', get_post_type() );
	$display = genesis_get_option( $key );

	// Bail if not displaying.
	if ( ! $display ) {
		return;
	}

	// Bail if hide featured image is checked.
	if ( get_post_meta( get_the_ID(), 'mai_hide_featured_image', true ) ) {
		return;
	}

	mai_do_featured_image();

}

function mai_do_featured_image( $size = 'featured' ) {
	echo '<div class="featured-image">';
		echo genesis_get_image( array(
			'format' => 'html',
			'size'   => $size,
			'attr'   => array( 'class' => 'wp-post-image' )
			));
	echo '</div>';

	$caption = get_post( get_post_thumbnail_id() )->post_excerpt;
	if ( $caption ) {
		echo '<span class="image-caption">' . $caption . '</span>';
	}
}
