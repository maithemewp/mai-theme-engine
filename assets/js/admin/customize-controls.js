jQuery( document ).ready( function( $ ) {
	/**
	 * Slider multicheck.
	 */

	// Loop through em.
	$( '.customize-control-multicheck' ).each( function() {

		// On change of a checkbox field.
		$(this).find( 'input[type="checkbox"]' ).on( 'change', function()  {
			// Get the values in comma separated string.
			checkboxValues = $(this).parents( '.customize-control' ).find( 'input[type="checkbox"]:checked' ).map( function() {
				return this.value;
			}).get().join( ',' );
			// Add the comma-separated values to the hidden input, for live preview.
			$(this).parents( '.customize-control' ).find( 'input[type="hidden"]' ).val( checkboxValues ).trigger( 'change' );
		});

	});

});
