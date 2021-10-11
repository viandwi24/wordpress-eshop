<?php
// 
function eshop_enhancement_product_custom_tab_external_shop_init() {
    add_action('woocommerce_product_write_panel_tabs', 'eshop_enhancement_product_custom_tab_external_shop');
    add_action('woocommerce_product_data_panels', 'eshop_enhancement_product_custom_tab_external_shop_content');
    add_action('woocommerce_process_product_meta', 'eshop_enhancement_product_custom_tab_external_shop_on_save', 10, 2);
}
add_action('woocommerce_init', 'eshop_enhancement_product_custom_tab_external_shop_init');

// Add Custom tab in Product Panel - Admin Woo
function eshop_enhancement_product_custom_tab_external_shop() {
    echo "<li class=\"eshop_enhancement_product_tab_external_shop\"><a href=\"#eshop_enhancement_product_tab_external_shop\"><span>"
        . 'External Shop'
        . "</span></a></li>";
}

// Add Custom content of Custom tab in Product Panel - Admin Woo
function eshop_enhancement_product_custom_tab_external_shop_content() {
    global $post;
    global $wpdb;

    // get list external_shops
    $external_shop_table_name = eshop_config('external_shop_table_name');
    $external_shops = $wpdb->get_results("SELECT * FROM {$external_shop_table_name}");

    echo '<div id="eshop_enhancement_product_tab_external_shop" class="panel woocommerce_options_panel">';
    foreach ($external_shops as $shop) {
        woocommerce_wp_text_input(
            array(
                'id' => 'eshop_enhancement_product_external_shop_' . $shop->external_shop_id,
                'label' => "{$shop->shop_name} Product Link",
                'placeholder' => 'https://',
                'value' => get_post_meta($post->ID, 'eshop_enhancement_product_external_shop_' . $shop->external_shop_id, true),
            )
        );
    }
    echo '</div>';
}

// Save Custom content of Custom tab in Product Panel - Admin Woo
function eshop_enhancement_product_custom_tab_external_shop_on_save($post_id, $post) {
    // Make sure we have a $post_id
    if ( empty( $post_id ) ) {
        return;
    }

    // 
    global $wpdb;

    // get list external_shops
    $external_shop_table_name = eshop_config('external_shop_table_name');
    $external_shops = $wpdb->get_results("SELECT * FROM {$external_shop_table_name}");
    
    // get post meta data
    $post_meta = get_post_meta($post_id);
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
    
    // check if post meta ids not include all external shop ids
    foreach ($post_meta_external_shop_ids as $index => $id) {
        if (!in_array($id, array_column($external_shops, 'external_shop_id'))) {
            delete_post_meta($post_id, 'eshop_enhancement_product_external_shop_' . $id);
        }
    }

    foreach ($external_shops as $shop) {
        $eshop_enhancement_product_external_shop_value = $_POST['eshop_enhancement_product_external_shop_' . $shop->external_shop_id];
        if (!empty($eshop_enhancement_product_external_shop_value)) {
            update_post_meta($post_id, 'eshop_enhancement_product_external_shop_' . $shop->external_shop_id, $eshop_enhancement_product_external_shop_value);
        } else {
            delete_post_meta($post_id, 'eshop_enhancement_product_external_shop_' . $shop->external_shop_id);
        }
    }
}