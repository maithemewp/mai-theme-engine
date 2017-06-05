<?php

// Add custom body class to the head
add_filter( 'body_class', 'mai_landing_page_body_class' );
function mai_landing_page_body_class( $classes ) {
   $classes[] = 'mai-landing';
   return $classes;
}

// Remove site header elements
add_filter( '_mai_header_before', '__return_false' );
add_filter( '_mai_header_left', '__return_false' );
add_filter( '_mai_header_right', '__return_false' );
add_filter( '_mai_header_after', '__return_false' );
add_filter( '_mai_mobile_menu', '__return_false' );

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

// Remove the site footer widget area
remove_action( 'genesis_footer', 'mai_site_footer_widget_area', 8 );

// Run the Genesis loop
genesis();
