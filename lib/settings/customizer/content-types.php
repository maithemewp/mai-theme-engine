<?php

/**
 * Register new Customizer elements.
 * Register priorty 10 since our actual settings are registered at 20.
 *
 * @access  private
 *
 * @param   WP_Customize_Manager $wp_customize WP_Customize_Manager instance.
 */
add_action( 'customize_register', 'mai_register_customizer_content_types_section', 10 );
function mai_register_customizer_content_types_section( $wp_customize ) {

	/* ***************** *
	 * Mai Content Types *
	 * ***************** */

	// Panel.
	$wp_customize->add_panel( 'mai_content_types', array(
		'priority' => 37,
		'title'    => __( 'Mai Content Types', 'mai-theme-engine' ),
	) );

}
