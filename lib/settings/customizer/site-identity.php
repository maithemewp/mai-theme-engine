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

	/* ************* *
	 * Site Identity *
	 * ************* */
	$wp_customize->add_setting(
		'custom_logo_width',
		array(
			'theme_supports' => array( 'custom-logo' ),
		)
	);
	$wp_customize->add_control( 'custom_logo_width', array(
		'type'        => 'number',
		'priority'    => 8,
		'section'     => 'title_tagline',
		'label'       => __( 'Logo Width (in px)', 'mai-theme-engine' ),
		'description' => '',
		'input_attrs' => array(
			'min'         => 0,
			'step'        => 1,
			'placeholder' => '180',
		),
		'active_callback' => function() use ( $wp_customize ) {
			return ! empty( $wp_customize->get_setting( 'custom_logo' )->value() );
		},
	));
}
