<?php
/**
 * Mai Theme.
 *
 * WARNING: This file is part of the core Mai Theme framework.
 * The goal is to keep all files in /lib/ untouched.
 * That way we can easily update the core structure of the theme on existing sites without breaking things
 *
 * @author   Mike Hemberger
 *
 * @version  1.0.5
 */


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

function mai_do_grid( $args, $content = null ) {
    echo mai_get_grid( $args, $content );
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

    return $is_archive;
}

/**
 * This function returns an archive setting value
 * without running through any filters.
 * Do not use in child themes! Instead use mai_get_archive_setting( $key );
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
                     * If we have a tax, get the first one.
                     * Changed to reset() when hit an error on a term archive that object_type array didn't start with [0]
                     */
                    $post_type = reset( $tax->object_type );
                    // If we have a post type and it supports genesis-cpt-archive-settings
                    if ( $post_type && genesis_has_post_type_archive_support( $post_type ) ) {
                        $meta = genesis_get_cpt_option( $key, $post_type );
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

// function mai_archive_display_image() {

//     $enabled = $archive_display = $display = false;

//     // Static blog page
//     if ( is_home() && ( $posts_page_id = get_option( 'page_for_posts' ) ) ) {
//         $enabled         = get_post_meta( $posts_page_id, 'enable_content_archive_settings', true );
//         $archive_display = get_post_meta( $posts_page_id, 'content_archive_thumbnail', true );
//     }
//     // Term archive
//     elseif ( is_category() || is_tag() || is_tax() ) {
//         $enabled         = get_term_meta( get_queried_object()->term_id, 'enable_content_archive_settings', true );
//         $archive_display = get_term_meta( get_queried_object()->term_id, 'content_archive_thumbnail', true );
//     }
//     // CPT archive
//     elseif ( is_post_type_archive() && genesis_has_post_type_archive_support() ) {
//         $enabled         = genesis_get_cpt_option( 'enable_content_archive_settings' );
//         $archive_display = genesis_get_cpt_option( 'content_archive_thumbnail' );
//     }
//     // Author archive
//     elseif ( is_author() ) {
//         $enabled         = get_the_author_meta( 'enable_content_archive_settings', get_query_var( 'author' ) );
//         $archive_display = get_the_author_meta( 'content_archive_thumbnail', get_query_var( 'author' ) );
//     }
//     // WooCommerce shop page
//     elseif ( class_exists( 'WooCommerce' ) && is_shop() && ( $shop_page_id = get_option( 'woocommerce_shop_page_id' ) ) ) {
//         $enabled         = get_post_meta( $shop_page_id, 'enable_content_archive_settings', true );
//         $archive_display = get_post_meta( $shop_page_id, 'content_archive_thumbnail', true );
//     }

//     // If archive settings are enabled
//     if ( $enabled ) {
//         $display = $archive_display;
//     }

//     return $display;
// }

// function mai_archive_get_image_location() {

//     $enabled = $archive_location = $location = false;

//     // Static blog page
//     if ( is_home() && ( $posts_page_id = get_option( 'page_for_posts' ) ) ) {
//         $enabled          = get_post_meta( $posts_page_id, 'enable_content_archive_settings', true );
//         $archive_location = get_post_meta( $posts_page_id, 'image_location', true );
//     }
//     // Term archive
//     elseif ( is_category() || is_tag() || is_tax() ) {
//         $enabled          = get_term_meta( get_queried_object()->term_id, 'enable_content_archive_settings', true );
//         $archive_location = get_term_meta( get_queried_object()->term_id, 'image_location', true );
//     }
//     // CPT archive
//     elseif ( is_post_type_archive() && genesis_has_post_type_archive_support() ) {
//         $enabled          = genesis_get_cpt_option( 'enable_content_archive_settings' );
//         $archive_location = genesis_get_cpt_option( 'image_location' );
//     }
//     // Author archive
//     elseif ( is_author() ) {
//         $enabled          = get_the_author_meta( 'enable_content_archive_settings', get_query_var( 'author' ) );
//         $archive_location = get_the_author_meta( 'image_location', get_query_var( 'author' ) );
//     }
//     // WooCommerce shop page
//     elseif ( class_exists( 'WooCommerce' ) && is_shop() && ( $shop_id = get_option( 'woocommerce_shop_page_id' ) ) ) {
//         $enabled          = get_post_meta( $shop_id, 'enable_content_archive_settings', true );
//         $archive_location = get_post_meta( $shop_id, 'image_location', true );
//     }

//     // If archive settings are enabled
//     if ( $enabled ) {
//         $location = $archive_location;
//     }

//     return $location;
// }

// function mai_get_archive_image_size() {

//     // Start of without any values
//     $enabled = $archive_size = $size = false;

//     // Static blog page
//     if ( is_home() && ( $posts_page_id = get_option( 'page_for_posts' ) ) ) {
//         $enabled      = get_post_meta( $posts_page_id, 'enable_content_archive_settings', true );
//         $archive_size = get_post_meta( $posts_page_id, 'image_size', true );
//     }
//     // Term archive
//     elseif ( is_category() || is_tag() || is_tax() ) {
//         $enabled      = get_term_meta( get_queried_object()->term_id, 'enable_content_archive_settings', true );
//         $archive_size = get_term_meta( get_queried_object()->term_id, 'image_size', true );
//     }
//     // CPT archive
//     elseif ( is_post_type_archive() && genesis_has_post_type_archive_support() ) {
//         $enabled      = genesis_get_cpt_option( 'enable_content_archive_settings' );
//         $archive_size = genesis_get_cpt_option( 'banner_id' );
//     }
//     // Author archive
//     elseif ( is_author() ) {
//         $enabled      = get_the_author_meta( 'enable_content_archive_settings', get_query_var( 'author' ) );
//         $archive_size = get_the_author_meta( 'image_size', get_query_var( 'author' ) );
//     }
//     // WooCommerce shop page
//     elseif ( class_exists( 'WooCommerce' ) && is_shop() && ( $shop_id = get_option( 'woocommerce_shop_page_id' ) ) ) {
//         $enabled      = get_post_meta( $shop_id, 'enable_content_archive_settings', true );
//         $archive_size = get_post_meta( $shop_id, 'banner_id', true );
//     }

//     // If archive settings are enabled
//     if ( $enabled ) {
//         $size = $archive_size;
//     }

//     return $size;
// }

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

function mai_get_columns() {

    $columns = mai_get_archive_setting( 'columns', genesis_get_option( 'columns' ) );

    if ( class_exists( 'WooCommerce' ) && ( is_shop() || is_tax( get_object_taxonomies( 'product', 'names' ) ) ) ) {
        if ( $columns <= 1 ) {
            $columns = 3;
        }
    }

    return absint( apply_filters( 'mai_columns', $columns ) );
}

/**
 * Helper function to check if archive is a flex loop
 * This doesn't check if viewing an actual archive, but this layout should not be an option if ! is_archive()
 *
 * @return bool   Whether the layout is a grid archive
 */
function mai_is_flex_loop() {
    // Bail if not a content archive
    if ( ! mai_is_content_archive() ) {
        return;
    }
    // Get columns
    $columns = mai_get_columns();
    // If we have more than 1 column, it's a flex loop
    if ( $columns > 1 ) {
        return true;
    }
    // Not a flex loop
    return false;
}

/**
 * Filter post_class to add flex classes by option.
 *
 * @param   string      $option  'layout' or 'columns'
 * @param   string|int  $value   layout name or number of columns
 *
 * @return  void        fires post_class filter which returns array of classes
 */
function mai_do_flex_entry_classes_by( $option, $value ) {
    if ( 'columns' == $option ) {
        mai_do_flex_entry_classes_by_columns( $value );
    }
}

/**
 * Filter post_class to add flex classes by layout.
 *
 * @param  string  $layout  the page layout
 *
 * @return  void        fires post_class filter which returns array of classes
 */
// function mai_do_flex_entry_classes_by_layout( $layout ) {
//     add_filter( 'post_class', function( $classes ) use ( $layout ) {
//         $classes[] = mai_get_flex_entry_classes_by_layout( $layout );
//         return $classes;
//     });
// }

/**
 * Filter post_class to add flex classes by number of columns.
 *
 * @param  string  $columns  number of columns to get classes for
 *
 * @return  void        fires post_class filter which returns array of classes
 */
function mai_do_flex_entry_classes_by_columns( $columns ) {
    add_filter( 'post_class', function( $classes ) use ( $columns ) {
        $classes[] = mai_get_flex_entry_classes_by_columns( $columns );
        return $classes;
    });
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
 * Get the classes needed for an entry
 * depending on the layout
 *
 * @param  string  $layout  the page layout
 *
 * @return string  the classes
 */
// function mai_get_flex_entry_classes_by_layout( $layout ) {
//     switch ( $layout ) {
//         case 'flex-loop-4':
//         case 'flex-loop-4md':
//         case 'flex-loop-4sm':
//             $classes = 'flex-entry column col col-xs-12 col-sm-6 col-md-3';
//             break;
//         case 'flex-loop-4-content-sidebar':
//         case 'flex-loop-4-sidebar-content':
//             $classes = 'flex-entry column col col-xs-12 col-sm-3';
//             break;
//         case 'flex-loop-3':
//         case 'flex-loop-3md':
//         case 'flex-loop-3sm':
//             $classes = 'flex-entry column col col-xs-12 col-sm-6 col-md-4';
//             break;
//         case 'flex-loop-3-content-sidebar':
//         case 'flex-loop-3-sidebar-content':
//             $classes = 'flex-entry column col col-xs-12 col-sm-4';
//             break;
//         case 'flex-loop-2':
//         case 'flex-loop-2md':
//         case 'flex-loop-2sm':
//         case 'flex-loop-2-content-sidebar':
//         case 'flex-loop-2-sidebar-content':
//             $classes = 'flex-entry column col col-xs-12 col-sm-6';
//             break;
//         default:
//             $classes = 'flex-entry column col col-xs-12';
//     }
//     return $classes;
// }

/**
 * Get the classes needed for an entry from number of columns.
 *
 * @param  string  $columns  number of columns to get classes for
 *
 * @return string  the classes
 */
function mai_get_flex_entry_classes_by_columns( $columns ) {
    switch ( (int)$columns ) {
        case 1:
            $classes = 'flex-entry column col col-xs-12';
            break;
        case 2:
            $classes = 'flex-entry column col col-xs-12 col-sm-6';
            break;
        case 3:
            $classes = 'flex-entry column col col-xs-12 col-sm-6 col-md-4';
            break;
        case 4:
            $classes = 'flex-entry column col col-xs-12 col-sm-6 col-md-3';
            break;
        case 6:
            $classes = 'flex-entry column col col-xs-6 col-sm-4 col-md-2';
            break;
        default:
            $classes = 'flex-entry column col col-xs-12 col-sm-6 col-md-4';
    }
    return $classes;
}

function mai_get_flex_entry_classes_by_fraction( $fraction ) {
    switch ( $fraction ) {
        case 'col':
            $classes = 'flex-entry column col col-xs-12 col-sm-6 col-md';
            break;
        case 'col-auto':
            $classes = 'flex-entry column col col-xs-12 col-sm-auto';
            break;
        case 'one-twelfth':
            $classes = 'flex-entry column col col-xs-3 col-sm-2 col-md-1';
            break;
        case 'one-sixth':
            $classes = 'flex-entry column col col-xs-4 col-sm-2';
            break;
        case 'one-fourth':
            $classes = 'flex-entry column col col-xs-12 col-sm-6 col-md-3';
            break;
        case 'one-third':
            $classes = 'flex-entry column col col-xs-12 col-sm-6 col-md-4';
            break;
        case 'five-twelfths':
            $classes = 'flex-entry column col col-xs-12 col-sm-5';
            break;
        case 'one-half':
            $classes = 'flex-entry column col col-xs-12 col-sm-6';
            break;
        case 'seven-twelfths':
            $classes = 'flex-entry column col col-xs-12 col-sm-7';
            break;
        case 'two-thirds':
            $classes = 'flex-entry column col col-xs-12 col-sm-8';
            break;
        case 'three-fourths':
            $classes = 'flex-entry column col col-xs-12 col-sm-9';
            break;
        case 'five-sixths':
            $classes = 'flex-entry column col col-xs-12 col-sm-10';
            break;
        case 'eleven-twelfths':
            $classes = 'flex-entry column col col-xs-12 col-sm-11';
            break;
        case 'one-whole':
            $classes = 'flex-entry column col col-xs-12';
            break;
        default:
            $classes = 'flex-entry column col col-xs-12 col-sm';
    }
    return $classes;
}

/**
 * Get flex entry classes by
 *
 * @param   string      $option  'layout' or 'columns'
 * @param   string|int  $value   layout name or number of columns
 *
 * @return  string               comma separated string of classes
 */
function mai_get_flex_entry_image_size_by( $option, $value ) {
    $image_size = '';
    if ( 'columns' == $option ) {
        $image_size = mai_get_flex_entry_image_size_by_columns( $value );
    }
    return $image_size;
}

/**
 * Get the image_size needed for an entry
 * from number of columns.
 *
 * Typically it's better to get image size by layout, because this function
 * doesn't account for sidebar layout.
 *
 * @param  string  $columns  number of columns to get image_size for
 *
 * @return string  the image_size
 */
function mai_get_flex_entry_image_size_by_columns( $columns ) {
    switch ( (int)$columns ) {
        case 1:
            $image_size = 'featured';
            break;
        case 2:
            $image_size = 'one-half';
            break;
        case 3:
            $image_size = 'one-third';
            break;
        case 4:
        case 6:
            $image_size = 'one-fourth';
            break;
        default:
            $image_size = 'one-third';
    }
    return $image_size;
}

function mai_add_aspect_ratio_attributes( $attributes, $image_id, $image_size ) {
    // Get all registered image sizes
    global $_wp_additional_image_sizes;

    // Get the image
    $image = wp_get_attachment_image_src( $image_id, $image_size, true );

    // Bail if no image
    if ( ! $image ) {
        return $attributes;
    }

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

/**
 * Helper function to get a read more link for a post or term
 *
 * @param  int|WP_Post|WP_term?  $object
 * @param  string                $text
 *
 * @return HTML string for the link
 */
function mai_get_read_more_link( $object = '', $text = '' ) {
    $link = '';
    // Maybe get a post object
    $post = $object ? get_post($object) : get_post( get_the_ID() );
    // If we have a post
    if ( $post ) {
        $link = get_permalink( $post->ID );
    }
    // No post, try a term
    else {
        $term = get_term( $object );
        if ( ! is_wp_error( $term ) ) {
            $link = get_term_link( $term );
        }
    }
    // Bail if no link
    if ( empty( $link ) ) {
        return;
    }
    $text = $text ? sanitize_text_field($text) : __( 'Read More', 'maitheme' );
    return sprintf( '<p class="more-link-wrap"><a class="more-link" href="%s">%s</a></p>', $link, sanitize_text_field( apply_filters( 'mai_more_link_text', $text ) ) );
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
    return array_map( 'sanitize_key', array_map( 'trim', array_filter($keys) ) );
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
    
    // Bail if not enabled at all
    if ( ! mai_is_banner_area_enabled_globally() ) {
        return false;
    }

    // Get 'disabled' content, typecasted as array because it may return empty string if none
    $disable_post_types = (array) genesis_get_option( 'banner_disable_post_types' );
    $disable_taxonomies = (array) genesis_get_option( 'banner_disable_taxonomies' );

    if ( is_singular() || is_post_type_archive() ) {
        if ( in_array( get_post_type(), $disable_post_types ) ) {
            return false;
        }
    } elseif ( is_tax() ) {
        if ( in_array( get_queried_object()->slug, $disable_taxonomies ) ) {
            return false;
        }        
    }

    return true;
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
    if ( mai_is_sticky_header_enabled() || 'side' != get_theme_mod( 'mobile_menu_style' ) ) {
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
