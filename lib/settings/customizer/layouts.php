<?php

/**
 * Register new Customizer elements.
 *
 * @access  private
 *
 * @param   WP_Customize_Manager $wp_customize WP_Customize_Manager instance.
 */
add_action( 'customize_register', 'mai_register_customizer_site_layout_settings', 20 );
function mai_register_customizer_site_layout_settings( $wp_customize ) {

	/* **************** *
	 * Mai Site Layouts *
	 * **************** */

	// Remove Genesis "Content Archives" section.
	$wp_customize->remove_section( 'genesis_layout' );

	$section        = 'mai_site_layout';
	$settings_field = 'genesis-settings';
	$post_type      = 'post';

	// Section.
	$wp_customize->add_section(
		$section,
		array(
			'title'    => __( 'Mai Site Layout', 'mai-theme-engine' ),
			'priority' => '40',
		)
	);

	// Default Layout.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'site_layout' ),
		array(
			'default'           => sanitize_key( genesis_get_default_layout() ),
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_key',
		)
	);
	$wp_customize->add_control(
		'site_layout',
		array(
			'label'    => __( 'Default Layout', 'mai-theme-engine' ),
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
			'default'           => sanitize_key( mai_get_default_option( 'layout_archive' ) ),
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_key',
		)
	);
	$wp_customize->add_control(
		'layout_archive',
		array(
			'label'    => __( 'Archives', 'mai-theme-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'layout_archive' ),
			'priority' => 10,
			'type'     => 'select',
			'choices'  => array_merge( array( '' => __( '- Site Default -', 'mai-theme-engine' ) ), genesis_get_layouts_for_customizer() ),
		)
	);

	// Pages.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'layout_page' ),
		array(
			'default'           => sanitize_key( mai_get_default_option( 'layout_page' ) ),
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_key',
		)
	);
	$wp_customize->add_control(
		'layout_page',
		array(
			'label'    => __( 'Pages', 'mai-theme-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'layout_page' ),
			'priority' => 10,
			'type'     => 'select',
			'choices'  => array_merge( array( '' => __( '- Site Default -', 'mai-theme-engine' ) ), genesis_get_layouts_for_customizer() ),
		)
	);

	// Posts.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'layout_post' ),
		array(
			'default'           => sanitize_key( mai_get_default_option( 'layout_post' ) ),
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_key',
		)
	);
	$wp_customize->add_control(
		'layout_post',
		array(
			'label'    => __( 'Posts', 'mai-theme-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'layout_post' ),
			'priority' => 10,
			'type'     => 'select',
			'choices'  => array_merge( array( '' => __( '- Site Default -', 'mai-theme-engine' ) ), genesis_get_layouts_for_customizer() ),
		)
	);

	// Boxed Containers.
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
				'label'       => __( 'Boxed Containers', 'mai-theme-engine' ),
				'description' => __( 'Display the following elements with a boxed look:', 'mai-theme-engine' ),
				'section'     => $section,
				'settings'    => _mai_customizer_get_field_name( $settings_field, 'boxed_elements' ),
				'priority'    => 10,
				'choices'     => array(
					'site_container'       => __( 'Site Container (fixed width)', 'mai-theme-engine' ),
					'content_sidebar_wrap' => __( 'Content Sidebar Wrap', 'mai-theme-engine' ),
					'content'              => __( 'Main Content', 'mai-theme-engine' ),
					'entry_singular'       => __( 'Single Posts/Entries', 'mai-theme-engine' ),
					'entry_archive'        => __( 'Archive Posts/Entries', 'mai-theme-engine' ),
					'sidebar'              => __( 'Primary Sidebar', 'mai-theme-engine' ),
					'sidebar_alt'          => __( 'Secondary Sidebar', 'mai-theme-engine' ),
					'sidebar_widgets'      => __( 'Primary Sidebar Widgets', 'mai-theme-engine' ),
					'sidebar_alt_widgets'  => __( 'Secondary Sidebar Widget', 'mai-theme-engine' ),
					'author_box'           => __( 'After Entry Author Box', 'mai-theme-engine' ),
					'after_entry_widgets'  => __( 'After Entry Widgets', 'mai-theme-engine' ),
					'adjacent_entry_nav'   => __( 'Previous/Next Entry Navigation', 'mai-theme-engine' ),
					'comment_wrap'         => __( 'Comments Wrap', 'mai-theme-engine' ),
					'comment'              => __( 'Comments', 'mai-theme-engine' ),
					'comment_respond'      => __( 'Comment Submission Form', 'mai-theme-engine' ),
					'pings'                => __( 'Pings and Trackbacks', 'mai-theme-engine' ),
				),
			)
		)
	);

}
