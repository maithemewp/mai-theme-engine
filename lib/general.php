<?php

// Add singular body class to the head
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
	<script type="text/javascript">
		//<![CDATA[
		(function(){
			var c = document.body.className;
			c = c.replace(/no-js/, 'js');
			document.body.className = c;
		})();
		jQuery(function( $ ) {
			'use strict';
			jQuery( 'p:empty' ).remove();
		});
		//]]>
	</script>
	<?php
}

/**
 * Remove empty <p></p> tags from content.
 * We have a bunch of cleanup in the shortcodes,
 * but this seems much easier, though a bit of a hack.
 */
add_action( 'genesis_before', 'mai_html_cleanup_script', 1 );
function mai_html_cleanup_script() {
	?>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(function( $ ) { 'use strict'; jQuery( 'p:empty' ).remove(); });
		//]]>
	</script>
	<?php
}
