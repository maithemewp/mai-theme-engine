<?php
/**
 * Mai Theme.
 *
 * WARNING: This file is part of the core Mai Theme framework.
 * The goal is to keep all files in /lib/ untouched.
 * That way we can easily update the core structure of the theme on existing sites without breaking things
 *
 * @author   Mike Hemberger
 *
 * @version  1.0.8
 */


/**
 * Check if we need to display the banner area
 *
 * @return  void
 */
add_action( 'genesis_before_content_sidebar_wrap', 'mai_do_banner_area' );
function mai_do_banner_area() {

	// Bail if banner area is not enabled or banner is hidden on this page
	if ( ! mai_is_banner_area_enabled() || mai_is_hide_banner() ) {
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
		'class'			=> 'banner-area width-full',
		'overlay'		=> get_theme_mod( 'enable_banner_overlay', 1 ),
		'wrap'			=> true,
		'inner'			=> get_theme_mod( 'enable_banner_inner', 0 ),
		'content_width'	=> get_theme_mod( 'banner_content_width', 'lg' ),
    );

	// Get the image ID
	$image_id = mai_get_banner_id();

	// Maybe add image background
	if ( $image_id ) {
		$args['image'] = $image_id;
	}

    // Add a filter so devs can change these defaults
    $args = apply_filters( 'mai_banner_args', $args );

    // Opening markup
    echo mai_get_section_open( $args );

    /**
     * Custom hook for banner content
     * Won't get used if banner area is not displayed
     */
	do_action( 'mai_banner_content', $args );

    // Closing markup
    echo mai_get_section_close( $args );

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

	// Add static front page banner content
	if ( is_front_page() && $front_page_id = get_option( 'page_on_front' ) ) {

		// Remove the edit link, cause it's super ugly here.
		add_filter ( 'genesis_edit_post_link' , '__return_false' );

		// Remove post title
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
		remove_action( 'genesis_entry_header', 'genesis_do_post_title' );

		// Use an h2 on front page, since the site title/logo is h1
		add_filter( 'genesis_entry_title_wrap', 'mai_filter_entry_title_wrap' );
		function mai_filter_entry_title_wrap( $wrap ) {
			return 'h2';
		}

		genesis_do_post_title( $front_page_id );
		get_the_excerpt( $front_page_id );

	}

	// Do static blog banner content
	elseif ( is_home() && $posts_page_id = get_option( 'page_for_posts' ) ) {
		printf( '<div %s>', genesis_attr( 'posts-page-description' ) );
			printf( '<h1 %s>%s</h1>', genesis_attr( 'archive-title' ), get_the_title( $posts_page_id ) );
			echo apply_filters( 'genesis_cpt_archive_intro_text_output', get_post( $posts_page_id )->post_excerpt );
		echo '</div>';
	}

	// Do singular banner content
	elseif ( is_singular() && ! is_front_page() && ! is_home() ) {

		// Remove post title
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
		remove_action( 'genesis_entry_header', 'genesis_do_post_title' );

		global $post;
		genesis_do_post_title();
		echo apply_filters( 'genesis_cpt_archive_intro_text_output', $post->post_excerpt );
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

	// Bail if WooCommerce is not active
	elseif ( class_exists( 'WooCommerce' ) ) {

		 if ( is_shop() && ( $shop_page_id = get_option( 'woocommerce_shop_page_id' ) ) ) {
		    // Get our new data
			// $post		= get_post( $shop_page_id );
			$headline	= get_the_title( $shop_page_id );
			$intro_text = get_the_excerpt( $shop_page_id );
			$headline	= $headline ? sprintf( '<h1 %s>%s</h1>', genesis_attr( 'archive-title' ), strip_tags( $headline ) ) : '';
			$intro_text = $intro_text ? $intro_text : '';
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

}
