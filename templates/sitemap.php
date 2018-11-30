<?php

// Add custom body class to the head.
add_filter( 'body_class', 'mai_sitemap_body_class' );
function mai_sitemap_body_class( $classes ) {
	$classes[] = 'sitemap';
	return $classes;
}

/**
 * Display the default sitemap.
 * A filter is run inside genesis_sitemap() that adds CPT's to the output as well.
 *
 * @uses    genesis_sitemap() to generate the sitemap.
 * @uses    genesis_sitemap_output() filter in Mai Theme.
 *
 * @return  void
 */
add_action( 'genesis_entry_content', 'mai_sitemap_template_content' );
function mai_sitemap_template_content() {
	genesis_sitemap();
}

// Run the Genesis loop.
genesis();
