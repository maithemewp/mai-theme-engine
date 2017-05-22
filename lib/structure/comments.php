<?php
/**
 * Mai Pro Engine.
 *
 * @author   Mike Hemberger
 *
 * @version  1.0.0
 */


/**
 * Add Flexington classes for comments.
 */

add_filter( 'genesis_attr_comment', 'mai_markup_comment' );
function mai_markup_comment( $attributes ) {
	$attributes['class'] .= ' row';
	return $attributes;
}

add_filter( 'genesis_attr_comment-header', 'mai_markup_comment_header' );
function mai_markup_comment_header( $attributes ) {
	$attributes['class'] .= ' row col col-xs-12';
	return $attributes;
}

add_filter( 'genesis_attr_comment-author', 'mai_markup_comment_author' );
function mai_markup_comment_author( $attributes ) {
 	$attributes['class'] .= ' col col-xs-12 row gutter-10 middle-xs bottom-xs-30';
 	return $attributes;
}

add_filter( 'genesis_attr_comment-meta', 'mai_markup_comment_meta' );
function mai_markup_comment_meta( $attributes ) {
	$attributes['class'] .= ' column col col-xs-12 text-xs-right first-xs';
	return $attributes;
}

add_filter( 'genesis_attr_comment-content', 'mai_markup_comment_content' );
function mai_markup_comment_content( $attributes ) {
	$attributes['class'] .= ' col col-xs-12';
	return $attributes;
}

add_filter( 'genesis_attr_comment-reply', 'mai_markup_comment_reply' );
function mai_markup_comment_reply( $attributes ) {
	$attributes['class'] .= ' col col-xs-12 text-xs-right';
	return $attributes;
}
