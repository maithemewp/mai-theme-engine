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
	$atts = mai_get_col_shortcode_default_sizes_atts( $atts, 'col' );
	$col  = new Mai_Col( $atts, $content );
	return $col->render();
}

function mai_get_col_auto_shortcode( $atts, $content = null ) {
	$atts = mai_get_col_shortcode_default_sizes_atts( $atts, 'auto' );
	$col  = new Mai_Col( $atts, $content );
	return $col->render();
}

function mai_get_col_one_twelfth_shortcode( $atts, $content = null ) {
	$atts = mai_get_col_shortcode_default_sizes_atts( $atts, '1' );
	$col  = new Mai_Col( $atts, $content );
	return $col->render();
}

function mai_get_col_one_sixth_shortcode( $atts, $content = null ) {
	$atts = mai_get_col_shortcode_default_sizes_atts( $atts, '2' );
	$col  = new Mai_Col( $atts, $content );
	return $col->render();
}

function mai_get_col_one_fourth_shortcode( $atts, $content = null ) {
	$atts = mai_get_col_shortcode_default_sizes_atts( $atts, '3' );
	$col  = new Mai_Col( $atts, $content );
	return $col->render();
}

function mai_get_col_one_third_shortcode( $atts, $content = null ) {
	$atts = mai_get_col_shortcode_default_sizes_atts( $atts, '4' );
	$col  = new Mai_Col( $atts, $content );
	return $col->render();
}

function mai_get_col_five_twelfths_shortcode( $atts, $content = null ) {
	$atts = mai_get_col_shortcode_default_sizes_atts( $atts, '5' );
	$col  = new Mai_Col( $atts, $content );
	return $col->render();
}

function mai_get_col_one_half_shortcode( $atts, $content = null ) {
	$atts = mai_get_col_shortcode_default_sizes_atts( $atts, '6' );
	$col  = new Mai_Col( $atts, $content );
	return $col->render();
}

function mai_get_col_seven_twelfths_shortcode( $atts, $content = null ) {
	$atts = mai_get_col_shortcode_default_sizes_atts( $atts, '7' );
	$col  = new Mai_Col( $atts, $content );
	return $col->render();
}

function mai_get_col_two_thirds_shortcode( $atts, $content = null ) {
	$atts = mai_get_col_shortcode_default_sizes_atts( $atts, '8' );
	$col  = new Mai_Col( $atts, $content );
	return $col->render();
}

function mai_get_col_three_fourths_shortcode( $atts, $content = null ) {
	$atts = mai_get_col_shortcode_default_sizes_atts( $atts, '9' );
	$col  = new Mai_Col( $atts, $content );
	return $col->render();
}

function mai_get_col_five_sixths_shortcode( $atts, $content = null ) {
	$atts = mai_get_col_shortcode_default_sizes_atts( $atts, '10' );
	$col  = new Mai_Col( $atts, $content );
	return $col->render();
}

function mai_get_col_eleven_twelfths_shortcode( $atts, $content = null ) {
	$atts = mai_get_col_shortcode_default_sizes_atts( $atts, '11' );
	$col  = new Mai_Col( $atts, $content );
	return $col->render();
}

function mai_get_col_one_whole_shortcode( $atts, $content = null ) {
	$atts = mai_get_col_shortcode_default_sizes_atts( $atts, '12' );
	$col  = new Mai_Col( $atts, $content );
	return $col->render();
}

/**
 * Return the default break attributes by size.
 *
 * @access  private
 *
 * @return  array  The modified attributes.
 */
function mai_get_col_shortcode_default_sizes_atts( $atts, $size ) {
	$atts        = (array) $atts;
	$breaks      = array( 'xs', 'sm', 'md', 'lg', 'xl' );
	$default_set = false;
	foreach ( $breaks as $break ) {
		if ( ! isset( $atts[ $break ] ) ) {
			if ( ! $default_set ) {
				$atts[ $break ] = $size;
				$default_set    = true;
			}
		} else {
			// Each time a break is used we need to add the default after.
			$default_set = false;
		}
	}
	return $atts;
}
