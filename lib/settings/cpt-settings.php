<?php

add_action( 'init', 'mai_do_cpt_archive_settings', 20 );
function mai_do_cpt_archive_settings() {
	/**
	 * Get post types.
	 * Applies apply_filters( 'genesis_cpt_archives_args', $args ); filter.
	 */
	$post_types = genesis_get_cpt_archive_types();
	if ( ! $post_types ) {
		return;
	}
	foreach ( $post_types as $post_type => $post_type_object ) {
		$args = array(
			'columns'                          => genesis_get_option( 'columns' ),
			'content_archive_thumbnail'        => genesis_get_option( 'content_archive_thumbnail' ),
			sprintf( 'layout_%s', $post_type ) => '', // Single
			'layout'                           => '', // Archive
			'posts_per_page'                   => get_option( 'posts_per_page' ),
			'posts_nav'                        => genesis_get_option( 'posts_nav' ),
		);
		// Allow filter to easily modify settings for each post type.
		$args = apply_filters( 'mai_cpt_archive_settings', $args, $post_type );
		// Do the settings.
		mai_archive_settings( $post_type, $args );
	}
}

// add_filter( 'mai_cpt_archive_settings', 'mai_woocommerce_archive_settings', 10, 2 );
function mai_woocommerce_archive_settings( $args, $post_type ) {
	if ( ! ( class_exists( 'WooCommerce') && ( 'product' === $post_type ) ) ) {
		return $args;
	}
	return array(
		'columns'                   => 3,
		'content_archive_thumbnail' => 1,
		'layout_product'            => 'md-content',         // Single
		'layout'                    => 'full-width-content', // Archive
		'posts_per_page'            => 12,
		'posts_nav'                 => 'prev-next',
	);
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
function mai_archive_settings( $post_type, $args ) {

	// Bail if we don't have a post type.
	if ( ! post_type_exists( $post_type ) ) {
		return;
	}

	// Make sure the post type has g cpt archive support, so the correct actions and filters run as a default.
	if ( ! genesis_has_post_type_archive_support( $post_type ) ) {
		// Genesis CPT Archive Support
		add_post_type_support( $post_type, 'genesis-cpt-archives-settings' );
		// add_post_type_support( $post_type, 'mai-archives-settings' );
	}

	$single_key = sprintf( 'layout_%s', $post_type );

	// Defaults.
	$defaults = array(
		$single_key                 => '',
		'layout'                    => '',
		'columns'                   => 1,
		'content_archive'           => 'unset',
		'content_archive_limit'     => 'unset',
		'more_link'                 => 'unset',
		'more_link_text'            => 'unset',
		'content_archive_thumbnail' => 'unset',
		'image_location'            => 'unset',
		'image_size'                => 'unset',
		'image_alignment'           => 'unset',
		'remove_meta'               => 'unset',
		'posts_per_page'            => 'unset',
		'posts_nav'                 => 'unset',
	);

	// Parse.
	$args = wp_parse_args( $args, $defaults );

	// Sanitize.
	$args = array(
		$single_key                 => sanitize_key( $args[$single_key] ),
		'layout'                    => sanitize_key( $args['layout'] ),
		'columns'                   => (string) absint( $args['columns'] ),
		'content_archive'           => ( 'unset' !== $args['content_archive'] ) ? sanitize_key( $args['content_archive'] ) : 'unset',
		'content_archive_limit'     => ( 'unset' !== $args['content_archive_limit'] ) ? sanitize_key( $args['content_archive_limit'] ) : 'unset',
		'content_archive_thumbnail' => ( ( 'unset' !== $args['content_archive_thumbnail'] ) && post_type_supports( $post_type, 'thumbnail' ) ) ? sanitize_key( $args['content_archive_thumbnail'] ) : 'unset',
		'image_location'            => ( 'unset' !== $args['image_location'] ) ? sanitize_key( $args['image_location'] ) : 'unset',
		'image_size'                => ( 'unset' !== $args['image_size'] ) ? sanitize_key( $args['image_size'] ) : 'unset',
		'image_alignment'           => ( 'unset' !== $args['image_alignment'] ) ? sanitize_key( $args['image_alignment'] ) : 'unset',
		'more_link'                 => ( 'unset' !== $args['more_link'] ) ? sanitize_key( $args['more_link'] ) : 'unset',
		'more_link_text'            => ( 'unset' !== $args['more_link_text'] ) ? sanitize_key( $args['more_link_text'] ) : 'unset',
		'remove_meta'               => ( ( 'unset' !== $args['remove_meta'] ) && ( post_type_supports( $post_type, 'genesis-entry-meta-before-content' ) || post_type_supports( $post_type, 'genesis-entry-meta-after-content' ) ) ) ? sanitize_key( $args['remove_meta'] ) : 'unset',
		'posts_per_page'            => ( 'unset' !== $args['posts_per_page'] ) ? absint( $args['posts_per_page'] ) : 'unset',
		'posts_nav'                 => ( 'unset' !== $args['posts_nav'] ) ? sanitize_key( $args['posts_nav'] ) : 'unset',
	);

	$settings = $args;
	$unset    = array();

	// Unset the 'unset' items.
	foreach ( array_keys( $settings, 'unset', true ) as $key ) {
		$unset[$key] = $settings[$key];
		unset( $settings[$key] );
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
			 * This should only happen if/when the mai_archive_settings() args change after first being setup.
			 */
			if ( ! empty( $unset ) ) {
				// genesis_update_settings( $unset, GENESIS_CPT_ARCHIVE_SETTINGS_FIELD_PREFIX . $post_type );
			}
		}
		// No options, let's setup some defaults.
		else {
			if ( isset( $settings[$single_key] ) ) {
				// Unset $single_key because this is stored in theme settings, not cpt archive settings.
				unset( $settings[$single_key] );
			}
			// genesis_update_settings( $settings, GENESIS_CPT_ARCHIVE_SETTINGS_FIELD_PREFIX . $post_type );
		}

	}

	// Register the archive settings.
	add_action( 'customize_register', function() use ( $post_type, $settings ) {
		mai_register_archive_settings( $post_type, $settings );
	}, 22 );

}

function mai_register_archive_settings( $post_type, $args ) {

	// Bail if Kirki isn't running.
	if ( ! class_exists( 'Kirki' ) ) {
		return;
	}

	// Bail if we don't have a post type.
	if ( ! post_type_exists( $post_type ) ) {
		return;
	}

	/* ************** *
	 * Kirki settings *
	 * ************** */

	$post_type_object = get_post_type_object( $post_type );

	// Congifure Kirki.
	$config  = sprintf( 'mai_%s_archive_settings', $post_type );
	$option  = sprintf( 'genesis-cpt-archive-settings-%s', $post_type );
	$section = $config;

	Kirki::add_config( $config, array(
		'capability'  => 'edit_theme_options',
		'option_type' => 'option',
		'option_name' => $option,
	) );


	// Mai {post type name} Settings.
	Kirki::add_section( $config, array(
		'title'      => sprintf( __( 'Mai %s Settings', 'mai-pro-engine' ), $post_type_object->labels->singular_name ),
		'panel'      => '',
		'priority'   => 35,
		'capability' => 'edit_theme_options',
	) );

	// Single layout.
	$single_key = sprintf( 'layout_%s', $post_type );
	if ( isset( $args[$single_key]  )) {

		Kirki::add_field( 'mai_settings', array(
			'type'     => 'radio-image',
			'settings' => $single_key,
			'label'    => sprintf( __( '%s - Single', 'mai-pro-engine' ), $post_type_object->label ),
			'section'  => $section,
			'default'  => $args[$single_key],
			'priority' => 10,
			'choices'  => _mai_kirki_get_layout_images_with_site_default_config(),
		) );

	}

	// Archive settings description.
	Kirki::add_field( $config, array(
		'type'     => 'custom',
		'settings' => 'archive_settings_description',
		'label'    => __( 'Archive Settings', 'mai-pro-engine' ),
		'section'  => $section,
		'default'  => __( 'The settings below affect all archive pages related to this post type.', 'mai-pro-engine' ),
		'priority' => 10,
	) );

	// Archive Layout.
	if ( isset( $args['layout'] ) ) {

		Kirki::add_field( $config, array(
			'type'     => 'radio-image',
			'settings' => 'layout',
			'label'    => sprintf( __( '%s - Archive', 'mai-pro-engine' ), $post_type_object->label ),
			'section'  => $section,
			'default'  => $args['layout'],
			'priority' => 10,
			'choices'  => _mai_kirki_get_layout_images_with_archives_default_config(),
		) );

	}

	// Columns.
	if ( isset( $args['columns'] ) ) {

		Kirki::add_field( $config, array(
			'type'        => 'select',
			'settings'    => 'columns',
			'label'       => __( 'Columns', 'mai-pro-engine' ),
			'description' => __( 'Display content in multiple columns.', 'mai-pro-engine' ),
			'section'     => $section,
			'default'     => $args['columns'],
			'priority'    => 10,
			'multiple'    => 1,
			'choices'     => array(
				'1' => __( 'None', 'mai-pro-engine' ),
				'2' => __( '2', 'mai-pro-engine' ),
				'3' => __( '3', 'mai-pro-engine' ),
				'4' => __( '4', 'mai-pro-engine' ),
				'6' => __( '6', 'mai-pro-engine' ),
			),
		) );

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
			Kirki::add_field( $config, array(
				'type'     => 'select',
				'settings' => 'content_archive',
				'label'    => __( 'Content', 'genesis' ),
				'section'  => $section,
				'default'  => $args['content_archive'],
				'priority' => 10,
				'multiple' => 1,
				'choices'  => $content_archive_choices,
			) );

			if ( isset( $args['content_archive_limit'] ) ) {

				// Content Limit.
				Kirki::add_field( $config, array(
					'type'        => 'number',
					'settings'    => 'content_archive_limit',
					'label'       => __( 'Limit content to how many characters?', 'mai-pro-engine' ),
					'description' => __( '(0 for no limit)', 'mai-pro-engine' ),
					'section'     => $section,
					'default'     => $args['content_archive_limit'],
					'priority'    => 10,
					'choices'     => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					),
					'active_callback' => array(
						'setting'  => 'content_archive',
						'operator' => '!=',
						'value'    => 'none',
					),
				) );

			}

		}

	}

	// Featured Image.
	if ( isset( $args['content_archive_thumbnail'] ) && post_type_supports( $post_type, 'thumbnail' ) ) {

		// Featured Image.
		Kirki::add_field( $config, array(
			'type'     => 'switch',
			'settings' => 'content_archive_thumbnail',
			'label'    => __( 'Featured Image', 'genesis' ),
			'section'  => $section,
			'default'  => genesis_get_option( 'content_archive_limit' ),
			'priority' => 10,
			'choices'  => array(
				1 => esc_attr__( 'Enable', 'mai-pro-engine' ),
				0 => esc_attr__( 'Disable', 'mai-pro-engine' ),
			),
		) );

		// Image Location.
		if ( isset( $args['image_location'] ) ) {

			Kirki::add_field( $config, array(
				'type'     => 'select',
				'settings' => 'image_location',
				'label'    => __( 'Image Location', 'genesis' ),
				'section'  => $section,
				'default'  => genesis_get_option( 'image_location' ),
				'priority' => 10,
				'multiple' => 1,
				'choices'  => array(
					'background'     => __( 'Background Image', 'mai-pro-engine' ),
					'before_entry'   => __( 'Before Entry', 'mai-pro-engine' ),
					'before_title'   => __( 'Before Title', 'mai-pro-engine' ),
					'after_title'    => __( 'After Title', 'mai-pro-engine' ),
					'before_content' => __( 'Before Content', 'mai-pro-engine' ),
				),
				'active_callback' => array(
					'setting'  => 'content_archive_thumbnail',
					'operator' => '!=',
					'value'    => 'none',
				),
			) );

		}

		// Image Size.
		if ( isset( $args['image_size'] ) ) {

			Kirki::add_field( $config, array(
				'type'     => 'select',
				'settings' => 'image_size',
				'label'    => __( 'Image Size', 'genesis' ),
				'section'  => $section,
				'default'  => genesis_get_option( 'image_size' ),
				'priority' => 10,
				'multiple' => 1,
				'choices'  => _mai_kirki_get_image_sizes_config(),
				'active_callback' => array(
					'setting'  => 'content_archive_thumbnail',
					'operator' => '==',
					'value'    => 1,
				),
			) );

		}

		// Image Alignment.
		if ( isset( $args['image_alignment'] ) ) {

			Kirki::add_field( $config, array(
				'type'     => 'select',
				'settings' => 'image_alignment',
				'label'    => __( 'Image Alignment', 'genesis' ),
				'section'  => $section,
				'default'  => genesis_get_option( 'image_alignment' ),
				'priority' => 10,
				'multiple' => 1,
				'choices'  => array(
					''            => __( '- None -', 'genesis' ),
					'aligncenter' => __( 'Center', 'genesis' ),
					'alignleft'   => __( 'Left', 'genesis' ),
					'alignright'  => __( 'Right', 'genesis' ),
				),
				'active_callback' => array(
					'setting'  => 'content_archive_thumbnail',
					'operator' => '==',
					'value'    => 1,
				),
			) );

		}

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

		Kirki::add_field( $config, array(
			'type'     => 'multicheck',
			'settings' => 'remove_meta',
			'label'    => __( 'Entry Meta', 'mai-pro-engine' ),
			'section'  => $section,
			'priority' => 10,
			'choices'  => $remove_meta_choices,
		) );

	}

	// Posts Per Page.
	if ( isset( $args['posts_per_page'] ) ) {

		Kirki::add_field( $config, array(
			'type'        => 'number',
			'settings'    => 'posts_per_page',
			'label'       => __( 'Entries Per Page', 'mai-pro-engine' ),
			'description' => __( 'The max number of posts to show, per page.', 'mai-pro-engine' ),
			'section'     => $section,
			'default'     => get_option( 'posts_per_page' ),
			'priority'    => 10,
			'choices'     => array(
				'min'  => 0,
				'max'  => 1000,
				'step' => 1,
			),
		) );

	}

	// Posts Nav.
	if ( isset( $args['posts_nav'] ) ) {

		Kirki::add_field( $config, array(
			'type'        => 'radio',
			'settings'    => 'posts_nav',
			'label'       => __( 'Shop Pagination', 'genesis' ),
			'section'     => $section,
			'default'     => genesis_get_option( 'posts_nav' ),
			'priority'    => 10,
			'multiple'    => 1,
			'choices'     => array(
				'prev-next' => __( 'Previous / Next', 'genesis' ),
				'numeric'   => __( 'Numeric', 'genesis' ),
			),
		) );

	}

}
