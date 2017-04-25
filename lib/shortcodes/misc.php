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
 * @version  1.0.2
 */

// Enable shortcodes in widgets
add_filter( 'widget_text', 'do_shortcode' );
// Custom Post Type Archive Intro Text
add_filter( 'genesis_cpt_archive_intro_text_output', 'do_shortcode' );
// Author Archive Intro Text
add_filter( 'genesis_author_intro_text_output', 'do_shortcode' );
// Term Archive Intro Text
add_filter( 'genesis_term_intro_text_output', 'do_shortcode' );

/**
 * Add new section shortcode
 * On layouts with no sidebar it will be a full browser/window width section
 *
 * Add parameter of 'image=246' with an image ID from the media library to use a full width background image
 */
add_shortcode( 'section', 'mai_get_section_shortcode' );
function mai_get_section_shortcode( $atts, $content = null ) {

    // Bail if no content
    if ( ! trim($content) ) {
        return;
    }

    // Filter these defaults, this allows the /lib/ to be updated later without affecting a customized theme
    $defaults = apply_filters( 'mai_section_shortcode_defaults', array(
        'class'   => '',
        'image'   => null,
        'overlay' => false,
        'wrap'    => true,
        'inner'   => true,
    ) );

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

/**
 * Filter the content to remove empty <p></p> tags from shortcodes
 *
 * @link https://gist.github.com/bitfade/4555047
 *
 * @return  mixed  Fixed shortcode content
 */
add_filter( 'the_content', 'mai_shortcode_content_filter' );
function mai_shortcode_content_filter( $content ) {

    $shortcodes = array(
        'section',
        'grid',
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
    );

    // Array of custom shortcodes requiring the fix
    $shortcodes = join( '|', $shortcodes );

    // Opening tag
    $rep = preg_replace( "/(<p>)?\[($shortcodes)(\s[^\]]+)?\](<\/p>|<br \/>)?/", "[$2$3]", $content );

    // Closing tag
    $rep = preg_replace( "/(<p>)?\[\/($shortcodes)](<\/p>|<br \/>)?/", "[/$2]", $rep );

    // Return fixed shortcodes
    return $rep;
}
