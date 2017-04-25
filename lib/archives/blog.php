<?php

// Output the static blog page content before the posts
add_action( 'genesis_before_loop', 'mai_do_blog_description', 20 );
function mai_do_blog_description() {

	// Bail if not the blog page
	if ( ! is_home() ) {
		return;
	}

	$posts_page = get_option( 'page_for_posts' );
	if ( is_null( $posts_page ) ) {
		return;
	}

	// Echo the content
	echo apply_filters( 'the_content', get_post( $posts_page )->post_content );
}
