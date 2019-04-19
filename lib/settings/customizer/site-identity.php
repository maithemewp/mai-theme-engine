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

	$width         = get_theme_mod( 'custom_logo_width', 180 );
	$shrink_width  = get_theme_mod( 'custom_logo_shrink_width', absint( $width * .7 ) );

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
		'default'           => $width,
		'sanitize_callback' => 'absint',
		'theme_supports'    => array( 'custom-logo' ),
	) );
	$wp_customize->add_control( 'custom_logo_width', array(
		'label'           => esc_attr__( 'Width', 'mai-theme-engine' ),
		'section'         => 'title_tagline',
		'priority'        => 8,
		'type'            => 'text',
		'active_callback' => function() use ( $wp_customize ) {
			$logo = $wp_customize->get_setting( 'custom_logo' )->value();
			return ! empty( $logo );
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
		'type'     => 'text',
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
		'type'     => 'text',
	) );

	// Shrink & Mobile Logo Sizing.
	$wp_customize->add_setting( 'custom_logo_shrink_sizing_break',	array(
		'default' => '',
	) );
	$wp_customize->add_control(
		new Mai_Customize_Control_Break( $wp_customize,
			'custom_logo_shrink_sizing_break',
			array(
				'label'    => __( 'Shrink & Mobile Logo Sizing', 'mai-theme-engine' ),
				'section'  => 'title_tagline',
				'priority' => 8,
				'settings' => false,
			)
		)
	);

	// Shrink Logo Width.
	$wp_customize->add_setting( 'custom_logo_shrink_width', array(
		'default'           => absint( $shrink_width ),
		'sanitize_callback' => 'absint',
		'theme_supports'    => array( 'custom-logo' ),
	) );
	$wp_customize->add_control( 'custom_logo_shrink_width', array(
		'label'           => esc_attr__( 'Width', 'mai-theme-engine' ),
		'section'         => 'title_tagline',
		'priority'        => 8,
		'type'            => 'text',
		'active_callback' => function() use ( $wp_customize ) {
			$logo = $wp_customize->get_setting( 'custom_logo' )->value();
			return ! empty( $logo );
		},
	) );

	// Shrink Logo Top Margin.
	$wp_customize->add_setting( 'custom_logo_shrink_top', array(
		'default'           => 4,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'custom_logo_shrink_top', array(
		'label'    => esc_attr__( 'Top', 'mai-theme-engine' ),
		'section'  => 'title_tagline',
		'priority' => 8,
		'type'     => 'text',
	) );

	// Shrink Logo Bottom Margin.
	$wp_customize->add_setting( 'custom_logo_shrink_bottom', array(
		'default'           => 4,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'custom_logo_shrink_bottom', array(
		'label'    => esc_attr__( 'Bottom', 'mai-theme-engine' ),
		'section'  => 'title_tagline',
		'priority' => 8,
		'type'     => 'text',
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
