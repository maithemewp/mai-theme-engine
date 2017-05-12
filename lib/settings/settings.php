<?php

/**
 * Updates theme settings on reset.
 *
 * @since 1.0.0
 */
// add_filter( 'genesis_theme_settings_defaults', 'mai_theme_settings_defaults' );
function mai_theme_settings_defaults( $defaults ) {
	$defaults['content_archive']           = 'excerpts';
	$defaults['content_archive_limit']     = 0;
	$defaults['content_archive_thumbnail'] = 1;
	$defaults['posts_nav']                 = 'numeric';
	$defaults['site_layout']               = 'md-content';
	return $defaults;
}

/**
 * Updates theme settings on activation.
 *
 * @since 1.0.0
 */
// add_action( 'after_switch_theme', 'mai_update_theme_settings_defaults' );
function mai_update_theme_settings_defaults() {
	if ( function_exists( 'genesis_update_settings' ) ) {
		genesis_update_settings( array(
			'content_archive'           => 'excerpts',
			'content_archive_limit'     => 0,
			'content_archive_thumbnail' => 1,
			'posts_nav'                 => 'numeric',
			'site_layout'               => 'md-content',
		) );
	}
	update_option( 'posts_per_page', 12 );
}



// If debug mode
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
	/**
	 * Update CMB2 URL cause JS/CSS files 404
	 * when using the plugin via symlink
	 * in my local dev environment.
	 *
	 * This shouldn't affect anyone else, sorry for extra code just for me :P
	 */
	add_filter( 'cmb2_meta_box_url', 'mai_update_cmb2_meta_box_url' );
	function mai_update_cmb2_meta_box_url( $url ) {
	    return str_replace( '/Users/JiveDig/Plugins/maitheme-engine/', MAITHEME_ENGINE_PLUGIN_PLUGIN_URL, $url );
	}
}

/**
 * Add some inline styles to make the banner metabox a little more streamlined.
 *
 * @return  void
 */
add_action( 'cmb2_before_form', 'mai_before_mai_metabox', 10, 4 );
function mai_before_mai_metabox( $cmb_id, $object_id, $object_type, $cmb ) {
	// Bail if not the form(s) we want
	if ( ! in_array( $cmb_id, array( 'mai_content_archive', 'mai_post_banner', 'mai_term_settings', 'mai_user_settings' ) )
		&& ( strpos( $cmb_id, 'mai-cpt-archive-settings-' ) === false ) ) {
		return;
	}

	// Enqueue
	wp_enqueue_style( 'mai-cmb2' );
	wp_enqueue_script( 'mai-cmb2' );

}

/**
 * CMB2 Genesis Settings Metabox
 *
 * To fetch these options, use `genesis_get_option()`, e.g.
 *      $color = genesis_get_option( 'test_colorpicker' );
 *
 * @version 0.1.0
 */
class Mai_Genesis_Theme_Settings_Metabox {

	/**
	 * Option key. Could maybe be 'genesis-seo-settings', or other section?
	 *
	 * @var string
	 */
	protected $key = GENESIS_SETTINGS_FIELD;

	/**
	 * The admin page slug.
	 *
	 * @var string
	 */
	protected $admin_page = 'genesis';

	/**
	 * Options page metabox id
	 *
	 * @var string
	 */
	protected $metabox_id = 'mai_content_archive';

	/**
	 * Admin page hook
	 *
	 * @var string
	 */
	protected $admin_hook = 'toplevel_page_genesis';

	/**
	 * Holds an instance of CMB2
	 *
	 * @var CMB2
	 */
	protected $cmb = null;

	/**
	 * Holds an instance of the object
	 *
	 * @var Mai_Genesis_Theme_Settings_Metabox
	 */
	protected static $instance = null;

	/**
	 * Returns the running object
	 *
	 * @return Mai_Genesis_Theme_Settings_Metabox
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * @since 0.1.0
	 */
	protected function __construct() {
	}

	/**
	 * Initiate our hooks
	 *
	 * @since 0.1.0
	 */
	public function hooks() {
		add_action( 'admin_menu', array( $this, 'admin_hooks' ) );
		add_action( 'cmb2_admin_init', array( $this, 'init_metabox' ) );
	}

	/**
	 * Add menu options page
	 *
	 * @since 0.1.0
	 */
	public function admin_hooks() {
		// Include CMB CSS in the head to avoid FOUC.
		add_action( "admin_print_styles-{$this->admin_hook}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );

		// Hook into the genesis cpt setttings save and add in the CMB2 sanitized values.
		add_filter( "sanitize_option_{$this->key}", array( $this, 'add_sanitized_values' ), 999 );

		// Hook up our Genesis metabox.
		add_action( 'genesis_theme_settings_metaboxes', array( $this, 'add_meta_box' ) );
	}


	/**
	 * Hook up our Genesis metabox.
	 *
	 * @since 0.1.0
	 */
	public function add_meta_box() {
		$cmb = $this->init_metabox();
		add_meta_box(
			$cmb->cmb_id,
			$cmb->prop( 'title' ),
			array( $this, 'output_metabox' ),
			$this->admin_hook,
			$cmb->prop( 'context' ),
			$cmb->prop( 'priority' )
		);
	}

	/**
	 * Output our Genesis metabox.
	 *
	 * @since 0.1.0
	 */
	public function output_metabox() {
		$cmb = $this->init_metabox();
		$cmb->show_form( $cmb->object_id(), $cmb->object_type() );
	}

	/**
	 * If saving the cpt settings option, add the CMB2 sanitized values.
	 *
	 * @since 0.1.0
	 *
	 * @param array $new_value Array of values for the setting.
	 *
	 * @return array Updated array of values for the setting.
	 */
	public function add_sanitized_values( $new_value ) {
		if ( ! empty( $_POST ) ) {
			$cmb = $this->init_metabox();

			$new_value = array_merge(
				$new_value,
				$cmb->get_sanitized_values( $_POST )
			);
		}

		return $new_value;
	}

	/**
	 * Register our Genesis metabox and return the CMB2 instance.
	 *
	 * @since  0.1.0
	 *
	 * @return CMB2 instance.
	 */
	public function init_metabox() {
		if ( null !== $this->cmb ) {
			return $this->cmb;
		}

		// TODO: Make sure these defaults match the genesis_options defaults filter!!!!!!

		$this->cmb = cmb2_get_metabox( array(
			'id'           => $this->metabox_id,
			'title'        => __( 'Mai Content Archives', 'maitheme' ),
			'object_types' => array( $this->admin_hook ),
			'hookup'       => false,  // We'll handle ourselves. (add_sanitized_values())
			'cmb_styles'   => false,  // We'll handle ourselves. (admin_hooks())
			'context'      => 'main', // Important for Genesis.
			'priority'     => 'low',  // Defaults to 'high'.
			'classes' 	   => 'mai-metabox mai-content-archive-metabox',
			'show_on'      => array(
				// These are important, don't remove.
				'key'   => 'options-page',
				'value' => array( $this->key ),
			),
		), $this->key, 'options-page' );

		$this->cmb->add_field( array(
			'name'	=> '',
			'desc'	=> __( 'These options will affect any blog listings page, including archive, author, blog, category, search, and tag pages. Unless overridden in the corresponding metabox.', 'maitheme' ),
			'type'	=> 'title',
			'id'	=> 'mai_content_archives_title',
		) );

		$this->cmb->add_field( _mai_cmb_columns_config() );
		$this->cmb->add_field( _mai_cmb_content_archive_config() );
		$this->cmb->add_field( _mai_cmb_content_archive_limit_config() );
		$this->cmb->add_field( _mai_cmb_more_link_config() );
		$this->cmb->add_field( _mai_cmb_content_archive_thumbnail_config() );
		$this->cmb->add_field( _mai_cmb_image_location_config() );
		$this->cmb->add_field( _mai_cmb_image_size_config() );
		$this->cmb->add_field( _mai_cmb_image_alignment_config() );
		$this->cmb->add_field( _mai_cmb_meta_config() );
		$this->cmb->add_field( _mai_cmb_posts_nav_config() );

		return $this->cmb;
	}

	/**
	 * Public getter method for retrieving protected/private variables.
	 *
	 * @since 0.1.0
	 *
	 * @param string $field Field to retrieve.
	 *
	 * @throws Exception Throws an exception if the field is invalid.
	 *
	 * @return mixed Field value or exception is thrown
	 */
	public function __get( $field ) {
		if ( 'cmb' === $field ) {
			return $this->init_metabox();
		}

		// Allowed fields to retrieve.
		if ( in_array( $field, array( 'key', 'admin_page', 'metabox_id', 'admin_hook' ), true ) ) {
			return $this->{$field};
		}

		throw new Exception( 'Invalid property: ' . $field );
	}

}

/**
 * Helper function to get/return the Mai_Genesis_Theme_Settings_Metabox object
 *
 * @since 0.1.0
 *
 * @return Mai_Genesis_Theme_Settings_Metabox object
 */
function mai_genesis_theme_settings_metabox() {
	return Mai_Genesis_Theme_Settings_Metabox::get_instance();
}

// Get it started.
mai_genesis_theme_settings_metabox();

/**
 * CMB2 Genesis CPT Archive Metabox
 *
 * @version 0.1.0
 */
class Mai_Genesis_CPT_Settings_Metabox {

	/**
 	 * Mmetabox id
 	 * @var string
 	 */
	protected $metabox_id = 'mai-cpt-archive-settings-%1$s';

	/**
 	 * CPT slug
 	 * @var string
 	 */
	protected $post_type = '';

	/**
 	 * CPT slug
 	 * @var string
 	 */
	protected $admin_hook = '%1$s_page_genesis-cpt-archive-%1$s';

	/**
 	 * Option key, and option page slug
 	 * @var string
 	 */
	protected $key = 'genesis-cpt-archive-settings-%1$s';

	/**
	 * Holds an instance of CMB2
	 *
	 * @var CMB2
	 */
	protected $cmb = null;

	/**
	 * Holds all instances of this class.
	 *
	 * @var Mai_Genesis_CPT_Settings_Metabox
	 */
	protected static $instances = array();

	/**
	 * Returns an instance.
	 *
	 * @since  0.1.0
	 *
	 * @param  string $post_type Post type slug
	 *
	 * @return Mai_Genesis_CPT_Settings_Metabox
	 */
	public static function get_instance( $post_type ) {
		if ( empty( self::$instances[ $post_type ] ) ) {
			self::$instances[ $post_type ] = new self( $post_type );
			self::$instances[ $post_type ]->hooks();
		}

		return self::$instances[ $post_type ];
	}

	/**
	 * Constructor
	 * @since 0.1.0
	 */
	protected function __construct( $post_type ) {
		$this->post_type  = $post_type;
		$this->admin_hook = sprintf( $this->admin_hook, $post_type );
		$this->key        = sprintf( $this->key, $post_type );
		$this->metabox_id = sprintf( $this->metabox_id, $post_type );
	}

	/**
	 * Initiate our hooks
	 * @since 0.1.0
	 */
	public function hooks() {
		add_action( 'admin_menu', array( $this, 'admin_hooks' ) );
		add_action( 'cmb2_admin_init', array( $this, 'init_metabox' ) );
	}

	/**
	 * Add admin hooks.
	 * @since 0.1.0
	 */
	public function admin_hooks() {
		// Include CMB CSS in the head to avoid FOUC
		add_action( "admin_print_styles-{$this->admin_hook}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );

		// Hook into the genesis cpt settings save and add in the CMB2 sanitized values.
		add_filter( "sanitize_option_genesis-cpt-archive-settings-{$this->post_type}", array( $this, 'add_sanitized_values' ), 999, 2 );

		// Hook up our Genesis metabox.
		add_action( 'genesis_cpt_archives_settings_metaboxes', array( $this, 'add_meta_box' ) );
	}

	/**
	 * Hook up our Genesis metabox.
	 * @since 0.1.0
	 */
	public function add_meta_box() {
		$cmb = $this->init_metabox();
		add_meta_box(
			$cmb->cmb_id,
			$cmb->prop( 'title' ),
			array( $this, 'output_metabox' ),
			$this->admin_hook,
			$cmb->prop( 'context' ),
			$cmb->prop( 'priority' )
		);
	}

	/**
	 * Output our Genesis metabox.
	 * @since  0.1.0
	 */
	public function output_metabox() {
		$cmb = $this->init_metabox();
		$cmb->show_form( $cmb->object_id(), $cmb->object_type() );
	}

	public function add_sanitized_values( $new_value, $option ) {
		if ( ! empty( $_POST ) ) {
			$cmb = $this->init_metabox();

			$new_value = array_merge(
				$new_value,
				$cmb->get_sanitized_values( $_POST )
			);
		}

		return $new_value;
	}

	/**
	 * Register our Genesis metabox and return the CMB2 instance.
	 *
	 * @since  0.1.0
	 *
	 * @return CMB2 instance.
	 */
	public function init_metabox() {

		if ( null !== $this->cmb ) {
			return $this->cmb;
		}

	    $this->cmb = cmb2_get_metabox( array(
			'id'			=> $this->metabox_id,
			'title'			=> __( 'Mai Content Archives', 'maitheme' ),
			'classes' 		=> 'mai-metabox',
			'hookup'		=> false, 	// We'll handle ourselves. ( add_sanitized_values() )
			'cmb_styles'	=> false, 	// We'll handle ourselves. ( admin_hooks() )
			'context'		=> 'main', 	// Important for Genesis.
			'priority'		=> 'low', 	// Defaults to 'high'.
			'object_types'	=> array( $this->admin_hook ),
			'show_on'		=> array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		), $this->key, 'options-page' );

	    $this->cmb->add_field( _mai_cmb_banner_visibility_config() );
	    $this->cmb->add_field( _mai_cmb_banner_config() );
	    $this->cmb->add_field( _mai_cmb_columns_config() );
	    $this->cmb->add_field( _mai_cmb_posts_per_page_config() );

	    // TODO: Rebuild the genesis-cpt-archives-settings.php fields!

		return $this->cmb;
	}

	/**
	 * Public getter method for retrieving protected/private variables
	 * @since  0.1.0
	 * @param  string  $field Field to retrieve
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( 'cmb' === $field ) {
			return $this->init_metabox();
		}

		if ( in_array( $field, array( 'metabox_id', 'post_type', 'admin_hook', 'key' ), true ) ) {
			return $this->{$field};
		}

		throw new Exception( 'Invalid property: ' . $field );
	}

}

/**
 * Add CMB2 metabox and fields to Genesis CPT Archive Settings pages
 *
 * @since   1.0.0
 *
 * @return  void
 */
add_action( 'wp_loaded', 'mai_do_cpt_archive_settings_metaboxes' );
function mai_do_cpt_archive_settings_metaboxes() {

	// Bail if not admin
	if ( ! is_admin() ) {
		return;
	}

    $post_types = genesis_get_cpt_archive_types();
    foreach( $post_types as $post_type ) {
        if ( genesis_has_post_type_archive_support( $post_type->name ) ) {
        	mai_do_genesis_cpt_settings( $post_type->name );
        }
    }

}

/**
 * Helper function to get/return the Mai_Genesis_CPT_Settings_Metabox object.
 *
 * @since  0.1.0
 *
 * @param  string $post_type Post type slug
 *
 * @return Mai_Genesis_CPT_Settings_Metabox object
 */
function mai_do_genesis_cpt_settings( $post_type ) {
	return Mai_Genesis_CPT_Settings_Metabox::get_instance( $post_type );
}

/**
 * Add metaboxes for banner image and archive settings.
 *
 * To get banner image:
 *
 * $post_banner_image = wp_get_attachment_image( get_post_meta( $post_id, 'banner_id', true ), 'banner' );
 * $term_banner_image = wp_get_attachment_image( get_term_meta( $term_id, 'banner_id', true ), 'banner' );
 * $user_banner_image = wp_get_attachment_image( get_user_meta( $user_id, 'banner_id', true ), 'banner' );
 *
 * @return  void
 */
add_action( 'cmb2_admin_init', 'mai_cmb2_add_metaboxes' );
function mai_cmb2_add_metaboxes() {

	$post_types = get_post_types( array('public' => true ), 'names' );
	// Remove attachments
	unset( $post_types['attachment'] );
	// Filter post_types so devs can change where this shows up
	$post_types = apply_filters( 'mai_banner_post_types', $post_types );

	$taxonomies = get_taxonomies( array( 'public' => true ) );
	// Filter taxonomies so devs can change where this shows up
	$taxonomies = apply_filters( 'mai_banner_taxonomies', $taxonomies );

	$metabox_title = __( 'Mai Content Archives', 'maitheme' );
	$upload_label  = __( 'Banner Image', 'maitheme' ); // Hidden on posts since show_names is false
	$button_text   = __( 'Add Banner Image', 'maitheme' );

	// Posts/Pages/CPTs
    $post = new_cmb2_box( array(
		'id'			=> 'mai_post_banner',
		'title'			=> $metabox_title,
		'object_types'	=> $post_types,
		'context'		=> 'side',
		'priority'		=> 'low',
		'classes' 		=> 'mai-metabox',
		'show_on_cb'	=> 'mai_is_banner_area_enabled',
    ) );
    $post->add_field( _mai_cmb_banner_visibility_config() );
    $post->add_field( _mai_cmb_banner_config() );

    // Static Blog and WooCommerce Shop
    $static_archive = new_cmb2_box( array(
        'id'               => 'mai_static_archive_settings',
        'title'            => $metabox_title,
        'object_types'     => array( 'page' ),
        'context' 		   => 'normal',
        'priority'		   => 'default',
        'classes' 		   => 'mai-metabox mai-content-archive-metabox',
        'show_on_cb' 	   => '_mai_cmb_show_if_static_archive',
    ) );
    $static_archive->add_field( _mai_cmb_remove_loop_config() );
    $static_archive->add_field( _mai_cmb_content_enable_archive_settings_config() );
    $static_archive->add_field( _mai_cmb_content_archive_settings_title_config() );
    $static_archive->add_field( _mai_cmb_posts_per_page_config() );
    $static_archive->add_field( _mai_cmb_columns_config() );
	$static_archive->add_field( _mai_cmb_content_archive_thumbnail_config() );
	$static_archive->add_field( _mai_cmb_image_location_config() );
	$static_archive->add_field( _mai_cmb_image_size_config() );
	$static_archive->add_field( _mai_cmb_image_alignment_config() );
	$static_archive->add_field( _mai_cmb_content_archive_config() );
	$static_archive->add_field( _mai_cmb_content_archive_limit_config() );
	$static_archive->add_field( _mai_cmb_more_link_config() );
	$static_archive->add_field( _mai_cmb_meta_config() );
	$static_archive->add_field( _mai_cmb_posts_nav_config() );

    // Taxonomy Terms
    $term = new_cmb2_box( array(
        'id'               => 'mai_term_settings',
        'title'            => $metabox_title,
        'object_types'     => array( 'term' ),
        'taxonomies'       => $taxonomies,
        'new_term_section' => true,
        'context' 		   => 'normal',
        'priority'		   => 'low',
        'classes' 		   => 'mai-metabox mai-content-archive-metabox',
    ) );
    $term->add_field( _mai_cmb_banner_visibility_config() );
    // Don't show banner upload field on product categories, since Woo has an image field already
    if ( ! ( class_exists( 'WooCommerce' ) && ( 'product_cat' == $taxonomies['product_cat'] ) ) ) {
	    $term->add_field( _mai_cmb_banner_config() );
	}
    $term->add_field( _mai_cmb_remove_loop_config() );
    $term->add_field( _mai_cmb_content_archive_settings_title_config() );
    $term->add_field( _mai_cmb_content_enable_archive_settings_config() );
    $term->add_field( _mai_cmb_posts_per_page_config() );
    $term->add_field( _mai_cmb_columns_config() );
	$term->add_field( _mai_cmb_content_archive_thumbnail_config() );
	$term->add_field( _mai_cmb_image_location_config() );
	$term->add_field( _mai_cmb_image_size_config() );
	$term->add_field( _mai_cmb_image_alignment_config() );
	$term->add_field( _mai_cmb_content_archive_config() );
	$term->add_field( _mai_cmb_content_archive_limit_config() );
	$term->add_field( _mai_cmb_more_link_config() );
	$term->add_field( _mai_cmb_meta_config() );
	$term->add_field( _mai_cmb_posts_nav_config() );

    // User Profiles
    $user = new_cmb2_box( array(
		'id'			=> 'mai_user_settings',
		'title'			=> $metabox_title,
		'object_types'	=> array( 'user' ),
		'context'		=> 'normal',
		'show_on_cb' 	=> 'mai_cmb_show_if_user_is_author_or_above',
		'classes' 		=> 'mai-metabox mai-content-archive-metabox',
    ) );
    $user->add_field( _mai_cmb_banner_visibility_config() );
    $user->add_field( _mai_cmb_banner_config() );
    $user->add_field( _mai_cmb_content_archive_settings_title_config() );
    $user->add_field( _mai_cmb_content_enable_archive_settings_config() );
    $user->add_field( _mai_cmb_remove_loop_config() );
    $user->add_field( _mai_cmb_posts_per_page_config() );
    $user->add_field( _mai_cmb_columns_config() );
	$user->add_field( _mai_cmb_content_archive_thumbnail_config() );
	$user->add_field( _mai_cmb_image_location_config() );
	$user->add_field( _mai_cmb_image_size_config() );
	$user->add_field( _mai_cmb_image_alignment_config() );
	$user->add_field( _mai_cmb_content_archive_config() );
	$user->add_field( _mai_cmb_content_archive_limit_config() );
	$user->add_field( _mai_cmb_more_link_config() );
	$user->add_field( _mai_cmb_meta_config() );
	$user->add_field( _mai_cmb_posts_nav_config() );
}

/**
 * Post metabox callback function to check if the
 * archive metabox should show for a post.
 *
 * Returns true if viewing the static blog page or WooCommerce shop page in the admin.
 *
 * @return bool
 */
function _mai_cmb_show_if_static_archive() {
    $post_id       = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
    $posts_page_id = get_option('page_for_posts');
    $shop_page_id  = get_option('woocommerce_shop_page_id');

    // If static blog page or WooCommerce shop page
    if ( ( $post_id == $posts_page_id ) || ( class_exists('WooCommerce') && ( $post_id == $shop_page_id ) ) ) {
    	return true;
    }
    return false;
}

/**
 * User metabox callback function to check if the
 * banner metabox should show for a user.
 *
 * Returns true if the viewed profile's user can publish posts.
 *
 * @return bool
 */
function _mai_cmb_show_if_user_is_author_or_above() {
	global $user_id;
	if ( user_can( $user_id, 'publish_posts' ) ) {
		return true;
	}
	return false;
}

function _mai_cmb_banner_visibility_config() {
	return array(
		'name'			=> __( 'Banner Visibility', 'maitheme' ),
		'desc'			=> __( 'Hide the banner area', 'maitheme' ),
		'id'			=> 'hide_banner',
		'type'			=> 'checkbox',
		'show_on_cb'	=> 'mai_is_banner_area_enabled',
    );
}

function _mai_cmb_banner_config() {
	return array(
		'name'			=> __( 'Banner Image', 'maitheme' ),
		'id'			=> 'banner',
		'type'			=> 'file',
		'preview_size'	=> 'one-third',
		'options'		=> array(
	        'url' => false,
	    ),
	    'text' 			=> array(
	        'add_upload_file_text' => __( 'Add Image', 'maitheme' ),
	    ),
	    'show_on_cb' 	=> 'mai_is_banner_area_enabled',
    );
}

function _mai_cmb_content_archive_settings_title_config() {
	return array(
		'name'	=> __( 'Mai Content Archives', 'maitheme' ),
		'desc'	=> __( 'If enabled, these will override the default content archive settings', 'maitheme' ),
		'type'	=> 'title',
		'id'	=> 'mai_content_archives_title',
	);
}

function _mai_cmb_content_enable_archive_settings_config() {
	return array(
		'name'	=> __( 'Archive Settings', 'maitheme' ),
		'desc'	=> __( 'Enable archive settings', 'maitheme' ),
		'id'	=> 'enable_content_archive_settings',
		'type'	=> 'checkbox',
    );
}

function _mai_cmb_remove_loop_config() {
	return array(
		'name'	=> __( 'Hide Entries', 'maitheme' ),
		'desc'	=> __( 'Hide entries from this archive', 'maitheme' ),
		'id'	=> 'remove_loop',
		'type'	=> 'checkbox',
    );
}

function _mai_cmb_columns_config() {
	return array(
		'name'				=> __( 'Content Columns', 'maitheme' ),
		'desc'				=> __( 'Display content in multiple columns.', 'maitheme' ),
		'id'				=> 'columns',
		'type'				=> 'select',
		'default'			=> 1,
		'options'			=> array(
			1 => __( '- None -', 'genesis' ),
			2 => __( '2 Columns', 'maitheme' ),
			3 => __( '3 Columns', 'maitheme' ),
			4 => __( '4 Columns', 'maitheme' ),
			6 => __( '6 Columns', 'maitheme' ),
		),
    );
}

function _mai_cmb_content_archive_config() {
	return array(
		'name'		=> __( 'Content', 'genesis' ),
		'id'		=> 'content_archive',
		'type'		=> 'select',
		'default'	=> 'excerpts',
		'options'	=> array(
			'none'		=> __( 'No content', 'maitheme' ),
			'full'		=> __( 'Entry content', 'genesis' ),
			'excerpts'	=> __( 'Entry excerpts', 'genesis' ),
		),
	);
}

function _mai_cmb_content_archive_thumbnail_config() {
	return array(
		'name'	=> __( 'Featured Image', 'genesis' ),
		'desc'	=> __( 'Include the Featured Image', 'maitheme' ),
		'id'	=> 'content_archive_thumbnail',
		'type'	=> 'checkbox',
    );
}

function _mai_cmb_image_location_config() {
	return array(
		'name'				=> __( 'Image Location:', 'maitheme' ),
		'id'				=> 'image_location',
		'before_field'		=> __( 'Image Location:', 'maitheme' ) . ' ',
		'type'				=> 'select',
		'default' 			=> 'before_entry',
		'options'			=> array(
			'before_entry'	 => __( 'Before Entry', 'maitheme' ),
			'before_title'	 => __( 'Before Title', 'maitheme' ),
			'after_title'	 => __( 'After Title', 'maitheme' ),
			'before_content' => __( 'Before Content', 'maitheme' ),
		),
	);
}

function _mai_cmb_image_size_config() {
	// Get our image size options
    $sizes = genesis_get_image_sizes();
    $size_options = array();
    foreach ( $sizes as $index => $value ) {
    	$size_options[$index] = sprintf( '%s (%s x %s)', $index, $value['width'], $value['height'] );
    }
	return array(
		'name'			=> __( 'Image Size:', 'genesis' ),
		'id'			=> 'image_size',
		'type'			=> 'select',
		'before_field'	=> __( 'Image Size:', 'genesis' ) . ' ',
		'default'		=> 'one-third',
		'options'		=> $size_options,
	);
}

function _mai_cmb_image_alignment_config() {
	return array(
		'name'				=> __( 'Image Alignment:', 'genesis' ),
		'id'				=> 'image_alignment',
		'type'				=> 'select',
		'before_field'		=> __( 'Image Alignment:', 'genesis' ) . ' ',
		'show_option_none'	=> __( '- None -', 'genesis' ),
		'options'			=> array(
			'alignleft'	 => __( 'Left', 'genesis' ),
			'alignright' => __( 'Right', 'genesis' ),
		),
	);
}

function _mai_cmb_content_archive_limit_config() {
	return array(
		'name'			=> __( 'Limit content to', 'genesis' ),
		'id'			=> 'content_archive_limit',
		'type'			=> 'text_small',
		'before_field'  => __( 'Limit content to', 'genesis' ) . ' ',
		'after_field'	=> ' ' . __( 'characters', 'genesis' ),
		'attributes'	=> array(
			'type'		  => 'number',
			'pattern'	  => '\d*',
		),
    );
}

function _mai_cmb_more_link_config() {
	return array(
		'name'	=> __( 'More Link', 'maitheme' ),
		'desc'	=> __( 'Include the Read More link', 'maitheme' ),
		'id'	=> 'more_link',
		'type'	=> 'checkbox',
    );
}

function _mai_cmb_meta_config() {
	return array(
		'name'	=> __( 'Entry Meta', 'maitheme' ),
		'id'	=> 'remove_meta',
		'type'	=> 'multicheck',
		'options'			=> array(
			'post_info'	=> __( 'Remove Post Info', 'maitheme' ),
			'post_meta' => __( 'Remove Post Meta', 'maitheme' ),
		),
		'select_all_button' => false,
    );
}

function _mai_cmb_posts_nav_config() {
	return array(
		'name'		=> __( 'Entry Pagination', 'genesis' ),
		'id'		=> 'posts_nav',
		'type'		=> 'select',
		'default'	=> 'numeric',
		'options'	=> array(
			'prev-next'	=> __( 'Previous / Next', 'genesis' ),
			'numeric'	=> __( 'Numeric', 'genesis' ),
		),
	);
}

function _mai_cmb_posts_per_page_config() {
	return array(
		'name'				=> __( 'Entries Per Page', 'maitheme' ),
		'desc'				=> __( 'The max number of posts to show, per page.', 'maitheme' ),
		'id'				=> 'posts_per_page',
		'type'				=> 'text_small',
		'default'			=> 12,
		'attributes'		=> array(
			'type'		  => 'number',
			'pattern'	  => '\d*',
		),
    );
}
