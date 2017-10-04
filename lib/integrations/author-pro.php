<?php

add_action( 'genesis_before_loop', function(){
	// d( get_option( GENESIS_CPT_ARCHIVE_SETTINGS_FIELD_PREFIX . 'books' ) );
	// delete_option( GENESIS_CPT_ARCHIVE_SETTINGS_FIELD_PREFIX . 'books' );
});

//
if ( function_exists( 'gapro_autoloader' ) ) {
	remove_action( 'after_setup_theme', array( 'Genesis_Author_Pro_CPT', 'init' ), 1 );
	add_action( 'init', array( 'Genesis_Author_Pro_CPT', 'init' ) );
}


add_action( 'genesis_before_loop', function() {
	if ( ! class_exists( 'Genesis_Author_Pro' ) ) {
		return;
	}
	if ( ! ( is_singular( 'books' ) && mai_is_banner_area_enabled() ) ) {
		return;
	}

	add_filter( 'genesis_entry_title_wrap', 'mai_author_pro_entry_title_wrap' );
	function mai_author_pro_entry_title_wrap( $wrap ) {
		return 'h2';
	}
});

/**
 * Add custom body class.
 *
 * @param   array  The existing body classes.
 *
 * @return  array  Modified classes.
 */
add_filter( 'body_class', 'mai_genesis_author_pro_add_body_class', 99 );
function mai_genesis_author_pro_add_body_class( $classes ) {
	if ( ! class_exists( 'Genesis_Author_Pro' ) ) {
		return $classes;
	}
	if ( ! ( is_post_type_archive( 'books' ) || is_tax( array( 'book-authors', 'book-series', 'book-tags' ) ) || is_singular( 'books' ) ) ) {
		return $classes;
	}
	// Remove default class.
	if ( ( $key = array_search( 'genesis-author-pro', $classes ) ) !== false ) {
		unset( $classes[$key] );
	}
	// Add mai class.
	$classes[] = 'mai-author-pro';
	return $classes;
}

add_filter( 'mai_cpt_settings', 'mai_genesis_author_pro_books_default_settings', 10, 2 );
function mai_genesis_author_pro_books_default_settings( $settings, $post_type ) {
	// Bail if CPT is not Genesis Author Pro  'books'.
	if ( ! ( class_exists( 'Genesis_Author_Pro') && ( 'books' === $post_type ) ) ) {
		return $settings;
	}
	$settings['remove_meta_product']       = false;
	$settings['content_archive']           = false;
	$settings['content_archive_limit']     = false;
	$settings['content_archive_thumbnail'] = false;
	$settings['image_location']            = false;
	$settings['image_size']                = false;
	$settings['image_alignment']           = false;
	$settings['layout']                    = false;
	// $settings['layout_books']              = false;
	$settings['more_link']                 = false;
	$settings['more_link_text']            = false;
	$settings['remove_meta']               = false;
	$settings['singular_image_books']      = false;
	return $settings;
}

/**
 * Set some cpt-archive-settings defaults for WooCommerce Shop/Products.
 *
 * @param  array   $settings   The default settings (already modified by Mai).
 * @param  string  $post_type  The post type name.
 *
 * @param  array   The modified settings.
 */
add_filter( 'genesis_cpt_archive_settings_defaults', 'mai_genesis_author_pro_books_cpt_archive_settings', 10, 2 );
function mai_genesis_author_pro_books_cpt_archive_settings( $settings, $post_type ) {
	if ( ! class_exists( 'Genesis_Author_Pro') && 'books' !== $post_type ) {
		return $settings;
	}
	$settings['enable_content_archive_settings'] = 1;
	$settings['columns']                         = 4;
	$settings['content_archive_thumbnail']       = 0;  // Doesn't seem to be working.
	$settings['image_location']                  = ''; // This disabled the archive thumbnail from displaying.
	$settings['posts_per_page']                  = 12;
	return $settings;
}
