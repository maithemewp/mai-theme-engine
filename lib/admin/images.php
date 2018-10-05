<?php

/**
 * Add our image sizes to the media chooser.
 *
 * @param   $sizes  The size options.
 *
 * @return  array   Modified size options.
 */
add_filter( 'image_size_names_choose', 'mai_do_media_chooser_sizes' );
function mai_do_media_chooser_sizes( $sizes ) {
	$addsizes = array(
		'featured'   => __( 'Featured', 'mai-theme-engine' ),
		'one-half'   => __( 'One Half', 'mai-theme-engine' ),
		'one-third'  => __( 'One Third', 'mai-theme-engine' ),
		'one-fourth' => __( 'One Fourth', 'mai-theme-engine' ),
		'banner'     => __( 'Banner', 'mai-theme-engine' ),
		'section'    => __( 'Section', 'mai-theme-engine' ),
	);
	$newsizes = array_merge( $sizes, $addsizes );
	return $newsizes;
}

/**
 * Remove unsupported FlexGrid gallery options from admin
 *
 * @return void
 */
add_action( 'admin_head', 'mai_remove_unsupported_flexgrid_gallery_options' );
function mai_remove_unsupported_flexgrid_gallery_options() {
	echo '<style type="text/css">
		.gallery-settings .columns option[value="5"],
		.gallery-settings .columns option[value="7"],
		.gallery-settings .columns option[value="8"],
		.gallery-settings .columns option[value="9"] {
			display:none !important;
			visibility: hidden !important;
		}
	</style>';
}
