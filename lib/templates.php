<?php

/**
 * Add page templates.
 * Override all but sections template by copying files from /mai-theme-engine/templates/{filename}.php
 * and putting in /{child-theme-name}/templates/{filename}.php
 *
 * @since   1.0.0
 *
 * @param   array  $page_templates  The existing page templates.
 *
 * @return  array  $page_templates  The modified page templates.
 */
add_filter( 'theme_page_templates', 'mai_plugin_theme_page_templates' );
function mai_plugin_theme_page_templates( $page_templates ) {
	$page_templates['landing.php']  = __( 'Landing Page', 'mai-theme-engine' );
	$page_templates['sections.php'] = __( 'Sections', 'mai-theme-engine' );
	$page_templates['sitemap.php']  = __( 'Sitemap', 'mai-theme-engine' );
	return $page_templates;
}

/**
 * Modify page based on selected page template.
 *
 * @since   1.0.0
 *
 * @param   string  $template  The path to the template being included.
 *
 * @return  string  $template  The modified template path to be included.
 */
add_filter( 'template_include', 'mai_plugin_include_theme_page_templates' );
function mai_plugin_include_theme_page_templates( $template ) {

	/**
	 * Bail if not a single page.
	 * We don't need page templates here anyway.
	 */
	if ( ! is_singular( 'page' ) ) {
		return $template;
	}

	// Get current template.
	$template_name = get_post_meta( get_the_ID(), '_wp_page_template', true );

	// Bail if not a template from our plugin.
	if ( ! in_array( basename( $template_name ), array( 'landing.php', 'sitemap.php' ) ) ) {
		return $template;
	}

	// Get the child theme template path.
	$_template = get_stylesheet_directory() . '/templates/' . $template_name;

	// If the template exists in the child theme.
	if ( file_exists( $_template ) ) {
		// Use child theme template.
		$template = $_template;
	} else {
		// Use our plugin template.
		$plugin_path = MAI_THEME_ENGINE_PLUGIN_DIR . 'templates/';
		if ( file_exists( $plugin_path . $template_name ) ) {
			$template = $plugin_path . $template_name;
		}
	}
	return $template;
}

/**
 * Run Sections template hooks and filters.
 * This allows us to still use front-page.php and other template files
 * while still getting the benefit of the Sections layout/styling.
 *
 * @since   1.1.8
 *
 * @return  void.
 */
add_action( 'template_redirect', 'mai_do_sections_template' );
function mai_do_sections_template() {

	/**
	 * Bail if not a single post/page/cpt.
	 * We don't need page templates here anyway.
	 */
	if ( ! is_singular() ) {
		return;
	}

	// Get current template.
	$template_name = get_post_meta( get_the_ID(), '_wp_page_template', true );

	// Bail if not a Sections template.
	if ( 'sections.php' !== $template_name ) {
		return;
	}

	// Add custom body class to the head.
	add_filter( 'body_class', 'mai_sections_page_body_class' );
	function mai_sections_page_body_class( $classes ) {
		$classes[] = 'mai-sections';
		return $classes;
	}

	// Remove page title.
	remove_action('genesis_entry_header', 'genesis_do_post_title');

	// Remove the post content.
	remove_action( 'genesis_entry_content', 'genesis_do_post_content' );

	// Add sections to the content.
	add_action( 'genesis_entry_content', 'mai_do_sections_loop' );
	function mai_do_sections_loop() {

		// Get the sections.
		$sections = get_post_meta( get_the_ID(), 'mai_sections', true );

		// Bail if no sections.
		if ( ! $sections ) {
			return;
		}

		$has_banner = mai_is_banner_area_enabled();
		$has_h1     = false;

		$settings = array(
			'align',
			'bg',
			'class',
			'content_width',
			'text_size',
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

			// Use h1 for title if no banner, no h1 yet, and we have title.
			if ( ! $has_banner && ! $has_h1 && ! empty( $section['title'] ) ) {
				$args['title_wrap'] = 'h1';
				$has_h1             = true;
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

}
