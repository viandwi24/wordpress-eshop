<?php
function dd($data) {
    echo '<pre>';
    if (is_array($data) || is_object($data)) {
        print_r($data);
    } else {
        echo $data;
    }
    echo '</pre>';
    die('');
    exit;
}
function eshop_enhancement_start_session() {
    if(!session_id()) {
        session_start();
    }
}
function eshop_config($key) {
    global $wpdb;
    $defaultConfig = [
        'external_shop_table_name' => $wpdb->prefix . 'wc_external_shops',
        'social_media_table_name' => $wpdb->prefix . 'wc_social_media'
    ];
	return apply_filters('eshop_config', $defaultConfig[$key], $key, $defaultConfig);
}
function money($value) {
    if (is_numeric($value)) {
        return "Rp " . number_format($value, 0,',','.');
    }
    return $value;
}
function eshop_get_external_shop() {
    global $wpdb;
    $tableName = eshop_config('external_shop_table_name');
    $sql = "SELECT * FROM $tableName";
    $result = $wpdb->get_results($sql);
    return $result;
}
function eshop_get_social_media() {
    global $wpdb;
    $tableName = eshop_config('social_media_table_name');
    $sql = "SELECT * FROM $tableName";
    $result = $wpdb->get_results($sql);
    return $result;
}