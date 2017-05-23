<?php

/**
 * CMB2 Genesis Settings Metabox
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
	protected $metabox_id = 'mai_settings';

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
			'title'        => __( 'Mai Settings', 'mai-pro' ),
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

		$this->cmb->add_field( _mai_cmb_banner_disable_post_types_config() );
		$this->cmb->add_field( array(
			'name'	=> __( 'Archive Settings', 'mai-pro' ),
			'desc'	=> __( 'These options will affect any blog listings page, including archive, author, blog, category, search, and tag pages, unless overridden in the corresponding metabox.', 'mai-pro' ),
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
