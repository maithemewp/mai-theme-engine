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
add_filter( 'shortcode_atts_post_author_link', 'mai_post_author_link_defaults', 10, 3 );
add_filter( 'shortcode_atts_post_author_posts_link', 'mai_post_author_link_defaults', 10, 3 );
function mai_post_author_link_defaults( $out, $pairs, $atts ) {
	if ( ! isset( $atts['before'] ) ) {
		$out['before'] = '//&nbsp;&nbsp;by&nbsp;';
	}
	return $out;
}

// Customize the entry meta in the entry header.
add_filter( 'genesis_post_info', 'mai_post_info' );
function mai_post_info( $post_info ) {
	// Bail if we can use Customizer setting.
	if ( _mai_use_customizer_entry_meta_content( 'before' ) ) {
		return $post_info;
	}
	$post_info = '[post_date] [post_author_posts_link]';
	if ( ! mai_is_flex_loop() ) {
		$post_info .= '[post_comments before="//&nbsp;&nbsp;"] [post_edit before="&nbsp;//&nbsp;&nbsp;"]';
	}
	return $post_info;
}

// Add all public taxonomies to post meta.
add_filter( 'genesis_post_meta', 'mai_post_meta', 11 );
function mai_post_meta( $post_meta ) {
	// Bail if we can use Customizer setting.
	if ( _mai_use_customizer_entry_meta_content( 'after' ) ) {
		return $post_meta;
	}
	return mai_get_the_posts_meta( get_the_ID() );
}

/**
 * This is a bit of a mess since Genesis added the Customizer settings.
 * We don't need our filters anymore but can't break existing sites.
 *
 * The following scenario will stop this filter from running:
 * 1. Is singular.
 * Or all 3 of these.
 * 1. Genesis is at least versions 3.2 (entry meta Customizer settings added here)
 * 2. Mai DB version is at least 1600
 * 3. Entry meta customizer setting is empty.
 *
 * @since   1.11.0
 *
 * @param   string  $before_or_after  Must be 'before' or 'after'.
 *
 * @return  bool
 */
function _mai_use_customizer_entry_meta_content( $before_or_after ) {
	if ( is_singular()
		&& version_compare( genesis_get_option( 'theme_version' ), 3.2, '>=' )
		&& version_compare( get_option( 'mai_db_version' ), 1600, '>=' )
		&& ! empty( genesis_get_option( "entry_meta_{$before_or_after}_content" ) )
		) {
		// Yep.
		return true;
	}
	// Nope.
	return false;
}

// Modify the size of the Gravatar in the author box.
add_filter( 'genesis_author_box_gravatar_size', 'mai_author_box_gravatar_size' );
function mai_author_box_gravatar_size( $size ) {
	return '200';
}

// Modify the size of the Gravatar in comments.
add_filter( 'genesis_comment_list_args', 'mai_comments_gravatar' );
function mai_comments_gravatar( $args ) {
	$args['avatar_size'] = 160;
	return $args;
}
