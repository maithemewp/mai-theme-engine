<?php

/**
 * Register new Customizer elements.
 *
 * @param WP_Customize_Manager $wp_customize WP_Customize_Manager instance.
 */
add_action( 'customize_register', 'mai_register_customizer_content_singular_settings', 20 );
function mai_register_customizer_content_singular_settings( $wp_customize ) {

	/* ************ *
	 * Mai Settings *
	 * ************ */

	$section        = 'mai_content_singular';
	$settings_field = 'genesis-settings';
	$post_type      = 'post';

	// Section.
	$wp_customize->add_section(
		$section,
		array(
			'title'    => __( 'Mai Content Singular', 'mai-pro-engine' ),
			'priority' => '38',
		)
	);

	// Featured Image - heading only.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'singular_featured_image_heading' ),
		array(
			'default' => '',
			'type'    => 'option',
		)
	);
	$wp_customize->add_control(
		new Mai_Customize_Control_Content( $wp_customize,
			'singular_featured_image_heading',
			array(
				'label'    => __( 'Featured Image', 'mai-pro-engine' ),
				'section'  => $section,
				'settings' => false,
			)
		)
	);

	// Featured image - Pages.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'singular_image_page' ),
		array(
			'default'           => mai_sanitize_one_zero( mai_get_default_option( 'singular_image_page' ) ),
			'type'              => 'option',
			'sanitize_callback' => 'mai_sanitize_one_zero',
		)
	);
	$wp_customize->add_control(
		'singular_image_page',
		array(
			'label'    => __( 'Display the featured image on pages', 'mai-pro-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'singular_image_page' ),
			'type'     => 'checkbox',
		)
	);

	// Featured image - Posts.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'singular_image_post' ),
		array(
			'default'           => mai_sanitize_one_zero( mai_get_default_option( 'singular_image_post' ) ),
			'type'              => 'option',
			'sanitize_callback' => 'mai_sanitize_one_zero',
		)
	);
	$wp_customize->add_control(
		'singular_image_post',
		array(
			'label'    => __( 'Display the featured image on posts', 'mai-pro-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'singular_image_post' ),
			'type'     => 'checkbox',
		)
	);

	// Entry Meta.
	if ( post_type_supports( $post_type, 'genesis-entry-meta-before-content' ) || post_type_supports( $post_type, 'genesis-entry-meta-after-content' ) ) {

		$remove_meta_choices = array();

		if ( post_type_supports( $post_type, 'genesis-entry-meta-before-content' ) ) {
			$remove_meta_choices['post_info'] = __( 'Remove Post Info', 'mai-pro-engine' );
		}

		if ( post_type_supports( $post_type, 'genesis-entry-meta-after-content' ) ) {
			$remove_meta_choices['post_meta'] = __( 'Remove Post Meta', 'mai-pro-engine' );
		}

		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $settings_field, 'remove_meta_post' ),
			array(
				'default'           => _mai_customizer_multicheck_sanitize_key( mai_get_default_option( 'remove_meta_post' ) ),
				'type'              => 'option',
				'sanitize_callback' => '_mai_customizer_multicheck_sanitize_key',
			)
		);
		$wp_customize->add_control(
			new Mai_Customize_Control_Multicheck( $wp_customize,
				'remove_meta_post',
				array(
					'label'    => __( 'Post Entry Meta', 'mai-pro-engine' ),
					'section'  => $section,
					'settings' => _mai_customizer_get_field_name( $settings_field, 'remove_meta_post' ),
					'priority' => 10,
					'choices'  => $remove_meta_choices,
				)
			)
		);

	}

}
