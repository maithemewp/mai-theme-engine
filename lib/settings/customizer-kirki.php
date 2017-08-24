<?php

/**
 * Register Customizer control for general settings.
 *
 * @param   $wp_customize  The customizer object.
 *
 * @return  void
 */
// add_action( 'customize_register', 'mai_customizer_settings', 20 );
function mai_customizer_settings( $wp_customize ) {

	// Bail if Kirki isn't running.
	// if ( ! class_exists( 'Kirki' ) ) {
	// 	return;
	// }

	// Remove Genesis 'Content Archives' section
	$wp_customize->remove_section( 'genesis_archives' );

	// Remove Genesis 'Site Layout' section
	$wp_customize->remove_section( 'genesis_layout' );

// Multi-check field
// http://wpsites.org/multiple-checkbox-customizer-control-10868/

	$section = 'mai-settings';
	/*****************************************************
	 * Mai Theme Settings section
	 */
	$wp_customize->add_section( $section, array(
		'description' => __( 'Customize your theme with sitewide changes.', 'mai-pro-engine' ),
		'title'       => __( 'Mai Settings', 'mai-pro-engine' ),
		'priority'    => 35,
	) );
	/*****************************************************
	 * Enable Sticky Header setting
	 */
	$wp_customize->add_setting( 'enable_sticky_header', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'enable_sticky_header', array(
		'label'    => __( 'Enable sticky header', 'mai-pro-engine' ),
		'section'  => $section,
		'type'     => 'checkbox',
		'priority' => 5,
	) );
	/*****************************************************
	 * Enable Shrinking Header setting
	 */
	$wp_customize->add_setting( 'enable_shrink_header', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'enable_shrink_header', array(
		'label'    => __( 'Enable shrinking header', 'mai-pro-engine' ),
		'section'  => $section,
		'type'     => 'checkbox',
		'priority' => 5,
	) );



// Trying this without Kirki.
return;

	Kirki::add_config( 'mai_settings', array(
		'capability'  => 'edit_theme_options',
		'option_type' => 'option',
		'option_name' => 'genesis-settings',
	) );

	/* ************ *
	 * Mai Settings *
	 * ************ */
	Kirki::add_section( 'mai_general', array(
		'title'      => __( 'Mai Settings', 'mai-pro-engine' ),
		'panel'      => '',
		'priority'   => 35,
		'capability' => 'edit_theme_options',
	) );

		// Sticky header.
		Kirki::add_field( 'mai_settings', array(
			'type'        => 'switch',
			'settings'    => 'enable_sticky_header',
			'label'       => __( 'Enable sticky header', 'mai-pro-engine' ),
			'description' => '',
			'section'     => 'mai_general',
			'default'     => 0,
			'priority'    => 10,
			'choices'     => array(
				1 => esc_attr__( 'Enable', 'mai-pro-engine' ),
				0 => esc_attr__( 'Disable', 'mai-pro-engine' ),
			),
		) );

		// Shrink header.
		Kirki::add_field( 'mai_settings', array(
			'type'        => 'switch',
			'settings'    => 'enable_shrink_header',
			'label'       => __( 'Enable shrinking header', 'mai-pro-engine' ),
			'description' => '',
			'section'     => 'mai_general',
			'default'     => 0,
			'priority'    => 10,
			'choices'     => array(
				1 => esc_attr__( 'Enable', 'mai-pro-engine' ),
				0 => esc_attr__( 'Disable', 'mai-pro-engine' ),
			),
		) );

		// Display featured image.
		Kirki::add_field( 'mai_settings', array(
			'type'        => 'multicheck',
			'settings'    => 'singular_image_post_types',
			'label'       => __( 'Display featured image', 'mai-pro-engine' ),
			'description' => __( 'Automatically display the featured image on the following single post types.', 'mai-pro-engine' ),
			'section'     => 'mai_general',
			'default'     => array( 'post', 'page' ),
			'priority'    => 10,
			'choices'     => _mai_kirki_get_singular_image_post_types_config(),
		) );

		// Footer widgets.
		Kirki::add_field( 'mai_settings', array(
			'type'        => 'radio-buttonset',
			'settings'    => 'footer_widget_count',
			'label'       => __( 'Footer widget areas', 'mai-pro-engine' ),
			'description' => __( 'Save and reload customizer to view changes.', 'mai-pro-engine' ),
			'section'     => 'mai_general',
			'default'     => '2',
			'priority'    => 10,
			'multiple'    => 1,
			'choices'     => array(
				'0' => __( 'None', 'mai-pro-engine' ),
				'1' => __( '1', 'mai-pro-engine' ),
				'2' => __( '2', 'mai-pro-engine' ),
				'3' => __( '3', 'mai-pro-engine' ),
				'4' => __( '4', 'mai-pro-engine' ),
				'6' => __( '6', 'mai-pro-engine' ),
			),
		) );

		Kirki::add_field( 'mai_settings', array(
			'type'            => 'radio',
			'settings'        => 'mobile_menu_style',
			'label'           => __( 'Mobile menu style', 'mai-pro-engine' ),
			'section'         => 'mai_general',
			'default'         => 'standard',
			'priority'        => 10,
			'multiple'        => 1,
			'choices'         => array(
				'standard' => __( 'Standard Menu', 'mai-pro-engine' ),
				'side'     => __( 'Side Menu', 'mai-pro-engine' ),
			),
		) );

	/* *****************
	 * Mai Banner Area *
	 * *************** */
	Kirki::add_section( 'mai_banner_area', array(
		'title'      => __( 'Mai Banner Area', 'mai-pro-engine' ),
		'panel'      => '',
		'priority'   => 35,
		'capability' => 'edit_theme_options',
	) );

		// Enable banner area
		Kirki::add_field( 'mai_settings', array(
			'type'            => 'switch',
			'settings'        => 'enable_banner_area',
			'label'           => __( 'Enable Banner Area', 'mai-pro-engine' ),
			'description'     => '',
			'section'         => 'mai_banner_area',
			'default'         => 1,
			'priority'        => 10,
			'choices'         => array(
				1 => esc_attr__( 'Enable', 'mai-pro-engine' ),
				0 => esc_attr__( 'Disable', 'mai-pro-engine' ),
			),
		) );

		// Background color
		Kirki::add_field( 'mai_settings', array(
			'type'            => 'color',
			'settings'        => 'banner_background_color',
			'label'           => __( 'Background color', 'mai-pro-engine' ),
			'section'         => 'mai_banner_area',
			'default'         => '#f1f1f1',
			'priority'        => 10,
			'active_callback' => _mai_kirki_is_banner_area_enabled(),
			'choices'         => array(
				'alpha' => false,
			),
		) );

		// Default image
		Kirki::add_field( 'mai_settings', array(
			'type'            => 'image',
			'settings'        => 'banner_id',
			'label'           => __( 'Banner image', 'mai-pro-engine' ),
			'description'     => __( 'Set a default banner image. Can be overridden per post/page.', 'mai-pro-engine' ),
			'section'         => 'mai_banner_area',
			'default'         => '',
			'priority'        => 10,
			'active_callback' => _mai_kirki_is_banner_area_enabled(),
			'choices'         => array(
				'save_as' => 'id'
			),
		) );

		// Overlay
		Kirki::add_field( 'mai_settings', array(
			'type'            => 'select',
			'settings'        => 'banner_overlay',
			'label'           => __( 'Enable overlay', 'mai-pro-engine' ),
			'section'         => 'mai_banner_area',
			'default'         => '',
			'priority'        => 10,
			'multiple'        => 1,
			'active_callback' => _mai_kirki_is_banner_area_enabled(),
			'choices'         => array(
				''         => __( 'None', 'mai-pro-engine' ),
				'light'    => __( 'Light', 'mai-pro-engine' ),
				'dark'     => __( 'Dark', 'mai-pro-engine' ),
				'gradient' => __( 'Gradient', 'mai-pro-engine' ),
			),
		) );

		// Inner
		Kirki::add_field( 'mai_settings', array(
			'type'            => 'select',
			'settings'        => 'banner_inner',
			'label'           => __( 'Enable inner styling', 'mai-pro-engine' ),
			'section'         => 'mai_banner_area',
			'default'         => '',
			'priority'        => 10,
			'multiple'        => 1,
			'active_callback' => _mai_kirki_is_banner_area_enabled(),
			'choices'         => array(
				''      => __( 'None', 'mai-pro-engine' ),
				'light' => __( 'Light Box', 'mai-pro-engine' ),
				'dark'  => __( 'Dark Box', 'mai-pro-engine' ),
			),
		) );

		// Content width
		Kirki::add_field( 'mai_settings', array(
			'type'            => 'select',
			'settings'        => 'banner_content_width',
			'label'           => __( 'Content width', 'mai-pro-engine' ),
			'section'         => 'mai_banner_area',
			'default'         => 'auto',
			'priority'        => 10,
			'multiple'        => 1,
			'active_callback' => _mai_kirki_is_banner_area_enabled(),
			'choices'         => array(
				'auto' => __( 'Auto', 'mai-pro-engine' ),
				'xs'   => __( 'Extra Small', 'mai-pro-engine' ),
				'sm'   => __( 'Small', 'mai-pro-engine' ),
				'md'   => __( 'Medium', 'mai-pro-engine' ),
				'lg'   => __( 'Large', 'mai-pro-engine' ),
				'xl'   => __( 'Extra Large', 'mai-pro-engine' ),
				'full' => __( 'Full Width', 'mai-pro-engine' ),
			),
		) );

		// Align text
		Kirki::add_field( 'mai_settings', array(
			'type'            => 'radio-buttonset',
			'settings'        => 'banner_align_text',
			'label'           => __( 'Text alignment', 'mai-pro-engine' ),
			'section'         => 'mai_banner_area',
			'default'         => '',
			'priority'        => 10,
			'multiple'        => 1,
			'active_callback' => _mai_kirki_is_banner_area_enabled(),
			'choices'         => array(
				''       => __( 'Default', 'mai-pro-engine' ),
				'left'   => __( 'Left', 'mai-pro-engine' ),
				'center' => __( 'Center', 'mai-pro-engine' ),
				'right'  => __( 'Right', 'mai-pro-engine' ),
			),
		) );

		// Featured image
		Kirki::add_field( 'mai_settings', array(
			'type'            => 'switch',
			'settings'        => 'banner_featured_image',
			'label'           => __( 'Featured image', 'mai-pro-engine' ),
			'description'     => __( 'Use featured image as banner image.', 'mai-pro-engine' ),
			'section'         => 'mai_banner_area',
			'default'         => 0,
			'priority'        => 10,
			'active_callback' => _mai_kirki_is_banner_area_enabled(),
			'choices'         => array(
				1 => esc_attr__( 'Enable', 'mai-pro-engine' ),
				0 => esc_attr__( 'Disable', 'mai-pro-engine' ),
			),
		) );

		// Disable post types
		Kirki::add_field( 'mai_settings', array(
			'type'            => 'multicheck',
			'settings'        => 'banner_disable_post_types',
			'label'           => __( 'Disable post types', 'mai-pro-engine' ),
			'description'     => __( 'Disable on the following singular post type.', 'mai-pro-engine' ),
			'section'         => 'mai_banner_area',
			'priority'        => 10,
			'active_callback' => _mai_kirki_is_banner_area_enabled(),
			'choices'         => Kirki_Helper::get_post_types(),
		) );

		Kirki::add_field( 'mai_settings', array(
			'type'            => 'multicheck',
			'settings'        => 'banner_disable_taxonomies',
			'label'           => __( 'Disable taxonomies', 'mai-pro-engine' ),
			'description'     => __( 'Disable on the following taxonomy archives.', 'mai-pro-engine' ),
			'section'         => 'mai_banner_area',
			'priority'        => 10,
			'active_callback' => _mai_kirki_is_banner_area_enabled(),
			'choices'         => Kirki_Helper::get_taxonomies(),
		) );

	/* **************** *
	 * Mai Site Layouts *
	 * **************** */
	Kirki::add_section( 'mai_site_layouts', array(
		'title'       => __( 'Mai Site Layouts', 'mai-pro-engine' ),
		'description' => __( 'Set the layout for specific types of content.', 'mai-pro-engine' ),
		'panel'       => '',
		'priority'    => 35,
		'capability'  => 'edit_theme_options',
	) );

		// Default.
		Kirki::add_field( 'mai_settings', array(
			'type'     => 'radio-image',
			'settings' => 'site_layout',
			'label'    => __( 'Site Default', 'mai-pro-engine' ),
			'section'  => 'mai_site_layouts',
			'default'  => genesis_get_option( 'site_layout' ),
			'priority' => 10,
			'choices'  => _mai_kirki_get_layout_images_config(),
		) );

		// Single pages.
		Kirki::add_field( 'mai_settings', array(
			'type'     => 'radio-image',
			'settings' => 'layout_page',
			'label'    => __( 'Pages', 'mai-pro-engine' ),
			'section'  => 'mai_site_layouts',
			'default'  => '',
			'priority' => 10,
			'choices'  => _mai_kirki_get_layout_images_with_site_default_config(),
		) );

		// Single posts.
		Kirki::add_field( 'mai_settings', array(
			'type'     => 'radio-image',
			'settings' => 'layout_post',
			'label'    => __( 'Posts', 'mai-pro-engine' ),
			'section'  => 'mai_site_layouts',
			'default'  => '',
			'priority' => 10,
			'choices'  => _mai_kirki_get_layout_images_with_site_default_config(),
		) );

		// Archive Layout.
		Kirki::add_field( 'mai_settings', array(
			'type'        => 'radio-image',
			'settings'    => 'layout_archive',
			'label'       => __( 'Archives', 'mai-pro-engine' ),
			'description' => __( 'Blog, category, tag, author, search results, etc.', 'mai-pro-engine' ),
			'section'     => 'mai_site_layouts',
			'default'     => '',
			'priority'    => 10,
			'choices'     => _mai_kirki_get_layout_images_with_site_default_config(),
		) );

	/* ******************** *
	 * Mai Content Archives *
	 * ******************** */
	Kirki::add_section( 'mai_content_archives', array(
		'title'       => __( 'Mai Content Archives', 'mai-pro-engine' ),
		'description' => __( 'These options will affect any blog listings page, including archive, author, blog, category, search, and tag pages, unless overridden in the corresponding metabox.', 'mai-pro-engine' ),
		'panel'       => '',
		'priority'    => 35,
		'capability'  => 'edit_theme_options',
	) );

	// Columns
	Kirki::add_field( 'mai_settings', array(
		'type'        => 'select',
		'settings'    => 'columns',
		'label'       => __( 'Columns', 'mai-pro-engine' ),
		'description' => __( 'Display content in multiple columns.', 'mai-pro-engine' ),
		'section'     => 'mai_content_archives',
		'default'     => '1',
		'priority'    => 10,
		'multiple'    => 1,
		'choices'     => array(
			'1' => __( 'None', 'mai-pro-engine' ),
			'2' => __( '2', 'mai-pro-engine' ),
			'3' => __( '3', 'mai-pro-engine' ),
			'4' => __( '4', 'mai-pro-engine' ),
			'6' => __( '6', 'mai-pro-engine' ),
		),
	) );

	// Content
	Kirki::add_field( 'mai_settings', array(
		'type'     => 'select',
		'settings' => 'content_archive',
		'label'    => __( 'Content', 'genesis' ),
		'section'  => 'mai_content_archives',
		'default'  => 'excerpts',
		'priority' => 10,
		'multiple' => 1,
		'choices'  => array(
			'none'     => __( 'No content', 'mai-pro-engine' ),
			'full'     => __( 'Entry content', 'genesis' ),
			'excerpts' => __( 'Entry excerpts', 'genesis' ),
		),
	) );

	// Content Limit
	Kirki::add_field( 'mai_settings', array(
		'type'        => 'number',
		'settings'    => 'content_archive_limit',
		'label'       => __( 'Limit content to how many characters?', 'mai-pro-engine' ),
		'description' => __( '(0 for no limit)', 'mai-pro-engine' ),
		'section'     => 'mai_content_archives',
		'default'     => 0,
		'priority'    => 10,
		'choices'     => array(
			'min'  => 0,
			'max'  => 1000,
			'step' => 1,
		),
		'active_callback' => array(
			'setting'  => 'content_archive',
			'operator' => '!=',
			'value'    => 'none',
		),
	) );

	// More Link
	Kirki::add_field( 'mai_settings', array(
		'type'     => 'switch',
		'settings' => 'more_link',
		'label'    => __( 'Read More link', 'mai-pro-engine' ),
		'section'  => 'mai_content_archives',
		'default'  => 1,
		'priority' => 10,
		'choices'  => array(
			1 => esc_attr__( 'Enable', 'mai-pro-engine' ),
			0 => esc_attr__( 'Disable', 'mai-pro-engine' ),
		),
	) );

	// Include the Featured Image
	Kirki::add_field( 'mai_settings', array(
		'type'     => 'switch',
		'settings' => 'content_archive_thumbnail',
		'label'    => __( 'Featured Image', 'genesis' ),
		'section'  => 'mai_content_archives',
		'default'  => 0,
		'priority' => 10,
		'choices'  => array(
			1 => esc_attr__( 'Enable', 'mai-pro-engine' ),
			0 => esc_attr__( 'Disable', 'mai-pro-engine' ),
		),
	) );

	// Image Location
	Kirki::add_field( 'mai_settings', array(
		'type'     => 'select',
		'settings' => 'image_location',
		'label'    => __( 'Image Location', 'genesis' ),
		'section'  => 'mai_content_archives',
		'default'  => 'before_entry',
		'priority' => 10,
		'multiple' => 1,
		'choices'  => array(
			'background'     => __( 'Background Image', 'mai-pro-engine' ),
			'before_entry'   => __( 'Before Entry', 'mai-pro-engine' ),
			'before_title'   => __( 'Before Title', 'mai-pro-engine' ),
			'after_title'    => __( 'After Title', 'mai-pro-engine' ),
			'before_content' => __( 'Before Content', 'mai-pro-engine' ),
		),
		'active_callback' => array(
			'setting'  => 'content_archive_thumbnail',
			'operator' => '==',
			'value'    => 1,
		),
	) );

	// Image Size
	Kirki::add_field( 'mai_settings', array(
		'type'     => 'select',
		'settings' => 'image_size',
		'label'    => __( 'Image Size', 'genesis' ),
		'section'  => 'mai_content_archives',
		'default'  => 'one-third',
		'priority' => 10,
		'multiple' => 1,
		'choices'  => _mai_kirki_get_image_sizes_config(),
		'active_callback' => array(
			'setting'  => 'content_archive_thumbnail',
			'operator' => '==',
			'value'    => 1,
		),
	) );

	// Image Alignment
	Kirki::add_field( 'mai_settings', array(
		'type'     => 'select',
		'settings' => 'image_alignment',
		'label'    => __( 'Image Alignment', 'genesis' ),
		'section'  => 'mai_content_archives',
		'default'  => '',
		'priority' => 10,
		'multiple' => 1,
		'choices'  => array(
			''            => __( '- None -', 'genesis' ),
			'aligncenter' => __( 'Center', 'genesis' ),
			'alignleft'   => __( 'Left', 'genesis' ),
			'alignright'  => __( 'Right', 'genesis' ),
		),
		'active_callback' => array(
			'setting'  => 'content_archive_thumbnail',
			'operator' => '==',
			'value'    => 1,
		),
	) );

	// Entry Meta
	Kirki::add_field( 'mai_settings', array(
		'type'     => 'multicheck',
		'settings' => 'remove_meta',
		'label'    => __( 'Entry Meta', 'mai-pro-engine' ),
		'section'  => 'mai_content_archives',
		'priority' => 10,
		'choices'  => array(
			'post_info' => __( 'Remove Post Info', 'mai-pro-engine' ),
			'post_meta' => __( 'Remove Post Meta', 'mai-pro-engine' ),
		),
	) );

}

/**
 * Limit the layout images so they all fit in 2 rows.
 *
 * @return  void
 */
// add_action( 'customize_controls_print_styles', 'mai_do_customizer_css' );
function mai_do_customizer_css() {
	echo '<style type="text/css">
		.customize-control-kirki-radio-image label:not(.customizer-text) {
			max-width: 19%;
		}
	</style>';
}

// add_action( 'customize_controls_enqueue_scripts', 'themedemo_customizer_style');
// function themedemo_customizer_style() {
	// wp_add_inline_style( 'customize-controls', '.wp-full-overlay-sidebar { background: #abcdef }');
// }

/**
 * Get the layout images array for radio-image choices with Kirki.
 *
 * @return  array
 */
function _mai_kirki_get_layout_images_config() {
	$layouts = genesis_get_layouts();
	$choices = array();
	foreach ( $layouts as $name => $values ) {
		$choices[$name] = $values['img'];
	}
	return $choices;
}

/**
 * Get the layout images array with site-default option for radio-image choices with Kirki.
 *
 * @return  array
 */
function _mai_kirki_get_layout_images_with_site_default_config() {
	$choices = _mai_kirki_get_layout_images_config();
	return array_merge( array( '' => MAI_PRO_ENGINE_PLUGIN_URL . 'assets/images/layouts/site-default.gif' ), $choices );
}

/**
 * Get the layout images array with archives-default option for radio-image choices with Kirki.
 *
 * @return  array
 */
function _mai_kirki_get_layout_images_with_archives_default_config() {
	$choices = _mai_kirki_get_layout_images_config();
	return array_merge( array( '' => MAI_PRO_ENGINE_PLUGIN_URL . 'assets/images/layouts/archives-default.gif' ), $choices );
}

/**
 * Get the singular images array for Kirki.
 *
 * @return  array
 */
function _mai_kirki_get_singular_image_post_types_config() {
	$singular_image_post_types = Kirki_Helper::get_post_types();
	if ( $singular_image_post_types ) {
		if ( isset( $singular_image_post_types['attachment'] ) ) {
			unset( $singular_image_post_types['attachment'] );
		}
		if ( class_exists( 'WooCommerce' ) && isset( $singular_image_post_types['product'] ) ) {
			unset( $singular_image_post_types['product'] );
		}
	}
	return $singular_image_post_types;
}

/**
 * Get the image sizes array for Kirki.
 *
 * @return  array
 */
function _mai_kirki_get_image_sizes_config() {
	// Get our image size options
	$sizes   = genesis_get_image_sizes();
	$options = array();
	foreach ( $sizes as $index => $value ) {
		$options[$index] = sprintf( '%s (%s x %s)', $index, $value['width'], $value['height'] );
	}
	return $options;
}

/**
 * Kirki conditional function to check if banner area is enabled.
 *
 * @return  array
 */
function _mai_kirki_is_banner_area_enabled() {
	return array( array(
		'setting'  => 'enable_banner_area',
		'operator' => '==',
		'value'    => 1,
	) );
}
