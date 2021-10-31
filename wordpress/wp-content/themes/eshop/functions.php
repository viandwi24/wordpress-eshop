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
    add_theme_support('widgets');
    add_theme_support('widgets-block-editor');
}
add_action('after_setup_theme', 'eshop_setup');

// 
// add_filter( 'woocommerce_product_query_tax_query', 'eshop_filter_product_topic', 10, 2 );
// function eshop_filter_product_topic( $tax_query, $query ) {
//     // Only on Product Category archives pages
//     if( is_admin() || ! is_product_category()  ) return $tax_query;
//     dd('ehe');

//     // The taxonomy for Product Categories
//     $taxonomy = 'product_cat';

//     if( isset( $_GET['topic'] ) && ! empty( $_GET['topic'] )) {
//         $tax_query[] = array(
//             'taxonomy'       => $taxonomy,
//             'field'   => 'slug',
//             'terms'     => array( $_GET['topic'] ),
//             'operator'   => 'IN'
//         );
//     }

//     return $tax_query;
// }

// 
// function eshop_widgets_init() {
// 	register_sidebar(
// 		array(
// 			'name'          => 'Eshop Products',
// 			'id'            => 'eshop-sidebar-products',
// 			'description'   => 'Add widgets to appear in Sidebar Products Pages.',
// 			'before_widget' => '<section id="%1$s" class="widget %2$s text-center">',
// 			'after_widget'  => '</section>',
// 			'before_title'  => '<h2 class="widget-title">',
// 			'after_title'   => '</h2>',
// 		)
// 	);

// }
// add_action( 'widgets_init', 'eshop_widgets_init' );


/**
 * Register 
 */
function eshop_register() {
    // $plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce/woocommerce.php';
    register_nav_menu('navbar-top-menu', 'Top Navbar Menu');
    register_nav_menu('navbar-bottom-menu', 'Bottom Navbar Menu');
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


// 
if( ! function_exists( 'eshop_better_commets' ) ):
function eshop_better_commets($comment, $args, $depth) {
    ?>
   <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
    <div class="comment">
        <div class="img-thumbnail d-none d-sm-block">
            <?php echo get_avatar($comment,$size='80',$default='http://0.gravatar.com/avatar/36c2a25e62935705c5565ec465c59a70?s=32&d=mm&r=g' ); ?>
        </div>
        <div class="comment-block">
            <div class="comment-arrow"></div>
                <?php if ($comment->comment_approved == '0') : ?>
                    <em><?php esc_html_e('Your comment is awaiting moderation.','5balloons_theme') ?></em>
                    <br />
                <?php endif; ?>
                <span class="comment-by">
                    <strong><?php echo get_comment_author() ?></strong>
                    <span class="float-right">
                        <span> <a href="#"><i class="fa fa-reply"></i> <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?></a></span>
                    </span>
                </span>
            <p> <?php comment_text() ?></p>
            <span class="date float-right"><?php printf(/* translators: 1: date and time(s). */ esc_html__('%1$s at %2$s' , '5balloons_theme'), get_comment_date(),  get_comment_time()) ?></span>
        </div>
        </div>
<?php } endif; ?>