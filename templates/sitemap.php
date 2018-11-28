<?php

// Add custom body class to the head.
add_filter( 'body_class', 'mai_sitemap_body_class' );
function mai_sitemap_body_class( $classes ) {
	$classes[] = 'sitemap';
	return $classes;
}

/**
 * Show a list of all public post types, including category, author, and montly archives.
 * A lost of code taken from genesis_get_sitemap() function.
 *
 * @since    1.0.0
 * @updated  1.6.0  A big rebuild based on genesis_get_sitemap().
 *
 * @return   void
 */
add_action( 'genesis_entry_content', 'mai_sitemap_template_content' );
function mai_sitemap_template_content() {

	$sitemap = '';

	/**
	 * Filter the sitemap before the default sitemap is built.
	 *
	 * @since 2.5.0 (Genesis)
	 *
	 * @param null $sitemap Null value. Change to something else to have that be returned.
	 */
	$pre = apply_filters( 'genesis_pre_get_sitemap', null );
	if ( null !== $pre ) {
		return wp_kses_post( $pre );
	}

	// Get public post types.
	$post_types = get_post_types( array(
		'public' => true,
	), 'objects' );

	// Filter for devs to add or remove specific post types.
	$post_types = apply_filters( 'mai_sitemap_post_types', $post_types );

	// Bail if no post types. Unlikely.
	if ( ! $post_types ) {
		return $sitemap;
	}

	$number  = 100;
	$heading = 'h2';

	// Loop through the posts.
	foreach ( $post_types as $post_type ) {

		$list = wp_get_archives( array(
			'post_type' => $post_type->name,
			'type'      => 'postbypost',
			'limit'     => $number,
			'echo'      => false,
		) );

		// Skip if no posts.
		if ( ! $list ) {
			continue;
		}

		// Add the posts to the sitemap variable.
		$sitemap .= sprintf( '<%2$s>%1$s</%2$s>', $post_type->label, $heading );
		$sitemap .= sprintf( '<ul>%s</ul>', $list );
	}

	$post_counts = wp_count_posts();
	if ( $post_counts->publish > 0 ) {
		$sitemap .= sprintf( '<%2$s>%1$s</%2$s>', __( 'Categories:', 'genesis' ), $heading );
		$sitemap .= sprintf( '<ul>%s</ul>', wp_list_categories( array(
			'number'      => $number,
			'sort_column' => 'name',
			'title_li'    => '',
			'echo'        => false,
		) ) );
		$sitemap .= sprintf( '<%2$s>%1$s</%2$s>', __( 'Authors:', 'genesis' ), $heading );
		$sitemap .= sprintf( '<ul>%s</ul>', wp_list_authors( array(
			'number'        => $number,
			'exclude_admin' => false,
			'optioncount'   => true,
			'echo'          => false,
		) ) );
		$sitemap .= sprintf( '<%2$s>%1$s</%2$s>', __( 'Monthly:', 'genesis' ), $heading );
		$sitemap .= sprintf( '<ul>%s</ul>', wp_get_archives( array(
			'number' => $number,
			'type'   => 'monthly',
			'echo'   => false,
		) ) );
	}

	/**
	 * Filter the sitemap.
	 *
	 * @since 2.2.0 (Genesis)
	 *
	 * @param string $sitemap Default sitemap.
	 */
	$sitemap = apply_filters( 'genesis_sitemap_output', $sitemap );

	echo wp_kses_post( $sitemap );
}

// Run the Genesis loop.
genesis();
