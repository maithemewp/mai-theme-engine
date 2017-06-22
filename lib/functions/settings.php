<?php

/**
 * Get an archive setting value with fallback.
 *
 * @param  [type] $key      [description]
 * @param  [type] $fallback [description]
 * @return [type]           [description]
 */
function mai_get_archive_setting( $key, $fallback = false ) {

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
        if ( $enabled ) {
            $meta = get_post_meta( $posts_page_id, $key, true );
        }
    }
    // Term archive
    elseif ( is_category() || is_tag() || is_tax() ) {
        $enabled = isset( get_queried_object()->term_id ) ? get_term_meta( get_queried_object()->term_id, 'enable_content_archive_settings', true ) : false;
        if ( $enabled ) {
            $meta = get_term_meta( get_queried_object()->term_id, $key, true );
        } else {
            // If post taxonomy
            if ( is_category() || is_tag() || is_tax( get_object_taxonomies( 'post', 'names' ) ) ) {
                if ( $posts_page_id = get_option( 'page_for_posts' ) ) {
                    $enabled = get_post_meta( $posts_page_id, 'enable_content_archive_settings', true );
                    if ( $enabled ) {
                        $meta = get_post_meta( $posts_page_id, $key, true );
                    }
                }
            }
            // If Woo product taxonomy
            elseif ( is_tax( get_object_taxonomies( 'product', 'names' ) ) ) {
                if ( $shop_page_id = get_option( 'woocommerce_shop_page_id' ) ) {
                    $enabled = get_post_meta( $shop_page_id, 'enable_content_archive_settings', true );
                    if ( $enabled ) {
                        $meta = get_post_meta( $shop_page_id, $key, true );
                    }
                }
            }
            // Must be custom taxonomy archive
            else {
                $tax = isset( get_queried_object()->taxonomy ) ? get_taxonomy( get_queried_object()->taxonomy ) : false;
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
        if ( $enabled ) {
            $meta = genesis_get_cpt_option( $key );
        }
    }
    // Author archive
    elseif ( is_author() ) {
        $enabled = get_the_author_meta( 'enable_content_archive_settings', get_query_var( 'author' ) );
        if ( $enabled ) {
            $meta = get_the_author_meta( $key, get_query_var( 'author' ) );
        }
    }
    // WooCommerce shop page
    elseif ( class_exists( 'WooCommerce' ) && is_shop() && ( $shop_page_id = get_option( 'woocommerce_shop_page_id' ) ) ) {
        $enabled = get_post_meta( $shop_page_id, 'enable_content_archive_settings', true );
        if ( $enabled ) {
            $meta = get_post_meta( $shop_page_id, $key, true );
        }
    }

    // If we have meta, return it
    if ( isset( $meta ) ) {
        return $meta;
    }
    // If we have a fallback, return it
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

    // If not enabled at all
    if ( ! mai_is_banner_area_enabled_globally() ) {
        $enabled = false;
    } else {

        // Get 'disabled' content, typecasted as array because it may return empty string if none
        $disable_post_types = (array) genesis_get_option( 'banner_disable_post_types' );

        /**
         * If disabled via theme settings.
         * (by post_type)
         */

        if ( is_singular() || is_post_type_archive() ) {
            if ( in_array( get_post_type(), $disable_post_types ) ) {
                $enabled = false;
            }
        } elseif ( is_tax() ) {
            if ( array_intersect( get_taxonomy( get_queried_object()->taxonomy )->object_type, $disable_post_types ) ) {
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

            // Static blog page
            if ( is_home() && ( $posts_page_id = get_option( 'page_for_posts' ) ) ) {
                $hidden = get_post_meta( $posts_page_id, 'hide_banner', true );
            }
            // Single posts/pages/cpts
            elseif ( is_singular() ) {
                $hidden = get_post_meta( get_the_ID(), 'hide_banner', true );
            }
            // Term archive
            elseif ( is_category() || is_tag() || is_tax() ) {
                $term_id = isset( get_queried_object()->term_id ) ? get_queried_object()->term_id : false;
                if ( $term_id ) {
                    $hidden = get_term_meta( $term_id, 'hide_banner', true );
                }
            }
            // CPT archive
            elseif ( is_post_type_archive() && genesis_has_post_type_archive_support() ) {
                $hidden = genesis_get_cpt_option( 'hide_banner' );
            }
            // Author archive
            elseif ( is_author() ) {
                $hidden = get_the_author_meta( 'hide_banner', get_query_var( 'author' ) );
            }
            // WooCommerce shop page
            elseif ( class_exists( 'WooCommerce' ) && is_shop() && ( $shop_page_id = get_option( 'woocommerce_shop_page_id' ) ) ) {
                $hidden = get_post_meta( $shop_page_id, 'hide_banner', true );
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
    } else {
        return 'light-content';
    }
}
