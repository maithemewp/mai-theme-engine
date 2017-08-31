<?php

add_action( 'genesis_before', function() {
	// d( genesis_get_option( 'layout_archive' ) );
	// d( genesis_get_cpt_option( 'cpt_archive_layouts_break' ) );
	// $post_types = genesis_get_cpt_archive_types();
	// d( $post_types );
	// delete_option( 'genesis-settings' );
	// delete_option( GENESIS_CPT_ARCHIVE_SETTINGS_FIELD_PREFIX . 'portfolio' );
	// wp_cache_delete( GENESIS_CPT_ARCHIVE_SETTINGS_FIELD_PREFIX . 'portfolio', 'options' );
	// d( get_option( GENESIS_CPT_ARCHIVE_SETTINGS_FIELD_PREFIX . 'portfolio' ) );
	// get_option( GENESIS_CPT_ARCHIVE_SETTINGS_FIELD_PREFIX . 'portfolio' );
	// get_option( GENESIS_CPT_ARCHIVE_SETTINGS_FIELD_PREFIX . 'portfolio' );
	// d( get_option( 'genesis-settings' ) );
	// d( genesis_get_option( 'columns' ) );
	// $option = genesis_get_cpt_option( 'archives_featured_image_heading' );
	// d( isset( $option ) );
	// d( get_option( 'disable_banner_customizer_heading' ) );
	// d( genesis_get_cpt_option( 'posts_per_page' ) );
	// d( genesis_get_option( 'posts_per_page' ) );
});


add_filter( 'genesis_options', 'mai_genesis_options_defaults', 10, 2 );
function mai_genesis_options_defaults( $options, $setting ) {

	if ( GENESIS_SETTINGS_FIELD !== $setting ) {
		return $options;
	}

	// Default options.
	$all_options = mai_get_default_options();

	// New installs.
	// if ( ! $options ) {
		// return apply_filters( 'genesis_theme_settings_defaults', $all_options );
		// return $all_options;
	// }
	// Existing installs, make sure new mai options exist.
	// else {
		foreach ( $all_options as $key => $value ) {
			if ( ! isset( $options[$key] ) ) {
				$options[$key] = $value;
			}
		}
	// }
	// Return the modified options.
	// return apply_filters( 'genesis_theme_settings_defaults', $options, $setting );
	return $options;
}

function mai_get_default_option( $key ) {
	$options = mai_get_default_options();
	return $options[$key];
}

function mai_get_default_options() {
	$defaults = array(
		// Genesis core.
		'update'                    => 1,
		'update_email'              => 0,
		'update_email_address'      => '',
		'blog_title'                => 'text',
		'style_selection'           => '',
		'site_layout'               => genesis_get_default_layout(),
		'superfish'                 => 0,
		'nav_extras'                => '',
		'nav_extras_twitter_id'     => '',
		'nav_extras_twitter_text'   => __( 'Follow me on Twitter', 'genesis' ),
		'feed_uri'                  => '',
		'redirect_feed'             => 0,
		'comments_feed_uri'         => '',
		'redirect_comments_feed'    => 0,
		'comments_pages'            => 0,
		'comments_posts'            => 1,
		'trackbacks_pages'          => 0,
		'trackbacks_posts'          => 1,
		'breadcrumb_home'           => 0,
		'breadcrumb_front_page'     => 0,
		'breadcrumb_posts_page'     => 0,
		'breadcrumb_single'         => 0,
		'breadcrumb_page'           => 0,
		'breadcrumb_archive'        => 0,
		'breadcrumb_404'            => 0,
		'breadcrumb_attachment'     => 0,
		'content_archive'           => 'full',
		'content_archive_limit'     => 0,
		'content_archive_thumbnail' => 0,
		'image_size'                => 'one-third',
		'image_alignment'           => 'alignleft',
		'posts_nav'                 => 'numeric',
		'blog_cat'                  => '',
		'blog_cat_exclude'          => '',
		'blog_cat_num'              => 10,
		'header_scripts'            => '',
		'footer_scripts'            => '',
		// 'theme_version'             => PARENT_THEME_VERSION,
		// 'db_version'                => PARENT_DB_VERSION,
		// 'first_version'             => genesis_first_version(),
		// Mai General.
		'enable_sticky_header'      => 0,
		'enable_shrink_header'      => 0,
		'singular_image_page'       => 1,
		'singular_image_post'       => 1,
		'footer_widget_count'       => 2,
		'mobile_menu_style'         => 'standard',
		// Mai Banner.
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
		// Mai Archives.
		'columns'                   => 1,
		'image_location'            => 'before_title',
		'more_link'                 => 0,
		'more_link_text'            => '',
		'remove_meta'               => array(),
		'posts_per_page'            => get_option( 'posts_per_page' ),
		// Mai Singular.
		'singular_image_page'       => 1,
		'singular_image_post'       => 1,
		'remove_meta_post'          => array(),
		// Mai Layouts.
		'layout_page'               => '',
		'layout_post'               => '',
		'layout_archive'            => 'full-width-content',
	);
	return apply_filters( 'genesis_theme_settings_defaults', $defaults );
}

function mai_get_default_cpt_option( $key, $post_type ) {
	if ( ! $post_type ) {
		$post_type = get_post_type();
	}
	$options = get_default_cpt_options( $post_type );
	return $options[$key];
}

function get_default_cpt_options( $post_type ) {
	$layout_singular_key    = sprintf( 'layout_%s', $post_type );
	$singular_image_key     = sprintf( 'singular_image_%s', $post_type );
	$remove_meta_single_key = sprintf( 'remove_meta_%s', $post_type );
	return array(
		$layout_singular_key              => mai_get_default_option( 'layout_post' ),         // (saves to 'genesis-settings' option)
		'layout'                          => mai_get_default_option( 'layout_archive' ),
		$singular_image_key               => mai_get_default_option( 'singular_image_post' ), // (saves to 'genesis-settings' option)
		$remove_meta_single_key           => mai_get_default_option( 'remove_meta_post' ),    // (saves to 'genesis-settings' option)
		'enable_content_archive_settings' => 0,
		'columns'                         => mai_get_default_option( 'columns' ),
		'content_archive'                 => mai_get_default_option( 'content_archive' ),
		'content_archive_limit'           => mai_get_default_option( 'content_archive_limit' ),
		'content_archive_thumbnail'       => mai_get_default_option( 'content_archive_thumbnail' ),
		'image_location'                  => mai_get_default_option( 'image_location' ),
		'image_size'                      => mai_get_default_option( 'image_size' ),
		'image_alignment'                 => mai_get_default_option( 'image_alignment' ),
		'more_link'                       => mai_get_default_option( 'more_link' ),
		'more_link_text'                  => mai_get_default_option( 'more_link_text' ),
		'remove_meta'                     => mai_get_default_option( 'remove_meta' ),
		'posts_per_page'                  => mai_get_default_option( 'posts_per_page' ),
		'posts_nav'                       => mai_get_default_option( 'posts_nav' ),
	);
}

/**
 * Add new custom genesis option filter.
 *
 * @param   array  $default_filters  The filters. The key is the name and value is the callback function/method.
 *
 * @return  array  The modified filters.
 */
// add_filter( 'genesis_available_sanitizer_filters', 'mai_available_sanitizer_filters' );
function mai_available_sanitizer_filters( $default_filters ) {
	$default_filters['array_no_html'] = 'mai_array_no_html';
	return $default_filters;
}
function mai_array_no_html( array $new_val, $old_val ) {
	return array_map( 'strip_tags', $new_val );
}

/**
 * THESE BREAK THINGS WHEN SAVING IN THE CUSTOMIZER. Particularly banner_id/image fields.
 *
 * Register each of the settings with a sanitization filter type.
 *
 * @return  void
 */
// add_action( 'genesis_settings_sanitizer_init', 'mai_genesis_sanitizer_filters' );
function mai_genesis_sanitizer_filters() {

	genesis_add_option_filter(
		'one_zero',
		GENESIS_SETTINGS_FIELD,
		array(
			'enable_sticky_header',
			'enable_shrink_header',
			'singular_image_page',
			'singular_image_post',
			'enable_banner_area',
			'banner_featured_image',
			'more_link',
			'more_link_text',
		)
	);

	genesis_add_option_filter(
		'no_html',
		GENESIS_SETTINGS_FIELD,
		array(
			'mobile_menu_style',
			'banner_background_color',
			'banner_overlay',
			'banner_inner',
			'banner_content_width',
			'banner_align_text',
			'image_location',
			'layout_page',
			'layout_post',
			'layout_archive',
		)
	);

	genesis_add_option_filter(
		'absint',
		GENESIS_SETTINGS_FIELD,
		array(
			'footer_widget_count',
			'banner_id',
			'columns',
		)
	);

	genesis_add_option_filter(
		'array_no_html',
		GENESIS_SETTINGS_FIELD,
		array(
			'banner_disable_post_types',
			'banner_disable_taxonomies',
			'remove_meta_post',
		)
	);

	/**
	 * Get post types.
	 * Applies apply_filters( 'genesis_cpt_archives_args', $args ); filter.
	 */
	$post_types = genesis_get_cpt_archive_types();

	if ( ! $post_types ) {
		return;
	}

	/**
	 * 'layout_{post_type}'          (saves to 'genesis-settings' option)
	 * 'layout'
	 * 'singular_image_{post_type}'  (saves to 'genesis-settings' option)
	 * 'remove_meta_{post_type}'     (saves to 'genesis-settings' option)
	 * 'enable_content_archive_settings'
	 * 'columns'
	 * 'content_archive'
	 * 'content_archive_limit'
	 * 'content_archive_thumbnail'
	 * 'image_location'
	 * 'image_size'
	 * 'image_alignment'
	 * 'more_link'
	 * 'more_link_text'
	 * 'remove_meta'
	 * 'posts_per_page'
	 * 'posts_nav'
	 */
	foreach( $post_types as $post_type => $object ) {

		/* ********************** *
		 * GENESIS_SETTINGS_FIELD *
		 * ********************** */

		genesis_add_option_filter(
			'one_zero',
			GENESIS_SETTINGS_FIELD,
			array(
				sprintf( 'singular_image_%s', $post_type ),
			)
		);

		genesis_add_option_filter(
			'no_html',
			GENESIS_SETTINGS_FIELD,
			array(
				sprintf( 'layout_%s', $post_type ),
			)
		);

		genesis_add_option_filter(
			'array_no_html',
			GENESIS_SETTINGS_FIELD,
			array(
				sprintf( 'remove_meta_%s', $post_type ),
			)
		);

		/* ****************************************************** *
		 * GENESIS_CPT_ARCHIVE_SETTINGS_FIELD_PREFIX . $post_type *
		 * ****************************************************** */

		$CPT_FIELD = GENESIS_CPT_ARCHIVE_SETTINGS_FIELD_PREFIX . $post_type;

		genesis_add_option_filter(
			'one_zero',
			$CPT_FIELD,
			array(
				'enable_content_archive_settings',
				'more_link',
				'content_archive_thumbnail',
			)
		);

		genesis_add_option_filter(
			'no_html',
			$CPT_FIELD,
			array(
				'layout',
				'posts_nav',
				'content_archive',
				'more_link_text',
				'image_location',
				'image_size',
				'image_alignment',
			)
		);

		genesis_add_option_filter(
			'absint',
			$CPT_FIELD,
			array(
				'columns',
				'posts_per_page',
				'content_archive_limit',
			)
		);

		genesis_add_option_filter(
			'array_no_html',
			$CPT_FIELD,
			array(
				'remove_meta',
			)
		);

	}

}



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
