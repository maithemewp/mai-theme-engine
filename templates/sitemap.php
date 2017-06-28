<?php

// Add custom body class to the head
add_filter( 'body_class', 'mai_sitemap_body_class' );
function mai_sitemap_body_class( $classes ) {
   $classes[] = 'sitemap';
   return $classes;
}

// Filter the default sitemap items and add any publically registered post types to the variable
add_filter( 'genesis_sitemap_output', 'mai_genesis_sitemap_output' );
function mai_genesis_sitemap_output( $sitemap ) {

	// Get public custom post types
	$post_types = get_post_types( array(
		'public'   => true,
	), 'objects' );

	// Remove pages and posts as they are added already in genesis_sitemap_output
	unset( $post_types['page'] );
	unset( $post_types['post'] );

	// Bail if none
	if ( ! $post_types ) {
		return $sitemap;
	}

	// Loop through the posts
	foreach ( $post_types as $post_type ) {
		$list = wp_get_archives( 'post_type=' . $post_type->name . '&type=postbypost&limit=100&echo=0' );
		// Skip if no posts
		if ( ! $list ) {
			continue;
		}
    	$heading = ( genesis_a11y( 'headings' ) ? 'h2' : 'h4' );
    	// Add the posts to the sitemap variable
		$sitemap .= sprintf( '<%2$s>%1$s</%2$s>', $post_type->label, $heading );
		$sitemap .= sprintf( '<ul>%s</ul>', $list );
	}
	return $sitemap;
}

/**
 * This function outputs sitemap-esque columns displaying all pages,
 * categories, authors, monthly archives, and recent posts.
 *
 * @uses 	genesis_sitemap() to generate the sitemap.
 *
 * @return  void
 */
add_action( 'genesis_entry_content', 'mai_sitemap_template_content' );
function mai_sitemap_template_content() {
	genesis_sitemap( );
}

genesis();
