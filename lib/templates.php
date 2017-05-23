<?php

/**
 * Add page templates.
 * Override by copying files from /mai-pro-engine/templates/{filename}.php
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
	$page_templates['landing.php'] = __( 'Landing Page', 'mai-pro' );
	$page_templates['sitemap.php'] = __( 'Sitemap', 'mai-pro' );
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

	// Get current template
	$template_name = get_post_meta( get_the_ID(), '_wp_page_template', true );

	// Bail if not a template from our plugin
	if ( ! in_array( $template_name, array( 'landing.php', 'sitemap.php' ) ) ) {
		return $template;
	}

	// Get the child theme template path
	$_template = get_stylesheet_directory() . '/templates/' . $template_name;

	// If the template exists in the child theme
	if ( file_exists( $_template ) ) {
		// Use child theme template
		$template = $_template;
	} else {
		// Use our plugin template
		$plugin_path = MAI_PRO_ENGINE_PLUGIN_DIR . 'templates/';
		if ( file_exists( $plugin_path . $template_name ) ) {
			$template = $plugin_path . $template_name;
		}
	}
	return $template;
}

/**
 * Add default archive settings for search results.
 *
 * @return  void
 */
add_action( 'genesis_meta', 'mai_search_results_template' );
function mai_search_results_template() {

	// Bail if not search results
	if ( ! is_search() ) {
		return;
	}

	// Layout (medium content)
 	add_filter( 'genesis_pre_get_option_site_layout', '__mai_return_md_content' );

 	// Columns
	add_filter( 'mai_pre_get_archive_setting_columns', function( $columns ) {
		return 3;
	});

	// Content Archive
	add_filter( 'mai_pre_get_archive_setting_content_archive', function( $archive ) {
		return 'full';
	});

	// Content Limit
	add_filter( 'mai_pre_get_archive_setting_content_archive_limit', function( $limit ) {
		return 140;
	});

	// More Link
	add_filter( 'mai_pre_get_archive_setting_more_link', function( $more_link ) {
		return 1;
	});

}
