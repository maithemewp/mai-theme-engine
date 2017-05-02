( function ( document, $, undefined ) {
    'use strict';

    /**
     * Hide banner upload field if the hide banner checkbox is checked.
     */
	var $hideBanner  = $( '#hide_banner' ),
		$bannerField = $( '.cmb2-id-banner' );

	if ( $hideBanner.is( ':checked' )  ) {
		$bannerField.hide();
	}
	$hideBanner.change(function() {
		if ( $(this).prop( 'checked' ) ) {
			$bannerField.slideUp('fast');
		} else {
			$bannerField.slideDown('fast');
		}
	});

	/**
	 * Hide the image size and image alignment fields
	 * if include the featured image checkbox is unchecked.
	 */
	var $includeImage 	= $( '#content_archive_thumbnail' ),
		$imageSize 	  	= $( '.cmb2-id-image-size' ),
		$imageAlignment = $( '.cmb2-id-image-alignment' );

	if ( ! $includeImage.is( ':checked' )  ) {
		$imageSize.hide();
		$imageAlignment.hide();
	}
	$includeImage.change(function() {
		console.log( $(this).is( ':checked' ) );
		if ( $(this).is( ':checked' ) ) {
			$imageSize.show();
			$imageAlignment.show();
		} else {
			$imageSize.hide();
			$imageAlignment.hide();
		}
	});

})( document, jQuery );