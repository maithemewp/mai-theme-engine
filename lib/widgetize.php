<?php
/**
 * Mai Theme.
 *
 * WARNING: This file is part of the core Mai Theme framework.
 * The goal is to keep all files in /lib/ untouched.
 * That way we can easily update the core structure of the theme on existing sites without breaking things
 *
 * @author   Mike Hemberger
 *
 * @version  1.0.1
 */


// Register widget areas
genesis_register_sidebar( array(
	'id'          => 'header_left',
	'name'        => __( 'Header Left', 'maitheme' ),
	'description' => __( 'This is the widget that appears on left of the title area.', 'maitheme' ),
) );
genesis_register_sidebar( array(
	'id'          => 'header_right',
	'name'        => __( 'Header Right', 'maitheme' ),
	'description' => __( 'This is the widget that appears on right of the title area.', 'maitheme' ),
) );
genesis_register_sidebar( array(
	'id'          => 'mobile_menu',
	'name'        => __( 'Mobile Menu', 'maitheme' ),
	'description' => __( 'This widget that replaces the default mobile menu.', 'maitheme' ),
) );

// Add support for footer widgets (1, 2, 3, 4, or 6 )
add_theme_support( 'genesis-footer-widgets', mai_get_footer_widgets_count() );

// Activate After Entry widget area and display it on single posts
add_theme_support( 'genesis-after-entry-widget-area' );
