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

// Remove the post content
remove_action( 'genesis_entry_content', 'genesis_do_post_content' );

// Add sections to the content
add_action( 'genesis_entry_content', 'mai_do_sections_loop' );
function mai_do_sections_loop() {

	// Get the sections
	$sections = get_post_meta( get_the_ID(), 'mai_sections', true );

	// Bail if no sections
	if ( ! $sections ) {
		return;
	}

	$settings = array(
		'align',
		'bg',
		'class',
		'content_width',
		'font_size',
		'height',
		'id',
		'inner',
		'overlay',
		'title',
	);

	// Loop through each section.
	foreach ( $sections as $section ) {

		// Reset args.
		$args = array();

		// Set the args.
		foreach ( $settings as $setting ) {
			$args[ $setting ] = isset( $section[ $setting ] ) ? $section[ $setting ] : '';
		}

		// Set the bg image.
		$args['image'] = isset( $section['image_id'] ) ? $section['image_id'] : '';

		// Set the content.
		$content = isset( $section['content'] ) ? $section['content'] : '';

		// Skip if no title and no content and no image.
		if ( empty( $args['title'] ) && empty( $args['image'] ) && empty( $content ) ) {
			continue;
		}

		echo mai_get_section( $content, $args );

	}

}

// Run the Genesis loop
genesis();
