<?php

// If debug mode
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
	/**
	 * Update CMB2 URL cause JS/CSS files 404
	 * when using the plugin via symlink
	 * in local dev environments.
	 */
	add_filter( 'cmb2_meta_box_url', 'mai_update_cmb2_meta_box_url' );
	function mai_update_cmb2_meta_box_url( $url ) {
		// TODO: streplace instead! See docs for this filter!!!!!!
	    return MAITHEME_ENGINE_PLUGIN_PLUGIN_URL . 'includes/vendor/CMB2';
	}
}

/**
 * Add some inline styles to make the banner metabox a little more streamlined.
 *
 * @return  void
 */
add_action( 'cmb2_before_form', 'mai_before_mai_metabox', 10, 4 );
function mai_before_mai_metabox( $cmb_id, $object_id, $object_type, $cmb ) {

	if ( ! in_array( $cmb_id, array( 'mai_post_banner', 'mai_term_settings', 'mai_user_settings' ) )
		&& ( strpos( $cmb_id, 'mai-cpt-archive-settings-' ) === false ) ) {
		return;
	}

	// Enqueue
	wp_enqueue_script( 'mai-cmb2' );

    echo '<style type="text/css">
	        .mai-metabox .cmb-row {
		        padding: 10px 0 0 !important;
			    border: none !important;
			    margin-bottom: 16px !important;
			}
			.mai-metabox .cmb2-metabox-description {
				display: block !important;
				color: #666 !important;
			    font-size: 14px !important;
			    font-style: italic !important;
			    padding-top: .5em !important;
			}
			.mai-metabox .cmb-type-checkbox {
				padding-top: 0 !important;
				margin-bottom: 0 !important;
			}
			.mai-metabox .cmb-type-checkbox .cmb2-metabox-description {
				display: inline-block !important;
			    color: #444 !important;
			    font-style: normal !important;
			    padding-bottom: 0 !important;
			    margin: 2px 0 5px !important;
			}
			.mai-metabox .cmb-th {
				margin-top: -5px !important;
			}
			.mai-metabox .cmb-type-checkbox .cmb-th {
				margin-top: -10px !important;
			}
			#mai_post_banner .cmb-th {
				position: absolute !important;
				clip: rect(0, 0, 0, 0);
				height: 1px;
				width: 1px;
				border: 0;
				overflow: hidden;
			}
			#mai_post_banner .cmb-th:focus {
				clip: auto !important;
				height: auto;
				width: auto;
				display: block;
				font-size: 1em;
				font-weight: bold;
				padding: 15px 23px 14px;
				color: #323232;
				background: #fff;
				z-index: 100000; /* Above WP toolbar. */
				text-decoration: none;
				box-shadow: 0 0 2px 2px rgba(0,0,0,.6);
			}
        </style>';

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
	// Remove Woo Product Cat since it has its own image field
	unset( $taxonomies['product_cat'] );
	// Filter taxonomies so devs can change where this shows up
	$taxonomies = apply_filters( 'mai_banner_taxonomies', $taxonomies );

	$metabox_title = __( 'Mai Archive Settings', 'maitheme' );
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
    $term = new_cmb2_box( array(
        'id'               => 'mai_static_archive_settings',
        'title'            => $metabox_title,
        'object_types'     => array( 'page' ),
        'context' 		   => 'normal',
        'priority'		   => 'default',
        'classes' 		   => 'mai-metabox',
        'show_on_cb' 	   => 'mai_cmb_show_if_static_archive',
    ) );
    $term->add_field( _mai_cmb_columns_config() );
    $term->add_field( _mai_cmb_posts_per_page_config() );

    // Taxonomy Terms
    $term = new_cmb2_box( array(
        'id'               => 'mai_term_settings',
        'title'            => $metabox_title,
        'object_types'     => array( 'term' ),
        'taxonomies'       => $taxonomies,
        'new_term_section' => true,
        'context' 		   => 'normal',
        'priority'		   => 'low',
        'classes' 		   => 'mai-metabox',
    ) );
    $term->add_field( _mai_cmb_banner_visibility_config() );
    $term->add_field( _mai_cmb_banner_config() );
    $term->add_field( _mai_cmb_columns_config() );
    $term->add_field( _mai_cmb_posts_per_page_config() );

    // User Profiles
    $user = new_cmb2_box( array(
		'id'			=> 'mai_user_settings',
		'title'			=> $metabox_title,
		'object_types'	=> array( 'user' ),
		'context'		=> 'normal',
		'show_on_cb' 	=> 'mai_cmb_show_if_user_is_author_or_above',
		'classes' 		=> 'mai-metabox',
    ) );
    $user->add_field( _mai_cmb_banner_visibility_config() );
    $user->add_field( _mai_cmb_banner_config() );
    $user->add_field( _mai_cmb_columns_config() );
    $user->add_field( _mai_cmb_posts_per_page_config() );
}

/**
 * Post metabox callback function to check if the
 * archive metabox should show for a post.
 *
 * Returns true if viewing the static blog page or WooCommerce shop page.
 *
 * @return bool
 */
function mai_cmb_show_if_static_archive() {
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
function mai_cmb_show_if_user_is_author_or_above() {
	global $user_id;
	if ( user_can( $user_id, 'publish_posts' ) ) {
		return true;
	}
	return false;
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

		// Hook into the genesis cpt setttings save and add in the CMB2 sanitized values.
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
			'title'			=> __( 'Mai Archive Settings', 'maitheme' ),
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

function _mai_cmb_banner_visibility_config() {
	return array(
		'name'			=> __( 'Banner Visibility', 'maitheme' ),
		'desc'			=> __( 'Hide banner on this archive.', 'maitheme' ),
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

function _mai_cmb_columns_config() {

	$columns = mai_admin_get_columns();
	$count	 = ( $columns > 1 ) ? $columns : __( 'None', 'maitheme' );
	$none	 = sprintf( __( 'Inherit - currently (%s)', 'maitheme' ), $count );

	return array(
		'name'				=> __( 'Post Columns', 'maitheme' ),
		'desc'				=> __( 'Display archives in multiple columns. Default inherits from archive settings.', 'maitheme' ),
		'id'				=> 'columns',
		'type'				=> 'radio',
		'show_option_none'	=> $none,
		// 'default' 			=> 1,
		'options'			=> array(
			1 => __( 'None', 'maitheme' ),
			2 => __( '2 Columns', 'maitheme' ),
			3 => __( '3 Columns', 'maitheme' ),
			4 => __( '4 Columns', 'maitheme' ),
			6 => __( '6 Columns', 'maitheme' ),
		),
    );
}

function _mai_cmb_posts_per_page_config() {
	return array(
		'name'				=> __( 'Posts Per Page', 'maitheme' ),
		'desc'				=> __( 'The max number of posts to show, per page. If empty, the number in Settings > Reading will be used.', 'maitheme' ),
		'id'				=> 'posts_per_page',
		'type'				=> 'text_small',
		'attributes'		=> array(
			'type'		  => 'number',
			'pattern'	  => '\d*',
			'placeholder' => get_option( 'posts_per_page' ),
		),
		'sanitization_cb'	=> 'intval',
    );
}
