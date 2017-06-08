<?php

// Add custom body class to the head
add_filter( 'body_class', 'mai_sections_page_body_class' );
function mai_sections_page_body_class( $classes ) {
   $classes[] = 'mai-sections';
   return $classes;
}

// Remove breadcrumbs
remove_action( 'genesis_before_content_sidebar_wrap', 'genesis_do_breadcrumbs', 12 );

// Remove page title
remove_action('genesis_entry_header', 'genesis_do_post_title');

// Remove the loop
remove_action( 'genesis_loop', 'genesis_do_loop' );

//
add_action( 'genesis_loop', 'mai_do_sections_loop' );
function mai_do_sections_loop() {

	// Get the sections
	$sections = get_post_meta( get_the_ID(), 'mai_sections', true );

	// Bail if no sections
	if ( ! $sections ) {
		return;
	}

	global $wp_embed;

	$content = '';

	// Loop through each section
	foreach ( $sections as $section ) {

		// Reset args
		$args = array();

		// Set the args
		$args['title']			= isset( $section['title'] ) ? $section['title'] : '';
		$args['height']			= isset( $section['height'] ) ? $section['height'] : '';
		$args['content_width']	= isset( $section['content_width'] ) ? $section['content_width'] : '';
		$args['bg']				= isset( $section['bg'] ) ? $section['bg'] : '';
		$args['image']			= isset( $section['image_id'] ) ? $section['image_id'] : '';
		$args['overlay']		= isset( $section['overlay'] ) ? $section['overlay'] : '';
		$args['inner']			= isset( $section['inner'] ) ? $section['inner'] : '';

		$section_content = $section['content'];
		$section_content = $wp_embed->autoembed( $section_content );
		$section_content = $wp_embed->run_shortcode( $section_content );
		$section_content = wpautop( $section_content );
		$section_content = mai_get_clean_content( $section_content );
		$content .= mai_get_section( $args, $section_content );

	}

	echo $content;

}

// Run the Genesis loop
genesis();
