<?php

// Add featured image to single posts
add_action( 'genesis_before_entry', 'mai_do_entry_featured_image' );
function mai_do_entry_featured_image() {

	// Bail if not a single post.
	if ( ! is_singular() ) {
		return;
	}

	// Get post types to display featured image on.
	$post_types = genesis_get_option( 'singular_image_post_types' );

	// Bail if no post types.
	if ( ! $post_types ) {
		return;
	}

	// Bail if not a post type we want to display image on, or if there is no featured image.
	if ( ! ( is_singular( $post_types ) && has_post_thumbnail() ) ) {
		return;
	}

	// Bail if hide featured image is checked.
	if ( get_post_meta( get_the_ID(), 'mai_hide_featured_image', true ) ) {
		return;
	}

	echo '<div class="featured-image">';
		echo genesis_get_image( array(
			'format' => 'html',
			'size'   => 'featured',
			'attr'   => array( 'class' => 'wp-post-image' )
			));
	echo '</div>';

	$caption = get_post(get_post_thumbnail_id())->post_excerpt;
	if ( $caption ) {
		echo '<span class="image-caption">' . $caption . '</span>';
	}

}
