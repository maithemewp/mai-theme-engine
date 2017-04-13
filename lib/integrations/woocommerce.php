<?php

// Enqueue and Dequeue WooCommerce styles
add_action( 'wp_enqueue_scripts', 'mai_enqueue_woocommerce_scripts' );
function mai_enqueue_woocommerce_scripts() {

	// Bail if WooCommerce is not active
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	// Use minified files if script debug is not being used
	$suffix = mai_get_suffix();

	// Register woocommerce script for later enqueuing
	wp_register_style( 'mai-woocommerce', get_stylesheet_directory_uri() . "/assets/css/woo{$suffix}.css", array(), CHILD_THEME_VERSION );

	// Bail if account, cart, or checkout pages. We need layout stuff here
	if ( is_account_page() || is_cart() || is_checkout() ) {
		return;
	}

	/**
	 * Remove Woo layout script
	 * @link https://gregrickaby.com/remove-woocommerce-styles-and-scripts/
	 */
	wp_dequeue_style( 'woocommerce-layout' );
}

// Enqueue the mai-woocommerce stylesheet if a woo template is used
add_action( 'woocommerce_before_template_part', 'mai_enqueue_woocommerce_styles' );
function mai_enqueue_woocommerce_styles() {
	wp_enqueue_style( 'mai-woocommerce' );
}

/**
 * Set default WooCommerce layouts.
 * We need to hook in later to make give
 * a chance for template to exist.
 *
 * Similar code in /lib/taxonomies.php
 *
 * @return  void
 */
add_action( 'genesis_meta', 'mai_woocommerce_default_layouts' );
function mai_woocommerce_default_layouts() {
	// Bail if WooCommerce is not active
	if ( ! class_exists( 'WooCommerce' ) ) {
		return ;
	}
	// Run filter
	add_filter( 'genesis_pre_get_option_site_layout', 'mai_woocommerce_default_layout' );
}

/**
 * genesis_site_layout() only calls genesis_get_option( 'site_layout' )
 * if a specific layout isn't chosen. So, it calls this for the default.
 *
 * @return  The site layout
 */
function mai_woocommerce_default_layout( $layout ) {

	// Bail if we have no layout or non-default layout is already chosen
	if ( ! empty( $layout ) && $layout != genesis_get_default_layout() ) {
		return $layout;
	}

	if ( is_shop() ) {
		$layout = 'flex-loop-3';
	}
	elseif ( is_product() ) {
	    $layout = 'md-content';
	}
	elseif ( is_account_page() ) {
	    $layout = 'full-width-content';
	}
	elseif ( is_cart() ) {
	    $layout = 'md-content';
	}
	elseif ( is_checkout() ) {
	    $layout = 'md-content';
	}
	return $layout;

}

/**
 * Add flex classes to WooCommerce shop loop
 * when loop is set to display categories.
 *
 * @return  array  classes for the product category entry
 */
add_filter( 'product_cat_class', 'mai_do_product_cat_flex_entry_classes' );
function mai_do_product_cat_flex_entry_classes( $classes ) {

    $layout = genesis_site_layout();

    // Bail if not a flex loop
    if ( ! mai_is_flex_loop_layout( $layout ) ) {
        return $classes;
    }

    // Get our classes by layout
    $classes[] = mai_get_flex_entry_classes_by( 'layout', $layout );

    return $classes;
}

/**
 * Add flex classes to a WooCommerce secondary loop.
 *
 * @uses    mai_do_flex_entry_classes_by_layout
 *
 * @return  void
 */
add_action( 'woocommerce_before_template_part', 'mai_do_woo_flex_loop', 10, 4 );
function mai_do_woo_flex_loop( $template_name, $template_path, $located, $args ) {


    // Bail if not the loop start template
    if ( 'loop/loop-start.php' != $template_name ) {
    	return;
    }

    // Filter and add our flex classes
	mai_do_flex_entry_classes_by_layout( genesis_site_layout() );
}

/**
 * Remove the post_class filter fired before template part.
 *
 * @return  void
 */
add_action( 'woocommerce_after_template_part', 'mai_woo_flex_loop_end', 10, 4 );
function mai_woo_flex_loop_end( $template_name, $template_path, $located, $args ) {

	// Bail if not the loop end template
    if ( 'loop/loop-end.php' != $template_name ) {
    	return;
    }

    // Remove the output of the post classes, so it doesn't affect things we don't want.
    remove_action( 'woocommerce_before_template_part', 'mai_do_woo_flex_loop', 10, 4 );
}

/**
 * Alter the post classes for the related products loop.
 *
 * @return  void
 */
add_action( 'woocommerce_after_single_product_summary', 'mai_before_woocommerce_output_related_products', 19 );
function mai_before_woocommerce_output_related_products() {

	// Remove the default flex loop classes, since they are based on layout
	remove_action( 'woocommerce_before_template_part', 'mai_do_woo_flex_loop', 10, 4 );

	// Set column count with a filter so devs can change the default column count
	$columns = apply_filters( 'mai_woo_related_products_columns', 3 );

	// Filter and add our flex classes
	mai_do_flex_entry_classes_by_columns( $columns );
}

/**
 * Revert post classes after the related products loop has fired.
 *
 * @return  void
 */
add_action( 'woocommerce_after_single_product_summary', 'mai_after_woocommerce_output_related_products', 21 );
function mai_after_woocommerce_output_related_products() {

	// Remove the related products flex loop
	remove_action( 'woocommerce_after_single_product_summary', 'mai_before_woocommerce_output_related_products', 19 );

	// Add back the default flex loop classes, incase there are any other custom product loops
	add_action( 'woocommerce_before_template_part', 'mai_do_woo_flex_loop', 10, 4 );
}

/**
 * Alter the post classes for any woocommerce product loops
 * coming from a shortcode.
 *
 * @return  void
 */
add_action( 'woocommerce_shortcode_before_recent_products_loop',       'mai_do_woo_shortcode_flex_loop' );
add_action( 'woocommerce_shortcode_before_sale_products_loop',         'mai_do_woo_shortcode_flex_loop' );
add_action( 'woocommerce_shortcode_before_best_selling_products_loop', 'mai_do_woo_shortcode_flex_loop' );
add_action( 'woocommerce_shortcode_before_top_rated_products_loop',    'mai_do_woo_shortcode_flex_loop' );
add_action( 'woocommerce_shortcode_before_featured_products_loop',     'mai_do_woo_shortcode_flex_loop' );
add_action( 'woocommerce_shortcode_before_related_products_loop',      'mai_do_woo_shortcode_flex_loop' );
function mai_do_woo_shortcode_flex_loop() {
	// Get the woo global so we know can access the data
	global $woocommerce_loop;
	// Remove the default woo flex loop function
	remove_action( 'woocommerce_before_template_part', 'mai_do_woo_flex_loop', 10, 4 );
	// Alter post classes based on columns in the shortcode
	mai_do_flex_entry_classes_by_columns( $woocommerce_loop['columns'] );
}

/**
 * Remove the shortcode post_class filters
 * after the shortcode has fired
 * so any additional shortcodes get processed on their own.
 *
 * @return  void
 */
add_action( 'woocommerce_shortcode_after_recent_products_loop',       'mai_remove_woo_shortcode_flex_loop' );
add_action( 'woocommerce_shortcode_after_sale_products_loop',         'mai_remove_woo_shortcode_flex_loop' );
add_action( 'woocommerce_shortcode_after_best_selling_products_loop', 'mai_remove_woo_shortcode_flex_loop' );
add_action( 'woocommerce_shortcode_after_top_rated_products_loop',    'mai_remove_woo_shortcode_flex_loop' );
add_action( 'woocommerce_shortcode_after_featured_products_loop',     'mai_remove_woo_shortcode_flex_loop' );
add_action( 'woocommerce_shortcode_after_related_products_loop',      'mai_remove_woo_shortcode_flex_loop' );
function mai_remove_woo_shortcode_flex_loop() {
	remove_action( 'woocommerce_shortcode_before_recent_products_loop',       'mai_do_woo_shortcode_flex_loop' );
	remove_action( 'woocommerce_shortcode_before_sale_products_loop',         'mai_do_woo_shortcode_flex_loop' );
	remove_action( 'woocommerce_shortcode_before_best_selling_products_loop', 'mai_do_woo_shortcode_flex_loop' );
	remove_action( 'woocommerce_shortcode_before_top_rated_products_loop',    'mai_do_woo_shortcode_flex_loop' );
	remove_action( 'woocommerce_shortcode_before_featured_products_loop',     'mai_do_woo_shortcode_flex_loop' );
	remove_action( 'woocommerce_shortcode_before_related_products_loop',      'mai_do_woo_shortcode_flex_loop' );
}

// Maybe remove woocommerce page title
add_filter( 'woocommerce_show_page_title', 'mai_woocommerce_show_page_title' );
function mai_woocommerce_show_page_title( $return ) {
	if ( mai_is_banner_area_enabled() ) {
		if ( is_shop() ) {
			return false;
		}
		if ( is_product() ) {
			return false;
		}
	}
	return false;
}

// Remove taxonomy archive description since Mai has this functionality already
remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );

// Replace Woocommerce Default pagination with Genesis Framework Pagination
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
