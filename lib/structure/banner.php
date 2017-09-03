<?php

/**
 * Add custom banner area body class.
 *
 * @param   array  The existing body classes.
 *
 * @return  array  Modified classes.
 */
add_filter( 'body_class', 'mai_do_banner_area_body_class' );
function mai_do_banner_area_body_class( $classes ) {
	// Bail if no banner area
	if ( ! mai_is_banner_area_enabled() ) {
		return $classes;
	}
	$classes[] = 'has-banner-area';
	return $classes;
}

/**
 * Check if we need to display the banner area
 *
 * @return  void
 */
add_action( 'genesis_before_content_sidebar_wrap', 'mai_do_banner_area' );
function mai_do_banner_area() {

	// Bail if banner area is not enabled or banner is hidden on this page
	if ( ! mai_is_banner_area_enabled() ) {
		return;
	}

	// Remove archive titles/descriptions, we'll add them back later in the banner area
	remove_action( 'genesis_before_loop', 'genesis_do_taxonomy_title_description', 15 );
	remove_action( 'genesis_before_loop', 'genesis_do_author_title_description', 15 );
	remove_action( 'genesis_before_loop', 'genesis_do_author_box_archive', 15 );
	remove_action( 'genesis_before_loop', 'genesis_do_cpt_archive_title_description' );
	remove_action( 'genesis_before_loop', 'genesis_do_date_archive_title' );
	remove_action( 'genesis_before_loop', 'genesis_do_posts_page_heading' );
	remove_action( 'genesis_before_loop', 'genesis_do_search_title' );

	$args = array(
		'class'         => 'banner-area width-full',
		'wrap'          => true,
		'bg'            => genesis_get_option( 'banner_background_color' ),
		'overlay'       => genesis_get_option( 'banner_overlay' ),
		'inner'         => genesis_get_option( 'banner_inner' ),
		'content_width' => genesis_get_option( 'banner_content_width' ),
		'height'        => 'sm',
		'styles'        => '',
	);

	// Get the image ID
	$image_id = mai_get_banner_id();

	// Maybe add image background
	if ( $image_id ) {
		$args['image'] = $image_id;
	}

	// Get the alignment setting
	$align_text = genesis_get_option( 'banner_align_text' );

	// Maybe add the align_text class
	if ( $align_text ) {
		// Get the form by type
		switch ( $align_text ) {
			case 'left':
				$args['class'] .= ' text-xs-left';
			break;
			case 'center':
				$args['class'] .= ' text-xs-center';
			break;
			case 'right':
				$args['class'] .= ' text-xs-right';
			break;
		}
	}

	// Add a filter so devs can change these defaults
	$args = apply_filters( 'mai_banner_args', $args );

	ob_start();
	/**
	 * Custom hook for banner content.
	 * Won't get used if banner area is not displayed.
	 */
	do_action( 'mai_banner_content', $args );
	$content = ob_get_clean();

	echo mai_get_section( $content, $args );
}

/**
 * Output default Genesis content in the banner
 * These won't fire if banner area is not enabled since that hook won't exist
 *
 * @return  void
 */
add_action( 'mai_banner_content', 'mai_do_banner_content' );
function mai_do_banner_content() {

	// Core G functions, that have their own conditionals
	genesis_do_taxonomy_title_description();
	genesis_do_cpt_archive_title_description();
	genesis_do_date_archive_title();

	// If front page displays your latest posts
	if ( is_front_page() && is_home() ) {
		printf( '<h1 class="archive-title">%s</h1>', __( 'Blog', 'genesis' ) );
	}

	// Add static front page banner content
	elseif ( is_front_page() && $front_page_id = get_option( 'page_on_front' ) ) {

		// Remove post title
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
		remove_action( 'genesis_entry_header', 'genesis_do_post_title' );

		// Use an h2 on front page, since the site title/logo is h1
		add_filter( 'genesis_entry_title_wrap', 'mai_filter_entry_title_wrap' );
		function mai_filter_entry_title_wrap( $wrap ) {
			return 'h2';
		}

		genesis_do_post_title();
		echo has_excerpt( get_the_ID() ) ? wpautop( get_the_excerpt( get_the_ID() ) ) : '';

	}

	// Do static blog banner content
	elseif ( is_home() && $posts_page_id = get_option( 'page_for_posts' ) ) {
		printf( '<div %s>', genesis_attr( 'posts-page-description' ) );
			printf( '<h1 %s>%s</h1>', genesis_attr( 'archive-title' ), get_the_title( $posts_page_id ) );
			echo has_excerpt( $posts_page_id ) ? wpautop( get_the_excerpt( $posts_page_id ) ) : '';
		echo '</div>';
	}

	// Do singular banner content
	elseif ( is_singular() && ! is_front_page() && ! is_home() ) {

		// Remove post title
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
		remove_action( 'genesis_entry_header', 'genesis_do_post_title' );

		genesis_do_post_title();
		echo has_excerpt( get_the_ID() ) ? wpautop( get_the_excerpt( get_the_ID() ) ) : '';
	}

	// Do author archive banner content
	elseif ( is_author() ) {
		// If author box is enabled, show it
		if ( get_the_author_meta( 'genesis_author_box_archive', get_query_var( 'author' ) ) ) {

			// Return only the name for the author box, not "About {name}"
			add_filter( 'genesis_author_box_title', function() {
				return get_the_author();
			});

			genesis_do_author_box_archive();
		}
		// Otherwise, show the default title and description
		else {
			genesis_do_author_title_description();
		}
	}

	elseif ( is_search() ) {
		genesis_do_search_title();
	}

	elseif ( is_404() ) {
		printf( '<div class="entry-title">%s</div>', __( '404', 'mai-pro-engine' ) );
	}

	// Bail if WooCommerce is not active
	elseif ( class_exists( 'WooCommerce' ) ) {

		 if ( is_shop() && ( $shop_page_id = get_option( 'woocommerce_shop_page_id' ) ) ) {
			// Get our new data
			$headline   = get_the_title( $shop_page_id );
			$headline   = $headline ? sprintf( '<h1 %s>%s</h1>', genesis_attr( 'archive-title' ), strip_tags( $headline ) ) : '';
			$intro_text = has_excerpt( $shop_page_id ) ? get_the_excerpt( $shop_page_id ) : '';
			printf( '<div %s>%s</div>', genesis_attr( 'cpt-archive-description' ), $headline . $intro_text );
		} elseif ( is_product() ) {

			// Use an h2 on front page, since the product title will be h1
			// We have to do this up top because is_singular() will output product title
			add_filter( 'genesis_entry_title_wrap', 'mai_filter_entry_title_wrap' );
			function mai_filter_entry_title_wrap( $wrap ) {
				return 'h2';
			}
		}

	}

	// Remove the filter so it doesn't affect anything later
	remove_filter( 'genesis_entry_title_wrap', 'mai_filter_entry_title_wrap' );

	// Add back the entry header/title incase custom queries & loops need it
	add_action( 'genesis_before_entry_content', function() {
		add_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
		add_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
		add_action( 'genesis_entry_header', 'genesis_do_post_title' );
	});

}
