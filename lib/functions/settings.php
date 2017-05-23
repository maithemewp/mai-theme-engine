<?php

/**
 * Get an archive setting value with fallback.
 *
 * @param  [type] $key      [description]
 * @param  [type] $fallback [description]
 * @return [type]           [description]
 */
function mai_get_archive_setting( $key, $fallback ) {

    // Bail if not a content archive
    if ( ! mai_is_content_archive() ) {
        return false;
    }

    // Allow child theme to short circuit this function.
    $pre = apply_filters( "mai_pre_get_archive_setting_{$key}", null );
    if ( null !== $pre ) {
        return $pre;
    }

    // Static blog page
    if ( is_home() && ( $posts_page_id = get_option( 'page_for_posts' ) ) ) {
        $enabled = get_post_meta( $posts_page_id, 'enable_content_archive_settings', true );
        if ( 'on' == $enabled ) {
            $meta = get_post_meta( $posts_page_id, $key, true );
        }
    }
    // Term archive
    elseif ( is_category() || is_tag() || is_tax() ) {
        $enabled = get_term_meta( get_queried_object()->term_id, 'enable_content_archive_settings', true );
        if ( 'on' == $enabled ) {
            $meta = get_term_meta( get_queried_object()->term_id, $key, true );
        } else {
            // If post taxonomy
            if ( is_category() || is_tag() || is_tax( get_object_taxonomies( 'post', 'names' ) ) ) {
                if ( $posts_page_id = get_option( 'page_for_posts' ) ) {
                    $enabled = get_post_meta( $posts_page_id, 'enable_content_archive_settings', true );
                    if ( 'on' == $enabled ) {
                        $meta = get_post_meta( $posts_page_id, $key, true );
                    }
                }
            }
            // If Woo product taxonomy
            elseif ( is_tax( get_object_taxonomies( 'product', 'names' ) ) ) {
                if ( $shop_page_id = get_option( 'woocommerce_shop_page_id' ) ) {
                    $enabled = get_post_meta( $shop_page_id, 'enable_content_archive_settings', true );
                    if ( 'on' == $enabled ) {
                        $meta = get_post_meta( $shop_page_id, $key, true );
                    }
                }
            }
            // Must be custom taxonomy archive
            else {
                $tax = get_taxonomy( get_queried_object()->taxonomy );
                if ( $tax ) {
                    /**
                     * If the taxonomy is only registered to 1 post type.
                     * Otherwise, how will we pick which post type archive to fall back to?
                     * If more than one, we'll just have to use the fallback.
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
    // CPT archive
    elseif ( is_post_type_archive() && genesis_has_post_type_archive_support() ) {
        $enabled = genesis_get_cpt_option( 'enable_content_archive_settings' );
        if ( 'on' == $enabled ) {
            $meta = genesis_get_cpt_option( $key );
        }
    }
    // Author archive
    elseif ( is_author() ) {
        $enabled = get_the_author_meta( 'enable_content_archive_settings', get_query_var( 'author' ) );
        if ( 'on' == $enabled ) {
            $meta = get_the_author_meta( $key, get_query_var( 'author' ) );
        }
    }
    // WooCommerce shop page
    elseif ( class_exists( 'WooCommerce' ) && is_shop() && ( $shop_page_id = get_option( 'woocommerce_shop_page_id' ) ) ) {
        $enabled = get_post_meta( $shop_page_id, 'enable_content_archive_settings', true );
        if ( 'on' == $enabled ) {
            $meta = get_post_meta( $shop_page_id, $key, true );
        }
    }

    // If we have meta, return it
    if ( isset( $meta ) ) {
        return $meta;
    }
    // If we hav a fallback, return it
    elseif ( $fallback ) {
        return $fallback;
    }
    // Return false
    return false;
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


function mai_is_banner_area_enabled_globally() {
    return filter_var( get_theme_mod( 'enable_banner_area', 1 ), FILTER_VALIDATE_BOOLEAN );
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

    // Bail if not enabled at all
    if ( ! mai_is_banner_area_enabled_globally() ) {
        $enabled = false;
    } else {

        // Get 'disabled' content, typecasted as array because it may return empty string if none
        $disable_post_types = (array) genesis_get_option( 'banner_disable_post_types' );

        if ( is_singular() || is_post_type_archive() ) {
            if ( in_array( get_post_type(), $disable_post_types ) ) {
                $enabled = false;
            }
        } elseif ( is_tax() ) {
            if ( array_intersect( get_taxonomy( get_queried_object()->taxonomy )->object_type, $disable_post_types ) ) {
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
 * @since 1.0.0
 *
 * @return string Hex color code for accent color.
 */
function mai_customizer_get_default_accent_color() {
    return '#067CCC';
}

/**
 * Generate a hex value that has appropriate contrast
 * against the inputted value.
 *
 * @since 1.0.0
 *
 * @return string Hex color code for contrasting color.
 */
function mai_color_contrast( $color ) {

    $hexcolor = str_replace( '#', '', $color );
    $red      = hexdec( substr( $hexcolor, 0, 2 ) );
    $green    = hexdec( substr( $hexcolor, 2, 2 ) );
    $blue     = hexdec( substr( $hexcolor, 4, 2 ) );

    $luminosity = ( ( $red * 0.2126 ) + ( $green * 0.7152 ) + ( $blue * 0.0722 ) );

    return ( $luminosity > 128 ) ? '#000000' : '#ffffff';
}
