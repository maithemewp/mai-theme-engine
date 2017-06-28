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
