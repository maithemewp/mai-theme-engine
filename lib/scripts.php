<?php

// Enqueue Javascript files
add_action( 'wp_enqueue_scripts', 'mai_enqueue_scripts' );
function mai_enqueue_scripts() {

	// Use minified files if script debug is not being used
	$suffix = mai_get_suffix();

	// Enqueue the main global js file
	wp_enqueue_script( 'mai-theme-engine', MAI_THEME_ENGINE_PLUGIN_URL . "assets/js/mai-pro{$suffix}.js", array('jquery'), MAI_THEME_ENGINE_VERSION, true );
	wp_localize_script( 'mai-theme-engine', 'maiVars', array(
		'mainMenu'    => __( 'Menu', 'genesis' ),
		'subMenu'     => __( 'Menu', 'genesis' ),
		'subMenuAria' => __( 'sub-menu toggle', 'genesis' ),
		'search_box'  => sprintf( '<div class="search-box" style="display:none;">%s</div>', get_search_form(false) ),
	) );

	// Register script for later use
	wp_register_script( 'mai-slick', MAI_THEME_ENGINE_PLUGIN_URL . "assets/js/slick{$suffix}.js", array( 'jquery' ), '1.8.0', true );
	wp_register_script( 'mai-slick-init', MAI_THEME_ENGINE_PLUGIN_URL . "assets/js/slick-init{$suffix}.js", array( 'mai-slick' ), MAI_THEME_ENGINE_VERSION, true );

}

// Enqueue CSS files
add_action( 'wp_enqueue_scripts', 'mai_enqueue_styles' );
function mai_enqueue_styles() {

	// Use minified files if script debug is not being used
	$suffix = mai_get_suffix();

	wp_enqueue_style( 'mai-theme-engine', MAI_THEME_ENGINE_PLUGIN_URL . "assets/css/mai-pro{$suffix}.css", array(), MAI_THEME_ENGINE_VERSION );
	wp_enqueue_style( 'flexington', MAI_THEME_ENGINE_PLUGIN_URL . "assets/css/flexington{$suffix}.css", array(), '2.3.5' );
	wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css', array(), MAI_THEME_ENGINE_VERSION );

}

// Remove WooCommerce default layout styles
add_filter( 'woocommerce_enqueue_styles', 'mai_woocommerce_styles' );
function mai_woocommerce_styles( $styles ) {

	$file_name = 'mai-woocommerce';
	$file_path = get_stylesheet_directory() . '/assets/css/' . $file_name . 'css';

	// If the file exists in the child theme /assets/css/{file_name}
	if ( file_exists( $file_path ) ) {
		// Use child theme file
		$src = get_stylesheet_directory_uri() . '/assets/css/' . $file_name . 'css';
	} else {

		// Use minified files if script debug is not being used
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

	// Bail if account, cart, or checkout pages. We need layout stuff here
	if ( is_account_page() || is_cart() || is_checkout() ) {
		return $styles;
	}

	unset( $styles['woocommerce-layout'] );

	return $styles;
}
