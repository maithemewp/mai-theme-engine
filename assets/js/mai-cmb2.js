( function ( document, $, undefined ) {
    'use strict';

	var $hideBanner  = $( '#hide_banner' ),
		$bannerField = $( '.cmb2-id-banner' );

	if ( $hideBanner.prop( "checked" ) ) {
		$bannerField.hide();
	}
	$( '.cmb2-id-hide-banner' ).on( 'click', '.cmb-td', function(){
		if ( $hideBanner.prop( 'checked' ) ) {
			$bannerField.slideUp("fast");
		} else {
			$bannerField.slideDown("fast");
		}
	});

})( document, jQuery );