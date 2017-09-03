<?php

/**
 * Custom CSS for the customizer.
 *
 * @return  void.
 */
add_action( 'customize_controls_print_styles', 'mai_customizer_styles', 999 );
function mai_customizer_styles() {
	echo '<style type="text/css">
		.customize-section-description-container + .customize-control-break {
			// margin-top: -8px;
		}
		.customize-control-heading + .customize-control-checkbox,
		.customize-control-heading + .customize-control-checkbox + .customize-control-checkbox {
			margin-top: -12px !important;
		}
		.customize-control-heading ~ .customize-control-checkbox label {
			padding-top: 3px !important;
			padding-bottom: 3px !important;
		}
		.customize-control-break {
			min-height: 10px;
			background: #d5d5d5;
			color: #555d66;
			padding: 4px 12px;
			margin-top: 6px;
			margin-left: -12px;
			margin-right: -12px;
			border-left: 4px solid #555d66;
		}
		.customize-control-break .customize-control-title {
			margin-bottom: 0px;
		}
	</style>';
}

/**
 * Helper function to check if the banner area is enabled globally.
 *
 * @param   object  $wp_customize    The customizer object.
 * @param   string  $settings_field  The genesis setting to check. This should always be 'genesis-settings'.
 *
 * @return  bool.
 */
function _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $settings_field ) {
	return (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'enable_banner_area' ) )->value();
}

/**
 * Get field name attribute value.
 *
 * @param   string  $name Option name.
 *
 * @return  string  Option name as key of settings field.
 */
function _mai_customizer_get_field_name( $settings_field, $name ) {
	return sprintf( '%s[%s]', $settings_field, $name );
}

/**
 * Get the image sizes array for Kirki.
 *
 * @return  array.
 */
function _mai_customizer_get_image_sizes_config() {
	// Get our image size options
	$sizes   = genesis_get_image_sizes();
	$options = array();
	foreach ( $sizes as $index => $value ) {
		$options[$index] = sprintf( '%s (%s x %s)', $index, $value['width'], $value['height'] );
	}
	return $options;
}

/**
 * Helper function to sanitize all values in an array with 'sanitize_key' function.
 *
 * @param   array  $values  The values to sanitize.
 *
 * @return  array  The sanitize array.
 */
function _mai_customizer_multicheck_sanitize_key( $values ) {
	$multi_values = ! is_array( $values ) ? explode( ',', $values ) : $values;
	return ! empty( $multi_values ) ? array_map( 'sanitize_key', $multi_values ) : array();
}

/**
 * Helper function to sanitize a value to be either the integers 1 or 0.
 *
 * @param   mixed  $value  The value to sanitize.
 *
 * @return  int    The sanitized value. Either 1 or 0.
 */
function _mai_customizer_sanitize_one_zero( $value ) {
	return absint( filter_var( $value, FILTER_VALIDATE_BOOLEAN ) );
}
