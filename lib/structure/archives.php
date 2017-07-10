<?php
/**
 * Mai Pro Engine.
 *
 * @author   Mike Hemberger
 *
 * @version  1.0.0
 */


/**
 * Output the static blog page content before the posts.
 *
 * @return  void
 */
add_action( 'genesis_before_loop', 'mai_do_blog_description', 20 );
function mai_do_blog_description() {

	// Bail if not the blog page
	if ( ! ( is_home() && $posts_page = get_option( 'page_for_posts' ) ) ) {
		return;
	}

	// Echo the content
	echo apply_filters( 'the_content', get_post( $posts_page )->post_content );
}


/**
 * Add term description before custom taxonomy loop.
 * This is the core WP term description, not the Genesis Intro Text.
 * Genesis Intro Text is in banner.
 *
 * @return  void
 */
add_action( 'genesis_before_loop', 'mai_do_term_description', 20 );
function mai_do_term_description() {

	// Bail if not a taxonomy archive
	if ( ! ( is_category() || is_tag() || is_tax() ) ) {
		return;
	}

	// If the first page
	if ( 0 === absint( get_query_var( 'paged' ) ) ) {
		$description = term_description();
		if ( $description ) {
			echo '<div class="term-description">' . do_shortcode($description) . '</div>';
		}
	}
}

/**
 * Remove the loop if archive settings say so.
 *
 * @return  void
 */
add_action( 'genesis_before_loop', 'mai_remove_content_archive_loop' );
function mai_remove_content_archive_loop() {

	// Bail if not a content archive
	if ( ! mai_is_content_archive() ) {
		return;
	}
	// Bail if not removing the loop
	$remove_loop = mai_get_archive_setting( 'remove_loop', false );
	if ( ! $remove_loop ) {
		return;
	}

	// Remove the loop
	remove_action( 'genesis_loop', 'genesis_do_loop' );
	remove_action( 'genesis_after_endwhile', 'genesis_posts_nav' );
	remove_action( 'genesis_after_loop', 'genesis_posts_nav' );
}

/**
 * Set the archive posts_per_page if we have a custom setting.
 *
 * @return void
 */
add_filter( 'pre_get_posts', 'mai_content_archive_posts_per_page' );
function mai_content_archive_posts_per_page( $query ) {

	// Bail if not the main query
	if ( ! $query->is_main_query() || is_admin() || is_singular() ) {
		return;
	}

	// Bail if not a content archive
	if ( ! mai_is_content_archive() ) {
		return;
	}

	// Get the posts_per_page
	$posts_per_page = mai_get_archive_setting( 'posts_per_page', false );
	/**
	* posts_per_page setting doesn't fallback to genesis_option,
	* if requires the core WP posts_per_page setting.
	* Instead of crazy conditionals in our helper function,
	* let's just bail here and let WP do it's thing.
	*/
	if ( ! $posts_per_page ) {
		return;
	}
	$query->set( 'posts_per_page', $posts_per_page );
}

/**
 * Add the mai_before_flex_loop hook when appropriate.
 *
 * @return void
 */
add_action( 'genesis_before_while', 'mai_add_before_flex_loop_hook', 100 );
function mai_add_before_flex_loop_hook() {

	// Bail if not a flex loop
	if ( ! mai_is_flex_loop() ) {
		return;
	}

	do_action( 'mai_before_flex_loop' );
}

/**
 * Add the mai_after_flex_loop hook when appropriate.
 *
 * @return void
 */
add_action( 'genesis_after_endwhile', 'mai_add_after_flex_loop_hook' );
function mai_add_after_flex_loop_hook() {

	// Bail if not a flex loop
	if ( ! mai_is_flex_loop() ) {
		return;
	}

	do_action( 'mai_after_flex_loop' );
}

/**
 * Flex loop opening html and column filters.
 * Add and remove the post/product class filters to create columns.
 *
 * @return  void
 */
add_action( 'mai_before_flex_loop', 'mai_do_flex_loop_before' );
function mai_do_flex_loop_before() {

	// Flex row wrap
	$attributes['class'] = 'row gutter-30';
	printf( '<div %s>', genesis_attr( 'flex-row', $attributes ) );

	// Get the archive column count
	$columns = mai_get_archive_setting( 'columns', genesis_get_option( 'columns' ) );

	// If a Woo product archive
	if ( class_exists( 'WooCommerce' ) ) {
		if ( is_shop() || is_tax( get_object_taxonomies( 'product', 'names' ) ) || is_product() ) {
			if ( $columns <= 1 ) {
				$columns = 3;
			}
		}
		// Cross-sells
		elseif ( is_cart() ) {
			$columns = 2;
		}
	}

	// Create an anonomous function using the column count
	$flex_classes = function( $classes ) use ( $columns ) {
		$classes[] = mai_get_flex_entry_classes_by_columns( $columns );
		return $classes;
	};

	// Add flex entry classes
	add_filter( 'post_class', $flex_classes );
	add_filter( 'product_cat_class', $flex_classes );

	/**
	 * After the loops, remove the entry classes filters and close the flex loop.
	 * This makes sure the columns classes aren't applied to
	 * additional loops.
	 */
	add_action( 'mai_after_flex_loop', function() use ( $flex_classes ) {
		remove_filter( 'post_class', $flex_classes );
		remove_filter( 'product_cat_class', $flex_classes );
		echo '</div>';
	});
}

/**
 * Add the WooCommerce shortcode column count to the flex loop setting.
 *
 * @return  void
 */
add_action( 'woocommerce_shortcode_before_recent_products_loop',       'mai_woo_shortcode_before_loop' );
add_action( 'woocommerce_shortcode_before_sale_products_loop',         'mai_woo_shortcode_before_loop' );
add_action( 'woocommerce_shortcode_before_best_selling_products_loop', 'mai_woo_shortcode_before_loop' );
add_action( 'woocommerce_shortcode_before_top_rated_products_loop',    'mai_woo_shortcode_before_loop' );
add_action( 'woocommerce_shortcode_before_featured_products_loop',     'mai_woo_shortcode_before_loop' );
add_action( 'woocommerce_shortcode_before_related_products_loop',      'mai_woo_shortcode_before_loop' );
function mai_woo_shortcode_before_loop( $atts ) {

	// Create an anonomous function using the column count
	$shortcode_columns = function( $columns ) use ( $atts ) {
	return $atts['columns'];
	};
	// Set the columns to the Woo shortcode att
	add_filter( 'mai_pre_get_archive_setting_columns', $shortcode_columns );

	// Create an anonomous function using the column count
	$entry_classes = function( $classes ) {
	$classes[] .= 'entry';
	return $classes;
	};
	// Add flex entry classes
	add_filter( 'post_class', $entry_classes );
	add_filter( 'product_cat_class', $entry_classes );

	// Remove the filters setting the columns
	add_action( 'woocommerce_shortcode_after_recent_products_loop', function() use ( $shortcode_columns, $entry_classes ) {
		remove_filter( 'mai_pre_get_archive_setting_columns', $shortcode_columns );
		remove_filter( 'post_class', $entry_classes );
		remove_filter( 'product_cat_class', $entry_classes );
	});
	add_action( 'woocommerce_shortcode_after_sale_products_loop', function() use ( $shortcode_columns, $entry_classes ) {
		remove_filter( 'mai_pre_get_archive_setting_columns', $shortcode_columns );
		remove_filter( 'post_class', $entry_classes );
		remove_filter( 'product_cat_class', $entry_classes );
	});
	add_action( 'woocommerce_shortcode_after_best_selling_products_loop', function() use ( $shortcode_columns, $entry_classes ) {
		remove_filter( 'mai_pre_get_archive_setting_columns', $shortcode_columns );
		remove_filter( 'post_class', $entry_classes );
		remove_filter( 'product_cat_class', $entry_classes );
	});
	add_action( 'woocommerce_shortcode_after_top_rated_products_loop', function() use ( $shortcode_columns, $entry_classes ) {
		remove_filter( 'mai_pre_get_archive_setting_columns', $shortcode_columns );
		remove_filter( 'post_class', $entry_classes );
		remove_filter( 'product_cat_class', $entry_classes );
	});
	add_action( 'woocommerce_shortcode_after_featured_products_loop', function() use ( $shortcode_columns, $entry_classes ) {
		remove_filter( 'mai_pre_get_archive_setting_columns', $shortcode_columns );
		remove_filter( 'post_class', $entry_classes );
		remove_filter( 'product_cat_class', $entry_classes );
	});
	add_action( 'woocommerce_shortcode_after_related_products_loop', function() use ( $shortcode_columns, $entry_classes ) {
		remove_filter( 'mai_pre_get_archive_setting_columns', $shortcode_columns );
		remove_filter( 'post_class', $entry_classes );
		remove_filter( 'product_cat_class', $entry_classes );
	});
}

/**
 * Do the content archive options.
 * Hook in before the loop, get the variables first,
 * then pass them to the filters to avoid a redirect loop
 * since the helper function falls back to genesis_option() function.
 *
 * @since   1.0.0
 *
 * @return  void
 */
add_action( 'genesis_before_while', 'mai_do_content_archive_archive_options' );
function mai_do_content_archive_archive_options() {

	// Bail if not a content archive
	if ( ! mai_is_content_archive() ) {
		return;
	}
	$content_archive_thumbnail = mai_get_archive_setting( 'content_archive_thumbnail', genesis_get_option( 'content_archive_thumbnail' ) );
	$image_size                = mai_get_archive_setting( 'image_size', genesis_get_option( 'image_size' ) );
	$image_alignment           = mai_get_archive_setting( 'image_alignment', genesis_get_option( 'image_alignment' ) );
	$image_location            = mai_get_archive_setting( 'image_location', genesis_get_option( 'image_location' ) );
	$content_archive           = mai_get_archive_setting( 'content_archive', genesis_get_option( 'content_archive' ) );
	$content_archive_limit     = absint( mai_get_archive_setting( 'content_archive_limit', genesis_get_option( 'content_archive_limit' ) ) );
	$posts_nav                 = mai_get_archive_setting( 'posts_nav', genesis_get_option( 'posts_nav' ) );

	// Content
	if ( 'none' === $content_archive ) {
		// Remove the post content
		remove_action( 'genesis_entry_content', 'genesis_do_post_content' );
	} else {
		// Content Archive
		add_filter( 'genesis_pre_get_option_content_archive', function( $option ) use ( $content_archive ) {
			return $content_archive;
		});
		// Archive Limit
		add_filter( 'genesis_pre_get_option_content_archive_limit', function( $option ) use ( $content_archive_limit ) {
			return $content_archive_limit;
		});
	}

	// Remove the post image
	remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );

	// If we're showing the image
	if ( $content_archive_thumbnail ) {

		// Add the image back, in a custom location
		mai_do_post_image();

		// Image Size
		add_filter( 'genesis_pre_get_option_image_size', function( $option ) use ( $image_size ) {
			return $image_size;
		});
		// Image Alignment
		add_filter( 'genesis_pre_get_option_image_alignment', function( $option ) use ( $image_alignment ) {
			return $image_alignment;
		});

	}

	// Posts Nav
	add_filter( 'genesis_pre_get_option_posts_nav', function( $option ) use ( $posts_nav ) {
		return $posts_nav;
	});
}

/**
 * Add the archive featured image in the correct location.
 * No need to check if display image is checked, since that happens
 * in the genesis_option filters already.
 *
 * @return  void
 */
function mai_do_post_image() {

	$location = mai_get_archive_setting( 'image_location', genesis_get_option( 'image_location' ) );

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
		add_action( 'genesis_entry_header', 'mai_add_bg_image_link', 1 );
		// Remove bg iamge link function so additional loops are not affected
		add_action( 'genesis_after_endwhile', function() {
			remove_action( 'genesis_entry_header', 'mai_add_bg_image_link', 1 );
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
 * @return void
 */
function mai_do_entry_image_background() {

	// Get the image ID
	$image_id = get_post_thumbnail_id();

	// Get image size
	$image_size = mai_get_archive_setting( 'image_size', genesis_get_option( 'image_size' ) );

	// Anonomous attributes function
	$entry_attributes = function( $attributes ) use ( $image_id, $image_size ) {

		// Make element a link whether we have an image or not
		$attributes = mai_add_background_image_attributes( $attributes, $image_id, $image_size );
		$attributes['href'] = get_permalink();

		// If we have an image
		if ( $image_id ) {
			// Add classes and href link
			$attributes['class'] .= ' overlay light-content';
			// Add image background attributes
		} else {
			$attributes['class'] .= ' dark-content';
		}

		// Center the content even if we don't have an image
		$attributes['class'] .= ' has-bg-link center-xs middle-xs text-xs-center';

		return $attributes;
	};

	// Add entry attributes
	add_filter( 'genesis_attr_entry', $entry_attributes );

	// Remove the filters so any other loops aren't affected
	add_action( 'genesis_after_entry', function() use ( $entry_attributes ) {
		remove_filter( 'genesis_attr_entry', $entry_attributes );
	});

}

function mai_add_bg_image_link() {
	printf( '<a href="%s" class="bg-link"><span class="screen-reader-text" aria-hidden="true">%s</span></a>', get_permalink(), get_the_title() );
}

/**
 * Maybe remove the archive meta.
 *
 * @return  void
 */
add_action( 'genesis_before_while', 'mai_archive_remove_meta' );
function mai_archive_remove_meta() {

	// Bail if not a content archive
	if ( ! mai_is_content_archive() ) {
		return;
	}

	// Get the meta to remove
	$meta_to_remove = (array) mai_get_archive_setting( 'remove_meta', genesis_get_option( 'remove_meta' ) );

	if ( in_array( 'post_info', $meta_to_remove ) ) {
		// Remove the entry meta in the entry header
		remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
	}

	if ( in_array( 'post_meta', $meta_to_remove ) ) {
		// Remove the entry footer markup
		remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
		remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );
		// Remove the entry meta in the entry footer
		remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
	}
}

/**
 * Filter the excerpt "read more" string.
 *
 * @uses    excerpt_more                When the excerpt is shorter then the full content, this read more link will show.
 * @uses    get_the_content_more_link   Genesis function to get the more link, if characters are limited.
 * @uses    the_content_more_link       Not sure when this is used.
 *
 * @param   string  $more               "Read more" excerpt string.
 *
 * @return  string  (Maybe)             Ellipses if content has been shortened.
 */
add_filter( 'excerpt_more', 'mai_read_more_ellipses' );
add_filter( 'get_the_content_more_link', 'mai_read_more_ellipses' );
add_filter( 'the_content_more_link', 'mai_read_more_ellipses' );
function mai_read_more_ellipses( $more ) {
	return ' &hellip;';
}

/**
 * Maybe add the more link to content archives.
 *
 * @since   1.0.0
 *
 * @return  void
 */
add_action( 'genesis_entry_content', 'mai_do_more_link' );
function mai_do_more_link() {

	// Bail if not a content archive
	if ( ! mai_is_content_archive() ) {
		return;
	}

	$more_link = mai_get_archive_setting( 'more_link', genesis_get_option( 'more_link' ) );
	if ( ! $more_link ) {
		return;
	}
	echo mai_get_read_more_link( get_the_ID() );
}
