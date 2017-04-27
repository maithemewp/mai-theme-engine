<?php

/**
 * All of these will be added as a custom theme settings metabox
 * For now, we'll use a simple filter to enable/disable the different options available
 */

// Maybe we should have a big helper function that returns all of the settings as an array

// Add fixed header body class
add_filter( 'body_class', 'mai_do_settings_body_classes' );
function mai_do_settings_body_classes( $classes ) {
	/**
	 * Add fixed header styling
	 * Fixed header currently only works with standard mobile menu
	 *
	 * DO NOT USE WITH SIDE MENU!
	 */
	if ( mai_is_fixed_header_enabled() ) {
		$classes[] = 'fixed-header';
	}

	/**
	 * Add boxed layout body class, which allows custom boxed container/entry styling
	 */
	if ( mai_is_boxed_content_enabled() ) {
		$classes[] = 'boxed-content';
	}

	/**
	 * Use a side mobile menu in place of the standard the mobile menu
	 */
	if ( mai_is_side_menu_enabled() ) {
		$classes[] = 'side-menu';
	}

	return $classes;
}

add_filter( 'post_class', 'mai_do_boxed_content_class' );
add_filter( 'product_cat_class', 'mai_do_boxed_content_class' );
function mai_do_boxed_content_class( $classes ) {
	if ( ! is_main_query() ) {
		return $classes;
	}
    if ( mai_is_boxed_content_enabled() ) {
    	$classes[] = 'boxed';
    }
    return $classes;
}

// Add boxed class to all elements affected by box styling
add_filter( 'genesis_attr_sidebar-primary', 'mai_do_boxed_content_attributes' );
add_filter( 'genesis_attr_sidebar-secondary', 'mai_do_boxed_content_attributes' );
add_filter( 'genesis_attr_author-box', 'mai_do_boxed_content_attributes' );
add_filter( 'genesis_attr_adjacent-entry-pagination', 'mai_do_boxed_content_attributes' );
function mai_do_boxed_content_attributes( $attributes ) {
    if ( mai_is_boxed_content_enabled() ) {
    	$attributes['class'] .= ' boxed';
    }
    return $attributes;
}
