<?php

/**
 * Maybe enqueue files.
 *
 * @version  1.0.0
 *
 * @since    1.4.0
 *
 * @return   void
 */
add_action( 'wp_enqueue_scripts', 'mai_maybe_enqueue_deprecated_scripts' );
function mai_maybe_enqueue_deprecated_scripts() {

	// Get the first version of Mai Theme that was installed.
	$first_version = get_option( 'mai_first_version' );

	// Bail if first version has not been set.
	if ( ! $first_version ) {
		return;
	}

	// Bail if 1.4.0 or newer.
	if ( version_compare( $first_version, '1.4.0', '>=' ) ) {
		return;
	}

	// Enqueue Font Awesome pre-5 (4.7?) for existing installs.
	wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css', array(), MAI_THEME_ENGINE_VERSION );
}
