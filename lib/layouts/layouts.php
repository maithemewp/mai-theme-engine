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
 * @version  1.0.2
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

// Add new layout options
add_action( 'init', 'mai_register_layouts' );
function mai_register_layouts() {

    $dir = MAITHEME_ENGINE_PLUGIN_PLUGIN_URL . '/assets/images/layouts/';

    // Medium Content
    genesis_register_layout( 'md-content', array(
        'label' => __( 'Medium Content', 'baseline' ),
        'img'   => $dir . 'mdc.gif'
    ) );
    // Small Content
    genesis_register_layout( 'sm-content', array(
        'label' => __( 'Small Content', 'baseline' ),
        'img'   => $dir . 'smc.gif'
    ) );
    // Extra Small Content
    genesis_register_layout( 'xs-content', array(
        'label' => __( 'Extra Small Content', 'baseline' ),
        'img'   => $dir . 'xsc.gif'
    ) );

    // Grid Archive - 4 column
    genesis_register_layout( 'flex-loop-4', array(
        'label' => __( 'Grid Archive - 4 column', 'baseline' ),
        'img'   => $dir . 'ga4.gif',
    ) );
    // Grid Archive - 3 column
    genesis_register_layout( 'flex-loop-3', array(
        'label' => __( 'Grid Archive - 3 column', 'baseline' ),
        'img'   => $dir . 'ga3.gif',
    ) );
    // Grid Archive - 2 column
    genesis_register_layout( 'flex-loop-2', array(
        'label' => __( 'Grid Archive - 2 column', 'baseline' ),
        'img'   => $dir . 'ga2.gif',
    ) );

    // Grid Archive - 4 column Medium
    genesis_register_layout( 'flex-loop-4md', array(
        'label' => __( 'Grid Archive - 4 column', 'baseline' ),
        'img'   => $dir . 'ga4md.gif',
    ) );
    // Grid Archive - 3 column Medium
    genesis_register_layout( 'flex-loop-3md', array(
        'label' => __( 'Grid Archive - 3 column', 'baseline' ),
        'img'   => $dir . 'ga3md.gif',
    ) );
    // Grid Archive - 2 column Medium
    genesis_register_layout( 'flex-loop-2md', array(
        'label' => __( 'Grid Archive - 2 column', 'baseline' ),
        'img'   => $dir . 'ga2md.gif',
    ) );

    // Grid Archive - 4 column Small
    genesis_register_layout( 'flex-loop-4sm', array(
        'label' => __( 'Grid Archive - 4 column', 'baseline' ),
        'img'   => $dir . 'ga4sm.gif',
    ) );
    // Grid Archive - 3 column Small
    genesis_register_layout( 'flex-loop-3sm', array(
        'label' => __( 'Grid Archive - 3 column', 'baseline' ),
        'img'   => $dir . 'ga3sm.gif',
    ) );
    // Grid Archive - 2 column Small
    genesis_register_layout( 'flex-loop-2sm', array(
        'label' => __( 'Grid Archive - 2 column', 'baseline' ),
        'img'   => $dir . 'ga2sm.gif',
    ) );

    // Grid Archive - 4 column - Content/Sidebar
    genesis_register_layout( 'flex-loop-4-content-sidebar', array(
        'label' => __( 'Grid Archive - 4 column - Sidebar/Content', 'baseline' ),
        'img'   => $dir . 'ga4cs.gif',
    ) );
    // Grid Archive - 4 column - Sidebar/Content
    genesis_register_layout( 'flex-loop-4-sidebar-content', array(
        'label' => __( 'Grid Archive - 4 column - Sidebar/Content', 'baseline' ),
        'img'   => $dir . 'ga4sc.gif',
    ) );
    // Grid Archive - 3 column - Content/Sidebar
    genesis_register_layout( 'flex-loop-3-content-sidebar', array(
        'label' => __( 'Grid Archive - 3 column - Sidebar/Content', 'baseline' ),
        'img'   => $dir . 'ga3cs.gif',
    ) );
    // Grid Archive - 3 column - Sidebar/Content
    genesis_register_layout( 'flex-loop-3-sidebar-content', array(
        'label' => __( 'Grid Archive - 3 column - Sidebar/Content', 'baseline' ),
        'img'   => $dir . 'ga3sc.gif',
    ) );
    // Grid Archive - 2 column - Content/Sidebar
    genesis_register_layout( 'flex-loop-2-content-sidebar', array(
        'label' => __( 'Grid Archive - 2 column - Sidebar/Content', 'baseline' ),
        'img'   => $dir . 'ga2cs.gif',
    ) );
    // Grid Archive - 2 column - Sidebar/Content
    genesis_register_layout( 'flex-loop-2-sidebar-content', array(
        'label' => __( 'Grid Archive - 2 column - Sidebar/Content', 'baseline' ),
        'img'   => $dir . 'ga2sc.gif',
    ) );

    // Bail early if not in admin
    if ( ! is_admin() ) {
        return;
    }

    global $pagenow, $_genesis_layouts;

    // If genesis theme settings
    if ( 'admin.php' == $pagenow ) {

        if ( 'genesis' == filter_input( INPUT_GET, 'post', FILTER_SANITIZE_STRING ) ) {
            // Unset the layouts from the global
            unset($_genesis_layouts['flex-loop-4']);
            unset($_genesis_layouts['flex-loop-3']);
            unset($_genesis_layouts['flex-loop-2']);

            unset($_genesis_layouts['flex-loop-4md']);
            unset($_genesis_layouts['flex-loop-3md']);
            unset($_genesis_layouts['flex-loop-2md']);

            unset($_genesis_layouts['flex-loop-4sm']);
            unset($_genesis_layouts['flex-loop-3sm']);
            unset($_genesis_layouts['flex-loop-2sm']);

            unset($_genesis_layouts['flex-loop-4-content-sidebar']);
            unset($_genesis_layouts['flex-loop-3-content-sidebar']);
            unset($_genesis_layouts['flex-loop-2-content-sidebar']);

            unset($_genesis_layouts['flex-loop-4-sidebar-content']);
            unset($_genesis_layouts['flex-loop-3-sidebar-content']);
            unset($_genesis_layouts['flex-loop-2-sidebar-content']);
        }
    }

    // If editing a post
    if ( 'post.php' == $pagenow ) {

        $post_id       = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
        $posts_page_id = get_option('page_for_posts');
        $shop_page_id  = get_option('woocommerce_shop_page_id');

        // If editing any post/page that is not the page that shows your latest posts
        if ( ! in_array( $post_id, array( $posts_page_id, $shop_page_id ) ) ){
            // Unset the layouts from the global
            unset($_genesis_layouts['flex-loop-4']);
            unset($_genesis_layouts['flex-loop-3']);
            unset($_genesis_layouts['flex-loop-2']);

            unset($_genesis_layouts['flex-loop-4md']);
            unset($_genesis_layouts['flex-loop-3md']);
            unset($_genesis_layouts['flex-loop-2md']);

            unset($_genesis_layouts['flex-loop-4sm']);
            unset($_genesis_layouts['flex-loop-3sm']);
            unset($_genesis_layouts['flex-loop-2sm']);

            unset($_genesis_layouts['flex-loop-4-content-sidebar']);
            unset($_genesis_layouts['flex-loop-3-content-sidebar']);
            unset($_genesis_layouts['flex-loop-2-content-sidebar']);

            unset($_genesis_layouts['flex-loop-4-sidebar-content']);
            unset($_genesis_layouts['flex-loop-3-sidebar-content']);
            unset($_genesis_layouts['flex-loop-2-sidebar-content']);
        }
        // If eding the shop page
        elseif ( $post_id == $shop_page_id ) {
            unset($_genesis_layouts['md-content']);
            unset($_genesis_layouts['sm-content']);
            unset($_genesis_layouts['xs-content']);

            unset($_genesis_layouts['content-sidebar']);
            unset($_genesis_layouts['sidebar-content']);

            unset($_genesis_layouts['content-sidebar-sidebar']);
            unset($_genesis_layouts['sidebar-sidebar-content']);
            unset($_genesis_layouts['sidebar-content-sidebar']);

            unset($_genesis_layouts['full-width-content']);
        }
    }

    // If editing a term
    if ( 'term.php' == $pagenow ) {
        // If editing a WooCommerce product category or product tag
        if ( in_array( filter_input( INPUT_GET, 'taxonomy', FILTER_SANITIZE_STRING ), array( 'product_cat', 'product_tag' ) ) ) {
            unset($_genesis_layouts['md-content']);
            unset($_genesis_layouts['sm-content']);
            unset($_genesis_layouts['xs-content']);

            unset($_genesis_layouts['content-sidebar']);
            unset($_genesis_layouts['sidebar-content']);

            unset($_genesis_layouts['content-sidebar-sidebar']);
            unset($_genesis_layouts['sidebar-sidebar-content']);
            unset($_genesis_layouts['sidebar-content-sidebar']);

            unset($_genesis_layouts['full-width-content']);
        }
    }

}

// Use Flexington for all layouts
add_action( 'genesis_before_content_sidebar_wrap', 'mai_do_layout' );
function mai_do_layout() {

    $layout = genesis_site_layout();

    // No sidebars
    $nosidebars = array(
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
    if ( in_array( $layout, $nosidebars ) ) {
        remove_action( 'genesis_after_content', 'genesis_get_sidebar' );
    }

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
    add_filter( 'genesis_attr_content', function( $attributes ) use ( $layout, $double_sidebars, $double_secondary_first_content_last ) {
        $classes = ' col col-xs-12 col-md';
        if ( in_array( $layout, $double_sidebars ) ) {
            // Break to full width earlier when there are 2 sidebars
            $classes = ' col col-xs-12 col-lg-6';
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
