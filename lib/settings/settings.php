<?php

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
	// protected $key = 'genesis-settings';
	// protected $key = 'genesis-theme-settings-layout';
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
			'name'		=> __( 'Display', 'genesis' ),
			'id'		=> 'content_archive',
			'type'		=> 'select',
			'default'	=> 'excerpts',
			'options'	=> array(
				'full'		=> __( 'Entry content', 'genesis' ),
				'excerpts'	=> __( 'Entry excerpts', 'genesis' ),
			),
		) );

		$this->cmb->add_field( array(
			'name'				=> __( 'Limit content to', 'genesis' ),
			'id'				=> 'content_archive_limit',
			'type'				=> 'text_small',
			'attributes'		=> array(
				'type'		  => 'number',
				'pattern'	  => '\d*',
				'placeholder' => get_option( 'posts_per_page' ),
			),
			'sanitization_cb'	=> 'intval',
	    ) );

		$this->cmb->add_field( array(
			'name'			=> __( 'Featured Image', 'genesis' ),
			'desc'			=> __( 'Include the Featured Image?', 'genesis' ),
			'id'			=> 'content_archive_thumbnail',
			'type'			=> 'checkbox',
	    ) );

		// Get our image size options
	    $sizes = genesis_get_image_sizes();
	    $size_options = array();
	    foreach ( $sizes as $index => $value ) {
	    	$size_options[$index] = sprintf( '%s (%s x %s)', $index, $value['width'], $value['height'] );
	    }

		$this->cmb->add_field( array(
			'name'			=> __( 'Image Size:', 'genesis' ),
			'id'			=> 'image_size',
			'type'			=> 'select',
			'before_field'	=> __( 'Image Size:', 'genesis' ) . ' ',
			'default'		=> 'one-third',
			'options'		=> $size_options,
		) );

		$this->cmb->add_field( array(
			'name'				=> __( 'Image Alignment:', 'genesis' ),
			'id'				=> 'image_alignment',
			'type'				=> 'select',
			'before_field'		=> __( 'Image Alignment:', 'genesis' ) . ' ',
			'show_option_none'	=> __( '- None -', 'genesis' ),
			'options'			=> array(
				'alignleft'	 => __( 'Left', 'genesis' ),
				'alignright' => __( 'Right', 'genesis' ),
			),
		) );

		$this->cmb->add_field( array(
			'name'				=> __( 'Entry Pagination:', 'genesis' ),
			'desc'				=> __( 'These options will affect any blog listings page, including archive, author, blog, category, search, and tag pages. Unless overridden in the corresponding metabox.', 'maitheme' ),
			'id'				=> 'posts_nav',
			'type'				=> 'select',
			'default'			=> 'numeric',
			'options'			=> array(
				'prev-next'	 => __( 'Previous / Next', 'genesis' ),
				'numeric' => __( 'Numeric', 'genesis' ),
			),
		) );

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
function mai_content_archive_metabox() {
	return Mai_Genesis_Theme_Settings_Metabox::get_instance();
}

// Get it started.
mai_content_archive_metabox();


















/**
 * Register Defaults
 *
 * @link 	http://www.billerickson.net/genesis-theme-options/
 *
 * @param 	array $defaults
 *
 * @return 	array modified defaults
 */
// add_filter( 'genesis_theme_settings_defaults', 'ourap_theme_settings_defaults' );
function ourap_theme_settings_defaults( $defaults ) {
	$defaults['oura_ring'] = '';
	return $defaults;
}

/**
 * Sanitization
 */
// add_action( 'genesis_settings_sanitizer_init', 'ourap_theme_settings_sanitization_filters' );
function ourap_theme_settings_sanitization_filters() {
	genesis_add_option_filter( 'absint', GENESIS_SETTINGS_FIELD, array( 'oura_ring' ) );
	genesis_add_option_filter( 'absint', GENESIS_SETTINGS_FIELD, array( 'oura_charger' ) );
}


/**
 * Register Metabox
 */
// add_action( 'genesis_theme_settings_metaboxes', 'ourap_register_theme_settings_metabox' );
function ourap_register_theme_settings_metabox( $_genesis_theme_settings_pagehook ) {
	global $_genesis_admin_settings;
	remove_meta_box( 'genesis-theme-settings-scripts', $_genesis_theme_settings_pagehook, 'main' );
	add_meta_box( 'mai-archive-settings', __( 'Mai Theme Settings', 'maitheme' ), 'mai_archive_settings_metabox', $_genesis_theme_settings_pagehook, 'main', 'default' );
	$_genesis_admin_settings->add_meta_box( 'genesis-theme-settings-scripts', __( 'Header and Footer Scripts', 'genesis', 'low' ) );
}

/**
 * Create Metabox
 */
function mai_archive_settings_metabox() {
	$ring_id = esc_attr( genesis_get_option('oura_ring') );
	$charger_id = esc_attr( genesis_get_option('oura_charger') );
	?>
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><label for="genesis-settings[blog_cat_exclude]">ŌURA Ring Product ID:</label></th>
				<td>
					<p>
						<input type="number" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[oura_ring]" class="regular-text" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[oura_ring]" value="<?php echo $ring_id; ?>">
						<br><span class="description">Enter the product ID for the main ring WooCommerce product</span>
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Product Title:</th>
				<td><?php echo $ring_id ? get_the_title($ring_id) : 'none'; ?></td>
			</tr>

			<tr valign="top" style="border-top:1px solid #eee;">
				<th scope="row"><label for="genesis-settings[blog_cat_exclude]">ŌURA Charger Product ID:</label></th>
				<td>
					<p>
						<input type="number" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[oura_charger]" class="regular-text" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[oura_charger]" value="<?php echo $charger_id; ?>">
						<br><span class="description">Enter the product ID for the ring charger WooCommerce product</span>
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Product Title:</th>
				<td><?php echo $charger_id ? get_the_title($charger_id) : 'none'; ?></td>
			</tr>
		</tbody>
	</table>
	<?php
}
