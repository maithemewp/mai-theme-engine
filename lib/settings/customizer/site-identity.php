<?php

/**
 * Register new Customizer elements.
 * Register priorty 10 since our actual settings are registered at 20.
 *
 * @access  private
 *
 * @param   WP_Customize_Manager $wp_customize WP_Customize_Manager instance.
 */
add_action( 'customize_register', 'mai_register_customizer_site_identity_settings', 10 );
function mai_register_customizer_site_identity_settings( $wp_customize ) {

	$logo       = $wp_customize->get_setting( 'custom_logo' )->value();
	$has_logo   = ! empty( $logo );
	$has_shrink = mai_has_shrink_header();

	/* ************* *
	 * Site Identity *
	 * ************* */

	// Logo Sizing.
	$wp_customize->add_setting( 'custom_logo_sizing_break',	array(
		'default' => '',
	) );
	$wp_customize->add_control(
		new Mai_Customize_Control_Break( $wp_customize,
			'custom_logo_sizing_break',
			array(
				'label'    => __( 'Logo Sizing', 'mai-theme-engine' ),
				'section'  => 'title_tagline',
				'priority' => 8,
				'settings' => false,
			)
		)
	);

	// Logo Width.
	$wp_customize->add_setting( 'custom_logo_width', array(
		'default'           => 180,
		'sanitize_callback' => 'absint',
		'theme_supports'    => array( 'custom-logo' ),
	) );
	$wp_customize->add_control( 'custom_logo_width', array(
		'label'           => esc_attr__( 'Width', 'mai-theme-engine' ),
		'section'         => 'title_tagline',
		'priority'        => 8,
		'type'            => 'number',
		'active_callback' => function() use ( $has_logo ) {
			return $has_logo;
		},
	) );

	// Logo Top Margin.
	$wp_customize->add_setting( 'custom_logo_top', array(
		'default'           => 24,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'custom_logo_top', array(
		'label'    => esc_attr__( 'Top', 'mai-theme-engine' ),
		'section'  => 'title_tagline',
		'priority' => 8,
		'type'     => 'number',
	) );

	// Logo Bottom Margin.
	$wp_customize->add_setting( 'custom_logo_bottom', array(
		'default'           => 24,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'custom_logo_bottom', array(
		'label'    => esc_attr__( 'Bottom', 'mai-theme-engine' ),
		'section'  => 'title_tagline',
		'priority' => 8,
		'type'     => 'number',
	) );

	// Shrunk Logo Sizing.
	$wp_customize->add_setting( 'custom_logo_shrink_sizing_break',	array(
		'default' => '',
	) );
	$wp_customize->add_control(
		new Mai_Customize_Control_Break( $wp_customize,
			'custom_logo_shrink_sizing_break',
			array(
				'label'    => __( 'Shrunk Logo Sizing', 'mai-theme-engine' ),
				'section'  => 'title_tagline',
				'priority' => 8,
				'settings' => false,
				'active_callback' => function() use ( $has_shrink ) {
					return $has_shrink;
				},
			)
		)
	);

	// Shrunk Logo Width.
	$wp_customize->add_setting( 'custom_logo_shrink_width', array(
		'default'           => 120,
		'sanitize_callback' => 'absint',
		'theme_supports'    => array( 'custom-logo' ),
	) );
	$wp_customize->add_control( 'custom_logo_shrink_width', array(
		'label'           => esc_attr__( 'Width', 'mai-theme-engine' ),
		'section'         => 'title_tagline',
		'priority'        => 8,
		'type'            => 'number',
		'active_callback' => function() use ( $has_logo, $has_shrink ) {
			return ( $has_logo && $has_shrink );
		},
	) );

	// Shrunk Logo Top Margin.
	$wp_customize->add_setting( 'custom_logo_shrink_top', array(
		'default'           => 4,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'custom_logo_shrink_top', array(
		'label'    => esc_attr__( 'Top', 'mai-theme-engine' ),
		'section'  => 'title_tagline',
		'priority' => 8,
		'type'     => 'number',
		'active_callback' => function() use ( $has_shrink ) {
			return $has_shrink;
		},
	) );

	// Shrunk Logo Bottom Margin.
	$wp_customize->add_setting( 'custom_logo_shrink_bottom', array(
		'default'           => 4,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'custom_logo_shrink_bottom', array(
		'label'    => esc_attr__( 'Bottom', 'mai-theme-engine' ),
		'section'  => 'title_tagline',
		'priority' => 8,
		'type'     => 'number',
		'active_callback' => function() use ( $has_shrink ) {
			return $has_shrink;
		},
	) );

	// Site Info.
	$wp_customize->add_setting( 'site_info_break', array(
		'default' => '',
	) );
	$wp_customize->add_control(
		new Mai_Customize_Control_Break( $wp_customize,
			'site_info_break',
			array(
				'label'    => __( 'Site Info', 'mai-theme-engine' ),
				'section'  => 'title_tagline',
				'priority' => 8,
				'settings' => false,
			)
		)
	);

}
