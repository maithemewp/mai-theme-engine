<?php

/**
 * Maybe run the version updater.
 * Mostly taken from G core. Some original inspiration from link below.
 *
 * @link   https://www.sitepoint.com/wordpress-plugin-updates-right-way/
 *
 * @return void
 */
add_action( 'admin_init', 'mai_update_database_version', 20 );
function mai_update_database_version() {

	// Get the version number saved in the db.
	$option_db_version = get_option( 'mai_db_version' );

	// Bail if the saved version is the version is greater than or equal to the current version.
	if ( $option_db_version >= MAI_PRO_ENGINE_DB_VERSION ) {
		return;
	}

	if ( $option_db_version < '1100' ) {
		mai_upgrade_1100();
	}

	// Update the version number option.
	update_option( 'mai_db_version', MAI_PRO_ENGINE_DB_VERSION );
}

/**
 * Convert theme mods to theme settings (options).
 *
 * @since 1.1.0
 */
function mai_upgrade_1100() {

	/**
	 * Bail if no theme_mod.
	 * This would happen if first install of the theme is already >= db version 1100.
	 * We use a mod that won't return something that could be falsey.
	 */
	if ( ! get_theme_mod( 'footer_widget_count' ) ) {
		return;
	}

	// New settings.
	$settings = array();

	// Theme mods to convert to settings.
	$mods = array(
		'enable_sticky_header',
		'enable_shrink_header',
		'footer_widget_count',
		'mobile_menu_style',
		'enable_banner_area',
		'banner_featured_image',
		'banner_background_color',
		'banner_id',
		'banner_overlay',
		'banner_inner',
		'banner_content_width',
		'banner_align_text',
	);
	foreach( $mods as $key ) {
		$value = get_theme_mod( $key );
		if ( $value ) {
			$settings[$key] = $value;
		}
	}

	// This was a string before, now let's force absint.
	$settings['footer_widget_count'] = absint( $settings['footer_widget_count'] );

	/**
	 * These fields is going from boolean for all, to individual keys per post type.
	 */
	$banner_featured_image = get_theme_mod( 'banner_featured_image' );
	$enable_singular_image = get_theme_mod( 'enable_singular_image' );

	// If enabled singular image is checked/true.
	if ( $enable_singular_image ) {
		$settings['singular_image_page'] = 1;
		$settings['singular_image_post'] = 1;
	}

	/**
	 * These originally had all the sites post_types and taxos.
	 * Since 1.1.0 we move post_types and taxos to post_type specific keys.
	 */
	$disable_post_types = (array) genesis_get_option( 'banner_disable_post_types' );
	$disable_taxonomies = (array) genesis_get_option( 'banner_disable_taxonomies' );

	$settings['banner_disable_post_types'] = $settings['banner_disable_taxonomies'] = array();

	// Remove other non-default post types from this setting.
	foreach( $disable_post_types as $post_type ) {
		if ( in_array( $post_type, array( 'page', 'post' ) ) ) {
			$settings['banner_disable_post_types'][] = $post_type;
		}
	}

	// Remove other non-post taxos from this setting.
	if ( $disable_taxonomies ) {

		$keeper_taxos = array();
		$taxonomies  = get_object_taxonomies( 'post', 'objects' );
		if ( $taxonomies ) {
			foreach ( $taxonomies as $taxo ) {
				// If taxo is not public or is registered to more than one object.
				if ( ! $taxo->public || ( count( (array) $taxo->object_type ) > 1 ) ) {
					continue;
				}
				$keeper_taxos[] = $taxo->name;
			}
		}

		foreach( $disable_taxonomies as $taxo ) {
			if ( in_array( $taxo, $keeper_taxos ) ) {
				$settings['banner_disable_taxonomies'][] = $taxo;
			}
		}

	}

	// Get post types.
	$post_types = genesis_get_cpt_archive_types();

	if ( $post_types ) {
		foreach ( $post_types as $post_type => $object ) {
			// Banner featured image.
			$settings[ sprintf( 'banner_featured_image_%s', $post_type ) ] = $banner_featured_image;
			// Banner disable post type.
			if ( in_array( $post_type, $disable_post_types ) ) {
				$settings[ sprintf( 'banner_disable_%s', $post_type ) ] = 1;
			}
			// Banner disable taxonomies.
			$taxonomies = get_object_taxonomies( $post_type, 'objects' );
			if ( $taxonomies ) {
				foreach ( $taxonomies as $name => $object ) {
					if ( in_array( $name, $disable_taxonomies ) )
					$settings[ sprintf( 'banner_disable_taxonomies_%s', $post_type ) ][] = $name;
				}
			}
			// Display featured image.
			$settings[ sprintf( 'singular_image_%s', $post_type ) ] = ( class_exists( 'WooCommerce' ) && 'product' === $post_type ) ? 1 : $enable_singular_image;
		}
	}

	// Update.
	if ( ! empty( $settings ) ) {

		// Update settings.
		genesis_update_settings( $settings );

		// Remove each theme mod from the array.
		foreach ( $mods as $mod ) {
			remove_theme_mod( $mod );
		}
		// enable_singular_image is not in the array because it was processed separately.
		remove_theme_mod( 'enable_singular_image' );
	}

	// Static blog.
	if ( $blog_page_id = get_option( 'page_for_posts' ) ) {
		// Update the settings.
		genesis_update_settings( _mai_upgrade_1100_get_static_archive_settings( $blog_page_id ), GENESIS_SETTINGS_FIELD );
	}

	// Woo upgrade, meta to cpt-archive-settings.
	if ( class_exists( 'WooCommerce' ) && ( $shop_page_id = get_option( 'woocommerce_shop_page_id' ) ) ) {
		$settings                = array();
		$settings['banner_id']   = get_post_meta( $shop_page_id, 'banner_id', true );
		$settings['hide_banner'] = get_post_meta( $shop_page_id, 'hide_banner', true );
		$settings                = array_merge( $settings, _mai_upgrade_1100_get_static_archive_settings( $shop_page_id ) );
		// Update the settings.
		genesis_update_settings( $settings, GENESIS_CPT_ARCHIVE_SETTINGS_FIELD_PREFIX . 'product' );
	}


}

function _mai_upgrade_1100_get_static_archive_settings( $post_id ) {
	$settings             = array();
	$cpt_archive_settings = get_post_meta( $post_id, 'enabled_custom_archive_settings', true );
	if ( $cpt_archive_settings ) {
		$settings['columns']                   = get_post_meta( $post_id, 'columns', true );
		$settings['content_archive']           = get_post_meta( $post_id, 'content_archive', true );
		$settings['content_archive_thumbnail'] = get_post_meta( $post_id, 'content_archive_thumbnail', true );
		$settings['content_archive_limit']     = get_post_meta( $post_id, 'columns', true );
		$settings['image_location']            = get_post_meta( $post_id, 'image_location', true );
		$settings['image_size']                = get_post_meta( $post_id, 'image_size', true );
		$settings['image_alignment']           = get_post_meta( $post_id, 'image_alignment', true );
		$settings['more_link']                 = get_post_meta( $post_id, 'more_link', true );
		$settings['remove_meta']               = get_post_meta( $post_id, 'remove_meta', true );
		$settings['posts_per_page']            = get_post_meta( $post_id, 'posts_per_page', true );
		$settings['posts_nav']                 = get_post_meta( $post_id, 'posts_nav', true );
	}
	delete_post_meta( $post_id, 'enabled_custom_archive_settings' );
	delete_post_meta( $post_id, 'columns' );
	delete_post_meta( $post_id, 'content_archive' );
	delete_post_meta( $post_id, 'content_archive_thumbnail' );
	delete_post_meta( $post_id, 'content_archive_limit' );
	delete_post_meta( $post_id, 'image_location' );
	delete_post_meta( $post_id, 'image_size' );
	delete_post_meta( $post_id, 'image_alignment' );
	delete_post_meta( $post_id, 'more_link' );
	delete_post_meta( $post_id, 'remove_meta' );
	delete_post_meta( $post_id, 'posts_per_page' );
	delete_post_meta( $post_id, 'posts_nav' );
	return $settings;
}
