( function ( document, $, undefined ) {
    'use strict';

	var $hideBanner  		= $( '#hide_banner' ),
		$bannerField		= $( '.cmb2-id-banner' ),
		$archiveMetabox		= $( '.mai-content-archive-metabox' ),
		$settingsEnabled	= $( '#enable_content_archive_settings' ),
		$removeLoop			= $( '#remove_loop' ),
		$columns			= $( '#columns' ),
		$includeImage		= $( '#content_archive_thumbnail' ),
		$imageAlignment		= $( '.cmb2-id-image-alignment' ),
		$contentArchive		= $( '#content_archive' ),
		$contentLimit		= $( '.cmb2-id-content-archive-limit' ),
		$includeImage		= $( '#content_archive_thumbnail' ),
		$imageLocation		= $( '.cmb2-id-image-location' ),
		$imageSize			= $( '.cmb2-id-image-size' ),
		$imageAlignment		= $( '.cmb2-id-image-alignment' );

    /**
     * Hide banner upload field if the hide banner checkbox is checked.
     */
	if ( $hideBanner.is( ':checked' )  ) {
		_hideElement( $bannerField );
	}
	$hideBanner.change(function() {
		if ( $(this).prop( 'checked' ) ) {
			_hideElement( $bannerField );
		} else {
			_showElement( $bannerField );
		}
	});

	/**
	 * Hide the archive settings if settings not enabled.
	 */

	// If we have an archive metabox
	if ( $archiveMetabox.length > 0 ) {

		var $nextAll = $removeLoop.parents( '.cmb-row' ).nextAll( '.cmb-row' );
		if ( $removeLoop.is( ':checked' ) ) {
			$nextAll.addClass( 'mai-hidden' );
		}
		$removeLoop.change( function() {
			if ( $(this).is( ':checked' ) ) {
				$nextAll.addClass( 'mai-hidden' );
			} else {
				$nextAll.removeClass( 'mai-hidden' );
			}
		});

		var $archiveRows = $settingsEnabled.parents( '.cmb-row' ).nextAll( '.cmb-row' );

		if ( ! $settingsEnabled.is( ':checked' )  ) {
			$.each( $archiveRows, function( key, value ) {
				$(this).addClass( 'mai-hidden' );
				// $(this).find( 'input' ).attr( 'disabled', true );
				// $(this).find( 'select' ).attr( 'disabled', true );
			});
		}
		$settingsEnabled.change( function() {
			if ( $(this).is( ':checked' ) ) {
				$.each( $archiveRows, function( key, value ) {
					$(this).removeClass( 'mai-hidden' );
				});
			} else {
				$.each( $archiveRows, function( key, value ) {
					$(this).addClass( 'mai-hidden' );
				});
			}
		});

	}

	/**
	 * Hide the image alignment field if columns are set.
	 */

	if ( $columns.val() > 1 ) {
		_hideElement( $imageAlignment );
	}
	$columns.change(function() {
		if ( $(this).val() > 1 ) {
			_hideElement( $imageAlignment );
		} else {
			if ( $includeImage.is( ':checked' ) ) {
				_showElement( $imageAlignment );
			}
		}
	});

	/**
	 * Hide the content limit fields if content archive is set to none or excerpt.
	 */

	if ( ( 'none' == $contentArchive.val() ) || ( 'excerpts' == $contentArchive.val() ) ) {
		_hideElement( $contentLimit );
	}
	$contentArchive.change(function() {
		if ( ( 'none' == $contentArchive.val() ) || ( 'excerpts' == $contentArchive.val() ) ) {
			_hideElement( $contentLimit );
		} else {
			_showElement( $contentLimit );
		}
	});

	/**
	 * Hide the image location, image size, and image alignment fields
	 * if include the featured image checkbox is unchecked.
	 */

	if ( ! $includeImage.is( ':checked' ) ) {
		_hideElement( $imageLocation );
		_hideElement( $imageSize );
		_hideElement( $imageAlignment );
	}
	$includeImage.change(function() {
		if ( $(this).is( ':checked' ) ) {
			_showElement( $imageLocation );
			_showElement( $imageSize );
			if ( $columns <= 1 ) {
				_showElement( $imageAlignment );
			}
		} else {
			_hideElement( $imageLocation );
			_hideElement( $imageSize );
			_hideElement( $imageAlignment );
		}
	});

	function _hideElement( $element ) {
		$element.hide();
	}

	function _showElement( $element ) {
		$element.show();
	}

})( document, jQuery );
