<?php

/**
 * Plugin Name:     Mai Theme Engine
 * Plugin URI:      https://github.com/bizbudding/maitheme-engine/
 * Description:     The Mai Theme engine
 *
 * Version:         0.0.4
 *
 * GitHub URI:      bizbudding/maitheme-engine
 *
 * Author:          BizBudding, Mike Hemberger
 * Author URI:      https://bizbudding.com
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Mai_Theme_Engine' ) ) :

/**
 * Main Mai_Theme_Engine Class.
 *
 * @since 1.0.0
 */
final class Mai_Theme_Engine {

    /**
     * @var Mai_Theme_Engine The one true Mai_Theme_Engine
     * @since 1.0.0
     */
    private static $instance;

    /**
     * Main Mai_Theme_Engine Instance.
     *
     * Insures that only one instance of Mai_Theme_Engine exists in memory at any one
     * time. Also prevents needing to define globals all over the place.
     *
     * @since   1.0.0
     * @static  var array $instance
     * @uses    Mai_Theme_Engine::setup_constants() Setup the constants needed.
     * @uses    Mai_Theme_Engine::includes() Include the required files.
     * @return  object | Mai_Theme_Engine The one true Mai_Theme_Engine
     */
    public static function instance() {
        if ( ! isset( self::$instance ) ) {
            // Setup the setup
            self::$instance = new Mai_Theme_Engine;
            // Methods
            self::$instance->setup_constants();
            self::$instance->setup();
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
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'Mai_Theme_Engine' ), '1.0' );
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
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'Mai_Theme_Engine' ), '1.0' );
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
        if ( ! defined( 'MAITHEME_ENGINE_PLUGIN_VERSION' ) ) {
            define( 'MAITHEME_ENGINE_PLUGIN_VERSION', '0.0.4' );
        }

        // Plugin Folder Path.
        if ( ! defined( 'MAITHEME_ENGINE_PLUGIN_PLUGIN_DIR' ) ) {
            define( 'MAITHEME_ENGINE_PLUGIN_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        }

        // Plugin Lib Path
        if ( ! defined( 'MAITHEME_ENGINE_PLUGIN_LIB_DIR' ) ) {
            define( 'MAITHEME_ENGINE_PLUGIN_LIB_DIR', MAITHEME_ENGINE_PLUGIN_PLUGIN_DIR . 'lib/' );
        }

        // Plugin Includes Path
        if ( ! defined( 'MAITHEME_ENGINE_PLUGIN_INCLUDES_DIR' ) ) {
            define( 'MAITHEME_ENGINE_PLUGIN_INCLUDES_DIR', MAITHEME_ENGINE_PLUGIN_PLUGIN_DIR . 'includes/' );
        }

        // Plugin Folder URL.
        if ( ! defined( 'MAITHEME_ENGINE_PLUGIN_PLUGIN_URL' ) ) {
            define( 'MAITHEME_ENGINE_PLUGIN_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
        }

        // Plugin Root File.
        if ( ! defined( 'MAITHEME_ENGINE_PLUGIN_PLUGIN_FILE' ) ) {
            define( 'MAITHEME_ENGINE_PLUGIN_PLUGIN_FILE', __FILE__ );
        }

        // Plugin Base Name
        if ( ! defined( 'MAITHEME_ENGINE_PLUGIN_BASENAME' ) ) {
            define( 'MAITHEME_ENGINE_PLUGIN_BASENAME', dirname( plugin_basename( __FILE__ ) ) );
        }

    }

    /**
     * Include required files.
     *
     * @access  private
     * @since   1.0.0
     * @return  void
     */
    private function setup() {

        /**
         * Include files after theme is loaded, to mimic being run in a child theme.
         * Priority must be earlier than 10 to make sure 'genesis_initial_layouts' filter fires.
         */
        add_action( 'genesis_setup', function(){

            // Do not load old stuff
            add_filter( 'genesis_load_deprecated', '__return_false' );

            // Add HTML5 markup structure
            add_theme_support( 'html5' );

            // Add title tag support
            add_theme_support( 'title-tag' );

            // Add viewport meta tag for mobile browsers
            add_theme_support( 'genesis-responsive-viewport' );

            add_theme_support( 'genesis-menus', array(
                'utility'       => __( 'Top (Utility) Menu', 'maitheme' ),
                'primary'       => __( 'Primary Menu', 'maitheme' ),
                'header_left'   => __( 'Header Left Menu', 'maitheme' ),
                'header_right'  => __( 'Header Right Menu', 'maitheme' ),
                'secondary'     => __( 'Footer Menu', 'maitheme' ),
                'mobile'        => __( 'Mobile Menu', 'maitheme' ),
            ) );

            // Add support for structural wraps
            add_theme_support( 'genesis-structural-wraps', array(
                'archive-description',
                'breadcrumb',
                'header',
                'menu-utility',
                'menu-primary',
                'menu-secondary',
                'footer-widgets',
                'footer',
            ) );

            // Add Accessibility support
            add_theme_support( 'genesis-accessibility', array(
                '404-page',
                'drop-down-menu',
                'headings',
                'search-form',
                'skip-links',
            ) );

            // Add custom logo support
            add_theme_support( 'custom-logo', array(
                'height'        => '',
                'width'         => '',
                'flex-height'   => true,
                'flex-width'    => true,
            ) );

            // Add excerpt support for pages
            add_post_type_support( 'page', 'excerpt' );

        }, 15 );

        // Vendor
        require_once MAITHEME_ENGINE_PLUGIN_INCLUDES_DIR . 'vendor/CMB2/init.php';

        // Includes
        foreach ( glob( MAITHEME_ENGINE_PLUGIN_INCLUDES_DIR . '*.php' ) as $file ) { include_once $file; }

        add_action( 'after_setup_theme', function(){

            // Lib
            foreach ( glob( MAITHEME_ENGINE_PLUGIN_LIB_DIR . '*.php' ) as $file ) { include_once $file; }
            foreach ( glob( MAITHEME_ENGINE_PLUGIN_LIB_DIR . 'archives/*.php' ) as $file ) { include_once $file; }
            foreach ( glob( MAITHEME_ENGINE_PLUGIN_LIB_DIR . 'customize/*.php' ) as $file ) { include_once $file; }
            foreach ( glob( MAITHEME_ENGINE_PLUGIN_LIB_DIR . 'integrations/*.php' ) as $file ) { include_once $file; }
            foreach ( glob( MAITHEME_ENGINE_PLUGIN_LIB_DIR . 'layouts/*.php' ) as $file ) { include_once $file; }
            foreach ( glob( MAITHEME_ENGINE_PLUGIN_LIB_DIR . 'shortcodes/*.php' ) as $file ) { include_once $file; }

        });
    }

}
endif; // End if class_exists check.

/**
 * The main function for that returns Mai_Theme_Engine
 *
 * The main function responsible for returning the one true Mai_Theme_Engine
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $plugin = Mai_Theme_Engine(); ?>
 *
 * @since 1.0.0
 *
 * @return object|Mai_Theme_Engine The one true Mai_Theme_Engine Instance.
 */
function Mai_Theme_Engine() {
    return Mai_Theme_Engine::instance();
}

// Get Mai_Theme_Engine Running.
Mai_Theme_Engine();
