<?php
/**
 * Plugin Name:     Eshop Enhancement - WooCommerce
 * Plugin URI:      https://www.github.com/viandwi24
 * Description:     Custom Plugin For E-Commerce WordPress with WooCommerce
 * Version:         1.0.0
 * Author:          Alfian Dwi Nugraha
 * Author URI:      https://www.github.com/viandwi24
 * License:         GPL
 * WC requires at least: 3.0.0
 * WC tested up to: 5.7
 */

//  Exit if accessed directly
if ( !defined('ABSPATH') ) {
	exit;
}

 
// constants.
define('ESHOP_ENHANCEMENT_VERSION', rand(1, 9999999));
define('ESHOP_ENHANCEMENT_PLUGIN_PATH', plugin_dir_path( __FILE__ ));
define('ESHOP_ENHANCEMENT_PLUGIN_URL', plugins_url(basename(plugin_dir_path( __FILE__ )), basename(__FILE__)));

// check if WooCommerce is active
$plugin_path = trailingslashit(WP_PLUGIN_DIR) . 'woocommerce/woocommerce.php';
if (in_array($plugin_path, wp_get_active_and_valid_plugins()) || in_array($plugin_path, wp_get_active_network_plugins())) {
	add_action('plugins_loaded', 'eshop_enhancement_init', 0);
} else {
	add_action('admin_notices', function () {
		echo '
			<div class="notice notice-warning is-dismissible">
				<p>WooCommerce Plugin is not active. Please active WooCommerce Plugin first. if WooCommerce not activated, ESHOP Enhancement Plugin will auto disable.</p>
			</div>
		';
	});
}

// Functions
require_once ESHOP_ENHANCEMENT_PLUGIN_PATH . '/inc/functions.php';

// init.
function eshop_enhancement_init() {
	require_once ESHOP_ENHANCEMENT_PLUGIN_PATH . '/inc/functions-admin.php';
	require_once ESHOP_ENHANCEMENT_PLUGIN_PATH . '/inc/product-custom-tab.php';
	require_once ESHOP_ENHANCEMENT_PLUGIN_PATH . '/inc/product-display.php';
}

// table
function eshop_enhancement_on_activate() {
	global $wpdb;
	$table_name = eshop_config('external_shop_table_name');
	$sql = "CREATE TABLE `$table_name` (
		`external_shop_id` INT NOT NULL AUTO_INCREMENT,
		`shop_name` VARCHAR(255),
		`shop_link` VARCHAR(255),
		`shop_logo` VARCHAR(255),
		PRIMARY KEY (`external_shop_id`)
	) ENGINE=InnoDB";
	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}
register_activation_hook(__FILE__, 'eshop_enhancement_on_activate');