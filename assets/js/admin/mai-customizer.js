/**
 * https://codex.wordpress.org/Theme_Customization_API
 *
 * This file adds some LIVE to the Theme Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 */
( function( $ ) {

	// Update the values in real time.

	// Logo Width.
	wp.customize( 'custom_logo_width', function( value ) {
		value.bind( function( newval ) {
			$( '.custom-logo-link' ).css( 'maxWidth', newval + 'px' );
		});
	} );

	// Top Margin.
	// wp.customize( 'custom_logo_top', function( value ) {
	// 	value.bind( function( newval ) {
	// 		$( '.site-title > a' ).css( 'marginTop', newval + 'px' );
	// 	});
	// } );

	// // Bottom Margin.
	// wp.customize( 'custom_logo_bottom', function( value ) {
	// 	value.bind( function( newval ) {
	// 		$( '.site-title > a' ).css( 'marginBottom', newval + 'px' );
	// 	});
	// } );

} )( jQuery );
