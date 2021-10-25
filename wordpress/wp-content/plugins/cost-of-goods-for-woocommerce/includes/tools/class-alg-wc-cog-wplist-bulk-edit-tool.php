<?php
/**
 * Cost of Goods for WooCommerce - WP_List Bulk Edit Tool Class
 *
 * @version 2.3.4
 * @since   2.3.1
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_WP_List_Bulk_Edit_Tool' ) ) :

	class Alg_WC_Cost_of_Goods_WP_List_Bulk_Edit_Tool extends \WP_List_Table {

		/**
		 * prepare_items.
		 *
		 * @version 2.3.1
		 * @since   2.3.1
		 */
		public function prepare_items() {
			if ( ! empty( $this->items ) ) {
				return;
			}
			// Columns
			$columns               = $this->get_columns();
			$hidden                = $this->get_hidden_columns();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );
			// Query args
			$types    = get_option( 'alg_wc_cog_bulk_edit_tool_product_types', array() );
			$per_page = $this->get_items_per_page( 'alg_wc_cog_bulk_edit_per_page', 20 );
			$args     = array(
				'paginate'       => true,
				'posts_per_page' => $per_page,
				'paged'          => isset( $_GET['paged'] ) ? filter_var( $_GET['paged'], FILTER_SANITIZE_NUMBER_INT ) : 1,
				'orderby'        => 'ID',
				'order'          => isset( $_GET['order'] ) ? strtoupper( sanitize_text_field( $_GET['order'] ) ) : 'ASC',
				'type'           => ( ! empty( $types ) ? $types : array_merge( array_keys( wc_get_product_types() ), array( 'variation' ) ) ),
			);
			// Search
			if ( isset( $_REQUEST['s'] ) && ! empty( $search_query = $_REQUEST['s'] ) ) {
				if ( 'title' === get_option( 'alg_wc_cog_bulk_edit_tool_search_method', 'title' ) ) {
					$args['s'] = wc_clean( wp_unslash( $search_query ) );
				} else {
					$data_store        = WC_Data_Store::load( 'product' );
					$ids               = $data_store->search_products( wc_clean( wp_unslash( $search_query ) ), '', true, true );
					$post_in           = array_merge( $ids, array( 0 ) );
					$args['include'] = $post_in;
				}
			}

			// Orderby
			if (
				isset( $_GET['orderby'] ) &&
				! empty( $orderby = $_GET['orderby'] )
			) {
				switch ( $orderby ) {
					case 'title':
						$args['orderby']  = 'title';
						break;
					case 'id':
						$args['orderby']  = 'ID';
						break;
					case '_sku':
						$args['meta_key'] = '_sku';
						$args['orderby']  = 'meta_value';
						break;
					default:
						$args['meta_key'] = $orderby;
						$args['orderby']  = 'meta_value_num';
						break;
				}
			}
			// Data
			$products = wc_get_products( $args );
			$this->set_pagination_args( [
				'total_items' => $products->total, //WE have to calculate the total number of items
				'per_page'    => $per_page, //WE have to determine how many items to show on a page
				'total_pages' => $products->max_num_pages,
			] );
			$this->items = $products->products;
		}

		/**
		 * column_default.
		 *
		 * @todo    [maybe] better description here and in settings
		 * @todo    [maybe] bulk edit order items meta
		 *
		 * @version 2.3.4
		 * @since   2.3.1
		 *
		 * @param object $item
		 * @param string $column_name
		 *
		 * @return string|void
		 */
		public function column_default( $item, $column_name ) {
			$result = '';
			switch ( $column_name ) {
				case 'id':
					$product_id = empty( $parent_id = $item->get_parent_id() ) ? $item->get_id() : $parent_id;
					$result     = '<a href="' . get_edit_post_link( $product_id ) . '">' . $item->get_id() . '</a>';
					break;
				case '_sku':
					$result = $item->get_sku();
					break;
				case 'title':
					$result = '<a href="' . $item->get_permalink() . '">' . $item->get_title() . '</a>';
					break;
				case '_price':
					$result = $item->get_price();
					break;
				case '_regular_price':
					$product_id = $item->get_id();
					$regular_price = get_post_meta( $product_id, '_regular_price', true );
					$result='<input' .
					        ' name="alg_wc_cog_bulk_edit_tool_regular_price[' . $product_id . ']"' .
					        ' type="text"' .
					        ' class="alg_wc_cog_bet_input short wc_input_price"' .
					        ' initial-value="' . $regular_price . '"' .
					        ' value="'         . $regular_price . '"' . '>';
					break;
				case '_sale_price':
					$product_id = $item->get_id();
					$sale_price    = get_post_meta( $product_id, '_sale_price', true );
					$result='<input' .
					        ' name="alg_wc_cog_bulk_edit_tool_sale_price[' . $product_id . ']"' .
					        ' type="text"' .
					        ' class="alg_wc_cog_bet_input short wc_input_price"' .
					        ' initial-value="' . $sale_price . '"' .
					        ' value="'         . $sale_price . '"' . '>';
					break;
				case '_alg_wc_cog_cost':
					$value = alg_wc_cog()->core->products->get_product_cost( $item->get_id() );
					$result = '<input' .
					          ' name="alg_wc_cog_bulk_edit_tool_costs[' . $item->get_id() . ']"' .
					          ' type="text"' .
					          ' class="alg_wc_cog_bet_input short wc_input_price"' .
					          ' initial-value="' . $value . '"' .
					          ' value="' . $value . '"' . '>';
					break;
				case '_stock':
					if ( 'yes' !== get_option( 'alg_wc_cog_bulk_edit_tool_manage_stock', 'no' ) ) {
						$result = $item->get_stock_quantity();
					} else {
						$product_id   = $item->get_id();
						$stock_value  = ( '' === ( $stock = get_post_meta( $product_id, '_stock', true ) ) ? '' : floatval( $stock ) );
						$stock_status = ( '' == ( $_stock_status = get_post_meta( $product_id, '_stock_status', true ) ) ? 'N/A' : $_stock_status );
						$result       = '<input' .
						                ' name="alg_wc_cog_bulk_edit_tool_stock[' . $product_id . ']"' .
						                ' type="text"' .
						                ' class="alg_wc_cog_bet_input short"' .
						                ' initial-value="' . $stock_value . '"' .
						                ' value="' . $stock_value . '"' . '>';
						$result       .= wc_help_tip( sprintf( __( 'Stock status: %s', 'cost-of-goods-for-woocommerce' ), $stock_status ) );
					}
					break;
				default:
					$result = $item->{$column_name};
			}
			return $result;
		}

		/**
		 * get_columns.
		 *
		 * @version 2.3.1
		 * @since   2.3.1
		 *
		 * @return array
		 */
		function get_columns() {
			$do_edit_prices = ( 'yes' === get_option( 'alg_wc_cog_bulk_edit_tool_edit_prices', 'no' ) );
			$columns        = [
				//'cb'      => '<input type="checkbox" />',
				'id'               => __( 'Product ID', 'cost-of-goods-for-woocommerce' ),
				'_sku'             => __( 'SKU', 'cost-of-goods-for-woocommerce' ),
				'title'            => __( 'Title', 'cost-of-goods-for-woocommerce' ),
				'_alg_wc_cog_cost' => __( 'Cost', 'cost-of-goods-for-woocommerce' ),
				'_stock'           => __( 'Stock', 'cost-of-goods-for-woocommerce' ),
			];
			if ( $do_edit_prices ) {
				$new_cols['_regular_price'] = __( 'Regular price', 'cost-of-goods-for-woocommerce' );
				$new_cols['_sale_price']    = __( 'Sale price', 'cost-of-goods-for-woocommerce' );
			} else {
				$new_cols['_price'] = __( 'Price', 'cost-of-goods-for-woocommerce' );
			}
			$position = array_search( '_alg_wc_cog_cost', array_keys( $columns ) );
			$columns  = array_merge( array_slice( $columns, 0, $position + 1 ), $new_cols, array_slice( $columns, $position + 1 ) );
			return $columns;
		}

		/**
		 * get_sortable_columns.
		 *
		 * @version 2.3.1
		 * @since   2.3.1
		 *
		 * @return array
		 */
		public function get_sortable_columns() {
			$sortable_columns = array(
				'id'               => array( 'id', true ),
				'_sku'             => array( '_sku', true ),
				'title'            => array( 'title', true ),
				'_alg_wc_cog_cost' => array( '_alg_wc_cog_cost', true ),
				'_price'           => array( '_price', true ),
				'_stock'           => array( '_stock', true ),
			);
			$do_edit_prices   = ( 'yes' === get_option( 'alg_wc_cog_bulk_edit_tool_edit_prices', 'no' ) );
			if ( $do_edit_prices ) {
				$sortable_columns['_regular_price'] = array( '_regular_price', true );
				$sortable_columns['_sale_price']    = array( '_sale_price', true );
			}
			return $sortable_columns;
		}

		/**
		 * Define which columns are hidden
		 *
		 * @version 2.3.1
		 * @since   2.3.1
		 *
		 * @return Array
		 */
		public function get_hidden_columns() {
			return array();
		}
	}

endif;