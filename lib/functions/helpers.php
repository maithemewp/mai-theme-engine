<?php

/**
 * Get the site layout.
 *
 * @access  private
 *
 * @since   1.3.0
 *
 * @return  string  The site layout.
 */
function mai_get_layout() {

	// Setup cache.
	static $layout_cache = '';

	// If cache is populated, return value.
	if ( '' !== $layout_cache ) {
		return esc_attr( $layout_cache );
	}

	$site_layout = '';

	global $wp_query;

	// If blog.
	if ( is_home() ) {
		if ( $page_for_posts = get_option( 'page_for_posts' ) ) {
			$site_layout = genesis_get_custom_field( '_genesis_layout', $page_for_posts );
		}
		if ( ! $site_layout ) {
			$site_layout = genesis_get_option( 'layout_archive' );
		}
	}

	// If viewing a singular page, post, or CPT.
	elseif ( is_singular() ) {
		$site_layout = genesis_get_custom_field( '_genesis_layout', get_the_ID() );
		if ( ! $site_layout ) {
			$site_layout = genesis_get_option( sprintf( 'layout_%s', get_post_type() ) );
		}
	}

	// If viewing a post taxonomy archive.
	elseif ( is_category() || is_tag() || is_tax( get_object_taxonomies( 'post', 'names' ) ) ) {
		$term        = $wp_query->get_queried_object();
		$site_layout = $term ? get_term_meta( $term->term_id, 'layout', true) : '';
		$site_layout = $site_layout ? $site_layout : genesis_get_option( 'layout_archive' );
	}

	// If viewing a custom taxonomy archive.
	elseif ( is_tax() ) {
		$term        = $wp_query->get_queried_object();
		$site_layout = $term ? get_term_meta( $term->term_id, 'layout', true) : '';
		if ( ! $site_layout ) {
			$tax = get_taxonomy( $wp_query->get_queried_object()->taxonomy );
			if ( $tax ) {
				/**
				 * If we have a tax, get the first one.
				 * Changed to reset() when hit an error on a term archive that object_type array didn't start with [0]
				 */
				$post_type = reset( $tax->object_type );
				// If we have a post type and it supports mai-cpt-settings.
				if ( post_type_exists( $post_type ) && post_type_supports( $post_type, 'mai-cpt-settings' ) ) {
					$site_layout = genesis_get_cpt_option( 'layout', $post_type );
				}
			}
		}
		$site_layout = $site_layout ? $site_layout : genesis_get_option( 'layout_archive' );
	}

	// If viewing a supported post type.
	elseif ( is_post_type_archive() && post_type_supports( get_post_type(), 'mai-cpt-settings' ) ) {
		// $site_layout = genesis_get_option( sprintf( 'layout_archive_%s', get_post_type() ) );
		$site_layout = genesis_get_cpt_option( 'layout', get_post_type() );
		$site_layout = $site_layout ? $site_layout : genesis_get_option( 'layout_archive' );
	}

	// If viewing an author archive.
	elseif ( is_author() ) {
		$site_layout = get_the_author_meta( 'layout', (int) get_query_var( 'author' ) );
		$site_layout = $site_layout ? $site_layout : genesis_get_option( 'layout_archive' );
	}

	// If viewing date archive or search results.
	elseif( is_date() || is_search() ) {
		$site_layout = genesis_get_option( 'layout_archive' );
	}

	// Pull the theme option.
	if ( ! $site_layout ) {
		$site_layout = genesis_get_option( 'site_layout' );
	}

	// Use default layout as a fallback, if necessary.
	if ( ! genesis_get_layout( $site_layout ) ) {
		$site_layout = genesis_get_default_layout();
	}
	// Push layout into cache.
	$layout_cache = $site_layout;

	// Return site layout.
	return esc_attr( $site_layout );
}

/**
 * Check if viewing a content archive page.
 * This is any archive page that may inherit (custom) archive settings.
 *
 * @return  bool
 */
function mai_is_content_archive() {

	global $wp_query;

	if ( ! $wp_query->is_main_query() ) {
		return false;
	}

	$is_archive = false;

	// Blog.
	if ( is_home() ) {
		$is_archive = true;
	}
	// Term archive.
	elseif ( is_category() || is_tag() || is_tax() ) {
		$is_archive = true;
	}
	// CPT archive - this may be called too early to use get_post_type().
	elseif ( is_post_type_archive() ) {
		$is_archive = true;
	}
	// Author archive.
	elseif ( is_author() ) {
		$is_archive = true;
	}
	// Search results.
	elseif ( is_search() ) {
		$is_archive = true;
	}
	// Date archives.
	elseif( is_date() ) {
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
			// If content archive (the only other place we'd have this setting).
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
	}

	// CPT archive
	elseif ( is_post_type_archive() && post_type_supports( get_post_type(), 'mai-cpt-settings' ) ) {
		$image_id = genesis_get_cpt_option( 'banner_id' );
	}

	// Author archive
	elseif ( is_author() ) {
		$image_id = get_the_author_meta( 'banner_id', get_query_var( 'author' ) );
	}

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
 * Get the col span out of 12 column grid.
 * If we want to show posts in 3 columns the size is 4 because 4 out of 12 is 1/3.
 *
 * @param   int  The amount of visual columns to display.
 *
 * @return  int  The column span out of 12.
 */
function mai_get_size_by_columns( $columns ) {
	switch ( (int) $columns ) {
		case 1:
			$size = 12;
			break;
		case 2:
			$size = 6;
			break;
		case 3:
			$size = 4;
			break;
		case 4:
			$size = 3;
			break;
		case 6:
			$size = 2;
			break;
		default:
			$size = 12;
	}
	return $size;
}

/**
 * Get gutter size name from gutter value.
 *
 * @since   1.3.8
 * @access  private
 *
 * @param   mixed   Gutter value.
 *
 * @return  string  The gutter size.
 */
function mai_get_gutter_size( $gutter ) {
	switch ( (string) $gutter ) {
		case '0':
		case 'none':
			$size = '0';
		break;
		case '5':
		case 'xxxs':
			$size = 'xxxs';
		break;
		case '10':
		case 'xxs':
			$size = 'xxs';
		break;
		case 'xs':
			$size = 'xs';
		break;
		case '20':
		case 'sm':
			$size = 'sm';
		break;
		case '30':
		case 'md':
			$size = 'md';
		break;
		case '40':
		case 'lg':
			$size = 'lg';
		break;
		case '50':
		case 'xl':
			$size = 'xl';
		break;
		case '50':
		case 'xl':
			$size = 'xl';
		break;
		case '60':
		case 'xxl':
			$size = 'xxl';
		break;
			$size = '0';
	}
	return $size;
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
 * Filter the CPT's that get archive settings.
 * Add via:
 * $post_types['cpt_name'] = get_post_type_object( 'cpt_name' );
 *
 * Remove via:
 * unset( $post_types['cpt_name'] );
 *
 * @return  array  key = post type name and value = post type object.
 */
function mai_get_cpt_settings_post_types() {
	return apply_filters( 'mai_cpt_settings_post_types', genesis_get_cpt_archive_types() );
}

function mai_sections_has_h1( $post_id ) {

	// Get the sections.
	$sections = get_post_meta( $post_id, 'mai_sections', true );

	// No sections.
	if ( ! $sections ) {
		return false;
	}

	// No title yet.
	$has_h1 = false;

	// Loop through each section.
	foreach ( (array) $sections as $section ) {
		// If content isset. Sometimes empty content doesn't even save the key.
		if ( isset( $section['content'] ) ) {
			// If content contains an h1.
			if ( false !== strpos( $section['content'], '</h1>' ) ) {
				$has_h1 = true;
				break;
			}
		}
	}

	return $has_h1;
}

function mai_sections_has_title( $post_id ) {

	// Get the sections.
	$sections = get_post_meta( $post_id, 'mai_sections', true );

	// No sections.
	if ( ! $sections ) {
		return false;
	}

	// No title yet.
	$has_title = false;

	// Loop through each section.
	foreach ( (array) $sections as $section ) {
		// Skip if no title.
		if ( empty( $section['title'] ) ) {
			continue;
		}
		// We have a title, change variable and break the loop.
		$has_title = true;
		break;
	}

	return $has_title;
}
