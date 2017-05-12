<?php

/**
 * Add new section shortcode
 * On layouts with no sidebar it will be a full browser/window width section
 *
 * Add parameter of 'image=246' with an image ID from the media library to use a full width background image
 */
add_shortcode( 'section', 'mai_get_section_shortcode' );
function mai_get_section_shortcode( $atts, $content = null ) {

    // Bail if no content
    if ( null == $content ) {
        return;
    }

    // Add shortcode class
    $atts['class'] = isset( $atts['class'] ) ? 'section-shortcode ' . $atts['class'] : 'section-shortcode';

    $output = '';

    $output .= mai_get_section_open( $atts );
    // $output .= trim($content);
    $output .= do_shortcode( trim($content) );
    $output .= mai_get_section_close( $atts );

    return $output;
}
