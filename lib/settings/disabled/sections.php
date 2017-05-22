<?php

add_action( 'cmb2_admin_init', 'mai_do_sections_metabox' );
function mai_do_sections_metabox() {

	// Posts/Pages/CPTs
	$sections = new_cmb2_box( array(
		'id'			=> 'mai_sections',
		'title'			=> __( 'Sections', 'maitheme' ),
		'object_types'	=> array( 'page' ),
		'context'		=> 'normal',
		'priority'		=> 'high',
		'classes' 		=> 'mai-metabox',
		'show_on' 		=> array( 'key' => 'page-template', 'value' => 'sections.php' ),
	) );

	$section = $sections->add_field( array(
	    'id'          => 'mai_sections',
	    'type'        => 'group',
	    'repeatable'  => true,
	    'options'     => array(
			'group_title'	=> __( 'Section #{#}', 'maitheme' ),
			'add_button'	=> __( 'Add Section', 'maitheme' ),
			'remove_button'	=> __( 'Remove Section', 'maitheme' ),
			'sortable'		=> true,
	    ),
	) );

	// Style
	$sections->add_group_field( $section, array(
		'name'			=> __( 'Background Image', 'maitheme' ),
		'id'			=> 'image',
		'type'			=> 'file',
		'preview_size'	=> 'one-third',
		'options'		=> array( 'url' => false ),
	    'text' 			=> array(
	        'add_upload_file_text' => __( 'Add Image', 'maitheme' ),
	    ),
	) );

	// Style Settings
	$sections->add_group_field( $section, array(
		'name'				=> __( 'Settings', 'maitheme' ),
		'id'				=> 'settings',
		'type'				=> 'multicheck',
		'select_all_button'	=> false,
		// 'default' 			=> mai_set_checkbox_default_for_new_post( 'wrap' ),
		'options' => array(
			'overlay'	=> __( 'Add image overlay', 'maitheme' ),
			// 'wrap'		=> __( 'Add content wrap', 'maitheme' ),
			'inner'		=> __( 'Add content inner styling', 'maitheme' ),
		),
	) );

	// Height
	$sections->add_group_field( $section, array(
		'name'             => __( 'Height', 'maitheme' ),
		'id'               => 'height',
		'type'             => 'select',
		'default'          => 'md',
		'options'          => array(
			'auto'	=> __( 'Auto (Use height of content)', 'maitheme' ),
			'sm'	=> __( 'Small', 'maitheme' ),
			'md'	=> __( 'Medium', 'maitheme' ),
			'lg'	=> __( 'Large', 'maitheme' ),
		),
	) );

	// Content Width
	$sections->add_group_field( $section, array(
		'name'             => __( 'Content Width', 'maitheme' ),
		'id'               => 'content_width',
		'type'             => 'select',
		'show_option_none' => __( 'Default (Use Layout Width)', 'maitheme' ),
		'options'          => array(
			'xs'	=> __( 'Extra Small', 'maitheme' ),
			'sm'	=> __( 'Small', 'maitheme' ),
			'md'	=> __( 'Medium', 'maitheme' ),
			'lg'	=> __( 'Large', 'maitheme' ),
			'xl'	=> __( 'Extra Large', 'maitheme' ),
			'full'	=> __( 'Full Width', 'maitheme' ),
		),
	) );

	// Content
	$sections->add_group_field( $section, array(
	    'name'             => 'Content',
	    'id'               => 'content',
	    'type'             => 'wysiwyg',
	) );

}

/**
 * Only return default value if we don't have a post ID (in the 'post' query variable)
 *
 * @param  bool  $default On/Off (true/false)
 * @return mixed          Returns true or '', the blank default
 */
function mai_set_checkbox_default_for_new_post( $default ) {
	return isset( $_GET['post'] ) ? '' : ( $default ? (string) $default : '' );
}
