jQuery(document).ready(function($) {

	/* === Multicheck Control === */
	$( '.customize-control-multicheck input[type="checkbox"]' ).on( 'change', function() {
		checkboxValues = $( this ).parents( '.customize-control' ).find( 'input[type="checkbox"]:checked' ).map(
			function() {
				return this.value;
			}
			).get().join( ',' );
		$( this ).parents( '.customize-control' ).find( 'input[type="hidden"]' ).val( checkboxValues ).trigger( 'change' );
	});

});
