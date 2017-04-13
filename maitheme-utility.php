<?php

/**
 * Plugin Name: 	Mai Theme Utility
 * Plugin URI: 		https://github.com/bizbudding/maitheme-utility/
 * Description: 	The Mai Theme engine
 * Version: 		0.0.1
 *
 * Author: 			BizBudding, Mike Hemberger
 * Author URI: 		https://bizbudding.com
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Mai_Theme_Utility' ) ) :

/**
 * Main Mai_Theme_Utility Class.
 *
 * @since 1.0.0
 */
final class Mai_Theme_Utility {

    /**
     * @var Mai_Theme_Utility The one true Mai_Theme_Utility
     * @since 1.0.0
     */
    private static $instance;

    /**
     * Main Mai_Theme_Utility Instance.
     *
     * Insures that only one instance of Mai_Theme_Utility exists in memory at any one
     * time. Also prevents needing to define globals all over the place.
     *
     * @since   1.0.0
     * @static  var array $instance
     * @uses    Mai_Theme_Utility::setup_constants() Setup the constants needed.
     * @uses    Mai_Theme_Utility::includes() Include the required files.
     * @return  object | Mai_Theme_Utility The one true Mai_Theme_Utility
     */
    public static function instance() {
        if ( ! isset( self::$instance ) ) {
            // Setup the setup
            self::$instance = new Mai_Theme_Utility;
            // Methods
            self::$instance->setup_constants();
            self::$instance->includes();
        }
        return self::$instance;
    }

    /**
     * Throw error on object clone.
     *
     * The whole idea of the singleton design pattern is that there is a single
     * object therefore, we don't want the object to be cloned.
     *
     * @since   1.0.0
     * @access  protected
     * @return  void
     */
    public function __clone() {
        // Cloning instances of the class is forbidden.
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'Mai_Theme_Utility' ), '1.0' );
    }

    /**
     * Disable unserializing of the class.
     *
     * @since   1.0.0
     * @access  protected
     * @return  void
     */
    public function __wakeup() {
        // Unserializing instances of the class is forbidden.
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'Mai_Theme_Utility' ), '1.0' );
    }

    /**
     * Setup plugin constants.
     *
     * @access  private
     * @since   1.0.0
     * @return  void
     */
    private function setup_constants() {

        // Plugin version.
        if ( ! defined( 'MAITHEME_UTILITY_PLUGIN_VERSION' ) ) {
            define( 'MAITHEME_UTILITY_PLUGIN_VERSION', '1.0.0' );
        }

        // Plugin Folder Path.
        if ( ! defined( 'MAITHEME_UTILITY_PLUGIN_PLUGIN_DIR' ) ) {
            define( 'MAITHEME_UTILITY_PLUGIN_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        }

        // Plugin Lib Path
        if ( ! defined( 'MAITHEME_UTILITY_PLUGIN_LIB_DIR' ) ) {
            define( 'MAITHEME_UTILITY_PLUGIN_LIB_DIR', MAITHEME_UTILITY_PLUGIN_PLUGIN_DIR . 'lib/' );
        }

        // Plugin Includes Path
        if ( ! defined( 'MAITHEME_UTILITY_PLUGIN_INCLUDES_DIR' ) ) {
            define( 'MAITHEME_UTILITY_PLUGIN_INCLUDES_DIR', MAITHEME_UTILITY_PLUGIN_PLUGIN_DIR . 'includes/' );
        }

        // Plugin Folder URL.
        if ( ! defined( 'MAITHEME_UTILITY_PLUGIN_PLUGIN_URL' ) ) {
            define( 'MAITHEME_UTILITY_PLUGIN_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
        }

        // Plugin Root File.
        if ( ! defined( 'MAITHEME_UTILITY_PLUGIN_PLUGIN_FILE' ) ) {
            define( 'MAITHEME_UTILITY_PLUGIN_PLUGIN_FILE', __FILE__ );
        }

        // Plugin Base Name
        if ( ! defined( 'MAITHEME_UTILITY_PLUGIN_BASENAME' ) ) {
            define( 'MAITHEME_UTILITY_PLUGIN_BASENAME', dirname( plugin_basename( __FILE__ ) ) );
        }

    }

    /**
     * Include required files.
     *
     * @access  private
     * @since   1.0.0
     * @return  void
     */
    private function includes() {
        // Lib
        foreach ( glob( MAITHEME_UTILITY_PLUGIN_LIB_DIR . '*.php' ) as $file ) { include_once $file; }
        foreach ( glob( MAITHEME_UTILITY_PLUGIN_LIB_DIR . 'archives/*.php' ) as $file ) { include_once $file; }
        foreach ( glob( MAITHEME_UTILITY_PLUGIN_LIB_DIR . 'customize/*.php' ) as $file ) { include_once $file; }
        foreach ( glob( MAITHEME_UTILITY_PLUGIN_LIB_DIR . 'integrations/*.php' ) as $file ) { include_once $file; }
        foreach ( glob( MAITHEME_UTILITY_PLUGIN_LIB_DIR . 'layouts/*.php' ) as $file ) { include_once $file; }
        foreach ( glob( MAITHEME_UTILITY_PLUGIN_LIB_DIR . 'shortcodes/*.php' ) as $file ) { include_once $file; }
        // Includes
        foreach ( glob( MAITHEME_UTILITY_PLUGIN_INCLUDES_DIR . '*.php' ) as $file ) { include $file; }
    }

}
endif; // End if class_exists check.

// function mai_get_suffix() {
//     $debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
//     return $debug ? '' : '.min';
// }

/**
 * The main function for that returns Mai_Theme_Utility
 *
 * The main function responsible for returning the one true Mai_Theme_Utility
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $plugin = Mai_Theme_Utility(); ?>
 *
 * @since 1.0.0
 *
 * @return object|Mai_Theme_Utility The one true Mai_Theme_Utility Instance.
 */
function Mai_Theme_Utility() {
    return Mai_Theme_Utility::instance();
}

// Get Mai_Theme_Utility Running.
Mai_Theme_Utility();
