<?php

// Add custom archive support for CPT
add_post_type_support( 'product', 'genesis-cpt-archives-settings' );

add_action( 'genesis_before_loop', function() {
	// $settings = get_option( 'genesis-settings' );
	// $settings = get_option( 'genesis-cpt-archive-settings-product' );
	// d( $settings );
});

// add_action( 'customize_register', function() {

// 	if ( ! genesis_is_customizer() ) {
// 		return;
// 	}

// 	add_filter( 'option_genesis-settings', 'mai_kirki_banner_id_to_url' );
// 	function mai_kirki_banner_id_to_url( $genesis_settings ) {
// 		// d( $genesis_settings['banner_id'] );
// 		$banner_id = (int) $genesis_settings['banner_id'];
// 		if ( ! ( is_integer( $banner_id ) && $banner_id > 1 ) ) {
// 			return $genesis_settings;
// 		}
// 		$genesis_settings['banner_id'] = wp_get_attachment_url( $banner_id );
// 		return $genesis_settings;
// 	}

// }, 10 );

/**
 * Register Customizer control for general settings.
 *
 * @param   $wp_customize  The customizer object.
 *
 * @return  void
 */
add_action( 'customize_register', 'mai_customizer_general_settings', 20 );
function mai_customizer_general_settings( $wp_customize ) {

	// Bail if Kirki isn't running.
	if ( ! class_exists( 'Kirki' ) ) {
		return;
	}

	// Remove Genesis 'Content Archives' section
	$wp_customize->remove_section( 'genesis_archives' );

	// Kirki::add_config( 'mai_settings', array(
	// 	'capability'  => 'edit_theme_options',
	// 	'option_type' => 'theme_mod',
	// ) );

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
			'choices'     => _mai_kirki_get_public_post_types_config(),
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
			'type'            => 'radio-buttonset',
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
			// 'sanitize_callback' => '_mai_kirki_get_banner_id',
		) );

		// Overlay
		Kirki::add_field( 'mai_settings', array(
			'type'            => 'radio-buttonset',
			'settings'        => 'banner_overlay',
			'label'           => __( 'Enable overlay', 'mai-pro-engine' ),
			'section'         => 'mai_banner_area',
			'default'         => '',
			'priority'        => 10,
			'multiple'        => 1,
			'active_callback' => _mai_kirki_is_banner_area_enabled(),
			'choices'         => array(
				''         => __( '- None -', 'genesis' ),
				'light'    => __( 'Light', 'mai-pro-engine' ),
				'dark'     => __( 'Dark', 'mai-pro-engine' ),
				'gradient' => __( 'Gradient', 'mai-pro-engine' ),
			),
		) );

		// Inner
		Kirki::add_field( 'mai_settings', array(
			'type'            => 'radio-buttonset',
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
			'type'            => 'radio-buttonset',
			'settings'        => 'banner_content_width',
			'label'           => __( 'Content width', 'mai-pro-engine' ),
			'section'         => 'mai_banner_area',
			'default'         => 'auto',
			'priority'        => 10,
			'multiple'        => 1,
			'active_callback' => _mai_kirki_is_banner_area_enabled(),
			'choices'         => array(
				'auto' => __( 'Auto', 'mai-pro-engine' ),
				'xs'   => __( 'XS', 'mai-pro-engine' ),
				'sm'   => __( 'SM', 'mai-pro-engine' ),
				'md'   => __( 'MD', 'mai-pro-engine' ),
				'lg'   => __( 'LG', 'mai-pro-engine' ),
				'xl'   => __( 'XL', 'mai-pro-engine' ),
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
			'description'     => __( 'Disable on the following post types.', 'mai-pro-engine' ),
			'section'         => 'mai_banner_area',
			'priority'        => 10,
			'active_callback' => _mai_kirki_is_banner_area_enabled(),
			'choices'         => Kirki_Helper::get_post_types(),
		) );

		Kirki::add_field( 'mai_settings', array(
			'type'            => 'multicheck',
			'settings'        => 'banner_disable_taxonomies',
			'label'           => __( 'Disable taxonomies', 'mai-pro-engine' ),
			'description'     => __( 'Disable on the following taxonomies.', 'mai-pro-engine' ),
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

	mai_kirki_do_content_archive_settings( 'mai_settings', 'mai_content_archives' );

	$post_types = Kirki_Helper::get_post_types();

	if ( $post_types ) {

		if ( isset( $post_types['attachment'] ) ) {
			unset( $post_types['attachment'] );
		}

		Kirki::add_panel( 'mai_cpt_archives', array(
			'priority' => 46,
			'title'    => __( 'Mai CPT Archives', 'mai-pro-engine' ),
		) );

		foreach ( $post_types as $post_type => $label ) {

			if ( class_exists( 'WooCommerce' ) && 'product' === $post_type ) {
				mai_kirki_do_product_archive_settings();
				continue;
			}

			$object = get_post_type_object( $post_type );

			// Skip if this post type doesn't have an archive.
			if ( ! $object->has_archive ) {
				continue;
			}

			$section = 'mai_cpt_' . $post_type;

			$option = sanitize_title_with_dashes( 'genesis-cpt-archive-settings-' . $post_type );
			$config = str_replace( '-', '_', $option );

			Kirki::add_config( $config, array(
				'capability'  => 'edit_theme_options',
				'option_type' => 'option',
				'option_name' => $option,
			) );

			Kirki::add_section( $section, array(
				'title'      => $label,
				'panel'      => 'mai_cpt_archives',
				'priority'   => 35,
				'capability' => 'edit_theme_options',
			) );

			mai_kirki_do_content_archive_settings( $config, $section, 'mai_cpt_archives', $check_enabled );

		}

	}

}


function mai_kirki_do_content_archive_settings( $config, $section, $panel = '', $check_enabled = false ) {

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
		'type'        => 'radio-buttonset',
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


function mai_kirki_do_product_archive_settings() {

	$config  = 'mai_woo_product_archive_settings';
	$option  = 'genesis-cpt-archive-settings-product';
	$section = 'mai_settings';

	Kirki::add_config( $config, array(
		'capability'  => 'edit_theme_options',
		'option_type' => 'option',
		'option_name' => $option,
	) );

	Kirki::add_section( 'mai_settings', array(
		'title'      => __( 'Mai Product Archives', 'mai-pro-engine' ),
		'panel'      => '',
		'priority'   => 35,
		'capability' => 'edit_theme_options',
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
