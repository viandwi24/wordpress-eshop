<div class="setting-row">
	<div class="setting-index">
		<label><?php esc_html_e( 'Use Simple Address Field', 'pok' ); ?></label>
		<p class="helper"><?php esc_html_e( 'If enabled, the address fields (province, city, district) will be combined into 1 simplified field. This option only available on API Tonjoo.', 'pok' ); ?></p>
	</div>
	<div class="setting-option">
		<?php if ( 'nusantara' === $settings['base_api'] ) : ?>
			<div class="toggle">
				<input type="radio" name="pok_setting[use_simple_address_field]" id="pok-use_simple_address_field-no" <?php echo 'no' === $settings['use_simple_address_field'] ? 'checked' : ''; ?> value="no">
				<label for="pok-use_simple_address_field-no"><?php esc_html_e( 'No', 'pok' ); ?></label>
				<input type="radio" name="pok_setting[use_simple_address_field]" id="pok-use_simple_address_field-yes" <?php echo 'yes' === $settings['use_simple_address_field'] ? 'checked' : ''; ?> value="yes">
				<label for="pok-use_simple_address_field-yes"><?php esc_html_e( 'Yes', 'pok' ); ?></label>
			</div>
		<?php else: ?>
			<p class="helper" style="margin:0;"><?php esc_html_e( 'This option only available on API Tonjoo.', 'pok' ) ?></p>
		<?php endif; ?>
	</div>
</div>
<div class="setting-row">
	<div class="setting-index">
		<label><?php esc_html_e( 'Auto Fill Address', 'pok' ); ?></label>
		<p class="helper"><?php esc_html_e( 'Auto-fill checkout field with saved address if customer is a returning user.', 'pok' ); ?></p>
	</div>
	<div class="setting-option">
		<div class="toggle">
			<input type="radio" name="pok_setting[auto_fill_address]" id="pok-auto_fill_address-no" <?php echo 'no' === $settings['auto_fill_address'] ? 'checked' : ''; ?> value="no">
			<label for="pok-auto_fill_address-no"><?php esc_html_e( 'No', 'pok' ); ?></label>
			<input type="radio" name="pok_setting[auto_fill_address]" id="pok-auto_fill_address-yes" <?php echo 'yes' === $settings['auto_fill_address'] ? 'checked' : ''; ?> value="yes">
			<label for="pok-auto_fill_address-yes"><?php esc_html_e( 'Yes', 'pok' ); ?></label>
		</div>
	</div>
</div>
<div class="setting-row">
	<div class="setting-index">
		<label><?php esc_html_e( 'Show Total Weight on Checkout', 'pok' ); ?></label>
		<p class="helper"><?php esc_html_e( 'Show total shipping weight on checkout page', 'pok' ); ?></p>
	</div>
	<div class="setting-option">
		<div class="toggle">
			<input type="radio" name="pok_setting[show_weight_on_checkout]" id="pok-show_weight_on_checkout-no" <?php echo 'no' === $settings['show_weight_on_checkout'] ? 'checked' : ''; ?> value="no">
			<label for="pok-show_weight_on_checkout-no"><?php esc_html_e( 'No', 'pok' ); ?></label>
			<input type="radio" name="pok_setting[show_weight_on_checkout]" id="pok-show_weight_on_checkout-yes" <?php echo 'yes' === $settings['show_weight_on_checkout'] ? 'checked' : ''; ?> value="yes">
			<label for="pok-show_weight_on_checkout-yes"><?php esc_html_e( 'Yes', 'pok' ); ?></label>
		</div>
	</div>
</div>
<div class="setting-row">
	<div class="setting-index">
		<label><?php esc_html_e( 'Show Shipping Estimation on Checkout', 'pok' ); ?></label>
		<p class="helper"><?php esc_html_e( 'Show shipping estimation on checkout', 'pok' ); ?></p>
	</div>
	<div class="setting-option">
		<div class="toggle">
			<input type="radio" name="pok_setting[show_shipping_etd]" id="pok-show_shipping_etd-no" <?php echo 'no' === $settings['show_shipping_etd'] ? 'checked' : ''; ?> value="no">
			<label for="pok-show_shipping_etd-no"><?php esc_html_e( 'No', 'pok' ); ?></label>
			<input type="radio" name="pok_setting[show_shipping_etd]" id="pok-show_shipping_etd-yes" <?php echo 'yes' === $settings['show_shipping_etd'] ? 'checked' : ''; ?> value="yes">
			<label for="pok-show_shipping_etd-yes"><?php esc_html_e( 'Yes', 'pok' ); ?></label>
		</div>
	</div>
</div>
<div class="setting-row">
	<div class="setting-index">
		<label><?php esc_html_e( 'Show Shipping Origin on Checkout', 'pok' ); ?></label>
		<p class="helper"><?php esc_html_e( 'Show your store location on checkout page', 'pok' ); ?></p>
	</div>
	<div class="setting-option">
		<div class="toggle">
			<input type="radio" name="pok_setting[show_origin_on_checkout]" id="pok-show_origin_on_checkout-no" <?php echo 'no' === $settings['show_origin_on_checkout'] ? 'checked' : ''; ?> value="no">
			<label for="pok-show_origin_on_checkout-no"><?php esc_html_e( 'No', 'pok' ); ?></label>
			<input type="radio" name="pok_setting[show_origin_on_checkout]" id="pok-show_origin_on_checkout-yes" <?php echo 'yes' === $settings['show_origin_on_checkout'] ? 'checked' : ''; ?> value="yes">
			<label for="pok-show_origin_on_checkout-yes"><?php esc_html_e( 'Yes', 'pok' ); ?></label>
		</div>
	</div>
</div>

<?php do_action( 'pok_setting_checkout', $settings ); ?>