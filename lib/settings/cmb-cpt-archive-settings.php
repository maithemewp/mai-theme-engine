<?php

/**
 * Helper function to get/return the Mai_Genesis_CPT_Settings_Metabox object.
 *
 * @since  0.1.0
 *
 * @param  string $post_type Post type slug
 *
 * @return Mai_Genesis_CPT_Settings_Metabox object
 */
function mai_do_genesis_cpt_archive_settings( $post_type ) {
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
 	 * @var array
 	 */
	protected $settings_fields = array();

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
		add_action( 'admin_menu',      array( $this, 'admin_hooks' ) );
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
			'id'           => $this->metabox_id,
			'title'        => __( 'Mai Archive Settings', 'mai-pro-engine' ),
			'classes'      => 'mai-metabox mai-content-archive-metabox',
			'hookup'       => false, 	// We'll handle ourselves. ( add_sanitized_values() )
			'cmb_styles'   => false, 	// We'll handle ourselves. ( admin_hooks() )
			'context'      => 'main', 	// Important for Genesis.
			'priority'     => 'low', 	// Defaults to 'high'.
			'object_types' => array( $this->admin_hook ),
			'show_on'      => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		), $this->key, 'options-page' );

		$this->cmb->add_field( _mai_cmb_banner_image_config() );
		$this->cmb->add_field( _mai_cmb_banner_visibility_config() );
		$this->cmb->add_field( _mai_cmb_remove_loop_config() );
		// $this->cmb->add_field( _mai_cmb_content_enable_archive_settings_config() );
		// $this->cmb->add_field( _mai_cmb_content_archive_settings_title_config() );
		// $this->cmb->add_field( _mai_cmb_columns_config() );
		// $this->cmb->add_field( _mai_cmb_content_archive_thumbnail_config() );
		// $this->cmb->add_field( _mai_cmb_image_location_config() );
		// $this->cmb->add_field( _mai_cmb_image_size_config() );
		// $this->cmb->add_field( _mai_cmb_image_alignment_config() );
		// $this->cmb->add_field( _mai_cmb_content_archive_config() );
		// $this->cmb->add_field( _mai_cmb_content_archive_limit_config() );
		// $this->cmb->add_field( _mai_cmb_more_link_config() );
		// $this->cmb->add_field( _mai_cmb_meta_config() );
		// $this->cmb->add_field( _mai_cmb_posts_per_page_config() );
		// $this->cmb->add_field( _mai_cmb_posts_nav_config() );

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
