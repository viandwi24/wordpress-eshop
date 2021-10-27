<?php
/**
 * VARIABELS
 */
define('ESHOP_VERSION', rand(1, 999));

/**
 * Global Functions
 */
require_once __DIR__ . '/inc/global.functions.php';

/**
 * Setup Theme
 */
function eshop_setup() {
    add_theme_support('menus');
    add_theme_support('title-tag');
    add_theme_support('woocommerce');
    add_theme_support('custom-logo');
}
add_action('after_setup_theme', 'eshop_setup');


/**
 * Register 
 */
function eshop_register() {
    // $plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce/woocommerce.php';
    register_nav_menu('navbar-top-menu', 'Top Navbar Menu');
    // add shortcode
    add_shortcode('eshop_blog', 'eshop_shortcode_page_blog');
}
add_action('init', 'eshop_register');

// 
function eshop_shortcode_page_blog() {
    require_once __DIR__ . '/shortcodes/blog.php';
}

/**
 * Setup Style and Scripts
 */
function eshop_setup_style_scripts() {
    $styles = [
        'eshop-font-awesome-all' => eshop_assets('vendor/fontawesome/css/all.min.css'),
        'eshop-font-awesome' => eshop_assets('vendor/fontawesome/css/fontawesome.min.css'),
        'eshop-vendor-style' => eshop_asset('css/vendor.css'),
        'eshop-style' => get_stylesheet_uri(),
    ];
    $scripts = [
        'eshop-vendor-script' => eshop_assets('vendor/fontawesome/js/fontawesome.min.js'),
        'eshop-script' => eshop_asset('js/app.js'),
    ];

    // 
    if (is_home()) {
        $styles['eshop-owl-carousel'] = eshop_assets('vendor/owl-carousel/dist/assets/owl.carousel.min.css');
        $styles['eshop-owl-theme'] = eshop_assets('vendor/owl-carousel/dist/assets/owl.theme.default.min.css');
        $scripts['eshop-owl-carousel'] = eshop_assets('vendor/owl-carousel/dist/owl.carousel.min.js');
    }


    // 
    foreach ($styles as $key => $style) {
        wp_enqueue_style($key, $style, [], ESHOP_VERSION);
    }
    foreach ($scripts as $key => $script) {
        wp_enqueue_script($key, $script, ['jquery'], ESHOP_VERSION, true);
    }    
}
add_action('wp_enqueue_scripts', 'eshop_setup_style_scripts');


// 
function eshop_woocommerce_breadcrumbs() {
    return array(
        'delimiter'   => ' &#47; ',
        'wrap_before' => '<nav class="woocommerce-breadcrumb eshop-breadcrumb" itemprop="breadcrumb">',
        'wrap_after'  => '</nav>',
        'before'      => '',
        'after'       => '',
        'home'        => _x( 'Home', 'breadcrumb', 'woocommerce' ),
    );
}
add_filter('woocommerce_breadcrumb_defaults', 'eshop_woocommerce_breadcrumbs');