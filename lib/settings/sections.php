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
		'title'			=> __( 'Sections', 'mai-pro' ),
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
			'group_title'	=> __( 'Section #{#}', 'mai-pro' ),
			'add_button'	=> __( 'Add Section', 'mai-pro' ),
			'remove_button'	=> __( 'Remove Section', 'mai-pro' ),
			'sortable'		=> true,
	    ),
	) );

	// Style
	$sections->add_group_field( $section, array(
		'name'			=> __( 'Background Image', 'mai-pro' ),
		'id'			=> 'image',
		'before_row'	=> '<div class="cmb-section-settings"><div class="cmb-flex-wrap"><div class="cmb-flex-item">',
		'after_row'		=> '</div>',
		'type'			=> 'file',
		'preview_size'	=> 'one-third',
		'options'		=> array( 'url' => false ),
		'text'			=> array(
	        'add_upload_file_text' => __( 'Add Image', 'mai-pro' ),
	    ),
	) );

	// Style Settings
	$sections->add_group_field( $section, array(
		'name'				=> __( 'Image Style', 'mai-pro' ),
		'id'				=> 'settings',
		'before_row'		=> '<div class="cmb-flex-item">',
		'after_row'			=> '</div>',
		'type'				=> 'multicheck',
		'select_all_button'	=> false,
		'options'			=> array(
			'overlay'	=> __( 'Add image overlay', 'mai-pro' ),
			'inner'		=> __( 'Add content inner styling', 'mai-pro' ),
		),
	) );

	// Height
	$sections->add_group_field( $section, array(
		'name'			=> __( 'Height', 'mai-pro' ),
		'id'			=> 'height',
		'before_row'	=> '<div class="cmb-flex-item">',
		'after_row'		=> '</div>',
		'type'			=> 'select',
		'default'		=> 'md',
		'options'		=> array(
			'auto'	=> __( 'Auto (Use height of content)', 'mai-pro' ),
			'sm'	=> __( 'Small', 'mai-pro' ),
			'md'	=> __( 'Medium', 'mai-pro' ),
			'lg'	=> __( 'Large', 'mai-pro' ),
		),
	) );

	// Content Width
	$sections->add_group_field( $section, array(
		'name'				=> __( 'Content Width', 'mai-pro' ),
		'id'				=> 'content_width',
		'before_row'		=> '<div class="cmb-flex-item">',
		'after_row'			=> '</div></div></div>',
		'type'				=> 'select',
		'show_option_none'	=> __( 'Default (Use Layout Width)', 'mai-pro' ),
		'options'			=> array(
			'xs'	=> __( 'Extra Small', 'mai-pro' ),
			'sm'	=> __( 'Small', 'mai-pro' ),
			'md'	=> __( 'Medium', 'mai-pro' ),
			'lg'	=> __( 'Large', 'mai-pro' ),
			'xl'	=> __( 'Extra Large', 'mai-pro' ),
			'full'	=> __( 'Full Width', 'mai-pro' ),
		),
	) );

	// Title
	$sections->add_group_field( $section, array(
		'name'	=> 'Title',
		'id'	=> 'title',
		'type'	=> 'text',
		'attributes'  => array(
			'placeholder' => __( 'Enter section title here', 'mai-pro' ),
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
