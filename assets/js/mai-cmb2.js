( function ( document, $, undefined ) {
    'use strict';

	var $archiveMetabox		= $( '.mai-content-archive-metabox' ),
		$removeLoop			= $( '#remove_loop' ),
		$settingsEnabled	= $( '#enable_content_archive_settings' ),
		$settingWrap   		= $( '.mai-archive-setting-wrap' ),
		$settingsWrap   	= $( '.mai-archive-settings-wrap' );

	// If we have an archive metabox
	if ( $archiveMetabox.length > 0 ) {

		// If removing the loop, hide all other settings
		if ( $removeLoop.is( ':checked' ) ) {
			_hideElement( $settingWrap );
			_hideElement( $settingsWrap );
		}
		// On change of the 'hide entries' setting
		$removeLoop.change( function() {
			// If checking the box
			if ( $(this).is( ':checked' ) ) {
				// Hide all settings
				_hideElement( $settingWrap );
				_hideElement( $settingsWrap );
			}
			// Unchecking
			else {
				// Show the main archive setting
				_showElement( $settingWrap );
				// If archive settings are enabled
				if ( $settingsEnabled.is( ':checked' ) ) {
					// Show the archive settings
					_showElement( $settingsWrap );
				}
			}
		});

		// If archive settings are not enabled, hide the settings
		if ( ! $settingsEnabled.is( ':checked' )  ) {
			_hideElement( $settingsWrap );
		}
		// On change of 'enable archive settings'
		$settingsEnabled.change( function() {
			// If checking the box
			if ( $(this).is( ':checked' ) ) {
				// Show the settings
				_showElement( $settingsWrap );
			}
			// Unchecking
			else {
				// Hide the settings
				_hideElement( $settingsWrap );
			}
		});


		var $columns			= $( '#columns' ),
			$includeImage		= $( '#content_archive_thumbnail' ),
			$includeImage		= $( '#content_archive_thumbnail' ),
			$imageLocation		= $( '.cmb2-id-image-location' ),
			$imageSize			= $( '.cmb2-id-image-size' ),
			$imageAlignment		= $( '.cmb2-id-image-alignment' ),
			$contentArchive		= $( '#content_archive' ),
			$contentLimit		= $( '.cmb2-id-content-archive-limit' );

		/**
		 * Maybe the image alignment field.
		 */

		// If more than 1 column
		if ( $columns.val() > 1 ) {
			// Hide image alignment field
			_hideElement( $imageAlignment );
		}
		// On change of column count field
		$columns.change(function() {
			// If more than 1 column
			if ( $(this).val() > 1 ) {
				// Hide image alignment field
				_hideElement( $imageAlignment );
			}
			// 1/none
			else {
				// If including an image
				if ( $includeImage.is( ':checked' ) ) {
					// Show the element
					_showElement( $imageAlignment );
				}
			}
		});

		/**
		 * Hide the image location, image size, and image alignment fields
		 * if include the featured image checkbox is unchecked.
		 */

		// If not including featured image
		if ( ! $includeImage.is( ':checked' ) ) {
			// Hide all the image settings
			_hideElement( $imageLocation );
			_hideElement( $imageSize );
			_hideElement( $imageAlignment );
		}
		// On change of include featured image setting
		$includeImage.change(function() {
			// If including image
			if ( $(this).is( ':checked' ) ) {
				// Show image location/size fields
				_showElement( $imageLocation );
				_showElement( $imageSize );
				// If columns 1 or less
				if ( $columns <= 1 ) {
					// Show image alignment field
					_showElement( $imageAlignment );
				}
			}
			// Not including featured image
			else {
				// Hide all the image settings
				_hideElement( $imageLocation );
				_hideElement( $imageSize );
				_hideElement( $imageAlignment );
			}
		});

		/**
		 * Maybe hide the content limit fields.
		 */

		// If content archive is 'none' or 'excerpts'
		if ( ( 'none' == $contentArchive.val() ) || ( 'excerpts' == $contentArchive.val() ) ) {
			// Hide content limit field
			_hideElement( $contentLimit );
		}
		// On change of content archive field
		$contentArchive.change(function() {
			// If content archive is 'none' or 'excerpts'
			if ( ( 'none' == $contentArchive.val() ) || ( 'excerpts' == $contentArchive.val() ) ) {
				// Hide content limit field
				_hideElement( $contentLimit );
			}
			else {
				// Show content limit field
				_showElement( $contentLimit );
			}
		});

	}

	// Helper function to hide an element
	function _hideElement( $element ) {
		$element.hide();
	}

	// Helper function to show an element
	function _showElement( $element ) {
		$element.show();
	}

})( document, jQuery );
