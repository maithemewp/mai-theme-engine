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
		'description' => __( 'Customize your theme with sitewide changes.', 'maitheme' ),
		'title'       => __( 'Mai Settings', 'maitheme' ),
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
		'label'    => __( 'Enable sticky header', 'maitheme' ),
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
		'label'    => __( 'Enable shrinking header', 'maitheme' ),
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
		'label'    => __( 'Automatically display the featured image on single posts/pages', 'maitheme' ),
		'section'  => $section,
		'type'     => 'checkbox',
		'priority' => 5,
	) );

	/*****************************************************
	 * Mobile Menu Style setting
	 */

	$wp_customize->add_setting( 'mobile_menu_style', array(
		'default'           => 'standard',
		'sanitize_callback' => 'wp_strip_all_tags',
	) );

	$wp_customize->add_control( 'mobile_menu_style', array(
		'label'			=> __( 'Mobile menu style', 'maitheme' ),
		'description'	=> __( '(Side menu disabled if sticky header enabled)', 'maitheme' ),
		'section'		=> $section,
		'priority'		=> 5,
		'type'			=> 'select',
		'choices'		=> array(
			'standard'	=> __( 'Standard Menu', 'maitheme' ),
			'side'		=> __( 'Side Menu', 'maitheme' ),
		),
	) );

	/*****************************************************
	 * Footer Widget Count setting
	 */

	$wp_customize->add_setting( 'footer_widget_count', array(
		'default'           => '2',
		'sanitize_callback' => 'wp_strip_all_tags',
	) );

	$wp_customize->add_control( 'footer_widget_count', array(
		'label'			=> __( 'Footer widget areas', 'maitheme' ),
		'description'	=> __( 'Save and reload customizer to view changes', 'maitheme' ),
		'section'		=> $section,
		'priority'		=> 5,
		'type'			=> 'select',
		'choices'		=> array(
			1 => __( '1', 'maitheme' ),
			2 => __( '2', 'maitheme' ),
			3 => __( '3', 'maitheme' ),
			4 => __( '4', 'maitheme' ),
			6 => __( '6', 'maitheme' ),
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
		'title'       => __( 'Mai Banner Area', 'maitheme' ),
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
		'label'    => __( 'Enable the banner area', 'maitheme' ),
		'section'  => $section,
		'priority' => 5,
		'type'     => 'checkbox',
	) );

	/*****************************************************
	 * Enable Banner Overlay
	 */

	$wp_customize->add_setting( 'enable_banner_overlay', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( 'enable_banner_overlay', array(
		'label'    => __( 'Enable banner overlay', 'maitheme' ),
		'section'  => $section,
		'priority' => 5,
		'type'     => 'checkbox',
	) );


	/*****************************************************
	 * Enable Banner Inner styling
	 */

	$wp_customize->add_setting( 'enable_banner_inner', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( 'enable_banner_inner', array(
		'label'    => __( 'Enable banner inner styling', 'maitheme' ),
		'section'  => $section,
		'priority' => 5,
		'type'     => 'checkbox',
	) );

	/*****************************************************
	 * Banner Content Width
	 */

	$wp_customize->add_setting( 'banner_content_width', array(
		'default'           => 'lg',
		// 'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( 'banner_content_width', array(
		'label'		=> __( 'Banner content width', 'maitheme' ),
		'section'	=> $section,
		'priority'	=> 5,
		'type'		=> 'select',
		'choices'	=> array(
			'xs'   => __( 'Extra Small', 'maitheme' ),
			'sm'   => __( 'Small', 'maitheme' ),
			'md'   => __( 'Medium', 'maitheme' ),
			'lg'   => __( 'Large', 'maitheme' ),
			'xl'   => __( 'Extra Large', 'maitheme' ),
			'full' => __( 'Full Width', 'maitheme' ),
		),
	) );

	/*****************************************************
	 * Default Banner Image setting
	 */

	$wp_customize->add_setting( 'banner_id', array(
		'default'           => '',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'banner_id', array(
		'label'			=> __( 'Banner Image', 'maitheme' ),
		'description'	=> __( 'Set a default banner image. Can be overridden per post/page.', 'maitheme' ),
		'section'		=> $section,
		'settings'		=> 'banner_id',
		'priority'		=> 5,
	) ) );

}
