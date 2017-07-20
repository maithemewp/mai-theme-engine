<?php

/**
 * Get an archive setting value with fallback.
 *
 * @param   string  $key            The field key to check.
 * @param   bool    $check_enabled  Whether to check if custom archive settings are enabled.
 * @param   mixed   $fallback       The value to fall back to if we don't get a value via setting.
 *
 * @return  mixed
 */
function mai_get_archive_setting( $key, $check_enabled = true, $fallback = false ) {

	// Allow child theme to short circuit this function.
	$pre = apply_filters( "mai_pre_get_archive_setting_{$key}", null );
	if ( null !== $pre ) {
		return $pre;
	}

	// Setup caches.
	static $settings_cache = array();

	// Check options cache.
	if ( isset( $settings_cache[ $key ] ) ) {
		// Option has been cached.
		return $settings_cache[ $key ];
	}

	// Set value
	$setting = mai_get_archive_setting_by_template( $key, $check_enabled, $fallback );

	// Option has not been previously been cached, so cache now.
	$settings_cache[ $key ] = is_array( $setting ) ? stripslashes_deep( $setting ) : stripslashes( wp_kses_decode_entities( $setting ) );

	return $settings_cache[ $key ];

}

/**
 * Get an archive setting value with fallback.
 *
 * @param   string  $key            The field key to check.
 * @param   bool    $check_enabled  Whether to check if custom archive settings are enabled.
 * @param   mixed   $fallback       The value to fall back to if we don't get a value via setting.
 *
 * @return  mixed
 */
function mai_get_archive_setting_by_template( $key, $check_enabled, $fallback = false ) {
	// Bail if not a content archive.
	if ( ! mai_is_content_archive() ) {
		return null;
	}
	$meta = null;
	// Static blog page.
	if ( is_home() && ( $posts_page_id = get_option( 'page_for_posts' ) ) ) {
		// If not checking enabled, or checking enabled and is enabled.
		if ( ! $check_enabled || ( $check_enabled && $enabled = get_post_meta( $posts_page_id, 'enable_content_archive_settings', true ) ) ) {
			$meta = get_post_meta( $posts_page_id, $key, true );
		}
	}
	// Term archive
	elseif ( is_category() || is_tag() || is_tax() ) {
		// If checking enabled and is enabled.
		if ( ! $check_enabled || ( $check_enabled && $enabled = get_term_meta( get_queried_object()->term_id, 'enable_content_archive_settings', true ) ) ) {
			$meta = get_term_meta( get_queried_object()->term_id, $key, true );
		}
		// If no meta
		if ( ! $meta ) {
			// Get hierarchical taxonomy term meta
			$meta = mai_get_hierarchichal_term_meta( get_queried_object(), $key, $check_enabled );
			// If no meta
			if ( ! $meta ) {
				// If post taxonomy
				if ( is_category() || is_tag() || is_tax( get_object_taxonomies( 'post', 'names' ) ) ) {
					// If we have a static front page
					if ( $posts_page_id = get_option( 'page_for_posts' ) ) {
						if ( ! $check_enabled || ( $check_enabled && $enabled = get_post_meta( $posts_page_id, 'enable_content_archive_settings', true ) ) ) {
							$meta = get_post_meta( $posts_page_id, $key, true );
						}
					}
				}
				// If Woo product taxonomy
				elseif ( class_exists('WooCommerce') && $product_taxos = get_object_taxonomies( 'product', 'names' ) && is_tax( $product_taxos ) ) {
					// If we have a Woo shop page
					if ( $shop_page_id = get_option( 'woocommerce_shop_page_id' ) ) {
						if ( ! $check_enabled || ( $check_enabled && $enabled = get_post_meta( $shop_page_id, 'enable_content_archive_settings', true ) ) ) {
							$meta = get_post_meta( $shop_page_id, $key, true );
						}
					}
				}
				else {
					// Custom taxonomy archive
					$tax = isset( get_queried_object()->taxonomy ) ? get_taxonomy( get_queried_object()->taxonomy ) : false;
					if ( $tax ) {
						/**
						 * If the taxonomy is only registered to 1 post type.
						 * Otherwise, how will we pick which post type archive to fall back to?
						 * If more than one, we'll just have to use the fallback later.
						 */
						if ( 1 == count( $tax->object_type ) ) {
							$post_type = reset( $tax->object_type );
							// If we have a post type and it supports genesis-cpt-archive-settings
							if ( $post_type && genesis_has_post_type_archive_support( $post_type ) ) {
								$meta = genesis_get_cpt_option( $key, $post_type );
							}
						}
					}
				}
			}
		}
	}
	// CPT archive
	elseif ( is_post_type_archive() && genesis_has_post_type_archive_support() ) {
		if ( ! $check_enabled || ( $check_enabled && $enabled = genesis_get_cpt_option( 'enable_content_archive_settings' ) ) ) {
			$meta = genesis_get_cpt_option( $key );
		}
	}
	// Author archive
	elseif ( is_author() ) {
		if ( ! $check_enabled || ( $check_enabled && $enabled = get_the_author_meta( 'enable_content_archive_settings', get_query_var( 'author' ) ) ) ) {
			$meta = get_the_author_meta( $key, get_query_var( 'author' ) );
		}
	}
	// WooCommerce shop page
	elseif ( class_exists( 'WooCommerce' ) && is_shop() && ( $shop_page_id = get_option( 'woocommerce_shop_page_id' ) ) ) {
		if ( ! $check_enabled || ( $check_enabled && $enabled = get_post_meta( $shop_page_id, 'enable_content_archive_settings', true ) ) ) {
			$meta = get_post_meta( $shop_page_id, $key, true );
		}
	}
	// If we have meta, return it
	if ( null !== $meta ) {
		return $meta;
	}
	// If we have a fallback, return it
	elseif ( $fallback ) {
		return $fallback;
	}
	// Return
	return null;
}

/**
 * Get an archive setting value without a fallback.
 * Original built to get the 'remove_loop' setting,
 * since that should be specific to each object (post/term/etc), and not have any fallbacks.
 *
 * @param  string  $key  The field key to check.
 *
 * @return mixed
 */
function mai_get_archive_setting_without_fallback( $key ) {
	// Static blog page
	if ( is_home() && ( $posts_page_id = get_option( 'page_for_posts' ) ) ) {
		$meta = get_post_meta( $posts_page_id, $key, true );
	}
	// Term archive
	elseif ( is_category() || is_tag() || is_tax() ) {
		$meta = get_term_meta( get_queried_object()->term_id, $key, true );
	}
	// CPT archive
	elseif ( is_post_type_archive() && genesis_has_post_type_archive_support() ) {
		$meta = genesis_get_cpt_option( $key );
	}
	// Author archive
	elseif ( is_author() ) {
		$meta = get_the_author_meta( $key, get_query_var( 'author' ) );
	}
	// WooCommerce shop page
	elseif ( class_exists( 'WooCommerce' ) && is_shop() && ( $shop_page_id = get_option( 'woocommerce_shop_page_id' ) ) ) {
		$meta = get_post_meta( $shop_page_id, $key, true );
	}
	// If we have meta, return it
	if ( isset( $meta ) ) {
		return $meta;
	}
	return null;
}

/**
 * Get the specified metadata value for the term or from
 * one of it's parent terms.
 *
 * @param  WP_Term  $term           Term object
 * @param  string   $key            The meta key to retrieve.
 * @param  bool     $check_enabled  Whether to check if custom archive settings are enabled.
 *
 * @return mixed|null
 */
function mai_get_hierarchichal_term_meta( WP_Term $term, $key, $check_enabled ) {
	if ( ! is_taxonomy_hierarchical( $term->taxonomy ) ) {
		return;
	}
	if ( ! mai_has_parent_term( $term ) ) {
		return;
	}
	return mai_get_term_meta_recursively( $term, $key, $check_enabled );
}

/**
 * Recursively get the term metadata by the specified meta key.
 *
 * This function walks up the term hierarchical tree, searching for
 * a valid metadata value for the given meta key.
 *
 * The recursive action stops when:
 *      1. The current term level has the metadata value.
 *      2. The current term level does not have a parent term.
 *
 * @param  WP_Term     $term           Term object
 * @param  string      $key            The meta key to retrieve.
 * @param  bool        $check_enabled  Whether to check if custom archive settings are enabled.
 * @param  mixed|null  $meta
 *
 * @return mixed|null
 */
function mai_get_term_meta_recursively( WP_Term $term, $key, $check_enabled, $meta = null ) {
	// Setup level.
	static $level = 1;
	/**
	 * If we're over the 4th level, we've gone too far.
	 * If we allow too many levels things could get slow.
	 */
	if ( $level > 4 ) {
		// Reset.
		$level = 1;
		// Return.
		return $meta;
	}
	// Increment the level.
	$level++;
	// We need to checking whether archive settings are enabled.
	if ( $check_enabled ) {
		// Enabled.
		if ( $enabled = get_term_meta( $term->term_id, 'enable_content_archive_settings', true ) ) {
			// Reset.
			$level = 1;
			// Return this level's meta.
			return get_term_meta( $term->term_id, $key, true );
		}
		// Not enabled.
		else {
			// Try the parent(s)
			$meta = mai_get_parent_term_meta_recursively( $term, $key, $check_enabled, $meta );
		}
	}
	// Don't check if archive settings are enabled.
	else {
		$meta = get_term_meta( $term->term_id, $key, true );
	}
	if ( ! $meta ) {
		// Try the parent(s)
		return mai_get_parent_term_meta_recursively( $term, $key, $check_enabled, $meta );
	}
	// Reset.
	$level = 1;
	// Return.
	return $meta;
}

/**
 * Continue the recursive term meta function, on a parent.
 *
 * @param  WP_Term     $term           Term object
 * @param  string      $key            The meta key to retrieve.
 * @param  bool        $check_enabled  Whether to check if custom archive settings are enabled.
 * @param  mixed|null  $meta
 *
 * @return mixed|null
 */
function mai_get_parent_term_meta_recursively( $term, $key, $check_enabled, $meta ) {
	// If no parent, use what we have.
	if ( ! mai_has_parent_term( $term ) ) {
		// Reset.
		$level = 1;
		// Return.
		return $meta;
	}
	// Get the parent term.
	$parent_term = get_term_by( 'id', $term->parent, $term->taxonomy );
	if ( false === $parent_term ) {
		// Reset.
		$level = 1;
		// Return.
		return $meta;
	}
	// Try again
	return mai_get_term_meta_recursively( $parent_term, $key, $check_enabled, $meta );
}

/**
 * Checks if the term has a parent.
 *
 * @param  WP_Term  $term Term object.
 *
 * @return bool
 */
function mai_has_parent_term( WP_Term $term ) {
	return ( $term->parent > 0 );
}

/**
 * Check if fixed header is enabled
 *
 * @return bool
 */
function mai_is_sticky_header_enabled() {
	return filter_var( get_theme_mod( 'enable_sticky_header', 0 ), FILTER_VALIDATE_BOOLEAN );
}

/**
 * Check if shrink header is enabled
 *
 * @return bool
 */
function mai_is_shrink_header_enabled() {
	return filter_var( get_theme_mod( 'enable_shrink_header', 0 ), FILTER_VALIDATE_BOOLEAN );
}

/**
 * Check if banner area is enabled globally
 *
 * @return bool
 */
function mai_is_banner_area_enabled_globally() {
	return filter_var( get_theme_mod( 'enable_banner_area', 1 ), FILTER_VALIDATE_BOOLEAN );
}

/**
 * Check if banner featured images is enabled
 *
 * @return bool
 */
function mai_is_banner_featured_image_enabled() {
	return filter_var( get_theme_mod( 'banner_featured_image', 0 ), FILTER_VALIDATE_BOOLEAN );
}

/**
 * Check if banner area is enabled.
 *
 * Force this in a template via:
 * add_filter( 'theme_mod_enable_banner_area', '__return_true' );
 *
 * First check global settings, then archive setting (if applicable), then immediate setting.
 *
 * @return bool
 */
function mai_is_banner_area_enabled() {

	$enabled = true;

	// If not enabled at all
	if ( ! mai_is_banner_area_enabled_globally() ) {
		$enabled = false;
	} else {

		/**
		 * If disabled via theme settings.
		 */

		if ( is_singular() ) {
			// Get 'disabled' content, typecasted as array because it may return empty string if none
			$disable_post_types = (array) genesis_get_option( 'banner_disable_post_types' );
			if ( in_array( get_post_type(), $disable_post_types ) ) {
				$enabled = false;
			}
		} elseif ( is_category() || is_tag() || is_tax() ) {
			// Get 'disabled' content, typecasted as array because it may return empty string if none
			$disable_taxonomies = (array) genesis_get_option( 'banner_disable_taxonomies' );
			if ( in_array( get_queried_object()->taxonomy, $disable_taxonomies ) ) {
				$enabled = false;
			}
		}

		/**
		 * If still enabled,
		 * check on the single object level.
		 *
		 * These conditionals were mostly adopted from mai_get_archive_setting() function.
		 */
		if ( $enabled ) {

			$hidden = false;

			// If single post/page/cpt
			if ( is_singular() ) {
				$hidden = get_post_meta( get_the_ID(), 'hide_banner', true );
			}
			// If content archive (the only other place we'd have this setting)
			elseif ( mai_is_content_archive() ) {
				$hidden = mai_get_archive_setting( 'hide_banner', false );
			}

			// If hidden, disable banner
			if ( $hidden ) {
				$enabled = false;
			}

		}

	}
	return $enabled;
}

/**
 * Check if auto display of featured image is enabled
 *
 * @return bool
 */
function mai_is_display_featured_image_enabled() {
	return filter_var( get_theme_mod( 'enable_singular_image', 1 ), FILTER_VALIDATE_BOOLEAN );
}

/**
 * Check if side menu is enabled
 *
 * @return bool
 */
function mai_is_side_menu_enabled() {
	if ( 'side' != get_theme_mod( 'mobile_menu_style' ) ) {
		return false;
	}
	return true;
}

/**
 * Get the number of footer widgets
 *
 * @return int
 */
function mai_get_footer_widgets_count() {
	return get_theme_mod( 'footer_widget_count', 2 );
}

/**
 * Get default accent color for Customizer.
 * Abstracted here since at least two functions use it.
 *
 * @return string Hex color code for accent color.
 */
function mai_get_customizer_get_default_accent_color() {
	return '#067CCC';
}

/**
 * Check if a specific hex color is dark.
 *
 * @param   string  $hex_color  3 or 6 digit hex color, with or without the hash "#"
 *
 * @return  bool
 */
function mai_is_dark_color( $hex_color ) {
	$color = new Mai_Color( $hex_color );
	return $color->isDark();
}

/**
 * Sanitises a HEX value.
 * The way this works is by splitting the string in 6 substrings.
 * Each sub-string is individually sanitized, and the result is then returned.
 *
 * This function is part of the `Kirki_Color` class in the [Kirki](http://kirki.org) Toolkit.
 * @link    https://aristath.github.io/blog/php-sanitize-hex-color
 *
 * @param   string      The 3 or 6 digit hex value with or without a hash.
 * @param   boolean     Whether we want to include a hash (#) at the beginning or not.
 *
 * @return  string      The sanitized hex color.
 */
function mai_sanitize_hex_color( $color, $hash = true ) {

	// Remove any spaces and special characters before and after the string
	$color = trim( $color );

	// Bail if no color
	if ( empty( $color ) ) {
		return;
	}

	// Remove any trailing '#' symbols from the color value
	$color = str_replace( '#', '', $color );

	// If the string is 6 characters long then use it in pairs.
	if ( 3 == strlen( $color ) ) {
		$color = substr( $color, 0, 1 ) . substr( $color, 0, 1 ) . substr( $color, 1, 1 ) . substr( $color, 1, 1 ) . substr( $color, 2, 1 ) . substr( $color, 2, 1 );
	}

	$substr = array();
	for ( $i = 0; $i <= 5; $i++ ) {
		$default    = ( 0 == $i ) ? 'F' : ( $substr[$i-1] );
		$substr[$i] = substr( $color, $i, 1 );
		$substr[$i] = ( false === $substr[$i] || ! ctype_xdigit( $substr[$i] ) ) ? $default : $substr[$i];
	}
	$hex = implode( '', $substr );

	/**
	 * Bail if we somehow still have an empty color.
	 * We don't want to end up returning a hash-only string.
	 */
	if ( empty( $hex ) ) {
		return;
	}

	return ( ! $hash ) ? $hex : '#' . $hex;
}

/**
 * Generate a hex value that has appropriate contrast
 * against the inputted value.
 *
 * @since 1.0.0
 *
 * @return string Hex color code for contrasting color.
 */
function mai_get_content_shade_from_bg( $hex_color ) {
	$color = new Mai_Color( $hex_color );
	if ( $color->isLight() ) {
		return 'dark-content';
	}
	return 'light-content';
}
