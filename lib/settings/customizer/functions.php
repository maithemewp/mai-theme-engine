<?php

function _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $settings_field ) {
	return $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'enable_banner_area' ) )->value();
}

/**
 * Get field name attribute value.
 *
 * @param   string  $name Option name.
 * @return  string  Option name as key of settings field.
 */
function _mai_customizer_get_field_name( $settings_field, $name ) {
	return sprintf( '%s[%s]', $settings_field, $name );
}

/**
 * Get the image sizes array for Kirki.
 *
 * @return  array
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

function _mai_customizer_multicheck_sanitize_key( $values ) {
	$multi_values = ! is_array( $values ) ? explode( ',', $values ) : $values;
	return ! empty( $multi_values ) ? array_map( 'sanitize_key', $multi_values ) : array();
}

function _mai_customizer_sanitize_one_zero( $value ) {
	return absint( filter_var( $value, FILTER_VALIDATE_BOOLEAN ) );
}

function _mai_customizer_sanitize_bool( $value ) {
	return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
}
