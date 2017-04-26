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
 * @version  1.0.7
 */


/**
 * Check if we need to display the banner area
 *
 * @return  void
 */
add_action( 'genesis_before_content_sidebar_wrap', 'mai_maybe_do_banner_area' );
function mai_maybe_do_banner_area() {

	// Bail if banner area is not enabled
	if ( ! mai_is_banner_area_enabled() ) {
		return;
	}

	// Get the banner visibility meta
	if ( is_singular() ) {
		$hide_banner = get_post_meta( get_the_ID(), 'mai_hide_banner', true );
	} elseif ( is_tax() ) {
		$hide_banner = get_term_meta( get_queried_object_id(), 'mai_hide_banner', true );
	} elseif ( is_author() ) {
		$hide_banner = get_user_meta( get_queried_object_id(), 'mai_hide_banner', true );
	} else {
		$hide_banner = false;
	}

	// Disable banner if checkbox is checked
	if ( $hide_banner ) {
		return;
	}

	mai_do_banner_area();

}

/**
 * Main function to display the banner area
 *
 * @return void
 */
function mai_do_banner_area() {

	// Remove archive titles/descriptions, we'll add them back later in the banner area
	remove_action( 'genesis_before_loop', 'genesis_do_taxonomy_title_description', 15 );
	remove_action( 'genesis_before_loop', 'genesis_do_author_title_description', 15 );
	remove_action( 'genesis_before_loop', 'genesis_do_author_box_archive', 15 );
	remove_action( 'genesis_before_loop', 'genesis_do_cpt_archive_title_description' );
	remove_action( 'genesis_before_loop', 'genesis_do_date_archive_title' );
	remove_action( 'genesis_before_loop', 'genesis_do_posts_page_heading' );
	remove_action( 'genesis_before_loop', 'genesis_do_search_title' );

	// Set defaults
	$image_id = $image = $image_url = $style = '';

	$default_id = get_option( 'banner_id' );

	// TODO: Convert all the following to helper function mai_get_banner_id()

	if ( is_front_page() ) {
		$image_id = get_post_meta( get_the_ID(), 'banner_id', true );
		if ( ! ( $image_id || $default_id ) ) {
			$image_id = get_post_thumbnail_id();
		}
	}
	elseif ( is_home() ) {
		$home_id  = get_option( 'page_for_posts' );
		if ( $home_id ) {
			$image_id = get_post_meta( $home_id, 'banner_id', true );
		}
		if ( ! ( $image_id || $default_id ) ) {
			$image_id = get_post_thumbnail_id( $home_id );
		}
	}
	elseif ( is_singular() && ! is_front_page() ) {
		$image_id = get_post_meta( get_the_ID(), 'banner_id', true );
		if ( ! ( $image_id || $default_id ) ) {
			$image_id = get_post_thumbnail_id();
		}
	}
	elseif ( is_category() || is_tag() || is_tax() ) {
		if ( is_tax( array( 'product_cat' ) ) ) {
		    $image_id = get_term_meta( get_queried_object()->term_id, 'thumbnail_id', true );
		} else {
			$image_id = get_term_meta( get_queried_object()->term_id, 'banner_id', true );
		}
	}
	// elseif ( is_post_type_archive() && genesis_has_post_type_archive_support() ) {
		// No option to easily add image upload fields to Genesis CPT archives :(
		// For now we have to use mai_banner_area_args
	// }
	elseif ( is_author() ) {
		$author	  = get_user_by( 'slug', get_query_var( 'author_name' ) );
		$image_id = get_user_meta( $author->ID, 'banner_id', true );
	}
	elseif ( class_exists( 'WooCommerce' ) && is_shop() ) {
		$shop_id  = get_option( 'woocommerce_shop_page_id' );
		$image_id = get_post_meta( $shop_id, 'banner_id', true );
		if ( ! ( $image_id || $default_id ) ) {
			$image_id = get_post_thumbnail_id( $shop_id );
		}
	}

	/**
	 * If no banner override use the default banner image
	 * Banner image may be false
	 */
	if ( ! $image_id && get_option( 'banner_id' ) ) {
		$image_id = absint( $default_id );
	}

    $args = array(
		'class'		=> 'banner-area',
		'image'		=> $image_id,
		'overlay'	=> true,
		'wrap'		=> true,
		'inner'		=> false,
    );

    // Filter these defaults, this allows the /lib/ to be updated later without affecting a customized theme
    $args = apply_filters( 'mai_banner_area_args', $args );

    // Opening markup
    echo mai_get_section_open( $args );

    /**
     * Custom hook for banner content
     * Won't get used if banner area is not displayed
     */
	do_action( 'mai_banner_content' );

    // Closing markup
    echo mai_get_section_close( $args );

}

/**
 * Output default Genesis content in the banner
 * These won't fire if banner area is not enabled since that hook won't exist
 */
add_action( 'mai_banner_content', 'mai_do_banner_content' );
function mai_do_banner_content() {

	// Core G functions, that have their own conditionals
	genesis_do_taxonomy_title_description();
	genesis_do_cpt_archive_title_description();
	genesis_do_date_archive_title();

	// Bail if WooCommerce is not active
	if ( class_exists( 'WooCommerce' ) ) {

		 if ( is_shop() ) {
		    // Get our new data
		    $shop_id 	= get_option( 'woocommerce_shop_page_id' );
			$post		= get_post( $shop_id );
			$headline	= $post->post_title;
			$intro_text = $post->post_excerpt;
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

	// Add front page banner content
	if ( is_front_page() ) {

		// Use an h2 on front page, since the site title/logo is h1
		add_filter( 'genesis_entry_title_wrap', 'mai_filter_entry_title_wrap' );
		function mai_filter_entry_title_wrap( $wrap ) {
			return 'h2';
		}

		// We have to create a loop, so these functions get the right data
		if ( have_posts() ) {
	        while ( have_posts() ) : the_post();
				genesis_do_post_title();
				genesis_do_post_content();
			endwhile;
		}

	}

	// Do home (blog) banner content
	if ( is_home() ) {
		$posts_page = get_option( 'page_for_posts' );
		if ( is_null( $posts_page ) ) {
			return;
		}
		printf( '<div %s>', genesis_attr( 'posts-page-description' ) );
			printf( '<h1 %s>%s</h1>', genesis_attr( 'archive-title' ), get_the_title( $posts_page ) );
			echo apply_filters( 'genesis_cpt_archive_intro_text_output', get_post( $posts_page )->post_content );
		echo '</div>';
	}

	// Do singular banner content
	if ( is_singular() && ! is_front_page() ) {

		// Remove post title
		remove_action( 'genesis_entry_header', 'genesis_do_post_title' );

		global $post;
		genesis_do_post_title();
		echo apply_filters( 'genesis_cpt_archive_intro_text_output', $post->post_excerpt );
	}

	// Do author archive banner content
	if ( is_author() ) {
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

	if ( is_search() ) {
		genesis_do_search_title();
	}

	// Remove the filter so it doesn't affect anything later
	remove_filter( 'genesis_entry_title_wrap', 'mai_filter_entry_title_wrap' );

}
