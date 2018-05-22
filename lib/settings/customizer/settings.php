<?php

/**
 * Register new Customizer elements.
 *
 * @param WP_Customize_Manager $wp_customize WP_Customize_Manager instance.
 */
add_action( 'customize_register', 'mai_register_customizer_settings', 20 );
function mai_register_customizer_settings( $wp_customize ) {

	/* ************ *
	 * Mai Settings *
	 * ************ */

	$section        = 'mai_settings';
	$settings_field = 'genesis-settings';

	// Section.
	$wp_customize->add_section(
		$section,
		array(
			'title'    => __( 'Mai Settings', 'mai-theme-engine' ),
			'priority' => '35',
		)
	);

	// Header heading.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'header_customizer_heading' ),
		array(
			'default' => '',
			'type'    => 'option',
		)
	);
	$wp_customize->add_control(
		new Mai_Customize_Control_Content( $wp_customize,
			'header_customizer_heading',
			array(
				'label'       => __( 'Header', 'mai-theme-engine' ),
				'description' => __( 'These settings are disabled on mobile.', 'mai-theme-engine' ),
				'section'     => $section,
				'settings'    => false,
			)
		)
	);

	// Sticky Header.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'enable_sticky_header' ),
		array(
			'default'           => 0,
			'type'              => 'option',
			'sanitize_callback' => 'mai_sanitize_one_zero',
		)
	);
	$wp_customize->add_control(
		'enable_sticky_header',
		array(
			'label'    => __( 'Enable sticky header', 'mai-theme-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'enable_sticky_header' ),
			'type'     => 'checkbox',
		)
	);

	// Shrink Header.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'enable_shrink_header' ),
		array(
			'default'           => 0,
			'type'              => 'option',
			'sanitize_callback' => 'mai_sanitize_one_zero',
		)
	);
	$wp_customize->add_control(
		'enable_shrink_header',
		array(
			'label'    => __( 'Enable shrink header', 'mai-theme-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'enable_shrink_header' ),
			'type'     => 'checkbox',
		)
	);

	// Boxed Content.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'boxed_elements' ),
		array(
			'default'           => _mai_customizer_multicheck_sanitize_key( mai_get_default_option( 'boxed_elements' ) ),
			'type'              => 'option',
			'sanitize_callback' => '_mai_customizer_multicheck_sanitize_key',
		)
	);
	$wp_customize->add_control(
		new Mai_Customize_Control_Multicheck( $wp_customize,
			'boxed_elements',
			array(
				'label'       => __( 'Boxed Content', 'mai-theme-engine' ),
				'description' => __( 'Display the following elements with a boxed look:', 'mai-theme-engine' ),
				'section'     => $section,
				'settings'    => _mai_customizer_get_field_name( $settings_field, 'boxed_elements' ),
				'priority'    => 10,
				'choices'     => array(
					'entry'               => __( 'Content/Entries', 'mai-theme-engine' ),
					'sidebar'             => __( 'Primary Sidebar', 'mai-theme-engine' ),
					'sidebar_widgets'     => __( 'Primary Sidebar Widgets', 'mai-theme-engine' ),
					'sidebar_alt'         => __( 'Secondary Sidebar', 'mai-theme-engine' ),
					'sidebar_alt_widgets' => __( 'Secondary Sidebar Widget', 'mai-theme-engine' ),
					'author_box'          => __( 'After Entry Author Box', 'mai-theme-engine' ),
					'after_entry_widgets' => __( 'After Entry Widgets', 'mai-theme-engine' ),
					'adjacent_entry_nav'  => __( 'Previous/Next Entry Navigation', 'mai-theme-engine' ),
					'comments'            => __( 'Comments', 'mai-theme-engine' ),
					'comment_respond'     => __( 'Comment Submission Form', 'mai-theme-engine' ),
					'pings'               => __( 'Pings and Trackbacks', 'mai-theme-engine' ),
				),
			)
		)
	);

	// Footer widgets.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'footer_widget_count' ),
		array(
			'default'           => 2,
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
				0 => __( 'None', 'mai-theme-engine' ),
				1 => __( '1', 'mai-theme-engine' ),
				2 => __( '2', 'mai-theme-engine' ),
				3 => __( '3', 'mai-theme-engine' ),
				4 => __( '4', 'mai-theme-engine' ),
				6 => __( '6', 'mai-theme-engine' ),
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

}
