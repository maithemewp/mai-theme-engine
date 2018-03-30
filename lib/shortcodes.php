<?php

// Enable shortcodes in widgets.
add_filter( 'widget_text', 'do_shortcode' );

// Custom Post Type Archive Intro Text.
add_filter( 'genesis_cpt_archive_intro_text_output', 'do_shortcode' );

// Author Archive Intro Text.
add_filter( 'genesis_author_intro_text_output', 'do_shortcode' );

// Term Archive Intro Text.
add_filter( 'genesis_term_intro_text_output', 'do_shortcode' );



/**
 * Filter the content to remove empty <p></p> tags and extray <br /> added by shortcodes.
 *
 * @link https://gist.github.com/bitfade/4555047
 *
 * @return  mixed  Fixed shortcode content
 */
add_filter( 'the_content', 'mai_content_filter_shortcodes' );
function mai_content_filter_shortcodes( $content ) {

	$shortcodes = array(
		'callout',
		'section',
		'columns',
		'col',
		'col_auto',
		'col_one_twelfth',
		'col_one_sixth',
		'col_one_fourth',
		'col_one_third',
		'col_five_twelfths',
		'col_one_half',
		'col_seven_twelfths',
		'col_two_thirds',
		'col_three_fourths',
		'col_five_sixths',
		'col_eleven_twelfths',
		'col_one_whole',
		'grid',
	);

	// Array of custom shortcodes requiring the fix.
	$shortcodes = join( '|', $shortcodes );

	// Cleanup.
	$content = strtr( $content, array ( '<p></p>' => '', '<p>[' => '[', ']</p>' => ']', ']<br />' => ']' ) );

	// Opening tag.
	$content = preg_replace( "/(<p>)?\[($shortcodes)(\s[^\]]+)?\](<\/p>|<br \/>)?/", "[$2$3]", $content );

	// Closing tag.
	$content = preg_replace( "/(<p>)?\[\/($shortcodes)](<\/p>|<br \/>)?/", "[/$2]", $content );

	// Return fixed shortcodes.
	return $content;

}
