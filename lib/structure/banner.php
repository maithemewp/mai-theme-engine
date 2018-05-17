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
		'styles'        => '',
		'text_size'     => 'lg',
	);

	// Get the image ID.
	$image_id = mai_get_banner_id();

	// Maybe add image background.
	if ( $image_id ) {
		$args['image'] = $image_id;
	}

	// Get the alignment setting.
	$align_text = genesis_get_option( 'banner_align_text' );

	// Maybe add the align_text class.
	if ( $align_text ) {
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
 * @return  void
 */
add_action( 'mai_banner_content', 'mai_do_banner_content' );
function mai_do_banner_content() {

	global $wp_query, $post;

	$wrap         = 'h1';
	$before_title = $title = $desc = '';

	// If front page displays your latest posts.
	if ( is_front_page() && is_home() ) {
		$title = __( 'Blog', 'genesis' );
		$desc  = has_excerpt( $front_page_id ) ? get_the_excerpt( $front_page_id ) : '';
	}

	// Add static front page banner content
	elseif ( is_front_page() && $front_page_id = get_option( 'page_on_front' ) ) {

		// Remove post title
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
		remove_action( 'genesis_entry_header', 'genesis_do_post_title' );

		$title = get_the_title();
		$desc  = has_excerpt( $front_page_id ) ? get_the_excerpt( $front_page_id ) : '';
	}

	// Do static blog banner content.
	elseif ( is_home() && $posts_page_id = get_option( 'page_for_posts' ) ) {
		$title = get_the_title( $posts_page_id );
		$desc  = has_excerpt( $posts_page_id ) ? get_the_excerpt( $posts_page_id ) : '';
	}

	// Do singular banner content.
	elseif ( is_singular() && ! is_front_page() && ! is_home() ) {

		// Remove default post title.
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
		remove_action( 'genesis_entry_header', 'genesis_do_post_title' );

		$title = get_the_title();
		$desc  = has_excerpt( get_the_ID() ) ? get_the_excerpt( get_the_ID() ) : '';
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

	// Do author archive banner content.
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

	elseif ( is_search() ) {
		$title = apply_filters( 'genesis_search_title_text', __( 'Search Results for:', 'genesis' ) );
	}

	elseif ( is_404() ) {
		$title = apply_filters( 'genesis_404_entry_title', __( 'Not found, error 404', 'genesis' ) );
	}

	// If WooCommerce is not active.
	elseif ( class_exists( 'WooCommerce' ) ) {

		// Shop page.
		if ( is_shop() && ( $shop_page_id = get_option( 'woocommerce_shop_page_id' ) ) ) {
			$title = get_the_title( $shop_page_id );
			$title = $title ? strip_tags( $title ) : '';
			$desc  = has_excerpt( $shop_page_id ) ? get_the_excerpt( $shop_page_id ) : '';
		}
		// Singular product.
		elseif ( is_product() ) {
			/**
			 * We already have the title set from is_singular().
			 *
			 * Use an h2 on front page, since the product title will be h1.
			 * We have to do this up top because is_singular() will output product title.
			 */
			$wrap = 'h2';
		}

	}

	// Banner content filters.
	$wrap  = apply_filters( 'mai_banner_title_wrap', $wrap );
	$title = apply_filters( 'mai_banner_title_text', $title );
	$title = $title ? sprintf( '<%s %s>%s</%s>', $wrap, genesis_attr( 'banner-title' ), $title, $wrap ) : '';
	$title = apply_filters( 'mai_banner_title', $title );
	$desc  = apply_filters( 'mai_banner_description', $desc );

	do_action( 'mai_banner_content_title', $title );
	do_action( 'mai_banner_content_description', $desc );

	// Add back the entry header/title because custom queries and loops may need it.
	add_action( 'genesis_before_entry_content', function() {
		add_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
		add_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
		add_action( 'genesis_entry_header', 'genesis_do_post_title' );
	});
}

add_action( 'mai_banner_content_title', 'mai_do_banner_title' );
function mai_do_banner_title( $title ) {
	echo $title;
}

add_action( 'mai_banner_content_description', 'mai_do_banner_description' );
function mai_do_banner_description( $desc ) {
	echo $desc;
}

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
