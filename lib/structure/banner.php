<?php

/**
 * Add custom banner area body class.
 *
 * @since   1.0.0
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
 * @since   1.0.0
 * @since   1.3.0  Changed from `genesis_before_content_sidebar_wrap' to `genesis_after_header `hook.
 *
 * @return  void
 */
add_action( 'genesis_after_header', 'mai_do_banner_area', 20 );
function mai_do_banner_area() {

	// Bail if banner area is not enabled or banner is hidden on this page.
	if ( ! mai_is_banner_area_enabled() ) {
		return;
	}

	// Remove archive titles/descriptions, we'll add them back later in the banner area.
	remove_action( 'genesis_before_loop', 'genesis_do_taxonomy_title_description', 15 );
	remove_action( 'genesis_before_loop', 'genesis_do_author_title_description', 15 );
	remove_action( 'genesis_before_loop', 'genesis_do_author_box_archive', 15 );
	remove_action( 'genesis_before_loop', 'genesis_do_cpt_archive_title_description' );
	remove_action( 'genesis_before_loop', 'genesis_do_date_archive_title' );
	remove_action( 'genesis_before_loop', 'genesis_do_posts_page_heading' );
	remove_action( 'genesis_before_loop', 'genesis_do_search_title' );

	$args = array(
		'context'       => 'banner-area',
		'class'         => 'banner-area width-full',
		'wrap'          => true,
		'bg'            => genesis_get_option( 'banner_background_color' ),
		'overlay'       => genesis_get_option( 'banner_overlay' ),
		'inner'         => genesis_get_option( 'banner_inner' ),
		'content_width' => genesis_get_option( 'banner_content_width' ),
		'height'        => genesis_get_option( 'banner_height' ),
		'align'         => genesis_get_option( 'banner_align_text' ),
		'align_content' => genesis_get_option( 'banner_align_content' ),
		'styles'        => '',
		'text_size'     => 'lg',
	);

	// Get the image ID.
	$image_id = mai_get_banner_id();

	// Maybe add image background.
	if ( $image_id ) {
		$args['image'] = $image_id;
	}

	// Add a filter so devs can change these defaults.
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
 * Output default Genesis content in the banner.
 * These won't fire if banner area is not enabled since that hook won't exist.
 *
 * @since   1.0.0
 *
 * @return  void
 */
add_action( 'mai_banner_content', 'mai_do_banner_content' );
function mai_do_banner_content() {

	global $wp_query, $post;

	$wrap         = 'h1';
	$before_title = $title = $desc = '';

	// Front page displays your latest posts.
	if ( is_front_page() && is_home() ) {
		$title = __( 'Blog', 'genesis' );
	}

	// Static front page.
	elseif ( is_front_page() && $front_page_id = get_option( 'page_on_front' ) ) {

		// Remove post title
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
		remove_action( 'genesis_entry_header', 'genesis_do_post_title' );

		$title = get_the_title();
		$desc  = has_excerpt( $front_page_id ) ? get_the_excerpt( $front_page_id ) : '';
	}

	// Static blog.
	elseif ( is_home() && $posts_page_id = get_option( 'page_for_posts' ) ) {
		$title = get_the_title( $posts_page_id );
		$desc  = has_excerpt( $posts_page_id ) ? get_the_excerpt( $posts_page_id ) : '';
	}

	// Singular.
	elseif ( is_singular() && ! is_front_page() && ! is_home() ) {

		// Remove default post title.
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
		remove_action( 'genesis_entry_header', 'genesis_do_post_title' );

		$title = get_the_title();

		// Woo single product.
		if ( class_exists( 'WooCommerce' ) && is_product() ) {
			// Use an h2 for the banner title, since the product title will be h1.
			$wrap = 'h2';
		} else {
			// Only show excerpt on non Woo product single entries. Woo products use excerpt as short description in content.
			$desc = has_excerpt( get_the_ID() ) ? get_the_excerpt( get_the_ID() ) : '';
		}
	}

	// CPT archives.
	elseif ( is_post_type_archive() ) {
		// Woo shop page.
		if ( class_exists( 'WooCommerce' ) && is_shop() && ( $shop_page_id = get_option( 'woocommerce_shop_page_id' ) ) ) {
				$title = get_the_title( $shop_page_id );
				$desc  = has_excerpt( $shop_page_id ) ? get_the_excerpt( $shop_page_id ) : '';
		} else {
			if ( genesis_has_post_type_archive_support( get_post_type() ) ) {
				$title = genesis_get_cpt_option( 'headline' );
				$desc  = genesis_get_cpt_option( 'intro_text' );
				$desc  = apply_filters( 'genesis_cpt_archive_intro_text_output', $desc ? $desc : '' );
			}
			if ( empty( $title ) && genesis_a11y( 'headings' ) ) {
				$title = post_type_archive_title( '', false );
			}
		}
	}

	// Term archives.
	elseif ( is_category() || is_tag() || is_tax() ) {
		$term = is_tax() ? get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ) : $wp_query->get_queried_object();
		if ( $term ) {
			$title = get_term_meta( $term->term_id, 'headline', true );
			if ( empty( $title ) && genesis_a11y( 'headings' ) ) {
				$title = $term->name;
			}
		}
		$desc = get_term_meta( $term->term_id, 'intro_text', true );
		$desc = apply_filters( 'genesis_term_intro_text_output', $desc ? $desc : '' );
	}

	// Author archives.
	elseif ( is_author() ) {

		// If author box is enabled.
		if ( get_the_author_meta( 'genesis_author_box_archive', get_query_var( 'author' ) ) ) {
			global $authordata;
			$authordata    = is_object( $authordata ) ? $authordata : get_userdata( get_query_var( 'author' ) );
			$gravatar_size = apply_filters( 'genesis_author_box_gravatar_size', 70, 'archive' );
			$before_title  = get_avatar( get_the_author_meta( 'email' ), $gravatar_size );
			$title         = get_the_author();
			$desc          = wpautop( get_the_author_meta( 'description' ) );
 		}
		// Otherwise show the G author archive settings title.
		else {
			$title = get_the_author_meta( 'headline', (int) get_query_var( 'author' ) );
			if ( empty( $title ) && genesis_a11y( 'headings' ) ) {
				$title = get_the_author_meta( 'display_name', (int) get_query_var( 'author' ) );
			}
			$desc = get_the_author_meta( 'intro_text', (int) get_query_var( 'author' ) );
			$desc = apply_filters( 'genesis_author_intro_text_output', $desc ? $desc : '' );
		}
	}

	// Date archives.
	elseif ( is_date() ) {
		if ( is_day() ) {
			$title = __( 'Archives for ', 'genesis' ) . get_the_date();
		} elseif ( is_month() ) {
			$title = __( 'Archives for ', 'genesis' ) . single_month_title( ' ', false );
		} elseif ( is_year() ) {
			$title = __( 'Archives for ', 'genesis' ) . get_query_var( 'year' );
		}
	}

	// Search results.
	elseif ( is_search() ) {
		$title = apply_filters( 'genesis_search_title_text', __( 'Search Results for:', 'genesis' ) );
	}

	// 404.
	elseif ( is_404() ) {

		// Remove default title.
		add_filter( 'genesis_markup_entry-title_open', '__return_empty_string' );
		add_filter( 'genesis_markup_entry-title_content', '__return_empty_string' );
		add_filter( 'genesis_markup_entry-title_close', '__return_empty_string' );

		$title = apply_filters( 'genesis_404_entry_title', __( 'Not found, error 404', 'genesis' ) );
	}

	// Banner content filters.
	$wrap  = apply_filters( 'mai_banner_title_wrap', $wrap );
	$title = apply_filters( 'mai_banner_title_text', $title );
	$title = $title ? sprintf( '<%s %s>%s</%s>', $wrap, genesis_attr( 'banner-title' ), $title, $wrap ) : '';
	$title = apply_filters( 'mai_banner_title', $title );
	$desc  = apply_filters( 'mai_banner_description', $desc );

	/**
	 * Action hook that fires at end of building banner content.
	 *
	 * Allows you to reorganize output of the archive headings.
	 *
	 * @since   1.3.0
	 *
	 * @param   string  $heading  The banner title.
	 * @param   string  $desc     The banner description.
	 *
	 * @return  void
	 */
	do_action( 'mai_banner_title_description', $title, $desc );

	// Add back the entry header/title because custom queries and loops may need it.
	add_action( 'genesis_before_entry_content', function() {
		add_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
		add_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
		add_action( 'genesis_entry_header', 'genesis_do_post_title' );
	});
}

/**
 * Do the banner title.
 *
 * @since   1.3.0
 *
 * @return  void
 */
add_action( 'mai_banner_title_description', 'mai_do_banner_title', 10, 2 );
function mai_do_banner_title( $title, $desc ) {
	echo $title;
}

/**
 * Do the banner title.
 *
 * @since   1.3.0
 *
 * @return  void
 */
add_action( 'mai_banner_title_description', 'mai_do_banner_description', 12, 2 );
function mai_do_banner_description( $title, $desc ) {
	echo $desc;
}

/**
 * Do the banner avatar on author archives when author box is enabled.
 *
 * @since   1.3.0
 *
 * @return  void
 */
add_action( 'mai_banner_content', 'mai_do_banner_avatar', 8 );
function mai_do_banner_avatar() {
	// Bail if not author archive.
	if ( ! is_author() ) {
		return;
	}
	// If author box is enabled.
	if ( ! get_the_author_meta( 'genesis_author_box_archive', get_query_var( 'author' ) ) ) {
		return;
	}
	global $authordata;
	$authordata    = is_object( $authordata ) ? $authordata : get_userdata( get_query_var( 'author' ) );
	$gravatar_size = apply_filters( 'genesis_author_box_gravatar_size', 70, 'archive' );
	echo get_avatar( get_the_author_meta( 'email' ), $gravatar_size );
}
