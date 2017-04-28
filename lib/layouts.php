<?php
/**
 * Mai Theme.
 *
 * WARNING: This file is part of the core Mai Theme framework.
 * The goal is to keep all files in /lib/ untouched.
 * That way we can easily update the core structure of the theme on existing sites without breaking things
 *
 * @author   Mike Hemberger
 *
 * @version  1.0.3
 */


/**
 * Helper function to force a layout in a template
 *
 * Used as shortcut second parameter for `add_filter()`.
 *
 * add_filter( 'genesis_pre_get_option_site_layout', '__mai_return_flex_loop_3md' );
 */

function __mai_return_md_content() {
    return 'md-content';
}

function __mai_return_sm_content() {
    return 'sm-content';
}

function __mai_return_xs_content() {
    return 'xs-content';
}

function __mai_return_flex_loop_4() {
    return 'flex-loop-4';
}

function __mai_return_flex_loop_3() {
    return 'flex-loop-3';
}

function __mai_return_flex_loop_2() {
    return 'flex-loop-2';
}

function __mai_return_flex_loop_4md() {
    return 'flex-loop-4md';
}

function __mai_return_flex_loop_3md() {
    return 'flex-loop-3md';
}

function __mai_return_flex_loop_2md() {
    return 'flex-loop-2md';
}

function __mai_return_flex_loop_4sm() {
    return 'flex-loop-4sm';
}

function __mai_return_flex_loop_3sm() {
    return 'flex-loop-3sm';
}

function __mai_return_flex_loop_2sm() {
    return 'flex-loop-2sm';
}

function __mai_return_flex_loop_4_content_sidebar() {
    return 'flex-loop-4-content-sidebar';
}

function __mai_return_flex_loop_4_sidebar_content() {
    return 'flex-loop-4-sidebar-content';
}

function __mai_return_flex_loop_3_content_sidebar() {
    return 'flex-loop-3-content-sidebar';
}

function __mai_return_flex_loop_3_sidebar_content() {
    return 'flex-loop-3-sidebar-content';
}

function __mai_return_flex_loop_2_content_sidebar() {
    return 'flex-loop-2-content-sidebar';
}

function __mai_return_flex_loop_2_sidebar_content() {
    return 'flex-loop-2-sidebar-content';
}



    // *
    //  * Add new layout options.
    //  *
    //  * @return  void

add_action( 'genesis_setup', 'mai_register_layouts', 20 );
function mai_register_layouts() {

    // Layout image directory
    $dir = MAITHEME_ENGINE_PLUGIN_PLUGIN_URL . '/assets/images/layouts/';

    // // Medium Content
    // $layouts['md-content'] = array(
    //     'label' => __( 'Medium Content', 'baseline' ),
    //     'img'   => $dir . 'mdc.gif',
    //     'type'  => array( 'site', 'archive' ),
    // );
    // // Small Content
    // $layouts['sm-content'] = array(
    //     'label' => __( 'Small Content', 'baseline' ),
    //     'img'   => $dir . 'smc.gif',
    //     'type'  => array( 'site', 'archive' ),
    // );
    // // Extra Small Content
    // $layouts['xs-content'] = array(
    //     'label' => __( 'Extra Small Content', 'baseline' ),
    //     'img'   => $dir . 'xsc.gif',
    //     'type'  => array( 'site', 'archive' ),
    // );

    // // Grid Archive - 4 column
    // $layouts['flex-loop-4'] = array(
    //     'label' => __( 'Grid Archive - 4 column', 'baseline' ),
    //     'img'   => $dir . 'ga4.gif',
    //     'type'  => array( 'archive' ),
    // );
    // // Grid Archive - 3 column
    // $layouts['flex-loop-3'] = array(
    //     'label' => __( 'Grid Archive - 3 column', 'baseline' ),
    //     'img'   => $dir . 'ga3.gif',
    //     'type'  => array( 'archive' ),
    // );
    // // Grid Archive - 2 column
    // $layouts['flex-loop-2'] = array(
    //     'label' => __( 'Grid Archive - 2 column', 'baseline' ),
    //     'img'   => $dir . 'ga2.gif',
    //     'type'  => array( 'archive' ),
    // );

    // // Grid Archive - 4 column Medium
    // $layouts['flex-loop-4md'] = array(
    //     'label' => __( 'Grid Archive - 4 column', 'baseline' ),
    //     'img'   => $dir . 'ga4md.gif',
    //     'type'  => array( 'archive' ),
    // );
    // // Grid Archive - 3 column Medium
    // $layouts['flex-loop-3md'] = array(
    //     'label' => __( 'Grid Archive - 3 column', 'baseline' ),
    //     'img'   => $dir . 'ga3md.gif',
    //     'type'  => array( 'archive' ),
    // );
    // // Grid Archive - 2 column Medium
    // $layouts['flex-loop-2md'] = array(
    //     'label' => __( 'Grid Archive - 2 column', 'baseline' ),
    //     'img'   => $dir . 'ga2md.gif',
    //     'type'  => array( 'archive' ),
    // );

    // // Grid Archive - 4 column Small
    // $layouts['flex-loop-4sm'] = array(
    //     'label' => __( 'Grid Archive - 4 column', 'baseline' ),
    //     'img'   => $dir . 'ga4sm.gif',
    //     'type'  => array( 'archive' ),
    // );
    // // Grid Archive - 3 column Small
    // $layouts['flex-loop-3sm'] = array(
    //     'label' => __( 'Grid Archive - 3 column', 'baseline' ),
    //     'img'   => $dir . 'ga3sm.gif',
    //     'type'  => array( 'archive' ),
    // );
    // // Grid Archive - 2 column Small
    // $layouts['flex-loop-2sm'] = array(
    //     'label' => __( 'Grid Archive - 2 column', 'baseline' ),
    //     'img'   => $dir . 'ga2sm.gif',
    //     'type'  => array( 'archive' ),
    // );

    // // Grid Archive - 4 column - Content/Sidebar
    // $layouts['flex-loop-4-content-sidebar'] = array(
    //     'label' => __( 'Grid Archive - 4 column - Sidebar/Content', 'baseline' ),
    //     'img'   => $dir . 'ga4cs.gif',
    //     'type'  => array( 'archive' ),
    // );
    // // Grid Archive - 4 column - Sidebar/Content
    // $layouts['flex-loop-4-sidebar-content'] = array(
    //     'label' => __( 'Grid Archive - 4 column - Sidebar/Content', 'baseline' ),
    //     'img'   => $dir . 'ga4sc.gif',
    //     'type'  => array( 'archive' ),
    // );
    // // Grid Archive - 3 column - Content/Sidebar
    // $layouts['flex-loop-3-content-sidebar'] = array(
    //     'label' => __( 'Grid Archive - 3 column - Sidebar/Content', 'baseline' ),
    //     'img'   => $dir . 'ga3cs.gif',
    //     'type'  => array( 'archive' ),
    // );
    // // Grid Archive - 3 column - Sidebar/Content
    // $layouts['flex-loop-3-sidebar-content'] = array(
    //     'label' => __( 'Grid Archive - 3 column - Sidebar/Content', 'baseline' ),
    //     'img'   => $dir . 'ga3sc.gif',
    //     'type'  => array( 'archive' ),
    // );
    // // Grid Archive - 2 column - Content/Sidebar
    // $layouts['flex-loop-2-content-sidebar'] = array(
    //     'label' => __( 'Grid Archive - 2 column - Sidebar/Content', 'baseline' ),
    //     'img'   => $dir . 'ga2cs.gif',
    //     'type'  => array( 'archive' ),
    // );
    // // Grid Archive - 2 column - Sidebar/Content
    // $layouts['flex-loop-2-sidebar-content'] = array(
    //     'label' => __( 'Grid Archive - 2 column - Sidebar/Content', 'baseline' ),
    //     'img'   => $dir . 'ga2sc.gif',
    //     'type'  => array( 'archive' ),
    // );

    // return $layouts;

    // Medium Content
    genesis_register_layout( 'md-content', array(
        'label' => __( 'Medium Content', 'baseline' ),
        'img'   => $dir . 'mdc.gif',
        'type'  => array( 'site', 'archive' ),
    ) );
    // Small Content
    genesis_register_layout( 'sm-content', array(
        'label' => __( 'Small Content', 'baseline' ),
        'img'   => $dir . 'smc.gif',
        'type'  => array( 'site', 'archive' ),
    ) );
    // Extra Small Content
    genesis_register_layout( 'xs-content', array(
        'label' => __( 'Extra Small Content', 'baseline' ),
        'img'   => $dir . 'xsc.gif',
        'type'  => array( 'site', 'archive' ),
    ) );

    // Grid Archive - 4 column
    genesis_register_layout( 'flex-loop-4', array(
        'label' => __( 'Grid Archive - 4 column', 'baseline' ),
        'img'   => $dir . 'ga4.gif',
        'type'  => array( 'archive' ),
    ) );
    // Grid Archive - 3 column
    genesis_register_layout( 'flex-loop-3', array(
        'label' => __( 'Grid Archive - 3 column', 'baseline' ),
        'img'   => $dir . 'ga3.gif',
        'type'  => array( 'archive' ),
    ) );
    // Grid Archive - 2 column
    genesis_register_layout( 'flex-loop-2', array(
        'label' => __( 'Grid Archive - 2 column', 'baseline' ),
        'img'   => $dir . 'ga2.gif',
        'type'  => array( 'archive' ),
    ) );

    // Grid Archive - 4 column Medium
    genesis_register_layout( 'flex-loop-4md', array(
        'label' => __( 'Grid Archive - 4 column', 'baseline' ),
        'img'   => $dir . 'ga4md.gif',
        'type'  => array( 'archive' ),
    ) );
    // Grid Archive - 3 column Medium
    genesis_register_layout( 'flex-loop-3md', array(
        'label' => __( 'Grid Archive - 3 column', 'baseline' ),
        'img'   => $dir . 'ga3md.gif',
        'type'  => array( 'archive' ),
    ) );
    // Grid Archive - 2 column Medium
    genesis_register_layout( 'flex-loop-2md', array(
        'label' => __( 'Grid Archive - 2 column', 'baseline' ),
        'img'   => $dir . 'ga2md.gif',
        'type'  => array( 'archive' ),
    ) );

    // Grid Archive - 4 column Small
    genesis_register_layout( 'flex-loop-4sm', array(
        'label' => __( 'Grid Archive - 4 column', 'baseline' ),
        'img'   => $dir . 'ga4sm.gif',
        'type'  => array( 'archive' ),
    ) );
    // Grid Archive - 3 column Small
    genesis_register_layout( 'flex-loop-3sm', array(
        'label' => __( 'Grid Archive - 3 column', 'baseline' ),
        'img'   => $dir . 'ga3sm.gif',
        'type'  => array( 'archive' ),
    ) );
    // Grid Archive - 2 column Small
    genesis_register_layout( 'flex-loop-2sm', array(
        'label' => __( 'Grid Archive - 2 column', 'baseline' ),
        'img'   => $dir . 'ga2sm.gif',
        'type'  => array( 'archive' ),
    ) );

    // Grid Archive - 4 column - Content/Sidebar
    genesis_register_layout( 'flex-loop-4-content-sidebar', array(
        'label' => __( 'Grid Archive - 4 column - Sidebar/Content', 'baseline' ),
        'img'   => $dir . 'ga4cs.gif',
        'type'  => array( 'archive' ),
    ) );
    // Grid Archive - 4 column - Sidebar/Content
    genesis_register_layout( 'flex-loop-4-sidebar-content', array(
        'label' => __( 'Grid Archive - 4 column - Sidebar/Content', 'baseline' ),
        'img'   => $dir . 'ga4sc.gif',
        'type'  => array( 'archive' ),
    ) );
    // Grid Archive - 3 column - Content/Sidebar
    genesis_register_layout( 'flex-loop-3-content-sidebar', array(
        'label' => __( 'Grid Archive - 3 column - Sidebar/Content', 'baseline' ),
        'img'   => $dir . 'ga3cs.gif',
        'type'  => array( 'archive' ),
    ) );
    // Grid Archive - 3 column - Sidebar/Content
    genesis_register_layout( 'flex-loop-3-sidebar-content', array(
        'label' => __( 'Grid Archive - 3 column - Sidebar/Content', 'baseline' ),
        'img'   => $dir . 'ga3sc.gif',
        'type'  => array( 'archive' ),
    ) );
    // Grid Archive - 2 column - Content/Sidebar
    genesis_register_layout( 'flex-loop-2-content-sidebar', array(
        'label' => __( 'Grid Archive - 2 column - Sidebar/Content', 'baseline' ),
        'img'   => $dir . 'ga2cs.gif',
        'type'  => array( 'archive' ),
    ) );
    // Grid Archive - 2 column - Sidebar/Content
    genesis_register_layout( 'flex-loop-2-sidebar-content', array(
        'label' => __( 'Grid Archive - 2 column - Sidebar/Content', 'baseline' ),
        'img'   => $dir . 'ga2sc.gif',
        'type'  => array( 'archive' ),
    ) );

}

/**
 * Add archive layout types to the static blog page and WooCommerce shop page.
 *
 * @return  array  The layouts to show.
 */
// add_filter( 'genesis_get_layouts', 'mai_maybe_add_layouts_to_static_pages', 10, 2 );
function mai_maybe_add_layouts_to_static_pages( $layouts, $type ) {

    // Bail early if not in admin
    if ( ! is_admin() ) {
        return $layouts;
    }

    $post_id       = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
    $posts_page_id = get_option('page_for_posts');
    $shop_page_id  = get_option('woocommerce_shop_page_id');

    // If static blog page or WooCommerce shop page
    if ( ( $post_id == $posts_page_id ) || ( class_exists('WooCommerce') && ( $post_id == $shop_page_id ) ) ) {
        global $_genesis_layouts;
        foreach ( (array) $_genesis_layouts as $id => $data ) {
            // If the layout type contains 'archive'
            if ( in_array( 'archive', $data['type'] ) ) {
                $layouts[ $id ] = $data;
            }
        }
    }

    return $layouts;

}

/**
 * Add custom body class to the head
 */
add_filter( 'body_class', 'mai_sidebars_body_class' );
function mai_sidebars_body_class( $classes ) {

   $layout = genesis_site_layout();

    // No sidebars
    $no_sidebars = array(
        'full-width-content',
        'md-content',
        'sm-content',
        'xs-content',
        'flex-loop-4',
        'flex-loop-3',
        'flex-loop-2',
        'flex-loop-4md',
        'flex-loop-3md',
        'flex-loop-2md',
        'flex-loop-4sm',
        'flex-loop-3sm',
        'flex-loop-2sm',
    );
    // Add .no-sidebar body class if don't have any sidebars
    if ( in_array( genesis_site_layout(), $no_sidebars ) ) {
        $classes[] = ' no-sidebars';
    }
    return $classes;
}

/**
 * Use Flexington for the main content and sidebar layout.
 *
 * @return  void
 */
add_action( 'genesis_before_content_sidebar_wrap', 'mai_do_layout' );
function mai_do_layout() {

    $layout = genesis_site_layout();

    d( $layout );

    // No sidebars
    $no_sidebars = array(
        'full-width-content',
        'md-content',
        'sm-content',
        'xs-content',
        'flex-loop-4',
        'flex-loop-3',
        'flex-loop-2',
        'flex-loop-4md',
        'flex-loop-3md',
        'flex-loop-2md',
        'flex-loop-4sm',
        'flex-loop-3sm',
        'flex-loop-2sm',
    );

    // Single sidebar
    $single_primary_first = array(
        'sidebar-content',
        'flex-loop-4-sidebar-content',
        'flex-loop-3-sidebar-content',
        'flex-loop-2-sidebar-content',
    );
    $single_content_first = array(
        'content-sidebar',
        'flex-loop-4-content-sidebar',
        'flex-loop-3-content-sidebar',
        'flex-loop-2-content-sidebar',
    );
    $single_sidebars = array_merge( $single_primary_first, $single_content_first );

    // Double sidebars
    $double_secondary_first = array(
        'sidebar-content-sidebar',
    );
    $double_secondary_last = array(
        'content-sidebar-sidebar',
    );
    $double_secondary_first_content_last = array(
        'sidebar-sidebar-content',
    );
    $double_sidebars = array_merge( $double_secondary_first, array_merge( $double_secondary_last, $double_secondary_first_content_last ) );

    $secondary_first = array_merge( $double_secondary_first, $double_secondary_first_content_last );
    $sidebars        = array_merge( $single_sidebars, $double_sidebars );

    // Remove primary sidebar
    if ( in_array( $layout, $no_sidebars ) ) {
        remove_action( 'genesis_after_content', 'genesis_get_sidebar' );
    }

    // Reposition secondary sidebar, we'll add it back later where we need it
    remove_action( 'genesis_after_content_sidebar_wrap', 'genesis_get_sidebar_alt' );

    // Add back the secondary sidebary where flexington needs it
    if ( in_array( $layout, $double_sidebars ) ) {
        add_action( 'genesis_after_content', function() {
            get_sidebar( 'alt' );
        }, 11 );
    }

    // Add flexington row classes to the content sidebar wrap
    add_filter( 'genesis_attr_content-sidebar-wrap', function( $attributes ) use ( $layout, $sidebars ) {
        $gutter = '';
        $align  = ' around-xs';
        // Remove alignment and add gutter
        if ( in_array( $layout, $sidebars ) ) {
            $gutter = ' gutter-30';
            $align  = '';
        }
        $attributes['class'] .= ' row' . $gutter . $align;
        return $attributes;
    });

    /**
     * Add flexington column classes to the content
     * The breakpoint classes here need to match with the sidebar classes and total 12
     * to avoid flash of full-width containers on page load
     */
    add_filter( 'genesis_attr_content', function( $attributes ) use ( $layout, $no_sidebars, $double_sidebars, $double_secondary_first_content_last ) {
        $classes = '';
        // Add .content-no-sidebar class if don't have any sidebars
        if ( in_array( $layout, $no_sidebars ) ) {
            $classes .= ' content-no-sidebars';
        }
        $classes .= ' col col-xs-12 col-md';
        if ( in_array( $layout, $double_sidebars ) ) {
            // Break to full width earlier when there are 2 sidebars
            $classes .= ' col col-xs-12 col-lg-6';
        }
        if ( in_array( $layout, $double_secondary_first_content_last ) ) {
            $classes .= ' last-lg';
        }
        $attributes['class'] .= $classes;
        return $attributes;
    });

    // Add flexington column classes to the primary sidebar
    add_filter( 'genesis_attr_sidebar-primary', function( $attributes ) use ( $layout, $double_sidebars, $single_primary_first ) {
        $classes = ' col col-xs-12 col-md-4';
        if ( in_array( $layout, $double_sidebars ) ) {
            // Break to full width earlier when there are 2 sidebars
            $classes = ' col col-xs-12 col-lg-4';
        }
        if ( in_array( $layout, $single_primary_first ) ) {
            $classes .= ' first-lg';
        }
        $attributes['class'] .= $classes;
        return $attributes;
    });

    // Add flexington column classes to the secondary sidebar
    add_filter( 'genesis_attr_sidebar-secondary', function( $attributes ) use ( $layout, $secondary_first ) {
        // This will only show if there are 2 sidebars, no need for the conditional above
        $classes = ' col col-xs-12 col-lg-2';
        if ( in_array( $layout, $secondary_first ) ) {
            $classes .= ' first-lg';
        }
        $attributes['class'] .= $classes;
        return $attributes;
    });

}

/**
 * Filter the footer-widgets context of the genesis_structural_wrap to add a div before the closing wrap div.
 *
 * @param   string  $output             The markup to be returned
 * @param   string  $original_output    Set to either 'open' or 'close'
 *
 * @return  string  The footer markup
 */
add_filter( 'genesis_structural_wrap-footer-widgets', 'mai_footer_widgets_flexington_row', 10, 2 );
function mai_footer_widgets_flexington_row( $output, $original_output ) {
    if ( 'open' == $original_output ) {
        $output = $output . '<div class="row gutter-30">';
    }
    elseif ( 'close' == $original_output ) {
        $output = '</div>' . $output;
    }
    return $output;
}

/**
 * Filter the footer-widget markup to add flexington column classes
 *
 * @param   array   $attributes  The array of attributes to be added to the footer widget wrap.
 *
 * @return  array  The attributes
 */
add_filter( 'genesis_attr_footer-widget-area', 'alsdkfjklsajflksa' );
function alsdkfjklsajflksa( $attributes ) {
    switch ( mai_get_footer_widgets_count() ) {
        case '1':
            $classes = ' col col-xs-12 center-xs';
            break;
        case '2':
            $classes = ' col col-xs-12 col-sm-6';
            break;
        case '3':
            $classes = ' col col-xs-12 col-sm-6 col-md-4';
            break;
        case '4':
            $classes = ' col col-xs-12 col-sm-6 col-md-3';
            break;
        case '6':
            $classes = ' col col-xs-6 col-sm-4 col-md-2';
            break;
        default:
            $classes = ' col col-xs';

    }
    $attributes['class'] .= $classes;
    return $attributes;
}
