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
	if ( $option_db_version >= MAI_THEME_ENGINE_DB_VERSION ) {
		return;
	}

	if ( $option_db_version < '1100' ) {
		mai_upgrade_1100();
	}

	if ( $option_db_version < '1161' ) {
		mai_upgrade_1161();
	}

	// Update the version number option.
	update_option( 'mai_db_version', MAI_THEME_ENGINE_DB_VERSION );
}

/**
 * Update banner_height to 'md' if banner image is set.
 *
 * @since 1.1.6.1
 */
function mai_upgrade_1161() {

	// Banner disabled.
	if ( ! mai_is_banner_area_enabled_globally() ) {
		return;
	}

	// If we have banner image.
	if ( genesis_get_option( 'banner_id' ) ) {
		// Update banner_height.
		genesis_update_settings( array(
			'banner_height' => 'lg',
		) );
	}

}

/**
 * Convert theme mods and original settings to new theme settings (options).
 *
 * @since 1.1.0
 */
function mai_upgrade_1100() {

	$settings = $mods_to_delete = array();

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
		if ( false !== $value ) {
			$settings[$key] = $value;
			$mods_to_delete[] = $key;
		}
	}

	// This was a string before, now let's force absint.
	if ( isset( $settings['footer_widget_count'] ) ) {
		$settings['footer_widget_count'] = absint( $settings['footer_widget_count'] );
	}

	// This field is going from boolean for all, to individual keys per post type.
	$banner_featured_image = get_theme_mod( 'banner_featured_image' );

	// We need to deal with this value separately.
	$enable_singular_image = get_theme_mod( 'enable_singular_image' );

	// If enabled singular image is checked/true.
	if ( false !== $enable_singular_image ) {
		$settings['singular_image_page'] = 1;
		$settings['singular_image_post'] = 1;
	}

	/**
	 * These originally had all the sites post_types and taxos.
	 * Since 1.1.0 we move post_types and taxos to post_type specific keys.
	 */
	$disable_post_types = (array) genesis_get_option( 'banner_disable_post_types' );
	$disable_taxonomies = (array) genesis_get_option( 'banner_disable_taxonomies' );

	$banner_disable_post_types = $banner_disable_taxonomies = array();

	// Remove other non-default post types from this setting.
	foreach( $disable_post_types as $post_type ) {
		if ( in_array( $post_type, array( 'page', 'post' ) ) ) {
			$banner_disable_post_types[] = $post_type;
		}
	}

	// If we have any, set them.
	if ( $banner_disable_post_types ) {
		$settings['banner_disable_post_types'] = (array) $banner_disable_post_types;
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
				$banner_disable_taxonomies[] = $taxo;
			}
		}

	}

	if ( $banner_disable_taxonomies ) {
		$settings['banner_disable_taxonomies'] = (array) $banner_disable_taxonomies;
	}

	// Get post types.
	$post_types = genesis_get_cpt_archive_types();

	if ( $post_types ) {
		foreach ( $post_types as $post_type => $object ) {
			if ( false !== $banner_featured_image ) {
				// Banner featured image.
				$settings[ sprintf( 'banner_featured_image_%s', $post_type ) ] = $banner_featured_image;
			}
			// Banner disable post type.
			if ( in_array( $post_type, $disable_post_types ) ) {
				$settings[ sprintf( 'banner_disable_%s', $post_type ) ] = 1;
			}
			// Banner disable taxonomies.
			$taxonomies = get_object_taxonomies( $post_type, 'objects' );
			if ( $taxonomies ) {
				foreach ( $taxonomies as $name => $object ) {
					if ( in_array( $name, $disable_taxonomies ) ) {
						$settings[ sprintf( 'banner_disable_taxonomies_%s', $post_type ) ][] = $name;
					}
				}
			}
			// If we have a value from this theme_mod.
			if ( false !== $enable_singular_image ) {
				// Display featured image.
				$settings[ sprintf( 'singular_image_%s', $post_type ) ] = ( class_exists( 'WooCommerce' ) && 'product' === $post_type ) ? 1 : mai_sanitize_one_zero( $enable_singular_image );
			}
		}
	}


	// Maybe update.
	if ( ! empty( $settings ) ) {
		// Update settings.
		genesis_update_settings( $settings );
	}

	// Remove each theme mod from the array.
	if ( $mods_to_delete ) {
		foreach ( $mods_to_delete as $mod ) {
			remove_theme_mod( $mod );
		}
	}

	// This is not in array because it was dealt with separately.
	if ( false !== $enable_singular_image ) {
		remove_theme_mod( 'enable_singular_image' );
	}

	// Static blog.
	if ( $blog_page_id = get_option( 'page_for_posts' ) ) {
		$blog_settings = _mai_upgrade_1100_get_static_archive_settings( $blog_page_id );
		if ( $blog_settings ) {
			genesis_update_settings( $blog_settings, GENESIS_SETTINGS_FIELD );
		}
	}

	// Woo upgrade, meta to cpt-archive-settings.
	if ( class_exists( 'WooCommerce' ) && ( $shop_page_id = get_option( 'woocommerce_shop_page_id' ) ) ) {
		$shop_settings = array();
		if ( metadata_exists( 'post', $shop_page_id, 'banner_id' ) ) {
			$shop_settings['banner_id']   = get_post_meta( $shop_page_id, 'banner_id', true );
			delete_post_meta( $shop_page_id, 'banner_id' );
		}
		if ( metadata_exists( 'post', $shop_page_id, 'hide_banner' ) ) {
			$shop_settings['hide_banner'] = get_post_meta( $shop_page_id, 'hide_banner', true );
			delete_post_meta( $shop_page_id, 'hide_banner' );
		}
		$shop_settings = array_merge( $shop_settings, _mai_upgrade_1100_get_static_archive_settings( $shop_page_id ) );
		if ( $shop_settings ) {
			genesis_update_settings( $shop_settings, GENESIS_CPT_ARCHIVE_SETTINGS_FIELD_PREFIX . 'product' );
		}
	}

}

function _mai_upgrade_1100_get_static_archive_settings( $post_id ) {

	$settings = array();

	if ( metadata_exists( 'post', $post_id, 'enabled_custom_archive_settings' ) ) {

		$meta = array(
			'columns',
			'content_archive',
			'content_archive_thumbnail',
			'content_archive_limit',
			'image_location',
			'image_size',
			'image_alignment',
			'more_link',
			'remove_meta',
			'posts_per_page',
			'posts_nav',
		);

		$cpt_archive_settings = get_post_meta( $post_id, 'enabled_custom_archive_settings', true );

		if ( $cpt_archive_settings ) {
			foreach ( $meta as $key ) {
				if ( metadata_exists( 'post', $post_id, $key ) ) {
					$settings[$key] = get_post_meta( $post_id, $key, true );
					delete_post_meta( $post_id, $key );
				}
			}
		}

		delete_post_meta( $post_id, 'enabled_custom_archive_settings' );

	}

	return $settings;
}
