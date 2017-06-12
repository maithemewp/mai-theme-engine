<?php

add_action( 'admin_head', 'hide_editor' );
function hide_editor() {
	global $pagenow;

	if ( 'post.php' != $pagenow ) {
		return;
	}
	if ( ! isset( $_GET['post'] ) ) {
		return;
	}
	if ( 'sections.php' == get_page_template_slug( absint( $_GET['post'] ) ) ) {
		remove_post_type_support('page', 'editor');
	}
}

add_action( 'cmb2_admin_init', 'mai_do_sections_metabox' );
function mai_do_sections_metabox() {

	// Posts/Pages/CPTs
	$sections = new_cmb2_box( array(
		'id'			=> 'mai_sections',
		'title'			=> __( 'Sections', 'mai-pro-engine' ),
		'object_types'	=> array( 'page' ),
		'context'		=> 'after_editor',
		'classes' 		=> 'mai-metabox',
		'show_on' 		=> array( 'key' => 'page-template', 'value' => 'sections.php' ),
	) );

	$section = $sections->add_field( array(
	    'id'          => 'mai_sections',
	    'type'        => 'group',
	    'repeatable'  => true,
	    'options'     => array(
			'group_title'	=> __( 'Section #{#}', 'mai-pro-engine' ),
			'add_button'	=> __( 'Add Section', 'mai-pro-engine' ),
			'remove_button'	=> __( 'Remove Section', 'mai-pro-engine' ),
			'sortable'		=> true,
	    ),
	) );

	// Background Color
	$sections->add_group_field( $section, array(
		'name'		 => __( 'Background Color', 'mai-pro-engine' ),
		'id'		 => 'bg',
		'before_row' => '<div class="cmb-section-settings"><div class="mai-row"><div class="mai-col mai-col-xs-12 mai-col-sm-6 mai-col-xl">',
		'type'		 => 'colorpicker',
		'default'	 => '', // Keep this empty, so CSS can control the defaults if left untouched
	) );

	// Background Image
	$sections->add_group_field( $section, array(
		'name'			=> __( 'Background Image', 'mai-pro-engine' ),
		'id'			=> 'image',
		'after_row'		=> '</div>',
		'type'			=> 'file',
		'preview_size'	=> 'one-third',
		'options'		=> array( 'url' => false ),
		'text'			=> array(
	        'add_upload_file_text' => __( 'Add Image', 'mai-pro-engine' ),
	    ),
	) );

	// Overlay
	$sections->add_group_field( $section, array(
		'name'				=> __( 'Overlay Style', 'mai-pro-engine' ),
		'id'				=> 'overlay',
		'before_row'		=> '<div class="mai-col mai-col-xs-12 mai-col-sm-6 mai-col-xl">',
		'type'				=> 'select',
		'select_all_button'	=> false,
		'options'			=> array(
			''			=> __( '- None -', 'mai-pro-engine' ),
			'gradient'	=> __( 'Gradient', 'mai-pro-engine' ),
			'light'		=> __( 'Light', 'mai-pro-engine' ),
			'dark'		=> __( 'Dark', 'mai-pro-engine' ),
		),
	) );

	// Inner
	$sections->add_group_field( $section, array(
		'name'				=> __( 'Inner Style', 'mai-pro-engine' ),
		'id'				=> 'inner',
		// 'before_row'		=> '<div class="mai-col mai-col-xs-12 mai-col-sm-6 mai-col-xl">',
		'after_row'			=> '</div>',
		'type'				=> 'select',
		'select_all_button'	=> false,
		'options'			=> array(
			''		=> __( '- None -', 'mai-pro-engine' ),
			'light' => __( 'Light Box', 'mai-pro-engine' ),
			'dark'  => __( 'Dark Box', 'mai-pro-engine' ),
		),
	) );

	// Height
	$sections->add_group_field( $section, array(
		'name'			=> __( 'Height', 'mai-pro-engine' ),
		'id'			=> 'height',
		'before_row'	=> '<div class="mai-col mai-col-xs-12 mai-col-sm-6 mai-col-xl">',
		'type'			=> 'select',
		'default'		=> 'md',
		'options'		=> array(
			'auto'	=> __( 'Auto (Use height of content)', 'mai-pro-engine' ),
			'sm'	=> __( 'Small', 'mai-pro-engine' ),
			'md'	=> __( 'Medium', 'mai-pro-engine' ),
			'lg'	=> __( 'Large', 'mai-pro-engine' ),
		),
	) );

	// Content Width
	$sections->add_group_field( $section, array(
		'name'				=> __( 'Content Width', 'mai-pro-engine' ),
		'id'				=> 'content_width',
		'after_row'			=> '</div></div></div>',
		'type'				=> 'select',
		'show_option_none'	=> __( 'Default (Use Layout Width)', 'mai-pro-engine' ),
		'options'			=> array(
			'xs'	=> __( 'Extra Small', 'mai-pro-engine' ),
			'sm'	=> __( 'Small', 'mai-pro-engine' ),
			'md'	=> __( 'Medium', 'mai-pro-engine' ),
			'lg'	=> __( 'Large', 'mai-pro-engine' ),
			'xl'	=> __( 'Extra Large', 'mai-pro-engine' ),
			'full'	=> __( 'Full Width', 'mai-pro-engine' ),
		),
	) );

	// Title
	$sections->add_group_field( $section, array(
		'name'	=> 'Title',
		'id'	=> 'title',
		'type'	=> 'text',
		'attributes'  => array(
			'placeholder' => __( 'Enter section title here', 'mai-pro-engine' ),
			'class'        => 'widefat',
		),
	) );

	// Content
	$sections->add_group_field( $section, array(
		'name'	=> 'Content',
		'id'	=> 'content',
		'type'	=> 'wysiwyg',
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
