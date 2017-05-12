<?php

/**
 * Add callout shortcode
 */
add_shortcode( 'callout', 'mai_get_callout_shortcode' );
function mai_get_callout_shortcode( $atts, $content = null ) {

    // Bail if no content
    if ( null == $content ) {
        return;
    }

    $defaults = array(
        'color' => '',
    );

    /**
     * Shortcode callout attributes
     */
    $atts = shortcode_atts( $defaults, $atts, 'callout' );

    $attributes['class'] = 'callout';

    if ( $atts['color'] ) {
    	$attributes['class'] .= mai_sanitized_html_classes( $atts['color'] );
    }

    $output = sprintf( '<div %s>%s</div>', genesis_attr( 'mai-callout', $attributes ), do_shortcode( wpautop( trim($content) ) ) );

    return $output;
}
