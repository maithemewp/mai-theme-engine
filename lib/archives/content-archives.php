<?php

// Output the static blog page content before the posts
add_action( 'genesis_before_loop', 'mai_do_blog_description', 20 );
function mai_do_blog_description() {

    // Bail if not the blog page
    if ( ! is_home() ) {
        return;
    }

    if ( $posts_page = get_option( 'page_for_posts' ) ) {
        // Echo the content
        echo apply_filters( 'the_content', get_post( $posts_page )->post_content );
    }
}


/**
 * Add term description before custom taxonomy loop.
 * This is the core WP term description, not the Genesis Intro Text.
 * Genesis Intro Text is in banner.
 */
add_action( 'genesis_before_loop', 'mai_do_term_description', 20 );
function mai_do_term_description() {
    // Bail if not a taxonomy archive
    if ( ! ( is_category() || is_tag() || is_tax() ) ) {
        return;
    }

    // If the first page
    if ( 0 === absint( get_query_var( 'paged' ) ) ) {
        $description = term_description();
        if ( $description ) {
            echo '<div class="term-description">' . do_shortcode($description) . '</div>';
        }
    }
}

add_action( 'genesis_before_loop', 'mai_maybe_remove_loop' );
function mai_maybe_remove_loop() {
    $remove_loop = mai_get_archive_meta_with_fallback( 'remove_loop' );
    if ( ! $remove_loop ) {
        return;
    }
    // Remove the loop
    remove_action( 'genesis_loop', 'genesis_do_loop' );
    remove_action( 'genesis_after_endwhile', 'genesis_posts_nav' );
    remove_action( 'genesis_after_loop', 'genesis_posts_nav' );
}

// Flex loop opening html
add_action( 'genesis_before_while', 'mai_archive_flex_loop_open', 100 );
function mai_archive_flex_loop_open() {
    // Bail if not a flex loop
    if ( ! mai_is_flex_loop() ) {
        return;
    }
    // Filter the post classes
    add_filter( 'post_class', 'mai_add_flex_entry_post_classes' );

    // Flex row wrap
    $attributes['class'] = 'row gutter-30';
    printf( '<div %s>', genesis_attr( 'flex-row', $attributes ) );
}

// Flex loop closing html
add_action( 'genesis_after_endwhile', 'mai_archive_flex_loop_close' );
function mai_archive_flex_loop_close() {
    // Bail if not a flex loop
    if ( ! mai_is_flex_loop() ) {
        return;
    }
    // Remove the post_class filter
    remove_filter( 'post_class', 'mai_add_flex_entry_post_classes' );

    // Close flex row wrap
    echo '</div>';
}

add_action( 'genesis_before_loop', 'mai_do_archive_options' );
function mai_do_archive_options() {

    $content_archive_thumbnail = mai_get_archive_meta_with_fallback( 'content_archive_thumbnail' );
    $image_size                = mai_get_archive_meta_with_fallback( 'image_size' );
    $content_archive           = mai_get_archive_meta_with_fallback( 'content_archive' );
    $content_archive_limit     = absint( mai_get_archive_meta_with_fallback( 'content_archive_limit' ) );

    add_filter( 'genesis_options', function( $options ) use ( $content_archive_thumbnail, $image_size, $content_archive, $content_archive_limit ) {
        $options['content_archive_thumbnail'] = $content_archive_thumbnail;
        $options['image_size']                = $image_size;
        $options['content_archive']           = $content_archive;
        $options['content_archive_limit']     = absint( $content_archive_limit );
        return $options;
    });

}

add_action( 'genesis_entry_content', 'mai_do_more_link' );
function mai_do_more_link() {
    $more_link = mai_get_archive_meta_with_fallback( 'more_link' );
    if ( ! $more_link ) {
        return;
    }
    echo mai_get_read_more_link( get_the_ID() );
}

/**
 * Hijack the thumbnail display genesis_option
 * maybe with our new archive settings, otherwise use default (theme settings).
 *
 * @param   bool  $display
 *
 * @return  bool  Whether to show the image.
 */
// add_filter( 'genesis_pre_get_option_content_archive_thumbnail', 'mai_archive_content_archive_thumbnail' );
function mai_archive_content_archive_thumbnail( $display ) {
    return mai_get_archive_meta_with_fallback( 'content_archive_thumbnail' );
}

/**
 * Get a custom image size for content archives.
 *
 * @return  string  The image size to use.
 */
// add_filter( 'genesis_pre_get_option_image_size', 'mai_archive_image_size' );
function mai_archive_image_size( $size ) {
    return mai_get_archive_meta_with_fallback( 'image_size' );
}

/**
 * Get content archive setting.
 *
 * @return  string  The type of content to display.
 */
// add_filter( 'genesis_pre_get_option_content_archive', 'mai_archive_content_archive' );
function mai_archive_content_archive( $archive ) {
    return mai_get_archive_meta_with_fallback( 'content_archive' );
}

/**
 * Get content archive setting.
 *
 * @return  string  The type of content to display.
 */
// add_filter( 'genesis_pre_get_option_content_archive_limit', 'mai_archive_content_archive_limit' );
function mai_archive_content_archive_limit( $limit ) {
    return absint( mai_get_archive_meta_with_fallback( 'content_archive_limit' ) );
}

/**
 * Add the archive featured image in the correct location.
 * No need to check if display image is checked, since that happens
 * in the genesis_option filters already.
 *
 * @return  void
 */
add_action( 'genesis_before_while', 'mai_do_post_image' );
function mai_do_post_image() {

    // Bail if not a content archive
    if ( ! mai_is_content_archive() ) {
        return;
    }

    // Remove the post image
    remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );

    $location = mai_get_archive_meta_with_fallback( 'image_location' );

    // Bail if no location
    if ( ! $location ) {
        return;
    }

    // Add the images in the correct location
    if ( 'before_entry' == $location ) {
        add_action( 'genesis_entry_header', 'genesis_do_post_image', 2 );
    } elseif ( 'before_title' == $location ) {
        add_action( 'genesis_entry_header', 'genesis_do_post_image', 8 );
    } elseif ( 'after_title' == $location ) {
        add_action( 'genesis_entry_header', 'genesis_do_post_image', 10 );
    } elseif ( 'before_content' == $location ) {
        add_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
    }

}

/**
 * Maybe remove the archive meta.
 *
 * @return  void
 */
add_action( 'genesis_before_while', 'mai_archive_remove_meta' );
function mai_archive_remove_meta() {

    $meta_to_remove = mai_get_archive_meta_with_fallback( 'remove_meta' );

    if ( $meta_to_remove ) {

        if ( in_array( 'post_info', $meta_to_remove ) ) {
            // Remove the entry meta in the entry header
            remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
        }

        // if ( isset( $meta_to_remove['post_meta'] ) ) {
        if ( in_array( 'post_meta', $meta_to_remove ) ) {
            // Remove the entry footer markup
            remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
            remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );
            // Remove the entry meta in the entry footer
            remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
        }

    }
}

/**
 * Change grid archive options
 * To override image size or content_archive (or others) in your template
 * use the 'genesis_options' filter with a priority over 10
 *
 * add_filter( 'genesis_options', function( $args ) {
 *     $args['content_archive']       = 'full';
 *     $args['content_archive_limit'] = '200';
 *     return $args;
 * });
 *
 * @param   array  $args  Full array of Genesis options
 *
 * @return  array  The altered array
 */
// add_filter( 'genesis_options', 'mai_flex_loop_genesis_options' );
function mai_flex_loop_genesis_options( $args ) {
    ddd( $args );
    $args['content_archive_thumbnail'] = true;
    $args['image_alignment']           = 'none';
    return $args;
}
