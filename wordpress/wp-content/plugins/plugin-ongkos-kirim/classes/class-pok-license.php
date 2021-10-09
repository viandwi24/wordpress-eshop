<?php

/**
 * POK License Class
 */
class POK_License {

	/**
	 * Default license value
	 *
	 * @var array
	 */
	private $defaults = array(
		'key'           => '',
		'status'        => array( false, 'License is not active' ),
		'attempt'       => 0,
		'type'          => 'trial',
		'exp_date'      => '',
	);

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->option_name = 'pok_license';
		$this->license = get_option( $this->option_name, $this->defaults );
	}

	/**
	 * Update license value
	 *
	 * @param  string $index Index name.
	 * @param  string $value Value.
	 */
	public function set( $index = 'key', $value = '' ) {
		if ( in_array( $index, array_keys( $this->license ), true ) ) {
			$this->license[ $index ] = $value;
		}
		update_option( $this->option_name, $this->license, true );
		$this->license = get_option( $this->option_name, $this->defaults );
	}

	/**
	 * Get license value
	 *
	 * @param  string $index Key or status?.
	 * @return mixed         License key if index is key, or status if index is status.
	 */
	public function get( $index = 'status' ) {
		return isset( $this->license[ $index ] ) ? $this->license[ $index ] : false;
	}

	/**
	 * Setting migration from old version.
	 * If user previously installed version <3.0.0, then migrate their setting to new one
	 */
	public function setting_migration() {
		$key    = get_option( 'nusantara_ongkir_lisensi', $this->defaults['key'] );
		$status = get_option( 'nusantara_ongkir_license_status', $this->defaults['status'] );
		$this->set( 'key', $key );
		$this->set( 'status', $status );
	}

	/**
	 * Debug the license
	 *
	 * @param  string $license_key License key.
	 */
	public function debug_status( $license_key = '' ) {
		$license_checker    = new tonjoo_license();
		$args               = array(
			'key'           => $license_key,
			'plugin_name'   => 'wooongkir-premium',
		);

		// hit server.
		$server_response = $license_checker->getStatus( $args );

		// status before hit server.
		echo '<b>Before hit server status:</b><pre>';
		print_r( $this->get( 'status' ) );
		echo '</pre>';

		// server response.
		echo '<b>Server response:</b><pre>';
		print_r( $server_response );
		echo '</pre>';
	}

	/**
	 * Check license status
	 *
	 * @return array                         License status.
	 */
	public function check_status() {
		$license_key    = $this->get( 'key' );
		$license_status = $this->get( 'status' );

		if ( empty( $license_key ) ) {
			$license_status = array( false, 'License is not active' );
			$this->set_status( $license_status, true );
			return $license_status;
		}

		$license_checker    = new tonjoo_license();
		$args               = array(
			'key'           => $license_key,
			'plugin_name'   => 'wooongkir-premium',
		);

		// hit server.
		$server_response = $license_checker->getStatus( $args );

		// if not connect internet, return latest status.
		if ( ! empty( $server_response ) && ! empty( $server_response['data'] ) && isset( $server_response['data']->status ) ) {
			if ( true === $server_response['data']->status ) {
				$license_status = array( true, 'License is active' );
				set_transient( 'nusantara_ongkir_license_status_check', true, 60 * 60 * 168 ); // 7 days
			} elseif ( false === $server_response['data']->status ) {
				$license_status = array( false, 'License is not active' );
			}
		}

		$this->set_status( $license_status );
		return $license_status;
	}

	/**
	 * Update license status based on false license counter
	 *
	 * @param  array   $license_status License status.
	 * @param  boolean $force_update   Force update?.
	 */
	protected function set_status( $license_status, $force_update = false ) {
		/**
		 * There are 2 kind of license status:
		 * - array(false,"License is not active")
		 * - array(true,"License is active")
		 */

		$max_false_counter = 5;
		$will_update_status = false;

		// get will_update_status.
		if ( false === $license_status[0] ) {
			$false_counter = $this->get( 'attempt' );
			if ( $false_counter >= $max_false_counter ) {
				$will_update_status = true;
			} else {
				$this->set( 'attempt', $false_counter + 1 );
				$will_update_status = false;
			}
		} else {
			$this->set( 'attempt', 0 );
			$will_update_status = true;
		}

		// set based of will_update_status.
		if ( $will_update_status || $force_update ) {
			$this->set( 'status', $license_statuss );
		}
	}

}
