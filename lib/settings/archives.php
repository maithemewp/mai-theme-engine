<?php

add_action( 'init', 'mai_woocommerce_archive_settings', 30 );
function mai_woocommerce_archive_settings() {
	if ( ! class_exists( 'WooCommerce') ) {
		return;
	}
	mai_archive_settings( 'product', array(
		'columns'                   => 3,
		'content_archive_thumbnail' => 1,
		'layout_single'             => 'md-content',
		'layout_archive'            => 'full-width-content',
		'posts_per_page'            => 12,
		'posts_nav'                 => 'prev-next',
	) );
}

/**
 * Define the archive settings for a post type.
 * Args should either be false (to disable) or provide the default setting (to enable).
 *
 * Needs to run before 'wp_loaded' hook.
 * A good hook may be 'init' with a late priority (after 10) to ensure post_types are registered.
 *
 * @param   string  $post_type  The post type name.
 * @param   array   $args       The args to enable, with their default value. Include only the fields you want.
 *
 * @return  void
 */
function mai_archive_settings( $post_type, $args ) {

	add_action( 'wp_loaded', function() use ( $post_type, $args ) {

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

		$single_key  = sprintf( 'layout_single_%s', $post_type );
		$archive_key = sprintf( 'layout_archive_%s', $post_type );

		// Defaults.
		$defaults = array(
			$single_key                 => '',
			$archive_key                => '',
			'columns'                   => 1,
			'content_archive'           => false,
			'content_archive_limit'     => false,
			'more_link'                 => false,
			'more_link_text'            => false,
			'content_archive_thumbnail' => false,
			'image_location'            => false,
			'image_size'                => false,
			'image_alignment'           => false,
			'remove_meta'               => false,
			'posts_per_page'            => false,
			'posts_nav'                 => false,
		);

		// Parse.
		$args = wp_parse_args( $args, $defaults );

		// Sanitize.
		$args = array(
			$args[$single_key]                 => sanitize_key( $args[$single_key] ),
			$args[$archive_key]                => sanitize_key( $args[$archive_key] ),
			$args['columns']                   => (string) absint( $args['columns'] ),
			$args['content_archive']           => $args['content_archive'] ? sanitize_key( $args['content_archive'] ) : false,
			$args['content_archive_limit']     => $args['content_archive_limit'] ? sanitize_key( $args['content_archive_limit'] ) : false,
			$args['content_archive_thumbnail'] => ( $args['content_archive_thumbnail'] && post_type_supports( $post_type, 'thumbnail' ) ) ? sanitize_key( $args['content_archive_thumbnail'] ) : false,
			$args['image_location']            => $args['image_location'] ? sanitize_key( $args['image_location'] ) : false,
			$args['image_size']                => $args['image_size'] ? sanitize_key( $args['image_size'] ) : false,
			$args['image_alignment']           => $args['image_alignment'] ? sanitize_key( $args['image_alignment'] ) : false,
			$args['more_link']                 => $args['more_link'] ? sanitize_key( $args['more_link'] ) : false,
			$args['more_link_text']            => $args['more_link_text'] ? sanitize_key( $args['more_link_text'] ) : false,
			$args['remove_meta']               => ( $args['remove_meta'] && ( post_type_supports( $post_type, 'genesis-entry-meta-before-content' ) || post_type_supports( $post_type, 'genesis-entry-meta-after-content' ) ) ) ? sanitize_key( $args['remove_meta'] ) : false,
			$args['posts_per_page']            => $args['posts_per_page'] ? absint( $args['posts_per_page'] ) : false,
			$args['posts_nav']                 => $args['posts_nav'] ? sanitize_key( $args['posts_nav'] ) : false,
		);

	});

	// Register the archive settings.
	add_action( 'customize_register', function() use ( $post_type, $args ) {
		mai_register_archive_settings( $post_type, $args );
	}, 20 );

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

// THESE ARE NOT SHOWING UP IN "Mai Site Layouts" Section!?!?!?

	$single_key  = sprintf( 'layout_single_%s', $post_type );
	$archive_key = sprintf( 'layout_archive_%s', $post_type );

	if ( $args[$single_key] ) {

		// Single layout
		Kirki::add_field( 'mai_settings', array(
			'type'     => 'select',
			'settings' => $single_key,
			'label'    => sprintf( __( '%s Single Layout', 'mai-pro-engine' ), $post_type_object->labels->singular_name ),
			'section'  => 'mai_site_layouts',
			'default'  => $args[$single_key],
			'priority' => 10,
			'choices'  => array_merge( array( '' => __( '- Site Default -', 'mai-pro-engine' ) ), genesis_get_layouts_for_customizer() ),
		) );

	}

	if ( $args[$archive_key] ) {

		// Archive Layout
		Kirki::add_field( 'mai_settings', array(
			'type'     => 'select',
			'settings' => $archive_key,
			'label'    => sprintf( __( '%s Archive Layout', 'mai-pro-engine' ), $post_type_object->labels->singular_name ),
			'section'  => 'mai_site_layouts',
			'default'  => $args[$archive_key],
			'priority' => 10,
			'choices'  => array_merge( array( '' => __( '- Archives Default -', 'mai-pro-engine' ) ), genesis_get_layouts_for_customizer() ),
		) );

	}

	$config  = sprintf( 'mai_%s_archive_settings', $post_type );
	$option  = sprintf( 'genesis-cpt-archive-settings-%s', $post_type );
	$section = $config;

	Kirki::add_config( $config, array(
		'capability'  => 'edit_theme_options',
		'option_type' => 'option',
		'option_name' => $option,
	) );


	Kirki::add_section( $config, array(
		'title'      => sprintf( __( 'Mai %s Settings', 'mai-pro-engine' ), $post_type_object->labels->singular_name ),
		'panel'      => '',
		'priority'   => 35,
		'capability' => 'edit_theme_options',
	) );


	if ( $args['columns'] ) {

		// Columns
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

	if ( $args['content_archive'] ) {

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

			// Content
			Kirki::add_field( $config, array(
				'type'     => 'select',
				'settings' => 'content_archive',
				'label'    => __( 'Content', 'genesis' ),
				'section'  => $section,
				'default'  => genesis_get_option( 'content_archive' ),
				'priority' => 10,
				'multiple' => 1,
				'choices'  => $content_archive_choices,
			) );

			// Content Limit
			Kirki::add_field( $config, array(
				'type'        => 'number',
				'settings'    => 'content_archive_limit',
				'label'       => __( 'Limit content to how many characters?', 'mai-pro-engine' ),
				'description' => __( '(0 for no limit)', 'mai-pro-engine' ),
				'section'     => $section,
				'default'     => genesis_get_option( 'content_archive_limit' ),
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

	if ( $args['content_archive_thumbnail'] && post_type_supports( $post_type, 'thumbnail' ) ) {

		// Include the Featured Image
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

		if ( $args['image_location'] ) {

			// Image Location
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

		if ( $args['image_size'] ) {

			// Image Size
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

		if ( $args['image_alignment'] ) {

			// Image Alignment
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

	if ( $args['remove_meta'] && post_type_supports( $post_type, 'genesis-entry-meta-before-content' ) && post_type_supports( $post_type, 'genesis-entry-meta-after-content' ) ) {

		$remove_meta_choices = array();

		if ( post_type_supports( $post_type, 'genesis-entry-meta-before-content' ) ) {
			$remove_meta_choices['post_info'] = __( 'Remove Post Info', 'mai-pro-engine' );
		}

		if ( post_type_supports( $post_type, 'genesis-entry-meta-after-content' ) ) {
			$remove_meta_choices['post_meta'] = __( 'Remove Post Meta', 'mai-pro-engine' );
		}

		// Entry Meta
		Kirki::add_field( $config, array(
			'type'     => 'multicheck',
			'settings' => 'remove_meta',
			'label'    => __( 'Entry Meta', 'mai-pro-engine' ),
			'section'  => $section,
			'priority' => 10,
			'choices'  => $remove_meta_choices,
		) );

	}

	if ( $args['posts_per_page'] ) {

		// Posts Per Page
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

	if ( $args['posts_nav'] ) {

		// Posts Nav
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
