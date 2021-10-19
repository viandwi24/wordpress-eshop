<?php
/**
 * Framework license field.
 *
 * @package    Woo_Gallery_Slider
 * @subpackage Woo_Gallery_Slider/public
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'WCGS_Field_license' ) ) {
	/**
	 *
	 * Field: license
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class WCGS_Field_license extends WCGS_Fields {

		/**
		 * Field constructor.
		 *
		 * @param array  $field The field type.
		 * @param string $value The values of the field.
		 * @param string $unique The unique ID for the field.
		 * @param string $where To where show the output CSS.
		 * @param string $parent The parent args.
		 */
		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {

			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		/**
		 * Render field
		 *
		 * @return void
		 */
		public function render() {

			if ( ! in_array( 'woo-gallery-slider-pro/woo-gallery-slider-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				return;
			}
			echo wp_kses_post( $this->field_before() );
			$type = ( ! empty( $this->field['attributes']['type'] ) ) ? $this->field['attributes']['type'] : 'text';

			$manage_license       = new Woo_Gallery_Slider_Pro_License( WOO_GALLERY_SLIDER_PRO_FILE, WOO_GALLERY_SLIDER_PRO_VERSION, 'ShapedPlugin', WOO_GALLERY_SLIDER_PRO_STORE_URL, WOO_GALLERY_SLIDER_PRO_ITEM_ID, WOO_GALLERY_SLIDER_PRO_ITEM_SLUG );
			$license_key          = $manage_license->get_license_key();
			$license_key_status   = $manage_license->get_license_status();
			$license_status       = ( is_object( $license_key_status ) ? $license_key_status->license : '' );
			$license_notices      = $manage_license->license_notices();
			$license_status_class = '';
			$license_active       = '';
			$license_data         = $manage_license->api_request();

			echo '<div class="woo-gallery-slider-pro-license text-center">';
			echo '<h3>' . esc_html__( 'Gallery Slider for WooCommerce - Pro License Key', 'woo-gallery-slider' ) . '</h3>';
			if ( 'valid' === $license_status ) {
				$license_status_class = 'license-key-active';
				$license_active       = '<span>' . __( 'Active', 'woo-gallery-slider' ) . '</span>';
				echo '<p>' . esc_html__( 'Your license key is active.', 'woo-gallery-slider' ) . '</p>';
			} elseif ( 'expired' === $license_status ) {
				echo '<p style="color: red;">Your license key expired on ' . wp_kses_post( date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) ) ) . '. <a href="' . esc_url( WOO_GALLERY_SLIDER_PRO_STORE_URL ) . '/checkout/?edd_license_key=' . esc_attr( $license_key ) . '&download_id=' . esc_attr( WOO_GALLERY_SLIDER_PRO_ITEM_ID ) . '&utm_campaign=woo_gallery_slider&utm_source=licenses&utm_medium=expired" target="_blank">Renew license key at discount.</a></p>';
			} else {
				echo '<p>Please activate your license key to make the plugin work. <a href="https://docs.shapedplugin.com/docs/gallery-slider-for-woocommerce-pro/getting-started/activating-license-key/" target="_blank">How to activate license key?</a></p>';
			}
			echo '<div class="woo-gallery-slider-pro-license-area">';
			echo '<div class="woo-gallery-slider-pro-license-key"><input class="woo-gallery-slider-pro-license-key-input ' . esc_attr( $license_status_class ) . '" type="' . esc_attr( $type ) . '" name="' . esc_attr( $this->field_name() ) . '" value="' . esc_attr( $this->value ) . '"' . $this->field_attributes() . ' />' . wp_kses_post( $license_active ) . '</div>'; // phpcs:ignore
			wp_nonce_field( 'sp_woo_gallery_slider_pro_nonce', 'sp_woo_gallery_slider_pro_nonce' );
			if ( 'valid' === $license_status ) {
				echo '<input style="color: #dc3545; border-color: #dc3545;" type="submit" class="button-secondary btn-license-deactivate" name="sp_woo_gallery_slider_pro_license_deactivate" value="' . esc_html__( 'Deactivate', 'woo-gallery-slider' ) . '"/>';
			} else {
				echo '<input type="submit" class="button-secondary btn-license-save-activate" name="' . esc_attr( $this->unique ) . '[_nonce][save]" value="' . esc_attr__( 'Activate', 'woo-gallery-slider' ) . '"/>';
				echo '<input type="hidden" class="btn-license-activate" name="sp_woo_gallery_slider_pro_license_activate" value="' . esc_html__( 'Activate', 'woo-gallery-slider' ) . '"/>';
			}
			echo '<br><div class="woo-gallery-slider-pro-license-error-notices">' . wp_kses_post( $license_notices ) . '</div>';
			echo '</div>';
			echo '</div>';
			echo wp_kses_post( $this->field_after() );
		}

	}
}
