<?php

/**
 * Register new Customizer elements.
 *
 * @access  private
 *
 * @param   WP_Customize_Manager $wp_customize WP_Customize_Manager instance.
 */
add_action( 'customize_register', 'mai_register_customizer_header_footer_settings', 20 );
function mai_register_customizer_header_footer_settings( $wp_customize ) {

	/* ************ *
	 * Mai Settings *
	 * ************ */

	$section        = 'mai_header_footer';
	$settings_field = 'genesis-settings';

	// Section.
	$wp_customize->add_section(
		$section,
		array(
			'title'    => __( 'Mai Header & Footer', 'mai-theme-engine' ),
			'priority' => '35',
		)
	);

	// Header style.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'header_style' ),
		array(
			'default'           => sanitize_key( mai_get_default_option( 'header_style' ) ),
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_key',
		)
	);
	$wp_customize->add_control(
		'header_style',
		array(
			'label'    => __( 'Header style', 'mai-theme-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'header_style' ),
			'type'     => 'select',
			'choices'  => array(
				'standard'      => __( 'Standard Header' ),
				'sticky'        => __( 'Sticky Header' ),
				'reveal'        => __( 'Reveal Header' ),
				'sticky_shrink' => __( 'Sticky/Shrink Header' ),
				'reveal_shrink' => __( 'Reveal/Shrink Header' ),
			),
		)
	);

	// Mobile menu.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'mobile_menu_style' ),
		array(
			'default'           => sanitize_key( mai_get_default_option( 'mobile_menu_style' ) ),
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_key',
		)
	);
	$wp_customize->add_control(
		'mobile_menu_style',
		array(
			'label'    => __( 'Mobile menu style', 'mai-theme-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'mobile_menu_style' ),
			'priority' => 10,
			'type'     => 'select',
			'choices'  => array(
				'standard' => __( 'Standard Menu', 'mai-theme-engine' ),
				'side'     => __( 'Side Menu', 'mai-theme-engine' ),
			),
		)
	);

	// Footer widgets.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'footer_widget_count' ),
		array(
			'default'           => absint( mai_get_default_option( 'footer_widget_count' ) ),
			'type'              => 'option',
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'footer_widget_count',
		array(
			'label'       => __( 'Footer widget areas', 'mai-theme-engine' ),
			'description' => __( 'Save and reload customizer to view changes.', 'mai-theme-engine' ),
			'section'     => $section,
			'settings'    => _mai_customizer_get_field_name( $settings_field, 'footer_widget_count' ),
			'priority'    => 10,
			'type'        => 'select',
			'choices'     => array(
				0 => __( '- None -', 'genesis' ),
				1 => __( '1', 'mai-theme-engine' ),
				2 => __( '2', 'mai-theme-engine' ),
				3 => __( '3', 'mai-theme-engine' ),
				4 => __( '4', 'mai-theme-engine' ),
				6 => __( '6', 'mai-theme-engine' ),
			),
		)
	);

}
