<?php


/**
 * Add the archive featured image in the correct location.
 * No need to check if display image is checked, since that happens
 * in the genesis_option filters already.
 *
 * @return  void
 */
function mai_do_archive_image( $location ) {

	// Bail if no location
	if ( ! $location ) {
		return;
	}

	/**
	 * Add the images in the correct location
	 */

	// Before Entry
	if ( 'before_entry' === $location ) {
		add_action( 'genesis_entry_header', 'genesis_do_post_image', 2 );
	}
	// Before Title
	elseif ( 'before_title' === $location ) {
		add_action( 'genesis_entry_header', 'genesis_do_post_image', 8 );
	}
	// After Title
	elseif ( 'after_title' === $location ) {
		add_action( 'genesis_entry_header', 'genesis_do_post_image', 10 );
	}
	// Before Content
	elseif ( 'before_content' === $location ) {
		add_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
	}
	// Background Image
	elseif ( 'background' === $location ) {
		// Add the entry image as a background image
		add_action( 'genesis_before_entry', 'mai_do_entry_image_background' );
		// Add the background image link
		add_action( 'genesis_entry_footer', 'mai_do_bg_image_link', 30 );
		// Remove bg iamge link function so additional loops are not affected
		add_action( 'mai_after_content_archive', function() {
			remove_action( 'genesis_entry_footer', 'mai_do_bg_image_link', 30 );
		});
	}

	// Add the location as a class to the image link
	add_filter( 'genesis_attr_entry-image-link', function( $attributes ) use ( $location ) {
		// Replace underscore with hyphen
		$location = str_replace( '_', '-', $location );
		// Add the class
		$attributes['class'] .= sprintf( ' entry-image-%s', $location );
		return $attributes;
	});

}


/**
 * Add the entry image as a background image.
 * Change the markup to wrap the entire entry in an href link.
 * Remove the title link.
 *
 * @return void.
 */
function mai_do_entry_image_background() {

	// Get the image ID
	$image_id = get_post_thumbnail_id();

	// Get image size
	$image_size = mai_get_archive_setting( 'image_size', true, genesis_get_option( 'image_size' ) );

	// Anonomous attributes function
	$entry_attributes = function( $attributes ) use ( $image_id, $image_size ) {

		// Make element a link whether we have an image or not
		$attributes = mai_add_background_image_attributes( $attributes, $image_id, $image_size );
		$attributes['href'] = get_permalink();

		// If we have an image
		if ( $image_id ) {
			// Add classes and href link. TODO: Overlay options, or no overlay if no content?
			$attributes['class'] .= ' overlay overlay-dark light-content';
		}

		// Add has-bg-link class for CSS
		$attributes['class'] .= ' has-bg-link';

		// Center the content even if we don't have an image
		$attributes['class'] .= ' center-xs middle-xs text-xs-center';

		return $attributes;
	};

	// Add entry attributes
	add_filter( 'genesis_attr_entry', $entry_attributes );

	// Remove the filters so any other loops aren't affected
	add_action( 'genesis_after_entry', function() use ( $entry_attributes ) {
		remove_filter( 'genesis_attr_entry', $entry_attributes );
	});

}

/**
 * Output the bg image link HTML. Must be used in the loop (posts/cpts only!).
 *
 * This doesn't have a parameter because it's hooked directly,
 * via add_action( 'genesis_entry_header', 'mai_do_bg_image_link', 1 );
 *
 * @return void.
 */
function mai_do_bg_image_link() {
	echo mai_get_bg_image_link();
}

/**
 * Get the bg image link HTML.
 *
 * @param  string $url (optional) The URL to use for the HTML.
 * @param  string $title (optional) The title to use for the HTML.
 *
 * @return string|HTML
 */
function mai_get_bg_image_link( $url = '', $title = '' ) {
	$url   = $url ? esc_url( $url ) : get_permalink();
	$title = $title ? esc_html( $title ) : get_the_title();
	return sprintf( '<a href="%s" class="bg-link"><span class="screen-reader-text" aria-hidden="true">%s</span></a>', $url, $title );
}

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

function mai_add_align_classes( $classes, $args ) {
	/**
	 * "align" takes precendence over "align_cols" and "align_text".
	 * "align" forces the text to align along with the cols.
	 */
	if ( isset( $args['align'] ) && ! empty( $args['align'] ) ) {
		$classes = mai_add_align_only_classes( $classes, $args['align'] );
	} else {
		// Align columns.
		if ( isset( $args['align_cols'] ) && ! empty( $args['align_cols'] ) ) {
			$classes = mai_add_align_cols_classes( $classes, $args['align_cols'] );
		}
		// Align columns.
		if ( isset( $args['align_text'] ) && ! empty( $args['align_text'] ) ) {
			$classes = mai_add_align_text_classes( $classes, $args['align_text'] );
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
function mai_add_align_only_classes( $classes, $alignment ) {
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
function mai_add_align_cols_classes( $classes, $alignment ) {
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
 * Add background color HTML attributes to an element.
 *
 * @param   array   $attributes    The existing HTML attributes.
 * @param   string  $image_id      The image ID.
 * @param   string  $image_size    The registered image size.
 * @param   bool    $aspect_ratio  Whether to add aspect ratio class and attributes.
 *
 * @return  array   The modified attributes.
 */
function mai_add_bg_image_attributes( $attributes, $image_id, $image_size, $aspect_ratio = true ) {

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
function mai_get_classes_by_columns( $columns ) {
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

function mai_get_bottom_class( $bottom ) {
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

function mai_get_overlay_classes( $overlay ) {
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
function mai_is_valid_gutter( $gutter ) {
	return in_array( $gutter, array( 5, 10, 20, 30, 40, 50 ) );
}

/**
 * If overlay is a valid type.
 */
function mai_is_valid_overlay( $overlay ) {
	$valid_overlay_values = array( 'gradient', 'light', 'dark' );
	return in_array( $overlay, $valid_overlay_values );
}
