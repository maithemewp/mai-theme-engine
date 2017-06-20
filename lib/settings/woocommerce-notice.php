<?php
/**
 * Mai Pro.
 *
 * This file adds the Genesis Connect for WooCommerce notice to the Mai Pro Engine.
 *
 * @package Mai Pro Engine
 * @author  Mike Hemberger
 * @license GPL-2.0+
 * @link    https://maipro.io
 */

/**
 * Remove the default WooCommerce Notice.
 *
 * @since 1.0.0
 */
add_action( 'admin_print_styles', 'mai_remove_woocommerce_notice' );
function mai_remove_woocommerce_notice() {

	// If below version WooCommerce 2.3.0, exit early.
	if ( ! class_exists( 'WC_Admin_Notices' ) ) {
		return;
	}

	WC_Admin_Notices::remove_notice( 'theme_support' );
}

/**
 * Enqueue script to clear the Genesis Connect for WooCommerce plugin install prompt on dismissal.
 *
 * @since 1.0.0
 */
add_action( 'admin_enqueue_scripts', 'mai_notice_script' );
function mai_notice_script() {

	// If WooCommerce isn't installed or Genesis Connect is installed, exit early.
	if ( ! class_exists( 'WooCommerce' ) || function_exists( 'gencwooc_setup' ) ) {
		return;
	}

	// If user doesn't have access, exit early.
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}

	// If message dismissed, exit early.
	if ( get_user_option( 'mai_woocommerce_message_dismissed', get_current_user_id() ) ) {
		return;
	}

	wp_add_inline_script( 'mai-admin', 'alert("Working!")' );
	wp_add_inline_script( 'mai-admin',
		'jQuery(document).on( "click", ".mai-woocommerce-notice .notice-dismiss", function() {
			jQuery.ajax({
				url: ajaxurl,
				data: {
					action: "mai_dismiss_woocommerce_notice"
				}
			});
		});'
	);

	// Admin notice
	add_action( 'admin_notices', 'mai_woocommerce_theme_notice' );

}

/**
 * Add a prompt to activate Genesis Connect for WooCommerce
 * if WooCommerce is active but Genesis Connect is not.
 *
 * @since 1.0.0
 */
function mai_woocommerce_theme_notice() {

	$notice_html = sprintf( __( 'Please install and activate <a href="https://wordpress.org/plugins/genesis-connect-woocommerce/" target="_blank">Genesis Connect for WooCommerce</a> to <strong>enable WooCommerce support for %s</strong>.', 'mai-pro-engine' ), esc_html( CHILD_THEME_NAME ) );

	if ( current_user_can( 'install_plugins' ) ) {
		$plugin_slug  = 'genesis-connect-woocommerce';
		$admin_url    = network_admin_url( 'update.php' );
		$install_link = sprintf( '<a href="%s">%s</a>', wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'install-plugin',
					'plugin' => $plugin_slug,
				),
				$admin_url
			),
			'install-plugin_' . $plugin_slug
		), __( 'install and activate Genesis Connect for WooCommerce', 'mai-pro-engine' ) );

		$notice_html = sprintf( __( 'Please %s to <strong>enable WooCommerce support for %s</strong>.', 'mai-pro-engine' ), $install_link, esc_html( CHILD_THEME_NAME ) );
	}

	echo '<div class="notice notice-info is-dismissible mai-woocommerce-notice"><p>' . $notice_html . '</p></div>';
}

/**
 * Add option to dismiss Genesis Connect for Woocommerce plugin install prompt.
 *
 * @since 1.0.0
 */
add_action( 'wp_ajax_mai_dismiss_woocommerce_notice', 'mai_dismiss_woocommerce_notice' );
function mai_dismiss_woocommerce_notice() {
	update_user_option( get_current_user_id(), 'mai_woocommerce_message_dismissed', 1 );
}

/**
 * Clear the Genesis Connect for WooCommerce plugin install prompt on theme change.
 *
 * @since 1.0.0
 */
add_action( 'switch_theme', 'mai_reset_woocommerce_notice', 10, 2 );
function mai_reset_woocommerce_notice() {

	global $wpdb;

	$args = array(
		'meta_key'   => $wpdb->prefix . 'mai_woocommerce_message_dismissed',
		'meta_value' => 1,
	);
	$users = get_users( $args );

	foreach ( $users as $user ) {
		delete_user_option( $user->ID, 'mai_woocommerce_message_dismissed' );
	}
}

/**
 * Clear the Genesis Connect for WooCommerce plugin prompt on deactivation.
 *
 * @since 1.0.0
 *
 * @param string $plugin The plugin slug.
 * @param $network_activation.
 */
add_action( 'deactivated_plugin', 'mai_reset_woocommerce_notice_on_deactivation', 10, 2 );
function mai_reset_woocommerce_notice_on_deactivation( $plugin, $network_activation ) {

	// Bail if not deactivating WooCommerce or Genesis Connect for WooCommerce.
	if ( ! in_array( $plugin, array( 'woocommerce/woocommerce.php', 'genesis-connect-woocommerce/genesis-connect-woocommerce.php' ) ) ) {
		return;
	}

	mai_reset_woocommerce_notice();
}
