<?php
/**
 * Template Name: Landing Page
 */

// Add custom body class to the head
add_filter( 'body_class', 'mai_landing_page_body_class' );
function mai_landing_page_body_class( $classes ) {
   $classes[] = 'mai-landing';
   return $classes;
}

//
add_action( 'genesis_entry_content', 'asjfkljaslfgjsalgfjsklajflsjgflk' );
function asjfkljaslfgjsalgfjsklajflsjgflk() {
	echo '<h1>In Plugin!</h1>';
}

// Remove site header elements
remove_filter( 'body_class', 'mai_do_fixed_header_body_class' );
add_filter( 'mai_utility_nav', '__return_false' );
add_filter( 'mai_header_left_content', '__return_false' );
add_filter( 'mai_header_right_content', '__return_false' );
add_filter( 'mai_mobile_menu', '__return_false' );

// Remove navigation
remove_action( 'genesis_after_header', 'genesis_do_nav' );
remove_action( 'genesis_before_footer', 'genesis_do_subnav' );

// Remove breadcrumbs
remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
remove_action( 'genesis_before_content_sidebar_wrap', 'genesis_do_breadcrumbs', 12 );

// Remove page title
remove_action('genesis_entry_header', 'genesis_do_post_title');

// Remove site footer widgets
remove_action( 'genesis_before_footer', 'genesis_footer_widget_areas' );

// Run the Genesis loop
genesis();
