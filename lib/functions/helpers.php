<?php

/**
 * Check if viewing a content archive page.
 * This is any archive page that may inherit (custom) archive settings.
 *
 * @return  bool
 */
function mai_is_content_archive() {

	$is_archive = false;

	// Blog
	if ( is_home() ) {
		$is_archive = true;
	}
	// Term archive
	elseif ( is_category() || is_tag() || is_tax() ) {
		$is_archive = true;
	}
	// CPT archive - this may be called too early to use get_post_type()
	// elseif ( is_post_type_archive() && genesis_has_post_type_archive_support() ) {
	// elseif ( is_post_type_archive() && post_type_supports( get_query_var( 'post_type' ), 'mai-cpt-settings' ) ) {
	elseif ( is_post_type_archive() ) {
		$is_archive = true;
	}
	// Author archive
	elseif ( is_author() ) {
		$is_archive = true;
	}
	// Search results
	elseif ( is_search() ) {
		$is_archive = true;
	}

	return $is_archive;
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

	// If not enabled at all.
	if ( ! mai_is_banner_area_enabled_globally() ) {
		$enabled = false;
	} else {

		/**
		 * If disabled per post_type or taxonomy.
		 */

		// Singular page/post.
		if ( is_singular( array( 'page', 'post' ) ) ) {
			// Get 'disabled' post types, typecasted as array because it may return empty string if none
			$disable_post_types = (array) genesis_get_option( 'banner_disable_post_types' );
			if ( in_array( get_post_type(), $disable_post_types ) ) {
				$enabled = false;
			}
		}
		// Singular CPT.
		elseif ( is_singular() ) {
			$disable_post_type = (bool) genesis_get_option( sprintf( 'banner_disable_%s', get_post_type() ) );
			if ( $disable_post_type ) {
				$enabled = false;
			}
		}

		// elseif ( mai_is_content_archive() ) {
		// 	$disabled = mai_get_archive_setting( 'hide_banner', false );
		// 	if ( $disable_post_type ) {
		// 		$enabled = false;
		// 	}
		// }
		// elseif ( is_post_type_archive() && post_type_supports( get_post_type(), 'mai-cpt-settings' ) ) {
		// 	$disabled = genesis_get_cpt_option( 'hide_banner' );
		// 	if ( $disabled ) {
		// 		$enabled = false;
		// 	}
		// }

		// Post taxonomy archive.
		elseif ( is_category() || is_tag() ) {
			// Get 'disabled' taxonomies, typecasted as array because it may return empty string if none
			$disable_taxonomies = (array) genesis_get_option( 'banner_disable_taxonomies' );
			if ( $disable_taxonomies && in_array( get_queried_object()->taxonomy, $disable_taxonomies ) ) {
				$enabled = false;
			}
		}
		// Custom taxonomy archive.
		elseif ( is_tax() ) {
			$disable_taxonomies = (array) genesis_get_option( sprintf( 'banner_disable_taxonomies_%s', get_post_type() ) );
			if ( $disable_taxonomies && in_array( get_queried_object()->taxonomy, $disable_taxonomies ) ) {
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

			// If single post/page/cpt.
			if ( is_singular() ) {
				$hidden = get_post_meta( get_the_ID(), 'hide_banner', true );
			}
			// If content archive (the only other place we'd have this setting)
			elseif ( mai_is_content_archive() ) {
				// Get the setting directly, without fallbacks.
				$hidden = mai_get_the_archive_setting( 'hide_banner' );
			}

			// If hidden, disable banner.
			if ( $hidden ) {
				$enabled = false;
			}

		}

	}

	return $enabled;
}

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
		if ( ! $image_id && mai_is_banner_featured_image_enabled( $posts_page_id ) ) {
			$image_id = get_post_thumbnail_id( $posts_page_id );
		}
	}

	// Single page/post/cpt, but not static front page or static home page
	elseif ( is_singular() ) {
		$image_id = get_post_meta( get_the_ID(), 'banner_id', true );
		// If no image and featured images as banner is enabled
		if ( ! $image_id && mai_is_banner_featured_image_enabled( get_the_ID() ) ) {
			$image_id = get_post_thumbnail_id( get_the_ID() );
		}
		// Fallback
		if ( ! $image_id ) {
			// Get the post's post_type
			$post_type = get_post_type();
			// Posts
			if ( 'post' === $post_type && ( $posts_page_id = get_option( 'page_for_posts' ) ) ) {
				$image_id = get_post_meta( $posts_page_id, 'banner_id', true );
			}
			// CPTs
			elseif ( post_type_supports( $post_type, 'mai-cpt-settings' ) ) {
				// if ( mai_is_banner_featured_image_enabled( get_the_ID() ) ) {
					// $image_id = get_post_thumbnail_id( $posts_page_id );
				// }
				// $image_id = $image_id ? $image_id : genesis_get_cpt_option( 'banner_id', $post_type );
				$image_id = genesis_get_cpt_option( 'banner_id', $post_type );
			}
		}
	}

	// Term archive
	elseif ( is_category() || is_tag() || is_tax() ) {
		// If WooCommerce product category
		if ( class_exists( 'WooCommerce' ) && is_tax( array( 'product_cat', 'product_tag' ) ) && ( $image_id = get_term_meta( get_queried_object()->term_id, 'thumbnail_id', true ) ) ) {
			// Woo uses it's own image field/key
			$image_id = $image_id;
		} else {
			// $image_id = get_term_meta( get_queried_object()->term_id, 'banner_id', true );
			$image_id = mai_get_archive_setting( 'banner_id', false, false );
		}
// d( $image_id );
		// If no image
		// if ( ! $image_id ) {
			// Get hierarchical taxonomy term meta
			// $image_id = mai_get_term_meta_value_in_hierarchy( get_queried_object(), 'banner_id', false );
			// If still no image
			// if ( ! $image_id ) {
// d( get_queried_object() );
				// // Posts
				// if ( 'post' === $post_type && ( $posts_page_id = get_option( 'page_for_posts' ) ) ) {
				// 	$image_id = get_post_meta( $posts_page_id, 'banner_id', true );
				// }
				// // CPTs
				// elseif ( post_type_supports( $post_type, 'mai-cpt-settings' ) ) {
				// 	// genesis_has_post_type_archive_support( $post_type ) ) {
				// 	$image_id = genesis_get_cpt_option( 'banner_id', $post_type );
				// }
				// Check the archive settings, so we can fall back to the taxo's post_type setting
				// $image_id = mai_get_archive_setting( 'banner_id', false );
			// }
		// }
	}

	// CPT archive
	elseif ( is_post_type_archive() && post_type_supports( get_post_type(), 'mai-cpt-settings' ) ) {
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
 * Display the featured image.
 * Must be used in the loop.
 *
 * @param   string  $size  The image size to use.
 *
 * @return  void
 */
function mai_do_featured_image( $size = 'featured' ) {
	echo '<div class="featured-image">';
		echo genesis_get_image( array(
			'format' => 'html',
			'size'   => $size,
			'attr'   => array( 'class' => 'wp-post-image' )
			));
	echo '</div>';

	$caption = get_post( get_post_thumbnail_id() )->post_excerpt;
	if ( $caption ) {
		echo '<span class="image-caption">' . $caption . '</span>';
	}
}

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
	// Get the columns with fallback.
	$columns = mai_get_archive_setting( 'columns', true, genesis_get_option( 'columns' ) );
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

/**
 * Helper function to get a read more link for a post or term
 *
 * @param  int|WP_Post|WP_term?  $object  The object to get read more link for.
 * @param  string                $text    The "Read More" text.
 * @param  string                $type    The object type ('post' or 'term').
 *
 * @return HTML string for the link.
 */
function mai_get_read_more_link( $object_or_id = '', $text = '', $type = 'post' ) {

	$link = $url = $screen_reader_html = $screen_reader_text = '';

	$text           = $text ? sanitize_text_field($text) : __( 'Read More', 'mai-pro-engine' );
	$more_link_text = sanitize_text_field( apply_filters( 'mai_more_link_text', $text, $object_or_id, $type ) );

	switch ( $type ) {
		case 'post':
			$url                = get_permalink( $object_or_id );
			$screen_reader_text = get_the_title( $object_or_id );
		break;
		case 'term':
			$term               = is_object( $object_or_id ) ? $object_or_id : get_term( $object_or_id );
			$url                = get_term_link( $term );
			$screen_reader_text = $term->name;
		break;
	}

	// Build the screen reader text html
	if ( $screen_reader_text ) {
		$screen_reader_html = sprintf( '<span class="screen-reader-text">%s</span>', esc_html( $screen_reader_text ) );
	}

	// If we have a url
	if ( $url ) {
		$attributes = array(
			'class' => 'more-link',
			'href'  => esc_url( $url ),
		);
		$link = sprintf( '<a %s>%s%s</a>', genesis_attr( 'more-link', $attributes ), $screen_reader_html, $more_link_text );
	}

	// Bail if no link
	if ( empty( $link ) ) {
		return;
	}

	return sprintf( '<p class="more-link-wrap">%s</p>', $link );
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

function mai_is_no_sidebar() {
	$layout = genesis_site_layout();
	$no_sidebars = array(
		'full-width-content',
		'md-content',
		'sm-content',
		'xs-content',
	);
	if ( in_array( $layout, $no_sidebars ) ) {
		return false;
	}
	return true;
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

function mai_is_admin_woo_shop_page() {
	// False is Woo is not active.
	if ( ! class_exists('WooCommerce') ) {
		return false;
	}
	// False if not editing a page/post.
	global $pagenow;
	if ( 'post.php' != $pagenow ) {
		return false;
	}
	// Get the ids.
	$post_id      = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
	$shop_page_id = get_option( 'woocommerce_shop_page_id' );
	// If WooCommerce shop page
	if ( $post_id == $shop_page_id ) {
		return true;
	}
	// Nope.
	return false;
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
