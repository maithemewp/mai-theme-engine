<?php

// Add HTML5 markup structure
add_theme_support( 'html5' );

// Add title tag support
add_theme_support( 'title-tag' );

// Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

add_theme_support( 'genesis-menus', array(
	'utility'		=> __( 'Top (Utility) Menu', 'maitheme' ),
	'primary'		=> __( 'Primary Menu', 'maitheme' ),
	'header_left'	=> __( 'Header Left Menu', 'maitheme' ),
	'header_right'	=> __( 'Header Right Menu', 'maitheme' ),
	'secondary'		=> __( 'Footer Menu', 'maitheme' ),
	'mobile'		=> __( 'Mobile Menu', 'maitheme' ),
) );

// Add support for structural wraps
add_theme_support( 'genesis-structural-wraps', array(
    'archive-description',
    'breadcrumb',
    'header',
    'menu-utility',
    'menu-primary',
    'menu-secondary',
    'footer-widgets',
    'footer',
) );

// Add Accessibility support
add_theme_support( 'genesis-accessibility', array(
	'404-page',
	'drop-down-menu',
	'headings',
	'search-form',
	'skip-links',
) );

// Add custom logo support
add_theme_support( 'custom-logo', array(
	'height'		=> '',
	'width'			=> '',
	'flex-height'	=> true,
	'flex-width'	=> true,
) );

// Add excerpt support for pages
add_post_type_support( 'page', 'excerpt' );

// Add custom body class to the head
add_filter( 'body_class', 'mai_global_body_class' );
function mai_global_body_class( $classes ) {
    $classes[] = 'no-js';
    if ( is_singular() ) {
        $classes[] = 'singular';
    }
    return $classes;
}
