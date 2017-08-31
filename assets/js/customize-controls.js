jQuery(window).load(function($) {

	var $ = jQuery;

	// Get all the multicheck fields.
	var $multiChecks = $( '.customize-control-multicheck' );

	// Bail if we don't have any.
	if ( ! $multiChecks.length ) {
		return;
	}

	// Loop through em.
	$.each( $multiChecks, function(){

		// On change of a checkbox field.
		$(this).find( 'input[type="checkbox"]' ).on( 'change', function() {
			// Get the values in comma separated string.
			checkboxValues = $(this).parents( '.customize-control' ).find( 'input[type="checkbox"]:checked' ).map( function() {
				return this.value;
			}).get().join( ',' );
			// Add the comma-separated values to the hidden input, for live preview.
			$(this).parents( '.customize-control' ).find( 'input[type="hidden"]' ).val( checkboxValues ).trigger( 'change' );
		});

	});

});
