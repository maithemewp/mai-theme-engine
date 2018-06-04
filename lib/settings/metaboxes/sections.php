<?php

/**
 * Remove the admin page editor on Sections template pages.
 *
 * @access  private
 *
 * @return  void.
 */
add_action( 'admin_head', 'mai_sections_hide_editor' );
function mai_sections_hide_editor() {

	global $pagenow;

	if ( 'post.php' !== $pagenow ) {
		return;
	}

	if ( ! isset( $_GET['post'] ) ) {
		return;
	}

	// Currently this will not work because scripts won't be loaded and [gallery] won't be parsed.
	// See https://github.com/CMB2/CMB2/issues/1083
	// if ( 'sections.php' === get_page_template_slug( filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT ) ) ) {
	// 	remove_post_type_support( 'page', 'editor' );
	// }

	// Bail if not on a sections template.
	if ( 'sections.php' !== get_page_template_slug( filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT ) ) ) {
		return;
	}

	// Instead we're forced to hide via CSS for now.
	echo '<style type="text/css">#postdivrich{display:none!important;}</style>';
}

/**
 * Add the CMB2 Sections repeater group.
 *
 * @access  private
 *
 * @return  void
 */
add_action( 'cmb2_admin_init', 'mai_do_sections_metabox' );
function mai_do_sections_metabox() {

	// Pages with Sections template.
	$sections = new_cmb2_box( array(
		'id'           => 'mai_sections',
		'title'        => __( 'Sections', 'mai-theme-engine' ),
		'object_types' => array( 'page' ),
		'context'      => 'after_editor',
		'classes'      => 'mai-metabox',
		'show_on'      => array( 'key' => 'page-template', 'value' => 'sections.php' ),
	) );

	// Sections.
	$section = $sections->add_field( array(
		'id'          => 'mai_sections',
		'type'        => 'group',
		'repeatable'  => true,
		'options'     => array(
			'group_title'   => __( 'Section #{#}', 'mai-theme-engine' ),
			'add_button'    => __( 'Add Section', 'mai-theme-engine' ),
			'remove_button' => __( 'Remove Section', 'mai-theme-engine' ),
			'sortable'      => true,
		),
	) );

	// Settings.
	$sections->add_group_field( $section, array(
		'name'       => '<button class="button mai-section-settings-toggle"><span class="dashicons dashicons-admin-generic"></span>' . __( 'Settings', 'mai-theme-engine' ) . '</button>',
		'id'         => 'settings_title',
		'before_row' => '<div class="mai-section"><div class="mai-section-wrap"><div class="mai-section-settings"><div class="mai-section-settings-inner"><button class="button mai-section-settings-close">' . __( 'Close', 'mai-theme-engine' ) . '</button>',
		'type'       => 'title',
		'classes'    => 'mai-section-settings-toggle-wrap',
	) );

		// Background Color.
		$sections->add_group_field( $section, array(
			'name'    => __( 'Background Color', 'mai-theme-engine' ),
			'id'      => 'bg',
			'type'    => 'colorpicker',
			'default' => '', // Keep this empty, so CSS can control the defaults if left untouched
		) );

		// Background Image.
		$sections->add_group_field( $section, array(
			'name'         => __( 'Background Image', 'mai-theme-engine' ),
			'id'           => 'image',
			'type'         => 'file',
			'preview_size' => 'one-third',
			'options'      => array( 'url' => false ),
			'text'         => array(
				'add_upload_file_text' => __( 'Add Image', 'mai-theme-engine' ),
			),
		) );

		// Overlay.
		$sections->add_group_field( $section, array(
			'name'              => __( 'Overlay Style', 'mai-theme-engine' ),
			'id'                => 'overlay',
			'type'              => 'select',
			'select_all_button' => false,
			'options'           => array(
				''         => __( '- None -', 'genesis' ),
				'gradient' => __( 'Gradient', 'mai-theme-engine' ),
				'light'    => __( 'Light', 'mai-theme-engine' ),
				'dark'     => __( 'Dark', 'mai-theme-engine' ),
			),
		) );

		// Inner.
		$sections->add_group_field( $section, array(
			'name'              => __( 'Inner Style', 'mai-theme-engine' ),
			'id'                => 'inner',
			'type'              => 'select',
			'select_all_button' => false,
			'options'           => array(
				''      => __( '- None -', 'genesis' ),
				'light' => __( 'Light Box', 'mai-theme-engine' ),
				'dark'  => __( 'Dark Box', 'mai-theme-engine' ),
			),
		) );

		// Height.
		$sections->add_group_field( $section, array(
			'name'    => __( 'Height', 'mai-theme-engine' ),
			'id'      => 'height',
			'type'    => 'select',
			'default' => 'md',
			'options' => array(
				'auto' => __( 'Auto (Use height of content)', 'mai-theme-engine' ),
				'xs'   => __( 'Extra Small', 'mai-theme-engine' ),
				'sm'   => __( 'Small', 'mai-theme-engine' ),
				'md'   => __( 'Medium (Default)', 'mai-theme-engine' ),
				'lg'   => __( 'Large', 'mai-theme-engine' ),
				'xl'   => __( 'Extra Large', 'mai-theme-engine' ),
			),
		) );

		// Content Width.
		$sections->add_group_field( $section, array(
			'name'             => __( 'Content Width', 'mai-theme-engine' ),
			'id'               => 'content_width',
			'type'             => 'select',
			'show_option_none' => __( 'Default (Use Layout Width)', 'mai-theme-engine' ),
			'options'          => array(
				'xs'   => __( 'Extra Small', 'mai-theme-engine' ),
				'sm'   => __( 'Small', 'mai-theme-engine' ),
				'md'   => __( 'Medium', 'mai-theme-engine' ),
				'lg'   => __( 'Large', 'mai-theme-engine' ),
				'xl'   => __( 'Extra Large', 'mai-theme-engine' ),
				'full' => __( 'Full Width', 'mai-theme-engine' ),
			),
		) );

		// Content Alignment.
		$sections->add_group_field( $section, array(
			'name'             => __( 'Content Alignment', 'mai-theme-engine' ),
			'id'               => 'align_content',
			'type'             => 'select',
			'show_option_none' => __( '- None -', 'genesis' ),
			'options'          => array(
				'left'         => __( 'Left', 'mai-theme-engine' ),
				'lefttop'      => __( 'Left Top', 'mai-theme-engine' ),
				'leftbottom'   => __( 'Left Bottom', 'mai-theme-engine' ),
				'center'       => __( 'Center', 'mai-theme-engine' ),
				'centertop'    => __( 'Center Top', 'mai-theme-engine' ),
				'centerbottom' => __( 'Center Bottom', 'mai-theme-engine' ),
				'right'        => __( 'Right', 'mai-theme-engine' ),
				'righttop'     => __( 'Right Top', 'mai-theme-engine' ),
				'rightbottom'  => __( 'Right Bottom', 'mai-theme-engine' ),
			),
		) );

		// Text Alignment.
		$sections->add_group_field( $section, array(
			'name'             => __( 'Text Alignment', 'mai-theme-engine' ),
			'id'               => 'align',
			'type'             => 'select',
			'show_option_none' => __( '- None -', 'genesis' ),
			'options'          => array(
				'left'   => __( 'Left', 'mai-theme-engine' ),
				'center' => __( 'Center', 'mai-theme-engine' ),
				'right'  => __( 'Right', 'mai-theme-engine' ),
			),
		) );

		// Text Size.
		$sections->add_group_field( $section, array(
			'name'             => __( 'Text Size', 'mai-theme-engine' ),
			'id'               => 'text_size',
			'type'             => 'select',
			'show_option_none' => __( '- None -', 'genesis' ),
			'options' => array(
				'xs' => __( 'Extra Small', 'mai-theme-engine' ),
				'sm' => __( 'Small', 'mai-theme-engine' ),
				'md' => __( 'Medium (Default)', 'mai-theme-engine' ),
				'lg' => __( 'Large', 'mai-theme-engine' ),
				'xl' => __( 'Extra Large', 'mai-theme-engine' ),
			),
		) );

		// Advanced Settings.
		$sections->add_group_field( $section, array(
			'name'       => __( 'Advanced Settings', 'mai-theme-engine' ),
			'id'         => 'advanced_settings_title',
			'type'       => 'title',
			'before_row' => '<div class="mai-section-advanced-settings">',
			'classes'    => 'mai-section-advanced-settings-title',
		) );

		// ID.
		$sections->add_group_field( $section, array(
			'name'            => 'HTML id',
			'id'              => 'id',
			'type'            => 'text',
			'sanitization_cb' => 'sanitize_key',
		) );

		// Class.
		$sections->add_group_field( $section, array(
			'name'            => 'HTML additional classes',
			'id'              => 'class',
			'type'            => 'text',
			'sanitization_cb' => 'mai_sanitize_html_classes',
		) );

		// Context.
		$sections->add_group_field( $section, array(
			'name'            => 'Context',
			'id'              => 'context',
			'type'            => 'text',
			'after_row'       => '</div>',
			'sanitization_cb' => 'sanitize_title_with_dashes',
		) );

	// Title.
	$sections->add_group_field( $section, array(
		'name'       => 'Title',
		'id'         => 'title',
		'type'       => 'text',
		'before_row' => '</div></div><div class="mai-section-content">',
		'attributes' => array(
			'placeholder' => __( 'Enter section title here', 'mai-theme-engine' ),
			'class'       => 'widefat',
		),
	) );

	// Content.
	$sections->add_group_field( $section, array(
		'name'            => 'Content',
		'id'              => 'content',
		'type'            => 'wysiwyg',
		'default'         => '',
		'after_row'       => '</div></div></div>',
		'sanitization_cb' => 'mai_sanitize_post_content',
	) );

	// Import.
	$sections->add_field( array(
		'name'       => 'Sections Import (JSON)',
		'desc'       => 'field description (optional)',
		'default'    => '',
		'id'         => 'mai_sections_json_import',
		'type'       => 'textarea',
		'save_field' => false, // Otherwise CMB2 will end up removing the value.
		// 'attributes' => array(
			// 'readonly' => 'readonly',
			// 'disabled' => 'disabled',
		// ),
	) );

	// Export.
	$sections->add_field( array(
		'name'       => 'Sections Export (JSON)',
		'desc'       => 'field description (optional)',
		'default_cb' => '_mai_get_sections_json',
		'id'         => 'mai_sections_json_export',
		'type'       => 'textarea',
		'save_field' => false, // Otherwise CMB2 will end up removing the value.
		'attributes' => array(
			'readonly' => 'readonly',
			// 'disabled' => 'disabled',
		),
	) );

}

function _mai_get_sections_json( $args, $field ) {
	$sections = get_post_meta( $field->object_id, 'mai_sections', true );
	return json_encode( $sections );
}

/**
 * Fires before fields have been processed/saved.
 *
 * The dynamic portion of the hook name, $object_type, refers to the
 * metabox/form's object type
 *    Usually `post` (this applies to all post-types).
 *    Could also be `comment`, `user` or `options-page`.
 *
 * The dynamic portion of the hook name, $this->cmb_id, is the meta_box id.
 *
 * @param array $cmb       This CMB2 object
 * @param int   $object_id The ID of the current object
 */
// do_action( "cmb2_{$object_type}_process_fields_{$this->cmb_id}", $this, $this->object_id() );
// add_action( 'cmb2_post_process_fields_mai_sections', 'mai_import_section_data', 10, 2 );
function mai_import_section_data( $cmb, $object_id ) {

	// Check required $_POST variables and security nonce
	if ( ! isset( $_POST[ $cmb->nonce() ] ) || ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
		return;
	}

	// If not importing section data.
	if ( empty( $_POST ) || ! isset( $_POST['mai_sections_json_import'] ) || empty( $_POST['mai_sections_json_import'] ) ) {
		return;
	}

	$submission   = trim( $_POST['mai_sections_json_import'] );
	$section_data = json_decode( stripslashes( $submission ), true );

	if ( ! $section_data ) {
		return;
	}

	$cmb->data_to_save['mai_sections'] = array();

	mai_update_sections( $object_id, $section_data );
}

function mai_update_sections( $post_id, $section_data ) {
	if ( empty( $section_data ) || ! is_array( $section_data ) ) {
		return;
	}
	$args = array(
		'bg'            => '',
		'image_id'      => '',
		'image'         => false,
		'overlay'       => '',
		'inner'         => '',
		'height'        => '',
		'content_width' => '',
		'align_content' => '',
		'align'         => '',
		'text_size'     => '',
		'id'            => '',
		'class'         => '',
		'context'       => '',
		'title'         => '',
		'content'       => '',
	);
	foreach ( $section_data as $section ) {
		$section = shortcode_atts( $args, $section );
		foreach ( $section as $index => $value ) {
			if ( 'content' === $index ) {
				$value = wp_kses_post( $value );
			} else {
				$value = sanitize_text_field( $value );
			}

			if ( 'image' === $index && ! empty( $value ) && filter_var( $value, FILTER_VALIDATE_URL ) ) {

				// TODO: Check if same image?
				// IF IMAGE ALREADY EXISTS SOMEHOW?

				$attachment_id       = mai_insert_attachment_from_url( $value );
				$section['image']    = wp_get_attachment_url( $attachment_id );
				$section['image_id'] = $attachment_id;
			}
		}
	}
	update_post_meta( $post_id, 'mai_sections', $section_data );
}

/**
 * Insert an attachment from an URL address.
 *
 * @link   https://gist.github.com/m1r0/f22d5237ee93bcccb0d9
 *
 * @param   string  $url
 * @param   int     $post_id
 * @param   array   $meta_data
 *
 * @return  int     Attachment ID
 */
function mai_insert_attachment_from_url( $url, $post_id = null ) {

	if ( ! class_exists( 'WP_Http' ) ) {
		include_once( ABSPATH . WPINC . '/class-http.php' );
	}

	$http = new WP_Http();
	$response = $http->request( $url );
	if( $response['response']['code'] != 200 ) {
		return false;
	}

	$upload = wp_upload_bits( basename($url), null, $response['body'] );
	if( !empty( $upload['error'] ) ) {
		return false;
	}

	$file_path = $upload['file'];
	$file_name = basename( $file_path );
	$file_type = wp_check_filetype( $file_name, null );
	$attachment_title = sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) );
	$wp_upload_dir = wp_upload_dir();

	$post_info = array(
		'guid'				=> $wp_upload_dir['url'] . '/' . $file_name,
		'post_mime_type'	=> $file_type['type'],
		'post_title'		=> $attachment_title,
		'post_content'		=> '',
		'post_status'		=> 'inherit',
	);

	// Create the attachment
	$attach_id = wp_insert_attachment( $post_info, $file_path, $post_id );

	// Include image.php
	require_once( ABSPATH . 'wp-admin/includes/image.php' );

	// Define attachment metadata
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );

	// Assign metadata to attachment
	wp_update_attachment_metadata( $attach_id,  $attach_data );

	return $attach_id;
}

/**
 * Fires after all fields have been saved.
 *
 * @param  int     $object_id  The ID of the current object
 * @param  string  $updated    Array of field ids that were updated.
 *                             Will only include field ids that had values change.
 * @param  array   $cmb        This CMB2 object
 */
// do_action( "cmb2_save_{$object_type}_fields_{$this->cmb_id}", $object_id, $this->updated, $this );
add_action( 'cmb2_save_post_fields_mai_sections', 'mai_import_section_data_og', 10, 3 );
function mai_import_section_data_og( $object_id, $updated, $cmb ) {

	// Check required $_POST variables and security nonce
	if ( ! isset( $_POST[ $cmb->nonce() ] ) || ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
		return;
	}

	// If not importing section data.
	if ( empty( $_POST ) || ! isset( $_POST['mai_sections_json_import'] ) || empty( $_POST['mai_sections_json_import'] ) ) {
		return;
	}

	$submission   = trim( $_POST['mai_sections_json_import'] );
	$section_data = json_decode( stripslashes( $submission ), true );

	if ( ! $section_data ) {
		return;
	}

	mai_update_sections( $object_id, $section_data );
}

// add_action( 'cmb2_after_init', 'yourprefix_handle_frontend_new_post_form_submission' );
function yourprefix_handle_frontend_new_post_form_submission() {

	// If no form submission, bail.
	if ( empty( $_POST ) || ! isset( $_POST['submit-cmb'], $_POST['object_id'] ) ) {
		return false;
	}
	// Get CMB2 metabox object
	$cmb = yourprefix_frontend_cmb2_get();
	$post_data = array();
	// Get our shortcode attributes and set them as our initial post_data args
	if ( isset( $_POST['atts'] ) ) {
		foreach ( (array) $_POST['atts'] as $key => $value ) {
			$post_data[ $key ] = sanitize_text_field( $value );
		}
		unset( $_POST['atts'] );
	}
	// Check security nonce
	if ( ! isset( $_POST[ $cmb->nonce() ] ) || ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
		return $cmb->prop( 'submission_error', new WP_Error( 'security_fail', __( 'Security check failed.' ) ) );
	}

}

/**
 * Save section meta content to the_content for search indexing and SEO content analysis.
 *
 * @param   int     $post_id  The ID of the current object
 * @param   string  $updated  Array of field ids that were updated.
 *                            Will only include field ids that had values change.
 * @param   array   $cmb      This CMB2 object
 *
 * @return  void.
 */
add_action( 'cmb2_save_post_fields_mai_sections', 'mai_save_sections_to_the_content', 10, 3 );
function mai_save_sections_to_the_content( $post_id, $updated, $cmb ) {

	// Get the sections.
	$sections = get_post_meta( $post_id, 'mai_sections', true );

	// Bail if no sections.
	if ( ! $sections ) {
		return;
	}

	// Get the page template.
	$template = get_post_meta( $post_id, '_wp_page_template', true );

	// Bail if switching away from Sections template.
	if ( 'sections.php' !== $template ) {
		return;
	}

	// Setup basic HTML.
	$content = '';
	foreach ( $sections as $section ) {
		$content .= ! empty( $section['title'] ) ? sprintf( '<h2>%s</h2>', $section['title'] ) . "\r\n" : '';
		$content .= $section['content'] . "\r\n";
	}

	// Remove this function so it doesn't cause infinite loop error.
	remove_action( 'cmb2_save_post_fields_mai_sections', 'mai_save_sections_to_the_content', 10, 3 );

	// Update the post content in the DB.
	$updated = wp_update_post( array(
		'ID'           => $post_id,
		'post_content' => $content,
	) );

	// Add this function back.
	add_action( 'cmb2_save_post_fields_mai_sections', 'mai_save_sections_to_the_content', 10, 3 );
}

/**
 * When change from another template to the Sections template, update the first section with the post content.
 * When changing from the Sections template to another template, delete the section meta.
 *
 * @since   1.3.0
 *
 * @param   null|bool  $check       Whether to allow updating metadata for the given type.
 * @param   int        $object_id   Object ID.
 * @param   string     $meta_key    Meta key.
 * @param   mixed      $meta_value  Meta value. Must be serializable if non-scalar.
 * @param   mixed      $prev_value  Optional. If specified, only update existing
 *                                  metadata entries with the specified value.
 *                                  Otherwise, update all entries.
 *
 * @return  mixed
 */
add_filter( 'update_post_metadata', 'mai_update_to_or_from_sections_template', 10, 5 );
function mai_update_to_or_from_sections_template( $check, $object_id, $meta_key, $meta_value, $prev_value ) {

	// Bail if no value change or not updating page template.
	if ( $meta_value === $prev_value || '_wp_page_template' !== $meta_key ) {
		return $check;
	}

	/**
	 * We must get the actual existing template value
	 * because $prev_value is not passed for _wp_page_template.
	 */
	$previous_value = get_post_meta( $object_id, '_wp_page_template', true );

	// Bail if the existing template is the same as the updated one.
	if ( $previous_value === $meta_value ) {
		return $check;
	}

	// If changing TO Sections template.
	if ( 'sections.php' === $meta_value ) {

		// Update the first section content with the post content.
		$sections = array( array( 'content' => get_post_field( 'post_content', $object_id ) ) );
		update_post_meta( $object_id, 'mai_sections', $sections );

	}
	// If changing FROM Sections template.
	elseif ( 'sections.php' === $previous_value ) {

		// Delete the section meta.
		delete_post_meta( $object_id, 'mai_sections' );

	}

	return $check;
}

/**
 * Inline script to display a warning when toggling to another page template than Sections.
 * This is only loaded on existing Sections template admin pages.
 *
 * @since   1.3.0
 *
 * @access  private
 *
 * @return  void
 */
add_action( 'cmb2_after_post_form_mai_sections', 'mai_change_from_sections_template_warning', 20, 2 );
function mai_change_from_sections_template_warning( $object_id, $cmb ) {

	$alert = __( 'Warning! Changing to another page template will lose delete Sections template settings and data. Your content will be moved to the regular editor, but there is no going back!', 'mai-theme-engine' );

	printf( "<script>
		jQuery(window).load( function() {
			$( '#pageparentdiv' ).on( 'change', 'select#page_template', function() {
				if ( 'sections.php' !== $(this).val() ) {
					alert( '%s' );
				}
			});
		});
	</script>", $alert );
}
