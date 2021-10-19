<?php
// 
function eshop_enhancement_product_display_external_shop_init() {
    add_action('woocommerce_single_product_summary', 'eshop_enhancement_product_display_external_shop_add_title', 0);
    add_action('woocommerce_product_meta_end', 'eshop_enhancement_product_display_external_shop_in_product_meta', 0);
    add_action('woocommerce_product_meta_end', 'eshop_enhancement_product_display_whatsapp_in_product_meta', 0);
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
function eshop_enhancement_product_display_whatsapp_in_product_meta() {
    global $post, $wpdb;
    
    // GET SOCIAL MEDIA
    $social_media_table_name = eshop_config('social_media_table_name');
    $social_medias = $wpdb->get_results("SELECT * FROM {$social_media_table_name} WHERE `social_media_name` LIKE '%whatsapp%'");
    
    // Display
    if (count($social_medias) > 0) {
        $whatsapp = $social_medias[0];
        echo '
            <div class="external_shop">
                <div class="title">Atau Hubungi Kami Di :</div>
                <div class="items">
                    <a href="' . $whatsapp->social_media_link . '?text= '. get_post_permalink($post->ID) .'" target="_blank">
                        <i class="' . $whatsapp->social_media_logo . ' fa-2x text-green-500"></i>
                    </a>
                </div>
            </div>
        ';
    }
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
            $post_meta_external_shop[$id] = $value[0];
            array_push($post_meta_external_shop_ids, $id);
        }
    }

    // display html
    if (count($post_meta_external_shop_ids) > 0) {
        echo '
            <div class="external_shop">
                <div class="title">Bisa Beli Lewat :</div>
                <div class="items">
        ';

        // loop external_shops
        foreach ($external_shops as $external_shop) {
            if (in_array($external_shop->external_shop_id, $post_meta_external_shop_ids)) {
                echo '
                    <a href="' . $post_meta_external_shop[$external_shop->external_shop_id] . '" target="_blank">
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
}