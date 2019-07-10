<?php

/**
 * Add featured image to single posts.
 *
 * @see     mai_woo_remove_featured_image() for Woo version.
 *
 * @return  void
 */
add_action( 'genesis_before_entry', 'mai_do_entry_featured_image' );
function mai_do_entry_featured_image() {

	// Bail if not a single entry with a featured image.
	if ( ! ( is_singular() && has_post_thumbnail() ) ) {
		return;
	}

	// Check if auto-displaying the featured image.
	$key     = sprintf( 'singular_image_%s', get_post_type() );
	$display = genesis_get_option( $key );

	// Bail if not displaying.
	if ( ! $display ) {
		return;
	}

	// Bail if hide featured image is checked.
	if ( get_post_meta( get_the_ID(), 'mai_hide_featured_image', true ) ) {
		return;
	}

	mai_do_featured_image();
}

/**
 * Maybe remove the WooCommerce single product image.
 *
 * @see     mai_do_entry_featured_image() for similar standard CPT.
 *
 * @return  void
 */
add_action( 'woocommerce_before_main_content', 'mai_woo_remove_featured_image' );
function mai_woo_remove_featured_image() {

	// Bail if not a single product.
	if ( ! is_product() ) {
		return;
	}

	// Get post types to display featured image on.
	$key     = sprintf( 'singular_image_%s', get_post_type() );
	$display = genesis_get_option( $key );

	// Bail if hide featured image is checked.
	if ( get_post_meta( get_the_ID(), 'mai_hide_featured_image', true ) ) {
		$display = false;
	}

	// Remove image if not displaying.
	if ( ! $display ) {
		// Remove product images from single template.
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
	}
}

/**
 * Remove the entry meta on single posts.
 *
 * @return  void
 */
add_action( 'genesis_before_loop', 'mai_remove_singular_meta' );
function mai_remove_singular_meta() {

	// Bail if not a single post_type.
	if ( ! is_singular() || is_page() ) {
		return;
	}

	// Get the meta to remove.
	$post_type       = get_post_type();
	$remove_meta_key = sprintf( 'remove_meta_%s', $post_type );
	$meta_to_remove  = (array) genesis_get_option( $remove_meta_key );

	// Bail if not removing any meta.
	if ( ! $meta_to_remove ) {
		return;
	}

	if ( in_array( 'post_info', $meta_to_remove ) ) {
		// Remove the entry meta in the entry header.
		remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
	}

	if ( in_array( 'post_meta', $meta_to_remove ) ) {
		// Remove the entry footer markup.
		remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
		remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );
		// Remove the entry meta in the entry footer.
		remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
	}
}

/**
 * Add has-avatars class to entry comments wrap.
 *
 * @since   1.11.0
 *
 * @param   $attributes  The existing attributes.
 *
 * @return  $attributes  The modifed attributes.
 */
add_filter( 'genesis_attr_entry-comments', 'mai_entry_comments_attributes' );
function mai_entry_comments_attributes( $attributes ) {
	// Bail if not showing avatars.
	if ( ! get_option( 'show_avatars' ) ) {
		return $attributes;
	}
	$attributes['class'] .= ' has-avatars';
	return $attributes;
}

/**
 * Add slow scroll class to comments link.
 *
 * @return  string|HTML  The modified output.
 */
add_filter( 'genesis_post_comments_shortcode', 'mai_slow_scroll_comments_link', 10, 2 );
function mai_slow_scroll_comments_link( $output, $atts ) {
	if ( ! is_singular() ) {
		return $output;
	}
	$output = str_replace( 'a href', 'a class="scroll-to" href', $output );
	$output = str_replace( trailingslashit( get_permalink() ), '', $output );
	return $output;
}
