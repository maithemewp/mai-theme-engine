<?php
/**
 * Mai Pro Engine.
 *
 * @author   Mike Hemberger
 *
 * @version  1.0.0
 */


// Add 'nav-header' class to header_left and header_right menus, for easier CSS styling
add_filter( 'wp_nav_menu_args', 'mai_nav_header_class' );
function mai_nav_header_class( $args ) {
	if ( in_array( $args['theme_location'], array( 'header_left', 'header_right' ) ) ) {
		$args['menu_class'] = 'nav-header ' . $args['menu_class'];
	}
	return $args;
}

// Add skip link needs to secondary nav
add_filter( 'genesis_skip_links_output', 'mai_add_nav_secondary_skip_link' );
function mai_add_nav_secondary_skip_link( $links ) {
	$new_links = $links;
	array_splice( $new_links, 1 );
	if ( has_nav_menu( 'secondary' ) ) {
		$new_links['genesis-nav-secondary'] = __( 'Skip to secondary navigation', 'genesis' );
	}
	$links = array( 'mai-toggle' => __( 'Menu', 'genesis' ) ) + array_merge( $new_links, $links );
	return $links;
}

// Limit Secondary and Utility nav menus to top level items only.
add_filter( 'wp_nav_menu_args', 'mai_limit_nav_secondary_menu_depth' );
function mai_limit_nav_secondary_menu_depth( $args ) {
	// If secondary nav
	if ( 'secondary' == $args['theme_location'] ) {
		// Limit to 1 level
		$args['depth'] = 1;
	}
	return $args;
}

// Reposition the secondary navigation menu
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_before_footer', 'genesis_do_subnav' );

// Reposition the breadcrumbs
remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
add_action( 'genesis_before_content_sidebar_wrap', 'genesis_do_breadcrumbs', 12 );

// Move archive pagination
remove_action( 'genesis_after_endwhile', 'genesis_posts_nav' );
add_action( 'genesis_after_loop', 'genesis_posts_nav' );

// Add previous/next post links to single posts
add_action( 'genesis_entry_footer', 'genesis_prev_next_post_nav' );

/**
 * Add mobile menu on custom hook, after the site header row
 * We need to return this data because it's used via a genesis_structural_wrap filter
 *
 * @return mixed
 */
function mai_get_mobile_menu() {

	// Widget areas
	$widget_mobile		  = is_active_sidebar( 'mobile_menu' );
	$widget_header_before = is_active_sidebar( 'header_before' );
	$widget_header_left	  = is_active_sidebar( 'header_left' );
	$widget_header_right  = is_active_sidebar( 'header_right' );

	// Menu locations
	$header_left_nav = wp_nav_menu( array(
		'theme_location' => 'header_left',
		'echo'           => false,
		'fallback_cb'    => false,
	) );
	$header_right_nav = wp_nav_menu( array(
		'theme_location' => 'header_right',
		'echo'           => false,
		'fallback_cb'    => false,
	) );
	$primary_nav = wp_nav_menu( array(
		'theme_location' => 'primary',
		'echo'           => false,
		'fallback_cb'    => false,
	) );
	$secondary_nav = wp_nav_menu( array(
		'theme_location' => 'secondary',
		'echo'           => false,
		'fallback_cb'    => false,
	) );

	// Bail if no mobile menu content
	if ( ! ( $widget_mobile || $widget_header_before || $widget_header_left || $widget_header_right || $header_left_nav || $header_right_nav || $primary_nav || $secondary_nav ) ) {
		return;
	}


	$menu = '';

	$menu .= '<div id="mai-menu" class="mai-menu">';

		$menu .= '<div class="mai-menu-outer">';
		$menu .= '<div class="mai-menu-inner">';

			/**
			 * I hate to use output buffering
			 * but there's no easy way to return widget areas, so bahhh.
			 */
			ob_start();

				if ( $widget_mobile ) {

					dynamic_sidebar( 'mobile_menu' );

				} else {

					get_search_form();

					if ( $widget_header_left || $header_left_nav ) {
						if ( ! empty( $header_left_nav ) ) {
							echo $header_left_nav;
						}
						genesis_widget_area( 'header_left' );
					}

					if ( $widget_header_left || $header_right_nav ) {
						if ( ! empty( $header_right_nav ) ) {
							echo $header_right_nav;
						}
						genesis_widget_area( 'header_left' );
					}

					if ( ! empty( $primary_nav ) ) {
						echo $primary_nav;
					}

					if ( ! empty( $secondary_nav ) ) {
						echo $secondary_nav;
					}

				}

			$menu .= ob_get_clean();

		$menu .= '</div>';
		$menu .= '</div>';

	$menu .= '</div>';

	return $menu;
}
