<?php
// Page
function eshop_enhancement_external_shop_page () {
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $view = '';
    if ($action == '' || $action == 'list') {
        $view = 'external-shop';
    } else if ($action == 'create' || $action == 'edit') {
        $view = 'external-shop-create';
    }

    if ($view != '') {
        eshop_enhancement_external_shop_scripts($view);
        $view = ESHOP_ENHANCEMENT_PLUGIN_PATH . '/views/' . $view . '.php';
        require_once($view);
    }
}
function eshop_enhancement_external_shop_scripts($page) {
  if( $page == 'external-shop-create' ) {
    wp_enqueue_media();
    wp_enqueue_script( 'eshop_enhancement_external_shop_scripts', plugins_url( '/../assets/media.js' , __FILE__ ), ['jquery'], ESHOP_ENHANCEMENT_VERSION );
  }
}
add_action( 'admin_enqueue_scripts', 'eshop_enhancement_external_shop_scripts' );

// add custom menu in sidebar admin woocommerce.
function eshop_enhancement_menu() {
    add_submenu_page(
        'woocommerce',
        'External Shop',
        'External Shop',
        'manage_options', 
        'external-shop',
        'eshop_enhancement_external_shop_page'
    );
}
add_action( 'admin_menu', 'eshop_enhancement_menu' );


// Functions
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
add_action('init', 'eshop_enhancement_start_session', 1);
function eshop_enhancement_form_get_data_only($data = [], $only = []) {
    $result = [];
    foreach ($data as $key => $value) {
        if (in_array($key, $only)) {
            $result[$key] = $value;
        }
    }
    return $result;
}
function eshop_enhancement_form_validate($redirect, $data, $rules) {
    $errors = [];
    foreach ($rules as $key => $rule) {
        if (isset($data[$key])) {
            if (in_array('required', $rule)) {
                if (empty($data[$key])) {
                    if (!isset($errors[$key])) $errors[$key] = [];
                    $errors[$key][] = "field {$key} is required";
                }
            }
        } elseif (isset($rule['required']) && $rule['required']) {
            if (!isset($errors[$key])) $errors[$key] = [];
            $errors[$key][] = "field {$key} is required";
        }
    }

    // if error redirect wordpress
    if (!empty($errors)) {
        $_SESSION['eshop_enhancement_form_validate_errors'] = json_encode($errors);
        wp_safe_redirect($redirect);
        exit;
    }
}
function eshop_enhancement_form_display_errors() {
    if (isset($_SESSION['eshop_enhancement_form_validate_errors'])){
        $errors = json_decode($_SESSION['eshop_enhancement_form_validate_errors']);
        unset($_SESSION['eshop_enhancement_form_validate_errors']);
        # display error to wordpress alert admin
        foreach ($errors as $key => $error) {
            foreach ($error as $e) {
                echo '<div class="notice notice-error is-dismissible">
                    <p>'.$e.'</p>
                </div>';
            }
        }
    }
}