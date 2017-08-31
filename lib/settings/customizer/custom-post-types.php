<?php

/**
 * Setup CPT's customizer and Archive Settings fields.
 *
 * Possible keys/settings:
 *
 * 'layout_{post_type}' (saves to 'genesis-settings' option)
 * 'layout'
 * 'singular_image_{post_type}' (saves to 'genesis-settings' option)
 * 'remove_meta_{post_type}' (saves to 'genesis-settings' option)
 * 'enable_content_archive_settings'
 * 'columns'
 * 'content_archive'
 * 'content_archive_limit'
 * 'content_archive_thumbnail'
 * 'image_location'
 * 'image_size'
 * 'image_alignment'
 * 'more_link'
 * 'more_link_text'
 * 'remove_meta'
 * 'posts_per_page'
 * 'posts_nav'
 *
 * @return  void
 */
add_action( 'init', 'mai_cpt_settings_init', 999 );
function mai_cpt_settings_init() {

	/**
	 * Get post types.
	 * Applies apply_filters( 'genesis_cpt_archives_args', $args ); filter.
	 */
	$post_types = genesis_get_cpt_archive_types();

	if ( ! $post_types ) {
		return;
	}

	foreach ( $post_types as $post_type => $object ) {

		// Build post_type specific keys.
		$layout_singular_key    = sprintf( 'layout_%s', $post_type );
		$remove_meta_single_key = sprintf( 'remove_meta_%s', $post_type );
		$singular_image_key     = sprintf( 'singular_image_%s', $post_type );

		$args = array(
			'layout'                          => mai_get_default_cpt_option( 'layout', $post_type ), // Archive
			$layout_singular_key              => mai_get_default_cpt_option( $layout_singular_key, $post_type ), // Single
			'enable_content_archive_settings' => mai_get_default_cpt_option( 'enable_content_archive_settings', $post_type ),
			'columns'                         => mai_get_default_cpt_option( 'columns', $post_type ),
			'more_link'                       => mai_get_default_cpt_option( 'more_link', $post_type ),
			'more_link_text'                  => mai_get_default_cpt_option( 'more_link_text', $post_type ),
			'posts_per_page'                  => mai_get_default_cpt_option( 'posts_per_page', $post_type ),
			'posts_nav'                       => mai_get_default_cpt_option( 'posts_nav', $post_type ),
		);

		$supports = get_all_post_type_supports( $post_type );

		if ( isset( $supports['genesis-entry-meta-before-content'] ) || isset( $supports['genesis-entry-meta-after-content'] ) ) {
			$args['remove_meta']               = mai_get_default_cpt_option( 'remove_meta', $post_type );
			$args[$remove_meta_single_key]     = mai_get_default_cpt_option( $remove_meta_single_key, $post_type ); // Use the default content archive setting
		}

		if ( isset( $supports['editor'] ) || isset( $supports['excerpt'] ) ) {
			$args['content_archive']           = mai_get_default_cpt_option( 'content_archive', $post_type );
			$args['content_archive_limit']     = mai_get_default_cpt_option( 'content_archive_limit', $post_type );
		}

		if ( isset( $supports['thumbnail'] ) ) {
			$args[$singular_image_key]         = mai_get_default_cpt_option( $singular_image_key, $post_type ); // Use the default for posts
			$args['content_archive_thumbnail'] = mai_get_default_cpt_option( 'content_archive_thumbnail', $post_type );
			$args['image_location']            = mai_get_default_cpt_option( 'image_location', $post_type );
			$args['image_size']                = mai_get_default_cpt_option( 'image_size', $post_type );
			$args['image_alignment']           = mai_get_default_cpt_option( 'image_alignment', $post_type );
		}

		/**
		 * Allow filter to easily modify settings for each post type.
		 * This is great for adding CPT support for specific plugins like Woo/EDD/etc.
		 */
		$args = apply_filters( 'mai_cpt_settings', $args, $post_type );

		// Do the customizer settings.
		mai_do_cpt_settings( $post_type, $args );

	}
}

/**
 * Define the archive settings for a post type.
 * Args should either be false (to disable) or provide the default setting (to enable).
 *
 * This should be hooked into 'init' with a late priority (after 10) to ensure post_types are registered.
 *
 * @param   string  $post_type  The post type name.
 * @param   array   $args       The args to enable, with their default value. Include only the fields you want.
 *
 * @return  void
 */
function mai_do_cpt_settings( $post_type, $args ) {

	// Bail if we don't have a post type.
	if ( ! post_type_exists( $post_type ) ) {
		return;
	}

	// Bail if no args.
	if ( ! $args ) {
		return;
	}

	/**
	 * Add Mai CPT support here.
	 * This should happen here, internally only. Please don't add 'mai-cpt-settings' support to CPT's manually.
	 */
	add_post_type_support( $post_type, 'mai-cpt-settings' );

	// Defaults.
	$defaults = get_default_cpt_options( $post_type );

	// Parse.
	$args = wp_parse_args( $args, $defaults );

	// Setup post_type specific keys.
	$layout_singular_key    = sprintf( 'layout_%s', $post_type );
	$remove_meta_single_key = sprintf( 'remove_meta_%s', $post_type );
	$singular_image_key     = sprintf( 'singular_image_%s', $post_type );

	// Sanitize.
	$args = array(
		$layout_singular_key              => sanitize_key( $args[$layout_singular_key] ),
		'layout'                          => sanitize_key( $args['layout'] ),
		$singular_image_key               => ( 'unset' !== $args[$singular_image_key] ) ? _mai_customizer_sanitize_one_zero( $args[$singular_image_key] ) : 'unset',
		$remove_meta_single_key           => ( ( 'unset' !== $args[$remove_meta_single_key] ) && ( post_type_supports( $post_type, 'genesis-entry-meta-before-content' ) || post_type_supports( $post_type, 'genesis-entry-meta-after-content' ) ) ) ? array_map( 'sanitize_key', (array) $args[$remove_meta_single_key] ) : 'unset',
		'enable_content_archive_settings' => _mai_customizer_sanitize_one_zero( $args['enable_content_archive_settings'] ),
		'columns'                         => absint( $args['columns'] ),
		'content_archive'                 => ( 'unset' !== $args['content_archive'] ) ? sanitize_key( $args['content_archive'] ) : 'unset',
		'content_archive_limit'           => ( 'unset' !== $args['content_archive_limit'] ) ? absint( $args['content_archive_limit'] ) : 'unset',
		'content_archive_thumbnail'       => ( ( 'unset' !== $args['content_archive_thumbnail'] ) && post_type_supports( $post_type, 'thumbnail' ) ) ? _mai_customizer_sanitize_one_zero( $args['content_archive_thumbnail'] ) : 'unset',
		'image_location'                  => ( 'unset' !== $args['image_location'] ) ? sanitize_key( $args['image_location'] ) : 'unset',
		'image_size'                      => ( 'unset' !== $args['image_size'] ) ? sanitize_key( $args['image_size'] ) : 'unset',
		'image_alignment'                 => ( 'unset' !== $args['image_alignment'] ) ? sanitize_key( $args['image_alignment'] ) : 'unset',
		'more_link'                       => ( 'unset' !== $args['more_link'] ) ? _mai_customizer_sanitize_one_zero( $args['more_link'] ) : 'unset',
		'more_link_text'                  => ( 'unset' !== $args['more_link_text'] ) ? sanitize_key( $args['more_link_text'] ) : 'unset',
		'remove_meta'                     => ( ( 'unset' !== $args['remove_meta'] ) && ( post_type_supports( $post_type, 'genesis-entry-meta-before-content' ) || post_type_supports( $post_type, 'genesis-entry-meta-after-content' ) ) ) ? array_map( 'sanitize_key', (array) $args['remove_meta'] ) : 'unset',
		'posts_per_page'                  => ( 'unset' !== $args['posts_per_page'] ) ? absint( $args['posts_per_page'] ) : 'unset',
		'posts_nav'                       => ( 'unset' !== $args['posts_nav'] ) ? sanitize_key( $args['posts_nav'] ) : 'unset',
	);

	$settings_fields = $args;
	$unset           = array();

	// Unset the 'unset' items.
	foreach ( array_keys( $settings_fields, 'unset', true ) as $key ) {
		$unset[$key] = $settings_fields[$key];
		unset( $settings_fields[$key] );
	}

	// If in admin or viewing customizer.
	if ( is_admin() or is_customize_preview() ) {

		$options = get_option( GENESIS_CPT_ARCHIVE_SETTINGS_FIELD_PREFIX . $post_type );

		// If CPT options exist.
		if ( $options ) {
			/**
			 * Get any options that need to be unset.
			 * Returns an associative array containing all the entries of array1 which have keys that are present in all arguments.
			 */
			$unset = array_intersect_key( $unset, $options );
			/**
			 * If we have any items to update.
			 * This should only happen if/when the mai_cpt_settings() args change after first being setup.
			 */
			if ( ! empty( $unset ) ) {
				// genesis_update_settings( $unset, GENESIS_CPT_ARCHIVE_SETTINGS_FIELD_PREFIX . $post_type );
			}
		}
		// No options, let's setup some defaults.
		else {
			// Unset $layout_singular_key because this is stored in theme settings, not cpt archive settings.
			if ( isset( $settings_fields[$layout_singular_key] ) ) {
				unset( $settings_fields[$layout_singular_key] );
			}
			// Unset $remove_meta_single_key because this is stored in theme settings, not cpt archive settings.
			if ( isset( $settings_fields[$remove_meta_single_key] ) ) {
				unset( $settings_fields[$remove_meta_single_key] );
			}
			// Unset $singular_image_key because this is stored in theme settings, not cpt archive settings.
			if ( isset( $settings_fields[$singular_image_key] ) ) {
				unset( $settings_fields[$singular_image_key] );
			}
			// genesis_update_settings( $settings_fields, GENESIS_CPT_ARCHIVE_SETTINGS_FIELD_PREFIX . $post_type );
		}

	}

	// Register the archive settings.
	add_action( 'customize_register', function( $wp_customize ) use ( $post_type, $settings_fields ) {
		mai_register_cpt_settings( $wp_customize, $post_type, $settings_fields );
	}, 22 );

	// Do the (CMB2) CPT archive settings.
	if ( post_type_supports( $post_type, 'genesis-cpt-archives-settings' ) ) {
		mai_do_genesis_cpt_archive_settings( $post_type );
	}

}

function mai_register_cpt_settings( $wp_customize, $post_type, $args ) {

	// Bail if we don't have a post type.
	if ( ! post_type_exists( $post_type ) ) {
		return;
	}

	// Vars.
	$section                       = sprintf( 'mai_%s_cpt_settings', $post_type );
	$settings_field                = GENESIS_CPT_ARCHIVE_SETTINGS_FIELD_PREFIX . $post_type;
	$genesis_settings              = 'genesis-settings';
	$post_type_object              = get_post_type_object( $post_type );
	$prefix                        = sprintf( '%s_', $post_type );

	// Keys
	$banner_featured_image_key     = sprintf( 'banner_featured_image_%s', $post_type );
	$banner_disable_key            = sprintf( 'banner_disable_%s', $post_type );
	$banner_disable_taxonomies_key = sprintf( 'banner_disable_taxonomies_%s', $post_type );
	$singular_image_key            = sprintf( 'singular_image_%s', $post_type );
	$remove_meta_single_key        = sprintf( 'remove_meta_%s', $post_type );
	$single_layout_key             = sprintf( 'layout_%s', $post_type );

	// Mai {post type name} Settings.
	$wp_customize->add_section(
		$section,
		array(
			'title'    => sprintf( __( 'Mai %s Settings', 'mai-pro-engine' ), $post_type_object->label ),
			'priority' => '39',
		)
	);

	// Banner break.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'cpt_banner_break' ),
		array(
			'default' => '',
			'type'    => 'option',
		)
	);
	$wp_customize->add_control(
		new Mai_Customize_Control_Break( $wp_customize,
			$prefix . 'cpt_banner_break',
			array(
				'label'           => __( 'Banner Area', 'mai-pro-engine' ),
				'section'         => $section,
				'settings'        => false,
				'active_callback' => function() use ( $wp_customize, $genesis_settings ) {
					return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $genesis_settings );
				},
			)
		)
	);

	// Banner Image
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'banner_id' ),
		array(
			'default'           => '',
			'type'              => 'option',
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control( $wp_customize,
		$prefix . 'banner_id',
		array(
			'label'           => __( 'Default Banner Image', 'mai-pro-engine' ),
			'section'         => $section,
			'settings'        => _mai_customizer_get_field_name( $settings_field, 'banner_id' ),
			'active_callback' => function() use ( $wp_customize, $genesis_settings ) {
				return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $genesis_settings );
			},
		)
	) );

	// Disable banner, heading only.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'cpt_hide_banner_customizer_heading' ),
		array(
			'default' => '',
			'type'    => 'option',
		)
	);
	$wp_customize->add_control(
		new Mai_Customize_Control_Content( $wp_customize,
			$prefix . 'cpt_hide_banner_customizer_heading',
			array(
				'label'           => __( 'Hide banner on (archive/single)', 'mai-pro-engine' ),
				'section'         => $section,
				'settings'        => false,
				'active_callback' => function() use ( $wp_customize, $genesis_settings ) {
					return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $genesis_settings );
				},
			)
		)
	);

	// Hide banner CPT archive.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'hide_banner' ),
		array(
			'default'           => 0,
			'type'              => 'option',
			'sanitize_callback' => '_mai_customizer_sanitize_one_zero',
		)
	);
	$wp_customize->add_control(
		$prefix . 'hide_banner',
		array(
			'label'           => __( 'Hide banner on the main archive', 'mai-pro-engine' ),
			'section'         => $section,
			'settings'        => _mai_customizer_get_field_name( $settings_field, 'hide_banner' ),
			'type'            => 'checkbox',
			'active_callback' => function() use ( $wp_customize, $genesis_settings ) {
				return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $genesis_settings );
			},
		)
	);

	// Disable banner singular (saves to genesis-settings option).
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $genesis_settings, $banner_disable_key ),
		array(
			'default'           => 0,
			'type'              => 'option',
			'sanitize_callback' => '_mai_customizer_sanitize_one_zero',
		)
	);
	$wp_customize->add_control(
		$prefix . $banner_disable_key,
		array(
			'label'           => __( 'Hide banner on single entries', 'mai-pro-engine' ),
			'section'         => $section,
			'settings'        => _mai_customizer_get_field_name( $genesis_settings, $banner_disable_key ),
			'type'            => 'checkbox',
			'active_callback' => function() use ( $wp_customize, $genesis_settings ) {
				return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $genesis_settings );
			},
		)
	);

	// Disable banner taxonomies (saves to genesis-settings option).
	$disable_taxonomies = array();
	$taxonomies         = get_object_taxonomies( $post_type, 'objects' );
	if ( $taxonomies ) {
		foreach ( $taxonomies as $taxo ) {
			/**
			 * If taxo is registered to more than one object.
			 * We may need to account for these taxos later, but for now
			 * this seems like an edge case. Most taxos are only registered to 1 object.
			 */
			if ( count( (array) $taxo->object_type ) > 1 ) {
				continue;
			}
			$disable_taxonomies[$taxo->name] = $taxo->label;
		}
		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $genesis_settings, $banner_disable_taxonomies_key ),
			array(
				'default'           => '',
				'type'              => 'option',
				'sanitize_callback' => '_mai_customizer_multicheck_sanitize_key',
			)
		);
		$wp_customize->add_control(
			new Mai_Customize_Control_Multicheck( $wp_customize,
				$prefix . $banner_disable_taxonomies_key,
				array(
					'label'    => __( 'Disable banner on (taxonomies)', 'mai-pro-engine' ),
					'section'  => $section,
					'settings' => _mai_customizer_get_field_name( $genesis_settings, $banner_disable_taxonomies_key ),
					'choices'  => $disable_taxonomies,
				)
			)
		);
	}

	// Banner featured image, heading only.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'banner_featured_image_heading' ),
		array(
			'default' => '',
			'type'    => 'option',
		)
	);
	$wp_customize->add_control(
		new Mai_Customize_Control_Content( $wp_customize,
			$prefix . 'banner_featured_image_heading',
			array(
				'label'           => __( 'Featured Image on (single entries)', 'mai-pro-engine' ),
				'section'         => $section,
				'settings'        => false,
				'active_callback' => function() use ( $wp_customize, $genesis_settings, $banner_disable_key ) {
					return ( (bool) ! $wp_customize->get_setting( _mai_customizer_get_field_name( $genesis_settings, $banner_disable_key ) )->value() && _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $genesis_settings ) );
				},
			)
		)
	);

	// Banner featured image (saves to genesis-settings option).
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $genesis_settings, $banner_featured_image_key ),
		array(
			'default'           => 0,
			'type'              => 'option',
			'sanitize_callback' => '_mai_customizer_sanitize_one_zero',
		)
	);
	$wp_customize->add_control(
		$prefix . $banner_featured_image_key,
		array(
			'label'           => __( 'Use featured image as banner image', 'mai-pro-engine' ),
			'section'         => $section,
			'settings'        => _mai_customizer_get_field_name( $genesis_settings, $banner_featured_image_key ),
			'priority'        => 10,
			'type'            => 'checkbox',
			'active_callback' => function() use ( $wp_customize, $genesis_settings, $banner_disable_key ) {
				return ( (bool) ! $wp_customize->get_setting( _mai_customizer_get_field_name( $genesis_settings, $banner_disable_key ) )->value() && _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $genesis_settings ) );
			},
		)
	);

	// Layouts break.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'cpt_archive_layouts_break' ),
		array(
			'default' => '',
			'type'    => 'option',
		)
	);
	$wp_customize->add_control(
		new Mai_Customize_Control_Break( $wp_customize,
			$prefix . 'cpt_archive_layouts_break',
			array(
				'label'    => __( 'Layouts', 'mai-pro-engine' ),
				'section'  => $section,
				'settings' => false,
			)
		)
	);

	// Archive Layout.
	if ( isset( $args['layout'] ) ) {

		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $settings_field, 'layout' ),
			array(
				'default'           => $args['layout'],
				'type'              => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);
		$wp_customize->add_control(
			$prefix . 'layout',
			array(
				'label'    => __( 'Archives', 'mai-pro-engine' ),
				'section'  => $section,
				'settings' => _mai_customizer_get_field_name( $settings_field, 'layout' ),
				'type'     => 'select',
				'choices'  => array_merge( array( '' => __( '- Archives Default -', 'mai-pro-engine' ) ), genesis_get_layouts_for_customizer() ),
			)
		);

	}

	// Single layout (saves to genesis-settings option).
	if ( isset( $args[$single_layout_key]  )) {

		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $genesis_settings, $single_layout_key ),
			array(
				'default'           => $args[$single_layout_key],
				'type'              => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);
		$wp_customize->add_control(
			$prefix . $single_layout_key,
			array(
				'label'    => __( 'Single Entries', 'mai-pro-engine' ),
				'section'  => $section,
				'settings' => _mai_customizer_get_field_name( $genesis_settings, $single_layout_key ),
				'type'     => 'select',
				'choices'  => array_merge( array( '' => __( '- Site Default -', 'mai-pro-engine' ) ), genesis_get_layouts_for_customizer() ),
			)
		);

	}

	if ( ( isset( $args[$singular_image_key] ) || isset( $args[$remove_meta_single_key] ) ) && ( post_type_supports( $post_type, 'thumbnail' ) || ( post_type_supports( $post_type, 'genesis-entry-meta-before-content' ) || post_type_supports( $post_type, 'genesis-entry-meta-after-content' ) ) ) ) {

		// Single Entry settings break.
		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $settings_field, 'cpt_singular_entries_break' ),
			array(
				'default' => '',
				'type'    => 'option',
			)
		);
		$wp_customize->add_control(
			new Mai_Customize_Control_Break( $wp_customize,
				$prefix . 'cpt_singular_entries_break',
				array(
					'label'    => __( 'Single Entries', 'mai-pro-engine' ),
					'section'  => $section,
					'settings' => false,
				)
			)
		);

	}

	if ( isset( $args[$singular_image_key] ) && post_type_supports( $post_type, 'thumbnail' ) ) {

		// Featured Image heading.
		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $genesis_settings, 'cpt_featured_image_customizer_heading' ),
			array(
				'default' => '',
				'type'    => 'option',
			)
		);
		$wp_customize->add_control(
			new Mai_Customize_Control_Content( $wp_customize,
				$prefix . 'cpt_featured_image_customizer_heading',
				array(
					'label'    => __( 'Featured Image', 'mai-pro-engine' ),
					'section'  => $section,
					'settings' => false,
				)
			)
		);

		// Featured Image.
		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $genesis_settings, $singular_image_key ),
			array(
				'default'           => $args[$singular_image_key],
				'type'              => 'option',
				'sanitize_callback' => '_mai_customizer_sanitize_one_zero',
			)
		);
		$wp_customize->add_control(
			$singular_image_key,
			array(
				'label'    => __( 'Display the Featured Image', 'mai-pro-engine' ),
				'section'  => $section,
				'settings' => _mai_customizer_get_field_name( $genesis_settings, $singular_image_key ),
				'type'     => 'checkbox',
			)
		);

	}

	// Entry Meta single (saves to genesis-settings option).
	if ( isset( $args[$remove_meta_single_key] ) && ( post_type_supports( $post_type, 'genesis-entry-meta-before-content' ) || post_type_supports( $post_type, 'genesis-entry-meta-after-content' ) ) ) {

		$remove_meta_choices = array();

		if ( post_type_supports( $post_type, 'genesis-entry-meta-before-content' ) ) {
			$remove_meta_choices['post_info'] = __( 'Remove Post Info', 'mai-pro-engine' );
		}

		if ( post_type_supports( $post_type, 'genesis-entry-meta-after-content' ) ) {
			$remove_meta_choices['post_meta'] = __( 'Remove Post Meta', 'mai-pro-engine' );
		}

		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $genesis_settings, $remove_meta_single_key ),
			array(
				'default'           => $args[$remove_meta_single_key],
				'type'              => 'option',
				'sanitize_callback' => '_mai_customizer_multicheck_sanitize_key',
			)
		);
		$wp_customize->add_control(
			new Mai_Customize_Control_Multicheck( $wp_customize,
				$prefix . $remove_meta_single_key,
				array(
					'label'    => __( 'Entry Meta', 'mai-pro-engine' ),
					'section'  => $section,
					'settings' => _mai_customizer_get_field_name( $genesis_settings, $remove_meta_single_key ),
					'priority' => 10,
					'choices'  => $remove_meta_choices,
				)
			)
		);

	}

	// Archive settings break.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'cpt_archives_break' ),
		array(
			'default' => '',
			'type'    => 'option',
		)
	);
	$wp_customize->add_control(
		new Mai_Customize_Control_Break( $wp_customize,
			$prefix . 'cpt_archives_break',
			array(
				'label'    => __( 'Archives', 'mai-pro-engine' ),
				'section'  => $section,
				'settings' => false,
			)
		)
	);

	// Enable Content Archive Settings.
	$wp_customize->add_setting(
		_mai_customizer_get_field_name( $settings_field, 'enable_content_archive_settings' ),
		array(
			'default'           => $args['enable_content_archive_settings'],
			'type'              => 'option',
			'sanitize_callback' => '_mai_customizer_sanitize_one_zero',
		)
	);
	$wp_customize->add_control(
		$prefix . 'enable_content_archive_settings',
		array(
			'label'    => __( 'Enable custom archive settings', 'mai-pro-engine' ),
			'section'  => $section,
			'settings' => _mai_customizer_get_field_name( $settings_field, 'enable_content_archive_settings' ),
			'priority' => 10,
			'type'     => 'checkbox',
		)
	);

	// Columns.
	if ( isset( $args['columns'] ) ) {

		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $settings_field, 'columns' ),
			array(
				'default'           => $args['columns'],
				'type'              => 'option',
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			$prefix . 'columns',
			array(
				'label'    => __( 'Columns', 'mai-pro-engine' ),
				'section'  => $section,
				'settings' => _mai_customizer_get_field_name( $settings_field, 'columns' ),
				'priority' => 10,
				'type'     => 'select',
				'choices'  => array(
					1 => __( 'None', 'mai-pro-engine' ),
					2 => __( '2', 'mai-pro-engine' ),
					3 => __( '3', 'mai-pro-engine' ),
					4 => __( '4', 'mai-pro-engine' ),
					6 => __( '6', 'mai-pro-engine' ),
				),
				'active_callback' => function() use ( $wp_customize, $settings_field ) {
					return (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'enable_content_archive_settings' ) )->value();
				},
			)
		);

	}

	// Content.
	if ( isset( $args['content_archive'] ) ) {

		$supports_editor  = post_type_supports( $post_type, 'editor' );
		$supports_excerpt = post_type_supports( $post_type, 'excerpt' );

		if ( $supports_editor || $supports_excerpt ) {

			$content_archive_choices = array(
				'none' => __( 'No content', 'mai-pro-engine' ),
			);

			if ( $supports_editor ) {
				$content_archive_choices['full'] = __( 'Entry content', 'genesis' );
			}

			if ( $supports_excerpt ) {
				$content_archive_choices['excerpts'] = __( 'Entry excerpts', 'genesis' );
			}

			// Content Type.
			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $settings_field, 'content_archive' ),
				array(
					'default'           => $args['content_archive'],
					'type'              => 'option',
					'sanitize_callback' => 'sanitize_key',
				)
			);
			$wp_customize->add_control(
				$prefix . 'content_archive',
				array(
					'label'           => __( 'Content', 'mai-pro-engine' ),
					'section'         => $section,
					'settings'        => _mai_customizer_get_field_name( $settings_field, 'content_archive' ),
					'priority'        => 10,
					'type'            => 'select',
					'choices'         => $content_archive_choices,
					'active_callback' => function() use ( $wp_customize, $settings_field ) {
						return (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'enable_content_archive_settings' ) )->value();
					},
				)
			);

			// Content Limit.
			if ( isset( $args['content_archive_limit'] ) ) {

				$wp_customize->add_setting(
					_mai_customizer_get_field_name( $settings_field, 'content_archive_limit' ),
					array(
						'default'           => $args['content_archive_limit'],
						'type'              => 'option',
						'sanitize_callback' => 'absint',
					)
				);
				$wp_customize->add_control(
					$prefix . 'content_archive_limit',
					array(
						'label'           => __( 'Limit content to how many characters?', 'mai-pro-engine' ),
						'description'     => __( '(0 for no limit)', 'mai-pro-engine' ),
						'section'         => $section,
						'settings'        => _mai_customizer_get_field_name( $settings_field, 'content_archive_limit' ),
						'priority'        => 10,
						'type'            => 'number',
						'active_callback' => function() use ( $wp_customize, $settings_field ) {
							return (bool) ( $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'enable_content_archive_settings' ) )->value() && ( 'none' != $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'content_archive' ) )->value() ) );
						},
					)
				);

			}

		}

	}

	// Featured Image.
	if ( isset( $args['content_archive_thumbnail'] ) && post_type_supports( $post_type, 'thumbnail' ) ) {

		// Archive featured image, heading only.
		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $settings_field, 'cpt_archives_featured_image_heading' ),
			array(
				'default' => '',
				'type'    => 'option',
			)
		);
		$wp_customize->add_control(
			new Mai_Customize_Control_Content( $wp_customize,
				$prefix . 'cpt_archives_featured_image_heading',
				array(
					'label'           => __( 'Featured Image', 'mai-pro-engine' ),
					'section'         => $section,
					'settings'        => false,
					'active_callback' => function() use ( $wp_customize, $settings_field ) {
						return (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'enable_content_archive_settings' ) )->value();
					},
				)
			)
		);

		// Featured Image
		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $settings_field, 'content_archive_thumbnail' ),
			array(
				'default'           => _mai_customizer_sanitize_one_zero( $args['content_archive_thumbnail'] ),
				'type'              => 'option',
				'sanitize_callback' => '_mai_customizer_sanitize_one_zero',
			)
		);
		$wp_customize->add_control(
			$prefix . 'content_archive_thumbnail',
			array(
				'label'           => __( 'Display the Featured Image', 'mai-pro-engine' ),
				'section'         => $section,
				'settings'        => _mai_customizer_get_field_name( $settings_field, 'content_archive_thumbnail' ),
				'type'            => 'checkbox',
				'active_callback' => function() use ( $wp_customize, $settings_field ) {
					return (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'enable_content_archive_settings' ) )->value();
				},
			)
		);

		// Image Location.
		if ( isset( $args['image_location'] ) ) {

			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $settings_field, 'image_location' ),
				array(
					'default'           => $args['image_location'],
					'type'              => 'option',
					'sanitize_callback' => 'sanitize_key',
				)
			);
			$wp_customize->add_control(
				$prefix . 'image_location',
				array(
					'label'    => __( 'Image Location', 'genesis' ),
					'section'  => $section,
					'settings' => _mai_customizer_get_field_name( $settings_field, 'image_location' ),
					'priority' => 10,
					'type'     => 'select',
					'choices'  => array(
						'background'     => __( 'Background Image', 'mai-pro-engine' ),
						'before_entry'   => __( 'Before Entry', 'mai-pro-engine' ),
						'before_title'   => __( 'Before Title', 'mai-pro-engine' ),
						'after_title'    => __( 'After Title', 'mai-pro-engine' ),
						'before_content' => __( 'Before Content', 'mai-pro-engine' ),
					),
					'active_callback' => function() use ( $wp_customize, $settings_field ) {
						return (bool) ( $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'enable_content_archive_settings' ) )->value() && (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'content_archive_thumbnail' ) )->value() );
					},
				)
			);

		}

		// Image Size.
		if ( isset( $args['image_size'] ) ) {

			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $settings_field, 'image_size' ),
				array(
					'default'           => $args['image_size'],
					'type'              => 'option',
					'sanitize_callback' => 'sanitize_key',
				)
			);
			$wp_customize->add_control(
				$prefix . 'image_size',
				array(
					'label'           => __( 'Image Size', 'genesis' ),
					'section'         => $section,
					'settings'        => _mai_customizer_get_field_name( $settings_field, 'image_size' ),
					'priority'        => 10,
					'type'            => 'select',
					'choices'         => _mai_customizer_get_image_sizes_config(),
					'active_callback' => function() use ( $wp_customize, $settings_field ) {
						return (bool) ( $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'enable_content_archive_settings' ) )->value() && (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'content_archive_thumbnail' ) )->value() );
					},
				)
			);

		}

		// Image Alignment.
		if ( isset( $args['image_alignment'] ) ) {

			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $settings_field, 'image_alignment' ),
				array(
					'default'           => $args['image_alignment'],
					'type'              => 'option',
					'sanitize_callback' => 'sanitize_key',
				)
			);
			$wp_customize->add_control(
				$prefix . 'image_alignment',
				array(
					'label'    => __( 'Image Alignment', 'genesis' ),
					'section'  => $section,
					'settings' => _mai_customizer_get_field_name( $settings_field, 'image_alignment' ),
					'priority' => 10,
					'type'     => 'select',
					'choices'  => array(
						''            => __( '- None -', 'genesis' ),
						'aligncenter' => __( 'Center', 'genesis' ),
						'alignleft'   => __( 'Left', 'genesis' ),
						'alignright'  => __( 'Right', 'genesis' ),
					),
					'active_callback' => function() use ( $wp_customize, $settings_field ) {
						// Showing featured image and background is not image location.
						return (bool) ( $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'enable_content_archive_settings' ) )->value() && $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'content_archive_thumbnail' ) )->value() && ( 'background' != $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'image_location' ) )->value() ) );
					},
				)
			);

		}

	}

	if ( isset( $args['more_link'] ) ) {

		// More Link heading
		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $settings_field, 'cpt_more_link_heading' ),
			array(
				'default' => '',
				'type'    => 'option',
			)
		);
		$wp_customize->add_control(
			new Mai_Customize_Control_Content( $wp_customize,
				$prefix . 'cpt_more_link_heading',
				array(
					'label'           => __( 'Read More Link', 'mai-pro-engine' ),
					'section'         => $section,
					'settings'        => false,
					'active_callback' => function() use ( $wp_customize, $settings_field ) {
						return (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'enable_content_archive_settings' ) )->value();
					},
				)
			)
		);

		// More Link
		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $settings_field, 'more_link' ),
			array(
				'default'           => 0,
				'type'              => 'option',
				'sanitize_callback' => '_mai_customizer_sanitize_one_zero',
			)
		);
		$wp_customize->add_control(
			$prefix . 'more_link',
			array(
				'label'           => __( 'Display the Read More link', 'mai-pro-engine' ),
				'section'         => $section,
				'settings'        => _mai_customizer_get_field_name( $settings_field, 'more_link' ),
				'priority'        => 10,
				'type'            => 'checkbox',
				'active_callback' => function() use ( $wp_customize, $settings_field ) {
					return (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'enable_content_archive_settings' ) )->value();
				},
			)
		);

	}

	// Entry Meta.
	if ( isset( $args['remove_meta'] ) && ( post_type_supports( $post_type, 'genesis-entry-meta-before-content' ) || post_type_supports( $post_type, 'genesis-entry-meta-after-content' ) ) ) {

		$remove_meta_choices = array();

		if ( post_type_supports( $post_type, 'genesis-entry-meta-before-content' ) ) {
			$remove_meta_choices['post_info'] = __( 'Remove Post Info', 'mai-pro-engine' );
		}

		if ( post_type_supports( $post_type, 'genesis-entry-meta-after-content' ) ) {
			$remove_meta_choices['post_meta'] = __( 'Remove Post Meta', 'mai-pro-engine' );
		}

		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $settings_field, 'remove_meta' ),
			array(
				'default'           => $args['remove_meta'],
				'type'              => 'option',
				'sanitize_callback' => '_mai_customizer_multicheck_sanitize_key',
			)
		);
		$wp_customize->add_control(
			new Mai_Customize_Control_Multicheck( $wp_customize,
				$prefix . 'remove_meta',
				array(
					'label'           => __( 'Entry Meta', 'mai-pro-engine' ),
					'section'         => $section,
					'settings'        => _mai_customizer_get_field_name( $settings_field, 'remove_meta' ),
					'priority'        => 10,
					'choices'         => $remove_meta_choices,
					'active_callback' => function() use ( $wp_customize, $settings_field ) {
						return (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'enable_content_archive_settings' ) )->value();
					},
				)
			)
		);

	}

	// Posts Per Page.
	if ( isset( $args['posts_per_page'] ) ) {

		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $settings_field, 'posts_per_page' ),
			array(
				'default'           => $args['posts_per_page'],
				'type'              => 'option',
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			$prefix . 'posts_per_page',
			array(
				'label'           => __( 'Entries Per Page', 'mai-pro-engine' ),
				'section'         => $section,
				'settings'        => _mai_customizer_get_field_name( $settings_field, 'posts_per_page' ),
				'priority'        => 10,
				'type'            => 'number',
				'active_callback' => function() use ( $wp_customize, $settings_field ) {
					return (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'enable_content_archive_settings' ) )->value();
				},
			)
		);

	}

	// Posts Nav.
	// if ( isset( $args['posts_nav'] ) ) {

	// 	$wp_customize->add_setting(
	// 		_mai_customizer_get_field_name( $settings_field, 'posts_nav' ),
	// 		array(
	// 			'default'           => $args['posts_nav'],
	// 			'type'              => 'option',
	// 			'sanitize_callback' => 'sanitize_key',
	// 		)
	// 	);
	// 	$wp_customize->add_control(
	// 		$prefix . 'posts_nav',
	// 		array(
	// 			'label'    => __( 'Pagination', 'genesis' ),
	// 			'section'  => $section,
	// 			'settings' => _mai_customizer_get_field_name( $settings_field, 'posts_nav' ),
	// 			'priority' => 10,
	// 			'type'     => 'select',
	// 			'choices'  => array(
	// 				'prev-next' => __( 'Previous / Next', 'genesis' ),
	// 				'numeric'   => __( 'Numeric', 'genesis' ),
	// 			),
	// 			'active_callback' => function() use ( $wp_customize, $settings_field ) {
	// 				return (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $settings_field, 'enable_content_archive_settings' ) )->value();
	// 			},
	// 		)
	// 	);

	// }

}
