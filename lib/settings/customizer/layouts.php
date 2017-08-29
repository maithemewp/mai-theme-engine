<?php

/**
 * Register new Customizer elements.
 *
 * @param WP_Customize_Manager $wp_customize WP_Customize_Manager instance.
 */
add_action( 'customize_register', 'mai_register_customizer_site_layouts_settings', 20 );
function mai_register_customizer_site_layouts_settings( $wp_customize ) {

	/* **************** *
	 * Mai Site Layouts *
	 * **************** */

	// Remove Genesis "Content Archives" section.
	$wp_customize->remove_section( 'genesis_layout' );

	$section        = 'mai_site_layouts';
	$settings_field = 'genesis-settings';
	$post_type      = 'post';

	// Section.
	$wp_customize->add_section(
		$section,
		array(
			'title'    => __( 'Mai Site Layouts', 'mai-pro-engine' ),
			'priority' => '40',
		)
	);

	// Default Layout.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'site_layout' ),
		array(
			'default'           => genesis_get_default_layout(),
			'type'              => 'option',
			// 'sanitize_callback' => 'sanitize_key',
		)
	);
	$wp_customize->add_control(
		'site_layout',
		array(
			'label'    => __( 'Default Layout', 'mai-pro-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'site_layout' ),
			'priority' => 10,
			'type'     => 'select',
			'choices'  => genesis_get_layouts_for_customizer(),
		)
	);

	// Archive Layout.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'layout_archive' ),
		array(
			'default'           => mai_get_default_option( 'layout_archive' ),
			'type'              => 'option',
			// 'sanitize_callback' => 'sanitize_key',
		)
	);
	$wp_customize->add_control(
		'layout_archive',
		array(
			'label'    => __( 'Archives', 'mai-pro-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'layout_archive' ),
			'priority' => 10,
			'type'     => 'select',
			'choices'  => array_merge( array( '' => __( '- Site Default -', 'mai-pro-engine' ) ), genesis_get_layouts_for_customizer() ),
		)
	);

	// Pages.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'layout_page' ),
		array(
			'default'           => mai_get_default_option( 'layout_page' ),
			'type'              => 'option',
			// 'sanitize_callback' => 'sanitize_key',
		)
	);
	$wp_customize->add_control(
		'layout_page',
		array(
			'label'    => __( 'Pages', 'mai-pro-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'layout_page' ),
			'priority' => 10,
			'type'     => 'select',
			'choices'  => array_merge( array( '' => __( '- Site Default -', 'mai-pro-engine' ) ), genesis_get_layouts_for_customizer() ),
		)
	);

	// Posts.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'layout_post' ),
		array(
			'default'           => mai_get_default_option( 'layout_post' ),
			'type'              => 'option',
			// 'sanitize_callback' => 'sanitize_key',
		)
	);
	$wp_customize->add_control(
		'layout_post',
		array(
			'label'    => __( 'Posts', 'mai-pro-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'layout_post' ),
			'priority' => 10,
			'type'     => 'select',
			'choices'  => array_merge( array( '' => __( '- Site Default -', 'mai-pro-engine' ) ), genesis_get_layouts_for_customizer() ),
		)
	);

}
