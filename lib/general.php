<?php

/**
 * Add body class to enabled specific settings.
 *
 * @param   array  $classes  The body classes.
 *
 * @return  array  $classes  The modified classes.
 */
add_filter( 'body_class', 'mai_do_settings_body_classes' );
function mai_do_settings_body_classes( $classes ) {
	/**
	 * Add sticky header styling
	 */
	if ( mai_is_sticky_header_enabled() && ! is_page_template( 'landing.php' ) ) {
		$classes[] = 'sticky-header';
	}

	/**
	 * Add shrink header styling
	 */
	if ( mai_is_shrink_header_enabled() && ! is_page_template( 'landing.php' ) ) {
		$classes[] = 'shrink-header';
	}

	/**
	 * Use a side mobile menu in place of the standard the mobile menu
	 */
	if ( mai_is_side_menu_enabled() ) {
		$classes[] = 'side-menu';
	}

	return $classes;
}

/**
 * Add singular body class to the head.
 *
 * @param   array  $classes  The body classes.
 *
 * @return  array  $classes  The modified classes.
 */
add_filter( 'body_class', 'mai_singular_body_class' );
function mai_singular_body_class( $classes ) {
	if ( ! is_singular() ) {
		return $classes;
	}
	$classes[] = 'singular';
	return $classes;
}

/**
 * Add no-js body class to the head.
 * This get's changed to "js" if JS is enabled.
 *
 * @link https://www.paulirish.com/2009/avoiding-the-fouc-v3/
 *
 * @param   array  $classes  The body classes.
 *
 * @return  array  $classes  The modified classes.
 */
add_filter( 'body_class', 'mai_js_detection_body_class' );
function mai_js_detection_body_class($classes) {
	$classes[] = 'no-js';
	return $classes;
}

/**
 * Remove the .no-js class from the html element via JS.
 * This allows styles targetting browsers without JS.
 *
 * Props Gary Jones for the initial push to start doing this
 * Props Sal Ferrarello for introducing me to this solution
 *
 * @link https://github.com/GaryJones/genesis-js-no-js/
 * @link https://www.paulirish.com/2009/avoiding-the-fouc-v3/
 */
add_action( 'genesis_before', 'mai_js_detection_script', 1 );
function mai_js_detection_script() {
	?>
	<script>
		//<![CDATA[
		(function(){
			var c = document.body.className;
			c = c.replace(/no-js/, 'js');
			document.body.className = c;
		})();
		//]]>
	</script>
	<?php
}


add_action( 'genesis_before', 'mai_do_boxed_elements' );
function mai_do_boxed_elements() {

	$elements = genesis_get_option( 'boxed_elements' );

	if ( ! $elements ) {
		return;
	}

	$atts = array(
		'entry'               => 'entry',
		'sidebar'             => 'sidebar-primary',
		// 'sidebar_widgets'     => '',
		'sidebar_alt'         => 'sidebar-secondary',
		// 'sidebar_alt_widgets' => '',
		// 'after_entry_widgets' => '',
		'author_box'          => 'author-box',
		'comments'            => 'entry-comments',
		// 'comment_respond'     => '',
		'pings'               => '',
	);

	foreach ( (array) $elements as $element ) {

		if ( isset( $atts[ $element ] ) ) {

			add_filter( "genesis_attr_{$atts[ $element ]}", function( $attributes ) {
				$attributes['class'] .= ' boxed';
				return $attributes;
			});

			// TODO: Move this setting/check into [grid] and add a parameter?
			if ( 'entry' === $atts[ $element ] ) {
				add_filter( 'genesis_attr_flex-entry', function( $attributes ) {
					$attributes['class'] .= ' boxed';
					return $attributes;
				});
			}

		}

	}

	// $open = apply_filters( "genesis_markup_{$args['context']}_open", $open, $args );



}


add_filter( 'genesis_register_widget_area_defaults', 'mai_boxed_widgets', 10, 2 );
function mai_boxed_widgets( $defaults, $args ) {


	$elements = genesis_get_option( 'boxed_elements' );
	// d( $elements );

	// Primary Sidebar.
	if ( in_array( 'sidebar_widgets', $elements ) && 'sidebar' === $args['id'] ) {
		$defaults['before_widget'] = str_replace( 'class="widget ', 'class="widget boxed ', $defaults['before_widget'] );
	}

	// Secondary Sidebar.
	if ( in_array( 'sidebar_alt_widgets', $elements ) && 'sidebar-alt' === $args['id'] ) {
		$defaults['before_widget'] = str_replace( 'class="widget ', 'class="widget boxed ', $defaults['before_widget'] );
	}

	if ( in_array( 'after_entry_widgets', $elements ) && 'after-entry' === $args['id'] ) {
		$defaults['before_widget'] = str_replace( 'class="widget ', 'class="widget boxed ', $defaults['before_widget'] );
	}

	return $defaults;
}

add_action( 'comment_form_before', 'mai_boxed_comment_form', 99 );
function mai_boxed_comment_form() {

	$elements = genesis_get_option( 'boxed_elements' );

	if ( ! in_array( 'comment_respond', $elements ) ) {
		return;
	}

	echo '<div class="comment-respond-wrap boxed">';

	add_action( 'comment_form_after', function() {
		echo '</div>';
	}, 0 );
}

// add_filter( 'genesis_comment_form_args', function( $args, $user_identity, $post_id, $commenter, $req, $aria_req ) {
// 	d( $args );
// 	return $args;
// }, 99, 6 );

// add_filter( 'comment_form_defaults', function( $defaults ) {
// 	$defaults['class_form'] .= ' boxed';
// 	return $defaults;
// });
