<?php

/**
 * Filter the Global Settings Options.
 * Media breakpoints and form title have been changed.
 */
add_filter( 'fl_builder_register_settings_form', 'mai_fl_builder_register_settings_form', 10, 2 );
function mai_fl_builder_register_settings_form( $form, $id ) {
	if ( 'global' !== $id ) {
		return $form;
	}
	$form['tabs']['general']['sections']['rows']['fields']['row_width']['default'] = 'auto';
	return $form;
}

