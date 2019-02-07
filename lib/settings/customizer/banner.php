<?php

/**
 * Register new Customizer elements.
 *
 * @access  private
 *
 * @param   WP_Customize_Manager $wp_customize WP_Customize_Manager instance.
 */
add_action( 'customize_register', 'mai_register_banner_customizer_settings', 20 );
function mai_register_banner_customizer_settings( $wp_customize ) {

	/* *************** *
	 * Mai Banner Area *
	 * *************** */

	$section        = 'mai_banner_area';
	$settings_field = 'genesis-settings';

	// Section.
	$wp_customize->add_section(
		$section,
		array(
			'title'    => __( 'Mai Banner Area', 'mai-theme-engine' ),
			'priority' => '36',
		)
	);

	// Enable.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'enable_banner_area' ),
		array(
			'default'           => mai_sanitize_one_zero( mai_get_default_option( 'enable_banner_area' ) ),
			'type'              => 'option',
			'sanitize_callback' => 'mai_sanitize_one_zero',
		)
	);
	$wp_customize->add_control(
		'enable_banner_area',
		array(
			'label'    => __( 'Enable Banner Area', 'mai-theme-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'enable_banner_area' ),
			'priority' => 2,
			'type'     => 'checkbox',
		)
	);

	// Background color.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'banner_background_color' ),
		array(
			'default'           => sanitize_hex_color( mai_get_default_option( 'banner_background_color' ) ),
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control( $wp_customize,
		'banner_background_color',
		array(
			'label'           => __( 'Background Color', 'mai-theme-engine' ),
			'section'         => $section,
			'settings'        => _mai_customizer_get_field_name( $settings_field, 'banner_background_color' ),
			'priority'        => 4,
			'active_callback' => function() use ( $wp_customize, $settings_field ) {
				return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $settings_field );
			},
		)
	) );

	// Banner Image.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'banner_id' ),
		array(
			'default'           => absint( mai_get_default_option( 'banner_id' ) ),
			'type'              => 'option',
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control( $wp_customize,
		'banner_id',
		array(
			'label'           => __( 'Banner Image', 'mai-theme-engine' ),
			'description'     => __( 'Set a default banner image (min. 1600px wide) which auto-crops based on the section content and height. Image can be overridden per post/page.', 'mai-theme-engine' ),
			'section'         => $section,
			'settings'        => _mai_customizer_get_field_name( $settings_field, 'banner_id' ),
			'priority'        => 4,
			'active_callback' => function() use ( $wp_customize, $settings_field ) {
				return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $settings_field );
			},
		)
	) );

	// Banner featured image.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'banner_featured_image' ),
		array(
			'default'           => _mai_customizer_multicheck_sanitize_key( mai_get_default_option( 'banner_featured_image' ) ),
			'type'              => 'option',
			'sanitize_callback' => '_mai_customizer_multicheck_sanitize_key',
		)
	);
	$wp_customize->add_control(
		new Mai_Customize_Control_Multicheck( $wp_customize,
			'banner_featured_image',
			array(
				'label'       => __( 'Featured Image', 'mai-theme-engine' ),
				'description' => __( 'Use featured image as banner image on:', 'mai-theme-engine' ),
				'section'     => $section,
				'settings'    => _mai_customizer_get_field_name( $settings_field, 'banner_featured_image' ),
				'priority'    => 6,
				'choices'     => array(
					'page' => __( 'Pages', 'mai-theme-engine' ),
					'post' => __( 'Posts', 'mai-theme-engine' ),
				),
				'active_callback' => function() use ( $wp_customize, $settings_field ) {
					return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $settings_field );
				},
			)
		)
	);

	// Overlay.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'banner_overlay' ),
		array(
			'default'           => sanitize_key( mai_get_default_option( 'banner_overlay' ) ),
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_key',
		)
	);
	$wp_customize->add_control(
		'banner_overlay',
		array(
			'label'    => __( 'Overlay', 'mai-theme-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'banner_overlay' ),
			'priority' => 10,
			'type'     => 'select',
			'choices'  => array(
				''         => __( 'None', 'mai-theme-engine' ),
				'light'    => __( 'Light', 'mai-theme-engine' ),
				'dark'     => __( 'Dark', 'mai-theme-engine' ),
				'gradient' => __( 'Gradient', 'mai-theme-engine' ),
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
			'default'           => sanitize_key( mai_get_default_option( 'banner_inner' ) ),
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_key',
		)
	);
	$wp_customize->add_control(
		'banner_inner',
		array(
			'label'    => __( 'Inner styling', 'mai-theme-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'banner_inner' ),
			'priority' => 10,
			'type'     => 'select',
			'choices'  => array(
				''      => __( 'None', 'mai-theme-engine' ),
				'light' => __( 'Light Box', 'mai-theme-engine' ),
				'dark'  => __( 'Dark Box', 'mai-theme-engine' ),
			),
			'active_callback' => function() use ( $wp_customize, $settings_field ) {
				return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $settings_field );
			},
		)
	);

	// Height.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'banner_height' ),
		array(
			'default'           => sanitize_key( mai_get_default_option( 'banner_height' ) ),
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_key',
		)
	);
	$wp_customize->add_control(
		'banner_height',
		array(
			'label'    => __( 'Height', 'mai-theme-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'banner_height' ),
			'priority' => 10,
			'type'     => 'select',
			'choices'  => array(
				'xs' => __( 'Extra Small', 'mai-theme-engine' ),
				'sm' => __( 'Small', 'mai-theme-engine' ),
				'md' => __( 'Medium', 'mai-theme-engine' ),
				'lg' => __( 'Large', 'mai-theme-engine' ),
				'xl' => __( 'Extra Large', 'mai-theme-engine' ),
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
			'default'           => sanitize_key( mai_get_default_option( 'banner_content_width' ) ),
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_key',
		)
	);
	$wp_customize->add_control(
		'banner_content_width',
		array(
			'label'    => __( 'Content width', 'mai-theme-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'banner_content_width' ),
			'priority' => 10,
			'type'     => 'select',
			'choices'  => array(
				'auto' => __( 'Auto', 'mai-theme-engine' ),
				'xs'   => __( 'Extra Small', 'mai-theme-engine' ),
				'sm'   => __( 'Small', 'mai-theme-engine' ),
				'md'   => __( 'Medium', 'mai-theme-engine' ),
				'lg'   => __( 'Large', 'mai-theme-engine' ),
				'xl'   => __( 'Extra Large', 'mai-theme-engine' ),
				'full' => __( 'Full Width', 'mai-theme-engine' ),
			),
			'active_callback' => function() use ( $wp_customize, $settings_field ) {
				return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $settings_field );
			},
		)
	);

	// Content align.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'banner_align_content' ),
		array(
			'default'           => sanitize_key( mai_get_default_option( 'banner_align_content' ) ),
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_key',
		)
	);
	$wp_customize->add_control(
		'banner_align_content',
			array(
			'label'    => __( 'Content alignment', 'mai-theme-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'banner_align_content' ),
			'priority' => 10,
			'type'     => 'select',
			'choices'  => array(
				''             => __( 'None', 'mai-theme-engine' ),
				'left'         => __( 'Left', 'mai-theme-engine' ),
				'lefttop'      => __( 'Left Top', 'mai-theme-engine' ),
				'leftbottom'   => __( 'Left Bottom', 'mai-theme-engine' ),
				'center'       => __( 'Center', 'mai-theme-engine' ),
				'centertop'    => __( 'Center Top', 'mai-theme-engine' ),
				'centerbottom' => __( 'Center Bottom', 'mai-theme-engine' ),
				'right'        => __( 'Right', 'mai-theme-engine' ),
				'righttop'     => __( 'Right Top', 'mai-theme-engine' ),
				'rightbottom'  => __( 'Right Bottom', 'mai-theme-engine' ),
			),
			'active_callback' => function() use ( $wp_customize, $settings_field ) {
				return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $settings_field );
			},
		)
	);

	// Text align.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'banner_align_text' ),
		array(
			'default'           => sanitize_key( mai_get_default_option( 'banner_align_text' ) ),
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_key',
		)
	);
	$wp_customize->add_control(
		'banner_align_text',
			array(
			'label'    => __( 'Text alignment', 'mai-theme-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'banner_align_text' ),
			'priority' => 10,
			'type'     => 'select',
			'choices'  => array(
				''       => __( 'None', 'mai-theme-engine' ),
				'left'   => __( 'Left', 'mai-theme-engine' ),
				'center' => __( 'Center', 'mai-theme-engine' ),
				'right'  => __( 'Right', 'mai-theme-engine' ),
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
			'default'           => _mai_customizer_multicheck_sanitize_key( mai_get_default_option( 'banner_disable_post_types' ) ),
			'type'              => 'option',
			'sanitize_callback' => '_mai_customizer_multicheck_sanitize_key',
		)
	);
	$wp_customize->add_control(
		new Mai_Customize_Control_Multicheck( $wp_customize,
			'banner_disable_post_types',
			array(
				'label'       => __( 'Disable on (post types)', 'mai-theme-engine' ),
				'description' => __( 'Disable on the following singular post type.', 'mai-theme-engine' ),
				'section'     => $section,
				'settings'    => _mai_customizer_get_field_name( $settings_field, 'banner_disable_post_types' ),
				'priority'    => 10,
				'choices'     => array(
					'page' => __( 'Pages', 'mai-theme-engine' ),
					'post' => __( 'Posts', 'mai-theme-engine' ),
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
			// If taxo is not public or is registered to more than one object.
			if ( ! $taxo->public || ( count( (array) $taxo->object_type ) > 1 ) ) {
				continue;
			}
			$disable_taxonomies[$taxo->name] = $taxo->label;
		}
		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $settings_field, 'banner_disable_taxonomies' ),
			array(
				'default'           => _mai_customizer_multicheck_sanitize_key( mai_get_default_option( 'banner_disable_taxonomies' ) ),
				'type'              => 'option',
				'sanitize_callback' => '_mai_customizer_multicheck_sanitize_key',
			)
		);
		$wp_customize->add_control(
			new Mai_Customize_Control_Multicheck( $wp_customize,
				'banner_disable_taxonomies',
				array(
					'label'           => __( 'Disable on (taxonomies)', 'mai-theme-engine' ),
					'description'     => __( 'Disable on the following taxonomy archives.', 'mai-theme-engine' ),
					'section'         => $section,
					'settings'        => _mai_customizer_get_field_name( $settings_field, 'banner_disable_taxonomies' ),
					'priority'        => 10,
					'choices'         => $disable_taxonomies,
					'active_callback' => function() use ( $wp_customize, $settings_field ) {
						return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $settings_field );
					},
				)
			)
		);
	}

}
