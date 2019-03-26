<?php

/**
 * Enqueue an admin script, for custom editor styles and other stuff.
 *
 * @return void
 */
add_action( 'admin_enqueue_scripts', 'mai_admin_enqueue_scripts' );
function mai_admin_enqueue_scripts() {
	$suffix = mai_get_suffix();
	wp_register_style( 'mai-admin', MAI_THEME_ENGINE_PLUGIN_URL . "assets/css/admin/mai-admin{$suffix}.css", array(), MAI_THEME_ENGINE_VERSION );
	wp_register_script( 'mai-admin', MAI_THEME_ENGINE_PLUGIN_URL . "assets/js/admin/mai-admin{$suffix}.js", array( 'jquery' ), MAI_THEME_ENGINE_VERSION, true );
}

/**
 * Enqueue customizer scripts.
 *
 * @since   1.8.0
 *
 * @return  void
 */
add_action( 'customize_preview_init', 'mai_customizer_enqueue_scripts' );
function mai_customizer_enqueue_scripts() {
	$suffix = mai_get_suffix();
	wp_enqueue_script( 'mai-customizer', MAI_THEME_ENGINE_PLUGIN_URL . "assets/js/admin/mai-customizer{$suffix}.js", array( 'jquery' ), MAI_THEME_ENGINE_VERSION, true );
}

/**
 * Add our image sizes to the media chooser.
 *
 * @since   Unknown
 * @since   1.8.0
 *
 * @param   $sizes  The size options.
 *
 * @return  array   Modified size options.
 */
add_filter( 'image_size_names_choose', 'mai_do_media_chooser_sizes' );
function mai_do_media_chooser_sizes( $sizes ) {

	// Get the image sizes to register.
	$new_sizes = mai_get_image_sizes();

	// Bail if no image sizes.
	if ( ! $new_sizes ) {
		return $sizes;
	}

	// Unset the big images.
	unset( $new_sizes['banner'] );
	unset( $new_sizes['section'] );
	unset( $new_sizes['full-width'] );

	// Build new array with 'name' => 'label';
	$new_sizes = wp_list_pluck( $new_sizes, 'label' );

	return array_merge( $sizes, $new_sizes );;
}

/**
 * Remove unsupported FlexGrid gallery options from admin
 *
 * @return void
 */
add_action( 'admin_head', 'mai_remove_unsupported_flexgrid_gallery_options' );
function mai_remove_unsupported_flexgrid_gallery_options() {
	echo '<style type="text/css">
		.gallery-settings .columns option[value="5"],
		.gallery-settings .columns option[value="7"],
		.gallery-settings .columns option[value="8"],
		.gallery-settings .columns option[value="9"] {
			display:none !important;
			visibility: hidden !important;
		}
	</style>';
}

/**
 * Adds a new select bar to the WP editor.
 * Insert 'styleselect' into the $buttons array.
 * _2 places the new button on the second line.
 *
 * @return  array
 */
add_filter( 'mce_buttons_2', 'mai_add_styleselect_dropdown' );
function mai_add_styleselect_dropdown( $buttons ) {
	// Bail if we already have styleselect.
	if ( in_array( 'styleselect', $buttons ) ) {
		return $buttons;
	}
	array_unshift( $buttons, 'styleselect' );
	return $buttons;
}

/**
 * Add a button option to the editor.
 *
 * @param   array  $init_array
 *
 * @return  array
 */
add_filter( 'tiny_mce_before_init', 'mai_add_style_format_options_to_editor' );
function mai_add_style_format_options_to_editor( $init_array ) {
	$style_formats = array(
		array(
			'title'    => __( 'Primary Button', 'mai-theme-engine' ),
			'selector' => 'a',
			'classes'  => 'button',
		),
		array(
			'title'    => __( 'Primary Button (Large)', 'mai-theme-engine' ),
			'selector' => 'a',
			'classes'  => 'button large',
		),
		array(
			'title'    => __( 'Primary Ghost Button', 'mai-theme-engine' ),
			'selector' => 'a',
			'classes'  => 'button ghost',
		),
		array(
			'title'    => __( 'Primary Ghost Button (Large)', 'mai-theme-engine' ),
			'selector' => 'a',
			'classes'  => 'button ghost large',
		),
		array(
			'title'    => __( 'Secondary Button', 'mai-theme-engine' ),
			'selector' => 'a',
			'classes'  => 'button alt',
		),
		array(
			'title'    => __( 'Secondary Button (Large)', 'mai-theme-engine' ),
			'selector' => 'a',
			'classes'  => 'button alt large',
		),
		array(
			'title'    => __( 'Secondary Ghost Button', 'mai-theme-engine' ),
			'selector' => 'a',
			'classes'  => 'button ghost',
		),
		array(
			'title'    => __( 'Secondary Ghost Button (Large)', 'mai-theme-engine' ),
			'selector' => 'a',
			'classes'  => 'button ghost large',
		),
		array(
			'title'    => __( 'White Button', 'mai-theme-engine' ),
			'selector' => 'a',
			'classes'  => 'button white',
		),
		array(
			'title'    => __( 'White Button (Large)', 'mai-theme-engine' ),
			'selector' => 'a',
			'classes'  => 'button white large',
		),
		array(
			'title'    => __( 'White Ghost Button', 'mai-theme-engine' ),
			'selector' => 'a',
			'classes'  => 'button ghost',
		),
		array(
			'title'    => __( 'White Ghost Button (Large)', 'mai-theme-engine' ),
			'selector' => 'a',
			'classes'  => 'button ghost large',
		),
	);

	// Add to existing formats.
	if ( isset( $init_array['style_formats'] ) && ! empty( $init_array['style_formats'] ) ) {
		$decoded       = json_decode( $init_array['style_formats'], true );
		$style_formats = array_merge( $decoded, $style_formats );
	}

	// JSON encode the array.
	$style_formats = json_encode( $style_formats );

	// Add the formats.
	$init_array['style_formats'] = $style_formats;

	return $init_array;
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

/**
 * Enable the editor on the page for posts.
 * Force an empty space as the post content so block editor (Gutenberg) will show in the admin when editing this page,
 * otherwise default editor will show. This is stupid and hacky and I wish Gutenberg didn't do this.
 *
 * @since   1.6.2
 *
 * @return  void
 */
add_action( 'admin_head', 'mai_posts_page_edit_form' );
function mai_posts_page_edit_form() {

	$current_screen = get_current_screen();

	// Bail if not editing a page.
	if ( ! $current_screen || 'page' !== $current_screen->id ) {
		return;
	}

	// Bail if not editing the "Page for Posts".
	if ( (int) get_the_ID() !== (int) get_option( 'page_for_posts' ) ) {
		return;
	}

	// Add the editor.
	add_post_type_support( 'page', 'editor' );

	// Get the post object.
	$post = get_post( get_the_ID() );

	// Bail if no post. Safety first!
	if ( ! $post ) {
		return;
	}

	// Bail the post has content.
	if ( ! empty( $post->post_content ) ) {
		return;
	}

	// Update the post, adding a space as the content.
	wp_update_post( array(
		'ID'           => $post->ID,
		'post_content' => ' ',
	) );
}

// Change login logo.
add_action( 'login_head',  'mai_login_logo_css' );
function mai_login_logo_css() {

	$logo_id = get_theme_mod( 'custom_logo' );

	// Bail if we don't have a custom logo.
	if ( ! $logo_id ) {
		return;
	}

	$width    = get_theme_mod( 'custom_logo_width', 180 );
	$width_px = absint( $width ) . 'px';

	// Hide the default logo and heading.
	echo "<style>
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
		.login .mai-login-logo a {
			max-width: {$width_px};
			display: block;
			margin: auto;
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
		.login #nav,
		.login #backtoblog {
			text-align: center;
		}
	</style>";

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
