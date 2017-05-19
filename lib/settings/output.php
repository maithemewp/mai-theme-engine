<?php

/**
 * Mai Theme.
 *
 * @package MaiTheme
 * @author  Mike Hemberger
 * @license GPL-2.0+
 * @link    https://bizbudding.com
 */

/**
 * Add body class to enabled specific settings.
 *
 * @since   1.0.0
 *
 * @param   array  The body classes.
 *
 * @return  array  The modified classes.
 */
add_filter( 'body_class', 'mai_do_settings_body_classes' );
function mai_do_settings_body_classes( $classes ) {
	/**
	 * Add sticky header styling
	 * Fixed header currently only works with standard mobile menu
	 *
	 * DO NOT USE WITH SIDE MENU!
	 */
	if ( mai_is_sticky_header_enabled() && ! is_page_template( 'landing.php' ) ) {
		$classes[] = 'sticky-header';
	}

	if ( mai_is_shrink_header_enabled() && ! is_page_template( 'landing.php' ) ) {
		$classes[] = 'shrink-header';
	}

	/**
	 * Use a side mobile menu in place of the standard the mobile menu
	 */
	if ( mai_is_side_menu_enabled() ) {
		$classes[] = 'side-menu';
	}

	return $classes;
}
