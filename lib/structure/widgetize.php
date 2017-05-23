<?php
/**
 * Mai Pro Engine.
 *
 * @author   Mike Hemberger
 *
 * @version  1.0.0
 */


/**
 * Register widget areas.
 *
 * header_* widget areas output in /lib/structure/header.php
 * site_footer widget area output below.
 * mobile_menu widget area output in /lib/structure/navigation.php
 */
genesis_register_sidebar( array(
	'id'          => 'header_before',
	'name'        => __( 'Before Header', 'maitheme' ),
	'description' => __( 'This is the widget that appears on before the site header.', 'maitheme' ),
) );
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

// Activate After Entry widget area and display it on single posts
add_theme_support( 'genesis-after-entry-widget-area' );

// Add support for footer widgets (1, 2, 3, 4, or 6 )
add_theme_support( 'genesis-footer-widgets', mai_get_footer_widgets_count() );

// Add site footer widget area, registered later just so it shows up at the end of the widget list
add_action( 'after_setup_theme', function(){

	genesis_register_sidebar( array(
		'id'          => 'site_footer',
		'name'        => __( 'Site Footer', 'maitheme' ),
		'description' => __( 'This is the widget that appears in the site footer area.', 'maitheme' ),
	) );

});

/**
 * Output the site footer widget area.
 * Limit any menus to 1 level deep.
 * Add nav menu args for better markup.
 *
 * @return  void
 */
add_action( 'genesis_footer', 'mai_site_footer_widget_area', 8 );
function mai_site_footer_widget_area() {

	if ( ! is_active_sidebar('site_footer') ) {
		return;
	}
	add_filter( 'wp_nav_menu_args', '_mai_limit_menu_depth' );
	add_filter( 'wp_nav_menu_args', 'genesis_header_menu_args' );
	genesis_widget_area( 'site_footer' );
	remove_filter( 'wp_nav_menu_args', '_mai_limit_menu_depth' );
	remove_filter( 'wp_nav_menu_args', 'genesis_header_menu_args' );
}

// Helper function to limit to 1 level deep
function _mai_limit_menu_depth( $args ) {
	$args['depth'] = 1;
	return $args;
}
