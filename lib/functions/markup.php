<?php

/**
 * Add classes to an existing string of classes.
 *
 * @param  string|array  $new       The classes to add.
 * @param  string        $existing  The existing classes.
 *
 * @return string  HTML ready classes.
 */
function mai_add_classes( $new, $existing = '' ) {
	if ( ! empty( $new ) ) {
		$space = ! empty( $existing ) ? ' ' : '';
		$new   = is_array( $new ) ? implode( ' ', $new ) : $new;
		return $existing . $space . $new;
	}
	return $existing;
}

/**
 * Add align classes.
 *
 * @param   string  $classes  The existing HTML classes.
 * @param   array   $args     The array of alignment args. Either 'align', 'align_cols', and 'align_text'.
 *
 * @return  string  HTML ready classes.
 */
function mai_add_align_classes( $classes, $args, $direction = 'row' ) {
	/**
	 * "align" takes precendence over "align_cols" and "align_text".
	 * "align" forces the text to align along with the cols.
	 */
	if ( isset( $args['align'] ) && ! empty( $args['align'] ) ) {
		// $classes = mai_add_align_only_classes( $classes, $args['align'] );
		switch ( $direction ) {
			case 'row':
				$classes = mai_add_align_classes_row( $classes, $args['align'] );
				break;
			case 'column':
				$classes = mai_add_align_classes_column( $classes, $args['align'] );
				break;
		}
	} else {
		// Align columns.
		if ( isset( $args['align_cols'] ) && ! empty( $args['align_cols'] ) ) {
			switch ( $direction ) {
				case 'row':
					$classes = mai_add_align_classes_row( $classes, $args['align_cols'] );
					break;
				case 'column':
					$classes = mai_add_align_classes_column( $classes, $args['align_cols'] );
					break;
			}
		}
		// Align columns.
		if ( isset( $args['align_text'] ) && ! empty( $args['align_text'] ) ) {
			$classes = mai_add_align_text_classes( $classes, $args['align_text'] );
		}
	}
	return $classes;
}

/**
 * Add align classes if only 'align' param is used.
 * This is when the element is flex-direction row.
 *
 * @param   string  $classes    The existing HTML classes.
 * @param   array   $alignment  The array of alignment values.
 *
 * @return  string  HTML ready classes.
 */
function mai_add_align_classes_row( $classes, $alignment ) {
	// Left.
	if ( in_array( 'left', $alignment ) ) {
		$classes .= ' start-xs text-xs-left';
	}
	// Center.
	if ( in_array( 'center', $alignment ) ) {
		$classes .= ' center-xs text-xs-center';
	}
	// Right.
	if ( in_array( 'right', $alignment ) ) {
		$classes .= ' end-xs text-xs-right';
	}
	// Top.
	if ( in_array( 'top', $alignment ) ) {
		$classes .= ' top-xs';
	}
	// Middle.
	if ( in_array( 'middle', $alignment ) ) {
		$classes .= ' middle-xs';
	}
	// Bottom.
	if ( in_array( 'bottom', $alignment ) ) {
		$classes .= ' bottom-xs';
	}
	return $classes;
}

/**
 * Add align classes if only 'align' param is used.
 * This is when the element is flex-direction column.
 *
 * @param   string  $classes    The existing HTML classes.
 * @param   array   $alignment  The array of alignment values.
 *
 * @return  string  HTML ready classes.
 */
function mai_add_align_classes_column( $classes, $alignment ) {
	// Left.
	if ( in_array( 'left', $alignment ) ) {
		$classes .= ' top-xs text-xs-left';
	}
	// Center.
	if ( in_array( 'center', $alignment ) ) {
		$classes .= ' middle-xs text-xs-center';
	}
	// Right.
	if ( in_array( 'right', $alignment ) ) {
		$classes .= ' bottom-xs text-xs-right';
	}
	// Top.
	if ( in_array( 'top', $alignment ) ) {
		$classes .= ' start-xs';
	}
	// Middle.
	if ( in_array( 'middle', $alignment ) ) {
		$classes .= ' center-xs';
	}
	// Bottom.
	if ( in_array( 'bottom', $alignment ) ) {
		$classes .= ' end-xs';
	}
	return $classes;
}


/**
 * Add align column classes.
 *
 * @param   string  $classes    The existing HTML classes.
 * @param   array   $alignment  The array of alignment values.
 *
 * @return  string  HTML ready classes.
 */
function mai_add_align_cols_classes_row( $classes, $alignment ) {
	// Left.
	if ( in_array( 'left', $alignment ) ) {
		$classes .= ' start-xs';
	}
	// Center.
	if ( in_array( 'center', $alignment ) ) {
		$classes .= ' center-xs';
	}
	// Right.
	if ( in_array( 'right', $alignment ) ) {
		$classes .= ' end-xs';
	}
	// Top.
	if ( in_array( 'top', $alignment ) ) {
		$classes .= ' top-xs';
	}
	// Middle.
	if ( in_array( 'middle', $alignment ) ) {
		$classes .= ' middle-xs';
	}
	// Bottom.
	if ( in_array( 'bottom', $alignment ) ) {
		$classes .= ' bottom-xs';
	}
	return $classes;
}

/**
 * Add align column classes if col is flex-direction column.
 * These are reversed (left is top instead of start) since the direction is column not row.
 *
 * @param   string  $classes    The existing HTML classes.
 * @param   array   $alignment  The array of alignment values.
 *
 * @return  string  HTML ready classes.
 */
function mai_add_align_cols_classes_column( $classes, $alignment ) {
	// Left.
	if ( in_array( 'left', $alignment ) ) {
		$classes .= ' top-xs';
	}
	// Center.
	if ( in_array( 'center', $alignment ) ) {
		$classes .= ' middle-xs';
	}
	// Right.
	if ( in_array( 'right', $alignment ) ) {
		$classes .= ' bottom-xs';
	}
	// Top.
	if ( in_array( 'top', $alignment ) ) {
		$classes .= ' start-xs';
	}
	// Middle.
	if ( in_array( 'middle', $alignment ) ) {
		$classes .= ' center-xs';
	}
	// Bottom.
	if ( in_array( 'bottom', $alignment ) ) {
		$classes .= ' end-xs';
	}
	return $classes;
}

/**
 * Add align text classes.
 *
 * @param   string  $classes    The existing HTML classes.
 * @param   array   $alignment  The array of alignment values.
 *
 * @return  string  HTML ready classes.
 */
function mai_add_align_text_classes( $classes, $alignment ) {
	// Left.
	if ( in_array( 'left', $alignment ) ) {
		$classes .= ' start-xs';
	}
	// Center.
	if ( in_array( 'center', $alignment ) ) {
		$classes .= ' center-xs';
	}
	// Right.
	if ( in_array( 'right', $alignment ) ) {
		$classes .= ' end-xs';
	}
	return $classes;
}

/**
 * Add align text classes when flex-direction is column.
 *
 * @param   string  $classes    The existing HTML classes.
 * @param   array   $alignment  The array of alignment values.
 *
 * @return  string  HTML ready classes.
 */
function mai_add_align_text_classes_column( $classes, $alignment ) {
	// Top.
	if ( in_array( 'top', $alignment ) ) {
		$classes .= ' start-xs';
	}
	// Middle.
	if ( in_array( 'middle', $alignment ) ) {
		$classes .= ' center-xs';
	}
	// Bottom.
	if ( in_array( 'bottom', $alignment ) ) {
		$classes .= ' end-xs';
	}
	return $classes;
}

/**
 * Add text size classes.
 *
 * @param   string  $classes  The existing HTML classes.
 * @param   array   $size     The size value.
 *
 * @return  string  HTML ready classes.
 */
function mai_add_text_size_classes( $classes, $size ) {
	switch ( $size ) {
		case 'xs':
		case 'extra-small';
			$classes .= ' text-xs';
			break;
		case 'sm':
		case 'small';
			$classes .= ' text-sm';
			break;
		case 'md':
		case 'medium';
			$classes .= ' text-md';
			break;
		case 'lg':
		case 'large':
			$classes .= ' text-lg';
			break;
		case 'xl':
		case 'extra-large':
			$classes .= ' text-xl';
			break;
	}
	return $classes;
}

/**
 * Add overlay classes.
 *
 * @param   string  $classes  The existing HTML classes.
 * @param   array   $overlay  The overlay value.
 *
 * @return  string  HTML ready classes.
 */
function mai_add_overlay_classes( $classes, $overlay ) {
	$classes .= ' overlay';
	switch ( $overlay ) {
		case 'gradient':
			$classes .= ' overlay-gradient';
			break;
		case 'light':
			$classes .= ' overlay-light';
			break;
		case 'dark':
			$classes .= ' overlay-dark';
			break;
	}
	return $classes;
}

/**
 * Add height classes.
 *
 * @param   string  $classes  The existing HTML classes.
 * @param   array   $height   The height value.
 *
 * @return  string  HTML ready classes.
 */
function mai_add_height_classes( $classes, $height ) {
	switch ( $height ) {
		case 'auto';
			$classes .= ' height-auto';
			break;
		case 'xs':
		case 'extra-small';
			$classes .= ' height-xs';
			break;
		case 'sm':
		case 'small';
			$classes .= ' height-sm';
			break;
		case 'md':
		case 'medium':
			$classes .= ' height-md';
			break;
		case 'lg':
		case 'large':
			$classes .= ' height-lg';
			break;
		case 'xl':
		case 'extra-large':
			$classes .= ' height-xl';
			break;
	}
	return $classes;
}

/**
 * Add content_width classes.
 *
 * @param   string  $classes        The existing HTML classes.
 * @param   array   $content_width  The content_width value.
 *
 * @return  string  HTML ready classes.
 */
function mai_add_content_width_classes( $classes, $content_width ) {
	if ( ! empty( $content_width ) ) {
		switch ( $content_width ) {
			case 'auto':
				$classes .= ' width-auto';
				break;
			case 'xs':
			case 'extra-small':
				$classes .= ' width-xs';
				break;
			case 'sm':
			case 'small';
				$classes .= ' width-sm';
				break;
			case 'md':
			case 'medium':
				$classes .= ' width-md';
				break;
			case 'lg':
			case 'large':
				$classes .= ' width-lg';
				break;
			case 'xl':
			case 'extra-large':
				$classes .= ' width-xl';
				break;
			case 'full':
				$classes .= ' width-full';
				break;
		}
	} else {
		// Add width classes based on layout.
		switch ( genesis_site_layout() ) {
			case 'xs-content':
				$classes .= ' width-xs';
				break;
			case 'sm-content':
				$classes .= ' width-sm';
				break;
			case 'md-content':
				$classes .= ' width-md';
				break;
			case 'lg-content':
				$classes .= ' width-lg';
				break;
		}
	}
	return $classes;
}

/**
 * May add inline styles to the attributes for an element.
 *
 * @param   array   $attributes  The existing HTML attributes.
 * @param   string  $styles      The HTML ready inline styles intended for style="".
 *
 * @return  array   The modified attributes.
 */
function mai_add_inline_styles( $attributes, $styles ) {
	if ( ! empty( $styles ) ) {
		if ( isset( $attributes['style'] ) && ! empty( $attributes['style'] ) ) {
			$attributes['style'] .= ' ' . $styles;
		} else {
			$attributes['style'] = $styles;
		}
	}
	return $attributes;
}

/**
 * Add background color HTML attributes to an element.
 *
 * @param   array   $attributes    The existing HTML attributes.
 * @param   string  $image_id      The image ID.
 * @param   string  $image_size    The registered image size.
 * @param   bool    $aspect_ratio  Whether to add aspect ratio class and attributes.
 *
 * @return  array   The modified attributes.
 */
function mai_add_background_image_attributes( $attributes, $image_id, $image_size, $aspect_ratio = true ) {

	// Get all registered image sizes.
	global $_wp_additional_image_sizes;

	// Get the image.
	$image = $image_id ? wp_get_attachment_image_src( $image_id, $image_size, true ) : false;

	// If we have an image, add it as inline style.
	if ( $image ) {

		// Make sure style attribute is set. TODO: IS THIS WHERE BG IMAGE IS GETTING ADDED TWICE?
		$attributes['style'] = isset( $attributes['style'] ) ? $attributes['style'] : '';

		// Add background image
		$inline_style         = sprintf( 'background-image: url(%s);', $image[0] );
		$attributes['style'] .= isset( $attributes['style'] ) ? $attributes['style'] . $inline_style : $inline_style;

		// Add image-bg class
		$attributes['class'] .= ' image-bg';

	} else {
		// Add image-bg class
		$attributes['class'] .= ' image-bg-none';
	}

	if ( $aspect_ratio ) {

		/**
		 * Add aspect ratio class, for JS to target.
		 * We do this even without an image to maintain equal height elements.
		 */
		$attributes['class'] .= ' aspect-ratio';

		// If image size is in the global (it should be)
		if ( isset( $_wp_additional_image_sizes[ $image_size ] ) ) {
			$registered_image = $_wp_additional_image_sizes[ $image_size ];
			$attributes['data-aspect-width']  = $registered_image['width'];
			$attributes['data-aspect-height'] = $registered_image['height'];
		}
		// Otherwise use the actual image dimensions
		elseif ( $image ) {
			$attributes['data-aspect-width']  = $image[1];
			$attributes['data-aspect-height'] = $image[2];
		}

	}

	return $attributes;
}

/**
 * Add background color HTML attributes to an element.
 *
 * @param   array   $attributes  The existing HTML attributes.
 * @param   string  $color       The hex color code.
 *
 * @return  array   The modified attributes.
 */
function mai_add_background_color_attributes( $attributes, $color ) {

	// Bail if no color to add
	if ( ! $color ) {
		return $attributes;
	}

	// Make sure style attribute is set
	$attributes['style'] = isset( $attributes['style'] ) ? $attributes['style'] : '';

	// Add background color
	$inline_style        = sprintf( 'background-color: %s;', $color );
	$attributes['style'] .= isset( $attributes['style'] ) ? $attributes['style'] . $inline_style : $inline_style;

	return $attributes;
}

/**
 * Helper function to get the column count, with Woo fallback and filter.
 *
 * @return  int  The number of columns
 */
function mai_get_columns() {
	// Get the columns with fallback.
	$columns = mai_get_archive_setting( 'columns', true, genesis_get_option( 'columns' ) );
	return (int) apply_filters( 'mai_get_columns', $columns );
}

/**
 *
 * Returns either string of HTML ready classes, or array for used on post_class filters.
 *
 * @return  string|array
 */
function mai_get_col_classes_by_breaks( $breaks, $size, $return = 'string' ) {
	$string  = '';
	$array   = array();
	$breaks  = mai_col_parse_breaks( $breaks, $size );
	foreach( $breaks as $break => $cols ) {
		if ( ! empty( $cols ) ) {
			$value = $cols;
		} else {
			$value = $size;
		}
		// Build the class name.
		$class   = mai_get_col_class( $break, $value );
		// Add to string.
		$string  = mai_add_classes( $class, $string );
		// Add to array.
		$array[] = $class;
	}
	if ( 'string' === $return ) {
		return $string;
	}
	if ( 'array' === $return ) {
		return $array;
	}
	// This shouldn't happen.
	return null;
}

/**
 * Parse breakpoints and their col size.
 * Removes unused/unneeded breaks so breaks can be looped through and only apply what's necessary.
 *
 * Possible break values are 'col', 'auto', and 1 thru 12.
 * The values are the amount of cols in the 12 column grid to fill.
 * Example: To make 1/3 columns you use '4', cause 4 out of 12 is 1/3.
 *
 * $breaks = array(
 *     'xs' => '12',
 *     'sm' => '',
 *     'md' => '',
 *     'lg' => '',
 *     'xl' => '',
 * );
 *
 * $size = 'col', 'auto, or '1' through '12'.
 *
 * @return  array  Associative array of breaks and size values.
 */
function mai_col_parse_breaks( $breaks, $size ) {

	// Parse breaks.
	$breaks = shortcode_atts( array(
		'xs' => '12',
		'sm' => '',
		'md' => '',
		'lg' => '',
		'xl' => '',
	), (array) $breaks );

	$default_set = false;

	foreach ( $breaks as $break => $cols ) {
		if ( empty( $cols ) ) {
			if ( ! $default_set ) {
				$breaks[ $break ] = $size;
				$default_set      = true;
			} else {
				unset( $breaks[ $break ] );
			}
		} else {
			// Each time a break is used we need to add the default after.
			$default_set = false;
		}
	}
	return $breaks;
}

function mai_get_col_class( $break, $size ) {
	return sprintf( 'col-%s%s', $break, mai_get_col_suffix( $size ) );
}

function mai_get_col_suffix( $size ) {
	switch ( (string) $size ) {
		case 'col':
			$suffix = '';
			break;
		case 'auto':
			$suffix = '-auto';
			break;
		case '12':
			$suffix = '-12';
			break;
		case '11':
			$suffix = '-11';
			break;
		case '10':
			$suffix = '-10';
			break;
		case '9':
			$suffix = '-9';
			break;
		case '8':
			$suffix = '-8';
			break;
		case '7':
			$suffix = '-7';
			break;
		case '6':
			$suffix = '-6';
			break;
		case '5':
			$suffix = '-5';
			break;
		case '4':
			$suffix = '-4';
			break;
		case '3':
			$suffix = '-3';
			break;
		case '2':
			$suffix = '-2';
			break;
		case '1':
			$suffix = '-1';
			break;
		default:
			$suffix = '';
	}
	return $suffix;
}

/**
 *
 * TODO: Can we replace this with mai_get_col_classes_by_breaks() stuff?
 * THIS IS STILL IN archive.php - Let's get rid of it somehow!
 *
 * Get the classes needed for an entry from number of columns.
 *
 * @param  string  $columns  number of columns to get classes for.
 *
 * @return string  the classes
 */
function mai_get_flex_entry_classes_by_columns( $columns ) {
	switch ( (int) $columns ) {
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
 * TODO: Can we replace this with mai_get_col_classes_by_breaks() stuff?
 *
 * Get the classes needed for an entry from fraction name.
 *
 * @param  string  $fraction  The fraction name.
 *
 * @return string  the classes
 */
function mai_get_flex_entry_classes_by_fraction_og( $fraction ) {
	switch ( $fraction ) {
		case 'col':
			$classes = 'flex-entry col col-xs-12 col-sm';
		break;
		case 'col-auto':
			$classes = 'flex-entry col col-xs-12 col-sm-auto';
		break;
		case 'one-twelfth':
			$classes = 'flex-entry col col-xs-12 col-sm-1';
		break;
		case 'one-sixth':
			$classes = 'flex-entry col col-xs-4 col-sm-2';
		break;
		case 'one-fourth':
			$classes = 'flex-entry col col-xs-12 col-sm-3';
		break;
		case 'one-third':
			$classes = 'flex-entry col col-xs-12 col-sm-4';
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

/**
 * Get bottom class name from bottom value.
 *
 * @param   int     Bottom value.
 *
 * @return  string  The HTML class.
 */
function mai_get_bottom_class( $bottom ) {
	switch ( (int) $bottom ) {
		case 0:
			$class = 'bottom-xs-0';
		break;
		case 5:
			$class = 'bottom-xs-5';
		break;
		case 10:
			$class = 'bottom-xs-10';
		break;
		case 20:
			$class = 'bottom-xs-20';
		break;
		case 30:
			$class = 'bottom-xs-30';
		break;
		case 40:
			$class = 'bottom-xs-40';
		break;
		case 50:
			$class = 'bottom-xs-50';
		break;
		case 60:
			$class = 'bottom-xs-60';
		break;
		default:
			$class = '';
	}
	return $class;
}

/**
 * If gutter is a valid Flexington size.
 */
function mai_is_valid_gutter( $gutter ) {
	return in_array( $gutter, array( 5, 10, 20, 30, 40, 50 ), true );
}

/**
 * If bottom is a valid Flexington size.
 */
function mai_is_valid_bottom( $bottom ) {
	return in_array( $bottom, array( 0, 5, 20, 30, 40, 50 ), true );
}

/**
 * If overlay is a valid type.
 */
function mai_is_valid_overlay( $overlay ) {
	return in_array( $overlay, array( 'gradient', 'light', 'dark' ) );
}

/**
 * If inner is a valid type.
 */
function mai_is_valid_inner( $inner ) {
	return in_array( $inner, array( 'light', 'dark' ) );
}
