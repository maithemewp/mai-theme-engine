<?php

add_action( 'genesis_before', function() {
	// delete_option( 'genesis-settings' );
	// d( get_option( 'genesis-settings' ) );
	// d( get_option( 'disable_banner_customizer_heading' ) );
	// d( get_option( GENESIS_CPT_ARCHIVE_SETTINGS_FIELD_PREFIX . 'portfolio' ) );
	// d( genesis_get_cpt_option( 'posts_per_page' ) );
	// d( genesis_get_option( 'posts_per_page' ) );
});


add_filter( 'genesis_options', function( $options, $setting ) {

	if ( GENESIS_SETTINGS_FIELD !== $setting ) {
		return $options;
	}

	// Genesis default options.
	$genesis_options = array(
		'site_layout'               => 'md-content',
		'content_archive'           => 'excerpt',
		'content_archive_thumbnail' => 1,
		'image_size'                => 'one-third',
		'image_alignment'           => '',
		'posts_nav'                 => 'numeric',
	);

	// Mai custom options.
	$mai_options = array(
		// General
		'enable_sticky_header'      => 0,
		'enable_shrink_header'      => 0,
		'singular_image_post_types' => array( 'post', 'page' ),
		'footer_widget_count'       => '2',
		'mobile_menu_style'         => 'standard',
		// Banner
		'enable_banner_area'        => 1,
		'banner_background_color'   => '#f1f1f1',
		'banner_id'                 => '',
		'banner_overlay'            => '',
		'banner_inner'              => '',
		'banner_content_width'      => 'auto',
		'banner_align_text'         => '',
		'banner_featured_image'     => 0,
		'banner_disable_post_types' => array(),
		'banner_disable_taxonomies' => array(),
		// Archives
		'columns'                   => 3,
		'more_link'                 => 1,
		'image_location'            => 'before_entry',
		'remove_meta'               => array(),
		// Layouts
		'layout_page'               => '',
		'layout_post'               => '',
		'layout_archive'            => '',
	);

	// All the options.
	$all_options = array_merge( $genesis_options, $mai_options );

	// New installs.
	if ( ! $options ) {
		return apply_filters( 'genesis_theme_settings_defaults', $all_options );
	}
	// Existing installs, make sure new mai options exist.
	else {
		foreach ( $mai_options as $key => $value ) {
			if ( ! isset( $options[$key] ) ) {
				$options[$key] = $value;
			}
		}
	}
	// Return the modified options.
	return apply_filters( 'genesis_theme_settings_defaults', $options, $setting );

}, 10, 2 );





/**
 * Add body class to enabled specific settings.
 *
 * @param   array  $settings  The theme setting defaults.
 *
 * @return  array  The modified theme setting defaults.
 */
// add_filter( 'genesis_theme_settings_defaults', 'mai_theme_settings_defaults' );
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
	$settings['columns']                   = 3;
	$settings['more_link']                 = 1;
	$settings['image_location']            = 'before_entry';
	$settings['image_alignment']           = '';
	$settings['image_size']                = 'one-third';
	$settings['remove_meta']               = array();

	// Layouts
	$settings['layout_page']               = '';
	$settings['layout_post']               = '';
	$settings['layout_archive']            = '';

	return $settings;
}

/**
 * Filter CPT defaults.
 *
 * @param   array  $settings  The theme setting defaults.
 *
 * @return  array  The modified theme setting defaults.
 */
// add_filter( 'genesis_cpt_archive_settings_defaults', 'mai_cpt_archive_settings_defaults' );
function mai_cpt_archive_settings_defaults( $settings ) {
	$settings['layout']                    = genesis_get_default_layout();
	$settings['columns']                   = 3;
	$settings['content_archive']           = 'full';
	$settings['content_archive_limit']     = 0;
	$settings['more_link']                 = 1;
	$settings['more_link_text']            = '';
	$settings['content_archive_thumbnail'] = '';
	$settings['image_location']            = '';
	$settings['image_size']                = '';
	$settings['image_alignment']           = '';
	$settings['remove_meta']               = '';
	$settings['posts_per_page']            = '';
	$settings['posts_nav']                 = '';
	return $settings;
}
