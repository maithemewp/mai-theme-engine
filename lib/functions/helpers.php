<?php

/**
 * Helper function to get custom image sizes.
 *
 * @access  private
 * @since   1.8.0
 *
 * @return  array  Image sizes and labels.
 */
function mai_get_image_sizes() {

	// Get labels.
	$labels = mai_get_image_size_labels();

	/**
	 * Create the initial image sizes.
	 * @link http://andrew.hedges.name/experiments/aspect_ratio/
	 */
	$image_sizes = array(
		'banner' => array(
			'label'  => $labels[ 'banner' ],
			'width'  => 1600,
			'height' => 533,
			'crop'   => true, // 3x1
		),
		'section' => array(
			'label'  => $labels[ 'section' ],
			'width'  => 1600,
			'height' => 900,
			'crop'   => true, // 16x9
		),
		'full-width' => array(
			'label'  => $labels[ 'full-width' ],
			'width'  => 1248,
			'height' => 832,
			'crop'   => true, // 3x2
		),
		'featured' => array(
			'label'  => $labels[ 'featured' ],
			'width'  => 800,
			'height' => 600,
			'crop'   => true, // 4x3 (works better for no sidebar)
		),
		'one-half' => array(
			'label'  => $labels[ 'one-half' ],
			'width'  => 550,
			'height' => 413,
			'crop'   => true, // 4x3
		),
		'one-third' => array(
			'label'  => $labels[ 'one-third' ],
			'width'  => 350,
			'height' => 263,
			'crop'   => true, // 4x3
		),
		'one-fourth' => array(
			'label'  => $labels[ 'one-fourth' ],
			'width'  => 260,
			'height' => 195,
			'crop'   => true, // 4x3
		),
		'tiny' => array(
			'label'  => $labels[ 'tiny' ],
			'width'  => 80,
			'height' => 80,
			'crop'   => true, // square
		),
	);

	/**
	 * Filter the image sizes to allow the theme to override.
	 *
	 * // Change the default Mai image sizes
	 * add_filter( 'mai_image_sizes', 'prefix_custom_image_sizes' );
	 * function prefix_custom_image_sizes( $image_sizes ) {
	 *
	 *   // Change one-third image size
	 *   $image_sizes['one-third'] = array(
	 *       'width'  => 350,
	 *       'height' => 350,
	 *       'crop'   => true,
	 *   );
	 *
	 *   // Change one-fourth image size
	 *   $image_sizes['one-fourth'] = array(
	 *       'width'  => 260,
	 *       'height' => 260,
	 *       'crop'   => true,
	 *   );
	 *
	 *   return $image_sizes;
	 *
	 * }
	 *
	 */
	$image_sizes = apply_filters( 'mai_image_sizes', $image_sizes );

	/**
	 * Make sure labels are added.
	 * 'mai_image_sizes' didn't have 'label' in the array prior to 1.8.0.
	 * This insures existing filters don't break.
	 */
	foreach( $image_sizes as $name => $values ) {
		if ( ! isset( $values['label'] ) || empty( $values['label'] ) ) {
			$image_sizes[ $name ]['label'] = $labels[ $name ];
		}
	}

	return $image_sizes;
}

/**
 * Get default image size labels.
 *
 * @access  private
 * @since   1.8.0
 *
 * @return  array
 */
function mai_get_image_size_labels() {
	return array(
		'banner'     => __( 'Banner', 'mai-theme-engine' ),
		'section'    => __( 'Section', 'mai-theme-engine' ),
		'full-width' => __( 'Full Width', 'mai-theme-engine' ),
		'featured'   => __( 'Featured', 'mai-theme-engine' ),
		'one-half'   => __( 'One Half', 'mai-theme-engine' ),
		'one-third'  => __( 'One Third', 'mai-theme-engine' ),
		'one-fourth' => __( 'One Fourth', 'mai-theme-engine' ),
		'tiny'       => __( 'Tiny', 'mai-theme-engine' ),
	);
}

/**
 * Utility method to get a combined list of default and custom registered image sizes.
 * Originally taken from CMB2. Static variable added here.
 *
 * @since   1.11.0
 * @link    http://core.trac.wordpress.org/ticket/18947
 * @global  array  $_wp_additional_image_sizes.
 * @return  array  The image sizes.
 */
function mai_get_available_image_sizes() {
	// Cache.
	static $image_sizes = array();
	if ( ! empty( $image_sizes ) ) {
		return $image_sizes;
	}
	// Get image sizes.
	global $_wp_additional_image_sizes;
	$default_image_sizes = array( 'thumbnail', 'medium', 'large' );
	foreach ( $default_image_sizes as $size ) {
		$image_sizes[ $size ] = array(
			'height' => intval( get_option( "{$size}_size_h" ) ),
			'width'  => intval( get_option( "{$size}_size_w" ) ),
			'crop'   => get_option( "{$size}_crop" ) ? get_option( "{$size}_crop" ) : false,
		);
	}
	if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) ) {
		$image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );
	}
	return $image_sizes;
}

/**
 * Get <picture> <sources> HTML.
 *
 * @since   1.11.0
 *
 * @param   int     The image ID.
 * @param   string  The image size.
 *
 * @return  string  The <sources> HTML.
 */
function mai_get_picture_sources( $image_id, $image_size ) {

	$sources       = '';
	$picture_sizes = mai_get_picture_sizes( $image_size );

	// Bail if no picture sizes.
	if ( ! array( $picture_sizes ) || empty( $picture_sizes ) ) {
		return $sources;
	}

	$all_sizes = wp_list_sort( mai_get_available_image_sizes(), 'width', 'ASC', true );

	// Bail if no sizes.
	if ( ! array( $all_sizes ) || empty( $all_sizes ) ) {
		return $sources;
	}

	/**
	 * Get only sizes and values we need.
	 * Tonya says this is faster than array_intersect_key( $all_sizes, array_flip( $picture_sizes ) ).
	 */
	$sizes = array();
	foreach ( $picture_sizes as $key ) {
		if ( array_key_exists( $key, $all_sizes ) ) {
			$sizes[ $key ] = $all_sizes[ $key ];
		}
	}

	// Bail if no sizes.
	if ( ! $sizes ) {
		return $sources;
	}

	// Loop through the sizes.
	foreach( $sizes as $size => $values ) {
		// Add the source.
		$sources .= sprintf( '<source srcset="%s" media="(max-width: %spx)">', wp_get_attachment_image_url( $image_id, $size ), $values['width'] );
	}

	return $sources;
}

/**
 * Get registered image sizes to be used for <sources> in <picture>.
 *
 * @since   1.11.0
 *
 * @param   string  The image size.
 *
 * @return  array  The registered image sizes.
 */
function mai_get_picture_sizes( $image_size ) {
	switch ( $image_size ) {
		case 'banner':
		case 'section':
		case 'full-width':
			$picture_sizes = array( 'featured', 'one-half', 'one-third', 'one-fourth' );
			break;
		case 'featured':
			$picture_sizes = array( 'one-half', 'one-third', 'one-fourth' );
			break;
		case 'one-half':
			$picture_sizes = array( 'one-third', 'one-fourth' );
			break;
		case 'one-third':
			$picture_sizes = array( 'one-fourth' );
			break;
		default:
			$picture_sizes = array();
	}
	return apply_filters( 'mai_picture_sizes', $picture_sizes, $image_size );
}

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
 * @return  bool  Whether the layout is a grid archive.
 */
function mai_is_flex_loop() {
	// Bail if not a content archive.
	if ( ! mai_is_content_archive() ) {
		return false;
	}
	// Get columns.
	$columns = mai_get_columns();
	// If we have more than 1 column or if we are using featured image as bg image, it's a flex loop.
	if ( ( $columns > 1 ) || ( 'background' === mai_get_archive_setting( 'image_location', true, genesis_get_option( 'image_location' ) ) ) ) {
		return true;
	}
	// Not a flex loop.
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

function mai_has_shrink_header() {
	$header_style = genesis_get_option( 'header_style' );
	if ( ! $header_style ) {
		return false;
	}
	if ( ! in_array( $header_style, array( 'sticky_shink', 'reveal_shrink' ) ) ) {
		return false;
	}
	return true;
}

function mai_has_scroll_header() {
	$header_style = genesis_get_option( 'header_style' );
	return ( $header_style && in_array( $header_style, array( 'sticky', 'reveal', 'sticky_shrink', 'reveal_shrink' ) ) );
}
