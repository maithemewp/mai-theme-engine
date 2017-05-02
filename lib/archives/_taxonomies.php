<?php

/**
 * Set default WooCommerce layouts.
 * We need to hook in later to make give
 * a chance for template to exist.
 *
 * WooCommerce defaults in /lib/integrations/woocommerce.php
 *
 * @return  void
 */
// add_action( 'genesis_meta', 'mai_taxonomy_default_layouts' );
function mai_taxonomy_default_layouts() {
    // Run filter
    add_filter( 'genesis_pre_get_option_site_layout', 'mai_taxonomy_default_layout' );
}
/**
 * genesis_site_layout() only calls genesis_get_option( 'site_layout' )
 * if a specific layout isn't chosen. So, it calls this for the default.
 *
 * @return  The site layout
 */
function mai_taxonomy_default_layout( $layout ) {

    // Bail if not category, tag, or custom taxo archive
    if ( ! ( is_category() || is_tag() || is_tax() ) ) {
        return $layout;
    }

    // Bail if we have no layout or non-default layout is already chosen
    if ( ! empty( $layout ) && $layout != genesis_get_default_layout() ) {
        return $layout;
    }

    // If post taxonomy
    if ( is_category() || is_tag() || is_tax( get_object_taxonomies( 'post', 'names' ) ) ) {
        $term_layout = genesis_get_custom_field( '_genesis_layout', get_option( 'page_for_posts' ) );
    }
    // If Woo product taxonomy
    elseif ( is_tax( get_object_taxonomies( 'product', 'names' ) ) ) {
        $term_layout = genesis_get_custom_field( '_genesis_layout', get_option( 'woocommerce_shop_page_id' ) );
    }
    // Must be custom taxonomy archive
    else {
        global $wp_taxonomies;
        $tax = get_queried_object()->taxonomy;
        /**
         * If we have a tax, get the first one.
         * Changed to reset() when hit an error on a term archive that object_type array didn't start with [0]
         */
        $post_type  = isset( $wp_taxonomies[$tax] ) ? reset($wp_taxonomies[$tax]->object_type) : '';
        // If we have a post type and it supports genesis-cpt-archive-settings
        if ( $post_type && genesis_has_post_type_archive_support( $post_type ) ) {
            $term_layout = genesis_get_cpt_option( 'layout', $post_type );
        }
    }

    // If no term layout, return what we started with
    if ( empty( $term_layout ) ) {
        return $layout;
    }

    // Return the new layout!
    return $term_layout;

}

