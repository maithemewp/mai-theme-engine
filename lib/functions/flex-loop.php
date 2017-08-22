<?php

/**
 * Helper function to check if archive is a flex loop.
 * This doesn't check if viewing an actual archive, but this layout should not be an option if ! is_archive()
 *
 * @return  bool  Whether the layout is a grid archive
 */
function mai_is_flex_loop() {
	// Bail if not a content archive
	if ( ! mai_is_content_archive() ) {
		return false;
	}
	// Get columns
	$columns = mai_get_columns();
	// If we have more than 1 column or if we are using featured image as bg image, it's a flex loop
	if ( ( $columns > 1 ) || ( 'background' === mai_get_archive_setting( 'image_location', true, genesis_get_option( 'image_location' ) ) ) ) {
		return true;
	}
	// Not a flex loop
	return false;
}

/**
 * Helper function to get the column count, with Woo fallback and filter.
 *
 * @return  int  The number of columns
 */
function mai_get_columns() {
	// If Woo shop or Woo taxonomy then fallback to 3.
	// if ( class_exists( 'WooCommerce' ) && ( is_shop() || is_tax( get_object_taxonomies( 'product', 'names' ) ) ) ) {
	// 	$fallback = 3;
	// } else {
	// 	$fallback = genesis_get_option( 'columns' );
	// }
	// Get the columns with fallback.
	$columns = mai_get_archive_setting( 'columns', true, $fallback );
	return (int) apply_filters( 'mai_get_columns', $columns );
}

/**
 * Get flex entry classes by
 *
 * @param   string      $option  'layout', 'columns', or 'fraction'
 * @param   string|int  $value   layout name, number of columns, or fraction name
 *
 * @return  string               comma separated string of classes
 */
function mai_get_flex_entry_classes_by( $option, $value ) {
	$classes = '';
	if ( 'columns' == $option ) {
		$classes = mai_get_flex_entry_classes_by_columns( $value );
	} elseif ( 'fraction' == $option ) {
		$classes = mai_get_flex_entry_classes_by_franction( $value );
	}
	return $classes;
}

/**
 * Filter post_class to add flex classes by number of columns.
 *
 * @param   string  $columns  number of columns to get classes for
 *
 * @return  void    fires post_class filter which returns array of classes
 */
function mai_do_flex_entry_classes_by_columns( $columns ) {
	add_filter( 'post_class', function( $classes ) use ( $columns ) {
		$classes[] = mai_get_flex_entry_classes_by_columns( $columns );
		return $classes;
	});
}

/**
 * Get the classes needed for an entry from number of columns.
 *
 * @param  string  $columns  number of columns to get classes for.
 *
 * @return string  the classes
 */
function mai_get_flex_entry_classes_by_columns( $columns ) {
	switch ( (int)$columns ) {
		case 1:
			$classes = 'flex-entry col col-xs-12';
		break;
		case 2:
			$classes = 'flex-entry col col-xs-12 col-sm-6';
		break;
		case 3:
			$classes = 'flex-entry col col-xs-12 col-sm-6 col-md-4';
		break;
		case 4:
			$classes = 'flex-entry col col-xs-12 col-sm-6 col-md-3';
		break;
		case 6:
			$classes = 'flex-entry col col-xs-6 col-sm-4 col-md-2';
		break;
		default:
			$classes = 'flex-entry col col-xs-12 col-sm-6 col-md-4';
	}
	return $classes;
}

/**
 * Get the classes needed for an entry from fraction name.
 *
 * @param  string  $fraction  The fraction name.
 *
 * @return string  the classes
 */
function mai_get_flex_entry_classes_by_fraction( $fraction ) {
	switch ( $fraction ) {
		case 'col':
			$classes = 'flex-entry col col-xs-12 col-sm-6 col-md';
		break;
		case 'col-auto':
			$classes = 'flex-entry col col-xs-12 col-sm-auto';
		break;
		case 'one-twelfth':
			$classes = 'flex-entry col col-xs-3 col-sm-2 col-md-1';
		break;
		case 'one-sixth':
			$classes = 'flex-entry col col-xs-4 col-sm-2';
		break;
		case 'one-fourth':
			$classes = 'flex-entry col col-xs-12 col-sm-6 col-md-3';
		break;
		case 'one-third':
			$classes = 'flex-entry col col-xs-12 col-sm-6 col-md-4';
		break;
		case 'five-twelfths':
			$classes = 'flex-entry col col-xs-12 col-sm-5';
		break;
		case 'one-half':
			$classes = 'flex-entry col col-xs-12 col-sm-6';
		break;
		case 'seven-twelfths':
			$classes = 'flex-entry col col-xs-12 col-sm-7';
		break;
		case 'two-thirds':
			$classes = 'flex-entry col col-xs-12 col-sm-8';
		break;
		case 'three-fourths':
			$classes = 'flex-entry col col-xs-12 col-sm-9';
		break;
		case 'five-sixths':
			$classes = 'flex-entry col col-xs-12 col-sm-10';
		break;
		case 'eleven-twelfths':
			$classes = 'flex-entry col col-xs-12 col-sm-11';
		break;
		case 'one-whole':
			$classes = 'flex-entry col col-xs-12';
		break;
		default:
			$classes = 'flex-entry col col-xs-12 col-sm';
	}
	return $classes;
}
