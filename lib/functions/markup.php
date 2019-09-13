<?php

/**
 * Add classes to an existing string of classes.
 *
 * @since  1.3.0
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
 * Add row align classes.
 * This is used by grid and col.
 * We can't do text_align here because columns can't pass params to col.
 *
 * @since   1.3.0
 * @access  private
 *
 * @param   string  $classes    The existing HTML classes.
 * @param   array   $alignment  The array of alignment values.
 *
 * @return  string  HTML ready classes.
 */
function mai_add_row_align_classes( $classes, $args ) {
	// "align" takes precendence over "align_cols"
	if ( isset( $args['align'] ) && ! empty( $args['align'] ) ) {
		$classes = mai_add_alignment_classes( $classes, $args['align'], 'row' );
		$classes = mai_add_align_text_classes( $classes, $args['align'] );
	}
	else {
		// Align columns.
		if ( isset( $args['align_cols'] ) && ! empty( $args['align_cols'] ) ) {
			$classes = mai_add_alignment_classes( $classes, $args['align_cols'], 'row' );
		}
	}
	return $classes;
}

/**
 * Add entry align classes by flex-direction.
 *
 * @since   1.3.0
 * @access  private
 *
 * @param   string  $classes    The existing HTML classes.
 * @param   array   $alignment  The array of alignment values.
 *
 * @return  string  HTML ready classes.
 */
function mai_add_entry_align_classes( $classes, $args, $direction = 'row' ) {
	// If direction is column, and we don't have column class yet, add it.
	if ( 'column' === $direction && false === strpos( $classes, 'column' ) ) {
		$classes = mai_add_classes( 'column', $classes );
	}
	// "align" takes precendence over "align_cols" and "align_text".
	if ( isset( $args['align'] ) && ! empty( $args['align'] ) ) {
		$classes = mai_add_alignment_classes( $classes, $args['align'], $direction );
		$classes = mai_add_align_text_classes( $classes, $args['align'] );
	} else {
		// Align text.
		if ( isset( $args['align_text'] ) && ! empty( $args['align_text'] ) ) {
			$classes = mai_add_alignment_classes( $classes, $args['align_text'], $direction );
			$classes = mai_add_align_text_classes( $classes, $args['align_text'] );
		}
	}
	return $classes;
}

/**
 * Add alignment classes by flex-direction.
 *
 * @since   1.3.0
 * @access  private
 *
 * @param   string  $classes    The existing HTML classes.
 * @param   array   $alignment  The array of alignment values.
 *
 * @return  string  HTML ready classes.
 */
function mai_add_alignment_classes( $classes, $alignment, $direction = 'row' ) {
	switch ( $direction ) {
		case 'row':
			$classes = mai_add_alignment_classes_row( $classes, $alignment );
			break;
		case 'column':
			$classes = mai_add_alignment_classes_column( $classes, $alignment );
			break;
	}
	return $classes;
}

/**
 * Add alignment classes if col is flex-direction row.
 *
 * @since   1.3.0
 * @access  private
 *
 * @param   string  $classes    The existing HTML classes.
 * @param   array   $alignment  The array of alignment values.
 *
 * @return  string  HTML ready classes.
 */
function mai_add_alignment_classes_row( $classes, $alignment ) {
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
 * Add alignment classes if col is flex-direction column.
 * These are reversed (left is top instead of start) since the direction is column not row.
 *
 * @since   1.3.0
 * @access  private
 *
 * @param   string  $classes    The existing HTML classes.
 * @param   array   $alignment  The array of alignment values.
 *
 * @return  string  HTML ready classes.
 */
function mai_add_alignment_classes_column( $classes, $alignment ) {
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
 * Add text align classes.
 *
 * @since   1.3.0
 * @access  private
 *
 * @param   string  $classes  The existing HTML classes.
 * @param   array   $size     The size value.
 *
 * @return  string  HTML ready classes.
 */
function mai_add_align_text_classes( $classes, $alignment ) {
	// Left.
	if ( in_array( 'left', $alignment ) ) {
		$classes .= ' text-xs-left';
	}
	// Center.
	if ( in_array( 'center', $alignment ) ) {
		$classes .= ' text-xs-center';
	}
	// Right.
	if ( in_array( 'right', $alignment ) ) {
		$classes .= ' text-xs-right';
	}
	return $classes;
}

/**
 * Add text size classes.
 *
 * @since   1.0.0
 * @access  private
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
 * @since   1.0.0
 * @access  private
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
 * @since   1.0.0
 * @access  private
 *
 * @param   string  $classes  The existing HTML classes.
 * @param   array   $height   The height value.
 *
 * @return  string  HTML ready classes.
 */
function mai_add_content_height_classes( $classes, $height ) {
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
 * @access  private
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
 * @access  private
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
 * @access  private
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

	// Add image-bg class.
	$attributes['class'] .= ' image-bg';

	// Get the image.
	$image = $image_id ? wp_get_attachment_image_src( $image_id, $image_size, true ) : false;

	// If we have an image, add it as inline style.
	if ( $image ) {

		// Add background image.
		$styles     = sprintf( 'background-image: url(%s);', $image[0] );
		$attributes = mai_add_inline_styles( $attributes, $styles );

	} else {
		// Add image-bg-none class.
		$attributes['class'] .= ' image-bg-none';
	}

	/**
	 * Add aspect ratio class, for JS to target.
	 * We do this even without an image to maintain equal height elements.
	 */
	if ( $aspect_ratio ) {

		// If image size is in the global (it should be).
		if ( isset( $_wp_additional_image_sizes[ $image_size ] ) ) {
			$registered_image = $_wp_additional_image_sizes[ $image_size ];
			$width  = $registered_image['width'];
			$height = $registered_image['height'];
		}
		// Otherwise use the actual image dimensions.
		elseif ( $image ) {
			$width  = $image[1];
			$height = $image[2];
		}
		// Fallback.
		else {
			$width  = 4;
			$height = 3;
		}

		$attributes = mai_add_aspect_ratio_attributes( $attributes, $width, $height );
	}

	return $attributes;
}

/**
 * Add aspect ratio class and data attributes.
 *
 * @since   1.8.0
 * @access  private
 *
 * @param   array   $attributes    The existing HTML attributes.
 *
 * @return  array   The modified attributes.
 */
function mai_add_aspect_ratio_attributes( $attributes, $width, $height ) {
	$attributes['class'] = mai_add_classes( 'aspect-ratio', $attributes['class'] );
	$attributes['style'] = sprintf( '--aspect-ratio:%s/%s;', absint( $width ), absint( $height ) );
	return $attributes;
}

/**
 * Add background color HTML attributes to an element.
 *
 * @access  private
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
	// Add background color
	$styles     = sprintf( 'background-color: %s;', $color );
	$attributes = mai_add_inline_styles( $attributes, $styles );
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
 * Get the classes needed for an entry from number of columns.
 *
 * @since   1.3.8
 * @access  private
 *
 * @param   string  $columns  number of columns to get classes for.
 *
 * @return  string  the classes
 */
function mai_get_col_classes_by_columns( $columns ) {
	switch ( (int) $columns ) {
		case 1:
			$classes = 'col-xs-12';
		break;
		case 2:
			$classes = 'col-xs-12 col-sm-6';
		break;
		case 3:
			$classes = 'col-xs-12 col-sm-6 col-md-4';
		break;
		case 4:
			$classes = 'col-xs-12 col-sm-6 col-md-3';
		break;
		case 6:
			$classes = 'col-xs-6 col-sm-4 col-md-2';
		break;
		default:
			$classes = 'col-xs-12 col-sm-6 col-md-4';
	}
	return $classes;
}

/**
 * Get the columns class by array of breakpoints with their col values.
 * Returns either string of HTML ready classes, or array for used on post_class filters.
 *
 * @since   1.3.0
 * @access  private
 *
 * @param   array   $breaks  See mai_col_parse_breaks().
 * @param   string  $size    See mai_col_parse_breaks().
 * @param   string  $return  Whether to return an HTML ready string of classes, or an array of classes.
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
 * @since   1.3.0
 * @access  private
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

/**
 * Get the individual col class by break and size.
 *
 * @since   1.3.0
 * @access  private
 *
 * @param   string      $break  The breakpoint size 'xs', 'sm', 'md', 'lg', or 'xl'.
 * @param   string|int  $size   The span of columns out of 12, or 'col', or 'auto'.
 *
 * @return  string  The HTML class.
 */
function mai_get_col_class( $break, $size ) {
	return sprintf( 'col-%s%s', $break, mai_get_col_suffix( $size ) );
}

/**
 * Get col suffix by the size value.
 *
 * @since   1.3.0
 * @access  private
 *
 * @param   string|int  $size   The span of columns out of 12, or 'col', or 'auto'.
 *
 * @return  string  The suffix for the HTML class.
 */
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
 * Get gutter class name from gutter value.
 *
 * @since   1.3.8
 * @access  private
 *
 * @param   mixed   Gutter value.
 *
 * @return  string  The HTML class.
 */
function mai_get_gutter_class( $gutter ) {
	switch ( (string) $gutter ) {
		case '0':
		case 'none':
			$class = 'gutter-none';
		break;
		case '5':
		case 'xxxs':
			$class = 'gutter-xxxs';
		break;
		case '10':
		case 'xxs':
			$class = 'gutter-xxs';
		break;
		case 'xs':
			$class = 'gutter-xs';
		break;
		case '20':
		case 'sm':
			$class = 'gutter-sm';
		break;
		case '30':
		case 'md':
			$class = 'gutter-md';
		break;
		case '40':
		case 'lg':
			$class = 'gutter-lg';
		break;
		case '50':
		case 'xl':
			$class = 'gutter-xl';
		break;
		case '50':
		case 'xl':
			$class = 'gutter-xl';
		break;
		case '60':
		case 'xxl':
			$class = 'gutter-xxl';
		break;
			$class = '';
	}
	return $class;
}

/**
 * Get top class name from top value.
 *
 * @access  private
 *
 * @param   int     Top value.
 *
 * @return  string  The HTML class.
 */
function mai_get_top_class( $top ) {
	switch ( (string) $top ) {
		case '0':
		case 'none':
			$class = 'top-xs-none';
		break;
		case '5':
		case 'xxxs':
			$class = 'top-xs-xxxs';
		break;
		case '10':
		case 'xxs':
			$class = 'top-xs-xxs';
		break;
		case 'xs':
			$class = 'top-xs-xs';
		break;
		case '20':
		case 'sm':
			$class = 'top-xs-sm';
		break;
		case '30':
		case 'md':
			$class = 'top-xs-md';
		break;
		case '40':
		case 'lg':
			$class = 'top-xs-lg';
		break;
		case '50':
		case 'xl':
			$class = 'top-xs-xl';
		break;
		case '50':
		case 'xl':
			$class = 'top-xs-xl';
		break;
		case '60':
		case 'xxl':
			$class = 'top-xs-xxl';
		break;
			$class = '';
	}
	return $class;
}

/**
 * Get bottom class name from bottom value.
 *
 * @access  private
 *
 * @param   int     Bottom value.
 *
 * @return  string  The HTML class.
 */
function mai_get_bottom_class( $bottom ) {
	switch ( (string) $bottom ) {
		case '0':
		case 'none':
			$class = 'bottom-xs-none';
		break;
		case '5':
		case 'xxxs':
			$class = 'bottom-xs-xxxs';
		break;
		case '10':
		case 'xxs':
			$class = 'bottom-xs-xxs';
		break;
		case 'xs':
			$class = 'bottom-xs-xs';
		break;
		case '20':
		case 'sm':
			$class = 'bottom-xs-sm';
		break;
		case '30':
		case 'md':
			$class = 'bottom-xs-md';
		break;
		case '40':
		case 'lg':
			$class = 'bottom-xs-lg';
		break;
		case '50':
		case 'xl':
			$class = 'bottom-xs-xl';
		break;
		case '50':
		case 'xl':
			$class = 'bottom-xs-xl';
		break;
		case '60':
		case 'xxl':
			$class = 'bottom-xs-xxl';
		break;
			$class = '';
	}
	return $class;
}

/**
 * Get flex look classes.
 *
 * @access  private
 *
 * @param   array  $classes  The existing classes array.
 *
 * @return  void
 */
function mai_flex_loop_post_class( $classes ) {

	$classes[] = 'flex-entry';
	$classes[] = 'col';

	$breaks  = array();
	$columns = mai_get_columns();

	if ( $columns > 2 ) {
		$breaks['sm'] = 6;
	}
	if ( $columns > 3 ) {
		$breaks['md'] = 6;
	}

	$classes = array_merge( $classes, mai_get_col_classes_by_breaks( $breaks, mai_get_size_by_columns( $columns ), $return = 'array' ) );

	$location  = mai_get_archive_setting( 'image_location', true, genesis_get_option( 'image_location' ) );
	$alignment = mai_get_archive_setting( 'image_alignment', true, genesis_get_option( 'image_alignment' ) );

	// If not a background image.
	if ( 'background' !== $location ) {

		$location  = $location ? $location : 'none';
		$classes[] = 'has-image-' . $location;

		// Maybe add column class.
		if ( ! $alignment || ( 'aligncenter' === $alignment ) ) {
			$classes[] = 'column';
		}
	}

	return $classes;
}

/**
 * If gutter is a valid Flexington size.
 */
function mai_is_valid_gutter( $gutter ) {
	return in_array( (string) $gutter, array( 'none', 'xxxs', 'xxs', 'xs', 'sm', 'md', 'lg', 'xl', 'xxl', '0', '5', '10', '20', '30', '40', '50', '60' ) );
}

/**
 * If top is a valid Flexington size.
 */
function mai_is_valid_top( $top ) {
	return in_array( (string) $top, array( 'none', 'xxxs', 'xxs', 'xs', 'sm', 'md', 'lg', 'xl', 'xxl', '0', '5', '10', '20', '30', '40', '50', '60' ) );
}

/**
 * If bottom is a valid Flexington size.
 */
function mai_is_valid_bottom( $bottom ) {
	return in_array( (string) $bottom, array( 'none', 'xxxs', 'xxs', 'xs', 'sm', 'md', 'lg', 'xl', 'xxl', '0', '5', '10', '20', '30', '40', '50' ) );
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

/**
 * If image_align is a valid type.
 */
function mai_is_valid_image_align( $image_align ) {
	return in_array( $image_align, array( 'left', 'right', 'center' ) );
}
