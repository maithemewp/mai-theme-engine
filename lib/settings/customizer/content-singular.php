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
		_mai_customizer_get_field_name( $settings_field, 'featured_image_heading' ),
		array(
			'default' => '',
			'type'    => 'option',
		)
	);
	$wp_customize->add_control(
		new Mai_Customize_Control_Content( $wp_customize,
			'featured_image_heading',
			array(
				'label'    => __( 'Featured Image', 'mai-pro-engine' ),
				'section'  => $section,
				'settings' => _mai_customizer_get_field_name( $settings_field, 'featured_image_heading' ),
			)
		)
	);

	// Featured image - Pages.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'singular_image_page' ),
		array(
			'default' => 1,
			'type'    => 'option',
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
			'default' => 1,
			'type'    => 'option',
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

		// Entry Meta single.
		// $remove_meta_single_key = sprintf( 'remove_meta_%s', $post_type );
		// $remove_meta_single_key = 'remove_meta';

		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $settings_field, 'remove_meta' ),
			array(
				'default'           => $args['remove_meta'],
				'type'              => 'option',
				'sanitize_callback' => '_mai_customizer_multicheck_sanitize_key',
			)
		);
		$wp_customize->add_control(
			new Mai_Customize_Control_Multicheck( $wp_customize,
				'remove_meta',
				array(
					'label'    => __( 'Post Entry Meta', 'mai-pro-engine' ),
					'section'  => $section,
					'settings' => _mai_customizer_get_field_name( $settings_field, 'remove_meta' ),
					'priority' => 10,
					'choices'  => $remove_meta_choices,
				)
			)
		);

	}

}
