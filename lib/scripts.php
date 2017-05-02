<?php

add_action( 'admin_enqueue_scripts', 'mai_enqueue_admin_scripts' );
function mai_enqueue_admin_scripts() {

	// Use minified files if script debug is not being used
	$suffix = mai_get_suffix();

	wp_register_script( 'mai-cmb2', MAITHEME_ENGINE_PLUGIN_PLUGIN_URL . "assets/js/mai-cmb2{$suffix}.js", array( 'jquery' ), MAITHEME_ENGINE_PLUGIN_VERSION, true );
	wp_register_style( 'mai-cmb2', MAITHEME_ENGINE_PLUGIN_PLUGIN_URL . "assets/css/mai-cmb2{$suffix}.css", array(), MAITHEME_ENGINE_PLUGIN_VERSION );
}

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
