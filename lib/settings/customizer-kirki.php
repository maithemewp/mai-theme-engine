<?php

// Add custom archive support
add_post_type_support( 'product', 'genesis-cpt-archives-settings' );

/**
 * Register Customizer control for general settings.
 *
 * @param   $wp_customize  The customizer object.
 *
 * @return  void
 */
add_action( 'customize_register', 'mai_customizer_settings', 20 );
function mai_customizer_settings( $wp_customize ) {

	// Bail if Kirki isn't running.
	if ( ! class_exists( 'Kirki' ) ) {
		return;
	}

	// Remove Genesis 'Content Archives' section
	$wp_customize->remove_section( 'genesis_archives' );

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

		$singular_image_post_types = Kirki_Helper::get_post_types();
		if ( $singular_image_post_types ) {
			if ( isset( $singular_image_post_types['attachment'] ) ) {
				unset( $singular_image_post_types['attachment'] );
			}
			if ( class_exists( 'WooCommerce' ) && isset( $singular_image_post_types['product'] ) ) {
				unset( $singular_image_post_types['product'] );
			}
		}

		// Display featured image.
		Kirki::add_field( 'mai_settings', array(
			'type'        => 'multicheck',
			'settings'    => 'singular_image_post_types',
			'label'       => __( 'Display featured image', 'mai-pro-engine' ),
			'description' => __( 'Automatically display the featured image on the following single post types.', 'mai-pro-engine' ),
			'section'     => 'mai_general',
			'default'     => array( 'post', 'page' ),
			'priority'    => 10,
			'choices'     => $singular_image_post_types,
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

		// Single Post
		// Kirki::add_field( 'mai_settings', array(
		// 	'type'     => 'select',
		// 	'settings' => 'single_post_layout',
		// 	'label'    => __( 'Single Post Layout', 'mai-pro-engine' ),
		// 	'section'  => 'genesis_layout',
		// 	'default'  => genesis_get_option( 'layout' ),
		// 	'priority' => 10,
		// 	'choices'  => genesis_get_layouts_for_customizer(),
		// ) );

		// if ( class_exists( 'WooCommerce' ) ) {

		// 	// Single Product
		// 	Kirki::add_field( 'mai_settings', array(
		// 		'type'     => 'select',
		// 		'settings' => 'single_product_layout',
		// 		'label'    => __( 'Single Post Layout', 'mai-pro-engine' ),
		// 		'section'  => 'genesis_layout',
		// 		'default'  => genesis_get_option( 'layout' ),
		// 		'priority' => 10,
		// 		'choices'  => genesis_get_layouts_for_customizer(),
		// 	) );

		// }

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
				''       => __( 'None', 'mai-pro-engine' ),
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

	// mai_kirki_do_content_archive_settings( 'mai_settings', 'mai_content_archives' );

	// $post_types = Kirki_Helper::get_post_types();
	$post_types = genesis_get_cpt_archive_types();
	// $post_types = array( 'post' => get_post_type_object( 'post' ) ) + $post_types;
// d( $post_types );

	if ( $post_types ) {

		// Kirki::add_panel( 'mai_cpt_archives', array(
		// 	'priority' => 46,
		// 	'title'    => __( 'Mai CPT Archives', 'mai-pro-engine' ),
		// ) );

		foreach ( $post_types as $name => $object ) {

			// if ( class_exists( 'WooCommerce' ) && 'product' === $name ) {
			// 	mai_kirki_do_product_archive_settings();
			// 	continue;
			// }

			$section = 'mai_cpt_' . $name;

			$option = sanitize_title_with_dashes( 'genesis-cpt-archive-settings-' . $name );
			$config = str_replace( '-', '_', $option );

			Kirki::add_config( $config, array(
				'capability'  => 'edit_theme_options',
				'option_type' => 'option',
				'option_name' => $option,
			) );

			Kirki::add_section( $section, array(
				'title'      => sprintf( __( 'Mai %s Settings' ), $object->label ),
				'panel'      => '',
				'priority'   => 35,
				'capability' => 'edit_theme_options',
			) );

			mai_kirki_do_cpt_settings( $config, $section, $name, 'mai_cpt_archives', true );

		}

	}

}


function mai_kirki_do_content_archive_settings( $config, $section, $post_type, $panel = '', $check_enabled = false ) {

	$active_callback = '';

	if ( $check_enabled ) {
		$active_callback = array(
			array(
				'setting'  => 'enable_content_archive_settings',
				'operator' => '==',
				'value'    => 1,
			),
		);
	}

	/**
	 * Banner image.
	 * Currently doesn't work when more than one field has same 'settings' value (banner_id)
	 */
	// Kirki::add_field( 'mai_settings', array(
	// 	'type'            => 'image',
	// 	'settings'        => 'banner_id',
	// 	'label'           => __( 'Banner image', 'mai-pro-engine' ),
	// 	'description'     => __( 'Set a default banner image. Can be overridden per post/page.', 'mai-pro-engine' ),
	// 	'section'         => $section,
	// 	'default'         => '',
	// 	'priority'        => 10,
	// 	'active_callback' => _mai_kirki_is_banner_area_enabled(),
	// 	'choices'         => array(
	// 		'save_as' => 'id'
	// 	),
	// ) );

	// Archive Layout
	Kirki::add_field( $config, array(
		'type'     => 'select',
		'settings' => 'layout',
		'label'    => __( 'Archive Layout', 'mai-pro-engine' ),
		'section'  => $section,
		'default'  => genesis_get_option( 'layout' ),
		'priority' => 10,
		'choices'  => genesis_get_layouts_for_customizer(),
	) );

	// Single layout (stored in theme-settings)
	Kirki::add_field( 'mai_settings', array(
		'type'     => 'select',
		'settings' => sprintf( 'single_%s_layout', $post_type ),
		'label'    => __( 'Singular Layout', 'mai-pro-engine' ),
		'section'  => $section,
		'default'  => genesis_get_option( 'layout' ),
		'priority' => 10,
		'choices'  => genesis_get_layouts_for_customizer(),
	) );

	// Enable archive settings
	Kirki::add_field( $config, array(
		'type'     => 'switch',
		'settings' => 'enable_content_archive_settings',
		'label'    => __( 'Enable custom archive settings', 'mai-pro-engine' ),
		'section'  => $section,
		'default'  => 1,
		'priority' => 10,
		'choices'  => array(
			1 => esc_attr__( 'Enable', 'mai-pro-engine' ),
			0 => esc_attr__( 'Disable', 'mai-pro-engine' ),
		),
	) );

	// Columns
	Kirki::add_field( $config, array(
		'type'        => 'select',
		'settings'    => 'columns',
		'label'       => __( 'Columns', 'mai-pro-engine' ),
		'description' => __( 'Display content in multiple columns.', 'mai-pro-engine' ),
		'section'     => $section,
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
		'active_callback' => $active_callback,
	) );

	// Content
	Kirki::add_field( $config, array(
		'type'     => 'select',
		'settings' => 'content_archive',
		'label'    => __( 'Content', 'genesis' ),
		'section'  => $section,
		'default'  => 'excerpts',
		'priority' => 10,
		'multiple' => 1,
		'choices'  => array(
			'none'     => __( 'No content', 'mai-pro-engine' ),
			'full'     => __( 'Entry content', 'genesis' ),
			'excerpts' => __( 'Entry excerpts', 'genesis' ),
		),
		'active_callback' => $active_callback,
	) );

	// Content Limit
	Kirki::add_field( $config, array(
		'type'        => 'number',
		'settings'    => 'content_archive_limit',
		'label'       => __( 'Limit content to how many characters?', 'mai-pro-engine' ),
		'description' => __( '(0 for no limit)', 'mai-pro-engine' ),
		'section'     => $section,
		'default'     => 0,
		'priority'    => 10,
		'choices'     => array(
			'min'  => 0,
			'max'  => 1000,
			'step' => 1,
		),
		'active_callback' => array_merge( $active_callback, array(
			'setting'  => 'content_archive',
			'operator' => '!=',
			'value'    => 'none',
		) ),
	) );

	// More Link
	Kirki::add_field( $config, array(
		'type'     => 'switch',
		'settings' => 'more_link',
		'label'    => __( 'Read More link', 'mai-pro-engine' ),
		'section'  => $section,
		'default'  => 1,
		'priority' => 10,
		'choices'  => array(
			1 => esc_attr__( 'Enable', 'mai-pro-engine' ),
			0 => esc_attr__( 'Disable', 'mai-pro-engine' ),
		),
		'active_callback' => $active_callback,
	) );

	// Include the Featured Image
	Kirki::add_field( $config, array(
		'type'     => 'switch',
		'settings' => 'content_archive_thumbnail',
		'label'    => __( 'Featured Image', 'genesis' ),
		'section'  => $section,
		'default'  => 0,
		'priority' => 10,
		'choices'  => array(
			1 => esc_attr__( 'Enable', 'mai-pro-engine' ),
			0 => esc_attr__( 'Disable', 'mai-pro-engine' ),
		),
		'active_callback' => $active_callback,
	) );

	// Image Location
	Kirki::add_field( $config, array(
		'type'     => 'select',
		'settings' => 'image_location',
		'label'    => __( 'Image Location', 'genesis' ),
		'section'  => $section,
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
		'active_callback' => array_merge( $active_callback, array(
			'setting'  => 'content_archive_thumbnail',
			'operator' => '==',
			'value'    => 1,
		) ),
	) );

	// Image Size
	Kirki::add_field( $config, array(
		'type'     => 'select',
		'settings' => 'image_size',
		'label'    => __( 'Image Size', 'genesis' ),
		'section'  => $section,
		'default'  => 'one-third',
		'priority' => 10,
		'multiple' => 1,
		'choices'  => _mai_kirki_get_image_sizes_config(),
		'active_callback' => array_merge( $active_callback, array(
			'setting'  => 'content_archive_thumbnail',
			'operator' => '==',
			'value'    => 1,
		) ),
	) );

	// Image Alignment
	Kirki::add_field( $config, array(
		'type'     => 'select',
		'settings' => 'image_alignment',
		'label'    => __( 'Image Alignment', 'genesis' ),
		'section'  => $section,
		'default'  => '',
		'priority' => 10,
		'multiple' => 1,
		'choices'  => array(
			''            => __( '- None -', 'genesis' ),
			'aligncenter' => __( 'Center', 'genesis' ),
			'alignleft'   => __( 'Left', 'genesis' ),
			'alignright'  => __( 'Right', 'genesis' ),
		),
		'active_callback' => array_merge( $active_callback, array(
			'setting'  => 'content_archive_thumbnail',
			'operator' => '==',
			'value'    => 1,
		) ),
	) );

	// Entry Meta
	Kirki::add_field( $config, array(
		'type'     => 'multicheck',
		'settings' => 'remove_meta',
		'label'    => __( 'Entry Meta', 'mai-pro-engine' ),
		'section'  => $section,
		'priority' => 10,
		'choices'  => array(
			'post_info' => __( 'Remove Post Info', 'mai-pro-engine' ),
			'post_meta' => __( 'Remove Post Meta', 'mai-pro-engine' ),
		),
		'active_callback' => $active_callback,
	) );

}

function mai_kirki_do_cpt_settings( $config, $section, $post_type, $panel = '', $check_enabled = false ) {

	// Single layout (stored in theme-settings)
	Kirki::add_field( $config, array(
		'type'     => 'select',
		'settings' => sprintf( 'single_%s_layout', $post_type ),
		'label'    => __( 'Singular Layout', 'mai-pro-engine' ),
		'section'  => $section,
		'default'  => genesis_get_option( 'layout' ),
		'priority' => 10,
		'choices'  => genesis_get_layouts_for_customizer(),
	) );

	// Archive Layout
	Kirki::add_field( $config, array(
		'type'     => 'select',
		'settings' => 'layout',
		'label'    => __( 'Archive Layout', 'mai-pro-engine' ),
		'section'  => $section,
		'default'  => genesis_get_option( 'layout' ),
		'priority' => 10,
		'choices'  => genesis_get_layouts_for_customizer(),
	) );

	// Columns
	Kirki::add_field( $config, array(
		'type'        => 'select',
		'settings'    => 'columns',
		'label'       => __( 'Columns', 'mai-pro-engine' ),
		'description' => __( 'Display content in multiple columns.', 'mai-pro-engine' ),
		'section'     => $section,
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
		'active_callback' => $active_callback,
	) );

	if ( post_type_supports( $post_type, 'editor' ) && post_type_supports( $post_type, 'excerpt' ) ) {

		// Content
		Kirki::add_field( $config, array(
			'type'     => 'select',
			'settings' => 'content_archive',
			'label'    => __( 'Content', 'genesis' ),
			'section'  => $section,
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
		Kirki::add_field( $config, array(
			'type'        => 'number',
			'settings'    => 'content_archive_limit',
			'label'       => __( 'Limit content to how many characters?', 'mai-pro-engine' ),
			'description' => __( '(0 for no limit)', 'mai-pro-engine' ),
			'section'     => $section,
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

	}

	if ( post_type_supports( $post_type, 'thumbnail' ) ) {

		// Include the Featured Image
		Kirki::add_field( $config, array(
			'type'     => 'switch',
			'settings' => 'content_archive_thumbnail',
			'label'    => __( 'Featured Image', 'genesis' ),
			'section'  => $section,
			'default'  => 0,
			'priority' => 10,
			'choices'  => array(
				1 => esc_attr__( 'Enable', 'mai-pro-engine' ),
				0 => esc_attr__( 'Disable', 'mai-pro-engine' ),
			),
		) );

		// Image Location
		Kirki::add_field( $config, array(
			'type'     => 'select',
			'settings' => 'image_location',
			'label'    => __( 'Image Location', 'genesis' ),
			'section'  => $section,
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
				'operator' => '!=',
				'value'    => 'none',
			),
		) );

		// Image Size
		Kirki::add_field( $config, array(
			'type'     => 'select',
			'settings' => 'image_size',
			'label'    => __( 'Image Size', 'genesis' ),
			'section'  => $section,
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
		Kirki::add_field( $config, array(
			'type'     => 'select',
			'settings' => 'image_alignment',
			'label'    => __( 'Image Alignment', 'genesis' ),
			'section'  => $section,
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

	}

}

function mai_kirki_do_product_archive_settings() {

	$config  = 'mai_woo_product_archive_settings';
	$option  = 'genesis-cpt-archive-settings-product';
	$section = $config;

	Kirki::add_config( $config, array(
		'capability'  => 'edit_theme_options',
		'option_type' => 'option',
		'option_name' => $option,
	) );

	Kirki::add_section( $config, array(
		'title'      => __( 'Mai Product Archives', 'mai-pro-engine' ),
		'panel'      => '',
		'priority'   => 35,
		'capability' => 'edit_theme_options',
	) );

	// Banner image
	Kirki::add_field( $config, array(
		'type'            => 'image',
		'settings'        => 'banner_id',
		'label'           => __( 'Banner image', 'mai-pro-engine' ),
		'description'     => __( 'Set a banner image for the shop and product category/tag pages. If none is set, the default banner image will be used.', 'mai-pro-engine' ),
		'section'         => $section,
		'default'         => '',
		'priority'        => 10,
		'active_callback' => _mai_kirki_is_banner_area_enabled(),
		'choices'         => array(
			'save_as' => 'id'
		),
	) );

	// Columns
	Kirki::add_field( $config, array(
		'type'        => 'radio-buttonset',
		'settings'    => 'columns',
		'label'       => __( 'Columns', 'mai-pro-engine' ),
		'description' => __( 'Display content in multiple columns.', 'mai-pro-engine' ),
		'section'     => $section,
		'default'     => '3',
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

	// Include the Featured Image
	Kirki::add_field( $config, array(
		'type'     => 'switch',
		'settings' => 'content_archive_thumbnail',
		'label'    => __( 'Featured Image', 'genesis' ),
		'section'  => $section,
		'default'  => 1,
		'priority' => 10,
		'choices'  => array(
			1 => esc_attr__( 'Enable', 'mai-pro-engine' ),
			0 => esc_attr__( 'Disable', 'mai-pro-engine' ),
		),
	) );

	// Content Limit
	Kirki::add_field( $config, array(
		'type'        => 'number',
		'settings'    => 'posts_per_page',
		'label'       => __( 'Entries Per Page', 'mai-pro-engine' ),
		'description' => __( 'The max number of posts to show, per page.', 'mai-pro-engine' ),
		'section'     => $section,
		'default'     => get_option( 'posts_per_page' ),
		'priority'    => 10,
		'choices'     => array(
			'min'  => 0,
			'max'  => 1000,
			'step' => 1,
		),
	) );

	// NOT WORKING
	Kirki::add_field( $config, array(
		'type'        => 'radio',
		'settings'    => 'posts_nav',
		'label'       => __( 'Shop Pagination', 'genesis' ),
		'section'     => $section,
		'default'     => genesis_get_option( 'posts_nav' ),
		'priority'    => 10,
		'multiple'    => 1,
		'choices'     => array(
			'prev-next' => __( 'Previous / Next', 'genesis' ),
			'numeric'   => __( 'Numeric', 'genesis' ),
		),
	) );

}


function _mai_kirki_get_public_post_types_config() {
	$options    = array();
	$post_types = get_post_types( array( 'public' => true ), 'objects' );
	if ( $post_types ) {
		foreach ( $post_types as $post_type ) {
			$options[$post_type->name] = $post_type->label;
		}
	}
	return $options;
}

function _mai_kirki_get_public_taxonomies_config() {
	$options    = array();
	$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
	if ( $taxonomies ) {
		foreach ( $taxonomies as $taxo ) {
			$options[$taxo->name] = $taxo->label;
		}
	}
	return $options;
}

function _mai_kirki_get_image_sizes_config() {
	// Get our image size options
	$sizes   = genesis_get_image_sizes();
	$options = array();
	foreach ( $sizes as $index => $value ) {
		$options[$index] = sprintf( '%s (%s x %s)', $index, $value['width'], $value['height'] );
	}
	return $options;
}

function _mai_kirki_is_banner_area_enabled() {
	return array( array(
		'setting'  => 'enable_banner_area',
		'operator' => '==',
		'value'    => 1,
	) );
}
