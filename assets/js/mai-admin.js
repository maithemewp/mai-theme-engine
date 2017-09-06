( function ( document, $, undefined ) {
	'use strict';

	var $archiveMetabox  = $( '.mai-content-archive-metabox' ),
		$removeLoop      = $( '#remove_loop' ),
		$settingsEnabled = $( '#enable_content_archive_settings' ),
		$settingWrap     = $( '.mai-archive-setting-wrap' ),
		$settingsWrap    = $( '.mai-archive-settings-wrap' );

	// If we have an archive metabox
	if ( $archiveMetabox.length > 0 ) {

		// If we have a setting (theme settings doesn't ) to enable archive settings and that setting is not checked, hide the archive settings
		if ( $settingsEnabled.length > 0 && ! $settingsEnabled.is( ':checked' )  ) {
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

		var $columns        = $( '#columns' ),
			$includeImage   = $( '#content_archive_thumbnail' ),
			$imageLocation  = $( '.cmb2-id-image-location' ),
			$imageSize      = $( '.cmb2-id-image-size' ),
			$imageAlignment = $( '.cmb2-id-image-alignment' ),
			$contentArchive = $( '#content_archive' ),
			$contentLimit   = $( '.cmb2-id-content-archive-limit' );

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

	// Get the Sections metabox
	var $sectionsRepeater = $( '#mai_sections_repeat' );

	if ( $sectionsRepeater.length ) {

		// Get the sections
		var $sections = $( '.mai-section' );

		// If we have any
		if ( $sections.length ) {
			// Loop through em
			$.each( $sections, function() {
				// Handle section settings toggle
				_sectionSettings( $(this) );
			});
		}

		/**
		 * Setup the settings events for new rows,
		 * on creation (typically by clicking Add Section),
		 */
		$sectionsRepeater.on( 'cmb2_add_row', function( event, row ) {
			var $section = $( row ).find( '.mai-section' );
			if ( $section.length ) {
				/**
				 * If you add a new section while the settings of the last section are open
				 * the new section starts visible.
				 */
				$section.removeClass( 'mai-settings-open' );
				// Handle section settings toggle
				_sectionSettings( $section );
			}
		});

	}

	// Helper function to toggle a section's settings
	function _sectionSettings( $section ) {

		var $button = $section.find( '.mai-section-settings-toggle' );

		_toggleAria( $button, 'aria-pressed' );
		_toggleAria( $button, 'aria-expanded' );

		$section.on( 'click', '.mai-section-settings-toggle', function(e) {

			e.preventDefault();

			$section.toggleClass( 'mai-settings-open' );

			_toggleAria( $button, 'aria-pressed' );
			_toggleAria( $button, 'aria-expanded' );

		});

		$section.on( 'click', '.mai-section-settings-close', function(e) {
			e.preventDefault();
			$section.removeClass( 'mai-settings-open' );
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

	/**
	 * Toggle aria attributes.
	 * @param  {button} $this   passed through
	 * @param  {aria-xx}        attribute aria attribute to toggle
	 * @return {bool}           from _ariaReturn
	 */
	function _toggleAria( $this, attribute ) {
		$this.attr( attribute, function( index, value ) {
			return 'false' === value;
		});
	}

})( document, jQuery );
