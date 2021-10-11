<?php
function eshop_config($key) {
    global $wpdb;
    $defaultConfig = [
        'external_shop_table_name' => $wpdb->prefix . 'wc_external_shops'
    ];
	return apply_filters('eshop_config', $defaultConfig[$key], $key, $defaultConfig);
}