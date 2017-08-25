<?php

/**
 * Register new Customizer elements.
 *
 * @param WP_Customize_Manager $wp_customize WP_Customize_Manager instance.
 */
add_action( 'customize_register', 'mai_register_customizer_settings', 20 );
function mai_register_customizer_settings( $wp_customize ) {

	// Remove Genesis 'Content Archives' section
	$wp_customize->remove_section( 'genesis_archives' );

	// Remove Genesis 'Site Layout' section
	$wp_customize->remove_section( 'genesis_layout' );

	$section        = 'mai_settings';
	$settings_field = 'genesis-settings';

	/* ************ *
	 * Mai Settings *
	 * ************ */

	// Section.
	$wp_customize->add_section(
		$section,
		array(
			'title'    => __( 'Mai Settings', 'mai-pro-engine' ),
			'priority' => '35',
		)
	);

	// Sticky Header.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'enable_sticky_header' ),
		array(
			'default' => 0,
			'type'    => 'option',
		)
	);
	$wp_customize->add_control(
		'enable_sticky_header',
		array(
			'label'    => __( 'Enable sticky header', 'mai-pro-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'enable_sticky_header' ),
			'type'     => 'checkbox',
		)
	);

	// Shrink Header.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'enable_shrink_header' ),
		array(
			'default' => 0,
			'type'    => 'option',
		)
	);
	$wp_customize->add_control(
		'enable_shrink_header',
		array(
			'label'    => __( 'Enable shrink header', 'mai-pro-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'enable_shrink_header' ),
			'type'     => 'checkbox',
		)
	);

	// // Footer widgets.
	// Kirki::add_field( 'mai_settings', array(
	// 	'type'        => 'radio-buttonset',
	// 	'settings'    => 'footer_widget_count',
	// 	'label'       => __( 'Footer widget areas', 'mai-pro-engine' ),
	// 	'description' => __( 'Save and reload customizer to view changes.', 'mai-pro-engine' ),
	// 	'section'     => 'mai_general',
	// 	'default'     => '2',
	// 	'priority'    => 10,
	// 	'multiple'    => 1,
	// 	'choices'     => array(
	// 		'0' => __( 'None', 'mai-pro-engine' ),
	// 		'1' => __( '1', 'mai-pro-engine' ),
	// 		'2' => __( '2', 'mai-pro-engine' ),
	// 		'3' => __( '3', 'mai-pro-engine' ),
	// 		'4' => __( '4', 'mai-pro-engine' ),
	// 		'6' => __( '6', 'mai-pro-engine' ),
	// 	),
	// ) );

	// Kirki::add_field( 'mai_settings', array(
	// 	'type'            => 'radio',
	// 	'settings'        => 'mobile_menu_style',
	// 	'label'           => __( 'Mobile menu style', 'mai-pro-engine' ),
	// 	'section'         => 'mai_general',
	// 	'default'         => 'standard',
	// 	'priority'        => 10,
	// 	'multiple'        => 1,
	// 	'choices'         => array(
	// 		'standard' => __( 'Standard Menu', 'mai-pro-engine' ),
	// 		'side'     => __( 'Side Menu', 'mai-pro-engine' ),
	// 	),
	// ) );

	// Footer widgets.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'footer_widget_count' ),
		array(
			'default'           => '2',
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_key',
		)
	);
	$wp_customize->add_control(
		'footer_widget_count',
		array(
			'label'       => __( 'Footer widget areas', 'mai-pro-engine' ),
			'description' => __( 'Save and reload customizer to view changes.', 'mai-pro-engine' ),
			'section'     => $section,
			'settings'    => _mai_customizer_get_field_name( $settings_field, 'footer_widget_count' ),
			'priority'    => 10,
			'type'        => 'select',
			'choices'     => array(
				'0' => __( 'None', 'mai-pro-engine' ),
				'1' => __( '1', 'mai-pro-engine' ),
				'2' => __( '2', 'mai-pro-engine' ),
				'3' => __( '3', 'mai-pro-engine' ),
				'4' => __( '4', 'mai-pro-engine' ),
				'6' => __( '6', 'mai-pro-engine' ),
			),
		)
	);

	// Mobile menu.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'mobile_menu_style' ),
		array(
			'default'           => 'standard',
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_key',
		)
	);
	$wp_customize->add_control(
		'mobile_menu_style',
		array(
			'label'    => __( 'Mobile menu style', 'mai-pro-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'mobile_menu_style' ),
			'priority' => 10,
			'type'     => 'select',
			'choices'  => array(
				'standard' => __( 'Standard Menu', 'mai-pro-engine' ),
				'side'     => __( 'Side Menu', 'mai-pro-engine' ),
			),
		)
	);

	/* *****************
	 * Mai Banner Area *
	 * *************** */

	$section = 'mai_banner_area';

	// Section.
	$wp_customize->add_section(
		$section,
		array(
			'title'    => __( 'Mai Banner Area', 'mai-pro-engine' ),
			'priority' => '35',
		)
	);

	// Enable.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'enable_banner_area' ),
		array(
			'default' => 0,
			'type'    => 'option',
		)
	);
	$wp_customize->add_control(
		'enable_banner_area',
		array(
			'label'    => __( 'Enable Banner Area', 'mai-pro-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'enable_banner_area' ),
			'type'     => 'checkbox',
		)
	);

	// Background color.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'banner_background_color' ),
		array(
			'default'           => '#f1f1f1',
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control( $wp_customize,
		'banner_background_color',
		array(
			'label'           => __( 'Background Color', 'mai-pro-engine' ),
			'section'         => $section,
			'settings'        => _mai_customizer_get_field_name( $settings_field, 'banner_background_color' ),
			'active_callback' => function() use ( $wp_customize, $settings_field ) {
				return _mai_customizer_is_banner_area_enabled( $wp_customize, $settings_field );
			},
		)
	) );

	// Banner Image.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'banner_id' ),
		array(
			'default'           => '',
			'type'              => 'option',
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control( $wp_customize,
		'banner_id',
		array(
			'label'           => __( 'Banner Image', 'mai-pro-engine' ),
			'description'     => __( 'Set a default banner image. Can be overridden per post/page.', 'mai-pro-engine' ),
			'section'         => $section,
			'settings'        => _mai_customizer_get_field_name( $settings_field, 'banner_id' ),
			'active_callback' => function() use ( $wp_customize, $settings_field ) {
				return _mai_customizer_is_banner_area_enabled( $wp_customize, $settings_field );
			},
		)
	) );

	// Banner featured image.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'banner_featured_image' ),
		array(
			'default' => 0,
			'type'    => 'option',
		)
	);
	$wp_customize->add_control(
		'banner_featured_image',
		array(
			'label'           => __( 'Use featured image as banner image.', 'mai-pro-engine' ),
			'section'         => $section,
			'settings'        => _mai_customizer_get_field_name( $settings_field, 'banner_featured_image' ),
			'priority'        => 10,
			'type'            => 'checkbox',
			'active_callback' => function() use ( $wp_customize, $settings_field ) {
				return _mai_customizer_is_banner_area_enabled( $wp_customize, $settings_field );
			},
		)
	);

	// Overlay.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'banner_overlay' ),
		array(
			'default'           => '',
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_key',
		)
	);
	$wp_customize->add_control(
		'banner_overlay',
		array(
			'label'    => __( 'Overlay', 'mai-pro-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'banner_overlay' ),
			'priority' => 10,
			'type'     => 'select',
			'choices'  => array(
				''         => __( 'None', 'mai-pro-engine' ),
				'light'    => __( 'Light', 'mai-pro-engine' ),
				'dark'     => __( 'Dark', 'mai-pro-engine' ),
				'gradient' => __( 'Gradient', 'mai-pro-engine' ),
			),
			'active_callback' => function() use ( $wp_customize, $settings_field ) {
				return _mai_customizer_is_banner_area_enabled( $wp_customize, $settings_field );
			},
		)
	);

	// Inner.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'banner_inner' ),
		array(
			'default'           => '',
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_key',
		)
	);
	$wp_customize->add_control(
		'banner_inner',
		array(
			'label'    => __( 'Inner styling', 'mai-pro-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'banner_inner' ),
			'priority' => 10,
			'type'     => 'select',
			'choices'  => array(
				''      => __( 'None', 'mai-pro-engine' ),
				'light' => __( 'Light Box', 'mai-pro-engine' ),
				'dark'  => __( 'Dark Box', 'mai-pro-engine' ),
			),
			'active_callback' => function() use ( $wp_customize, $settings_field ) {
				return _mai_customizer_is_banner_area_enabled( $wp_customize, $settings_field );
			},
		)
	);

	// Content width.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'banner_content_width' ),
		array(
			'default'           => 'auto',
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_key',
		)
	);
	$wp_customize->add_control(
		'banner_content_width',
		array(
			'label'    => __( 'Content width', 'mai-pro-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'banner_content_width' ),
			'priority' => 10,
			'type'     => 'select',
			'choices'  => array(
				'auto' => __( 'Auto', 'mai-pro-engine' ),
				'xs'   => __( 'Extra Small', 'mai-pro-engine' ),
				'sm'   => __( 'Small', 'mai-pro-engine' ),
				'md'   => __( 'Medium', 'mai-pro-engine' ),
				'lg'   => __( 'Large', 'mai-pro-engine' ),
				'xl'   => __( 'Extra Large', 'mai-pro-engine' ),
				'full' => __( 'Full Width', 'mai-pro-engine' ),
			),
			'active_callback' => function() use ( $wp_customize, $settings_field ) {
				return _mai_customizer_is_banner_area_enabled( $wp_customize, $settings_field );
			},
		)
	);

	// Align text.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'banner_align_text' ),
		array(
			'default'           => '',
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_key',
		)
	);
	$wp_customize->add_control(
		'banner_align_text',
		array(
			'label'    => __( 'Text alignment', 'mai-pro-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'banner_align_text' ),
			'priority' => 10,
			'type'     => 'select',
			'choices'  => array(
				''       => __( 'Default', 'mai-pro-engine' ),
				'left'   => __( 'Left', 'mai-pro-engine' ),
				'center' => __( 'Center', 'mai-pro-engine' ),
				'right'  => __( 'Right', 'mai-pro-engine' ),
			),
			'active_callback' => function() use ( $wp_customize, $settings_field ) {
				return _mai_customizer_is_banner_area_enabled( $wp_customize, $settings_field );
			},
		)
	);

	// Disable post types. TODO: MAKE SINGLE SETTING 	banner_disable_{post_type} (genesis-settings)
	//                     TODO: CPT SETTINGS disable_banner (key?)
	// $disable_post_types = array();
	// $post_types         = get_post_types( array( 'public' => true ), 'objects' );
	// if ( $post_types ) {
	// 	foreach ( $post_types as $post_type ) {
	// 		$disable_post_types[$post_type->name] = $post_type->label;
	// 	}
	// }
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'banner_disable_post_types' ),
		array(
			'default'           => '',
			'type'              => 'option',
			'sanitize_callback' => '_mai_customizer_multicheck_sanitize_key',
		)
	);
	$wp_customize->add_control(
		new Mai_Customize_Control_Multicheck( $wp_customize,
			'banner_disable_post_types',
			array(
				'label'       => __( 'Disable on (post types)', 'mai-pro-engine' ),
				'description' => __( 'Disable on the following singular post type.', 'mai-pro-engine' ),
				'section'     => $section,
				'settings'    => _mai_customizer_get_field_name( $settings_field, 'banner_disable_post_types' ),
				'priority'    => 10,
				'choices'     => array(
					'post' => __( 'Posts', 'mai-pro-engine' ),
					'page' => __( 'Pages', 'mai-pro-engine' ),
				),
			)
		)
	);

	// Disable taxonomies.
	$disable_taxonomies = array();
	$taxonomies         = get_object_taxonomies( array( 'page', 'post' ), 'objects' );
	if ( $taxonomies ) {
		foreach ( $taxonomies as $taxo ) {
			$disable_taxonomies[$taxo->name] = $taxo->label;
		}
		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $settings_field, 'banner_disable_taxonomies' ),
			array(
				'default'           => '',
				'type'              => 'option',
				'sanitize_callback' => '_mai_customizer_multicheck_sanitize_key',
			)
		);
		$wp_customize->add_control(
			new Mai_Customize_Control_Multicheck( $wp_customize,
				'banner_disable_taxonomies',
				array(
					'label'       => __( 'Disable on (taxonomies)', 'mai-pro-engine' ),
					'description' => __( 'Disable on the following taxonomy archives.', 'mai-pro-engine' ),
					'section'     => $section,
					'settings'    => _mai_customizer_get_field_name( $settings_field, 'banner_disable_taxonomies' ),
					'priority'    => 10,
					'choices'     => $disable_taxonomies,
				)
			)
		);
	}

}

function mai_register_cpt_settings( $wp_customize, $post_type, $args ) {

	// Bail if we don't have a post type.
	if ( ! post_type_exists( $post_type ) ) {
		return;
	}

	$section          = sprintf( 'mai_%s_cpt_settings', $post_type );
	$settings_field   = GENESIS_CPT_ARCHIVE_SETTINGS_FIELD_PREFIX . $post_type;
	$genesis_settings = 'genesis-settings';
	$post_type_object = get_post_type_object( $post_type );
	$prefix           = sprintf( '%s_', $post_type );

	// Mai {post type name} Settings.
	$wp_customize->add_section(
		$section,
		array(
			'title'    => sprintf( __( 'Mai %s Settings', 'mai-pro-engine' ), $post_type_object->label ),
			'priority' => '35',
		)
	);

	// Banner Image
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'banner_id' ),
		array(
			'default'           => '',
			'type'              => 'option',
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control( $wp_customize,
		$prefix . 'banner_id',
		array(
			'label'           => __( 'Default Banner Image', 'mai-pro-engine' ),
			// 'description'     => __( 'Set a default banner image. Can be overridden per post/page.', 'mai-pro-engine' ),
			'section'         => $section,
			'settings'        => _mai_customizer_get_field_name( $genesis_settings, 'banner_id' ),
			'active_callback' => function() use ( $wp_customize, $genesis_settings ) {
				return _mai_customizer_is_banner_area_enabled( $wp_customize, $genesis_settings );
			},
		)
	) );

	// Hide Banner singular (saves to genesis-settings option).
	$hide_banner_key = sprintf( 'hide_banner_%s', $post_type );
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $genesis_settings, $hide_banner_key ),
		array(
			'default'           => '',
			'type'              => 'option',
		)
	);
	$wp_customize->add_control(
		$hide_banner_key,
		array(
			'label'    => __( 'Hide Banner on single entries', 'mai-pro-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $genesis_settings, $hide_banner_key ),
			'priority' => 10,
			'type'     => 'checkbox',
			'active_callback' => function() use ( $wp_customize, $genesis_settings ) {
				return _mai_customizer_is_banner_area_enabled( $wp_customize, $genesis_settings );
			},
		)
	);

	// Hide for archive here!?!?!?

	// Columns.
	if ( isset( $args['columns'] ) ) {

		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $settings_field, 'columns' ),
			array(
				'default'           => $args['columns'],
				'type'              => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);
		$wp_customize->add_control(
			$prefix . 'columns',
			array(
				'label'       => __( 'Archives: Columns', 'mai-pro-engine' ),
				'description' => __( 'Display content in multiple columns.', 'mai-pro-engine' ),
				'section'     => $section,
				'settings'    => _mai_customizer_get_field_name( $settings_field, 'columns' ),
				'priority'    => 10,
				'type'        => 'select',
				'choices'     => array(
					'1' => __( 'None', 'mai-pro-engine' ),
					'2' => __( '2', 'mai-pro-engine' ),
					'3' => __( '3', 'mai-pro-engine' ),
					'4' => __( '4', 'mai-pro-engine' ),
					'6' => __( '6', 'mai-pro-engine' ),
				),
			)
		);

	}

	// Align.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'content_archive_align' ),
		array(
			'default'           => $args['content_archive_align'],
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_key',
		)
	);
	$wp_customize->add_control(
		$prefix . 'content_archive_align',
		array(
			'label'    => __( 'Archives: Align Content', 'mai-pro-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'content_archive_align' ),
			'priority' => 10,
			'type'     => 'select',
			'choices'  => array(
				''       => __( '- None -', 'genesis' ),
				'left'   => __( 'Left', 'genesis' ),
				'center' => __( 'Center', 'genesis' ),
				'right'  => __( 'Right', 'genesis' ),
			),
		)
	);

	// Content.
	if ( isset( $args['content_archive'] ) ) {

		$supports_editor  = post_type_supports( $post_type, 'editor' );
		$supports_excerpt = post_type_supports( $post_type, 'excerpt' );

		if ( $supports_editor || $supports_excerpt ) {

			$content_archive_choices = array(
				'none' => __( 'No content', 'mai-pro-engine' ),
			);

			if ( $supports_editor ) {
				$content_archive_choices['full'] = __( 'Entry content', 'genesis' );
			}

			if ( $supports_excerpt ) {
				$content_archive_choices['excerpts'] = __( 'Entry excerpts', 'genesis' );
			}

			// Content Type.
			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $settings_field, 'content_archive' ),
				array(
					'default'           => $args['content_archive'],
					'type'              => 'option',
					'sanitize_callback' => 'sanitize_key',
				)
			);
			$wp_customize->add_control(
				$prefix . 'content_archive',
				array(
					'label'       => __( 'Archives: Content', 'mai-pro-engine' ),
					'section'     => $section,
					'settings'    => _mai_customizer_get_field_name( $settings_field, 'content_archive' ),
					'priority'    => 10,
					'type'        => 'select',
					'choices'     => $content_archive_choices,
				)
			);

			// Content Limit.
			if ( isset( $args['content_archive_limit'] ) ) {

				$wp_customize->add_setting(
					_mai_customizer_get_field_name( $settings_field, 'content_archive_limit' ),
					array(
						'default'           => $args['content_archive_limit'],
						'type'              => 'option',
						'sanitize_callback' => 'absint',
					)
				);
				$wp_customize->add_control(
					$prefix . 'content_archive_limit',
					array(
						'label'           => __( 'Archives: Limit content to how many characters?', 'mai-pro-engine' ),
						'description'     => __( '(0 for no limit)', 'mai-pro-engine' ),
						'section'         => $section,
						'settings'        => _mai_customizer_get_field_name( $settings_field, 'content_archive_limit' ),
						'priority'        => 10,
						'type'            => 'number',
						'active_callback' => function() use ( $wp_customize, $settings_field ) {
							return ( 'none' != $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'content_archive' ) )->value() );
						},
					)
				);

			}

		}

	}

	// Featured Image.
	if ( isset( $args['content_archive_thumbnail'] ) && post_type_supports( $post_type, 'thumbnail' ) ) {

		// Enable
		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $settings_field, 'content_archive_thumbnail' ),
			array(
				'default'           => _mai_customizer_sanitize_one_zero( $args['content_archive_thumbnail'] ),
				'type'              => 'option',
				'sanitize_callback' => '_mai_customizer_sanitize_one_zero',
			)
		);
		$wp_customize->add_control(
			$prefix . 'content_archive_thumbnail',
			array(
				'label'    => __( 'Archives: Featured Image', 'genesis' ),
				'section'  => $section,
				'settings' => _mai_customizer_get_field_name( $settings_field, 'content_archive_thumbnail' ),
				'type'     => 'select',
				'choices'  => array(
					1 => __( 'Show Image', 'mai-pro-engine' ),
					0 => __( 'Hide Image', 'mai-pro-engine' ),
				),
			)
		);

		// Image Location.
		if ( isset( $args['image_location'] ) ) {

			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $settings_field, 'image_location' ),
				array(
					'default'           => $args['image_location'],
					'type'              => 'option',
					'sanitize_callback' => 'sanitize_key',
				)
			);
			$wp_customize->add_control(
				$prefix . 'image_location',
				array(
					'label'    => __( 'Archives: Image Location', 'genesis' ),
					'section'  => $section,
					'settings' => _mai_customizer_get_field_name( $settings_field, 'image_location' ),
					'priority' => 10,
					'type'     => 'select',
					'choices'  => array(
						'background'     => __( 'Background Image', 'mai-pro-engine' ),
						'before_entry'   => __( 'Before Entry', 'mai-pro-engine' ),
						'before_title'   => __( 'Before Title', 'mai-pro-engine' ),
						'after_title'    => __( 'After Title', 'mai-pro-engine' ),
						'before_content' => __( 'Before Content', 'mai-pro-engine' ),
					),
					'active_callback' => function() use ( $wp_customize, $settings_field ) {
						return (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'content_archive_thumbnail' ) )->value();
					},
				)
			);

		}

		// Image Size.
		if ( isset( $args['image_size'] ) ) {

			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $settings_field, 'image_size' ),
				array(
					'default'           => $args['image_size'],
					'type'              => 'option',
					'sanitize_callback' => 'sanitize_key',
				)
			);
			$wp_customize->add_control(
				$prefix . 'image_size',
				array(
					'label'           => __( 'Archives: Image Size', 'genesis' ),
					'section'         => $section,
					'settings'        => _mai_customizer_get_field_name( $settings_field, 'image_size' ),
					'priority'        => 10,
					'type'            => 'select',
					'choices'         => _mai_customizer_get_image_sizes_config(),
					'active_callback' => function() use ( $wp_customize, $settings_field ) {
						return (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'content_archive_thumbnail' ) )->value();
					},
				)
			);

		}

		// Image Alignment.
		if ( isset( $args['image_alignment'] ) ) {

			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $settings_field, 'image_alignment' ),
				array(
					'default'           => $args['image_alignment'],
					'type'              => 'option',
					'sanitize_callback' => 'sanitize_key',
				)
			);
			$wp_customize->add_control(
				$prefix . 'image_alignment',
				array(
					'label'    => __( 'Archives: Image Alignment', 'genesis' ),
					'section'  => $section,
					'settings' => _mai_customizer_get_field_name( $settings_field, 'image_alignment' ),
					'priority' => 10,
					'type'     => 'select',
					'choices'  => array(
						''            => __( '- None -', 'genesis' ),
						'aligncenter' => __( 'Center', 'genesis' ),
						'alignleft'   => __( 'Left', 'genesis' ),
						'alignright'  => __( 'Right', 'genesis' ),
					),
					'active_callback' => function() use ( $wp_customize, $settings_field ) {
						// Showing featured image and background is not image location.
						return (bool) ( $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'content_archive_thumbnail' ) )->value() && ( 'background' != $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'image_location' ) )->value() ) );
					},
				)
			);

		}

	}
	// Entry Meta.
	if ( isset( $args['remove_meta'] ) && ( post_type_supports( $post_type, 'genesis-entry-meta-before-content' ) || post_type_supports( $post_type, 'genesis-entry-meta-after-content' ) ) ) {

		$remove_meta_choices = array();

		if ( post_type_supports( $post_type, 'genesis-entry-meta-before-content' ) ) {
			$remove_meta_choices['post_info'] = __( 'Remove Post Info', 'mai-pro-engine' );
		}

		if ( post_type_supports( $post_type, 'genesis-entry-meta-after-content' ) ) {
			$remove_meta_choices['post_meta'] = __( 'Remove Post Meta', 'mai-pro-engine' );
		}

		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $settings_field, 'remove_meta' ),
			array(
				'default'           => $args['remove_meta'],
				'type'              => 'option',
				'sanitize_callback' => '_mai_customizer_multicheck_sanitize_key',
			)
		);
		$wp_customize->add_control(
			new Mai_Customize_Control_Multicheck( $wp_customize,
				$prefix . 'remove_meta',
				array(
					'label'    => __( 'Archives: Entry Meta', 'mai-pro-engine' ),
					'section'  => $section,
					'settings' => _mai_customizer_get_field_name( $settings_field, 'remove_meta' ),
					'priority' => 10,
					'choices'  => $remove_meta_choices,
				)
			)
		);

	}

	// Entry Meta single.
	$remove_meta_single = sprintf( 'remove_meta_%s', $post_type );
	if ( isset( $args[$remove_meta_single] ) && ( post_type_supports( $post_type, 'genesis-entry-meta-before-content' ) || post_type_supports( $post_type, 'genesis-entry-meta-after-content' ) ) ) {

		$remove_meta_choices = array();

		if ( post_type_supports( $post_type, 'genesis-entry-meta-before-content' ) ) {
			$remove_meta_choices['post_info'] = __( 'Remove Post Info', 'mai-pro-engine' );
		}

		if ( post_type_supports( $post_type, 'genesis-entry-meta-after-content' ) ) {
			$remove_meta_choices['post_meta'] = __( 'Remove Post Meta', 'mai-pro-engine' );
		}

		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $genesis_settings, $remove_meta_single ),
			array(
				'default'           => $args[$remove_meta_single],
				'type'              => 'option',
				'sanitize_callback' => '_mai_customizer_multicheck_sanitize_key',
			)
		);
		$wp_customize->add_control(
			new Mai_Customize_Control_Multicheck( $wp_customize,
				$remove_meta_single,
				array(
					'label'    => __( 'Archives: Entry Meta', 'mai-pro-engine' ),
					'section'  => $section,
					'settings' => _mai_customizer_get_field_name( $genesis_settings, $remove_meta_single ),
					'priority' => 10,
					'choices'  => $remove_meta_choices,
				)
			)
		);

	}

	// Posts Per Page.
	if ( isset( $args['posts_per_page'] ) ) {

		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $settings_field, 'posts_per_page' ),
			array(
				'default'           => $args['posts_per_page'],
				'type'              => 'option',
				// 'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			$prefix . 'posts_per_page',
			array(
				'label'    => __( 'Archives: Entries Per Page', 'mai-pro-engine' ),
				'section'  => $section,
				'settings' => _mai_customizer_get_field_name( $settings_field, 'posts_per_page' ),
				'priority' => 10,
				'type'     => 'number',
			)
		);

	}

	// Posts Nav.
	if ( isset( $args['posts_nav'] ) ) {

		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $settings_field, 'posts_nav' ),
			array(
				'default'           => $args['posts_nav'],
				'type'              => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);
		$wp_customize->add_control(
			$prefix . 'posts_nav',
			array(
				'label'    => __( 'Archives: Pagination', 'genesis' ),
				'section'  => $section,
				'settings' => _mai_customizer_get_field_name( $settings_field, 'posts_nav' ),
				'priority' => 10,
				'type'     => 'select',
				'choices'  => array(
					'prev-next' => __( 'Previous / Next', 'genesis' ),
					'numeric'   => __( 'Numeric', 'genesis' ),
				),
			)
		);

	}

	// Archive Layout.
	if ( isset( $args['layout'] ) ) {

		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $settings_field, 'layout' ),
			array(
				'default'           => $args['layout'],
				'type'              => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);
		$wp_customize->add_control(
			$prefix . 'layout',
			array(
				'label'    => __( 'Archives: Layout', 'mai-pro-engine' ),
				'section'  => $section,
				'settings' => _mai_customizer_get_field_name( $settings_field, 'layout' ),
				'priority' => 10,
				'type'     => 'select',
				'choices'  => array_merge( array( '' => __( '- Archives Default -', 'mai-pro-engine' ) ), genesis_get_layouts_for_customizer() ),
			)
		);

	}

	// Single layout (saves to genesis-settings option).
	$single_key = sprintf( 'layout_%s', $post_type );
	if ( isset( $args[$single_key]  )) {

		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $genesis_settings, $single_key ),
			array(
				'default'           => $args[$single_key],
				'type'              => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);
		$wp_customize->add_control(
			$single_key,
			array(
				'label'    => __( 'Single: Layout', 'mai-pro-engine' ),
				'section'  => $section,
				'settings' => _mai_customizer_get_field_name( $genesis_settings, $single_key ),
				'priority' => 10,
				'type'     => 'select',
				'choices'  => array_merge( array( '' => __( '- Site Default -', 'mai-pro-engine' ) ), genesis_get_layouts_for_customizer() ),
			)
		);

	}

}

add_action( 'init', 'mai_cpt_settings_init', 999 );
function mai_cpt_settings_init() {
	/**
	 * Get post types.
	 * Applies apply_filters( 'genesis_cpt_archives_args', $args ); filter.
	 */
	$post_types = genesis_get_cpt_archive_types();

	if ( ! $post_types ) {
		return;
	}

	foreach ( $post_types as $post_type => $object ) {

		$args = array(
			'columns'                          => genesis_get_option( 'columns' ),
			'content_archive_align'            => genesis_get_option( 'content_archive_align' ), // TODO: Add an align field to align archive content!!!!
			sprintf( 'layout_%s', $post_type ) => '', // Single
			'layout'                           => '', // Archive
			'posts_per_page'                   => 12, // get_option( 'posts_per_page' ) would be another db hit
			'posts_nav'                        => genesis_get_option( 'posts_nav' ),
		);

		$supports = get_all_post_type_supports( $post_type );

		if ( isset( $supports['genesis-entry-meta-before-content'] ) || isset( $supports['genesis-entry-meta-after-content'] ) ) {
			$args['remove_meta']               = genesis_get_option( 'remove_meta' );
			$remove_meta_single                = sprintf( 'remove_meta_%s', $post_type );
			$args[$remove_meta_single]         = genesis_get_option( 'remove_meta_singular' ); // Use the default content archive setting
		}

		if ( isset( $supports['editor'] ) || isset( $supports['excerpt'] ) ) {
			$args['content_archive']           = genesis_get_option( 'content_archive' );
			$args['content_archive_limit']     = genesis_get_option( 'content_archive_limit' );
			$args['more_link']                 = genesis_get_option( 'more_link' );
			$args['more_link_text']            = genesis_get_option( 'more_link_text' );
		}

		if ( isset( $supports['thumbnail'] ) ) {
			$args['content_archive_thumbnail'] = genesis_get_option( 'content_archive_thumbnail' );
			$args['image_location']            = genesis_get_option( 'image_location' );
			$args['image_size']                = genesis_get_option( 'image_size' );
			$args['image_alignment']           = genesis_get_option( 'image_alignment' );
		}

		// Allow filter to easily modify settings for each post type.
		$args = apply_filters( 'mai_cpt_settings', $args, $post_type );

		// Do the settings.
		mai_do_cpt_settings( $post_type, $args );
	}
}

/**
 * Define the archive settings for a post type.
 * Args should either be false (to disable) or provide the default setting (to enable).
 *
 * This should be hooked into 'init' with a late priority (after 10) to ensure post_types are registered.
 *
 * @param   string  $post_type  The post type name.
 * @param   array   $args       The args to enable, with their default value. Include only the fields you want.
 *
 * @return  void
 */
function mai_do_cpt_settings( $post_type, $args ) {

	// Bail if we don't have a post type.
	if ( ! post_type_exists( $post_type ) ) {
		return;
	}

	// Bail if no args.
	if ( ! $args ) {
		return;
	}

	// Make sure the post type has g cpt archive support, so the correct actions and filters run as a default.
	// if ( ! genesis_has_post_type_archive_support( $post_type ) ) {
		// Genesis CPT Archive Support
		// add_post_type_support( $post_type, 'genesis-cpt-archives-settings' );
		add_post_type_support( $post_type, 'mai-cpt-settings' );
	// }

	$single_key = sprintf( 'layout_%s', $post_type );

	// Defaults.
	$defaults = array(
		$single_key                 => '',
		'layout'                    => '',
		'columns'                   => 1,
		'content_archive'           => 'unset',
		'content_archive_limit'     => 'unset',
		'more_link'                 => 'unset',
		'more_link_text'            => 'unset',
		'content_archive_thumbnail' => 'unset',
		'image_location'            => 'unset',
		'image_size'                => 'unset',
		'image_alignment'           => 'unset',
		'remove_meta'               => 'unset',
		'posts_per_page'            => 'unset',
		'posts_nav'                 => 'unset',
	);

	// Parse.
	$args = wp_parse_args( $args, $defaults );

	// Sanitize.
	$args = array(
		$single_key                 => sanitize_key( $args[$single_key] ),
		'layout'                    => sanitize_key( $args['layout'] ),
		'columns'                   => (string) absint( $args['columns'] ),
		'content_archive'           => ( 'unset' !== $args['content_archive'] ) ? sanitize_key( $args['content_archive'] ) : 'unset',
		'content_archive_limit'     => ( 'unset' !== $args['content_archive_limit'] ) ? sanitize_key( $args['content_archive_limit'] ) : 'unset',
		'content_archive_thumbnail' => ( ( 'unset' !== $args['content_archive_thumbnail'] ) && post_type_supports( $post_type, 'thumbnail' ) ) ? sanitize_key( $args['content_archive_thumbnail'] ) : 'unset',
		'image_location'            => ( 'unset' !== $args['image_location'] ) ? sanitize_key( $args['image_location'] ) : 'unset',
		'image_size'                => ( 'unset' !== $args['image_size'] ) ? sanitize_key( $args['image_size'] ) : 'unset',
		'image_alignment'           => ( 'unset' !== $args['image_alignment'] ) ? sanitize_key( $args['image_alignment'] ) : 'unset',
		'more_link'                 => ( 'unset' !== $args['more_link'] ) ? sanitize_key( $args['more_link'] ) : 'unset',
		'more_link_text'            => ( 'unset' !== $args['more_link_text'] ) ? sanitize_key( $args['more_link_text'] ) : 'unset',
		'remove_meta'               => ( ( 'unset' !== $args['remove_meta'] ) && ( post_type_supports( $post_type, 'genesis-entry-meta-before-content' ) || post_type_supports( $post_type, 'genesis-entry-meta-after-content' ) ) ) ? sanitize_key( $args['remove_meta'] ) : 'unset',
		'posts_per_page'            => ( 'unset' !== $args['posts_per_page'] ) ? absint( $args['posts_per_page'] ) : 'unset',
		'posts_nav'                 => ( 'unset' !== $args['posts_nav'] ) ? sanitize_key( $args['posts_nav'] ) : 'unset',
	);

	$settings_fields = $args;
	$unset    = array();

	// Unset the 'unset' items.
	foreach ( array_keys( $settings_fields, 'unset', true ) as $key ) {
		$unset[$key] = $settings_fields[$key];
		unset( $settings_fields[$key] );
	}

	// If in admin or viewing customizer.
	if ( is_admin() or is_customize_preview() ) {

		$options = get_option( GENESIS_CPT_ARCHIVE_SETTINGS_FIELD_PREFIX . $post_type );

		// If CPT options exist.
		if ( $options ) {
			/**
			 * Get any options that need to be unset.
			 * Returns an associative array containing all the entries of array1 which have keys that are present in all arguments.
			 */
			$unset = array_intersect_key( $unset, $options );
			/**
			 * If we have any items to update.
			 * This should only happen if/when the mai_cpt_settings() args change after first being setup.
			 */
			if ( ! empty( $unset ) ) {
				genesis_update_settings( $unset, GENESIS_CPT_ARCHIVE_SETTINGS_FIELD_PREFIX . $post_type );
			}
		}
		// No options, let's setup some defaults.
		else {
			if ( isset( $settings_fields[$single_key] ) ) {
				// Unset $single_key because this is stored in theme settings, not cpt archive settings.
				unset( $settings_fields[$single_key] );
			}
			genesis_update_settings( $settings_fields, GENESIS_CPT_ARCHIVE_SETTINGS_FIELD_PREFIX . $post_type );
		}

	}

	// Register the archive settings.
	add_action( 'customize_register', function( $wp_customize ) use ( $post_type, $settings_fields ) {
		mai_register_cpt_settings( $wp_customize, $post_type, $settings_fields );
	}, 22 );

}

add_action( 'customize_register', 'mai_register_customize_control_multicheck' );
function mai_register_customize_control_multicheck() {

	/**
	 * Multiple checkbox customize control class.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	class Mai_Customize_Control_Multicheck extends WP_Customize_Control {

		/**
		 * The type of customize control being rendered.
		 *
		 * @since  1.0.0
		 * @access public
		 * @var    string
		 */
		public $type = 'multicheck';

		/**
		 * Enqueue scripts/styles.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function enqueue() {
			// Use minified files if script debug is not being used.
			$suffix = mai_get_suffix();
			// Enqueue.
			wp_enqueue_script( 'mai-customize-controls', MAI_PRO_ENGINE_PLUGIN_URL . "assets/js/customize-controls{$suffix}.js", array( 'jquery' ) );
		}

		/**
		 * Displays the control content.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function render_content() {

			if ( empty( $this->choices ) ) {
				return;
			}

			if ( ! empty( $this->label ) ) {
				printf( '<span class="customize-control-title">%s</span>', esc_html( $this->label ) );
			}

			if ( ! empty( $this->description ) ) {
				printf( '<span class="description customize-control-description">%s</span>', $this->description );
			}

			$multi_values = ! is_array( $this->value() ) ? explode( ',', $this->value() ) : $this->value();

			?>
			<ul>
				<?php foreach ( $this->choices as $value => $label ) { ?>
					<li>
						<label>
							<input type="checkbox" value="<?php echo esc_attr( $value ); ?>" <?php checked( in_array( $value, $multi_values ) ); ?> />
							<?php echo esc_html( $label ); ?>
						</label>
					</li>
				<?php } ?>
			</ul>

			<input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( implode( ',', $multi_values ) ); ?>" />
			<?php

		}

	}

}

function _mai_customizer_is_banner_area_enabled( $wp_customize, $settings_field ) {
	return $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'enable_banner_area' ) )->value();
}

/**
 * Get field name attribute value.
 *
 * @param   string  $name Option name.
 * @return  string  Option name as key of settings field.
 */
function _mai_customizer_get_field_name( $settings_field, $name ) {
	return sprintf( '%s[%s]', $settings_field, $name );
}

/**
 * Get the image sizes array for Kirki.
 *
 * @return  array
 */
function _mai_customizer_get_image_sizes_config() {
	// Get our image size options
	$sizes   = genesis_get_image_sizes();
	$options = array();
	foreach ( $sizes as $index => $value ) {
		$options[$index] = sprintf( '%s (%s x %s)', $index, $value['width'], $value['height'] );
	}
	return $options;
}

function _mai_customizer_multicheck_sanitize_key( $values ) {
	$multi_values = ! is_array( $values ) ? explode( ',', $values ) : $values;
	return ! empty( $multi_values ) ? array_map( 'sanitize_key', $multi_values ) : array();
}

function _mai_customizer_sanitize_one_zero( $value ) {
	return absint( filter_var( $value, FILTER_VALIDATE_BOOLEAN ) );
}

function _mai_customizer_sanitize_bool( $value ) {
	return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
}
