<?php

// TODO: REMOVE THIS IF ALL IS WORKING VIA /functions/content.php helper functions.

abstract class Mai_Content {

	/**
	 * Add classes to an existing string of classes.
	 *
	 * @param  string|array  $classes           The classes to add.
	 * @param  string        $existing_classes  The existing classes.
	 *
	 * @return string  HTML ready classes.
	 */
	public static function add_classes( $classes, $existing_classes = '' ) {
		if ( ! empty( $classes ) ) {
			$space   = ! empty( $existing_classes ) ? ' ' : '';
			$classes = is_array( $classes ) ? implode( ' ', $classes ) : $classes;
			return $existing_classes . $space . $classes;
		}
		return $existing_classes;
	}

	public static function add_align_classes( $classes, $args ) {
		/**
		 * "align" takes precendence over "align_cols" and "align_text".
		 * "align" forces the text to align along with the cols.
		 */
		if ( isset( $args['align'] ) && ! empty( $args['align'] ) ) {
			$classes = self::add_align_only_classes( $classes, $args['align'] );
		} else {
			// Align columns.
			if ( isset( $args['align_cols'] ) && ! empty( $args['align_cols'] ) ) {
				$classes = self::add_align_cols_classes( $classes, $args['align_cols'] );
			}
			// Align columns.
			if ( isset( $args['align_text'] ) && ! empty( $args['align_text'] ) ) {
				$classes = self::add_align_text_classes( $classes, $args['align_text'] );
			}
		}
		return $classes;
	}

	/**
	 * Add align classes.
	 *
	 * @param   string  $classes  The existing HTML classes.
	 * @param   array   $align    The array of alignment values.
	 *
	 * @return  $string  HTML ready classes.
	 */
	public static function add_align_only_classes( $classes, $alignment ) {
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
	 * Add align column classes.
	 *
	 * @param   string  $classes  The existing HTML classes.
	 * @param   array   $align    The array of alignment values.
	 *
	 * @return  $string  HTML ready classes.
	 */
	public static function add_align_cols_classes( $classes, $alignment ) {
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
	 * Add align text classes.
	 *
	 * @param   string  $classes  The existing HTML classes.
	 * @param   array   $align    The array of alignment values.
	 *
	 * @return  $string  HTML ready classes.
	 */
	public static function add_align_text_classes( $classes, $alignment ) {
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
	 * Add background color HTML attributes to an element.
	 *
	 * @param   array   $attributes    The existing HTML attributes.
	 * @param   string  $image_id      The image ID.
	 * @param   string  $image_size    The registered image size.
	 * @param   bool    $aspect_ratio  Whether to add aspect ratio class and attributes.
	 *
	 * @return  array   The modified attributes.
	 */
	public static function add_bg_image_attributes( $attributes, $image_id, $image_size, $aspect_ratio = true ) {

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
	 * Get the classes needed for an entry from number of columns.
	 *
	 * @param  string  $columns  number of columns to get classes for.
	 *
	 * @return string  the classes
	 */
	public static function get_classes_by_columns( $columns ) {
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

	public static function get_bottom_class( $bottom ) {
		if ( ! $bottom ) {
			return '';
		}
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

	public static function get_overlay_classes( $overlay ) {
		$classes = 'overlay';
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
	 * If gutter is a valid Flexington size.
	 */
	public static function is_valid_gutter( $gutter ) {
		return in_array( $gutter, array( 5, 10, 20, 30, 40, 50 ) );
	}

	/**
	 * If overlay is a valid type.
	 */
	public static function is_valid_overlay( $overlay ) {
		$valid_overlay_values = array( 'gradient', 'light', 'dark' );
		return in_array( $overlay, $valid_overlay_values );
	}

}
