<?php
/**
 * Cost of Goods for WooCommerce - Analytics Class.
 *
 * @version 2.4.8
 * @since   1.7.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Analytics' ) ) :

class Alg_WC_Cost_of_Goods_Analytics {

	/**
	 * Constructor.
	 *
	 * @version 2.4.8
	 * @since   1.7.0
	 *
	 */
	function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_script' ) );

		// Analytics > Orders.
		require_once('class-alg-wc-cog-analytics-orders.php');

		// Analytics > Revenue.
		require_once('class-alg-wc-cog-analytics-revenue.php');

		// Analytics > Stock.
		require_once('class-alg-wc-cog-analytics-stock.php');
	}

	/**
	 * register_script.
	 *
	 * @version 2.4.5
	 * @since   1.7.0
	 */
	function register_script() {
		if (
			! class_exists( 'Automattic\WooCommerce\Admin\Loader' )
			|| ! function_exists( 'wc_admin_is_registered_page' )
			|| ! \Automattic\WooCommerce\Admin\Loader::is_admin_page()
			|| ! apply_filters( 'alg_wc_cog_create_analytics_orders_validation', true )
		) {
			return;
		}
		wp_register_script(
			'alg-wc-cost-of-goods-analytics-report',
			plugins_url( '/build/index.js', __FILE__ ),
			array(
				'wp-hooks',
				'wp-element',
				'wp-i18n',
				'wc-components',
			),
			alg_wc_cog()->version,
			true
		);
		wp_enqueue_script( 'alg-wc-cost-of-goods-analytics-report' );
		wp_localize_script( 'alg-wc-cost-of-goods-analytics-report', 'alg_wc_cog_analytics_obj',
			apply_filters( 'alg_wc_cog_analytics_localization_info', array() )
		);
	}

}

endif;

return new Alg_WC_Cost_of_Goods_Analytics();
