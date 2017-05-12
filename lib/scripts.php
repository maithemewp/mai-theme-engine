<?php

// Enqueue Javascript files
add_action( 'wp_enqueue_scripts', 'mai_enqueue_scripts' );
function mai_enqueue_scripts() {

	// Use minified files if script debug is not being used
	$suffix = mai_get_suffix();

	// Enqueue the main global js file
	wp_enqueue_script( 'mai-theme-script', MAITHEME_ENGINE_PLUGIN_PLUGIN_URL . "/assets/js/mai-theme{$suffix}.js", array('jquery'), MAITHEME_ENGINE_PLUGIN_VERSION, true );
	wp_localize_script( 'mai-theme-script', 'maiVars', array(
		'mainMenu'		=> __( 'Menu', 'genesis' ),
		'subMenu'		=> __( 'Menu', 'genesis' ),
		'subMenuAria'	=> __( 'sub-menu toggle', 'genesis' ),
		'search_box'	=> sprintf( '<div class="search-box" style="display:none;">%s</div>', get_search_form(false) ),
	) );

	// Register script for later use
	wp_register_script( 'mai-slick', MAITHEME_ENGINE_PLUGIN_PLUGIN_URL . "assets/js/slick{$suffix}.js", array( 'jquery' ), '1.6.0', true );
	wp_register_script( 'mai-slick-init', MAITHEME_ENGINE_PLUGIN_PLUGIN_URL . "assets/js/slick-init{$suffix}.js", array( 'mai-slick' ), MAITHEME_ENGINE_PLUGIN_VERSION, true );
}

// Enqueue CSS files
add_action( 'wp_enqueue_scripts', 'mai_enqueue_styles' );
function mai_enqueue_styles() {

	// Use minified files if script debug is not being used
	$suffix = mai_get_suffix();

	wp_enqueue_style( 'mai-theme-style', MAITHEME_ENGINE_PLUGIN_PLUGIN_URL . "assets/css/mai-theme{$suffix}.css", array(), MAITHEME_ENGINE_PLUGIN_VERSION );
	wp_enqueue_style( 'flexington', MAITHEME_ENGINE_PLUGIN_PLUGIN_URL . "assets/css/flexington{$suffix}.css", array(), '2.3.5' );
	wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css', array(), MAITHEME_ENGINE_PLUGIN_VERSION );

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
		$src = MAITHEME_ENGINE_PLUGIN_PLUGIN_URL . "assets/css/{$file_name}{$suffix}.css";
	}

	$styles['mai-woocommerce'] = array(
		'src'     => $src,
		'deps'    => '',
		'version' => CHILD_THEME_VERSION,
		'media'   => 'all',
	);

	// Bail if account, cart, or checkout pages. We need layout stuff here
	if ( is_account_page() || is_cart() || is_checkout() ) {
		return $styles;
	}

	unset( $styles['woocommerce-layout'] );		 	// Remove the layout
	// unset( $styles['woocommerce-general'] );	 	// Remove the gloss
	// unset( $styles['woocommerce-smallscreen'] ); // Remove the smallscreen optimisation
	return $styles;
}
