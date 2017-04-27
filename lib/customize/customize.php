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
 * Register custom javascript file for the customizer.
 *
 * @see 	add_action( 'customize_preview_init', $func )
 *
 * @return  void
 */
// add_action( 'customize_preview_init', 'mai_enqueue_customizer_scripts' );
function mai_enqueue_customizer_scripts() {
	wp_enqueue_script( 'mai-customizer', MAITHEME_ENGINE_PLUGIN_PLUGIN_URL . "/assets/js/mai-customizer{$suffix}.js", array( 'jquery', 'customize-preview' ), CHILD_THEME_VERSION, true );
}

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

	$wp_customize->add_section( $section, array(
		'description' => __( 'Customize your theme with sitewide changes.', 'maitheme' ),
		'title'       => __( 'Mai Theme Settings', 'maitheme' ),
		'priority'    => 35,
	) );


	$wp_customize->add_setting( 'enable_fixed_header', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( 'enable_fixed_header', array(
		'label'    => __( 'Enable fixed (sticky) header', 'maitheme' ),
		'section'  => $section,
		'type'     => 'checkbox',
		'priority' => 5,
	) );


	$wp_customize->add_setting( 'enable_boxed_content', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( 'enable_boxed_content', array(
		'label'    => __( 'Enable boxed content styling', 'maitheme' ),
		'section'  => $section,
		'type'     => 'checkbox',
		'priority' => 5,
	) );

	// $wp_customize->add_setting( 'enable_singular_image', array(
	// 	'default'           => 1,
	// 	'sanitize_callback' => 'absint',
	// ) );

	// $wp_customize->add_control( 'enable_singular_image', array(
	// 	'label'    => __( 'Auto-display the featured image on single posts/pages', 'maitheme' ),
	// 	'section'  => $section,
	// 	'type'     => 'checkbox',
	// 	'priority' => 5,
	// ) );

	$wp_customize->add_setting( 'mobile_menu_style', array(
		'default'           => 'standard',
		'sanitize_callback' => 'wp_strip_all_tags',
	) );

	$wp_customize->add_control( 'mobile_menu_style', array(
		'label'			=> __( 'Mobile menu style', 'maitheme' ),
		'description'	=> __( '(Side menu disabled if fixed header enabled)', 'maitheme' ),
		'section'		=> $section,
		'priority'		=> 5,
		'type'			=> 'select',
		'choices'		=> array(
			'standard'	=> __( 'Standard Menu', 'maitheme' ),
			'side'		=> __( 'Side Menu', 'maitheme' ),
		),
	) );


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

	$wp_customize->add_section( $section, array(
		'title'       => __( 'Banner Area', 'maitheme' ),
		'priority'    => 35,
	) );


	$wp_customize->add_setting( 'enable_banner_area', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( 'enable_banner_area', array(
		'label'    => __( 'Enable the banner area', 'maitheme' ),
		'section'  => $section,
		'type'     => 'checkbox',
		'priority' => 5,
	) );


	$wp_customize->add_setting( 'banner_id', array(
		'default'           => '',
		'sanitize_callback' => 'absint',
		'type'              => 'option',
	) );

	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'banner_id', array(
		'label'			=> __( 'Banner Image', 'maitheme' ),
		'description'	=> __( 'Set a default banner image. Leave empty to use Featured Image. Can be overidden per post/page.', 'maitheme' ),
		'section'		=> $section,
		'settings'		=> 'banner_id',
		'priority'		=> 5,
	) ) );



	// TODO: Enable color controls, for now... return!
	return;



	$wp_customize->add_setting(
		'mai_accent_color',
		array(
			'default'           => mai_customizer_get_default_accent_color(),
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'mai_accent_color',
			array(
				'description' => __( 'Change the default color for some links, link hovers, buttons, and button hovers.', 'maitheme' ),
				'label'       => __( 'Accent Color', 'maitheme' ),
				'section'     => 'colors',
				'settings'    => 'mai_accent_color',
			)
		)
	);

	// If using a primary nav menu
	if ( has_nav_menu( 'primary' ) ) {

		$wp_customize->add_setting(
			'mai_primary_nav_bg_color',
			array(
				'default'           => '#3f3f3f',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'mai_primary_nav_bg_color',
				array(
					'description' => __( 'Change the default background color for the footer widgets area.', 'maitheme' ),
					'label'       => __( 'Footer Widgets Background Color', 'maitheme' ),
					'section'     => 'colors',
					'settings'    => 'mai_primary_nav_bg_color',
				)
			)
		);

	}

	// If we have a least 1 footer widget area
	if ( mai_get_footer_widgets_count() > 0 ) {

		$wp_customize->add_setting(
			'mai_footer_widgets_bg_color',
			array(
				'default'           => '#3f3f3f',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'mai_footer_widgets_bg_color',
				array(
					'description' => __( 'Change the default background color for the footer widgets area.', 'maitheme' ),
					'label'       => __( 'Footer Widgets Background Color', 'maitheme' ),
					'section'     => 'colors',
					'settings'    => 'mai_footer_widgets_bg_color',
				)
			)
		);

	}

	$wp_customize->add_setting(
		'mai_site_footer_bg_color',
		array(
			'default'           => '#323232',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'mai_site_footer_bg_color',
			array(
				'description' => __( 'Change the default background color for the site footer area.', 'maitheme' ),
				'label'       => __( 'Site Footer Background Color', 'maitheme' ),
				'section'     => 'colors',
				'settings'    => 'mai_site_footer_bg_color',
			)
		)
	);

}
