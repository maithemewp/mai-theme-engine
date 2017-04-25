<?php

/**
 * This file adds Flexington support for Bill Erickson's "Display Posts Shortcode" plugin
 *
 * @version 1.0.3
 *
 * @link https://wordpress.org/plugins/display-posts-shortcode/
 * @link https://github.com/billerickson/display-posts-shortcode/
 */

/**
 * Allow use of the [display-posts] shortcode without Display Posts Shortcode being active
 * Uses the plugin if it's active, otherwise uses the local copy
 *
 * Hooks in really late to make sure the plugin has a chance to register the shortcode first
 */
// add_action( 'genesis_before', 'mai_display_posts_shortcode' );
function mai_display_posts_shortcode() {
    if ( ! shortcode_exists( 'display-posts' ) ) {
		require_once( get_stylesheet_directory() . '/lib/vendor/display-posts-shortcode.php' );
    }
}

// Run all the filters to integrate Flexington/Slick/etc
add_filter( 'shortcode_atts_display-posts', 'mai_dps_defaults', 10, 3 );
add_filter( 'display_posts_shortcode_wrapper_open', 'mai_dps_wrapper_open', 10, 2 );
add_filter( 'display_posts_shortcode_wrapper_close', 'mai_dps_wrapper_close', 10, 2 );
add_filter( 'display_posts_shortcode_post_class', 'mai_dps_post_classes', 10, 4 );
add_filter( 'display_posts_shortcode_output', 'mai_dps_output', 10, 9 );

/**
 * Set Defaults in Display Posts Shortcode
 *
 * @param   array  $out    the output array of shortcode attributes (after user-defined and defaults have been combined)
 * @param 	array  $pairs  the supported attributes and their defaults
 * @param 	array  $atts   the user defined shortcode attributes
 *
 * @return 	array  $out    modified output
 */
function mai_dps_defaults( $out, $pairs, $atts ) {

	/**
	 * Set new defaults for the shortcode
	 * Some unused, but not sure if we should unset at some point
	 */
	$new_defaults = array(
		'gutter'		 => 30, // 5, 10, 20, 30, 40, 50, or 60
		'image_size' 	 => 'one-third',
		'posts_per_page' => 12,
		'date_format' 	 => get_option( 'date_format' ), // Default no longer used, but can override core Genesis
		'content_class'  => 'content-inner', 			 // No longer used
		'wrapper' 		 => 'div', 						 // No longer used since we force div for columns
	);

	foreach( $new_defaults as $name => $default ) {
		if ( array_key_exists( $name, $atts ) ) {
			$out[$name] = $atts[$name];
		} else {
			$out[$name] = $default;
		}
	}

	return $out;
}

// Add Flexington and Slick.js support to the main wrap
function mai_dps_wrapper_open( $output, $shortcode_atts ) {

	$default_atts = array(
		'center' 		 => false, // Center horizontally
		'center_content' => false, // Center horizontally
		'columns'		 => 3, 	   // Used for Slick slidestoshow also
		'gutter' 		 => 30,
		'middle'		 => false, // Center vertically
		'wrapper_class'  => false,
		'wrapper_id'	 => false,
		'slider'		 => false, // Turn posts into a slider
		'arrows' 		 => true,  // (slider only) Whether to display arrows
		'center_mode' 	 => false, // (slider only) Mobile 'peek'
		'dots' 		 	 => false, // (slider only) Whether to display dots
		'fade' 		 	 => false, // (slider only) Fade instead of left/right scroll (works requires slidestoshow 1)
		'infinite' 		 => true,  // (slider only) Loop slider
		'slidestoscroll' => 1, 	   // (slider only) The amount of posts to scroll
	);
	$shortcode_atts = shortcode_atts( $default_atts, $shortcode_atts );

	/**
	 * Add filter so developers can change these shortcode defaults sitewide
	 * May still be more useful to use 'shortcode_atts_display-posts' filter for default DPS atts?
	 */
	$shortcode_atts = apply_filters( 'mai_dps_defaults', $shortcode_atts );

	/**
	 * Sanitize the attributes back into an array
	 * so we can pass them to multiple shortcodes
	 */
	$sanitized_atts = array(
		'center'			=> filter_var( $shortcode_atts['center'], FILTER_VALIDATE_BOOLEAN ),
		'columns'			=> absint( $shortcode_atts['columns'] ),
		'gutter'			=> absint( $shortcode_atts['gutter'] ),
		'middle'			=> filter_var( $shortcode_atts['middle'], FILTER_VALIDATE_BOOLEAN ),
		'wrapper_class'		=> implode( ' ', array_map( 'sanitize_html_class', ( explode( ' ', $shortcode_atts['wrapper_class'] ) ) ) ),
		'wrapper_id'		=> sanitize_html_class( $shortcode_atts['wrapper_id'] ),
		'slider'			=> filter_var( $shortcode_atts['slider'], FILTER_VALIDATE_BOOLEAN ),
		'arrows'			=> filter_var( $shortcode_atts['arrows'], FILTER_VALIDATE_BOOLEAN ),
		'center_mode'		=> filter_var( $shortcode_atts['center_mode'], FILTER_VALIDATE_BOOLEAN ),
		'dots'				=> filter_var( $shortcode_atts['dots'], FILTER_VALIDATE_BOOLEAN ),
		'fade'				=> filter_var( $shortcode_atts['fade'], FILTER_VALIDATE_BOOLEAN ),
		'infinite'			=> filter_var( $shortcode_atts['infinite'], FILTER_VALIDATE_BOOLEAN ),
		'slidestoscroll'	=> absint( $shortcode_atts['slidestoscroll'] ),
	);

	/**
	 * Main posts wrapper
	 */
	$mai_posts			= array();
	$mai_posts['id']	= $sanitized_atts['wrapper_id'] ? $sanitized_atts['wrapper_id'] : '';
	$mai_posts['class']	= $sanitized_atts['wrapper_class'] ? 'mai-posts ' . $sanitized_atts['wrapper_class'] : 'mai-posts';

	/**
	 * The flex row wrapper
	 */
    $flex_row = array();

	$flex_row['class']	= 'flex-row row';
	$gutters			= array( 5, 10, 20, 30, 40, 50, 60 );
	$is_valid_gutter	= in_array( $sanitized_atts['gutter'], $gutters );

    // If posts are a slider
	if ( $sanitized_atts['slider'] ) {

		// Enqueue Slick Carousel
		wp_enqueue_script( 'mai-slick' );
		wp_enqueue_script( 'mai-slick-init' );

		// Slider wrapper class
		$flex_row['class'] .= ' mai-slider';

		// Slider HTML data attributes
		$flex_row['data-arrows']		 = $sanitized_atts['arrows'] ? 'true' : 'false';
		$flex_row['data-center']	 	 = $sanitized_atts['center'] ? 'true' : 'false';
		$flex_row['data-centermode']	 = $sanitized_atts['center_mode'] ? 'true' : 'false';
		$flex_row['data-dots']			 = $sanitized_atts['dots'] ? 'true' : 'false';
		$flex_row['data-fade']			 = $sanitized_atts['fade'] ? 'true' : 'false';
		$flex_row['data-infinite']		 = $sanitized_atts['infinite'] ? 'true' : 'false';
		$flex_row['data-middle']	 	 = $sanitized_atts['middle'] ? 'true' : 'false';
		$flex_row['data-slidestoscroll'] = $sanitized_atts['slidestoscroll'];
		$flex_row['data-slidestoshow']	 = $sanitized_atts['columns'];
		$flex_row['data-gutter']		 = $is_valid_gutter ? $sanitized_atts['gutter'] : 0;

	}
	// Flex row classes are not on slider
	else {

		// Add gutter
    	if ( $is_valid_gutter ) {
	        $flex_row['class'] .= ' gutter-' . $sanitized_atts['gutter'];
	    }

		// Center horizontally
		if ( $sanitized_atts['center'] ) {
			$flex_row['class'] .= ' center-xs';
		}

		// Center vertically
		if ( $sanitized_atts['middle'] ) {
			$flex_row['class'] .= ' middle-xs';
		}

	}

	/**
	 * Build it all
	 *
	 * No need for an additional filter when you can just use:
	 *
	 * add_filter( 'genesis_attr_mai-posts', 'prefix_filter_mai_posts' );
	 * function prefix_filter_mai_posts( $mai_posts ) {
	 * 		// Do your thing here
	 * 		return $mai_posts
	 * }
	 *
	 * But why not just edit the shortcode parameters?
	 *
	 */
	$output = sprintf( '<div %s><div %s>', genesis_attr( 'mai-posts', $mai_posts ), genesis_attr( 'flex-row', $flex_row ) );

	return $output;

}

// Close the open containers
function mai_dps_wrapper_close( $output, $atts ) {

	$output = '</div></div>';
	return $output;

}

/**
 * If posts are a slider, setup the classes
 *
 * Add Flexington support to the individual entries
 * Allows a new shortcode parameter of 'columns' to set the amount of columns per row
 *
 * @param 	array    $class          Post classes.
 * @param 	WP_Post  $post           Post object.
 * @param 	WP_Query $listing        WP_Query object for the posts listing.
 * @param 	array    $original_atts  Original attributes passed to the shortcode.
 *
 * @return  array  	 The array of post classes
 */
function mai_dps_post_classes( $classes, $post, $listing, $original_atts ) {

	$classes		 = array();
	$default_classes = array( 'entry', 'flex-entry' );

	if ( isset( $original_atts['center_content'] ) && filter_var( $original_atts['center_content'], FILTER_VALIDATE_BOOLEAN ) ) {
		// Add Flexington center class
		$default_classes[] .= ' text-xs-center';
	}

	if ( ! ( isset( $original_atts['slider'] ) && filter_var( $original_atts['slider'], FILTER_VALIDATE_BOOLEAN ) ) ) {

		// Get column count, default to 3
		$columns = isset( $original_atts['columns'] ) ? intval( $original_atts['columns'] ) : 3;

		$classes = mai_get_flex_entry_classes_by_columns( $columns );

		$classes = explode( ' ', $classes );

	}

    /**
     * Return our new classes with the default WP generated classes
     * Also removes potential duplicate flex-entry since we need it in both $default_classes and $classes
     */
	$classes = get_post_class( array_unique( array_merge( $default_classes, $classes ) ), $post->ID );
	return $classes;
}

/**
 * Add the correct markup to easily inherit flex-entry styling
 *
 * @param  string  $output         The shortcode's HTML output.
 * @param  array   $original_atts  Original attributes passed to the shortcode.
 * @param  string  $image          HTML markup for the post's featured image element.
 * @param  string  $title          HTML markup for the post's title element.
 * @param  string  $date           HTML markup for the post's date element.
 * @param  string  $excerpt        HTML markup for the post's excerpt element.
 * @param  string  $inner_wrapper  Type of container to use for the post's inner wrapper element.
 * @param  string  $content        The post's content.
 * @param  string  $class          Space-separated list of post classes to supply to the $inner_wrapper element.
 */
function mai_dps_output( $output, $original_atts, $image, $title, $date, $excerpt, $inner_wrapper, $content, $class ) {

	$entry_header = $entry_meta = $entry_content = $entry_footer = '';

	// Handle new shortcode parameters
	$new_defaults = array(
		'title_wrap' => 'h2', // The wrapping element for individual post titles
	);
	$new_atts = wp_parse_args( $original_atts, $new_defaults );

	// Swap image class for entry-image-link for entry consistency
	$image = str_replace( 'class="image"', 'class="entry-image-link"', $image );

	$date = '';
	if ( isset( $original_atts['include_date'] ) && filter_var( $original_atts['include_date'], FILTER_VALIDATE_BOOLEAN ) ) {

		/**
		 * Introduce new 'date_before' and 'date_after' params to be used in G post_date shortcode
		 *
		 * If date formate is set in shortcode, use that format instead of default Genesis
		 * Since using G post_date shortcode you can also use 'relative' for '3 days ago'
		 */

		$date_before	= isset( $original_atts['date_before'] ) ? ' before="' . $original_atts['date_before'] . '"' : '';
		$date_after		= isset( $original_atts['date_after'] ) ? ' after="' . $original_atts['date_after'] . '"' : '';
		$date_format	= isset( $original_atts['date_format'] ) ? ' format="' . $original_atts['date_format'] . '"' : '';
		$date_shortcode	= sprintf( '[post_date%s%s%s]', $date_before, $date_after, $date_format );
		// Use Genesis output for post date
		$date = do_shortcode( $date_shortcode );

	}

	$author	= '';
	if ( isset( $original_atts['include_author'] ) && filter_var( $original_atts['include_author'], FILTER_VALIDATE_BOOLEAN ) ) {

		/**
		 * Introduce new 'author_before' and 'author_after' params to be used in G post_author_link shortcode
		 * If author has no link this shortcode defaults to genesis_post_author_shortcode() [post_author]
		 */

		$author_before		= isset( $original_atts['author_before'] ) ? ' before="' . $original_atts['author_before'] . '"' : '';
		$author_after		= isset( $original_atts['author_after'] ) ? ' after="' . $original_atts['author_after'] . '"' : '';
		$author_shortcode	= sprintf( '[post_author_link%s%s]', $author_before, $author_after );
		// Use Genesis output for author, including link
		$author = do_shortcode( $author_shortcode );

	}

	// Build entry meta
	if ( $date || $author ) {

		$entry_meta .= sprintf( '<p %s>', genesis_attr( 'entry-meta-before-content' ) );
		$entry_meta .= $date . $author;
		$entry_meta .= '</p>';

	}

	// Build entry header
	if ( $title || $entry_meta ) {

		$entry_header .= sprintf( '<header %s>', genesis_attr( 'entry-header' ) );

		if ( $title ) {
			$entry_header .= sprintf( '<%s class="entry-title" itemprop="headline">%s</%s>', $new_atts['title_wrap'], $title, $new_atts['title_wrap'] );
		}
		if ( $entry_meta ) {
			$entry_header .= $entry_meta;
		}

		$entry_header .= '</header>';
	}


	if ( $excerpt ) {
		// Override DPS default, let's only do excerpt
		$entry_content .= wpautop( get_the_excerpt() );
	}

	if ( $content ) {
		add_filter( 'shortcode_atts_display-posts', 'be_display_posts_off', 10, 3 );
		$entry_content .= apply_filters( 'the_content', get_the_content() );
		remove_filter( 'shortcode_atts_display-posts', 'be_display_posts_off', 10, 3 );
	}

	$more_link = isset( $new_atts['excerpt_more_link'] ) && filter_var( $new_atts['excerpt_more_link'], FILTER_VALIDATE_BOOLEAN );

	// Add the Mai Theme read more link, for styling consistency
	if ( $more_link ) {
		// Custom read more link text
		$more = isset( $new_atts['excerpt_more'] ) ? sanitize_text_field( $new_atts['excerpt_more'] ) : '';
		// Add read more link
		$entry_content = $entry_content . mai_get_read_more_link( get_the_ID(), $more );
	}

	// Add entry content wrap if displaying excerpt, or content
	if ( $excerpt || $content || $more_link ) {
		$entry_content = sprintf( '<div %s>%s</div>', genesis_attr( 'entry-content' ), $entry_content );
	}

	// Rebuild the output
	$output = '<' . $inner_wrapper . ' class="' . implode( ' ', $class ) . '">' . $image . $entry_header . $entry_content . '</' . $inner_wrapper . '>';

	// Finally we'll return the modified output
	return $output;

}
