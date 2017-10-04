<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Setup CPT's customizer and Archive Settings fields.
 *
 * Possible keys/settings:
 *
 * 'banner_id'
 * 'hide_banner'
 * 'banner_disable_{post_type}           (saves to 'genesis-settings' option)
 * 'layout_{post_type}'                  (saves to 'genesis-settings' option)
 * 'layout'
 * 'singular_image_{post_type}'          (saves to 'genesis-settings' option)
 * 'remove_meta_{post_type}'             (saves to 'genesis-settings' option)
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
	 *
	 * @return  array  Post types  array( 'name' => object )
	 */
	$post_types = mai_get_cpt_settings_post_types();

	// Bail if no post types.
	if ( ! $post_types ) {
		return;
	}

	// Loop through the post types.
	foreach ( $post_types as $post_type => $object ) {
		$settings = new Mai_CPT_Settings( $post_type );
	}
}

/**
 * Main plugin class.
 *
 * @package Mai_Pro_Engine
 */
class Mai_CPT_Settings {

	public static $post_type;
	public static $settings_field;
	public static $genesis_settings;
	public static $section_id;
	public static $post_type_object;
	public static $prefix;
	public static $banner_featured_image_key;
	public static $banner_disable_key;
	public static $banner_disable_taxonomies_key;
	public static $layout_key;
	public static $singular_image_key;
	public static $remove_meta_key;
	public static $settings_defaults;
	public static $post_type_supports;

	public function __construct( $post_type ) {

		// Bail if we don't have a post type.
		if ( ! post_type_exists( $post_type ) ) {
			return;
		}

		$this->post_type                     = $post_type;
		$this->post_type_supports            = get_all_post_type_supports( $this->post_type );
		$this->settings_field                = GENESIS_CPT_ARCHIVE_SETTINGS_FIELD_PREFIX . $this->post_type;
		$this->genesis_settings              = 'genesis-settings';
		$this->section_id                    = sprintf( 'mai_%s_cpt_settings', $this->post_type );
		$this->post_type_object              = get_post_type_object( $this->post_type );
		$this->prefix                        = sprintf( '%s_', $this->post_type );

		// Build post_type specific keys (for genesis-settings).
		$this->banner_featured_image_key     = sprintf( 'banner_featured_image_%s', $this->post_type );
		$this->banner_disable_key            = sprintf( 'banner_disable_%s', $this->post_type );
		$this->banner_disable_taxonomies_key = sprintf( 'banner_disable_taxonomies_%s', $this->post_type );
		$this->layout_key                    = sprintf( 'layout_%s', $this->post_type );
		$this->singular_image_key            = sprintf( 'singular_image_%s', $this->post_type );
		$this->remove_meta_key               = sprintf( 'remove_meta_%s', $this->post_type );

		// Settings defaults array.
		$this->settings = array(
			'banner_id'                          => true,
			$this->banner_featured_image_key     => true,
			'hide_banner'                        => true,
			$this->banner_disable_key            => true,
			$this->banner_disable_taxonomies_key => true,
			$this->layout_key                    => true,
			'layout'                             => true,
			$this->singular_image_key            => true,
			$this->remove_meta_key               => true,
			'enable_content_archive_settings'    => true,
			'columns'                            => true,
			'content_archive'                    => true,
			'content_archive_limit'              => true,
			'content_archive_thumbnail'          => true,
			'image_location'                     => true,
			'image_size'                         => true,
			'image_alignment'                    => true,
			'more_link'                          => true,
			'more_link_text'                     => true,
			'remove_meta'                        => true,
			'posts_per_page'                     => true,
			'posts_nav'                          => true,
		);

		// If no entry meta support.
		if ( ! ( isset( $this->post_type_supports['genesis-entry-meta-before-content'] ) || isset( $this->post_type_supports['genesis-entry-meta-after-content'] ) ) ) {
			$this->settings['remove_meta']               = false;
			$this->settings[ $this->remove_meta_key ]    = false;
		}

		// If no editor or no excerpt support.
		if ( ! ( isset( $this->post_type_supports['editor'] ) || isset( $this->post_type_supports['excerpt'] ) ) ) {
			$this->settings['content_archive']           = false;
			$this->settings['content_archive_limit']     = false;
		}

		// If no featured image support.
		if ( ! isset( $this->post_type_supports['thumbnail'] ) ) {
			$this->settings[ $this->singular_image_key ] = false;
			$this->settings['content_archive_thumbnail'] = false;
			$this->settings['image_location']            = false;
			$this->settings['image_size']                = false;
			$this->settings['image_alignment']           = false;
		}

		/**
		 * Filter to enabled/disable settings for each post type.
		 * This is great for adding CPT support for specific plugins like Woo/EDD/etc.
		 */
		$settings = apply_filters( 'mai_cpt_settings', $this->settings, $this->post_type );

		/**
		 * Add Mai CPT support here.
		 * This should happen here, internally only. Please don't add 'mai-cpt-settings' support to CPT's manually.
		 */
		add_post_type_support( $this->post_type, 'mai-cpt-settings' );

		// Filters.
		add_filter( "pre_update_option_{$this->settings_field}", array( $this, 'update_setting' ), 10, 2 );
		add_filter( 'genesis_options', array( $this, 'filter_options' ), 10, 2 );

		// Actions.
		add_action( 'customize_register', array( $this, 'customizer_settings' ), 22 );
	}

	/**
	 * This filter makes sure our custom settings are not wiped out when updating via CPT > Archive Settings.
	 * In 1.1.2 we were made aware of a critical bug where our custom settings were cleared anytime
	 * a user would hit "Save" in CPT > Archive Settings.
	 *
	 * @since   1.1.5
	 *
	 * @return  array
	 */
	function update_setting( $new_value, $old_value ) {

		// Bail if this isn't happening from a form submission page.
		if ( ! isset( $_POST ) || empty( $_POST ) ) {
			return $new_value;
		}

		// Bail if this isn't happening on a page that's submitting a 'genesis-settings' form.
		if ( ! isset( $_POST[ $this->settings_field ] ) || empty( $_POST[ $this->settings_field ] ) ) {
			return $new_value;
		}

		// Get the submitted and existing settings values.
		$values   = $_POST[ $this->settings_field ];
		$settings = get_option( $this->settings_field );

		// Loop through em.
		foreach ( (array) $settings as $key => $value ) {
			/**
			 * If a custom setting is not part of the $_POST submission,
			 * we need to add to the $new_value array it so it's not lost.
			 */
			if ( ! isset( $values[ $key ] ) ) {
				$new_value[ $key ] = genesis_get_cpt_option( $key, $this->post_type );
			}
		}

		return $new_value;
	}

	/**
	 * Filter the default options, adding our custom post type settings.
	 *
	 * @since   1.1.0
	 *
	 * @param   array   $options  The genesis options.
	 * @param   string  $setting  The setting key/name.
	 *
	 * @return  array   The modified options.
	 */
	function filter_options( $options, $setting ) {

		// Bail if not this post_type's settings.
		if ( $$this->settings_field !== $setting ) {
			return $options;
		}

		// Default options.
		foreach ( (array) mai_get_default_cpt_options( $this->post_type ) as $key => $value ) {
			if ( ! isset( $options[$key] ) ) {
				$options[$key] = $value;
			}
		}

		// Return the modified options.
		return $options;
	}

	/**
	 * Register the customizer settings sections and fields.
	 *
	 * @param   object  $wp_customize  The customizeer object.
	 *
	 * @return  void.
	 */
	function customizer_settings( $wp_customize ) {

		// Mai {post type name} Settings.
		$wp_customize->add_section(
			$this->section_id,
			array(
				'title'    => sprintf( __( 'Mai %s Settings', 'mai-pro-engine' ), $this->post_type_object->label ),
				'priority' => '39',
			)
		);

		// Banner break.
		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $this->settings_field, 'cpt_banner_break' ),
			array(
				'default' => '',
				'type'    => 'option',
			)
		);
		$wp_customize->add_control(
			new Mai_Customize_Control_Break( $wp_customize,
				$this->prefix . 'cpt_banner_break',
				array(
					'label'           => __( 'Banner Area', 'mai-pro-engine' ),
					'section'         => $this->section_id,
					'settings'        => false,
					'active_callback' => function() use ( $wp_customize ) {
						return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $this->genesis_settings );
					},
				)
			)
		);

		if ( $this->settings['banner_id'] ) {

			// Banner Image
			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $this->settings_field, 'banner_id' ),
				array(
					'default'           => absint( mai_get_default_cpt_option( 'banner_id' ) ),
					'type'              => 'option',
					'sanitize_callback' => 'absint',
				)
			);
			$wp_customize->add_control(
				new WP_Customize_Media_Control( $wp_customize,
				$this->prefix . 'banner_id',
				array(
					'label'           => __( 'Default Banner Image', 'mai-pro-engine' ),
					'description'     => __( 'This will be the default banner image for archives and single entries.', 'mai-pro-engine' ),
					'section'         => $this->section_id,
					'settings'        => _mai_customizer_get_field_name( $this->settings_field, 'banner_id' ),
					'active_callback' => function() use ( $wp_customize ) {
						return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $this->genesis_settings );
					},
				)
			) );

		}

		// Disable banner, heading only.
		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $this->settings_field, 'cpt_hide_banner_customizer_heading' ),
			array(
				'default' => '',
				'type'    => 'option',
			)
		);
		$wp_customize->add_control(
			new Mai_Customize_Control_Content( $wp_customize,
				$this->prefix . 'cpt_hide_banner_customizer_heading',
				array(
					'label'           => __( 'Hide banner on (archive/single)', 'mai-pro-engine' ),
					'section'         => $this->section_id,
					'settings'        => false,
					'active_callback' => function() use ( $wp_customize ) {
						return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $this->genesis_settings );
					},
				)
			)
		);

		// Hide banner CPT archive.
		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $this->settings_field, 'hide_banner' ),
			array(
				'default'           => mai_sanitize_one_zero( mai_get_default_cpt_option( 'hide_banner' ) ),
				'type'              => 'option',
				'sanitize_callback' => 'mai_sanitize_one_zero',
			)
		);
		$wp_customize->add_control(
			$this->prefix . 'hide_banner',
			array(
				'label'           => __( 'Hide banner on main archive', 'mai-pro-engine' ),
				'section'         => $this->section_id,
				'settings'        => _mai_customizer_get_field_name( $this->settings_field, 'hide_banner' ),
				'type'            => 'checkbox',
				'active_callback' => function() use ( $wp_customize ) {
					return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $this->genesis_settings );
				},
			)
		);

		// Disable banner singular (saves to genesis-settings option).
		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $this->genesis_settings, $this->banner_disable_key ),
			array(
				'default'           => mai_sanitize_one_zero( mai_get_default_option( $this->banner_disable_key ) ),
				'type'              => 'option',
				'sanitize_callback' => 'mai_sanitize_one_zero',
			)
		);
		$wp_customize->add_control(
			$this->prefix . $this->banner_disable_key,
			array(
				'label'           => __( 'Hide banner on single entries', 'mai-pro-engine' ),
				'section'         => $this->section_id,
				'settings'        => _mai_customizer_get_field_name( $this->genesis_settings, $this->banner_disable_key ),
				'type'            => 'checkbox',
				'active_callback' => function() use ( $wp_customize ) {
					return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $this->genesis_settings );
				},
			)
		);

		// Disable banner taxonomies (saves to genesis-settings option).
		$disable_taxonomies = array();
		$taxonomies         = get_object_taxonomies( $this->post_type, 'objects' );
		if ( $taxonomies ) {
			foreach ( $taxonomies as $taxo ) {
				/**
				 * If taxo is not public, or is registered to more than one object.
				 * We may need to account for these taxos later, but for now
				 * this seems like an edge case. Most taxos are only registered to 1 object.
				 */
				if ( ! $taxo->public || ( count( (array) $taxo->object_type ) > 1 ) ) {
					continue;
				}
				$disable_taxonomies[$taxo->name] = $taxo->label;
			}
			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $this->genesis_settings, $this->banner_disable_taxonomies_key ),
				array(
					'default'           => _mai_customizer_multicheck_sanitize_key( mai_get_default_option( $this->banner_disable_taxonomies_key ) ),
					'type'              => 'option',
					'sanitize_callback' => '_mai_customizer_multicheck_sanitize_key',
				)
			);
			$wp_customize->add_control(
				new Mai_Customize_Control_Multicheck( $wp_customize,
					$this->prefix . $this->banner_disable_taxonomies_key,
					array(
						'label'           => __( 'Hide banner on (taxonomies)', 'mai-pro-engine' ),
						'section'         => $this->section_id,
						'settings'        => _mai_customizer_get_field_name( $this->genesis_settings, $this->banner_disable_taxonomies_key ),
						'choices'         => $disable_taxonomies,
						'active_callback' => function() use ( $wp_customize ) {
							return _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $this->genesis_settings );
						},
					)
				)
			);
		}

		// Banner featured image, heading only.
		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $this->settings_field, 'banner_featured_image_heading' ),
			array(
				'default' => '',
				'type'    => 'option',
			)
		);
		$wp_customize->add_control(
			new Mai_Customize_Control_Content( $wp_customize,
				$this->prefix . 'banner_featured_image_heading',
				array(
					'label'           => __( 'Featured Image on (single entries)', 'mai-pro-engine' ),
					'section'         => $this->section_id,
					'settings'        => false,
					'active_callback' => function() use ( $wp_customize ) {
						return ( (bool) ! $wp_customize->get_setting( _mai_customizer_get_field_name( $this->genesis_settings, $this->banner_disable_key ) )->value() && _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $this->genesis_settings ) );
					},
				)
			)
		);

		// Banner featured image (saves to genesis-settings option).
		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $this->genesis_settings, $this->banner_featured_image_key ),
			array(
				'default'           => mai_sanitize_one_zero( mai_get_default_option( $this->banner_featured_image_key ) ),
				'type'              => 'option',
				'sanitize_callback' => 'mai_sanitize_one_zero',
			)
		);
		$wp_customize->add_control(
			$this->prefix . $this->banner_featured_image_key,
			array(
				'label'           => __( 'Use featured image as banner image', 'mai-pro-engine' ),
				'section'         => $this->section_id,
				'settings'        => _mai_customizer_get_field_name( $this->genesis_settings, $this->banner_featured_image_key ),
				'priority'        => 10,
				'type'            => 'checkbox',
				'active_callback' => function() use ( $wp_customize ) {
					return ( (bool) ! $wp_customize->get_setting( _mai_customizer_get_field_name( $this->genesis_settings, $this->banner_disable_key ) )->value() && _mai_customizer_is_banner_area_enabled_globally( $wp_customize, $this->genesis_settings ) );
				},
			)
		);

		if ( $this->settings['layout'] && $settings[$this->single_layout_key] ) {

			// Layouts break.
			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $this->settings_field, 'cpt_archive_layouts_break' ),
				array(
					'default' => '',
					'type'    => 'option',
				)
			);
			$wp_customize->add_control(
				new Mai_Customize_Control_Break( $wp_customize,
					$this->prefix . 'cpt_archive_layouts_break',
					array(
						'label'    => __( 'Layouts', 'mai-pro-engine' ),
						'section'  => $this->section_id,
						'settings' => false,
					)
				)
			);

		}

		// Archive Layout.
		if ( $this->settings['layout'] ) {

			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $this->settings_field, 'layout' ),
				array(
					'default'           => sanitize_key( mai_get_default_cpt_option( 'layout' ) ),
					'type'              => 'option',
					'sanitize_callback' => 'sanitize_key',
				)
			);
			$wp_customize->add_control(
				$this->prefix . 'layout',
				array(
					'label'    => __( 'Archives', 'mai-pro-engine' ),
					'section'  => $this->section_id,
					'settings' => _mai_customizer_get_field_name( $this->settings_field, 'layout' ),
					'type'     => 'select',
					'choices'  => array_merge( array( '' => __( '- Archives Default -', 'mai-pro-engine' ) ), genesis_get_layouts_for_customizer() ),
				)
			);

		}

		// Single layout (saves to genesis-settings option).
		if ( $settings[ $this->single_layout_key ] ) {

			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $this->genesis_settings, $this->single_layout_key ),
				array(
					'default'           => sanitize_key( mai_get_default_option( $this->single_layout_key ) ),
					'type'              => 'option',
					'sanitize_callback' => 'sanitize_key',
				)
			);
			$wp_customize->add_control(
				$this->prefix . $this->single_layout_key,
				array(
					'label'    => __( 'Single Entries', 'mai-pro-engine' ),
					'section'  => $this->section_id,
					'settings' => _mai_customizer_get_field_name( $this->genesis_settings, $this->single_layout_key ),
					'type'     => 'select',
					'choices'  => array_merge( array( '' => __( '- Site Default -', 'mai-pro-engine' ) ), genesis_get_layouts_for_customizer() ),
				)
			);

		}

		if ( ( $settings[$this->singular_image_key] || $settings[$this->remove_meta_single_key] ) && ( post_type_supports( $this->post_type, 'thumbnail' ) || ( post_type_supports( $this->post_type, 'genesis-entry-meta-before-content' ) || post_type_supports( $this->post_type, 'genesis-entry-meta-after-content' ) ) ) ) {

			// Single Entry settings break.
			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $this->settings_field, 'cpt_singular_entries_break' ),
				array(
					'default' => '',
					'type'    => 'option',
				)
			);
			$wp_customize->add_control(
				new Mai_Customize_Control_Break( $wp_customize,
					$this->prefix . 'cpt_singular_entries_break',
					array(
						'label'    => __( 'Single Entries', 'mai-pro-engine' ),
						'section'  => $this->section_id,
						'settings' => false,
					)
				)
			);

		}

		// Featured Image.
		if ( $settings[$this->singular_image_key] && post_type_supports( $this->post_type, 'thumbnail' ) ) {

			// Featured Image heading.
			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $this->genesis_settings, 'cpt_featured_image_customizer_heading' ),
				array(
					'default' => '',
					'type'    => 'option',
				)
			);
			$wp_customize->add_control(
				new Mai_Customize_Control_Content( $wp_customize,
					$this->prefix . 'cpt_featured_image_customizer_heading',
					array(
						'label'    => __( 'Featured Image', 'mai-pro-engine' ),
						'section'  => $this->section_id,
						'settings' => false,
					)
				)
			);

			// Featured Image (saves to genesis-settings option).
			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $this->genesis_settings, $this->singular_image_key ),
				array(
					'default'           => mai_sanitize_one_zero( mai_get_default_option( $this->singular_image_key ) ),
					'type'              => 'option',
					'sanitize_callback' => 'mai_sanitize_one_zero',
				)
			);
			$wp_customize->add_control(
				$this->prefix . $this->singular_image_key,
				array(
					'label'    => __( 'Display the Featured Image', 'mai-pro-engine' ),
					'section'  => $this->section_id,
					'settings' => _mai_customizer_get_field_name( $this->genesis_settings, $this->singular_image_key ),
					'type'     => 'checkbox',
				)
			);

		}

		// Entry Meta single (saves to genesis-settings option).
		if ( $settings[ $this->remove_meta_single_key ] && ( post_type_supports( $this->post_type, 'genesis-entry-meta-before-content' ) || post_type_supports( $this->post_type, 'genesis-entry-meta-after-content' ) ) ) {

			$remove_meta_choices = array();

			if ( post_type_supports( $this->post_type, 'genesis-entry-meta-before-content' ) ) {
				$remove_meta_choices['post_info'] = __( 'Remove Post Info', 'mai-pro-engine' );
			}

			if ( post_type_supports( $this->post_type, 'genesis-entry-meta-after-content' ) ) {
				$remove_meta_choices['post_meta'] = __( 'Remove Post Meta', 'mai-pro-engine' );
			}

			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $this->genesis_settings, $this->remove_meta_single_key ),
				array(
					'default'           =>  _mai_customizer_multicheck_sanitize_key( mai_get_default_option( $this->remove_meta_single_key ) ),
					'type'              => 'option',
					'sanitize_callback' => '_mai_customizer_multicheck_sanitize_key',
				)
			);
			$wp_customize->add_control(
				new Mai_Customize_Control_Multicheck( $wp_customize,
					$this->prefix . $this->remove_meta_single_key,
					array(
						'label'    => __( 'Entry Meta', 'mai-pro-engine' ),
						'section'  => $this->section_id,
						'settings' => _mai_customizer_get_field_name( $this->genesis_settings, $this->remove_meta_single_key ),
						'priority' => 10,
						'choices'  => $remove_meta_choices,
					)
				)
			);

		}

		// Archive settings break.
		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $this->settings_field, 'cpt_archives_break' ),
			array(
				'default' => '',
				'type'    => 'option',
			)
		);
		$wp_customize->add_control(
			new Mai_Customize_Control_Break( $wp_customize,
				$this->prefix . 'cpt_archives_break',
				array(
					'label'    => __( 'Archives', 'mai-pro-engine' ),
					'section'  => $this->section_id,
					'settings' => false,
				)
			)
		);

		// Enable Content Archive Settings.
		$wp_customize->add_setting(
			_mai_customizer_get_field_name( $this->settings_field, 'enable_content_archive_settings' ),
			array(
				'default'           => mai_sanitize_one_zero( mai_get_default_cpt_option( 'enable_content_archive_settings' ) ),
				'type'              => 'option',
				'sanitize_callback' => 'mai_sanitize_one_zero',
			)
		);
		$wp_customize->add_control(
			$this->prefix . 'enable_content_archive_settings',
			array(
				'label'    => __( 'Enable custom archive settings', 'mai-pro-engine' ),
				'section'  => $this->section_id,
				'settings' => _mai_customizer_get_field_name( $this->settings_field, 'enable_content_archive_settings' ),
				'priority' => 10,
				'type'     => 'checkbox',
			)
		);

		// Columns.
		if ( $this->settings['columns'] ) {

			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $this->settings_field, 'columns' ),
				array(
					'default'           => absint( mai_get_default_cpt_option( 'columns' ) ),
					'type'              => 'option',
					'sanitize_callback' => 'absint',
				)
			);
			$wp_customize->add_control(
				$this->prefix . 'columns',
				array(
					'label'    => __( 'Columns', 'mai-pro-engine' ),
					'section'  => $this->section_id,
					'settings' => _mai_customizer_get_field_name( $this->settings_field, 'columns' ),
					'priority' => 10,
					'type'     => 'select',
					'choices'  => array(
						1 => __( 'None', 'mai-pro-engine' ),
						2 => __( '2', 'mai-pro-engine' ),
						3 => __( '3', 'mai-pro-engine' ),
						4 => __( '4', 'mai-pro-engine' ),
						6 => __( '6', 'mai-pro-engine' ),
					),
					'active_callback' => function() use ( $wp_customize ) {
						return (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $this->settings_field, 'enable_content_archive_settings' ) )->value();
					},
				)
			);

		}

		// Content.
		if ( $this->settings['content_archive'] ) {

			$supports_editor  = $this->post_type_supports['editor'];
			$supports_excerpt = $this->post_type_supports['excerpt'];

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
					_mai_customizer_get_field_name( $this->settings_field, 'content_archive' ),
					array(
						'default'           => sanitize_key( mai_get_default_cpt_option( 'content_archive' ) ),
						'type'              => 'option',
						'sanitize_callback' => 'sanitize_key',
					)
				);
				$wp_customize->add_control(
					$this->prefix . 'content_archive',
					array(
						'label'           => __( 'Content', 'mai-pro-engine' ),
						'section'         => $this->section_id,
						'settings'        => _mai_customizer_get_field_name( $this->settings_field, 'content_archive' ),
						'priority'        => 10,
						'type'            => 'select',
						'choices'         => $content_archive_choices,
						'active_callback' => function() use ( $wp_customize ) {
							return (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $this->settings_field, 'enable_content_archive_settings' ) )->value();
						},
					)
				);

				// Content Limit.
				if ( $this->settings['content_archive_limit'] ) {

					$wp_customize->add_setting(
						_mai_customizer_get_field_name( $this->settings_field, 'content_archive_limit' ),
						array(
							'default'           => absint( mai_get_default_cpt_option( 'content_archive_limit' ) ),
							'type'              => 'option',
							'sanitize_callback' => 'absint',
						)
					);
					$wp_customize->add_control(
						$this->prefix . 'content_archive_limit',
						array(
							'label'           => __( 'Limit content to how many characters?', 'mai-pro-engine' ),
							'description'     => __( '(0 for no limit)', 'mai-pro-engine' ),
							'section'         => $this->section_id,
							'settings'        => _mai_customizer_get_field_name( $this->settings_field, 'content_archive_limit' ),
							'priority'        => 10,
							'type'            => 'number',
							'active_callback' => function() use ( $wp_customize ) {
								return (bool) ( $wp_customize->get_setting( _mai_customizer_get_field_name( $this->settings_field, 'enable_content_archive_settings' ) )->value() && ( 'none' != $wp_customize->get_setting( _mai_customizer_get_field_name( $this->settings_field, 'content_archive' ) )->value() ) );
							},
						)
					);

				}

			}

		}

		// Featured Image.
		if ( $this->settings['content_archive_thumbnail'] && $this->post_type_supports['thumbnail'] ) {

			// Archive featured image, heading only.
			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $this->settings_field, 'cpt_archives_featured_image_heading' ),
				array(
					'default' => '',
					'type'    => 'option',
				)
			);
			$wp_customize->add_control(
				new Mai_Customize_Control_Content( $wp_customize,
					$this->prefix . 'cpt_archives_featured_image_heading',
					array(
						'label'           => __( 'Featured Image', 'mai-pro-engine' ),
						'section'         => $this->section_id,
						'settings'        => false,
						'active_callback' => function() use ( $wp_customize ) {
							return (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $this->settings_field, 'enable_content_archive_settings' ) )->value();
						},
					)
				)
			);

			// Featured Image
			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $this->settings_field, 'content_archive_thumbnail' ),
				array(
					'default'           => mai_sanitize_one_zero( mai_get_default_cpt_option( 'content_archive_thumbnail' ) ),
					'type'              => 'option',
					'sanitize_callback' => 'mai_sanitize_one_zero',
				)
			);
			$wp_customize->add_control(
				$this->prefix . 'content_archive_thumbnail',
				array(
					'label'           => __( 'Display the Featured Image', 'mai-pro-engine' ),
					'section'         => $this->section_id,
					'settings'        => _mai_customizer_get_field_name( $this->settings_field, 'content_archive_thumbnail' ),
					'type'            => 'checkbox',
					'active_callback' => function() use ( $wp_customize ) {
						return (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $this->settings_field, 'enable_content_archive_settings' ) )->value();
					},
				)
			);

			// Image Location.
			if ( $this->settings['image_location'] ) {

				$wp_customize->add_setting(
					_mai_customizer_get_field_name( $this->settings_field, 'image_location' ),
					array(
						'default'           => sanitize_key( mai_get_default_cpt_option( 'image_location' ) ),
						'type'              => 'option',
						'sanitize_callback' => 'sanitize_key',
					)
				);
				$wp_customize->add_control(
					$this->prefix . 'image_location',
					array(
						'label'    => __( 'Image Location', 'genesis' ),
						'section'  => $this->section_id,
						'settings' => _mai_customizer_get_field_name( $this->settings_field, 'image_location' ),
						'priority' => 10,
						'type'     => 'select',
						'choices'  => array(
							'background'     => __( 'Background Image', 'mai-pro-engine' ),
							'before_entry'   => __( 'Before Entry', 'mai-pro-engine' ),
							'before_title'   => __( 'Before Title', 'mai-pro-engine' ),
							'after_title'    => __( 'After Title', 'mai-pro-engine' ),
							'before_content' => __( 'Before Content', 'mai-pro-engine' ),
						),
						'active_callback' => function() use ( $wp_customize ) {
							return (bool) ( $wp_customize->get_setting( _mai_customizer_get_field_name( $this->settings_field, 'enable_content_archive_settings' ) )->value() && (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $this->settings_field, 'content_archive_thumbnail' ) )->value() );
						},
					)
				);

			}

			// Image Size.
			if ( $this->settings['image_size'] ) {

				$wp_customize->add_setting(
					_mai_customizer_get_field_name( $this->settings_field, 'image_size' ),
					array(
						'default'           => sanitize_key( mai_get_default_cpt_option( 'image_size' ) ),
						'type'              => 'option',
						'sanitize_callback' => 'sanitize_key',
					)
				);
				$wp_customize->add_control(
					$this->prefix . 'image_size',
					array(
						'label'           => __( 'Image Size', 'genesis' ),
						'section'         => $this->section_id,
						'settings'        => _mai_customizer_get_field_name( $this->settings_field, 'image_size' ),
						'priority'        => 10,
						'type'            => 'select',
						'choices'         => _mai_customizer_get_image_sizes_config(),
						'active_callback' => function() use ( $wp_customize, $settings_field ) {
							return (bool) ( $wp_customize->get_setting( _mai_customizer_get_field_name( $this->settings_field, 'enable_content_archive_settings' ) )->value() && (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $this->settings_field, 'content_archive_thumbnail' ) )->value() );
						},
					)
				);

			}

			// Image Alignment.
			if ( $this->settings['image_alignment'] ) {

				$wp_customize->add_setting(
					_mai_customizer_get_field_name( $this->settings_field, 'image_alignment' ),
					array(
						'default'           => sanitize_key( mai_get_default_cpt_option( 'image_alignment' ) ),
						'type'              => 'option',
						'sanitize_callback' => 'sanitize_key',
					)
				);
				$wp_customize->add_control(
					$this->prefix . 'image_alignment',
					array(
						'label'    => __( 'Image Alignment', 'genesis' ),
						'section'  => $this->section_id,
						'settings' => _mai_customizer_get_field_name( $this->settings_field, 'image_alignment' ),
						'priority' => 10,
						'type'     => 'select',
						'choices'  => array(
							''            => __( '- None -', 'genesis' ),
							'aligncenter' => __( 'Center', 'genesis' ),
							'alignleft'   => __( 'Left', 'genesis' ),
							'alignright'  => __( 'Right', 'genesis' ),
						),
						'active_callback' => function() use ( $wp_customize ) {
							// Showing featured image and background is not image location.
							return (bool) ( $wp_customize->get_setting( _mai_customizer_get_field_name( $this->settings_field, 'enable_content_archive_settings' ) )->value() && $wp_customize->get_setting( _mai_customizer_get_field_name( $this->settings_field, 'content_archive_thumbnail' ) )->value() && ( 'background' != $wp_customize->get_setting( _mai_customizer_get_field_name( $this->settings_field, 'image_location' ) )->value() ) );
						},
					)
				);

			}

		}

		if ( $this->settings['more_link'] ) {

			// More Link heading
			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $this->settings_field, 'cpt_more_link_heading' ),
				array(
					'default' => '',
					'type'    => 'option',
				)
			);
			$wp_customize->add_control(
				new Mai_Customize_Control_Content( $wp_customize,
					$this->prefix . 'cpt_more_link_heading',
					array(
						'label'           => __( 'Read More Link', 'mai-pro-engine' ),
						'section'         => $this->section_id,
						'settings'        => false,
						'active_callback' => function() use ( $wp_customize ) {
							return (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $this->settings_field, 'enable_content_archive_settings' ) )->value();
						},
					)
				)
			);

			// More Link
			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $this->settings_field, 'more_link' ),
				array(
					'default'           => mai_sanitize_one_zero( mai_get_default_cpt_option( 'more_link' ) ),
					'type'              => 'option',
					'sanitize_callback' => 'mai_sanitize_one_zero',
				)
			);
			$wp_customize->add_control(
				$this->prefix . 'more_link',
				array(
					'label'           => __( 'Display the Read More link', 'mai-pro-engine' ),
					'section'         => $this->section_id,
					'settings'        => _mai_customizer_get_field_name( $this->settings_field, 'more_link' ),
					'priority'        => 10,
					'type'            => 'checkbox',
					'active_callback' => function() use ( $wp_customize ) {
						return (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $this->settings_field, 'enable_content_archive_settings' ) )->value();
					},
				)
			);

		}

		// Entry Meta.
		if ( $this->settings['remove_meta'] && ( $this->post_type_supports['genesis-entry-meta-before-content'] || $this->post_type_supports['genesis-entry-meta-after-content'] ) ) {

			$remove_meta_choices = array();

			if ( $this->post_type_supports['genesis-entry-meta-before-content'] ) {
				$remove_meta_choices['post_info'] = __( 'Remove Post Info', 'mai-pro-engine' );
			}

			if ( $this->post_type_supports['genesis-entry-meta-after-content'] ) {
				$remove_meta_choices['post_meta'] = __( 'Remove Post Meta', 'mai-pro-engine' );
			}

			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $this->settings_field, 'remove_meta' ),
				array(
					'default'           => _mai_customizer_multicheck_sanitize_key( mai_get_default_cpt_option( 'remove_meta' ) ),
					'type'              => 'option',
					'sanitize_callback' => '_mai_customizer_multicheck_sanitize_key',
				)
			);
			$wp_customize->add_control(
				new Mai_Customize_Control_Multicheck( $wp_customize,
					$this->prefix . 'remove_meta',
					array(
						'label'           => __( 'Entry Meta', 'mai-pro-engine' ),
						'section'         => $this->section_id,
						'settings'        => _mai_customizer_get_field_name( $this->settings_field, 'remove_meta' ),
						'priority'        => 10,
						'choices'         => $remove_meta_choices,
						'active_callback' => function() use ( $wp_customize ) {
							return (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $this->settings_field, 'enable_content_archive_settings' ) )->value();
						},
					)
				)
			);

		}

		// Posts Per Page.
		if ( $this->settings['posts_per_page'] ) {

			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $this->settings_field, 'posts_per_page' ),
				array(
					'default'           => absint( mai_get_default_cpt_option( 'posts_per_page' ) ),
					'type'              => 'option',
					'sanitize_callback' => 'absint',
				)
			);
			$wp_customize->add_control(
				$this->prefix . 'posts_per_page',
				array(
					'label'           => __( 'Entries Per Page', 'mai-pro-engine' ),
					'section'         => $this->section_id,
					'settings'        => _mai_customizer_get_field_name( $this->settings_field, 'posts_per_page' ),
					'priority'        => 10,
					'type'            => 'number',
					'active_callback' => function() use ( $wp_customize ) {
						return (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $this->settings_field, 'enable_content_archive_settings' ) )->value();
					},
				)
			);

		}

		// Posts Nav.
		if ( $this->settings['posts_nav'] ) {

			$wp_customize->add_setting(
				_mai_customizer_get_field_name( $this->settings_field, 'posts_nav' ),
				array(
					'default'           => sanitize_key( mai_get_default_cpt_option( 'posts_nav' ) ),
					'type'              => 'option',
					'sanitize_callback' => 'sanitize_key',
				)
			);
			$wp_customize->add_control(
				$this->prefix . 'posts_nav',
				array(
					'label'    => __( 'Pagination', 'genesis' ),
					'section'  => $this->section_id,
					'settings' => _mai_customizer_get_field_name( $this->settings_field, 'posts_nav' ),
					'priority' => 10,
					'type'     => 'select',
					'choices'  => array(
						'prev-next' => __( 'Previous / Next', 'genesis' ),
						'numeric'   => __( 'Numeric', 'genesis' ),
					),
					'active_callback' => function() use ( $wp_customize ) {
						return (bool) $wp_customize->get_setting( _mai_customizer_get_field_name( $this->settings_field, 'enable_content_archive_settings' ) )->value();
					},
				)
			);

		}

	}

}
