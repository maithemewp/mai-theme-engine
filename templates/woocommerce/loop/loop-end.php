<?php
/**
 * ************************************************************************************ *
 * Remove all markup and add 'mai_after_content_archive' and mai_after_flex_loop' hooks *
 * ************************************************************************************ *
 *
 * Product Loop End
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

do_action( 'mai_after_flex_loop' );
do_action( 'mai_after_content_archive' );
?>
