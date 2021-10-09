
(function($){
	var local = pok_checkout_data;

	//check country
	pok_check_country = function(context) {
		if (context === 'billing') {
			$('#billing_country').val(local.billing_country).trigger('change');
		} else if (context === 'shipping') {
			$('#shipping_country').val(local.shipping_country).trigger('change');
		}
	}

	//load city list
	pok_load_city = function(context) {
		if (context !== 'billing' && context !== 'shipping') return;
		if ( ( 'billing' === context && ( 'ID' !== local.billing_country && 'ID' !== $('#billing_country').val() ) ) || ( 'shipping' === context && ( 'ID' !== local.shipping_country && 'ID' !== $('#shipping_country').val() ) ) ) return;
		var state = $('#'+context+'_state').val();
		if (state) {
			$('#'+context+'_city_field, #'+context+'_district_field').prop('title',local.labelLoadingCity).addClass('pok_loading');
			$('#'+context+'_city, #'+context+'_district').prop('disabled', true);
			$.post(pok_checkout_data.ajaxurl, {
				action: 'pok_get_list_city',
				pok_action: local.nonce_get_list_city,
				province_id: state
			}, function(data, status) {
				var arr = $.parseJSON(data);
				if (state != 0 && (status != "success" || Array.isArray(arr))) {
					$('#'+context+'_city_field').removeAttr('title').removeClass('pok_loading');
					$('#'+context+'_city').prop('disabled', false);
					if (confirm( local.labelFailedCity )) {
						return pok_load_city(context);
					}
					return;
				} 

				$('#'+context+'_city').val('').empty().append('<option value="">'+local.labelSelectCity+'</option>');
				$('#'+context+'_district').val('').empty().append('<option value="">'+local.labelSelectDistrict+'</option>'); 
				$.each(arr, function (i,v) {
					if (v != '' && v != '0') {
					   $('#'+context+'_city').append('<option value="'+i+'">'+v+'</option>');       
					}
				});
				
				$('#'+context+'_city_field, #'+context+'_district_field').removeAttr('title').removeClass('pok_loading').removeClass('woocommerce-validated');
				$('#'+context+'_city').prop('disabled', false).trigger('options_loaded', arr);
				$('#'+context+'_district').prop('disabled', false);
				if (!local.enableDistrict) {
					$('#'+context+'_city_field').addClass('update_totals_on_change');
				}
			});
		}
	}

	//load district list
	pok_load_district = function(context) {
		if (context !== 'billing' && context !== 'shipping') return;
		if ( ( 'billing' === context && ( 'ID' !== local.billing_country && 'ID' !== $('#billing_country').val() ) ) || ( 'shipping' === context && ( 'ID' !== local.shipping_country && 'ID' !== $('#shipping_country').val() ) ) ) return;
		var city = $('#'+context+'_city').val();
		if (parseInt(city)) {
			$('#'+context+'_district_field').prop('title',local.labelLoadingDistrict).addClass('pok_loading');
			$('#'+context+'_district').prop('disabled',true);
			$.post(pok_checkout_data.ajaxurl, {
				action: 'pok_get_list_district',
				pok_action: local.nonce_get_list_district,
				city_id: city        
			}, function(data,status) {
				var arr = $.parseJSON(data);
				if (city != 0 && (status != "success" || Array.isArray(arr))) {
					$('#'+context+'_district_field').removeAttr('title').removeClass('pok_loading');
					$('#'+context+'_district').prop('disabled', false);
					if (confirm( local.labelFailedDistrict )) {
						return pok_load_district(context);
					}
					return;
				}

				$('#'+context+'_district').val('').empty().append('<option value="">'+local.labelSelectDistrict+'</option>'); 
				$.each(arr, function (i,v) {
					if (v != '' && v != '0') {               
					   $('#'+context+'_district').append('<option value="'+i+'">'+v+'</option>');       
					}
				});
				$('#'+context+'_district_field').removeClass('woocommerce-validated').removeAttr('title').removeClass('pok_loading').addClass('update_totals_on_change');
				$('#'+context+'_district').prop('disabled', false).trigger('options_loaded', arr);
			});
		}
	}

	//load simple city list
	pok_load_simple_address = function(context) {
		if (context !== 'billing' && context !== 'shipping') return;
		if ( ( 'billing' === context && ( 'ID' !== local.billing_country && 'ID' !== $('#billing_country').val() ) ) || ( 'shipping' === context && ( 'ID' !== local.shipping_country && 'ID' !== $('#shipping_country').val() ) ) ) return;
		var city = $('#'+context+'_simple_address').val();
		var ids = city.split('_');

		if ( ids[0] ) {
			$('#'+context+'_district').val( ids[0] );
		} else {
			$('#'+context+'_district').val('');
		}
		if ( ids[1] ) {
			$('#'+context+'_city').val( ids[1] );
		} else {
			$('#'+context+'_city').val('');
		}
		if ( ids[2] ) {
			$('#'+context+'_state').val( ids[2] ).change();
		} else {
			$('#'+context+'_state').val('').change();
		}
	}

	// check if billing/shipping country changed.
	$( document.body ).on( 'updated_checkout', function(e,data){
		if ( data.fragments.pok_reload && "true" === data.fragments.pok_reload ) {
			$('*').css('cursor','wait');
			location.reload();
		} else {
			$('#billing_state').prop('disabled',false);
			$('#shipping_state').prop('disabled',false);
		}
	} );

	$(document).on('ready', function() {
		if ( local.loadReturningUserData === 'yes' ) {
			if ( 0 === parseInt(local.billing_state) ) {
				pok_check_country('billing');
				$('#billing_state').prop('disabled',false);
			}
			if ( 0 === parseInt(local.shipping_state) ) {
				pok_check_country('shipping');
				$('#shipping_state').prop('disabled',false);
			}
		} else {
			pok_check_country('billing');
			pok_check_country('shipping');
		}
	});

	if ( ! local.is_checkout || ! local.useSimpleAddress ) {
		$('.woocommerce-checkout, .woocommerce-address-fields').on('change', '#billing_country', function() {
			$('#billing_state').prop('disabled',true);
		});
		$('.woocommerce-checkout, .woocommerce-address-fields').on('change', '#shipping_country', function() {
			$('#shipping_state').prop('disabled',true);
		});
		$('.woocommerce-checkout, .woocommerce-address-fields').on('change', '#billing_state', function() {
			pok_load_city('billing');
		});
		$('.woocommerce-checkout, .woocommerce-address-fields').on('change', '#shipping_state', function() {
			pok_load_city('shipping');
		});
		if (local.enableDistrict) {
			$('.woocommerce-checkout, .woocommerce-address-fields').on('change', '#billing_city', function() {
				pok_load_district('billing');
			});
			$('.woocommerce-checkout, .woocommerce-address-fields').on('change', '#shipping_city', function() {
				pok_load_district('shipping');
			});
		}
	} else {
		$('.woocommerce-checkout, .woocommerce-address-fields').on('change', '#billing_simple_address', function() {
			pok_load_simple_address('billing');
		});
		$('.woocommerce-checkout, .woocommerce-address-fields').on('change', '#shipping_simple_address', function() {
			pok_load_simple_address('shipping');
		});
	}

	//load returning user data
	pok_load_returning_user_data = function() {
		if ( ! local.is_checkout || ! local.useSimpleAddress ) {
			if (parseInt(local.billing_state) && local.billing_country == 'ID') {
				$('#billing_state').val(local.billing_state).trigger('change');
				if (parseInt(local.billing_city)) {
					$('#billing_city').on('options_loaded', function(e, city_list) {
						if (city_list[local.billing_city]) {
							if (!local.enableDistrict) $('#billing_city').addClass('update_totals_on_change');
							$('#billing_city').val(local.billing_city).trigger('change');
						}
					});
					if (parseInt(local.billing_district) && local.enableDistrict) {
						$('#billing_district').on('options_loaded', function(e, district_list) {
							if (district_list[local.billing_district]) {
								$('#billing_district').addClass('update_totals_on_change').val(local.billing_district).trigger('change');
							}
						});
					}
				}
			}
			if (parseInt(local.shipping_state) && local.shipping_country == 'ID') {
				$('#shipping_state').val(local.shipping_state).trigger('change');
				if (parseInt(local.shipping_city)) {
					$('#shipping_city').on('options_loaded', function(e, city_list) {
						if (city_list[local.shipping_city]) {
							if (!local.enableDistrict) $('#shipping_city').addClass('update_totals_on_change');
							$('#shipping_city').val(local.shipping_city).trigger('change');
						}
					});
					if (parseInt(local.shipping_district) && local.enableDistrict) {
						$('#shipping_district').on('options_loaded', function(e, district_list) {
							if (district_list[local.shipping_district]) {
								$('#shipping_district').addClass('update_totals_on_change').val(local.shipping_district).trigger('change');
							}
						});
					}
				}
			}
		}
	}

	if (local.loadReturningUserData === 'yes') {
		pok_load_returning_user_data();
	}

	// reorder hack
	var wrappers = $('.woocommerce-billing-fields__field-wrapper, .woocommerce-shipping-fields__field-wrapper, .woocommerce-address-fields__field-wrapper, .woocommerce-additional-fields__field-wrapper .woocommerce-account-fields');
	wrappers.each( function( index, wrapper ) {
		var orig_class = $(wrapper).attr('class');
		$(wrapper).removeClass(orig_class).addClass(orig_class+'-pok').find('.form-row').sort(function (a, b) {
			var fieldA = parseInt($(a).data('priority')) || parseInt($(a).data('sort'));
			var fieldB = parseInt($(b).data('priority')) || parseInt($(b).data('sort'));
			return (fieldA < fieldB) ? -1 : (fieldA > fieldB) ? 1 : 0;
		}).appendTo(wrapper);
	});

	$( '.init-select2 select' ).select2();

	$( '.select2-ajax select' ).each(function() {
		var action 	= $(this).data('action');
		var phrase	= $(this).val();
		var nonce 	= $(this).data('nonce');
		$(this).select2({
			ajax: {
				url: local.ajaxurl,
				dataType: 'json',
				delay: 250,
				data: function( params ) {
					return {
						pok_action: nonce,
						action: action,
						q: params.term
					}
				},
				processResults: function (data, params) {
					return {
						results: data
					};
				},
    			cache: true
			},
			minimumInputLength: 3,
			placeholder: $(this).attr('placeholder')
		});
	});
		

})(jQuery);
