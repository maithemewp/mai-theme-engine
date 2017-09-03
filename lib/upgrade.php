<?php

/**
 * Maybe run the version updater.
 *
 * @link   https://www.sitepoint.com/wordpress-plugin-updates-right-way/
 *
 * @return void
 */
add_action( 'admin_init', 'mai_update_database_version' );
function mai_update_database_version() {
	// Get the version number saved in the db.
	$option_version = get_option( 'mai_pro_engine_version' );
	// Bail if the version is the version.
	if ( MAI_PRO_ENGINE_VERSION === $option_version ) {
		return;
	}
	// Add new hook that fires during plugin update.
	do_action( 'mai_pro_engine_update', MAI_PRO_ENGINE_VERSION, $option_version, $plugin_version );
	// Update the version number option.
	update_option( 'mai_pro_engine_version', MAI_PRO_ENGINE_VERSION );
}

/**
 * Convert theme mods to theme settings (options).
 * This is for versions < 1.1.0.
 *
 * @return void.
 */
// add_action( 'mai_pro_engine_update', 'mai_pro_update_1_1_0' );
function mai_pro_update_1_1_0( $option_version, $plugin_version ) {

	// If new install and version is over 1.1.0
	if ( $plugin_version >= '1.1.0' ) {
		return;
	}

	// Bail if we have an option version number and it's over 1.1.0
	if ( isset( $option_version ) && ( $option_version >= '1.1.0' ) ) {
		return;
	}

	// New settings.
	$settings = array();

	// Theme mods to convert to settings.
	$mods = array(
		'enable_sticky_header',
		'enable_shrink_header',
		'footer_widget_count',
		'mobile_menu_style',
		'enable_banner_area',
		'banner_featured_image',
		'banner_background_color',
		'banner_id',
		'banner_overlay',
		'banner_inner',
		'banner_content_width',
		'banner_align_text',
	);
	foreach( $mods as $mod ) {
		$theme_mod = get_theme_mod( $mod );
		if ( $theme_mod ) {
			$settings[$theme_mod] = $theme_mod;
		}
	}

	$settings['columns'] = absint( genesis_get_option( 'columns' ) );

	/**
	 * This field is going from boolean for all, to individual keys per post type.
	 */
	$enable_singular_image = get_theme_mod( 'enable_singular_image' );

	// If enabled singular image is checked/true.
	if ( $enable_singular_image ) {
		$settings['singular_image_page'] = 1;
		$settings['singular_image_post'] = 1;
	}

	if ( ! empty( $settings ) ) {
		// Update settings.
		genesis_update_settings( $settings );

		// Remove each theme mod from the array.
		foreach ( $mods as $mod ) {
			remove_theme_mod( $mod );
		}
		// enable_singular_image is not in the array because it was processed separately.
		remove_theme_mod( 'enable_singular_image' );
	}

	// TODO: Archive stuff?!?!?

	// Woo upgrade, meta to cpt-archive-settings.
	if ( class_exists( 'WooCommerce' ) ) {

	}

}
