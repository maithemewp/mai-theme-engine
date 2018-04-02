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
	$col = new Mai_Col( $atts, $content );
	return $col->render();
}
