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

    $defaults = array(
        'class'    => '',
        'bg_color' => '',
        'image'    => null,
        'overlay'  => false,
        'wrap'     => true,
        'inner'    => true,
    );

    // Filter these defaults, this allows the /lib/ to be updated later without affecting a customized theme
    $defaults = apply_filters( 'mai_section_defaults', $defaults );

    /**
     * Shortcode section attributes
     * Note, these are different then the defaults in mai_get_section_* functions
     */
    $atts = shortcode_atts( $defaults, $atts, 'section' );

    // Add shortcode class
    $atts['class'] = 'section-shortcode' . $atts['class'];

    $output = '';

    $output .= mai_get_section_open( $atts );
    $output .= do_shortcode( trim($content) );
    $output .= mai_get_section_close( $atts );

    return $output;
}
