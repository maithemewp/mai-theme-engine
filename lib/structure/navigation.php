<?php
/**
 * Mai Theme Engine.
 *
 * @author   Mike Hemberger
 *
 * @version  1.0.0
 */


// Add 'nav-header' class to header_left and header_right menus, for easier CSS styling.
add_filter( 'wp_nav_menu_args', 'mai_nav_header_class' );
function mai_nav_header_class( $args ) {
	if ( in_array( $args['theme_location'], array( 'header_left', 'header_right' ) ) ) {
		$args['menu_class'] = 'nav-header ' . $args['menu_class'];
	}
	return $args;
}

// Add skip link needs to secondary nav.
add_filter( 'genesis_skip_links_output', 'mai_add_nav_skip_links' );
function mai_add_nav_skip_links( $links ) {
	$new_links = $links;
	// Leave only primary nav.
	array_splice( $new_links, 1 );
	$header_nav = array();
	if ( has_nav_menu( 'header_left' ) ) {
		$header_nav['genesis-nav-header-left'] = __( 'Skip to left header navigation', 'mai-theme-engine' );
	}
	if ( has_nav_menu( 'header_right' ) ) {
		$header_nav['genesis-nav-header-right'] = __( 'Skip to right header navigation', 'mai-theme-engine' );
	}
	$new_links = array_merge( $header_nav, $new_links );
	if ( has_nav_menu( 'secondary' ) ) {
		$new_links['genesis-nav-secondary'] = __( 'Skip to secondary navigation', 'mai-theme-engine' );
	}
	$links = array( 'mai-toggle' => __( 'Menu', 'mai-theme-engine' ) ) + array_merge( $new_links, $links );
	return $links;
}

/**
 * Add ID to header left navigation.
 *
 * @param   array  $attributes
 *
 * @return  array  The modified attributes.
 */
add_filter( 'genesis_attr_nav-header_left', 'mai_add_nav_header_left_id' );
function mai_add_nav_header_left_id( $attributes ) {
	$attributes['id'] = 'genesis-nav-header-left';
	return $attributes;
}

/**
 * Add ID to header right navigation.
 *
 * @param   array  $attributes
 *
 * @return  array  The modified attributes.
 */
add_filter( 'genesis_attr_nav-header_right', 'mai_add_nav_header_right_id' );
function mai_add_nav_header_right_id( $attributes ) {
	$attributes['id'] = 'genesis-nav-header-right';
	return $attributes;
}

/**
 * Add ID to secondary navigation.
 *
 * @param   array  $attributes
 *
 * @return  array  The modified attributes.
 */
add_filter( 'genesis_attr_nav-secondary', 'mai_add_nav_secondary_id' );
function mai_add_nav_secondary_id( $attributes ) {
	$attributes['id'] = 'genesis-nav-secondary';
	return $attributes;
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

// Reposition the secondary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_before_footer', 'genesis_do_subnav' );

// Reposition the breadcrumbs.
remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
add_action( 'genesis_before_content_sidebar_wrap', 'genesis_do_breadcrumbs', 12 );

// Move archive pagination.
remove_action( 'genesis_after_endwhile', 'genesis_posts_nav' );
add_action( 'genesis_after_loop', 'genesis_posts_nav' );

// Add previous/next post links to single posts.
add_post_type_support( 'post', 'genesis-adjacent-entry-nav' );

// Remove Genesis filters added in 2.7 to add float classes, since we use flexbox.
remove_filter( 'genesis_attr_pagination-previous', 'genesis_adjacent_entry_attr_previous_post' );
remove_filter( 'genesis_attr_pagination-next', 'genesis_adjacent_entry_attr_next_post' );

/**
 * Filters the adjacent post link.
 *
 * The dynamic portion of the hook name, `$adjacent`, refers to the type
 * of adjacency, 'next' or 'previous'.
 *
 * @updated 1.5.3   Account for changes in Genesis 2.7.0.
 *
 * @param   string   $output    The adjacent post link.
 * @param   string   $format    Link anchor format.
 * @param   string   $link      Link permalink format.
 * @param   WP_Post  $post      The adjacent post.
 * @param   string   $adjacent  Whether the post is previous or next.
 *
 * @return  string|HTML  The post link and image HTML.
 */
add_filter( 'previous_post_link', 'mai_adjacent_post_link_thumbnail', 10, 5 );
add_filter( 'next_post_link', 'mai_adjacent_post_link_thumbnail', 10, 5 );
function mai_adjacent_post_link_thumbnail( $output, $format, $link, $post, $adjacent ) {
	$output = _mai_get_adjacent_post_output( $output, $post );
	// G < 2.7
	$output = str_replace( '&#x000AB;', '<span class="pagination-icon">&#x000AB;</span>', $output );
	$output = str_replace( '&#x000BB;', '<span class="pagination-icon">&#x000BB;</span>', $output );
	// G >= 2.7
	$output = str_replace( '&#xAB;', '<span class="pagination-icon">&#xAB;</span>', $output );
	$output = str_replace( '&#xBB;', '<span class="pagination-icon">&#xBB;</span>', $output );
	return $output;
}

function _mai_get_adjacent_post_output( $output, $post ) {
	$image_id = get_post_thumbnail_id( $post );
	if ( $image_id ) {
		$image = wp_get_attachment_image( $image_id, 'tiny' );
		if ( $image ) {
			// G < 2.7
			$output = str_replace( '&#x000AB;', '&#x000AB;' . $image, $output );
			$output = str_replace( '&#x000BB;', $image . '&#x000BB;', $output );
			// G >= 2.7
			$output = str_replace( '&#xAB;', '&#xAB;' . $image, $output );
			$output = str_replace( '&#xBB;', $image . '&#xBB;', $output );

		}
	}
	return $output;
}

/**
 * Add mobile menu on custom hook, after the site header row.
 * We need to return this data because it's used via a genesis_structural_wrap filter.
 *
 * @return  mixed
 */
function mai_get_mobile_menu() {

	// Widget areas
	$widget_mobile       = is_active_sidebar( 'mobile_menu' );
	$widget_header_left  = is_active_sidebar( 'header_left' );
	$widget_header_right = is_active_sidebar( 'header_right' );

	// Menu locations
	$mobile_nav = wp_nav_menu( array(
		'theme_location' => 'mobile',
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
	if ( ! ( $widget_mobile || $widget_header_left || $widget_header_right || $mobile_nav || $header_left_nav || $header_right_nav || $primary_nav || $secondary_nav ) ) {
		return;
	}


	$menu = '';

	$menu .= '<div id="mai-menu" class="mai-menu">';

		$menu .= '<div class="mai-menu-outer">';
		$menu .= '<div class="mai-menu-inner">';

			if ( mai_is_side_menu_enabled() ) {
				$menu .= '<button class="button menu-close icon icon-left" role="button">' . __( 'Close', 'mai-theme-engine' ) . '</button>';
			}

			/**
			 * I hate to use output buffering
			 * but there's no easy way to return widget areas, so bahhh.
			 */
			ob_start();

				if ( $widget_mobile || $mobile_nav ) {

					if ( ! empty( $mobile_nav ) ) {
						get_search_form();
						echo $mobile_nav;
					}

					genesis_widget_area( 'mobile_menu' );

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

						genesis_widget_area( 'header_right' );
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
