<?php

/**
 * Add body class to enabled specific settings.
 *
 * @param   array  $settings  The theme setting defaults.
 *
 * @return  array  The modified theme setting defaults.
 */
add_filter( 'genesis_theme_settings_defaults', 'mai_theme_settings_defaults' );
function mai_theme_settings_defaults( $settings ) {

	// General
	$settings['enable_sticky_header']      = 0;
	$settings['enable_shrink_header']      = 0;
	$settings['singular_image_post_types'] = array( 'post', 'page' );
	$settings['footer_widget_count']       = '2';
	$settings['mobile_menu_style']         = 'standard';

	// Banner
	$settings['enable_banner_area']        = 1;
	$settings['banner_background_color']   = '#f1f1f1';
	$settings['banner_id']                 = '';
	$settings['banner_overlay']            = '';
	$settings['banner_inner']              = '';
	$settings['banner_content_width']      = 'auto';
	$settings['banner_align_text']         = '';
	$settings['banner_featured_image']     = 0;
	$settings['banner_disable_post_types'] = array();
	$settings['banner_disable_taxonomies'] = array();

	// Archives
	$settings['columns']                   = 1;
	$settings['more_link']                 = 1;
	$settings['image_location']            = 'before_entry';
	$settings['image_alignment']           = '';
	$settings['image_size']                = 'one-third';
	$settings['remove_meta']               = array();

	// Site Layout
	$settings['single_post_layout']        = genesis_get_default_layout();

	return $settings;
}

/**
 * Add body class to enabled specific settings.
 *
 * @since   1.0.0
 *
 * @param   array  $classes  The body classes.
 *
 * @return  array  $classes  The modified classes.
 */
add_filter( 'body_class', 'mai_do_settings_body_classes' );
function mai_do_settings_body_classes( $classes ) {
	/**
	 * Add sticky header styling
	 * Fixed header currently only works with standard mobile menu
	 *
	 * DO NOT USE WITH SIDE MENU!
	 */
	if ( mai_is_sticky_header_enabled() && ! is_page_template( 'landing.php' ) ) {
		$classes[] = 'sticky-header';
	}

	if ( mai_is_shrink_header_enabled() && ! is_page_template( 'landing.php' ) ) {
		$classes[] = 'shrink-header';
	}

	/**
	 * Use a side mobile menu in place of the standard the mobile menu
	 */
	if ( mai_is_side_menu_enabled() ) {
		$classes[] = 'side-menu';
	}

	return $classes;
}
