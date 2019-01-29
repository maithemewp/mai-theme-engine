<?php


/**
 * A big ol' helper/cleanup function to
 * enabled embeds inside the shortcodes and
 * keep the shorcodes from causing extra p's and br's.
 *
 * Most of the order comes from /wp-includes/default-filters.php
 *
 * @param   string|HTML  $content  The unprocessed content.
 *
 * @return  string|HTML  The processed content.
 */
function mai_get_processed_content( $content ) {
	global $wp_embed;
	$content = $wp_embed->autoembed( $content );              // WP runs priority 8.
	$content = $wp_embed->run_shortcode( $content );          // WP runs priority 8.
	$content = wptexturize( $content );                       // WP runs priority 10.
	$content = wpautop( $content );                           // WP runs priority 10.
	$content = mai_content_filter_shortcodes( $content );     // after wpautop, before shortcodes are parsed.
	$content = shortcode_unautop( $content );                 // WP runs priority 10.
	$content = wp_make_content_images_responsive( $content ); // WP runs priority 10.
	$content = do_shortcode( $content );                      // WP runs priority 11.
	$content = convert_smilies( $content );                   // WP runs priority 20.
	return $content;
}

/**
 * Filter the content to remove empty <p></p> tags and extray <br /> added by shortcodes.
 *
 * @link https://gist.github.com/bitfade/4555047
 *
 * @return  string|HTML  Fixed shortcode content.
 */
function mai_content_filter_shortcodes( $content ) {

	$shortcodes = array(
		'callout',
		'section',
		'columns',
		'col',
		'col_auto',
		'col_one_twelfth',
		'col_one_sixth',
		'col_one_fourth',
		'col_one_third',
		'col_five_twelfths',
		'col_one_half',
		'col_seven_twelfths',
		'col_two_thirds',
		'col_three_fourths',
		'col_five_sixths',
		'col_eleven_twelfths',
		'col_one_whole',
		'grid',
	);

	// Array of custom shortcodes requiring the fix.
	$shortcodes = join( '|', $shortcodes );

	// Cleanup.
	$content = strtr( $content, array ( '<p></p>' => '', '<p>[' => '[', ']</p>' => ']', ']<br />' => ']' ) );

	// Opening tag.
	$content = preg_replace( "/(<p>)?\[($shortcodes)(\s[^\]]+)?\](<\/p>|<br \/>)?/", "[$2$3]", $content );

	// Closing tag.
	$content = preg_replace( "/(<p>)?\[\/($shortcodes)](<\/p>|<br \/>)?/", "[/$2]", $content );

	// Return fixed shortcodes.
	return $content;

}

/**
 * Display the featured image.
 * Must be used in the loop.
 *
 * @param   string  $size  The image size to use.
 *
 * @return  void
 */
function mai_do_featured_image( $size = 'featured' ) {

	$image = genesis_get_image( array(
		'format' => 'html',
		'size'   => $size,
		'attr'   => array( 'class' => 'wp-post-image' )
	) );

	if ( ! $image ) {
		return;
	}

	// Get featured image ID.
	$image_id = get_post_thumbnail_id();

	// If we have a featured image (genesis_get_image() has fallbacks).
	if ( $image_id ) {
		// Add srcset.
		$image = wp_image_add_srcset_and_sizes( $image, wp_get_attachment_metadata( $image_id ), $image_id );
	}

	printf( '<div class="featured-image">%s</div>', $image );

	// Bail if no featured image.
	if ( ! $image_id ) {
		return;
	}

	// Get attachment.
	$attachment = get_post( $image_id );
	if ( ! $attachment ) {
		return;
	}

	// Get the excerpt.
	$caption = $attachment->post_excerpt;
	if ( $caption ) {
		printf( '<span class="image-caption">%s</span>', $caption );
	}
}

/**
 * Add the archive featured image in the correct location.
 * No need to check if display image is checked, since that happens
 * in the genesis_option filters already.
 *
 * @param   string  $location  The image location.
 *
 * @return  void
 */
function mai_do_archive_image( $location ) {

	// Bail if no location
	if ( ! $location ) {
		return;
	}

	/**
	 * Add the images in the correct location
	 */

	// Before Entry
	if ( 'before_entry' === $location ) {
		add_action( 'genesis_entry_header', 'genesis_do_post_image', 2 );
	}
	// Before Title
	elseif ( 'before_title' === $location ) {
		add_action( 'genesis_entry_header', 'genesis_do_post_image', 8 );
	}
	// After Title
	elseif ( 'after_title' === $location ) {
		add_action( 'genesis_entry_header', 'genesis_do_post_image', 10 );
	}
	// Before Content
	elseif ( 'before_content' === $location ) {
		add_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
	}
	// Background Image
	elseif ( 'background' === $location ) {
		// Add the entry image as a background image
		add_action( 'genesis_before_entry', 'mai_do_entry_image_background' );
		// Add the background image link
		add_action( 'genesis_entry_footer', 'mai_do_bg_image_link', 30 );
		// Remove bg iamge link function so additional loops are not affected
		add_action( 'mai_after_content_archive', function() {
			remove_action( 'genesis_entry_footer', 'mai_do_bg_image_link', 30 );
		});
	}

	// Add the location as a class to the image link
	add_filter( 'genesis_attr_entry-image-link', function( $attributes ) use ( $location ) {
		// Replace underscore with hyphen
		$location = str_replace( '_', '-', $location );
		// Add the class
		$attributes['class'] .= sprintf( ' entry-image-%s', $location );
		// Maybe add alignnone class.
		$img_alignment = mai_get_archive_setting( 'image_alignment', true, genesis_get_option( 'image_alignment' ) );
		if ( ! $img_alignment ) {
			$attributes['class'] .= ' alignnone';
		}
		return $attributes;
	});

}

/**
 * Add the entry image as a background image.
 * Change the markup to wrap the entire entry in an href link.
 * Remove the title link.
 *
 * @return void.
 */
function mai_do_entry_image_background() {

	// Get the image ID
	$image_id = get_post_thumbnail_id();

	// Get image size
	$image_size = mai_get_archive_setting( 'image_size', true, genesis_get_option( 'image_size' ) );

	// Anonomous attributes function
	$entry_attributes = function( $attributes ) use ( $image_id, $image_size ) {

		// Make element a link whether we have an image or not
		$attributes = mai_add_background_image_attributes( $attributes, $image_id, $image_size );
		$attributes['href'] = get_permalink();

		// If we have an image
		if ( $image_id ) {
			// Add classes and href link. TODO: Overlay options, or no overlay if no content?
			$attributes['class'] .= ' overlay overlay-dark light-content';
		}

		// Add has-bg-link class for CSS
		$attributes['class'] .= ' has-bg-link';

		// Center the content even if we don't have an image
		$attributes['class'] .= ' center-xs middle-xs text-xs-center';

		return $attributes;
	};

	// Add entry attributes
	add_filter( 'genesis_attr_entry', $entry_attributes );

	// Remove the filters so any other loops aren't affected
	add_action( 'genesis_after_entry', function() use ( $entry_attributes ) {
		remove_filter( 'genesis_attr_entry', $entry_attributes );
	});

}

/**
 * Output the bg image link HTML. Must be used in the loop (posts/cpts only!).
 *
 * This doesn't have a parameter because it's hooked directly,
 * via add_action( 'genesis_entry_header', 'mai_do_bg_image_link', 1 );
 *
 * @return void.
 */
function mai_do_bg_image_link() {
	echo mai_get_bg_image_link();
}

/**
 * Get the bg image link HTML.
 *
 * @param   string  $url (optional)    The URL to use for the HTML.
 * @param   string  $title (optional)  The title to use for the HTML.
 * @param   array   $attributes        Additional link attributes.
 *
 * @return  string|HTML
 */
function mai_get_bg_image_link( $url = '', $title = '', $attributes = array() ) {
	$title      = $title ? esc_html( $title ) : get_the_title();
	$attributes = wp_parse_args( $attributes, array(
		'class' => 'bg-link',
		'href'  => $url ? esc_url( $url ) : get_permalink(),
	) );
	return sprintf( '<div class="bg-link-wrap"><a %s><span class="screen-reader-text" aria-hidden="true">%s</span></a></div>', genesis_attr( 'bg-link', $attributes ), $title );
}

/**
 * Helper function to get a read more link for a post or term
 *
 * @param  int|WP_Post|WP_term?  $object      The object to get read more link for.
 * @param  string                $text        The "Read More" text.
 * @param  string                $type        The object type ('post' or 'term').
 * @param  array                 $attributes  Additional link attributes.
 *
 * @return HTML string for the link.
 */
function mai_get_read_more_link( $object_or_id = '', $text = '', $type = 'post', $attributes = array() ) {

	$link = $url = $screen_reader_html = $screen_reader_text = '';

	$text           = $text ? sanitize_text_field( $text ) : __( 'Read More', 'mai-theme-engine' );
	$more_link_text = sanitize_text_field( apply_filters( 'mai_more_link_text', $text, $object_or_id, $type ) );

	switch ( $type ) {
		case 'post':
			$url                = get_permalink( $object_or_id );
			$screen_reader_text = get_the_title( $object_or_id );
		break;
		case 'term':
			$term               = is_object( $object_or_id ) ? $object_or_id : get_term( $object_or_id );
			$url                = get_term_link( $term );
			$screen_reader_text = $term->name;
		break;
	}

	// Build the screen reader text html
	if ( $screen_reader_text ) {
		$screen_reader_html = sprintf( '<span class="screen-reader-text">%s</span>', esc_html( $screen_reader_text ) );
	}

	// If we have a url
	if ( $url ) {
		$attributes = wp_parse_args( $attributes, array(
			'class' => 'more-link',
			'href'  => esc_url( $url ),
		) );
		$link = sprintf( '<a %s>%s%s</a>', genesis_attr( 'more-link', $attributes ), $screen_reader_html, $more_link_text );
	}

	// Bail if no link
	if ( empty( $link ) ) {
		return;
	}

	return sprintf( '<p class="more-link-wrap">%s</p>', $link );
}

/**
 * Get a post's post_meta.
 *
 * @param  int|object   $post  (Optional) the post to get the meta for.
 *
 * @return string|HTML  The post meta.
 */
function mai_get_the_posts_meta( $post = '' ) {

	if ( ! empty( $post ) ) {
		$post = get_post( $post );
	} else {
		global $post;
	}

	$post_meta = $shortcodes = '';

	$taxos = get_post_taxonomies($post);
	if ( $taxos ) {

		// Skip if Post Formats and Yoast prominent keyworks
		$taxos = array_diff( $taxos, array( 'post_format', 'yst_prominent_words' ) );

		$taxos = apply_filters( 'mai_post_meta_taxos', $taxos );

		foreach ( $taxos as $tax ) {
			$taxonomy = get_taxonomy($tax);
			$shortcodes .= '[post_terms taxonomy="' . $tax . '" before="' . $taxonomy->labels->singular_name . ': "]';
		}
		$post_meta = sprintf( '<p class="entry-meta">%s</p>', do_shortcode( $shortcodes ) );
	}
	return $post_meta;
}

/**
 * Helper function to get a grid of content.
 * This is a php version of the [grid] shortcode.
 *
 * @param   array  $args  The [grid] shortcode atts.
 *
 * @return  string|HTML
 */
function mai_get_grid( $args ) {
	$section = new Mai_Grid( $args );
	return $section->render();
}

/**
 * Get a section.
 *
 * @param  array  $content  The section content (required).
 * @param  array  $args     The section args (optional).
 *
 * @return string|HTML
 */
function mai_get_section( $content, $args = array() ) {
	$section = new Mai_Section( $args, $content );
	return $section->render();
}

/**
 * Get the sections HTML.
 * Requires $sections to be valid section meta.
 *
 * @access  private
 * @since   1.3.0
 * @since   1.7.0  Added 'mai_valid_section_args' filter.
 * @param   array  $sections  The 'mai_sections' meta data.
 *
 * @return  string|HTML  The sections HTML
 */
function mai_get_sections( $sections ) {

	$html       = '';
	$has_banner = mai_is_banner_area_enabled();
	$has_h1     = false;

	$valid = array(
		'align',
		'align_content',
		'bg',
		'class',
		'content_width',
		'text_size',
		'height',
		'id',
		'inner',
		'overlay',
		'title',
	);

	/**
	 * Filter to allow new settings to get passed to mai_get_section() function.
	 *
	 * @since  1.7.0.
	 */
	$valid = apply_filters( 'mai_valid_section_args', $valid );

	// Loop through each section.
	foreach ( $sections as $section ) {

		// Reset args.
		$args = array();

		// Set the args.
		foreach ( $valid as $setting ) {
			$args[ $setting ] = isset( $section[ $setting ] ) ? $section[ $setting ] : '';
		}

		// Set the bg image.
		$args['image'] = isset( $section['image_id'] ) ? $section['image_id'] : '';

		// Set the content.
		$content = isset( $section['content'] ) ? trim( $section['content'] ) : '';

		// Skip if no title and no content and no image.
		if ( empty( $args['title'] ) && empty( $args['image'] ) && empty( $content ) ) {
			continue;
		}

		// If no banner area.
		if ( ! $has_banner ) {

			/**
			 * Check content first, so there is more manual control over h1.
			 * If no h1 yet, and we have content, check it for an h1.
			 */
			if ( ! $has_h1 && ! empty( $content ) ) {
				if ( false  !== strpos ( $content, '<h1' ) ) {
					$has_h1 = true;
				}
			}

			/**
			 * If no h1 yet, and we have a section title, make it an h1.
			 */
			if ( ! $has_h1 && ! empty( $section['title'] ) ) {
				$args['title_wrap'] = 'h1';
				$has_h1             = true;
			}
		}

		$html .= mai_get_section( $content, $args );
	}

	return $html;
}

/**
 * Get the sitemap content
 * A lot of code taken from genesis_get_sitemap() function.
 *
 * @since   1.6.1
 * @access  private
 *
 * @return  string|HTML
 */
function mai_get_sitemap() {

	$sitemap = '';

	// Get public post types.
	$post_types = get_post_types( array(
		'public' => true,
	), 'objects' );

	// Filter for devs to add or remove specific post types.
	$post_types = apply_filters( 'mai_sitemap_post_types', $post_types );

	// Bail if no post types. Unlikely.
	if ( ! $post_types ) {
		return $sitemap;
	}

	$number  = 100;
	$heading = 'h2';

	// Loop through the posts.
	foreach ( $post_types as $post_type ) {

		$list = wp_get_archives( array(
			'post_type' => $post_type->name,
			'type'      => 'postbypost',
			'limit'     => $number,
			'echo'      => false,
		) );

		// Skip if no posts.
		if ( ! $list ) {
			continue;
		}

		// Add the posts to the sitemap variable.
		$sitemap .= sprintf( '<%2$s>%1$s</%2$s>', $post_type->label, $heading );
		$sitemap .= sprintf( '<ul>%s</ul>', $list );
	}

	$post_counts = wp_count_posts();
	if ( $post_counts->publish > 0 ) {
		$sitemap .= sprintf( '<%2$s>%1$s</%2$s>', __( 'Categories:', 'mai-theme-engine' ), $heading );
		$sitemap .= sprintf( '<ul>%s</ul>', wp_list_categories( array(
			'number'      => $number,
			'sort_column' => 'name',
			'title_li'    => '',
			'echo'        => false,
		) ) );
		$sitemap .= sprintf( '<%2$s>%1$s</%2$s>', __( 'Authors:', 'mai-theme-engine' ), $heading );
		$sitemap .= sprintf( '<ul>%s</ul>', wp_list_authors( array(
			'number'        => $number,
			'exclude_admin' => false,
			'optioncount'   => true,
			'echo'          => false,
		) ) );
		$sitemap .= sprintf( '<%2$s>%1$s</%2$s>', __( 'Monthly:', 'mai-theme-engine' ), $heading );
		$sitemap .= sprintf( '<ul>%s</ul>', wp_get_archives( array(
			'number' => $number,
			'type'   => 'monthly',
			'echo'   => false,
		) ) );
	}

	return $sitemap;
}
