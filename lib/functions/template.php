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

    if ( is_front_page() && $front_page_id = get_option( 'page_on_front' ) ) {
        $image_id = get_post_meta( $front_page_id, 'banner_id', true );
    }
    elseif ( is_home() && $posts_page_id = get_option( 'page_for_posts' ) ) {
        $image_id = get_post_meta( $posts_page_id, 'banner_id', true );
    }
    // Single page/post/cpt, but not static front page or static home page
    elseif ( is_singular() ) {
        $image_id = get_post_meta( get_the_ID(), 'banner_id', true );

        // If No image and CPT has genesis archive support
        if ( ! $image_id ) {

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
            // Check the archive settings, so we can fall back to the taxo's post_type setting
            $image_id = mai_get_archive_setting( 'banner_id', false );
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
    elseif ( class_exists( 'WooCommerce' ) && is_shop() && ( $shop_page_id = get_option( 'woocommerce_shop_page_id' ) ) ) {
        $image_id = get_post_meta( $shop_page_id, 'banner_id', true );
    }

    /**
     * If no banner, but we have a default,
     * use the default banner image.
     */
    if ( ! $image_id ) {
        if ( $default_id = get_theme_mod( 'banner_id' ) ) {
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
function mai_get_grid( $args, $content = null ) {
    return Mai_Shortcodes()->get_grid( $args, $content );
}


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

function mai_get_section( $args, $content ) {
    return Mai_Shortcodes()->get_section( $args, $content );
}

/**
 * Get the section opening markup
 *
 * @param  array  $args  The section args.
 *
 * @return string|HTML
 */
function mai_get_section_open( $args ) {
    return Mai_Shortcodes()->get_section_open( $args );
}

/**
 * Get the section closing markup
 *
 * @param  array  $args  The section args.
 *
 * @return string|HTML
 */
function mai_get_section_close( $args ) {
    return Mai_Shortcodes()->get_section_close( $args );
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

    $url = $screen_reader_html = $screen_reader_text = '';

    $text           = $text ? sanitize_text_field($text) : __( 'Read More', 'mai-pro' );
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
    $image_location = mai_get_archive_setting( 'image_location', genesis_get_option( 'image_location' ) );

    // If background image
    if ( 'background' == $image_location ) {
        $link = sprintf( '<span class="more-link">%s%s</span>', $screen_reader_html, $more_link_text );
    } else {
        if ( $url ) {
            $link = sprintf( '<a class="more-link" href="%s">%s%s</a>', $url, $screen_reader_html, $more_link_text );
        }
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
function mai_get_post_meta( $post = '' ) {

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
        $post_meta = $shortcodes;
    }
    return $post_meta;
}

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

function mai_add_background_image_attributes( $attributes, $image_id, $image_size ) {
    // Get all registered image sizes
    global $_wp_additional_image_sizes;

    // Get the image
    $image = wp_get_attachment_image_src( $image_id, $image_size, true );

    // Bail if no image
    if ( ! $image ) {
        return $attributes;
    }

    // Make sure style attribute is set
    $attributes['style'] = isset( $attributes['style'] ) ? $attributes['style'] : '';

    // Add background image
    $inline_style        = sprintf( 'background-image: url(%s);', $image[0] );
    $attributes['style'] .= isset( $attributes['style'] ) ? $attributes['style'] . $inline_style : $inline_style;

    // Add aspect ratio class, for JS to target
    $attributes['class'] .= ' image-bg aspect-ratio';

    // If image size is in the global (it should be)
    if ( isset( $_wp_additional_image_sizes[ $image_size ] ) ) {
        $registered_image = $_wp_additional_image_sizes[ $image_size ];
        $attributes['data-aspect-width']  = $registered_image['width'];
        $attributes['data-aspect-height'] = $registered_image['height'];
    }
    // Otherwise use the actual image dimensions
    else {
        $attributes['data-aspect-width']  = $image[1];
        $attributes['data-aspect-height'] = $image[2];
    }
    return $attributes;
}
