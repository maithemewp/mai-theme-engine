<?php

// Output the static blog page content before the posts
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

add_action( 'genesis_before_loop', 'mai_maybe_remove_loop' );
function mai_maybe_remove_loop() {

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
    return $query;
}

add_action( 'genesis_before_while', 'mai_add_before_flex_loop_hook', 100 );
function mai_add_before_flex_loop_hook() {

    // Bail if not a content archive
    if ( ! mai_is_content_archive() ) {
        return;
    }

    // Bail if not a flex loop
    if ( ! mai_is_flex_loop() ) {
        return;
    }

    do_action( 'mai_before_flex_loop' );
}

add_action( 'genesis_after_endwhile', 'mai_add_after_flex_loop_hook' );
function mai_add_after_flex_loop_hook() {

    // Bail if not a content archive
    if ( ! mai_is_content_archive() ) {
        return;
    }

    // Bail if not a flex loop
    if ( ! mai_is_flex_loop() ) {
        return;
    }

    do_action( 'mai_after_flex_loop' );
}

// Flex loop opening html and filters
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

add_action( 'genesis_before_entry', 'mai_do_entry_image_background' );
function mai_do_entry_image_background() {

    // Get image location
    $image_location = mai_get_archive_setting( 'image_location', genesis_get_option( 'image_location' ) );

    // If background image
    if ( 'background' != $image_location ) {
        return;
    }

    // Get the image ID
    $image_id = get_post_thumbnail_id();

    // Bail if no image
    if ( ! $image_id ) {
        return;
    }

    // Create an anonomous markup functions
    $markup_open = function( $open ) {
        $open = str_replace( '<article', '<a', $open );
        return $open;
    };
    $markup_close = function( $close ) {
        $close = str_replace( '</article>', '</a>', $close );
        return $close;
    };

    // Get image size
    $image_size = mai_get_archive_setting( 'image_size', genesis_get_option( 'image_size' ) );

    // Anonomous attributes function
    $entry_attributes = function( $attributes ) use ( $image_id, $image_size ) {

        // Add classes and href link
        $attributes['class'] .= ' overlay light-content center-xs middle-xs';
        $attributes['href'] = get_permalink();

        // Add image background attributes
        $attributes = mai_add_image_background_attributes( $attributes, $image_id, $image_size );

        return $attributes;
    };

    // Change entry markup from 'arcticle' to 'a'
    add_filter( 'genesis_markup_entry_open', $markup_open );
    add_filter( 'genesis_markup_entry_close', $markup_close );
    add_filter( 'genesis_attr_entry', $entry_attributes );
    add_filter( 'genesis_link_post_title', '__return_false' );
    add_filter( 'genesis_markup_entry-content_open', '__return_false' );
    add_filter( 'genesis_markup_entry-content_close', '__return_false' );

    // Remove the filters so any other loops aren't affected
    add_action( 'genesis_after_entry', function() use ( $markup_open, $markup_close, $entry_attributes ) {
        remove_filter( 'genesis_markup_entry_open', $markup_open );
        remove_filter( 'genesis_markup_entry_close', $markup_close );
        remove_filter( 'genesis_attr_entry', $entry_attributes );
        remove_filter( 'genesis_link_post_title', '__return_true' );
        remove_filter( 'genesis_markup_entry-content_open', '__return_true' );
        remove_filter( 'genesis_markup_entry-content_close', '__return_true' );
    });

}

/**
 * Add the shortcode column count to the flex loop setting.
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
 * then pass them to the filter to avoid a redirect loop
 * since the helper function falls back to genesis_option() function.
 *
 * @since   1.0.0
 *
 * @return  void
 */
add_action( 'genesis_before_loop', 'mai_do_archive_options' );
function mai_do_archive_options() {

    // Bail if not a content archive
    if ( ! mai_is_content_archive() ) {
        return;
    }

    $content_archive_thumbnail = mai_get_archive_setting( 'content_archive_thumbnail', genesis_get_option( 'content_archive_thumbnail' ) );
    $image_size                = mai_get_archive_setting( 'image_size', genesis_get_option( 'image_size' ) );
    $content_archive           = mai_get_archive_setting( 'content_archive', genesis_get_option( 'content_archive' ) );
    $content_archive_limit     = absint( mai_get_archive_setting( 'content_archive_limit', genesis_get_option( 'content_archive_limit' ) ) );

    if ( 'none' == $content_archive ) {
        // Remove the post content
        remove_action( 'genesis_entry_content', 'genesis_do_post_content' );
    }

    add_filter( 'genesis_options', function( $options ) use ( $content_archive_thumbnail, $image_size, $content_archive, $content_archive_limit ) {
        $options['content_archive_thumbnail'] = $content_archive_thumbnail;
        $options['image_size']                = $image_size;
        $options['content_archive']           = $content_archive;
        $options['content_archive_limit']     = absint( $content_archive_limit );
        return $options;
    });

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

/**
 * Add the archive featured image in the correct location.
 * No need to check if display image is checked, since that happens
 * in the genesis_option filters already.
 *
 * @return  void
 */
add_action( 'genesis_before_while', 'mai_do_post_image' );
function mai_do_post_image() {

    // Bail if not a content archive
    if ( ! mai_is_content_archive() ) {
        return;
    }

    // Remove the post image
    remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );

    $location = mai_get_archive_setting( 'image_location', genesis_get_option( 'more_link' ) );

    // Bail if no location
    if ( ! $location ) {
        return;
    }

    // Add the images in the correct location
    if ( 'before_entry' == $location ) {
        add_action( 'genesis_entry_header', 'genesis_do_post_image', 2 );
    } elseif ( 'before_title' == $location ) {
        add_action( 'genesis_entry_header', 'genesis_do_post_image', 8 );
    } elseif ( 'after_title' == $location ) {
        add_action( 'genesis_entry_header', 'genesis_do_post_image', 10 );
    } elseif ( 'before_content' == $location ) {
        add_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
    }

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

    $meta_to_remove = mai_get_archive_setting( 'remove_meta', genesis_get_option( 'remove_meta' ) );

    if ( $meta_to_remove ) {

        if ( in_array( 'post_info', $meta_to_remove ) ) {
            // Remove the entry meta in the entry header
            remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
        }

        // if ( isset( $meta_to_remove['post_meta'] ) ) {
        if ( in_array( 'post_meta', $meta_to_remove ) ) {
            // Remove the entry footer markup
            remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
            remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );
            // Remove the entry meta in the entry footer
            remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
        }

    }
}
