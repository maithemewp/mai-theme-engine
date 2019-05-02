<?php

/**
 * Helper function to check if the banner area is enabled globally.
 *
 * @access  private
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
 * @access  private
 *
 * @param   string  $name Option name.
 *
 * @return  string  Option name as key of settings field.
 */
function _mai_customizer_get_field_name( $settings_field, $name ) {
	return sprintf( '%s[%s]', $settings_field, $name );
}

/**
 * Get the image sizes array for option values.
 *
 * @access  private
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
 * @access  private
 *
 * @param   array  $values  The values to sanitize.
 *
 * @return  array  The sanitize array.
 */
function _mai_customizer_multicheck_sanitize_key( $values ) {
	$multi_values = ! is_array( $values ) ? explode( ',', $values ) : $values;
	return ! empty( $multi_values ) ? array_map( 'sanitize_key', $multi_values ) : array();
}
