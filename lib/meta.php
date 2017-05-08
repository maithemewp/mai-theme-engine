<?php

/**
 * Modify the defaults for [post_date] shortcode
 * This is modified per-shortcode since [grid] shortcode now uses this shortcode
 * when showing meta/date.
 *
 * @param   $atts  Attributes in Genesis genesis_post_date_shortcode()
 *
 * @return  array  Modified $atts
 */
add_filter( 'shortcode_atts_post_date', 'mai_post_date_defaults' );
function mai_post_date_defaults( $atts ) {
	$atts['after']	= '';
	$atts['before']	= '';
	$atts['format']	= 'M j, Y';
	return $atts;
}

/**
 * Modify the defaults for [post_author_link] shortcode
 * This is modified per-shortcode since [display-posts] shortcode now uses this shortcode
 * when include_author is true
 *
 * @param  $atts  Attributes in Genesis genesis_post_author_link_shortcode()
 */
add_filter( 'shortcode_atts_post_author_link', 'mai_post_author_link_defaults' );
add_filter( 'shortcode_atts_post_author_posts_link', 'mai_post_author_link_defaults' );
function mai_post_author_link_defaults( $atts ) {
	$atts['before']	= ' &middot;';
	if ( ! mai_is_flex_loop() ) {
		$atts['before'] .= '&nbsp;by';
	}
	return $atts;
}

// Customize the entry meta in the entry header
add_filter( 'genesis_post_info', 'mai_post_info_filter' );
function mai_post_info_filter( $post_info ) {
	$post_info = '[post_date] [post_author_posts_link]';
	if ( ! mai_is_flex_loop() ) {
		$post_info .= '[post_comments before=" &middot; "] [post_edit before="&nbsp; &middot; "]';
	}
	return $post_info;
}

// Add all public taxonomies to post meta
add_filter( 'genesis_post_meta','mai_post_meta_filter', 11 );
function mai_post_meta_filter( $post_meta ) {
	global $post;
    $taxos = get_post_taxonomies($post);
    if ( $taxos ) {
    	$post_meta = $shortcodes = '';
    	foreach ( $taxos as $tax ) {
    		$taxonomy = get_taxonomy($tax);
    		$shortcodes .= '[post_terms taxonomy="' . $tax . '" before="' . $taxonomy->labels->singular_name . ': "]';
    	}
		$post_meta = $shortcodes;
    }
	return $post_meta;
}

// Modify the size of the Gravatar in the author box
add_filter( 'genesis_author_box_gravatar_size', 'mai_author_box_gravatar_size' );
function mai_author_box_gravatar_size( $size ) {
	return '200';
}

// Modify the size of the Gravatar in comments
add_filter( 'genesis_comment_list_args', 'mai_comments_gravatar' );
function mai_comments_gravatar( $args ) {
	$args['avatar_size'] = 160;
	return $args;
}
