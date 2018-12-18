<?php

/**
 * Filter the Genesis Theme Settings customizer panel settings and add our new beta update setting.
 * Allows all Mai plugins to be updated to beta releases.
 *
 * @since   1.8.0
 *
 * @param   array  The existing customizer config.
 *
 * @return  array  The modified config.
 */
add_filter( 'genesis_customizer_theme_settings_config', 'mai_customizer_theme_settings_config' );
function mai_customizer_theme_settings_config( $config ) {

	// Bail if these settings are not set.
	if ( ! isset( $config['genesis']['sections']['genesis_updates']['controls'] ) ) {
		return $config;
	}

	// Add our new settings.
	$config['genesis']['sections']['genesis_updates']['controls']['mai_updater_branch'] = array(
		'label'       => __( 'Mai Theme Updates', 'mai-theme-engine' ),
		'description' => __( 'Choose the type of Mai plugin updates to check for.', 'mai-theme-engine' ),
		'section'     => 'genesis_updates',
		'type'        => 'select',
		'choices'     => array(
			'master' => __( 'Stable', 'mai-theme-engine' ),
			'beta'   => __( 'Beta', 'mai-theme-engine' ),
		),
		'settings'    => array(
			'default' => 'master',
		),
	);

	return $config;
}
