<?php

/**
 * Get an archive setting directly from the archive.
 * Original built to get the 'remove_loop' setting,
 * since that should be specific to each object (post/term/etc), and not have any fallbacks.
 *
 * @param   string  $key  The field key to check.
 *
 * @return  mixed
 */
function mai_get_the_archive_setting( $key ) {

	// Setup caches.
	static $settings_cache = array();

	// Check options cache.
	if ( isset( $settings_cache[ $key ] ) ) {
		// Option has been cached.
		return $settings_cache[ $key ];
	}

	// Static blog page
	if ( is_home() && ( $posts_page_id = get_option( 'page_for_posts' ) ) ) {
		$setting = get_post_meta( $posts_page_id, $key, true );
	}
	// Term archive
	elseif ( is_category() || is_tag() || is_tax() ) {
		$setting = get_term_meta( get_queried_object()->term_id, $key, true );
	}
	// CPT archive
	elseif ( is_post_type_archive() ) {
		$setting = genesis_get_cpt_option( $key );
	}
	// Author archive
	elseif ( is_author() ) {
		$setting = get_the_author_meta( $key, get_query_var( 'author' ) );
	}
	// Nada
	else {
		$setting = null;
	}

	// Option has not been previously been cached, so cache now.
	$settings_cache[ $key ] = is_array( $setting ) ? stripslashes_deep( $setting ) : stripslashes( wp_kses_decode_entities( $setting ) );
	return $settings_cache[ $key ];
}

/**
 * Get an archive setting value with fallback.
 *
 * @param   string  $key                        The field key to check.
 * @param   bool    $check_for_archive_setting  Whether to check if custom archive settings are enabled.
 * @param   mixed   $fallback                   The value to fall back to if we don't get a value via setting.
 *
 * @return  mixed
 */
function mai_get_archive_setting( $key, $check_for_archive_setting = true, $fallback = false ) {

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
	$setting = mai_get_archive_setting_by_template( $key, $check_for_archive_setting, $fallback );

	// Option has not been previously been cached, so cache now.
	$settings_cache[ $key ] = is_array( $setting ) ? stripslashes_deep( $setting ) : stripslashes( wp_kses_decode_entities( $setting ) );

	return $settings_cache[ $key ];

}

/**
 * Get an archive setting value with fallback.
 *
 * @param   string  $key                        The field key to check.
 * @param   bool    $check_for_archive_setting  Whether to check if custom archive settings are enabled.
 * @param   mixed   $fallback                   The value to fall back to if we don't get a value via setting.
 *
 * @return  mixed
 */
function mai_get_archive_setting_by_template( $key, $check_for_archive_setting, $fallback = false ) {

	// Bail if not a content archive.
	if ( ! mai_is_content_archive() ) {
		return null;
	}

	$meta = null;

	// Blog.
	if ( is_home() ) {
		$meta = genesis_get_option( $key );
	}

	// Taxonomy archive.
	elseif ( is_category() || is_tag() || is_tax() ) {

		$queried_object = get_queried_object();

		/**
		 * Check if we have an object.
		 * We hit an issue when permlinks have /%category%/ in the base and a user
		 * 404's via top level URL like example.com/non-existent-slug.
		 * This returned true for is_category() and blew things up.
		 */
		if ( $queried_object ) {

			// If checking enabled and is enabled.
			if ( ! $check_for_archive_setting || ( $check_for_archive_setting && $enabled = get_term_meta( $queried_object->term_id, 'enable_content_archive_settings', true ) ) ) {
				$meta = get_term_meta( $queried_object->term_id, $key, true );
			}

			// If no meta
			if ( ! $meta ) {

				// Get hierarchical taxonomy term meta
				$meta = mai_get_term_meta_value_in_hierarchy( $queried_object, $key, $check_for_archive_setting );

				// If no meta
				if ( ! $meta ) {

					// If post or page taxonomy.
					if ( is_category() || is_tag() || is_tax( get_object_taxonomies( 'post', 'names' ) ) ) {
						$meta = genesis_get_option( $key );
					}

					// Custom taxonomy archive.
					else {

						$tax = isset( get_queried_object()->taxonomy ) ? get_taxonomy( get_queried_object()->taxonomy ) : false;
						if ( $tax ) {
							/**
							 * If the taxonomy is only registered to 1 post type.
							 * Otherwise, how will we pick which post type archive to fall back to?
							 * If more than one, we'll just have to use the fallback later.
							 */
							if ( 1 === count( (array) $tax->object_type ) ) {
								$post_type = reset( $tax->object_type );
								// If we have a post type and it supports genesis-cpt-archive-settings
								// if ( $post_type && genesis_has_post_type_archive_support( $post_type ) ) {
								if ( $post_type ) {
									if ( ! $check_for_archive_setting || ( $check_for_archive_setting && $enabled = genesis_get_cpt_option( 'enable_content_archive_settings', $post_type ) ) ) {
										$meta = genesis_get_cpt_option( $key, $post_type );
									}
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * CPT archive.
	 * This may be called too early to use get_post_type().
	 */
	elseif ( is_post_type_archive() && post_type_supports( get_query_var( 'post_type' ), 'mai-cpt-settings' ) ) {
		if ( ! $check_for_archive_setting || ( $check_for_archive_setting && $enabled = genesis_get_cpt_option( 'enable_content_archive_settings' ) ) ) {
			$meta = genesis_get_cpt_option( $key );
		}
	}

	// Author archive.
	elseif ( is_author() ) {
		if ( ! $check_for_archive_setting || ( $check_for_archive_setting && $enabled = get_the_author_meta( 'enable_content_archive_settings', get_query_var( 'author' ) ) ) ) {
			$meta = get_the_author_meta( $key, get_query_var( 'author' ) );
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
 * Get the metadata value for the term. This function walks up the term hierarchy,
 * searching each parent level to find a value for the given meta key. When it finds
 * one, it's returned.
 *
 * To perform an archive settings check, turn on the $check_for_archive_setting flag to
 * true.  This extra check does the following:
 *
 *   1.  Checks each level's  `enable_content_archive_settings` value.
 *   2.  If it's enabled, then that level's meta value is returned, regardless if
 *          it has a value or not.
 *
 * It works a level override, forcing that level to return it's value.
 *
 * @param  WP_Term  $term                       Term object
 * @param  string   $meta_key                   Meta key for the value you want to retrieve
 * @param  bool     $check_for_archive_setting  Flag to check if the `enable_content_archive_settings`
 *                                              is set.  When TRUE, check if this flag is set.
 *
 * @return mixed
 */
function mai_get_term_meta_value_in_hierarchy(  WP_Term $term, $meta_key, $check_for_archive_setting = false ) {
	$meta_keys = array( $meta_key );
	if ( $check_for_archive_setting ) {
		$meta_keys[] = 'enable_content_archive_settings';
	}
	$term_ancestors = mai_get_hierarchichal_term_metadata( $term, $meta_keys );
	if ( false === $term_ancestors ) {
		return;
	}
	// Loop through the objects until you find one that has a meta value.
	foreach( (array) $term_ancestors as $term_ancestor ) {
		// If checking content archive setting.
		if ( $check_for_archive_setting ) {
			// If setting is on.
			if ( $term_ancestor->metadata2 ) {
				return $term_ancestor->metadata1;
			}
		}
		// Not checking for content archive, and we have a value
		elseif ( $term_ancestor->metadata1 ) {
			return $term_ancestor->metadata1;
		}
	}
	// Whoops, didn't find one with a value for that meta key.
	return;
}

/**
 * Get the specified metadata value for the term or from
 * one of it's parent terms.
 *
 * @param  WP_Term       $term      Term object
 * @param  string|array  $meta_key  The meta key(s) to retrieve.
 *
 * @return mixed|null
 */
function mai_get_hierarchichal_term_metadata( WP_Term $term, $meta_key ) {
	if ( ! is_taxonomy_hierarchical( $term->taxonomy ) ) {
		return;
	}
	if ( ! mai_has_parent_term( $term ) ) {
		return;
	}
	return mai_get_terms_ancestory_tree( $term->term_id, $meta_key );
}

/**
 * Get an array of term ancestors for the given term id, meaning
 * the SQL query starts at the given term id and then walks up
 * the parent tree as it stores the columns.
 *
 * The result is an array of stdClass objects that have the following:
 *      term_id   => int
 *      parent_id => int
 *      metadata1 => value of that meta key's column
 *      ..
 *      metadataN => value of the meta key #N
 *
 * @param  integer  $term_id
 * @param  array    $meta_keys  Array of meta key(s) to retrieve.
 *
 * @return array|bool
 */
function mai_get_terms_ancestory_tree( $term_id, array $meta_keys ) {
	global $wpdb;
	// Build the SQL Query first.
	$sql_query = mai_build_terms_ancestory_tree_sql_query( $meta_keys );
	// Assemble the values, i.e. get them in the right order
	// to insert into the SQL query.
	$values = $meta_keys;
	array_unshift( $values, $term_id );
	// Prepare the values and then insert into the SQL query.
	// We are swapping out the %d/%f/%s placeholders with their value.
	$sql_query = $wpdb->prepare( $sql_query, $values );
	// Run the query to get records from the database.
	$records = $wpdb->get_results( $sql_query );
	// Check if we got records back from the database. If yes,
	// return the records.
	if ( $records && is_array( $records ) ) {
		return $records;
	}
	// Oh poo, we something when wrong.
	return false;
}

/**
 * Build the SQL Query string.
 *
 * @param array $meta_keys Array of meta key(s) to retrieve.
 *
 * @return string
 */
function mai_build_terms_ancestory_tree_sql_query( array $meta_keys  ) {
	global $wpdb;
	$number_of_meta_keys = count( $meta_keys );
	$sql_query = "SELECT t.term_id, @parent := t.parent AS parent_id";
	for( $suffix_number = 1; $suffix_number <= $number_of_meta_keys; $suffix_number++ ) {
		$sql_query .= sprintf( ', tm%1$d.meta_value AS metadata%1$d', $suffix_number );
	}
	$sql_query .= "\n" .
	"FROM (
		SELECT *
		FROM {$wpdb->term_taxonomy} AS tt
			ORDER BY
			CASE
				WHEN tt.term_id > tt.parent THEN tt.term_id
				ELSE tt.parent
			END DESC
	) AS t
	JOIN (
		SELECT @parent := %d
	) AS tmp";
	for( $suffix_number = 1; $suffix_number <= $number_of_meta_keys; $suffix_number++ ) {
		$sql_query .= "\n" . sprintf(
				'LEFT JOIN %1$s AS tm%2$d ON tm%2$d.term_id = @parent AND tm%2$d.meta_key = ',
				$wpdb->termmeta,
				$suffix_number
			);
		$sql_query .= '%s';
	}
	$sql_query .= "\n" . "WHERE t.term_id = @parent;";
	return $sql_query;
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
	return filter_var( genesis_get_option( 'enable_sticky_header' ), FILTER_VALIDATE_BOOLEAN );
}

/**
 * Check if shrink header is enabled
 *
 * @return bool
 */
function mai_is_shrink_header_enabled() {
	return filter_var( genesis_get_option( 'enable_shrink_header' ), FILTER_VALIDATE_BOOLEAN );
}

/**
 * Check if banner area is enabled globally
 *
 * @return bool
 */
function mai_is_banner_area_enabled_globally() {
	return filter_var( genesis_get_option( 'enable_banner_area' ), FILTER_VALIDATE_BOOLEAN );
}

/**
 * Check if banner featured images is enabled
 *
 * @return bool
 */
function mai_is_banner_featured_image_enabled( $post_id = '' ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	$post_type = get_post_type( $post_id );
	if ( $post_type && post_type_supports( $post_type, 'mai-cpt-settings' ) ) {
		return filter_var( genesis_get_option( sprintf( 'banner_featured_image_%s', $post_type ) ), FILTER_VALIDATE_BOOLEAN );
	}
	return in_array( $post_type, (array) genesis_get_option( 'banner_featured_image' ) );
}

/**
 * Check if side menu is enabled
 *
 * @return bool
 */
function mai_is_side_menu_enabled() {
	if ( 'side' != genesis_get_option( 'mobile_menu_style' ) ) {
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
	return genesis_get_option( 'footer_widget_count' );
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
 * Get a direct link to open a specific customizer panel.
 * Optionally include a frontend URL to preview.
 *
 * @param   string  $section  The name of the section to open.
 * @param   string  $url      The preview URL.
 *
 * @return  string  The customizer URL.
 */
function mai_get_customizer_section_link( $section, $url = '' ) {
	$query['autofocus[section]'] = $section;
	if ( $url ) {
		$query['url'] = esc_url( $url );
	}
	return add_query_arg( $query, admin_url( 'customize.php' ) );
}

/**
 * Get a direct link to open a specific CPT settings customizer panel.
 *
 * @param   string  $post_type  The registered CPT name.
 *
 * @return  string  The customizer URL.
 */
function mai_get_customizer_post_type_settings_link( $post_type ) {
	$section = sprintf( 'mai_%s_archive_settings', $post_type );
	$url     = get_post_type_archive_link( $post_type );
	return mai_get_customizer_section_link( $section, $url );
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
	return array_map( 'sanitize_key', array_map( 'trim', array_filter( $keys ) ) );
}

/**
 * Helper function to sanitize a value to be either the integers 1 or 0.
 *
 * @param   mixed  $value  The value to sanitize.
 *
 * @return  int    The sanitized value. Either 1 or 0.
 */
function mai_sanitize_one_zero( $value ) {
	return absint( filter_var( $value, FILTER_VALIDATE_BOOLEAN ) );
}

/**
 * Sanitizes WYSIWYG fields like WordPress does for post_content fields.
 */
function mai_sanitize_post_content( $content ) {
	// $blah = apply_filters( 'content_save_pre', $content );
	// ddd( $content );
	// ddd( $blah );
	return apply_filters( 'content_save_pre', $content );
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
