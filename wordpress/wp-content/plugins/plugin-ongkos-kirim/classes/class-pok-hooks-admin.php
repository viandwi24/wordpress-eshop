<?php

/**
 * Admin hooks
 */
class POK_Hooks_Admin {

	/**
	 * Costructor
	 */
	public function __construct() {
		global $pok_helper;
		global $pok_core;
		$this->core     = $pok_core;
		$this->setting  = new POK_Setting();
		$this->helper   = $pok_helper;
		if ( $this->helper->is_plugin_active() ) {

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

			// order page.
			add_filter( 'woocommerce_admin_billing_fields', array( $this, 'custom_admin_billing_fields' ) );
			add_filter( 'woocommerce_admin_shipping_fields', array( $this, 'custom_admin_shipping_fields' ) );
			add_action( 'woocommerce_order_item_add_line_buttons', array( $this, 'add_button_ongkir' ) );
			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_item_meta' ) );
			add_action( 'woocommerce_after_order_itemmeta', array( $this, 'add_switch_button' ), 10, 2 );
			add_action( 'woocommerce_admin_order_totals_after_discount', array( $this, 'add_weight_info' ) );
			add_filter( 'woocommerce_ajax_get_customer_details', array( $this, 'get_district_from_meta' ), 10, 3 );
			add_action( 'woocommerce_process_shop_order_meta', array( $this, 'save_addresses_meta' ), 50, 2 );
			if ( $this->helper->compare_wc_version( '>=', '3.1.0' ) ) {
				add_filter( 'woocommerce_order_item_display_meta_key', array( $this, 'change_meta_keys' ), 10, 3 );
				add_filter( 'woocommerce_order_item_display_meta_value', array( $this, 'change_meta_values' ), 10, 3 );
			}
			add_filter( 'woocommerce_shipping_address_map_url_parts', array( $this, 'change_address_map_url' ), 10, 2 );

			// user page.
			add_filter( 'woocommerce_customer_meta_fields', array( $this, 'custom_user_address_fields' ) );
			add_action( 'profile_update', array( $this, 'save_user_address' ), 10, 2 );

			// report page.
			add_filter( 'woocommerce_admin_reports', array( $this, 'shipping_report' ) );

			// coupons.
			add_filter( 'woocommerce_coupon_discount_types', array( $this, 'new_coupon_types' ) );
			add_filter( 'woocommerce_coupon_data_tabs', array( $this, 'coupon_shipping_restriction_tab' ) );
			add_action( 'woocommerce_coupon_data_panels', array( $this, 'coupon_shipping_restriction_panel' ), 10, 2 );
			add_action( 'woocommerce_coupon_options', array( $this, 'new_coupon_data_field' ), 10, 2 );
			add_action( 'woocommerce_coupon_options_save', array( $this, 'shipping_coupon_save' ), 10, 2 );

			// Let 3rd parties unhook the above via this hook.
			do_action( 'pok_hooks_admin', $this );
		}
	}

	/**
	 * Custom address fields on edit user
	 *
	 * @param  array $fields Address fields.
	 * @return array         Address fields.
	 */
	public function custom_user_address_fields( $fields ) {
		$screen = get_current_screen();
		if ( 'profile' === $screen->base ) {
			$user_id = get_current_user_id();
		} else {
			$user_id = isset( $_GET['user_id'] ) ? intval( $_GET['user_id'] ) : 0; // WPCS: Input var okay, CSRF okay.
		}
		$billing_country    = get_user_meta( $user_id, 'billing_country', true );
		$billing_state      = $this->helper->get_address_id_from_user( $user_id, 'billing_state' );
		$billing_city       = $this->helper->get_address_id_from_user( $user_id, 'billing_city' );
		$shipping_country   = get_user_meta( $user_id, 'shipping_country', true );
		$shipping_state     = $this->helper->get_address_id_from_user( $user_id, 'shipping_state' );
		$shipping_city      = $this->helper->get_address_id_from_user( $user_id, 'shipping_city' );
		$provinces          = $this->core->get_province();
		$billing_cities     = ( $cities = $this->core->get_city( $billing_state ) ) ? $cities : array();
		$shipping_cities    = ( $cities = $this->core->get_city( $shipping_state ) ) ? $cities : array();
		$billing_districts  = ( $districts = $this->core->get_district( $billing_city ) ) ? $districts : array();
		$shipping_districts = ( $districts = $this->core->get_district( $shipping_city ) ) ? $districts : array();
		if ( 'ID' === $billing_country ) {
			$custom_fields['billing'] = array(
				'title'  => __( 'Customer billing address', 'pok' ),
				'fields' => array(
					'billing_first_name' => array(
						'label'       => __( 'First name', 'pok' ),
						'description' => '',
					),
					'billing_last_name'  => array(
						'label'       => __( 'Last name', 'pok' ),
						'description' => '',
					),
					'billing_company'    => array(
						'label'       => __( 'Company', 'pok' ),
						'description' => '',
					),
					'billing_address_1'  => array(
						'label'       => __( 'Address line 1', 'pok' ),
						'description' => '',
					),
					'billing_address_2'  => array(
						'label'       => __( 'Address line 2', 'pok' ),
						'description' => '',
					),
					'billing_country' => array(
						'label'       => __( 'Country', 'pok' ),
						'description' => '',
						'class'       => 'js_field-country',
						'type'        => 'select',
						'options'     => array( __( 'Select a country&hellip;', 'pok' ) ) + WC()->countries->get_allowed_countries(),
					),
					'billing_state'   => array(
						'label'       => __( 'Province', 'pok' ),
						'description' => '',
						'class'       => 'select select2',
						'type'        => 'select',
						'options'     => array( __( 'Select Province', 'pok' ) ) + $provinces,
					),
					'billing_city'    => array(
						'label'       => __( 'City', 'pok' ),
						'type'        => 'select',
						'class'       => 'select select2',
						'options'     => array( __( 'Select City', 'pok' ) ) + $billing_cities,
						'description' => '',
					),
					'billing_district'    => array(
						'label'       => __( 'District', 'pok' ),
						'type'        => 'select',
						'class'       => 'select select2',
						'options'     => array( __( 'Select District', 'pok' ) ) + $billing_districts,
						'description' => '',
					),
					'billing_postcode'   => array(
						'label'       => __( 'Postcode / ZIP', 'pok' ),
						'description' => '',
					),
					'billing_phone'      => array(
						'label'       => __( 'Phone', 'pok' ),
						'description' => '',
					),
					'billing_email'      => array(
						'label'       => __( 'Email address', 'pok' ),
						'description' => '',
					),
				),
			);
			if ( 'default' === $this->helper->get_license_type() ) {
				unset( $custom_fields['billing']['district'] );
			}
		} else {
			$custom_fields['billing'] = $fields['billing'];
		}
		if ( 'ID' === $shipping_country ) {
			$custom_fields['shipping'] = array(
				'title'  => __( 'Customer shipping address', 'pok' ),
				'fields' => array(
					'shipping_first_name' => array(
						'label'       => __( 'First name', 'pok' ),
						'description' => '',
					),
					'shipping_last_name'  => array(
						'label'       => __( 'Last name', 'pok' ),
						'description' => '',
					),
					'shipping_company'    => array(
						'label'       => __( 'Company', 'pok' ),
						'description' => '',
					),
					'shipping_address_1'  => array(
						'label'       => __( 'Address line 1', 'pok' ),
						'description' => '',
					),
					'shipping_address_2'  => array(
						'label'       => __( 'Address line 2', 'pok' ),
						'description' => '',
					),
					'shipping_country'    => array(
						'label'       => __( 'Country', 'pok' ),
						'description' => '',
						'class'       => 'js_field-country',
						'type'        => 'select',
						'options'     => array( __( 'Select a country&hellip;', 'pok' ) ) + WC()->countries->get_allowed_countries(),
					),
					'shipping_state'      => array(
						'label'       => __( 'State / County', 'pok' ),
						'description' => '',
						'class'       => 'select select2',
						'type'        => 'select',
						'options'     => array( __( 'Select Province', 'pok' ) ) + $provinces,
					),
					'shipping_city'    => array(
						'label'       => __( 'City', 'pok' ),
						'type'        => 'select',
						'class'       => 'select select2',
						'options'     => array( __( 'Select City', 'pok' ) ) + $billing_cities,
						'description' => '',
					),
					'shipping_district'    => array(
						'label'       => __( 'District', 'pok' ),
						'type'        => 'select',
						'class'       => 'select select2',
						'options'     => array( __( 'Select District', 'pok' ) ) + $billing_districts,
						'description' => '',
					),
					'shipping_postcode'   => array(
						'label'       => __( 'Postcode / ZIP', 'pok' ),
						'description' => '',
					),
				),
			);
			if ( 'default' === $this->helper->get_license_type() ) {
				unset( $custom_fields['shipping']['district'] );
			}
		} else {
			$custom_fields['shipping'] = $fields['shipping'];
		}
		return $custom_fields;
	}

	/**
	 * Save user address data
	 *
	 * @param  integer $user_id       User ID.
	 * @param  array   $old_user_data Old user data.
	 */
	public function save_user_address( $user_id, $old_user_data ) {
		if ( isset( $_POST['billing_country'] ) && 'ID' === $_POST['billing_country'] ) {
			if ( isset( $_POST['billing_state'] ) ) {
				$province = $this->core->get_single_province( intval( $_POST['billing_state'] ) );
				update_user_meta( $user_id, 'billing_state', ( isset( $province ) && ! empty( $province ) ? $province : $_POST['billing_state'] ) );
				update_user_meta( $user_id, 'billing_state_id', intval( $_POST['billing_state'] ) );
			}
			if ( isset( $_POST['billing_city'] ) ) {
				$city = $this->core->get_single_city_without_province( intval( $_POST['billing_city'] ) );
				update_user_meta( $user_id, 'billing_city', ( isset( $city ) && ! empty( $city ) ? $city : $_POST['billing_city'] ) );
				update_user_meta( $user_id, 'billing_city_id', intval( $_POST['billing_city'] ) );
				if ( isset( $_POST['billing_district'] ) ) {
					$district = $this->core->get_single_district( intval( $_POST['billing_city'] ), intval( $_POST['billing_district'] ) );
					update_user_meta( $user_id, 'billing_district', ( isset( $district ) && ! empty( $district ) ? $district : $_POST['billing_district'] ) );
					update_user_meta( $user_id, 'billing_district_id', intval( $_POST['billing_district'] ) );
				}
			}
		}
		if ( isset( $_POST['billing_country'] ) && 'ID' === $_POST['shipping_country'] ) {
			if ( isset( $_POST['shipping_state'] ) ) {
				$province = $this->core->get_single_province( intval( $_POST['shipping_state'] ) );
				update_user_meta( $user_id, 'shipping_state', ( isset( $province ) && ! empty( $province ) ? $province : $_POST['shipping_state'] ) );
				update_user_meta( $user_id, 'shipping_state_id', intval( $_POST['shipping_state'] ) );
			}
			if ( isset( $_POST['shipping_city'] ) ) {
				$city = $this->core->get_single_city_without_province( intval( $_POST['shipping_city'] ) );
				update_user_meta( $user_id, 'shipping_city', ( isset( $city ) && ! empty( $city ) ? $city : $_POST['shipping_city'] ) );
				update_user_meta( $user_id, 'shipping_city_id', intval( $_POST['shipping_city'] ) );
				if ( isset( $_POST['shipping_district'] ) ) {
					$district = $this->core->get_single_district( intval( $_POST['shipping_city'] ), intval( $_POST['shipping_district'] ) );
					update_user_meta( $user_id, 'shipping_district', ( isset( $district ) && ! empty( $district ) ? $district : $_POST['shipping_district'] ) );
					update_user_meta( $user_id, 'shipping_district_id', intval( $_POST['shipping_district'] ) );
				}
			}
		}
	}

	/**
	 * Custom admin billing fields
	 *
	 * @param  array $fields Billing fields.
	 * @return array         Custom billing fields.
	 */
	public function custom_admin_billing_fields( $fields ) {
		return $this->custom_admin_fields( 'billing', $fields );
	}

	/**
	 * Custom admin shipping fields
	 *
	 * @param  array $fields Shipping fields.
	 * @return array         Custom shipping fields.
	 */
	public function custom_admin_shipping_fields( $fields ) {
		return $this->custom_admin_fields( 'shipping', $fields );
	}

	/**
	 * Custom admin fields
	 *
	 * @param  string $context Context.
	 * @param  array  $fields  Original fields.
	 * @return array           Custom fields.
	 */
	private function custom_admin_fields( $context = 'billing', $fields ) {
		global $thepostid, $post;
		$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
		$custom_fields = array(
			'first_name'    => $fields['first_name'],
			'last_name'     => $fields['last_name'],
			'company'       => $fields['company'],
			'country'       => $fields['country'],
			'state'         => array(
				'label' => __( 'State', 'pok' ),
				'class' => 'js_field-state select',
				'show'  => false,
			),
			'city'          => array(
				'label'     => __( 'City', 'pok' ),
				'class'     => 'select select2',
				'type'      => 'select',
				'options'   => array( '' => __( 'Select City', 'pok' ) ),
				'show'      => false,
			),
			'district'      => array(
				'label'     => __( 'District', 'pok' ),
				'class'     => 'select select2',
				'type'      => 'select',
				'options'   => array( '' => __( 'Select District', 'pok' ) ),
				'show'      => false,
			),
			'address_1'     => $fields['address_1'],
			'address_2'     => $fields['address_2'],
			'postcode'      => array(
				'label' => __( 'Postcode', 'pok' ),
				'show'  => false,
			),
		);
		if ( 'default' === $this->helper->get_license_type() ) {
			unset( $custom_fields['district'] );
		}
		if ( 'billing' === $context ) {
			$custom_fields['phone'] = $fields['phone'];
			$custom_fields['email'] = $fields['email'];
		} else {
			$custom_fields['phone'] = array(
				'label' => __( 'Phone', 'woocommerce' ),
			);
			$custom_fields['email'] = array(
				'label' => __( 'Email address', 'woocommerce' ),
			);
		}
		$state = $this->helper->get_address_id_from_order( $thepostid, $context . '_state' );
		$cities = $this->core->get_city( $state );
		if ( is_array( $cities ) ) {
			foreach ( $cities as $city_id => $city ) {
				$custom_fields['city']['options'][ $city_id ] = $city;
			}
		}
		$city = $this->helper->get_address_id_from_order( $thepostid, $context . '_city' );
		$custom_fields['city']['value'] = $city;
		$districts = $this->core->get_district( $city );
		if ( is_array( $districts ) ) {
			$custom_fields['district']['options'] = $districts;
		}
		return $custom_fields;
	}

	/**
	 * Load scripts
	 */
	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		if ( 'post' === $screen->base && 'shop_order' === $screen->post_type ) {
			global $thepostid, $post;
			$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
			wp_register_style( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css', array(), '4.0.5' );
			wp_enqueue_style( 'pok-order', POK_PLUGIN_URL . '/assets/css/order.css', array( 'select2', 'woocommerce_admin_styles' ), rand() );
			wp_enqueue_script( 'pok-order', POK_PLUGIN_URL . '/assets/js/order.js', array( 'jquery', 'select2' ), POK_VERSION, true );
			$localize = array(
				'labelFailedCity'       => __( 'Failed to load city list. Try again?', 'pok' ),
				'labelFailedDistrict'   => __( 'Failed to load district list. Try again?', 'pok' ),
				'labelSelectCity'       => __( 'Select City', 'pok' ),
				'labelLoadingCity'      => __( 'Loading city options...', 'pok' ),
				'labelSelectDistrict'   => __( 'Select District', 'pok' ),
				'labelLoadingDistrict'  => __( 'Loading district options...', 'pok' ),
				'labelNoDistrict'       => __( "You need to set customer's shipping district to get the costs", 'pok' ),
				'labelNoCity'           => __( "You need to set customer's shipping city to get the costs", 'pok' ),
				'labelSelectShipping'   => __( 'Select shipping service', 'pok' ),
				'labelOnlyIndonesia'    => __( 'Currently this feature only support shipping to Indonesia.', 'pok' ),
				'billing_country'       => $this->helper->get_address_id_from_order( $thepostid, 'billing_country' ),
				'shipping_country'      => $this->helper->get_address_id_from_order( $thepostid, 'shipping_country' ),
				'billing_state'         => $this->helper->get_address_id_from_order( $thepostid, 'billing_state' ),
				'shipping_state'        => $this->helper->get_address_id_from_order( $thepostid, 'shipping_state' ),
				'billing_city'          => $this->helper->get_address_id_from_order( $thepostid, 'billing_city' ),
				'shipping_city'         => $this->helper->get_address_id_from_order( $thepostid, 'shipping_city' ),
				'billing_district'      => $this->helper->get_address_id_from_order( $thepostid, 'billing_district' ),
				'shipping_district'     => $this->helper->get_address_id_from_order( $thepostid, 'shipping_district' ),
				'nonce_change_country'  => wp_create_nonce( 'change_country' ),
				'nonce_get_list_city'   => wp_create_nonce( 'get_list_city' ),
				'nonce_get_list_district' => wp_create_nonce( 'get_list_district' ),
				'enableDistrict'        => ( 'pro' === $this->helper->get_license_type() ? true : false ),
			);
			if ( 'ID' === $localize['billing_country'] ) {
				$localize['billing_state_options'] = $this->core->get_province();
				if ( 0 !== intval( $localize['billing_state'] ) ) {
					$localize['billing_city_options'] = $this->core->get_city( intval( $localize['billing_state'] ) );
				}
				if ( $localize['enableDistrict'] && 0 !== intval( $localize['billing_city'] ) ) {
					$localize['billing_district_options'] = $this->core->get_district( intval( $localize['billing_city'] ) );
				}
			}
			if ( 'ID' === $localize['shipping_country'] ) {
				$localize['shipping_state_options'] = $this->core->get_province();
				if ( 0 !== intval( $localize['shipping_state'] ) ) {
					$localize['shipping_city_options'] = $this->core->get_city( intval( $localize['shipping_state'] ) );
				}
				if ( $localize['enableDistrict'] && 0 !== intval( $localize['shipping_city'] ) ) {
					$localize['shipping_district_options'] = $this->core->get_district( intval( $localize['shipping_city'] ) );
				}
			}
			wp_localize_script( 'pok-order', 'pok_order_data', $localize );
			wp_localize_script(
				'pok-order', 'pok_nonces', array(
					'get_cost'              => wp_create_nonce( 'get_cost' ),
					'set_order_shipping'    => wp_create_nonce( 'set_order_shipping' ),
				)
			);
		} elseif ( 'profile' === $screen->base || 'user-edit' === $screen->base ) {
			if ( 'profile' === $screen->base ) {
				$user_id = get_current_user_id();
			} else {
				$user_id = intval( $_GET['user_id'] );
			}
			wp_enqueue_style( 'select2', POK_PLUGIN_URL . '/assets/css/select2.min.css', array() );
			wp_enqueue_script( 'pok-profile', POK_PLUGIN_URL . '/assets/js/profile.js', array( 'jquery', 'select2' ), POK_VERSION, true );
			$localize = array(
				'billing_country'       => get_user_meta( $user_id, 'billing_country', true ),
				'shipping_country'      => get_user_meta( $user_id, 'shipping_country', true ),
				'billing_state'         => $this->helper->get_address_id_from_user( $user_id, 'billing_state' ),
				'shipping_state'        => $this->helper->get_address_id_from_user( $user_id, 'shipping_state' ),
				'billing_city'          => $this->helper->get_address_id_from_user( $user_id, 'billing_city' ),
				'shipping_city'         => $this->helper->get_address_id_from_user( $user_id, 'shipping_city' ),
				'billing_district'      => $this->helper->get_address_id_from_user( $user_id, 'billing_district' ),
				'shipping_district'     => $this->helper->get_address_id_from_user( $user_id, 'shipping_district' ),
				'labelFailedCity'       => __( 'Failed to load city list. Try again?', 'pok' ),
				'labelFailedDistrict'   => __( 'Failed to load district list. Try again?', 'pok' ),
				'labelSelectCity'       => __( 'Select City', 'pok' ),
				'labelLoadingCity'      => __( 'Loading city options...', 'pok' ),
				'labelSelectDistrict'   => __( 'Select District', 'pok' ),
				'labelLoadingDistrict'  => __( 'Loading district options...', 'pok' ),
				'nonce_change_country'  => wp_create_nonce( 'change_country' ),
				'nonce_get_list_city'   => wp_create_nonce( 'get_list_city' ),
				'nonce_get_list_district' => wp_create_nonce( 'get_list_district' ),
				'enableDistrict'        => ( 'pro' === $this->helper->get_license_type() ? true : false ),
			);
			wp_localize_script( 'pok-profile', 'pok_profile_data', $localize );
		} elseif ( 'shop_coupon' === $screen->id ) {
			wp_enqueue_style( 'select2', POK_PLUGIN_URL . '/assets/css/select2.min.css', array() );
			wp_enqueue_style( 'pok-coupon', POK_PLUGIN_URL . '/assets/css/coupon.css', array( 'select2', 'woocommerce_admin_styles' ), POK_VERSION );
			wp_enqueue_script( 'pok-coupon', POK_PLUGIN_URL . '/assets/js/coupon.js', array( 'jquery', 'select2' ), POK_VERSION, true );
			wp_localize_script(
				'pok-coupon', 'pok_translations', array(
					'all_province'                  => __( 'All Province', 'pok' ),
					'all_city'                      => __( 'All City', 'pok' ),
					'all_district'                  => __( 'All District', 'pok' ),
					'delete'                        => __( 'Delete', 'pok' ),
					'add'                           => __( 'Add', 'pok' ),
					'select_city'                   => __( 'Select city', 'pok' ),
					'select_district'               => __( 'Select district', 'pok' ),
				)
			);
			wp_localize_script(
				'pok-coupon', 'pok_nonces', array(
					'get_list_city'             => wp_create_nonce( 'get_list_city' ),
					'get_list_district'         => wp_create_nonce( 'get_list_district' ),
				)
			);
			wp_localize_script(
				'pok-coupon', 'pok_data', array(
					'couriers' => $this->setting->get( 'couriers' ),
					'services' => $this->core->get_courier_service()
				)
			);
		}
	}

	/**
	 * Add add ongkir button
	 *
	 * @param object $order Order object.
	 */
	public function add_button_ongkir( $order ) {
		add_thickbox();
		?>
		<div id="pok-switch-shipping" style="display:none;width:300px;">
			<div class="pok-order-shipping-result">
				<div class="loading">
					<img src="<?php echo POK_PLUGIN_URL . '/assets/img/wpspin-2x.gif'; ?>" alt="loading">
				</div>
				<div class="results hidden">
					<table cellspacing="0" cellpadding="0">
						<thead>
							<th><?php esc_html_e( 'Courier', 'pok' ); ?></th>
							<th><?php esc_html_e( 'Service', 'pok' ); ?></th>
							<th><?php esc_html_e( 'Etd', 'pok' ); ?></th>
							<th><?php esc_html_e( 'Cost', 'pok' ); ?></th>
						</thead>
						<tbody></tbody>
					</table>
				</div>
				<div class="no-result hidden">
					<p><?php esc_html_e( 'No shipping service found or something wrong happens. Please check your setting.', 'pok' ); ?></p>
				</div>
			</div>
		</div>
		<a class="button add-order-ongkir" data-order-id="<?php echo esc_attr( $order->get_id() ); ?>"><?php esc_html_e( 'Add Shipping (Ongkir)', 'pok' ); ?></a>
		<?php
	}

	/**
	 * Add courier switch button
	 *
	 * @param integer $item_id Item ID.
	 * @param object  $item    Order item object.
	 */
	public function add_switch_button( $item_id, $item ) {
		if ( 'shipping' === $item->get_type() && $item->meta_exists( 'created_by_pok' ) ) {
			if ( $item->meta_exists( 'weight' ) ) {
				$weight = $item->get_meta( 'weight', true );
			} else {
				$order = wc_get_order( $item->get_order_id() );
				$weight = $this->helper->get_order_weight( $order );
			}
			$origin = apply_filters( 'pok_admin_switch_courier_origin', $this->setting->get( 'store_location' )[0], $item );
			?>
			<div class="pok-switch-courier"><a data-id="<?php echo esc_attr( $item_id ); ?>" data-order-id="<?php echo esc_attr( $item->get_order_id() ); ?>" data-weight="<?php echo esc_attr( $weight ); ?>" data-origin="<?php echo esc_attr( $origin ); ?>" class="switch-order-ongkir"><?php esc_html_e( 'Change Service', 'pok' ); ?></a></div>
			<?php
		}
	}

	/**
	 * Hide custom item meta from order
	 *
	 * @param  array $metas Item metas.
	 * @return array        Item metas.
	 */
	public function hide_item_meta( $metas ) {
		$metas[] = 'created_by_pok';
		$metas[] = 'courier';
		$metas[] = 'service';
		return $metas;
	}

	/**
	 * Show shipping weight on order
	 *
	 * @param integer $order_id Order ID.
	 */
	public function add_weight_info( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( $order->get_shipping_methods() ) {
			?>
			<tr>
				<td class="label"><?php esc_html_e( 'Shipping weight:', 'pok' ); ?></td>
				<td width="1%"></td>
				<td class="total">
					<span class="amount"><?php echo esc_html( $this->helper->get_order_weight( $order ) . ' kg' ); ?></span>
				</td>
			</tr>
			<?php
		}
	}

	/**
	 * Change meta keys
	 *
	 * @param  string $meta_key Original meta key.
	 * @param  object $meta     Meta object.
	 * @param  object $item     Item object.
	 * @return string           Changed meta key.
	 */
	public function change_meta_keys( $meta_key, $meta, $item ) {
		if ( 'etd' === $meta->key ) {
			$meta_key = __( 'Estimated', 'pok' );
		} elseif ( 'insurance' === $meta->key ) {
			$meta_key = __( 'Insurance fee', 'pok' );
		} elseif ( 'timber_packing' === $meta->key ) {
			$meta_key = __( 'Timber packing fee', 'pok' );
		} elseif ( 'markup' === $meta->key ) {
			$meta_key = __( 'Timber packing fee', 'pok' );
		} elseif ( 'weight' === $meta->key ) {
			$meta_key = __( 'Shipping weight', 'pok' );
		} elseif ( 'original_cost' === $meta->key ) {
			$meta_key = __( 'Shipping discount', 'pok' );
		}
		return $meta_key;
	}

	/**
	 * Change meta values
	 *
	 * @param  string $display_value Original value.
	 * @param  object $meta          Meta object.
	 * @param  object $item          Item object.
	 * @return string                Changed value.
	 */
	public function change_meta_values( $display_value, $meta, $item ) {
		if ( 'etd' === $meta->key ) {
			if ( ! empty( $meta->value ) ) {
				$display_value = $meta->value . ' ' . __( 'day(s)', 'pok' );
			} else {
				$display_value = '-';
			}
		} elseif ( 'insurance' === $meta->key ) {
			$display_value = wc_price( $meta->value );
		} elseif ( 'timber_packing' === $meta->key ) {
			$display_value = wc_price( $meta->value );
		} elseif ( 'markup' === $meta->key ) {
			$display_value = wc_price( $meta->value );
		} elseif ( 'weight' === $meta->key ) {
			$display_value = $meta->value . ' kg';
		} elseif ( 'original_cost' === $meta->key ) {
			$display_value = wc_price( floatval( $item->get_total() ) - floatval( $meta->value ) );
		}
		return $display_value;
	}

	/**
	 * Add district to customer data
	 *
	 * @param  array  $data     Customer data.
	 * @param  object $customer Customer object.
	 * @param  int    $user_id  User ID.
	 * @return array            Customer data.
	 */
	public function get_district_from_meta( $data, $customer, $user_id ) {
		$data['billing']['state']       = $this->helper->get_address_id_from_user( $user_id, 'billing_state' );
		$data['billing']['city']        = $this->helper->get_address_id_from_user( $user_id, 'billing_city' );
		$data['billing']['district']    = $this->helper->get_address_id_from_user( $user_id, 'billing_district' );
		$data['shipping']['state']      = $this->helper->get_address_id_from_user( $user_id, 'shipping_state' );
		$data['shipping']['city']       = $this->helper->get_address_id_from_user( $user_id, 'shipping_city' );
		$data['shipping']['district']   = $this->helper->get_address_id_from_user( $user_id, 'shipping_district' );
		if ( 'ID' === $data['billing']['country'] ) {
			$data['billing']['state_options'] = $this->core->get_province();
			if ( 0 !== intval( $data['billing']['state'] ) ) {
				$data['billing']['city_options'] = $this->core->get_city( intval( $data['billing']['state'] ) );
			}
			if ( 'pro' === $this->helper->get_license_type() && 0 !== intval( $data['billing']['city'] ) ) {
				$data['billing']['district_options'] = $this->core->get_district( intval( $data['billing']['city'] ) );
			}
		}
		if ( 'ID' === $data['shipping']['country'] ) {
			$data['shipping']['state_options'] = $this->core->get_province();
			if ( 0 !== intval( $data['shipping']['state'] ) ) {
				$data['shipping']['city_options'] = $this->core->get_city( intval( $data['shipping']['state'] ) );
			}
			if ( 'pro' === $this->helper->get_license_type() && 0 !== intval( $data['shipping']['city'] ) ) {
				$data['shipping']['district_options'] = $this->core->get_district( intval( $data['shipping']['city'] ) );
			}
		}
		return $data;
	}

	/**
	 * Handle save address data to order meta
	 *
	 * @param  integer $order_id Order ID.
	 * @param  array   $data     Order data.
	 */
	public function save_addresses_meta( $order_id, $data ) {
		if ( isset( $_POST['_billing_country'] ) && 'ID' === sanitize_text_field( wp_unslash( $_POST['_billing_country'] ) ) ) { // WPCS: Input var okay. CSRF okay.
			if ( isset( $_POST['_billing_state'] ) ) { // WPCS: Input var okay. CSRF okay.
				if ( 0 !== intval( $_POST['_billing_state'] ) ) { // WPCS: Input var okay. CSRF okay.
					$province = $this->core->get_single_province( intval( $_POST['_billing_state'] ) ); // WPCS: Input var okay. CSRF okay.
					update_post_meta( $order_id, '_billing_state', ( isset( $province ) && ! empty( $province ) ? $province : sanitize_text_field( wp_unslash( $_POST['_billing_state'] ) ) ) ); // WPCS: Input var okay. CSRF okay.
					update_post_meta( $order_id, '_billing_state_id', sanitize_text_field( wp_unslash( $_POST['_billing_state'] ) ) ); // WPCS: Input var okay. CSRF okay.
				}
			}
			if ( isset( $_POST['_billing_city'] ) ) { // WPCS: Input var okay. CSRF okay.
				if ( 0 !== intval( $_POST['_billing_city'] ) ) { // WPCS: Input var okay. CSRF okay.
					$city = $this->core->get_single_city_without_province( intval( $_POST['_billing_city'] ) ); // WPCS: Input var okay. CSRF okay.
					update_post_meta( $order_id, '_billing_city', ( isset( $city ) && ! empty( $city ) ? $city : sanitize_text_field( wp_unslash( $_POST['_billing_city'] ) ) ) ); // WPCS: Input var okay. CSRF okay.
					update_post_meta( $order_id, '_billing_city_id', sanitize_text_field( wp_unslash( $_POST['_billing_city'] ) ) ); // WPCS: Input var okay. CSRF okay.
				}
			}
			if ( isset( $_POST['_billing_district'] ) ) { // WPCS: Input var okay. CSRF okay.
				if ( 0 !== intval( $_POST['_billing_district'] ) ) { // WPCS: Input var okay. CSRF okay.
					$district = $this->core->get_single_district( intval( $_POST['_billing_city'] ), intval( $_POST['_billing_district'] ) ); // WPCS: Input var okay. CSRF okay.
					update_post_meta( $order_id, '_billing_district', ( isset( $district ) && ! empty( $district ) ? $district : sanitize_text_field( wp_unslash( $_POST['_billing_district'] ) ) ) ); // WPCS: Input var okay. CSRF okay.
					update_post_meta( $order_id, '_billing_district_id', sanitize_text_field( wp_unslash( $_POST['_billing_district'] ) ) ); // WPCS: Input var okay. CSRF okay.
				}
			}
		}
		if ( isset( $_POST['_shipping_country'] ) && 'ID' === sanitize_text_field( wp_unslash( $_POST['_shipping_country'] ) ) ) { // WPCS: Input var okay. CSRF okay.
			if ( isset( $_POST['_shipping_state'] ) ) { // WPCS: Input var okay. CSRF okay.
				if ( 0 !== intval( $_POST['_shipping_state'] ) ) { // WPCS: Input var okay. CSRF okay.
					$province = $this->core->get_single_province( intval( $_POST['_shipping_state'] ) ); // WPCS: Input var okay. CSRF okay.
					update_post_meta( $order_id, '_shipping_state', ( isset( $province ) && ! empty( $province ) ? $province : sanitize_text_field( wp_unslash( $_POST['_shipping_state'] ) ) ) ); // WPCS: Input var okay. CSRF okay.
					update_post_meta( $order_id, '_shipping_state_id', sanitize_text_field( wp_unslash( $_POST['_shipping_state'] ) ) ); // WPCS: Input var okay. CSRF okay.
				}
			}
			if ( isset( $_POST['_shipping_city'] ) ) { // WPCS: Input var okay. CSRF okay.
				if ( 0 !== intval( $_POST['_shipping_city'] ) ) { // WPCS: Input var okay. CSRF okay.
					$city = $this->core->get_single_city_without_province( intval( $_POST['_shipping_city'] ) ); // WPCS: Input var okay. CSRF okay.
					update_post_meta( $order_id, '_shipping_city', ( isset( $city ) && ! empty( $city ) ? $city : sanitize_text_field( wp_unslash( $_POST['_shipping_city'] ) ) ) ); // WPCS: Input var okay. CSRF okay.
					update_post_meta( $order_id, '_shipping_city_id', sanitize_text_field( wp_unslash( $_POST['_shipping_city'] ) ) ); // WPCS: Input var okay. CSRF okay.
				}
			}
			if ( isset( $_POST['_shipping_district'] ) ) { // WPCS: Input var okay. CSRF okay.
				if ( 0 !== intval( $_POST['_shipping_district'] ) ) { // WPCS: Input var okay. CSRF okay.
					$district = $this->core->get_single_district( intval( $_POST['_shipping_city'] ), intval( $_POST['_shipping_district'] ) ); // WPCS: Input var okay. CSRF okay.
					update_post_meta( $order_id, '_shipping_district', ( isset( $district ) && ! empty( $district ) ? $district : sanitize_text_field( wp_unslash( $_POST['_shipping_district'] ) ) ) ); // WPCS: Input var okay. CSRF okay.
					update_post_meta( $order_id, '_shipping_district_id', sanitize_text_field( wp_unslash( $_POST['_shipping_district'] ) ) ); // WPCS: Input var okay. CSRF okay.
				}
			}
		}
	}

	/**
	 * Change Google Maps URL on orders table
	 * 
	 * @param  array $address Old address parts.
	 * @param  object $order   WC_Order object.
	 * @return array          New address parts.
	 */
	public function change_address_map_url( $address, $order ) {
		$order_id = $order->get_id();
		if ( '' !== ( $pok_api = get_post_meta( $order_id, '_pok_data_api', true ) ) ) {
			$address['state'] = get_post_meta( $order_id, '_shipping_state', true );
			$address['city'] = get_post_meta( $order_id, '_shipping_city', true );
			if ( isset( $address['district'] ) ) {
				$address['city'] = get_post_meta( $order_id, '_shipping_district', true ) . ', ' . $address['city'];
				unset( $address['district'] );
			}
		}
		return $address;
	}

	/**
	 * Register report tab
	 *
	 * @param  array $tabs Report tabs.
	 * @return array       Report tabs.
	 */
	public function shipping_report( $tabs ) {
		$tabs['pok_shipping'] = array(
			'title'   => __( 'Shipping Couriers', 'pok' ),
			'reports' => array(
				'all' => array(
					'title'       => __( 'All Courier', 'pok' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( $this, 'get_report' ),
				),
			),
		);
		foreach ( $this->core->get_courier( $this->setting->get( 'base_api' ), $this->helper->get_license_type() ) as $courier ) {
			$tabs['pok_shipping']['reports'][ $courier ] = array(
				'title'       => $this->helper->get_courier_name( $courier ),
				'description' => '',
				'hide_title'  => true,
				'callback'    => array( $this, 'get_report' ),
			);
		}
		return $tabs;
	}

	/**
	 * Display report
	 *
	 * @param  string $courier Courier name.
	 */
	public function get_report( $courier ) {
		$courier = in_array( $courier, $this->core->get_courier( $this->setting->get('base_api'), $this->helper->get_license_type() ), true ) ? $courier : 'all';
		$report = new POK_Report_Table( $courier );
		$report->output_report();
	}

	/**
	 * Add new coupon types
	 * 
	 * @param  array $coupon_types Coupon types.
	 * @return array               Coupon types.
	 */
	public function new_coupon_types( $coupon_types ) {
		$coupon_types['ongkir'] = __( 'Shipping discount (by Plugin Ongkos Kirim)', 'pok' );
		return $coupon_types;
	}

	/**
	 * Add new coupon restriction tab
	 * 
	 * @param  array $tabs Coupon data tab.
	 * @return array       Coupon data tab.
	 */
	public function coupon_shipping_restriction_tab( $tabs ) {
		$new_tabs = array_slice( $tabs, 0, 1, true ) +
			array( "shipping_restriction" => array(
				'label'  => __( 'Shipping restriction', 'pok' ),
				'target' => 'shipping_restriction_coupon_data',
				'class'  => 'tab-shipping-restriction',
			) ) +
			array_slice( $tabs, 1, count($tabs) - 1, true );
		return $new_tabs;
	}

	/**
	 * Add new coupon restriction panel
	 * 
	 * @param  integer $coupon_id Coupon ID.
	 * @param  object  $coupon    Coupon data.
	 */
	public function coupon_shipping_restriction_panel( $coupon_id, $coupon ) {
		$ship_res 		= get_post_meta( $coupon_id, 'shipping_restriction', true );
		$res_couriers 	= isset( $ship_res['courier'] ) ? $ship_res['courier'] : array();
		$res_services 	= isset( $ship_res['service'] ) ? $ship_res['service'] : array();
		$couriers 		= $this->setting->get( 'couriers' );
		$services 		= $this->core->get_courier_service();
		$provinces  	= $this->core->get_province();
		?>
		<div id="shipping_restriction_coupon_data" class="panel woocommerce_options_panel">
			<div class="options_group">
				<p class="form-field">
					<label for="shipping_restriction_min_weight"><?php _e( 'Minimum weight', 'pok' ); ?> (<?php echo esc_attr( get_option('woocommerce_weight_unit') ) ?>)</label>
					<input type="number" min="0" id="shipping_restriction_min_weight" name="shipping_restriction[min_weight]" style="width: 50%;" data-placeholder="<?php esc_attr_e( 'Minimum shipping weight', 'pok' ); ?>" value="<?php echo isset( $ship_res['min_weight'] ) ? esc_attr( $ship_res['min_weight'] ) : '' ?>">
					<?php echo wc_help_tip( __( 'Left this field empty if the coupon does not need a minimum shipping weight.', 'pok' ) ); ?>
				</p>
				<p class="form-field">
					<label for="shipping_restriction_max_weight"><?php _e( 'Maximum weight', 'pok' ); ?> (<?php echo esc_attr( get_option('woocommerce_weight_unit') ) ?>)</label>
					<input type="number" min="0" id="shipping_restriction_max_weight" name="shipping_restriction[max_weight]" style="width: 50%;" data-placeholder="<?php esc_attr_e( 'Maximum shipping weight', 'pok' ); ?>" value="<?php echo isset( $ship_res['min_weight'] ) ? esc_attr( $ship_res['max_weight'] ) : '' ?>">
					<?php echo wc_help_tip( __( 'Left this field empty if the coupon does not need a maximum shipping weight.', 'pok' ) ); ?>
				</p>
				<p class="form-field">
					<label for="shipping_restriction_courier"><?php _e( 'Couriers', 'pok' ); ?></label>
					<select id="shipping_restriction_courier" name="shipping_restriction[courier][]" style="width: 90%;"  class="init-select2" multiple="multiple" data-placeholder="<?php esc_attr_e( 'All couriers', 'pok' ); ?>">
						<?php
						foreach ( $couriers as $courier ) {
							echo '<option value="' . esc_attr( $courier ) . '"' . ( in_array( $courier, $res_couriers, true ) ? 'selected' : '' ) . '>' . esc_html( $this->helper->get_courier_name( $courier ) ) . '</option>';
						}
						?>
					</select>
					<?php echo wc_help_tip( __( 'Left this field empty if the coupon is valid for all shipping couriers.', 'pok' ) ); ?>
				</p>
				<p class="form-field">
					<label for="shipping_restriction_service"><?php _e( 'Services', 'pok' ); ?></label>
					<select id="shipping_restriction_service" name="shipping_restriction[service][]" style="width: 90%;"  class="init-select2" multiple="multiple" data-placeholder="<?php esc_attr_e( 'All services', 'pok' ); ?>">
						<?php
						foreach ( $services as $cou => $ser ) {
							if ( empty( $res_couriers ) || in_array( $cou, $res_couriers ) ) {
								foreach ( $ser as $ser_slug => $service ) {
									echo '<option value="' . esc_attr( $cou . '-' . $ser_slug ) . '"' . ( in_array( $cou . '-' . $ser_slug, $res_services, true ) ? 'selected' : '' ) . '>' . esc_html( $this->helper->get_courier_name( $cou ) ) . ' - ' . $service['long'] . '</option>';
								}
							}
						}
						?>
					</select>
				</p>
				<div class="form-field">
					<label for="shipping_restriction_destination"><?php _e( 'Destination', 'pok' ); ?></label>
					<div class="pok-coupon-destination">
						<table class="">
							<tbody>
								<tr class="repeater">
									<td>
										<select class="select_province">
											<option value=""><?php esc_html_e( 'All Province', 'pok' ); ?></option>
											<?php if ( ! empty( $provinces ) ) : ?>
												<?php foreach ( $provinces as $province_id => $province ) : ?>
													<option value="<?php echo esc_attr( $province_id ); ?>"><?php echo esc_html( $province ); ?></option>
												<?php endforeach; ?>
											<?php endif; ?>
										</select>
									</td>
									<td>
										<select class="select_city">
											<option value=""><?php esc_html_e( 'All City', 'pok' ); ?></option>
										</select>
									</td>
									<td>
										<select class="select_district">
											<option value=""><?php esc_html_e( 'All District', 'pok' ); ?></option>
										</select>
									</td>
									<td style="width: 1%;">
										<a class="button remove-manual"><?php esc_html_e( 'Delete', 'pok' ); ?></a>
									</td>
								</tr>
								<?php if ( isset( $ship_res['destination'] ) ) : ?>
									<?php foreach ( $ship_res['destination'] as $key => $destination ) : ?>
										<tr class="base">
											<td>
												<select class="select_province" name="shipping_restriction[destination][<?php echo esc_attr( $key ) ?>][province]">
													<option value=""><?php esc_html_e( 'All Province', 'pok' ); ?></option>
													<?php if ( ! empty( $provinces ) ) : ?>
														<?php foreach ( $provinces as $province_id => $province ) : ?>
															<option <?php echo isset( $destination['province'] ) && $province_id == $destination['province'] ? 'selected' : '' ?> value="<?php echo esc_attr( $province_id ); ?>"><?php echo esc_html( $province ); ?></option>
														<?php endforeach; ?>
													<?php endif; ?>
												</select>
											</td>
											<td>
												<select class="select_city" name="shipping_restriction[destination][<?php echo esc_attr( $key ) ?>][city]">
													<option value=""><?php esc_html_e( 'All City', 'pok' ); ?></option>
													<?php
														if ( isset( $destination['province'] ) && in_array( $destination['province'], array_keys( $provinces ) ) ) {
															$cities 	= $this->core->get_city( $destination['province'] );
														} else {
															$cities 	= array();
														}
													?>
													<?php if ( ! empty( $cities ) ) : ?>
														<?php foreach ( $cities as $city_id => $city ) : ?>
															<option <?php echo isset( $destination['city'] ) && $city_id == $destination['city'] ? 'selected' : '' ?> value="<?php echo esc_attr( $city_id ); ?>"><?php echo esc_html( $city ); ?></option>
														<?php endforeach; ?>
													<?php endif; ?>
												</select>
											</td>
											<td>
												<select class="select_district" name="shipping_restriction[destination][<?php echo esc_attr( $key ) ?>][district]">
													<option value=""><?php esc_html_e( 'All District', 'pok' ); ?></option>
													<?php
														if ( 'pro' === $this->helper->get_license_type() && isset( $destination['city'] ) && in_array( $destination['city'], array_keys( $cities ) ) ) {
															$districts 	= $this->core->get_district( $destination['city'] );
														} else {
															$districts 	= array();
														}
													?>
													<?php if ( ! empty( $districts ) ) : ?>
														<?php foreach ( $districts as $district_id => $district ) : ?>
															<option <?php echo isset( $destination['district'] ) && $district_id == $destination['district'] ? 'selected' : '' ?> value="<?php echo esc_attr( $district_id ); ?>"><?php echo esc_html( $district ); ?></option>
														<?php endforeach; ?>
													<?php endif; ?>
												</select>
											</td>
											<td style="width: 1%;">
												<a class="button remove-manual"><?php esc_html_e( 'Delete', 'pok' ); ?></a>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php else: ?>
									<tr class="base">
										<td>
											<select class="select_province" name="shipping_restriction[destination][0][province]">
												<option value=""><?php esc_html_e( 'All Province', 'pok' ); ?></option>
												<?php if ( ! empty( $provinces ) ) : ?>
													<?php foreach ( $provinces as $province_id => $province ) : ?>
														<option value="<?php echo esc_attr( $province_id ); ?>"><?php echo esc_html( $province ); ?></option>
													<?php endforeach; ?>
												<?php endif; ?>
											</select>
										</td>
										<td>
											<select class="select_city" name="shipping_restriction[destination][0][city]">
												<option value=""><?php esc_html_e( 'All City', 'pok' ); ?></option>
											</select>
										</td>
										<td>
											<select class="select_district" name="shipping_restriction[destination][0][district]">
												<option value=""><?php esc_html_e( 'All District', 'pok' ); ?></option>
											</select>
										</td>
										<td style="width: 1%;">
											<a class="button remove-manual"><?php esc_html_e( 'Delete', 'pok' ); ?></a>
										</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
						<button class="add-destination button" type="button"><?php esc_html_e( 'Add Destination', 'pok' ) ?></button>
					</div>
					<?php
						echo wc_help_tip( __( 'Specify the shipping destination where the discount should apply.', 'pok' ) );
					?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Create new field to coupon data
	 * 
	 * @param  integer $coupon_id Coupon ID.
	 * @param  object  $coupon    Coupon data.
	 */
	public function new_coupon_data_field( $coupon_id, $coupon ) {
		woocommerce_wp_select(
			array(
				'id'      => 'shipping_discount_type',
				'label'   => __( 'Shipping discount type', 'pok' ),
				'options' => array(
					'free'		=> __( 'Free shipping', 'pok' ),
					'fixed'		=> __( 'Fixed discount', 'pok' ),
					'percent'	=> __( 'Percentage discount', 'pok' )
				),
				'value'   => get_post_meta( $coupon_id, 'shipping_discount_type', true ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'          => 'shipping_discount_amount',
				'label'       => __( 'Discount amount', 'pok' ),
				'placeholder' => wc_format_localized_price( 0 ),
				'description' => __( 'Value of the coupon.', 'pok' ),
				'data_type'   => 'price',
				'desc_tip'    => true,
				'value'       => get_post_meta( $coupon_id, 'shipping_discount_amount', true ),
			)
		);
	}

	/**
	 * Handle save coupon
	 * 
	 * @param  integer $coupon_id Coupon ID.
	 * @param  object  $coupon    Coupon data.
	 */
	public function shipping_coupon_save( $coupon_id, $coupon ) {
		if ( $coupon->is_type( 'ongkir' ) ) {
			if ( isset( $_POST['shipping_discount_type'] ) && ! empty( $_POST['shipping_discount_type'] ) ) {
				update_post_meta( $coupon_id, 'shipping_discount_type', sanitize_text_field( wp_unslash( $_POST['shipping_discount_type'] ) ) );
			}
			if ( isset( $_POST['shipping_discount_amount'] ) && ! empty( $_POST['shipping_discount_amount'] ) ) {
				update_post_meta( $coupon_id, 'shipping_discount_amount', floatval( $_POST['shipping_discount_amount'] ) );
			}
			if ( isset( $_POST['shipping_restriction'] ) ) {
				$input = $_POST['shipping_restriction'];
				$coupon_data = array(
					'min_weight'	=> isset( $input['min_weight'] ) && is_numeric( $input['min_weight'] ) ? floatval( $input['min_weight'] ) : '',
					'max_weight'	=> isset( $input['max_weight'] ) && is_numeric( $input['max_weight'] ) ? floatval( $input['max_weight'] ) : '',
					'courier'		=> isset( $input['courier'] ) && ! empty( $input['courier'] ) ? $input['courier'] : array(),
					'service'		=> isset( $input['service'] ) && ! empty( $input['service'] ) ? $input['service'] : array(),
					'destination'	=> isset( $input['destination'] ) && ! empty( $input['destination'] ) ? $input['destination'] : array(),
				);
				update_post_meta( $coupon_id, 'shipping_restriction', $coupon_data );
			}
			$coupon->set_amount( 0 );
			$coupon->save();
		}
	}

}
