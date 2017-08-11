<?php

/**
 * Get the banner image ID.
 *
 * First check immediate setting, then archive setting (if applicable), then fallback to default image.
 *
 * @return int|false
 */
function mai_get_banner_id() {

	// Start of without an image
	$image_id = false;

	// Static front page
	if ( is_front_page() && $front_page_id = get_option( 'page_on_front' ) ) {
		$image_id = get_post_meta( $front_page_id, 'banner_id', true );
		// If no image and featured images as banner is enabled
		if ( ! $image_id && mai_is_banner_featured_image_enabled() ) {
			$image_id = get_post_thumbnail_id( $front_page_id );
		}
	}

	// Static blog page
	elseif ( is_home() && $posts_page_id = get_option( 'page_for_posts' ) ) {
		$image_id = get_post_meta( $posts_page_id, 'banner_id', true );
		// If no image and featured images as banner is enabled
		if ( ! $image_id && mai_is_banner_featured_image_enabled() ) {
			$image_id = get_post_thumbnail_id( $posts_page_id );
		}
	}
	// Single page/post/cpt, but not static front page or static home page
	elseif ( is_singular() ) {
		$image_id = get_post_meta( get_the_ID(), 'banner_id', true );
		// If no image and featured images as banner is enabled
		if ( ! $image_id && mai_is_banner_featured_image_enabled() ) {
			$image_id = get_post_thumbnail_id( get_the_ID() );
		}
		// If no image and CPT has genesis archive support
		if ( ! $image_id ) {
			// Get the post's post_type
			$post_type = get_post_type();
			// Posts
			if ( 'post' == $post_type && $posts_page_id = get_option( 'page_for_posts' ) ) {
				$image_id = get_post_meta( $posts_page_id, 'banner_id', true );
			}
			// CPTs
			elseif ( genesis_has_post_type_archive_support( $post_type ) ) {
				$image_id = genesis_get_cpt_option( 'banner_id' );
			}
			// Products
			elseif ( class_exists( 'WooCommerce' ) && is_product() && $shop_page_id = get_option( 'woocommerce_shop_page_id' ) ) {
				$image_id = get_post_meta( $shop_page_id, 'banner_id', true );
			}
		}
	}

	// Term archive
	elseif ( is_category() || is_tag() || is_tax() ) {
		// If WooCommerce product category
		if ( class_exists( 'WooCommerce' ) && is_tax( array( 'product_cat' ) ) ) {
			// Woo uses it's own image field/key
			$image_id = get_term_meta( get_queried_object()->term_id, 'thumbnail_id', true );
		} else {
			$image_id = get_term_meta( get_queried_object()->term_id, 'banner_id', true );
		}
		// If no image
		if ( ! $image_id ) {
			// Get hierarchical taxonomy term meta
			$image_id = mai_get_term_meta_value_in_hierarchy( get_queried_object(), 'banner_id', false );
			// If still no image
			if ( ! $image_id ) {
				// Check the archive settings, so we can fall back to the taxo's post_type setting
				$image_id = mai_get_archive_setting( 'banner_id', false );
			}
		}
	}

	// CPT archive
	elseif ( is_post_type_archive() && genesis_has_post_type_archive_support() ) {
		$image_id = genesis_get_cpt_option( 'banner_id' );
	}

	// Author archive
	elseif ( is_author() ) {
		$image_id = get_the_author_meta( 'banner_id', get_query_var( 'author' ) );
	}

	// WooCommerce shop page
	// elseif ( class_exists( 'WooCommerce' ) && is_shop() && ( $shop_page_id = get_option( 'woocommerce_shop_page_id' ) ) ) {
	// 	$image_id = get_post_meta( $shop_page_id, 'banner_id', true );
	// }

	/**
	 * If no banner, but we have a default,
	 * use the default banner image.
	 */
	if ( ! $image_id ) {
		if ( $default_id = genesis_get_option( 'banner_id' ) ) {
			$image_id = absint( $default_id );
		}
	}

	// Filter so devs can force a specific image ID
	$image_id = apply_filters( 'mai_banner_image_id', $image_id );

	return $image_id;
}

/**
 * Helper function to get a grid of content.
 * This is a php version of the [grid] shortcode.
 *
 * @param   array  $args  The [grid] shortcode atts.
 *
 * @return  string|HTML
 */
function mai_get_grid( $args ) {
	return Mai_Shortcodes()->get_grid( $args );
}

/**
 * Get a section.
 *
 * @param  array  $content  The section content (required).
 * @param  array  $args     The section args (optional).
 *
 * @return string|HTML
 */
function mai_get_section( $content, $args = array() ) {
	return Mai_Shortcodes()->get_section( $args, $content );
}

/**
 * Helper function to get a read more link for a post or term
 *
 * @param  int|WP_Post|WP_term?  $object
 * @param  string                $text
 *
 * @return HTML string for the link
 */
function mai_get_read_more_link( $object_or_id = '', $text = '' ) {

	$link = $url = $screen_reader_html = $screen_reader_text = '';

	$text           = $text ? sanitize_text_field($text) : __( 'Read More', 'mai-pro-engine' );
	$more_link_text = sanitize_text_field( apply_filters( 'mai_more_link_text', $text ) );

	$object = mai_get_read_more_object( $object_or_id );

	if ( $object ) {
		if ( isset( $object['post'] ) ) {
			$url                = get_permalink( $object['post'] );
			$screen_reader_text = $object['post']->post_title;
		} elseif ( isset( $object['term'] ) ) {
			$url                = get_term_link( $object['term'] );
			$screen_reader_text = $object['term']->name;
		}
	}

	// Build the screen reader text html
	if ( $screen_reader_text ) {
		$screen_reader_html = sprintf( '<span class="screen-reader-text">%s</span>', esc_html( $screen_reader_text ) );
	}

	// Get image location
	$image_location = mai_get_archive_setting( 'image_location', true, genesis_get_option( 'image_location' ) );

	// If background image
	if ( $url ) {
		$link = sprintf( '<a class="more-link" href="%s">%s%s</a>', $url, $screen_reader_html, $more_link_text );
	}

	// Bail if no link
	if ( empty( $link ) ) {
		return;
	}

	return sprintf( '<p class="more-link-wrap">%s</p>', $link );
}

/**
 * Get the object for a read more link.
 *
 * @param   int|object  $object_or_id  The object or ID, for now only post or term.
 *
 * @return  associated array, key is object type and value is the object
 */
function mai_get_read_more_object( $object_or_id ) {
	$type = array();
	// Bail if no object_or_id
	if ( ! $object_or_id ) {
		return $type;
	}
	// If we have a post
	if ( $object = get_post($object_or_id) ) {
		$type['post'] = $object;
	}
	// No post, try a term
	elseif ( $term = get_term( $object_or_id ) && ! is_wp_error( $term ) ) {
		$type['term'] = $object;
	}
	return $type;
}

/**
 * Get a post's post_meta
 *
 * @param  int|object  $post  (Optional) the post to get the meta for.
 *
 * @return string|HTML The post meta
 */
function mai_get_the_posts_meta( $post = '' ) {

	if ( ! empty( $post ) ) {
		$post = get_post( $post );
	} else {
		global $post;
	}

	$post_meta = $shortcodes = '';

	$taxos = get_post_taxonomies($post);
	if ( $taxos ) {

		// Skip if Post Formats and Yoast prominent keyworks
		$taxos = array_diff( $taxos, array( 'post_format', 'yst_prominent_words' ) );

		$taxos = apply_filters( 'mai_post_meta_taxos', $taxos );

		foreach ( $taxos as $tax ) {
			$taxonomy = get_taxonomy($tax);
			$shortcodes .= '[post_terms taxonomy="' . $tax . '" before="' . $taxonomy->labels->singular_name . ': "]';
		}
		$post_meta = sprintf( '<p class="entry-meta">%s</p>', do_shortcode( $shortcodes ) );
	}
	return $post_meta;
}

/**
 * Check if viewing a content archive page.
 * This is any archive page that may inherit (custom) archive settings.
 *
 * @return  bool
 */
function mai_is_content_archive() {

	$is_archive = false;

	// Static blog page
	if ( is_home() && ( $posts_page_id = get_option( 'page_for_posts' ) ) ) {
		$is_archive = true;
	}
	// Term archive
	elseif ( is_category() || is_tag() || is_tax() ) {
		$is_archive = true;
	}
	// CPT archive
	elseif ( is_post_type_archive() && genesis_has_post_type_archive_support() ) {
		$is_archive = true;
	}
	// Author archive
	elseif ( is_author() ) {
		$is_archive = true;
	}
	// WooCommerce shop page
	elseif ( class_exists( 'WooCommerce' ) && is_shop() && ( $shop_id = get_option( 'woocommerce_shop_page_id' ) ) ) {
		$is_archive = true;
	}
	// Search results
	elseif ( is_search() ) {
		$is_archive = true;
	}

	return $is_archive;
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
 * Add background color HTML attributes to an element.
 *
 * @param   array   $attributes  The existing HTML attributes.
 * @param   string  $image_id    The image ID.
 * @param   string  $image_size  The registered image size.
 *
 * @return  array   The modified attributes.
 */
function mai_add_background_image_attributes( $attributes, $image_id, $image_size ) {
	// Get all registered image sizes
	global $_wp_additional_image_sizes;

	// Get the image
	$image = $image_id ? wp_get_attachment_image_src( $image_id, $image_size, true ) : false;

	// If we have an image, add it as inline style
	if ( $image ) {

		// Make sure style attribute is set
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
	return $attributes;
}

/**
 * Helper function to get processed (cleaned up) HTML content.
 *
 * @param   string|HTML  $content  The content to process.
 *
 * @return  string|HTML  The processed content.
 */
function mai_get_processed_content( $content ) {
	return Mai_Shortcodes()->get_processed_content( $content );
}

/**
 * Helper function for getting the script/style `.min` suffix for minified files.
 *
 * @return string
 */
function mai_get_suffix() {
	$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
	return $debug ? '' : '.min';
}

/**
 * Sanitize a string or array of classes.
 *
 * @param   string|array  $classes   The classes to sanitize.
 *
 * @return  string  Space-separated, sanitized classes.
 */
function mai_sanitize_html_classes( $classes ) {
	if ( ! is_array( $classes ) ) {
		$classes = explode( ' ', $classes );
	}
	return implode( ' ', array_unique( array_map( 'sanitize_html_class', array_map( 'trim', $classes ) ) ) );
}

/**
 * Sanitize a string or array of keys.
 *
 * @param   string|array  $keys   The keys to sanitize.
 *
 * @return  array  Array of sanitized keys.
 */
function mai_sanitize_keys( $keys ) {
	if ( ! is_array( $keys ) ) {
		$keys = explode( ',', $keys );
	}
	return array_map( 'sanitize_key', array_map( 'trim', array_filter($keys) ) );
}

/**
 * Kind of a gross function to run do_action in output buffering
 * and return the content of that hook.
 *
 * @param   string  $hook  The hook name to run.
 *
 * @return  string|HTML
 */
function mai_get_do_action( $hook ) {
    // Start buffer
	ob_start();
    // Add new hook
	do_action( $hook );
    // End buffer
	$content = ob_get_clean();
    // Return the content, filtered by of hook name with underscore prepended
	return apply_filters( '_' . $hook, $content );
}

/**
 * Check if a string starts with another string.
 *
 * @link    http://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
 *
 * @param   string  $haystack  The string to check against.
 * @param   string  $needle    The string to check if starts with.
 *
 * @return  bool
 */
function mai_starts_with( $haystack, $needle ) {
	$length = strlen( $needle );
	return ( $needle === substr( $haystack, 0, $length ) );
}

/**
 * Check if a string ends with another string.
 *
 * @link    http://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
 *
 * @param   string  $haystack  The string to check against.
 * @param   string  $needle    The string to check if starts with.
 *
 * @return  bool
 */
function mai_ends_with( $haystack, $needle ) {
	$length = strlen($needle);
	if ( 0 == $length ) {
		return true;
	}
	return ( $needle === substr( $haystack, -$length ) );
}
