<?php
/**
 * Mai Theme.
 *
 * This file adds the Customizer additions to Mai Theme.
 *
 * @package Mai Theme
 * @author  Mike Hemberger
 * @license GPL-2.0+
 * @link    https://bizbudding.com/
 */


/**
 * Register settings and controls with the Customizer.
 *
 * @since 	1.0.0
 *
 * @param   object $wp_customize the customizer object.
 *
 * @return  void
 */
add_action( 'customize_register', 'mai_register_customizer_general' );
function mai_register_customizer_general( $wp_customize ) {

	$section = 'mai-settings';

	/*****************************************************
	 * Mai Theme Settings section
	 */

	$wp_customize->add_section( $section, array(
		'description' => __( 'Customize your theme with sitewide changes.', 'mai-pro-engine' ),
		'title'       => __( 'Mai Settings', 'mai-pro-engine' ),
		'priority'    => 35,
	) );

	/*****************************************************
	 * Enable Sticky Header setting
	 */

	$wp_customize->add_setting( 'enable_sticky_header', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( 'enable_sticky_header', array(
		'label'    => __( 'Enable sticky header', 'mai-pro-engine' ),
		'section'  => $section,
		'type'     => 'checkbox',
		'priority' => 5,
	) );

	/*****************************************************
	 * Enable Shrinking Header setting
	 */

	$wp_customize->add_setting( 'enable_shrink_header', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( 'enable_shrink_header', array(
		'label'    => __( 'Enable shrinking header', 'mai-pro-engine' ),
		'section'  => $section,
		'type'     => 'checkbox',
		'priority' => 5,
	) );

	/*****************************************************
	 * Enable Auto Display of featured image setting
	 */

	$wp_customize->add_setting( 'enable_singular_image', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( 'enable_singular_image', array(
		'label'    => __( 'Automatically display the featured image on single posts/pages', 'mai-pro-engine' ),
		'section'  => $section,
		'type'     => 'checkbox',
		'priority' => 5,
	) );

	/*****************************************************
	 * Footer Widget Count setting
	 */

	$wp_customize->add_setting( 'footer_widget_count', array(
		'default'           => '2',
		'sanitize_callback' => 'wp_strip_all_tags',
	) );

	$wp_customize->add_control( 'footer_widget_count', array(
		'label'			=> __( 'Footer widget areas', 'mai-pro-engine' ),
		'description'	=> __( 'Save and reload customizer to view changes', 'mai-pro-engine' ),
		'section'		=> $section,
		'priority'		=> 5,
		'type'			=> 'select',
		'choices'		=> array(
			1 => __( '1', 'mai-pro-engine' ),
			2 => __( '2', 'mai-pro-engine' ),
			3 => __( '3', 'mai-pro-engine' ),
			4 => __( '4', 'mai-pro-engine' ),
			6 => __( '6', 'mai-pro-engine' ),
		),
	) );

	/*****************************************************
	 * Mobile Menu Style setting
	 */

	$wp_customize->add_setting( 'mobile_menu_style', array(
		'default'           => 'standard',
		'sanitize_callback' => 'wp_strip_all_tags',
	) );

	$wp_customize->add_control( 'mobile_menu_style', array(
		'label'			=> __( 'Mobile menu style', 'mai-pro-engine' ),
		'section'		=> $section,
		'priority'		=> 5,
		'type'			=> 'select',
		'choices'		=> array(
			'standard'	=> __( 'Standard Menu', 'mai-pro-engine' ),
			'side'		=> __( 'Side Menu', 'mai-pro-engine' ),
		),
	) );

}

/**
 * Register settings and controls with the Customizer.
 *
 * @since 1.0.0
 *
 * @param  object $wp_customize the customizer object.
 */
add_action( 'customize_register', 'mai_register_customizer_settings' );
function mai_register_customizer_settings( $wp_customize ) {

	$section = 'mai-banner-area';

	/*****************************************************
	 * Banner Area section
	 */

	$wp_customize->add_section( $section, array(
		'title'       => __( 'Mai Banner Area', 'mai-pro-engine' ),
		'priority'    => 35,
	) );

	/*****************************************************
	 * Enable Banner Area
	 */

	$wp_customize->add_setting( 'enable_banner_area', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( 'enable_banner_area', array(
		'label'    => __( 'Enable the banner area', 'mai-pro-engine' ),
		'section'  => $section,
		'priority' => 5,
		'type'     => 'checkbox',
	) );

	/*****************************************************
	 * Banner BG Color
	 */

	$wp_customize->add_setting( 'banner_background_color', array(
		'default'           => '#f1f1f1',
		'sanitize_callback' => 'sanitize_hex_color'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'banner_background_color', array(
		'label'    => __( 'Background color', 'mai-pro-engine' ),
		'section'  => $section,
		'settings' => 'banner_background_color',
		'priority' => 5
	) ) );

	/*****************************************************
	 * Default Banner Image setting
	 */

	$wp_customize->add_setting( 'banner_id', array(
		'default'           => '',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'banner_id', array(
		'label'			=> __( 'Banner Image', 'mai-pro-engine' ),
		'description'	=> __( 'Set a default banner image. Can be overridden per post/page.', 'mai-pro-engine' ),
		'section'		=> $section,
		'settings'		=> 'banner_id',
		'priority'		=> 5,
	) ) );

	/*****************************************************
	 * Enable Banner Overlay
	 */

	$wp_customize->add_setting( 'banner_overlay', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'banner_overlay', array(
		'label'		=> __( 'Enable banner overlay', 'mai-pro-engine' ),
		'section'	=> $section,
		'priority'	=> 5,
		'type'		=> 'select',
		'choices'	=> array(
			''			=> __( '- None -', 'genesis' ),
			'light'		=> __( 'Light Overlay', 'mai-pro-engine' ),
			'dark'		=> __( 'Dark Overlay', 'mai-pro-engine' ),
			'gradient'	=> __( 'Gradient Overlay', 'mai-pro-engine' ),
		),
	) );

	/*****************************************************
	 * Enable Banner Inner styling
	 */

	$wp_customize->add_setting( 'banner_inner', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'banner_inner', array(
		'label'		=> __( 'Enable banner inner styling', 'mai-pro-engine' ),
		'section'	=> $section,
		'priority'	=> 5,
		'type'		=> 'select',
		'choices'	=> array(
			''		=> __( '- None -', 'genesis' ),
			'light'	=> __( 'Light Box', 'mai-pro-engine' ),
			'dark'	=> __( 'Dark Box', 'mai-pro-engine' ),
		),
	) );

	/*****************************************************
	 * Banner Content Width
	 */

	$wp_customize->add_setting( 'banner_content_width', array(
		'default'			=> 'auto',
		'sanitize_callback'	=> 'sanitize_key',
	) );

	$wp_customize->add_control( 'banner_content_width', array(
		'label'		=> __( 'Banner content width', 'mai-pro-engine' ),
		'section'	=> $section,
		'priority'	=> 5,
		'type'		=> 'select',
		'choices'	=> array(
			'auto' => __( 'Auto', 'mai-pro-engine' ),
			'xs'   => __( 'Extra Small', 'mai-pro-engine' ),
			'sm'   => __( 'Small', 'mai-pro-engine' ),
			'md'   => __( 'Medium', 'mai-pro-engine' ),
			'lg'   => __( 'Large', 'mai-pro-engine' ),
			'xl'   => __( 'Extra Large', 'mai-pro-engine' ),
			'full' => __( 'Full Width', 'mai-pro-engine' ),
		),
	) );

	/*****************************************************
	 * Banner Alignment
	 */

	$wp_customize->add_setting( 'banner_align', array(
		'default'			=> '',
		'sanitize_callback'	=> 'sanitize_key',
	) );

	$wp_customize->add_control( 'banner_align', array(
		'label'		=> __( 'Banner alignment', 'mai-pro-engine' ),
		'section'	=> $section,
		'priority'	=> 5,
		'type'		=> 'select',
		'choices'	=> array(
			''		 => __( '- None -', 'genesis' ),
			'left'	 => __( 'Left', 'mai-pro-engine' ),
			'center' => __( 'Center', 'mai-pro-engine' ),
			'right'	 => __( 'Right', 'mai-pro-engine' ),
		),
	) );

}
