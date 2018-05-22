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

	$boxed = mai_get_boxed_elements_array();

	foreach ( (array) $elements as $element ) {

		if ( isset( $boxed[ $element ] ) ) {

			$name = $boxed[ $element ];

			add_filter( "genesis_attr_{$boxed[ $element ]}", function( $attributes ) use ( $name ) {
				// If boxing entries, make sure it's only the main entries.
				if ( ( 'entry' !== $name ) && ! is_main_query() ) {
					return $attributes;
				}
				$attributes['class'] .= ' boxed';
				return $attributes;
			});

			// TODO: Move this setting/check into [grid] and add a parameter?
			// if ( 'entry' === $boxed[ $element ] ) {
			// 	add_filter( 'genesis_attr_flex-entry', function( $attributes ) {
			// 		$attributes['class'] .= ' boxed';
			// 		return $attributes;
			// 	});
			// }

		}

	}

	// $open = apply_filters( "genesis_markup_{$args['context']}_open", $open, $args );

}

/**
 * Adds boxed class to the adjacent post link.
 *
 * The dynamic portion of the hook name, `$adjacent`, refers to the type
 * of adjacency, 'next' or 'previous'.
 *
 * @since 1.3.0
 *
 * @param  string   $output    The adjacent post link.
 * @param  string   $format    Link anchor format.
 * @param  string   $link      Link permalink format.
 * @param  WP_Post  $post      The adjacent post.
 * @param  string   $adjacent  Whether the post is previous or next.
 */
add_filter( 'previous_post_link', 'mai_boxed_adjacent_entry_nav', 10, 5 );
add_filter( 'next_post_link', 'mai_boxed_adjacent_entry_nav', 10, 5 );
function mai_boxed_adjacent_entry_nav( $output, $format, $link, $post, $adjacent ) {
	$elements = genesis_get_option( 'boxed_elements' );
	if ( ! in_array( 'adjacent_entry_nav', (array) $elements ) ) {
		return $output;
	}
	$output = str_replace( '<a href', '<a class="boxed" href', $output );
	return $output;
}


// add_filter( 'genesis_register_widget_area_defaults', 'mai_boxed_widgets', 10, 2 );
function mai_boxed_widgets( $defaults, $args ) {

	$elements = genesis_get_option( 'boxed_elements' );
	// d( $elements );

	// Primary Sidebar Widgets.
	if ( in_array( 'sidebar_widgets', (array) $elements ) && 'sidebar' === $args['id'] ) {
		$defaults['before_widget'] = str_replace( 'class="widget ', 'class="widget boxed ', $defaults['before_widget'] );
	}
	// Secondary Sidebar Widgets.
	elseif ( in_array( 'sidebar_alt_widgets', (array) $elements ) && 'sidebar-alt' === $args['id'] ) {
		$defaults['before_widget'] = str_replace( 'class="widget ', 'class="widget boxed ', $defaults['before_widget'] );
	}
	// After Entry Widgets.
	elseif ( in_array( 'after_entry_widgets', (array) $elements ) && 'after-entry' === $args['id'] ) {
		$defaults['before_widget'] = str_replace( 'class="widget ', 'class="widget boxed ', $defaults['before_widget'] );
	}

	return $defaults;
}


/**
 * Filters the parameters passed to a widget's display callback.
 *
 * Note: The filter is evaluated on both the front end and back end,
 * including for the Inactive Widgets sidebar on the Widgets screen.
 *
 * @since 2.5.0
 *
 * @see register_sidebar()
 *
 * @param array $params {
 *     @type array $args  {
 *         An array of widget display arguments.
 *
 *         @type string $name          Name of the sidebar the widget is assigned to.
 *         @type string $id            ID of the sidebar the widget is assigned to.
 *         @type string $description   The sidebar description.
 *         @type string $class         CSS class applied to the sidebar container.
 *         @type string $before_widget HTML markup to prepend to each widget in the sidebar.
 *         @type string $after_widget  HTML markup to append to each widget in the sidebar.
 *         @type string $before_title  HTML markup to prepend to the widget title when displayed.
 *         @type string $after_title   HTML markup to append to the widget title when displayed.
 *         @type string $widget_id     ID of the widget.
 *         @type string $widget_name   Name of the widget.
 *     }
 *     @type array $widget_args {
 *         An array of multi-widget arguments.
 *
 *         @type int $number Number increment used for multiples of the same widget.
 *     }
 * }
 */
// $params = apply_filters( 'dynamic_sidebar_params', $params );
add_filter( 'dynamic_sidebar_params', 'mai_dotheotherthing' );
function mai_dotheotherthing( $params ) {
	if ( ! $params ) {
		return;
	}
	$sidebars = array(
		'sidebar'     => 'sidebar_widgets',
		'sidebar-alt' => 'sidebar_alt_widgets',
		'after-entry' => 'after_entry_widgets',
	);
	if ( ! ( isset( $sidebars[ $params[0]['id'] ] ) && in_array( $sidebars[ $params[0]['id'] ], (array) genesis_get_option( 'boxed_elements' ) ) ) ) {
		return $params;
	}
	$params[0]['before_widget'] = str_replace( 'class="widget ', 'class="widget boxed ', $params[0]['before_widget'] );
	return $params;
}

// add_filter( 'genesis_markup_widget-wrap_open', 'mai_dothethinghere', 10, 2 );
// function mai_dothethinghere( $open, $args ) {
// d( $args );
// 	$elements = genesis_get_option( 'boxed_elements' );
// 	if ( in_array( 'adjacent_entry_nav', (array) $elements ) ) {
// 		$open = str_replace( 'class="widget ', 'class="widget boxed ', $open );
// 	}
// 	return $open;
// }

// add_filter( 'genesis_attr_widget-wrap', function( $attributes ) {
// 	$attributes['class'] .= ' boxed';
// 	// d( $attributes );
// 	// $elements = genesis_get_option( 'boxed_elements' );

// 	// if ( ! in_array( 'comment_respond', (array) $elements ) ) {
// 	// 	return;
// 	// }

// 	return $attributes;
// });

add_action( 'comment_form_before', 'mai_boxed_comment_form', 99 );
function mai_boxed_comment_form() {

	$elements = genesis_get_option( 'boxed_elements' );

	if ( ! in_array( 'comment_respond', (array) $elements ) ) {
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

function mai_get_boxed_element( $key ) {
	$element = mai_get_boxed_elements_array();
	return isset( $element[ $key ] ) ? $element[ $key ] : false;
}

function mai_get_boxed_elements_array() {
	return array(
		'entry'               => 'entry',
		'sidebar'             => 'sidebar-primary',
		'sidebar_widgets'     => '',
		'sidebar_alt'         => 'sidebar-secondary',
		'sidebar_alt_widgets' => '',
		'after_entry_widgets' => '',
		'author_box'          => 'author-box',
		'adjacent_entry_nav'  => 'adjacent-entry-pagination',
		'comments'            => 'entry-comments',
		'comment_respond'     => '',
		'pings'               => '',
	);
}
