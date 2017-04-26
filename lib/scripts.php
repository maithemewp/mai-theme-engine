<?php

// Enqueue Javascript files
add_action( 'wp_enqueue_scripts', 'mai_enqueue_scripts' );
function mai_enqueue_scripts() {

	// Use minified files if script debug is not being used
	$suffix = mai_get_suffix();

	wp_enqueue_script( 'mai-theme', get_stylesheet_directory_uri() . "/assets/js/mai-theme{$suffix}.js", array('jquery'), CHILD_THEME_VERSION, true );
	wp_localize_script( 'mai-theme', 'maiVars', array(
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

	wp_enqueue_style( 'flexington', MAITHEME_ENGINE_PLUGIN_PLUGIN_URL . "assets/css/flexington{$suffix}.css", array(), '2.3.5' );
}

// Enqueue Slick and Slick init JS files only when a slider is being used
add_filter( 'genesis_attr_mai-slider', 'mai_attr_enqueue_slider_scripts' );
function mai_attr_enqueue_slider_scripts( $attributes ) {
	// Enqueue Slick Carousel
	wp_enqueue_script( 'mai-slick' );
	wp_enqueue_script( 'mai-slick-init' );
	return $attributes;
}
