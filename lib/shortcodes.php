<?php

// Enable shortcodes in widgets.
add_filter( 'widget_text', 'do_shortcode' );

// Custom Post Type Archive Intro Text.
add_filter( 'genesis_cpt_archive_intro_text_output', 'do_shortcode' );

// Author Archive Intro Text.
add_filter( 'genesis_author_intro_text_output', 'do_shortcode' );

// Term Archive Intro Text.
add_filter( 'genesis_term_intro_text_output', 'do_shortcode' );

// Filter the content.
add_filter( 'the_content', 'mai_content_filter_shortcodes' );

// Register shortcodes.
add_shortcode( 'callout',              'mai_get_callout_shortcode' );
add_shortcode( 'grid',                 'mai_get_grid_shortcode' );
add_shortcode( 'section',              'mai_get_section_shortcode' );
add_shortcode( 'columns',              'mai_get_columns_shortcode' );
add_shortcode( 'col',                  'mai_get_col_shortcode' );
add_shortcode( 'col_auto',             'mai_get_col_auto_shortcode' );
add_shortcode( 'col_one_twelfth',      'mai_get_col_one_twelfth_shortcode' );
add_shortcode( 'col_one_sixth',        'mai_get_col_one_sixth_shortcode' );
add_shortcode( 'col_one_fourth',       'mai_get_col_one_fourth_shortcode' );
add_shortcode( 'col_one_third',        'mai_get_col_one_third_shortcode' );
add_shortcode( 'col_five_twelfths',    'mai_get_col_five_twelfths_shortcode' );
add_shortcode( 'col_one_half',         'mai_get_col_one_half_shortcode' );
add_shortcode( 'col_seven_twelfths',   'mai_get_col_seven_twelfths_shortcode' );
add_shortcode( 'col_two_thirds',       'mai_get_col_two_thirds_shortcode' );
add_shortcode( 'col_three_fourths',    'mai_get_col_three_fourths_shortcode' );
add_shortcode( 'col_five_sixths',      'mai_get_col_five_sixths_shortcode' );
add_shortcode( 'col_eleven_twelfths',  'mai_get_col_eleven_twelfths_shortcode' );
add_shortcode( 'col_one_whole',        'mai_get_col_one_whole_shortcode' );

function mai_get_callout_shortcode( $atts, $content = null ) {
	$callout = new Mai_Callout( $atts, $content );
	return $callout->render();
}

function mai_get_grid_shortcode( $atts, $content = null ) {
	$grid = new Mai_Grid( $atts, $content );
	return $grid->render();
}

function mai_get_section_shortcode( $atts, $content = null ) {
	$section = new Mai_Section( $atts, $content );
	return $section->render();
}

function mai_get_columns_shortcode( $atts, $content = null ) {
	$columns = new Mai_Columns( $atts, $content );
	return $columns->render();
}

function mai_get_col_shortcode( $atts, $content = null ) {
	$col = new Mai_Col( 'col', $atts, $content );
	return $col->render();
}

function mai_get_col_auto_shortcode( $atts, $content = null ) {
	$col = new Mai_Col( 'auto', $atts, $content );
	return $col->render();
}

function mai_get_col_one_twelfth_shortcode( $atts, $content = null ) {
	$col = new Mai_Col( '1', $atts, $content );
	return $col->render();
}

function mai_get_col_one_sixth_shortcode( $atts, $content = null ) {
	$col = new Mai_Col( '2', $atts, $content );
	return $col->render();
}

function mai_get_col_one_fourth_shortcode( $atts, $content = null ) {
	$col = new Mai_Col( '3', $atts, $content );
	return $col->render();
}

function mai_get_col_one_third_shortcode( $atts, $content = null ) {
	$col = new Mai_Col( '4', $atts, $content );
	return $col->render();
}

function mai_get_col_five_twelfths_shortcode( $atts, $content = null ) {
	$col = new Mai_Col( '5', $atts, $content );
	return $col->render();
}

function mai_get_col_one_half_shortcode( $atts, $content = null ) {
	$col = new Mai_Col( '6', $atts, $content );
	return $col->render();
}

function mai_get_col_seven_twelfths_shortcode( $atts, $content = null ) {
	$col = new Mai_Col( '7', $atts, $content );
	return $col->render();
}

function mai_get_col_two_thirds_shortcode( $atts, $content = null ) {
	$col = new Mai_Col( '8', $atts, $content );
	return $col->render();
}

function mai_get_col_three_fourths_shortcode( $atts, $content = null ) {
	$col = new Mai_Col( '9', $atts, $content );
	return $col->render();
}

function mai_get_col_five_sixths_shortcode( $atts, $content = null ) {
	$col = new Mai_Col( '10', $atts, $content );
	return $col->render();
}

function mai_get_col_eleven_twelfths_shortcode( $atts, $content = null ) {
	$col = new Mai_Col( '11', $atts, $content );
	return $col->render();
}

function mai_get_col_one_whole_shortcode( $atts, $content = null ) {
	$col = new Mai_Col( '12', $atts, $content );
	return $col->render();
}

/**
 * Add utility classes to the default gallery shortcode.
 *
 * @since   1.3.8
 *
 * @param  string  $output    The gallery output. Default empty.
 * @param  array   $atts      Attributes of the gallery shortcode.
 * @param  int     $instance  Unique numeric ID of this gallery shortcode instance.
 *
 * @return  string  The gallery HTML.
 */
add_filter( 'post_gallery', 'mai_post_gallery', 10, 3 );
function mai_post_gallery( $output, $atts, $instance ) {

	// Bail if not a default gallery. This fixes compatibility with Jetpack galleries.
	if ( isset( $atts['type'] ) && ! in_array( $atts['type'], array( 'default', 'thumbnails' ) ) ) {
		return $output;
	}

	// Remove filter to avoid infinite loop.
	remove_filter( 'post_gallery', 'mai_post_gallery', 10, 3 );
	add_filter( 'gallery_style', 'mai_gallery_style' );

	// Make sure we have a columns value.
	$atts = wp_parse_args( $atts, array( 'columns' => 3 ) );

	// Get the full gallery markup.
	$output = gallery_shortcode( $atts );

	// Build our new entry/item classes.
	$classes = 'gallery-item col';
	$classes = mai_add_classes( mai_get_col_classes_by_columns( $atts['columns'] ), $classes );
	$classes = mai_add_classes( 'bottom-xs-md', $classes );
	$output  = str_replace( 'gallery-item', $classes, $output );

	// Add filter back incase there is another gallery.
	add_filter( 'post_gallery', 'mai_post_gallery', 10, 3 );
	remove_filter( 'gallery_style', 'mai_gallery_style' );

	return $output;
}

/**
 * Add utility classes to the default gallery shortcode wrap.
 *
 * @since    1.4.2
 * @updated  1.4.3
 *
 * @param   string   $output  Default CSS styles and opening HTML div container
 *                            for the gallery shortcode output.
 *
 * @return  string  The gallery HTML.
 */
function mai_gallery_style( $output ) {
	return str_replace( "class='", "class='row gutter-md ", $output );
}
