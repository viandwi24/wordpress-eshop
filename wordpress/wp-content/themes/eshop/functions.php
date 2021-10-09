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
    // add_theme_support('menus');
}
add_action('after_setup_theme', 'eshop_setup');

/**
 * Register 
 */
function eshop_register() {
    $plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce/woocommerce.php';
    register_nav_menu('navbar-top-menu', 'Top Navbar Menu');
}
add_action('init', 'eshop_register');

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
    foreach ($styles as $key => $style) {
        wp_enqueue_style($key, $style, [], ESHOP_VERSION);
    }
    foreach ($scripts as $key => $script) {
        wp_enqueue_script($key, $script, [], ESHOP_VERSION, true);
    }    
}
add_action('wp_enqueue_scripts', 'eshop_setup_style_scripts');