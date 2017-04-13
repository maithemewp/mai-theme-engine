<?php
/**
 * Mai Theme.
 *
 * WARNING: This file is part of the core Mai Theme framework.
 * The goal is to keep all files in /lib/ untouched.
 * That way we can easily update the core structure of the theme on existing sites without breaking things
 *
 * @author   Mike Hemberger
 *
 * @version  1.0.0
 */


// Reposition secondary sidebar, we'll add it back later where we need it
remove_action( 'genesis_after_content_sidebar_wrap', 'genesis_get_sidebar_alt' );

/**
 * Filter the footer-widgets context of the genesis_structural_wrap to add a div before the closing wrap div.
 *
 * @param  	string  $output 		 	The markup to be returned
 * @param  	string  $original_output 	Set to either 'open' or 'close'
 *
 * @return  string 	The footer markup
 */
add_filter( 'genesis_structural_wrap-footer-widgets', 'mai_footer_widgets_flexington_row', 10, 2 );
function mai_footer_widgets_flexington_row( $output, $original_output ) {
    if ( 'open' == $original_output ) {
    	$output = $output . '<div class="row gutter-30">';
    }
    elseif ( 'close' == $original_output ) {
    	$output = '</div>' . $output;
    }
    return $output;
}

/**
 * Filter the footer-widget markup to add flexington column classes
 *
 * @param  	string  $output 		 	The markup to be returned
 * @param  	int 	$footer_widgets 	The number of footer widgets via add_theme_support( 'genesis-footer-widgets', 4 );
 *
 * @return  string 	The footer markup
 */
add_filter( 'genesis_footer_widget_areas', 'mai_footer_widget_flexington_cols', 10, 2 );
function mai_footer_widget_flexington_cols( $output, $footer_widgets ) {
    switch ( $footer_widgets ) {
        case '1':
            $classes = 'widget-area col col-xs-12 center-xs"';
            break;
        case '2':
            $classes = 'widget-area col col-xs-12 col-sm-6"';
            break;
        case '3':
            $classes = 'widget-area col col-xs-12 col-sm-6 col-md-4"';
            break;
        case '4':
            $classes = 'widget-area col col-xs-12 col-sm-6 col-md-3"';
            break;
        case '6':
            $classes = 'widget-area col col-xs-6 col-sm-4 col-md-2"';
            break;
        default:
            $classes = 'widget-area col col-xs"';

    }
	$output = str_replace( 'widget-area"', $classes, $output );
	return $output;
}
