<?php

/**
 * Modify the defaults for [post_date] shortcode.
 *
 * @param   array  $out    The output array of shortcode attributes.
 * @param   array  $pairs  The supported attributes and their defaults.
 * @param   array  $atts   The user defined shortcode attributes.
 *
 * @return  array  Modified $atts
 */
add_filter( 'shortcode_atts_post_date', 'mai_post_date_defaults', 10, 3 );
function mai_post_date_defaults( $out, $pairs, $atts ) {
	if ( ! isset( $atts['after'] ) ) {
		$out['after']  = '';
	}
	if ( ! isset( $atts['before'] ) ) {
		$out['before'] = '';
	}
	if ( ! isset( $atts['format'] ) ) {
		$out['format'] = 'M j, Y';
	}
	return $out;
}

/**
 * Modify the defaults for [post_author_link] and [post_author_posts_link] shortcodes.
 *
 * @param   array  $out    The output array of shortcode attributes.
 * @param   array  $pairs  The supported attributes and their defaults.
 * @param   array  $atts   The user defined shortcode attributes.
 *
 * @return  array  Modified $atts
 */
add_filter( 'shortcode_atts_post_author_link', 'mai_post_author_link_defaults' );
add_filter( 'shortcode_atts_post_author_posts_link', 'mai_post_author_link_defaults' );
function mai_post_author_link_defaults( $out, $pairs, $atts ) {
	if ( ! isset( $atts['before'] ) ) {
		$out['before'] = '//&nbsp;&nbsp;by&nbsp;';
	}
	return $out;
}

// Customize the entry meta in the entry header
add_filter( 'genesis_post_info', 'mai_post_info' );
function mai_post_info( $post_info ) {
	$post_info = '[post_date] [post_author_posts_link]';
	if ( ! mai_is_flex_loop() ) {
		$post_info .= '[post_comments before="&nbsp;//&nbsp;&nbsp;"] [post_edit before="&nbsp;//&nbsp;&nbsp;"]';
	}
	return $post_info;
}

// Add all public taxonomies to post meta
add_filter( 'genesis_post_meta','mai_post_meta', 11 );
function mai_post_meta( $post_meta ) {
	global $post;

	$taxos = get_object_taxonomies( $post, 'objects' );

	if ( $taxos ) {

		$taxos = apply_filters( 'mai_post_meta_taxos', $taxos );

		$post_meta = $shortcodes = '';
		foreach ( $taxos as $taxonomy ) {
			// Skip if not a public taxonomy.
			if ( ! $taxonomy->public ) {
				continue;
			}
			$shortcodes .= '[post_terms taxonomy="' . $taxonomy->name . '" before="' . $taxonomy->labels->singular_name . ': "]';
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
