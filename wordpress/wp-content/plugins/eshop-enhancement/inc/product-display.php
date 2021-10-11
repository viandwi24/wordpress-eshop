<?php
// 
function eshop_enhancement_product_display_external_shop_init() {
    add_action('woocommerce_single_product_summary', 'eshop_enhancement_product_display_external_shop_add_title', 0);
    add_action('woocommerce_product_meta_end', 'eshop_enhancement_product_display_external_shop_in_product_meta', 0);
}
add_action('woocommerce_init', 'eshop_enhancement_product_display_external_shop_init');

// 
function eshop_enhancement_product_display_external_shop_add_title() {
    global $post;
    echo '
        <div class="title">
            <a href="' . get_permalink($post->ID) . '">' . get_the_title($post->ID) . '</a>
        </div>
    ';
}
function eshop_enhancement_product_display_external_shop_in_product_meta() {
    global $post, $wpdb;

    // get list external_shops
    $external_shop_table_name = eshop_config('external_shop_table_name');
    $external_shops = $wpdb->get_results("SELECT * FROM {$external_shop_table_name}");
    
    // get post meta data
    $post_meta = get_post_meta($post->ID);
    $post_meta_external_shop = [];
    $post_meta_external_shop_ids = [];

    // loop post meta and search if key include string 'eshop_enhancement_product_external_shop'
    foreach ($post_meta as $key => $value) {
        if (strpos($key, 'eshop_enhancement_product_external_shop') !== false) {
            $id = str_replace('eshop_enhancement_product_external_shop_', '', $key);
            $post_meta_external_shop[$key] = $value[0];
            array_push($post_meta_external_shop_ids, $id);
        }
    }

    // display html
    echo '
        <div class="external_shop">
            <div class="title">Atau Beli Lewat :</div>
            <div class="items">
    ';

    // loop external_shops
    foreach ($external_shops as $external_shop) {
        if (in_array($external_shop->external_shop_id, $post_meta_external_shop_ids)) {
            echo '
                <a href="' . 1 . '" target="_blank">
                    <img src="' . $external_shop->shop_logo . '" alt="' . $external_shop->shop_name . '" />
                </a>
            ';
        }
    }
     
    echo '
            </div>
        </div>
    ';
}