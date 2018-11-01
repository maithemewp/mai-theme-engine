<?php

/**
 * Add an action hook if hide breadcrumbs settings is checked.
 * This allows custom themes with breadcrumbs in a different location to remove them via this hook.
 *
 * @since   1.3.0
 *
 * @return  void
 */
add_action( 'genesis_before', 'mai_do_remove_breadcrumbs' );
function mai_do_remove_breadcrumbs() {

	// Bail if blog or single post.
	if ( ! ( is_front_page() || is_home() || is_singular() ) ) {
		return;
	}

	/**
	 * Start with an object variable,
	 * cause we may add this setting to archives, terms, etc.
	 */
	$object = false;

	// Static blog.
	if ( is_home() && $posts_page_id = get_option( 'page_for_posts' ) ) {
		$object = get_post( $posts_page_id );
	}

	// Singular.
	elseif ( is_singular() && ! is_home() ) {
		global $post;
		$object = $post;
	}

	// Bail if no object.
	if ( ! $object ) {
		return;
	}

	$hide = get_post_meta( $object->ID, 'mai_hide_breadcrumbs', true );

	// Bail if breadcrumbs is not checked.
	if ( ! (bool) $hide ) {
		return;
	}

	do_action( 'mai_hide_breadcrumbs', $object );
}

/**
 * Add an action hook if hide breadcrumbs settings is checked.
 * This allows custom themes with breadcrumbs in a different location to remove them via this hook.
 *
 * @since   1.3.0
 *
 * @return  void
 */
add_action( 'genesis_before', 'mai_do_remove_title' );
function mai_do_remove_title() {

	// Bail if not front page, blog, or single post.
	if ( ! ( is_front_page() || is_home() || is_singular() ) ) {
		return;
	}

	/**
	 * Start with an object variable,
	 * cause we may add this setting to archives, terms, etc.
	 */
	$object = false;

	// Static front page.
	if ( is_front_page() && $front_page_id = get_option( 'page_on_front' ) ) {
		$object = get_post( $front_page_id );
	}

	// Static blog.
	elseif ( is_home() && $posts_page_id = get_option( 'page_for_posts' ) ) {
		$object = get_post( $posts_page_id );
	}

	// Singular.
	elseif ( is_singular() && ! is_home() ) {
		global $post;
		$object = $post;
	}

	// Bail if no object.
	if ( ! $object ) {
		return;
	}

	$hide = get_post_meta( $object->ID, 'be_title_toggle_hide', true );

	// Bail if title is not checked.
	if ( ! (bool) $hide ) {
		return;
	}

	do_action( 'mai_hide_title', $object );
}

/**
 * Run the hide breadcrumbs hook/function.
 *
 * @since   1.3.0
 *
 * @return  void
 */
add_action( 'mai_hide_breadcrumbs', 'mai_hide_breadcrumbs' );
function mai_hide_breadcrumbs() {
	remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
	remove_action( 'genesis_before_content_sidebar_wrap', 'genesis_do_breadcrumbs', 12 );
}

/**
 * Hide the page title.
 * Integrates with Genesis Title Toggle.
 *
 * @since   1.3.0
 *
 * @return  void
 */
add_action( 'mai_hide_title',         'mai_hide_title' );
add_action( 'be_title_toggle_remove', 'mai_hide_title' );
function mai_hide_title() {
	// Remove banner title.
	remove_action( 'mai_banner_title_description', 'mai_do_banner_title', 10, 2 );
	// Remove single post title.
	if ( is_singular() ) {
		remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
	}
	// Remove blog title.
	if ( is_home() ) {
		remove_action( 'genesis_archive_title_descriptions', 'genesis_do_archive_headings_headline', 10, 3 );
		remove_action( 'genesis_archive_title_descriptions', 'genesis_do_archive_headings_open', 5, 3 );
		remove_action( 'genesis_archive_title_descriptions', 'genesis_do_archive_headings_close', 15, 3 );
	}
}




// Remove the page title from the front page
add_action( 'genesis_before_content_sidebar_wrap', 'mai_remove_front_page_post_title' );
function mai_remove_front_page_post_title() {
	// Bail if not front page
	if ( ! is_front_page() ) {
		return;
	}
	/**
	 * Bail if home.
	 * This would happen if front page is set to recent posts,
	 * not a static page.
	 */
	if ( is_home() ) {
		return;
	}
	// Remove post title
	remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
}

// Unregister header right widget area
unregister_sidebar( 'header-right' );

// This action may cause duplicate or empty header-widget-area markup, so remove it.
remove_all_actions( 'genesis_header_right' );

// Remove Blog & Archive Template From Genesis
add_filter( 'theme_page_templates', 'mai_remove_page_templates' );
function mai_remove_page_templates( $templates ) {
	unset( $templates['page_blog.php'] );
	unset( $templates['page_archive.php'] );
	return $templates;
}

// Remove edit post link
add_filter ( 'genesis_edit_post_link', '__return_false' );

// Remove author 'says' text.
add_filter( 'comment_author_says_text', '__return_empty_string' );

// Disable the Genesis Favicon
remove_action( 'wp_head', 'genesis_load_favicon' );

/**
 * Remove default Superfish arguments
 * They are now added in global.js
 *
 * @author Gary Jones
 * @link   http://code.garyjones.co.uk/change-superfish-arguments
 *
 * @param string $url Existing URL.
 *
 * @return string Amended URL.
 */
add_filter( 'genesis_superfish_args_url', '__return_false' );

/**
 * Remove custom title/logo and blog page template metaboxes from Genesis theme options page
 *
 * @link http://www.billerickson.net/code/remove-metaboxes-from-genesis-theme-settings/
 */
add_action( 'genesis_theme_settings_metaboxes', 'mai_remove_genesis_theme_settings_metaboxes' );
function mai_remove_genesis_theme_settings_metaboxes( $_genesis_theme_settings_pagehook ) {
	remove_meta_box( 'genesis-theme-settings-header', $_genesis_theme_settings_pagehook, 'main' );
	remove_meta_box( 'genesis-theme-settings-posts', $_genesis_theme_settings_pagehook, 'main' );
	remove_meta_box( 'genesis-theme-settings-blogpage', $_genesis_theme_settings_pagehook, 'main' );
	remove_meta_box( 'genesis-theme-settings-layout', $_genesis_theme_settings_pagehook, 'main' );
	remove_meta_box( 'genesis-theme-settings-nav', $_genesis_theme_settings_pagehook, 'main' );
	remove_meta_box( 'genesis-theme-settings-breadcrumb', $_genesis_theme_settings_pagehook, 'main' );
	remove_meta_box( 'genesis-theme-settings-comments', $_genesis_theme_settings_pagehook, 'main' );
}

/**
 * Remove custom title/logo metabox from customizer.
 * Priority had to be late for this to work.
 *
 * @link https://developer.wordpress.org/themes/advanced-topics/customizer-api/
 */
add_action( 'customize_register', 'mai_remove_genesis_customizer_controls', 99 );
function mai_remove_genesis_customizer_controls( $wp_customize ) {
	$wp_customize->remove_control( 'blog_title' );
}

/**
 * Remove site title/logo toggle from Customize > Genesis > Theme Settings > Header > Site title/logo.
 * I think Genesis 2.6 moved it here so we needed a new function.
 *
 * @since   1.3.0
 *
 * @param   array  The Genesis customizer config.
 *
 * @return  array  The modified config.
 */
add_filter( 'genesis_customizer_theme_settings_config', 'mai_remove_site_title_logo_toggle' );
function mai_remove_site_title_logo_toggle( $config ) {
	if ( isset( $config['genesis']['sections']['genesis_header']['controls']['blog_title'] ) ) {
		unset( $config['genesis']['sections']['genesis_header']['controls']['blog_title'] );
	}
	return $config;
}
