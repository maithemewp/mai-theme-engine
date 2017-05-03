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

// Move archive pagination
remove_action( 'genesis_after_endwhile', 'genesis_posts_nav' );
add_action( 'genesis_after_loop', 'genesis_posts_nav' );

/**
 * Add mobile menu on custom hook, after the site header row
 * We need to return this data because it's used via a genesis_structural_wrap filter
 *
 * @return mixed
 */
function mai_get_mobile_menu() {

	// Widget areas
	$widget_mobile		 = is_active_sidebar( 'mobile_menu' );
	$widget_header_left	 = is_active_sidebar( 'header_left' );
	$widget_header_right = is_active_sidebar( 'header_right' );

	// Menu locations
	$utility_nav = wp_nav_menu( array(
		'theme_location' => 'utility',
		'echo'           => false,
		'fallback_cb'    => false,
	) );
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
	if ( ! ( $widget_mobile || $widget_header_left || $widget_header_right || $utility_nav || $header_left_nav || $header_right_nav || $primary_nav || $secondary_nav ) ) {
		return;
	}

	$menu = '';

	$menu .= '<div id="mai-menu" class="mai-menu" style="display:none;">';

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

					if ( ! empty( $utility_nav ) ) {
						echo $utility_nav;
					}

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

	return $menu;

}
