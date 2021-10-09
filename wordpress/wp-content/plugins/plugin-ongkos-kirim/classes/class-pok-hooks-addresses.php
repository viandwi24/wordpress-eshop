<?php

/**
 * Customized checkout fields
 */
class POK_Hooks_Addresses {

	/**
	 * POK Core
	 *
	 * @var object
	 */
	protected $core;

	/**
	 * POK Setting
	 *
	 * @var object
	 */
	protected $setting;

	/**
	 * POK Helper
	 *
	 * @var object
	 */
	protected $helper;

	/**
	 * Field order
	 *
	 * @var array
	 */
	protected $field_order;

	/**
	 * Constructor
	 */
	public function __construct() {
		global $pok_helper;
		global $pok_core;
		$this->core     = $pok_core;
		$this->setting  = new POK_Setting();
		$this->helper   = $pok_helper;
		$this->field_order = apply_filters(
			'pok_fields_priority', array(
				'first_name'    => 10,
				'last_name'     => 20,
				'company'       => 30,
				'country'       => 40,
				'state'         => 50,
				'city'          => 60,
				'district'      => 70,
				'simple_address' => 70,
				'address_1'     => 80,
				'address_2'     => 90,
				'postcode'      => 100,
				'phone'         => 110,
				'email'         => 120,
				'insurance'		=> 9999
			)
		);

		if ( $this->helper->is_plugin_active() ) {
			// checkout.
			add_filter( 'woocommerce_states', array( $this, 'set_provinces' ) );
			add_filter( 'woocommerce_checkout_fields', array( $this, 'custom_checkout_fields' ), 30 );
			add_filter( 'woocommerce_billing_fields', array( $this, 'custom_billing_fields' ), 40 );
			add_filter( 'woocommerce_shipping_fields', array( $this, 'custom_shipping_fields' ), 40 );
			add_filter( 'woocommerce_default_address_fields', array( $this, 'custom_special_checkout_fields' ), 40 );
			add_filter( 'woocommerce_get_country_locale', array( $this, 'country_locale' ), 30 );
			add_filter( 'woocommerce_checkout_get_value', array( $this, 'set_default_checkout_value' ), 10, 2 );
			add_action( 'woocommerce_checkout_process', array( $this, 'validate_district' ) );
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'update_order_meta' ), 20, 2 );
			add_action( 'woocommerce_review_order_after_cart_contents', array( $this, 'show_additional_info_on_checkout' ) );
			add_action( 'woocommerce_checkout_update_order_review', array( $this, 'delete_wc_cache' ) );
			add_action( 'woocommerce_cart_calculate_fees', array( $this, 'add_custom_fee' ) );
			add_filter( 'woocommerce_cart_ready_to_calc_shipping', array( $this, 'remove_shipping_on_cart' ) );
			add_filter( 'woocommerce_update_order_review_fragments', array( $this, 'maybe_reload_checkout' ) );
			add_filter( 'woocommerce_cart_shipping_method_full_label', array( $this, 'custom_shipping_label' ), 10, 2 );
			add_filter( 'woocommerce_coupon_discount_amount_html', array( $this, 'change_coupon_discount_label' ), 10, 2 );
			add_filter( 'default_checkout_billing_state', array( $this, 'custom_checkout_billing_state' ), 10 );
			add_filter( 'default_checkout_shipping_state', array( $this, 'custom_checkout_shipping_state' ), 10 );
			add_filter( 'default_checkout_billing_country', array( $this, 'set_default_checkout_country' ) );
			add_filter( 'default_checkout_shipping_country', array( $this, 'set_default_checkout_country' ) );
			add_filter( 'woocommerce_customer_default_location_array', array( $this, 'set_default_customer_country' ) );

			// order.
			add_filter( 'woocommerce_localisation_address_formats', array( $this, 'custom_address_format' ) );
			add_filter( 'woocommerce_formatted_address_replacements', array( $this, 'custom_address_replacement' ), 30, 2 );
			add_action( 'woocommerce_get_order_address', array( $this, 'set_order_address_data' ), 10, 3 );

			// my account.
			add_filter( 'woocommerce_my_account_edit_address_field_value', array( $this, 'set_my_account_address_value' ), 10, 3 );
			add_filter( 'woocommerce_my_account_my_address_formatted_address', array( $this, 'format_myaccount_address' ), 10, 3 );
			add_action( 'woocommerce_customer_save_address', array( $this, 'update_customer_address' ), 10, 2 );

			// other.
			add_filter( 'woocommerce_shipping_settings', array( $this, 'modify_shipping_settings' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// Let 3rd parties unhook the above via this hook.
			do_action( 'pok_hooks_addresses', $this );
		}
	}

	/**
	 * Set custom provinces
	 *
	 * @param array $states WC States.
	 */
	public function set_provinces( $states ) {
		if ( is_admin() && function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( 'woocommerce_page_wc-settings' === $screen->id && ( ! isset( $_GET['tab'] ) || 'general' === sanitize_text_field( wp_unslash( $_GET['tab'] ) ) ) ) {
				return $states;
			}
		}
		$provinces = $this->core->get_province();
		if ( ! empty( $provinces ) ) {
			$states['ID'] = $provinces;
		} else {
			if ( function_exists( 'wc_add_notice' ) ) {
				wc_add_notice( __( 'Failed to load data. Please refresh the page.', 'pok' ), 'error' );
			}
		}
		return $states;
	}

	/**
	 * Custom checkout fields
	 *
	 * @param  array $fields Checkout fields.
	 * @return array         Checkout fields
	 */
	public function custom_checkout_fields( $fields ) {
		$fields['billing']  = $this->alter_fields( $fields['billing'], 'billing' );
		$fields['shipping'] = $this->alter_fields( $fields['shipping'], 'shipping' );
		return $fields;
	}

	/**
	 * Custom billing fields
	 *
	 * @param  array $fields Billing fields.
	 * @return array         Billing fields
	 */
	public function custom_billing_fields( $fields ) {
		return $this->alter_fields( $fields, 'billing' );
	}

	/**
	 * Custom shipping fields
	 *
	 * @param  array $fields Billing fields.
	 * @return array         Billing fields
	 */
	public function custom_shipping_fields( $fields ) {
		return $this->alter_fields( $fields, 'shipping' );
	}

	/**
	 * Alter checkout fields
	 *
	 * @param  array  $fields Checkout fields.
	 * @param  string $type   Billing/Shipping.
	 * @return array          Customized fields.
	 */
	private function alter_fields( $fields = array(), $type = 'billing' ) {
		if ( ! $this->helper->is_store_only_sell_to_indonesia() && 'ID' !== $this->helper->get_country_session( $type ) ) {
			return $fields;
		}

		if ( isset( $fields[ $type . '_first_name' ] ) ) {
			$fields[ $type . '_first_name' ]['label']      = __( 'First Name', 'pok' );
		}
		if ( isset( $fields[ $type . '_last_name' ] ) ) {
			$fields[ $type . '_last_name' ]['label']       = __( 'Last Name', 'pok' );
		}

		if ( isset( $fields[ $type . '_address_1' ] ) ) {
			$fields[ $type . '_address_1' ]['label']       = __( 'Address', 'pok' );
			$fields[ $type . '_address_1' ]['priority']    = $this->field_order['address_1'];
		}
		if ( isset( $fields[ $type . '_address_2' ] ) ) {
			$fields[ $type . '_address_2' ]['priority']    = $this->field_order['address_2'];
		}

		if ( isset( $fields[ $type . '_postcode' ] ) ) {
			$fields[ $type . '_postcode' ]['label']        = __( 'Postcode / ZIP', 'pok' );
			$fields[ $type . '_postcode' ]['required']     = false;
			$fields[ $type . '_postcode' ]['class']        = array();
			$fields[ $type . '_postcode' ]['priority']     = $this->field_order['postcode'];
		}

		if ( isset( $fields[ $type . '_email' ] ) ) {
			$fields[ $type . '_email' ]['label']           = __( 'Email Address', 'pok' );
			$fields[ $type . '_email' ]['priority']        = $this->field_order['email'];
		}

		if ( isset( $fields[ $type . '_phone' ] ) ) {
			$fields[ $type . '_phone' ]['label']           = __( 'Phone', 'pok' );
			$fields[ $type . '_phone' ]['priority']        = $this->field_order['phone'];
		}

		if ( isset( $fields[ $type . '_country' ] ) ) {
			$fields[ $type . '_country' ]['label']         = __( 'Country', 'pok' );
			$fields[ $type . '_country' ]['priority']      = $this->field_order['country'];
		}

		if ( ! is_checkout() || ! $this->helper->is_use_simple_address_field() ) {

			if ( isset( $fields[ $type . '_state' ] ) ) {
				$fields[ $type . '_state' ]['label']           = __( 'Province', 'pok' );
				$fields[ $type . '_state' ]['placeholder']     = __( 'Select Province', 'pok' );
				$fields[ $type . '_state' ]['priority']        = $this->field_order['state'];
			}

			if ( isset( $fields[ $type . '_city' ] ) ) {
				$fields[ $type . '_city' ]['label']            = __( 'City', 'pok' );
				$fields[ $type . '_city' ]['placeholder']      = __( 'Select City', 'pok' );
				$fields[ $type . '_city' ]['type']             = 'select';
				$fields[ $type . '_city' ]['required']         = true;
				$fields[ $type . '_city' ]['class']            = is_array( $fields[ $type . '_city' ]['class'] ) ? array_merge( $fields[ $type . '_city' ]['class'],array( 'validate-required', 'init-select2' ) ) : array( 'validate-required', 'init-select2' );
				$fields[ $type . '_city' ]['priority']         = $this->field_order['city'];
				$fields[ $type . '_city' ]['options']          = array( '' => __( 'Select City', 'pok' ) );
			}

			if ( 'pro' === $this->helper->get_license_type() ) {
				$fields[ $type . '_district' ]['label']        = __( 'District', 'pok' );
				$fields[ $type . '_district' ]['placeholder']  = __( 'Select District', 'pok' );
				$fields[ $type . '_district' ]['type']         = 'select';
				$fields[ $type . '_district' ]['required']     = true;
				$fields[ $type . '_district' ]['options']      = array( '' => __( 'Select District', 'pok' ) );
				$fields[ $type . '_district' ]['class']        = isset( $fields[ $type . '_district' ]['class'] ) ? array_merge( $fields[ $type . '_district' ]['class'], array( 'update_totals_on_change', 'address-field', 'init-select2' ) ) : array( 'update_totals_on_change', 'address-field', 'init-select2' );
				$fields[ $type . '_district' ]['priority']     = $this->field_order['district'];
			}

			// get returning user data.
			if ( is_user_logged_in() && ( is_account_page() || 'yes' === $this->setting->get( 'auto_fill_address' ) ) ) {
				$user_id = get_current_user_id();
				$default[ $type . '_state' ]      = $this->helper->get_address_id_from_user( $user_id, $type . '_state' );
				$default[ $type . '_city' ]       = $this->helper->get_address_id_from_user( $user_id, $type . '_city' );
				$default[ $type . '_district' ]   = $this->helper->get_address_id_from_user( $user_id, $type . '_district' );
				if ( ! empty( $default[ $type . '_state' ] ) ) {
					$fields[ $type . '_city' ]['options'] = $this->core->get_city( $default[ $type . '_state' ] );
				}
				if ( 'pro' === $this->helper->get_license_type() && ! empty( $default[ $type . '_city' ] ) ) {
					$fields[ $type . '_district' ]['options'] = $this->core->get_district( $default[ $type . '_city' ] );
				}
			}

		} else {

			/**
			 * Simple address field.
			 * @since  3.4.0
			 */

			$fields[ $type . '_simple_address' ] = array(
				'label'        		=> __( 'Town / City', 'pok' ),
				'placeholder'  		=> __( 'Search Town / City', 'pok' ),
				'type'         		=> 'select',
				'required'     		=> true,
				'options'      		=> array( '' => __( 'Search Town / City', 'pok' ) ),
				'class'        		=> array( 'update_totals_on_change', 'address-field', 'select2-ajax' ),
				'custom_attributes'	=> array(
					'data-action'	=> 'pok_search_simple_address',
					'data-nonce'	=> wp_create_nonce( 'search_city' )
				),
				'priority'     		=> $this->field_order['simple_address'],
			);

			$fields[ $type . '_state' ] = array(
				'label'		=> __( 'Province', 'pok' ),
				'type'		=> 'text',
				'required'	=> true,
				'class'		=> array( 'pok-hidden' ),
				'priority'	=> $this->field_order['state']
			);

			$fields[ $type . '_city' ] = array(
				'label'		=> __( 'City', 'pok' ),
				'type'		=> 'text',
				'required'	=> true,
				'class'		=> array( 'pok-hidden' ),
				'priority'	=> $this->field_order['city']
			);

			$fields[ $type . '_district' ] = array(
				'label'		=> __( 'District', 'pok' ),
				'type'		=> 'text',
				'required'	=> true,
				'class'		=> array( 'pok-hidden' ),
				'priority'	=> $this->field_order['district']
			);

			// get returning user data.
			if ( is_user_logged_in() && 'yes' === $this->setting->get( 'auto_fill_address' ) ) {
				$user_id = get_current_user_id();
				$default[ $type . '_state' ]      = $this->helper->get_address_id_from_user( $user_id, $type . '_state' );
				$default[ $type . '_city' ]       = $this->helper->get_address_id_from_user( $user_id, $type . '_city' );
				$default[ $type . '_district' ]   = $this->helper->get_address_id_from_user( $user_id, $type . '_district' );
				
				if ( ! empty( $default[ $type . '_state' ] ) && ! empty( $default[ $type . '_city' ] ) && ! empty( $default[ $type . '_district' ] ) ) {
					$city_id = $default[ $type . '_district' ] . '_' . $default[ $type . '_city' ] . '_' . $default[ $type . '_state' ];
					$city = $this->core->get_simple_address( $city_id );
					if ( ! empty( $city ) ) {
						$fields[ $type . '_simple_address' ]['options'] = array();
						$fields[ $type . '_simple_address' ]['options'][ $city_id ] = $city;
					}
				}
			}

		}

		if ( 'billing' === $type && $this->helper->is_let_user_decide_insurance() && is_checkout() ){
			$fields[ $type . '_insurance' ]['label']	= __( 'Add shipping insurance to this order', 'pok' );
			$fields[ $type . '_insurance' ]['type']		= 'checkbox';
			$fields[ $type . '_insurance' ]['class']	= array( 'update_totals_on_change', 'form-row-wide' );
			$fields[ $type . '_insurance' ]['priority'] = $this->field_order['insurance'];
		}

		// sort fields.
		uasort( $fields, array( $this, 'sort_field_by_priority' ) );

		return $fields;
	}

	/**
	 * Set default checkout value
	 * 
	 * @param mixed  $value Field value.
	 * @param string $input Field name.
	 */
	public function set_default_checkout_value( $value, $input ) {
		if ( is_user_logged_in() && ( is_account_page() || 'yes' === $this->setting->get( 'auto_fill_address' ) ) ) {
			$user_id = get_current_user_id();
			$default = array(
				'billing_state'      => $this->helper->get_address_id_from_user( $user_id, 'billing_state' ),
				'billing_city'       => $this->helper->get_address_id_from_user( $user_id, 'billing_city' ),
				'billing_district'   => $this->helper->get_address_id_from_user( $user_id, 'billing_district' ),
				'shipping_state'     => $this->helper->get_address_id_from_user( $user_id, 'shipping_state' ),
				'shipping_city'      => $this->helper->get_address_id_from_user( $user_id, 'shipping_city' ),
				'shipping_district'  => $this->helper->get_address_id_from_user( $user_id, 'shipping_district' ),
			);
			if ( isset( $default[ $input ] ) && ! empty( $default[ $input ] ) ) {
				return $default[ $input ];
			}
		}
		return $value;
	}

	/**
	 * Sort checkout fields based on priority.
	 *
	 * @param  array $x Field.
	 * @param  array $y Field.
	 * @return int      Diff.
	 */
	private function sort_field_by_priority( $x, $y ) {
		return ( isset( $x['priority'] ) ? $x['priority'] : 50 ) - ( isset( $y['priority'] ) ? $y['priority'] : 50 );
	}

	/**
	 * Custom checkout fields that can't modified by custom_checkout_fields hooks
	 *
	 * @param  array $fields Default checkout fields.
	 * @return array         Default checkout fields.
	 */
	public function custom_special_checkout_fields( $fields ) {
		$fields['postcode']['required'] = false;
		return $fields;
	}

	/**
	 * Get country locale
	 *
	 * @param  array $fields Fields.
	 * @return array         Fields.
	 */
	public function country_locale( $fields ) {
		$fields['ID']['state']['label'] = __( 'Province', 'pok' );
		$fields['ID']['postcode']['label'] = __( 'Postcode / ZIP', 'pok' );
		$fields['ID']['city']['label'] = __( 'Town / City', 'pok' );
		return $fields;
	}

	/**
	 * Check if billing/shipping is change on session
	 *
	 * @param  array $fragments Checkout fragments.
	 * @return array            Checkout fragments.
	 */
	public function maybe_reload_checkout( $fragments ) {
		$fragments['pok_reload'] = 'false';
		if ( $this->helper->is_store_only_sell_to_indonesia() ) {
			WC()->customer->set_billing_country( 'ID' );
			WC()->customer->set_shipping_country( 'ID' );
			WC()->session->set( 'pok_billing_country', 'ID' );
			WC()->session->set( 'pok_shipping_country', 'ID' );
			return $fragments;
		}
		$old_billing_country    = isset( WC()->session->pok_billing_country ) ? WC()->session->pok_billing_country : 'ID';
		$old_shipping_country   = isset( WC()->session->pok_shipping_country ) ? WC()->session->pok_shipping_country : 'ID';
		$new_billing_country    = WC()->customer->get_billing_country();
		$new_shipping_country   = WC()->customer->get_shipping_country();
		if ( $old_billing_country !== $new_billing_country ) {
			if ( 'ID' === $old_billing_country || 'ID' === $new_billing_country ) {
				$fragments['pok_reload'] = 'true';
			}
			WC()->session->set( 'pok_billing_country', sanitize_text_field( wp_unslash( $_POST['country'] ) ) ); // WPCS: Input var okay. CSRF okay.
		} elseif ( $old_shipping_country !== $new_shipping_country ) {
			if ( 'ID' === $old_shipping_country || 'ID' === $new_shipping_country ) {
				$fragments['pok_reload'] = 'true';
			}
			WC()->session->set( 'pok_shipping_country', sanitize_text_field( wp_unslash( $_POST['s_country'] ) ) ); // WPCS: Input var okay. CSRF okay.
		}
		return $fragments;
	}

	/**
	 * Load scripts
	 */
	public function enqueue_scripts() {
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
			if ( is_checkout() || is_account_page() || apply_filters( 'pok_is_checkout', false ) ) {
				wp_enqueue_style( 'pok-checkout', POK_PLUGIN_URL . '/assets/css/checkout.css', array( 'select2' ), POK_VERSION );
				wp_enqueue_script( 'pok-checkout', POK_PLUGIN_URL . '/assets/js/checkout.js', array( 'jquery', 'select2' ), POK_VERSION, true );
				$localize = array(
					'ajaxurl'               => admin_url( 'admin-ajax.php' ),
					'labelFailedCity'       => __( 'Failed to load city list. Try again?', 'pok' ),
					'labelFailedDistrict'   => __( 'Failed to load district list. Try again?', 'pok' ),
					'labelSelectCity'       => __( 'Select City', 'pok' ),
					'labelLoadingCity'      => __( 'Loading city options...', 'pok' ),
					'labelSelectDistrict'   => __( 'Select District', 'pok' ),
					'labelLoadingDistrict'  => __( 'Loading district options...', 'pok' ),
					'enableDistrict'        => false,
					'only_sell_to_indonesia' => $this->helper->is_store_only_sell_to_indonesia(),
					'billing_country'       => $this->helper->get_country_session( 'billing' ),
					'shipping_country'      => $this->helper->get_country_session( 'shipping' ),
					'loadReturningUserData' => is_account_page() ? 'yes' : $this->setting->get( 'auto_fill_address' ),
					'is_my_account'			=> is_account_page(),
					'is_checkout'			=> is_checkout(),
					'billing_state'         => 0,
					'shipping_state'        => 0,
					'billing_city'          => 0,
					'shipping_city'         => 0,
					'billing_district'      => 0,
					'shipping_district'     => 0,
					'nonce_change_country'  => wp_create_nonce( 'change_country' ),
					'nonce_get_list_city'   => wp_create_nonce( 'get_list_city' ),
					'nonce_get_list_district' => wp_create_nonce( 'get_list_district' ),
					'enableDistrict'        => ( 'pro' === $this->helper->get_license_type() ? true : false ),
					'useSimpleAddress'		=> $this->helper->is_use_simple_address_field()
				);
				// get returning user data.
				if ( is_user_logged_in() && ( is_account_page() || $this->setting->get( 'auto_fill_address' ) ) ) {
					$user_id = get_current_user_id();
					$localize['billing_state']      = $this->helper->get_address_id_from_user( $user_id, 'billing_state' );
					$localize['billing_city']       = $this->helper->get_address_id_from_user( $user_id, 'billing_city' );
					$localize['billing_district']   = $this->helper->get_address_id_from_user( $user_id, 'billing_district' );
					$localize['shipping_state']     = $this->helper->get_address_id_from_user( $user_id, 'shipping_state' );
					$localize['shipping_city']      = $this->helper->get_address_id_from_user( $user_id, 'shipping_city' );
					$localize['shipping_district']  = $this->helper->get_address_id_from_user( $user_id, 'shipping_district' );
				}
				wp_localize_script( 'pok-checkout', 'pok_checkout_data', $localize );
			}
		}
	}

	/**
	 * Custom address format
	 *
	 * @param  array $formats Address formats.
	 * @return array          Address formats.
	 */
	public function custom_address_format( $formats ) {
		$formats['ID'] = "{name}\n{company}\n{address_1}\n{address_2}\n{pok_district}{pok_city}\n{pok_state}\n{country}\n{postcode}";
		return $formats;
	}

	/**
	 * Custom address format replacements
	 *
	 * @param  array $replacements Replacement fields.
	 * @param  array $args         Address args.
	 * @return array               Replacement fields.
	 */
	public function custom_address_replacement( $replacements, $args ) {
		// set state name.
		$province = isset( $args['state'] ) ? $args['state'] : '';
		if ( isset( $args['state'] ) ) {
			if ( 0 !== intval( $args['state'] ) ) {
				$province = $this->core->get_single_province( intval( $args['state'] ) );
			} else {
				$province = $args['state'];
			}
		}

		// set city name.
		$city = isset( $args['city'] ) ? $args['city'] : '';
		if ( isset( $args['city'] ) ) {
			if ( 0 !== intval( $args['city'] ) ) {
				$city = $this->core->get_single_city_without_province( intval( $args['city'] ) );
			} else {
				$city = $args['city'];
			}
		}

		// set district name.
		$district = '';
		if ( isset( $args['district'] ) ) {
			if ( 0 !== intval( $args['city'] ) && 0 !== intval( $args['city'] ) && 'pro' === $this->helper->get_license_type() && 0 !== intval( $args['district'] ) ) {
				$district = 'Kec. ' . $this->core->get_single_district( intval( $args['city'] ), intval( $args['district'] ) ) . "\n";
			} elseif ( ! empty( $args['district'] ) ) {
				$district = 'Kec. ' . $args['district'] . "\n";
			} else {
				$district = "";
			}
		}

		$replacements['{pok_district}'] = $district;
		$replacements['{pok_city}']     = $city;
		$replacements['{pok_state}']    = $province;

		return $replacements;
	}

	/**
	 * Set address value on my account page
	 * 
	 * @param string $value        Address value.
	 * @param string $key          Field key.
	 * @param string $load_address Address type.
	 */
	public function set_my_account_address_value( $value, $key, $load_address ) {
		if ( in_array( $key, array( 'billing_state', 'billing_city', 'billing_district', 'shipping_state', 'shipping_city', 'shipping_district' ) ) ) {
			$user_id = get_current_user_id();
			$address_value = $this->helper->get_address_id_from_user( $user_id, $key );
			if ( ! empty( $address_value ) ) {
				$value = $address_value;
			}
		}
		return $value;
	}

	/**
	 * Fix name formatting on myaccount page
	 *
	 * @param  array  $address     Address data.
	 * @param  int    $customer_id Customer ID.
	 * @param  string $name        Billing/Shipping.
	 * @return array               Address data.
	 */
	public function format_myaccount_address( $address, $customer_id, $name ) {
		$address['district'] = get_user_meta( $customer_id, $name . '_district', true );
		return $address;
	}

	/**
	 * Validate district on checkout
	 */
	public function validate_district() {
		if ( 'pro' === $this->helper->get_license_type() ) {
			if ( isset( $_POST['billing_country'] ) && 'ID' === $_POST['billing_country'] && ( ! isset( $_POST['billing_district'] ) || empty( $_POST['billing_district'] ) ) ) { // WPCS: Input var okay. CSRF okay.
				wc_add_notice( __( '<b>Billing district</b> is required', 'pok' ), 'error' );
			}

			if ( isset( $_POST['ship_to_different_address'] ) && ! empty( $_POST['ship_to_different_address'] ) && isset( $_POST['shipping_country'] ) && 'ID' === $_POST['shipping_country'] ) { // WPCS: Input var okay. CSRF okay.
				if ( ! isset( $_POST['shipping_district'] ) || empty( $_POST['shipping_district'] ) ) { // WPCS: Input var okay. CSRF okay.
					wc_add_notice( __( '<b>Shipping district</b> is required', 'pok' ), 'error' );
				}
			}
		}

	}

	/**
	 * Update user meta on checkout
	 *
	 * @param  integer $order_id Order ID.
	 * @param  array   $data     Input data.
	 */
	public function update_order_meta( $order_id, $data ) {
		$order = wc_get_order( $order_id );
		$user_id = version_compare( WC()->version, '3.0', '>=' ) ? $order->get_user_id() : $order->user_id;

		// update order meta & user meta.
		if ( 'ID' === $data['billing_country'] ) {
			$province = $this->core->get_single_province( intval( $data['billing_state'] ) );
			update_post_meta( $order_id, '_billing_state', ( isset( $province ) && ! empty( $province ) ? $province : $data['billing_state'] ) );
			update_post_meta( $order_id, '_billing_state_id', $data['billing_state'] );
			update_user_meta( $user_id, 'billing_state', ( isset( $province ) && ! empty( $province ) ? $province : $data['billing_state'] ) );
			update_user_meta( $user_id, 'billing_state_id', $data['billing_state'] );

			$city = $this->core->get_single_city_without_province( intval( $data['billing_city'] ) );
			update_post_meta( $order_id, '_billing_city', ( isset( $city ) && ! empty( $city ) ? $city : $data['billing_city'] ) );
			update_post_meta( $order_id, '_billing_city_id', $data['billing_city'] );
			update_user_meta( $user_id, 'billing_city', ( isset( $city ) && ! empty( $city ) ? $city : $data['billing_city'] ) );
			update_user_meta( $user_id, 'billing_city_id', $data['billing_city'] );

			if ( isset( $data['billing_district'] ) ) {
				$district = $this->core->get_single_district( intval( $data['billing_city'] ), intval( $data['billing_district'] ) );
				update_post_meta( $order_id, '_billing_district', ( isset( $district ) && ! empty( $district ) ? $district : $data['billing_district'] ) );
				update_post_meta( $order_id, '_billing_district_id', $data['billing_district'] );
				update_user_meta( $user_id, 'billing_district', ( isset( $district ) && ! empty( $district ) ? $district : $data['billing_district'] ) );
				update_user_meta( $user_id, 'billing_district_id', $data['billing_district'] );
			}
		}
		if ( 'ID' === $data['shipping_country'] ) {
			$province = $this->core->get_single_province( intval( $data['shipping_state'] ) );
			update_post_meta( $order_id, '_shipping_state', ( isset( $province ) && ! empty( $province ) ? $province : $data['shipping_state'] ) );
			update_post_meta( $order_id, '_shipping_state_id', $data['shipping_state'] );
			update_user_meta( $user_id, 'shipping_state', ( isset( $province ) && ! empty( $province ) ? $province : $data['shipping_state'] ) );
			update_user_meta( $user_id, 'shipping_state_id', $data['shipping_state'] );

			$city = $this->core->get_single_city_without_province( intval( $data['shipping_city'] ) );
			update_post_meta( $order_id, '_shipping_city', ( isset( $city ) && ! empty( $city ) ? $city : $data['shipping_city'] ) );
			update_post_meta( $order_id, '_shipping_city_id', $data['shipping_city'] );
			update_user_meta( $user_id, 'shipping_city', ( isset( $city ) && ! empty( $city ) ? $city : $data['shipping_city'] ) );
			update_user_meta( $user_id, 'shipping_city_id', $data['shipping_city'] );

			if ( isset( $data['shipping_district'] ) ) {
				$district = $this->core->get_single_district( intval( $data['shipping_city'] ), intval( $data['shipping_district'] ) );
				update_post_meta( $order_id, '_shipping_district', ( isset( $district ) && ! empty( $district ) ? $district : $data['shipping_district'] ) );
				update_post_meta( $order_id, '_shipping_district_id', $data['shipping_district'] );
				update_user_meta( $user_id, 'shipping_district', ( isset( $district ) && ! empty( $district ) ? $district : $data['shipping_district'] ) );
				update_user_meta( $user_id, 'shipping_district_id', $data['shipping_district'] );
			}
		}

		// set data API.
		update_post_meta( $order_id, '_pok_data_api', $this->setting->get( 'base_api' ) );

		// unset random number.
		if ( WC()->session->__isset( 'pok_random_number' ) ) {
			WC()->session->__unset( 'pok_random_number' );
		}
	}

	/**
	 * Add district to order address
	 *
	 * @param array  $address Address data.
	 * @param string $type    Billing/shipping.
	 * @param object $order   Order object.
	 */
	public function set_order_address_data( $address, $type = 'billing', $order ) {
		$order_id = version_compare( WC()->version, '3.0', '>=' ) ? $order->get_id() : $order->id;

		$source_api = get_post_meta( $order_id, '_pok_data_api', true );

		$state = get_post_meta( $order_id, '_' . $type . '_state_id', true );
		if ( ! empty( $state ) && ( '' === $source_api || $source_api === $this->setting->get( 'base_api' ) ) ) {
			$address['state'] = $state;
		}

		$city = get_post_meta( $order_id, '_' . $type . '_city_id', true );
		if ( ! empty( $city ) && ( '' === $source_api || $source_api === $this->setting->get( 'base_api' ) ) ) {
			$address['city'] = $city;
		}

		$district = get_post_meta( $order_id, '_' . $type . '_district_id', true );
		// backward compatibility ( < 3.0.3 ).
		if ( '' === $district || ( '' !== $source_api && $source_api !== $this->setting->get( 'base_api' ) ) ) {
			$district = get_post_meta( $order_id, '_' . $type . '_district', true );
		}
		if ( ! empty( $district ) ) {
			$address['district'] = $district;
		}
		return $address;
	}

	/**
	 * Delete shipping cache
	 */
	public function delete_wc_cache() {
		$packages = WC()->cart->get_shipping_packages();
		foreach ( $packages as $key => $value ) {
			$shipping_session = "shipping_for_package_$key";
			WC()->session->__unset( $shipping_session );
		}
	}

	/**
	 * Add custom fee to checkout
	 */
	public function add_custom_fee() {
		// unique number.
		if ( 'yes' === $this->setting->get( 'unique_number' ) ) {
			if ( WC()->session->__isset( 'pok_random_number' ) ) {
				$number = WC()->session->get( 'pok_random_number' );
			} else {
				$number = $this->helper->random_number( $this->setting->get( 'unique_number_length' ) );
				WC()->session->set( 'pok_random_number', $number );
			}
			WC()->cart->add_fee( __( 'Unique Number', 'pok' ), $number );
		}
	}

	/**
	 * Show shipping weight on checkout
	 */
	public function show_additional_info_on_checkout() {
		if ( 'yes' === $this->setting->get( 'show_origin_on_checkout' ) && ! $this->helper->is_multi_vendor_addon_active() ) {
			$origin = $this->setting->get( 'store_location' );
			if ( ! empty( $origin ) && isset( $origin[0] ) && $this->core->get_single_city( intval( $origin[0] ) ) ) {
				?>
				<tr>
					<td class="product-name">
						<?php echo esc_html( apply_filters( 'pok_show_origin_label', __( 'Your items will be shipped from', 'pok' ) ) ); ?>
					</td>
					<td class="product-total">
						<?php echo esc_html( $this->core->get_single_city( intval( $origin[0] ) ) ); ?>
					</td>
				</tr>
				<?php
			}
		}
		if ( 'yes' === $this->setting->get( 'show_weight_on_checkout' ) && ! $this->helper->is_multi_vendor_addon_active() ) {
			if ( count( WC()->cart->get_cart() ) > 0 ) {
				$weight = $this->helper->get_total_weight( WC()->cart->get_cart() );
				if ( floor( $weight ) < $weight ) {
					$weight = number_format( $weight, 1 );
				}
				if( $weight > 0 ):
				?>
				<tr>
					<td class="product-name">
						<?php echo esc_html( apply_filters( 'pok_show_weight_label', __( 'Total shipping weight', 'pok' ) ) ); ?>
					</td>
					<td class="product-total">
						<?php echo esc_html( $weight ); ?>
						Kg
					</td>
				</tr>
				<?php
				endif;
			}
		}
	}

	/**
	 * Modify woocommerce setting page
	 *
	 * @param  array $fields Setting fields.
	 * @return array         Setting fields.
	 */
	public function modify_shipping_settings( $fields ) {
		if ( function_exists( 'array_column' ) ) {
			$key = array_search( 'woocommerce_enable_shipping_calc', array_column( $fields, 'id' ), true );
			if ( false !== $key ) {
				update_option( 'woocommerce_enable_shipping_calc', 'no' );
				$fields[ $key ]['custom_attributes']['disabled'] = 'disabled';
				$fields[ $key ]['desc'] .= ' (' . esc_html__( 'disabled by Plugin Ongkos Kirim', 'pok' ) . ')';
			}
		}
		return $fields;
	}

	/**
	 * Remove shipping from cart page
	 *
	 * @param  boolean $show_shipping Show shipping or not.
	 * @return boolean                Show shipping or not.
	 */
	public function remove_shipping_on_cart( $show_shipping ) {
		if ( is_cart() ) {
			return false;
		}
		return $show_shipping;
	}

	/**
	 * Handle save customer address
	 *
	 * @param  integer $user_id      User ID.
	 * @param  string  $load_address Billing/shipping.
	 */
	public function update_customer_address( $user_id, $load_address ) {
		if ( isset( $_POST['billing_country'] ) && 'ID' === sanitize_text_field( wp_unslash( $_POST['billing_country'] ) ) ) { // WPCS: Input var okay. CSRF okay.
			if ( isset( $_POST['billing_state'] ) ) { // WPCS: Input var okay. CSRF okay.
				if ( 0 !== intval( $_POST['billing_state'] ) ) { // WPCS: Input var okay. CSRF okay.
					$province = $this->core->get_single_province( intval( $_POST['billing_state'] ) ); // WPCS: Input var okay. CSRF okay.
					update_user_meta( $user_id, 'billing_state', ( isset( $province ) && ! empty( $province ) ? $province : $_POST['billing_state'] ) );
					update_user_meta( $user_id, 'billing_state_id', $_POST['billing_state'] );
				}
			}
			if ( isset( $_POST['billing_city'] ) ) { // WPCS: Input var okay. CSRF okay.
				if ( 0 !== intval( $_POST['billing_city'] ) ) { // WPCS: Input var okay. CSRF okay.
					$city = $this->core->get_single_city_without_province( intval( $_POST['billing_city'] ) ); // WPCS: Input var okay. CSRF okay.
					update_user_meta( $user_id, 'billing_city', ( isset( $city ) && ! empty( $city ) ? $city : $_POST['billing_city'] ) );
					update_user_meta( $user_id, 'billing_city_id', $_POST['billing_city'] );
				}
			}
			if ( isset( $_POST['billing_district'] ) ) { // WPCS: Input var okay. CSRF okay.
				if ( 0 !== intval( $_POST['billing_district'] ) ) { // WPCS: Input var okay. CSRF okay.
					$district = $this->core->get_single_district( intval( $_POST['billing_city'] ), intval( $_POST['billing_district'] ) ); // WPCS: Input var okay. CSRF okay.
					update_user_meta( $user_id, 'billing_district', ( isset( $district ) && ! empty( $district ) ? $district : $_POST['billing_district'] ) );
					update_user_meta( $user_id, 'billing_district_id', $_POST['billing_district'] );
				}
			}
		}
		if ( isset( $_POST['shipping_country'] ) && 'ID' === sanitize_text_field( wp_unslash( $_POST['shipping_country'] ) ) ) { // WPCS: Input var okay. CSRF okay.
			if ( isset( $_POST['shipping_state'] ) ) { // WPCS: Input var okay. CSRF okay.
				if ( 0 !== intval( $_POST['shipping_state'] ) ) { // WPCS: Input var okay. CSRF okay.
					$province = $this->core->get_single_province( intval( $_POST['shipping_state'] ) ); // WPCS: Input var okay. CSRF okay.
					update_user_meta( $user_id, 'shipping_state', ( isset( $province ) && ! empty( $province ) ? $province : $_POST['shipping_state'] ) );
					update_user_meta( $user_id, 'shipping_state_id', $_POST['shipping_state'] );
				}
			}
			if ( isset( $_POST['shipping_city'] ) ) { // WPCS: Input var okay. CSRF okay.
				if ( 0 !== intval( $_POST['shipping_city'] ) ) { // WPCS: Input var okay. CSRF okay.
					$city = $this->core->get_single_city_without_province( intval( $_POST['shipping_city'] ) ); // WPCS: Input var okay. CSRF okay.
					update_user_meta( $user_id, 'shipping_city', ( isset( $city ) && ! empty( $city ) ? $city : $_POST['shipping_city'] ) );
					update_user_meta( $user_id, 'shipping_city_id', $_POST['shipping_city'] );
				}
			}
			if ( isset( $_POST['shipping_district'] ) ) { // WPCS: Input var okay. CSRF okay.
				if ( 0 !== intval( $_POST['shipping_district'] ) ) { // WPCS: Input var okay. CSRF okay.
					$district = $this->core->get_single_district( intval( $_POST['shipping_city'] ), intval( $_POST['shipping_district'] ) ); // WPCS: Input var okay. CSRF okay.
					update_user_meta( $user_id, 'shipping_district', ( isset( $district ) && ! empty( $district ) ? $district : $_POST['shipping_district'] ) );
					update_user_meta( $user_id, 'shipping_district_id', $_POST['shipping_district'] );
				}
			}
		}
	}

	/**
	 * Change shipping method label if it has discount
	 * 
	 * @param  string $label  Shipping label.
	 * @param  object $method Method object.
	 * @return string         New shipping label.
	 */
	public function custom_shipping_label( $label, $method ) {
		if ( 'plugin_ongkos_kirim' === $method->get_method_id() ) {
			$shipping_meta = $method->get_meta_data();
			$etd = '';
			if ( 'yes' === $this->setting->get( 'show_shipping_etd' ) && ! empty( $shipping_meta['etd'] ) && '-' !== $shipping_meta['etd'] ) {
				if( 'Q9 Barang' === $shipping_meta['service'] ) {
					$shipping_meta['etd'] = 1;
				}
				$etd = " <span class='etd'>(" . $this->helper->format_etd( $shipping_meta['etd'] ) . ")</span>";
			}
			$original_cost = isset( $shipping_meta['original_cost'] ) ? floatval( $shipping_meta['original_cost'] ) : floatval( $method->get_cost() );

			if ( isset( $shipping_meta['original_cost'] ) && floatval( $shipping_meta['original_cost'] ) > floatval( $method->get_cost() ) ) {
				$label = "<span class='label'>" . $method->get_label() . ':</span> <span class="price"><del>' . wc_price( $original_cost ) . '</del><ins>' . wc_price( $method->get_cost() ) . '</ins></span>' . $etd;
			} else {
				$label = "<span class='label'>" . $method->get_label() . ':</span> <span class="price">' . wc_price( $original_cost ) . "</span>" . $etd;
			}
		}
		return $label;
	}

	/**
	 * Change default coupon label
	 * 
	 * @param  string $label  Coupon label.
	 * @param  object $coupon Coupon data.
	 * @return string         Coupon label.
	 */
	public function change_coupon_discount_label( $label, $coupon ) {
		if ( 'ongkir' === get_post_meta( $coupon->get_id(), 'discount_type', true ) ) {
			$label = $coupon->get_description();
			if ( empty( $label ) ) {
				$label = __( 'Shipping discount coupon', 'pok' );
			}
		}
		return $label;
	}

	/**
	 * Change default billing state on checkout
	 * @param  string $state
	 * @return string $state
	 */
	public function custom_checkout_billing_state( $state ) {
		$is_override  = $this->helper->is_override_to_indonesia();
		$country      = $this->helper->get_country_session( 'billing' );
		$base_country = WC()->countries->get_base_country();
		$map_states   = $this->helper->map_ongkir_states();

		if( $is_override ) {
			if( 'ID' === $country && 'ID' === $base_country ) {
				$state = $this->helper->map_ongkir_states( $state ) ? $this->helper->map_ongkir_states( $state ) : $state;
			} else {
				if( ! in_array( $state , $map_states ) ) {
					$state = '';
				}
			}
		} else {
			if( 'ID' === $base_country ) {
				$state = $this->helper->map_ongkir_states( $state ) ? $this->helper->map_ongkir_states( $state ) : $state;
			}
		}
		return $state;
	}

	/**
	 * Change default shipping state on checkout
	 * @param  string $state
	 * @return string $state
	 */
	public function custom_checkout_shipping_state( $state ) {
		$is_override  = $this->helper->is_override_to_indonesia();
		$country      = $this->helper->get_country_session( 'shipping' );
		$base_country = WC()->countries->get_base_country();
		$map_states   = $this->helper->map_ongkir_states();

		if( $is_override ) {
			if( 'ID' === $country && 'ID' === $base_country ) {
				$state = $this->helper->map_ongkir_states( $state ) ? $this->helper->map_ongkir_states( $state ) : $state;
			} else {
				if( ! in_array( $state , $map_states ) ) {
					$state = '';
				}
			}
		} else {
			if( 'ID' === $base_country ) {
				$state = $this->helper->map_ongkir_states( $state ) ? $this->helper->map_ongkir_states( $state ) : $state;
			}
		}
		return $state;
	}

	/**
	 * Set default checkout country to Indonesia if option override to indonesia is active
	 * 
	 * @param  string $base_country Base Country.
	 * @return string               Base Country.
	 */
	public function set_default_checkout_country( $base_country ) {
		if ( $this->helper->is_override_to_indonesia() ) {
			$base_country = 'ID';
		}
		return $base_country;
	}

	/**
	 * Set default customer country to Indonesia if option override to indonesia is active
	 * 
	 * @param array $location Customer default location array.
	 */
	public function set_default_customer_country( $location ) {
		if ( $this->helper->is_override_to_indonesia() ) {
			$location['country'] = 'ID';
		}
		return $location;
	}

}
