<?php

/**
 * Enqueue Javascript files.
 *
 * @since   1.0.0
 *
 * @return  void
 */
add_action( 'wp_enqueue_scripts', 'mai_enqueue_scripts' );
function mai_enqueue_scripts() {

	$suffix = mai_get_suffix();

	// Enqueue the main global js file.
	wp_enqueue_script( 'mai-theme-engine', MAI_THEME_ENGINE_PLUGIN_URL . "assets/js/mai-theme{$suffix}.js", array( 'jquery' ), MAI_THEME_ENGINE_VERSION, true );
	wp_localize_script( 'mai-theme-engine', 'maiVars', array(
		'mainMenu'    => __( 'Menu', 'mai-theme-engine' ),
		'subMenu'     => __( 'Submenu', 'mai-theme-engine' ),
		'searchBox'   => sprintf( '<div class="search-box" style="display:none;">%s</div>', get_search_form( false ) ),
		'logoWidth'   => get_theme_mod( 'custom_logo_width', 180 ),
		'maiScrollTo' => implode( ', ', apply_filters( 'mai_scroll_to_elements', array( '.scroll-to' ) ) ),
	) );

	// Logo values.
	$width         = get_theme_mod( 'custom_logo_width', 180 );
	$top           = get_theme_mod( 'custom_logo_top', 24 );
	$bottom        = get_theme_mod( 'custom_logo_bottom', 24 );
	$shrink_width  = get_theme_mod( 'custom_logo_shrink_width', 120 );
	$shrink_top    = get_theme_mod( 'custom_logo_shrink_top', 4 );
	$shrink_bottom = get_theme_mod( 'custom_logo_shrink_bottom', 4 );

	// Enqueue scroll js.
	wp_enqueue_script( 'basic-scroll', MAI_THEME_ENGINE_PLUGIN_URL . "assets/js/basic-scroll{$suffix}.js", array(), '3.0.1', true );
	wp_enqueue_script( 'mai-scroll', MAI_THEME_ENGINE_PLUGIN_URL . "assets/js/mai-scroll{$suffix}.js", array( 'basic-scroll' ), MAI_THEME_ENGINE_VERSION, true );
	wp_localize_script( 'mai-scroll', 'maiScroll', array(
		'logoWidth'        => get_theme_mod( 'custom_logo_width', 180 ),
		'logoTop'          => get_theme_mod( 'custom_logo_top', 24 ),
		'logoBottom'       => get_theme_mod( 'custom_logo_bottom', 24 ),
		'logoShrinkWidth'  => get_theme_mod( 'custom_logo_shrink_width', 120 ),
		'logoShrinkTop'    => get_theme_mod( 'custom_logo_shrink_top', 4 ),
		'logoShrinkBottom' => get_theme_mod( 'custom_logo_shrink_bottom', 4 ),
	) );

	// Maybe enabled responsive videos.
	$responsive_videos = apply_filters( 'mai_enable_responsive_videos', '__return_true' );
	if ( $responsive_videos ) {
		// If not on a Gutenberg page/post that has responsive embeds enabled.
		if ( ! ( function_exists( 'has_blocks' ) && has_blocks() && current_theme_supports( 'responsive-embeds' ) ) ) {
			// FitVids.
			wp_enqueue_script( 'mai-responsive-videos', MAI_THEME_ENGINE_PLUGIN_URL . "assets/js/fitvids{$suffix}.js", array( 'jquery' ), '1.2.0', true );
			wp_enqueue_script( 'mai-responsive-video-init', MAI_THEME_ENGINE_PLUGIN_URL . "assets/js/fitvids-init{$suffix}.js", array( 'mai-responsive-videos' ), MAI_THEME_ENGINE_VERSION, true );
		}
	}

	// Register Slick.
	wp_register_script( 'mai-slick', MAI_THEME_ENGINE_PLUGIN_URL . "assets/js/slick{$suffix}.js", array( 'jquery' ), '1.8.0', true );
	wp_register_script( 'mai-slick-init', MAI_THEME_ENGINE_PLUGIN_URL . "assets/js/slick-init{$suffix}.js", array( 'mai-slick' ), MAI_THEME_ENGINE_VERSION, true );
}

/**
 * Enqueue CSS files.
 *
 * @since   1.0.0
 *
 * @return  void
 */
add_action( 'wp_enqueue_scripts', 'mai_enqueue_styles' );
function mai_enqueue_styles() {

	$suffix = mai_get_suffix();

	wp_enqueue_style( 'mai-theme-engine', MAI_THEME_ENGINE_PLUGIN_URL . "assets/css/mai-theme{$suffix}.css", array(), MAI_THEME_ENGINE_VERSION );
	wp_enqueue_style( 'flexington', MAI_THEME_ENGINE_PLUGIN_URL . "assets/css/flexington{$suffix}.css", array(), '2.5.0' );
	if ( is_rtl() ) {
		wp_enqueue_style( 'mai-theme-engine-rtl', MAI_THEME_ENGINE_PLUGIN_URL . "assets/css/mai-theme-rtl{$suffix}.css", array(), MAI_THEME_ENGINE_VERSION );
	}
}

/**
 * Remove WooCommerce default layout styles.
 *
 * @since   1.0.0
 *
 * @return  void
 */
add_filter( 'woocommerce_enqueue_styles', 'mai_woocommerce_styles' );
function mai_woocommerce_styles( $styles ) {

	$file_name = 'mai-woocommerce';
	$file_path = get_stylesheet_directory() . '/assets/css/' . $file_name . 'css';

	// If the file exists in the child theme /assets/css/{file_name}
	if ( file_exists( $file_path ) ) {
		// Use child theme file.
		$src = get_stylesheet_directory_uri() . '/assets/css/' . $file_name . 'css';
	} else {

		$suffix = mai_get_suffix();

		// Use our plugin file
		$src = MAI_THEME_ENGINE_PLUGIN_URL . "assets/css/{$file_name}{$suffix}.css";
	}

	$styles['mai-woocommerce'] = array(
		'src'     => $src,
		'deps'    => '',
		'version' => MAI_THEME_ENGINE_VERSION,
		'media'   => 'all',
	);

	// Bail if account, cart, or checkout pages. We need layout stuff here.
	if ( is_account_page() || is_cart() || is_checkout() ) {
		return $styles;
	}

	unset( $styles['woocommerce-layout'] );

	return $styles;
}
