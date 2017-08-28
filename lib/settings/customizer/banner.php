<?php

/**
 * Register new Customizer elements.
 *
 * @param WP_Customize_Manager $wp_customize WP_Customize_Manager instance.
 */
add_action( 'customize_register', 'mai_register_banner_customizer_settings', 20 );
function mai_register_banner_customizer_settings( $wp_customize ) {

	/* *****************
	 * Mai Banner Area *
	 * *************** */

	$section        = 'mai_banner_area';
	$settings_field = 'genesis-settings';

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
				return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $settings_field );
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
				return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $settings_field );
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
				return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $settings_field );
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
				return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $settings_field );
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
				return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $settings_field );
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
				return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $settings_field );
			},
		)
	);

	// Disable post types.
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
				'active_callback' => function() use ( $wp_customize, $settings_field ) {
					return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $settings_field );
				},
			)
		)
	);

	/**
	 * Disable taxonomies.
	 * Only taxos registered to 'post' and 'page' post_type.
	 * Custom taxos (on CPT's) are handled on the CPT settings panel.
	 */
	$disable_taxonomies = array();
	$taxonomies         = get_object_taxonomies( 'post', 'objects' );
	if ( $taxonomies ) {
		foreach ( $taxonomies as $taxo ) {
			// If taxo is registered to more than one object.
			if ( count( (array) $taxo->object_type ) > 1 ) {
				continue;
			}
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
					'active_callback' => function() use ( $wp_customize, $settings_field ) {
						return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $settings_field );
					},
				)
			)
		);
	}

}
