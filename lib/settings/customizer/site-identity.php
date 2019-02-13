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
	$wp_customize->add_setting( 'custom_logo_width',
		array(
			'default'           => 180,
			'sanitize_callback' => 'absint',
			'theme_supports'    => array( 'custom-logo' ),
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control( new Mai_Customize_Control_Slider( $wp_customize, 'custom_logo_width',
		array(
			'label'       => esc_attr__( 'Logo Width', 'mai-theme-engine' ),
			'priority'    => 8,
			'section'     => 'title_tagline',
			'input_attrs' => array(
				'min'  => 40,  // Required.
				'max'  => 320, // Required.
				'step' => 1,   // Required.
			),
			'active_callback' => function() use ( $wp_customize ) {
				// Need to set this as a variable cause older PHP was freaking out.
				$has_logo = ! empty( $wp_customize->get_setting( 'custom_logo' )->value() );
				return $has_logo;
			},
		)
	) );
}
