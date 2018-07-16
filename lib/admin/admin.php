<?php

/**
 * Enqueue an admin script, for custom editor styles and other stuff.
 *
 * @return void
 */
add_action( 'admin_enqueue_scripts', 'mai_admin_enqueue_scripts' );
function mai_admin_enqueue_scripts() {

	// Use minified files if script debug is not being used.
	$suffix = mai_get_suffix();

	wp_register_style( 'mai-admin', MAI_THEME_ENGINE_PLUGIN_URL . "assets/css/mai-admin{$suffix}.css", array(), MAI_THEME_ENGINE_VERSION );
	wp_register_script( 'mai-admin', MAI_THEME_ENGINE_PLUGIN_URL . "assets/js/mai-admin{$suffix}.js", array( 'jquery' ), MAI_THEME_ENGINE_VERSION, true );
}

/**
 * Yoast (and possibly others) run do_shortcode on category descriptions in admin list,
 * which blows things up on [grid] when parent="current" and it searches for the category/post ID.
 *
 * @return  string
 */
add_filter( 'category_description', 'mai_limit_term_description' );
function mai_limit_term_description( $desc ) {
	if ( ! is_admin() ) {
		return $desc;
	}
	return wp_trim_words( strip_tags( $desc ), 24, '...' );
}

// Show the editor on the page set for is_home().
add_action( 'edit_form_after_title', 'mai_posts_page_edit_form' );
function mai_posts_page_edit_form() {
	global $post, $post_type, $post_ID;
	if ( $post_ID == get_option( 'page_for_posts' ) && empty( $post->post_content ) ) {
		add_post_type_support( $post_type, 'editor' );
	}
}

// Change login logo.
add_action( 'login_head',  'mai_login_logo_css' );
function mai_login_logo_css() {

	$logo_id = get_theme_mod( 'custom_logo' );

	// Bail if we don't have a custom logo.
	if ( ! $logo_id ) {
		return;
	}

	// Hide the default logo and heading.
	echo '<style  type="text/css">
		.login h1,
		.login h1 a {
			background: none !important;
			position: absolute !important;
			clip: rect(0, 0, 0, 0) !important;
			height: 1px !important;
			width: 1px !important;
			padding: 0 !important;
			margin: 0 !important;
			border: 0 !important;
			overflow: hidden !important;
		}
		.login .mai-login-logo img {
			display: block !important;
			height: auto !important;
			width: auto !important;
			max-width: 100% !important;
			margin: 0 auto !important;
		}
		.login #login_error,
		.login .message {
			margin-top: 16px !important;
		}
	</style>';

	// Add our own inline logo.
	add_action( 'login_message', function() use ( $logo_id ) {
		// From WP core.
		if ( is_multisite() ) {
			$login_header_url   = network_home_url();
			$login_header_title = get_network()->site_name;
		} else {
			$login_header_url   = __( 'https://wordpress.org/' );
			$login_header_title = __( 'Powered by WordPress' );
		}
		printf( '<h2 class="mai-login-logo"><a href="%s" title="%s" tabindex="-1">%s</a></h2>',
			esc_url( apply_filters( 'login_headerurl', $login_header_url ) ),
			esc_attr( apply_filters( 'login_headertitle', $login_header_title ) ),
			wp_get_attachment_image( $logo_id, 'medium' )
		);
	});

}

// Change login link.
add_filter( 'login_headerurl', 'mai_login_link' );
function mai_login_link() {
	return get_site_url();
}
