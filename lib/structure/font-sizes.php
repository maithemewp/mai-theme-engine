<?php

/**
 * Add text size class to the body.
 *
 * @param   array  $classes  The body classes.
 *
 * @return  array  The modified classes.
 */
add_filter( 'body_class', 'mai_body_text_size' );
function mai_body_text_size( $classes ) {
	$classes[] = 'text-md';
	return $classes;
}

/**
 * Add text size class to the footer widgets.
 *
 * @param   array  $attributes  The element attributes.
 *
 * @return  array  The modified attributes.
 */
// add_filter( 'genesis_attr_footer-widgets', 'mai_footer_widgets_text_size' );
function mai_footer_widgets_text_size( $attributes ) {
	$attributes['class'] .= ' text-sm';
	return $attributes;
}

/**
 * Add text size class to the site footer.
 *
 * @param   array  $attributes  The element attributes.
 *
 * @return  array  The modified attributes.
 */
add_filter( 'genesis_attr_site-footer', 'mai_site_footer_text_size' );
function mai_site_footer_text_size( $attributes ) {
	$attributes['class'] .= ' text-sm';
	return $attributes;
}
