(function($){
	var local = pok_profile_data;

	//check country
	pok_check_country = function(context) {
		if (context === 'billing') {
			$('#billing_country').val(local.billing_country);
		} else if (context === 'shipping') {
			$('#shipping_country').val(local.shipping_country);
		}
		$('#'+context+'_country').on('change',function(){
			if ( ( 'billing' === context && $('#'+context+'_country').val() !== local.billing_country ) || ( 'shipping' === context && $('#'+context+'_country').val() !== local.shipping_country ) ) {
				$('#'+context+'_country, #'+context+'_state').prop('disabled', true);
				$.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						pok_action: local.nonce_change_country,
						action: 'pok_change_profile_country',
						country: $('#'+context+'_country').val(),
						context: context,
						user_id: $('#user_id').val()
					},
					success: function(data){
						if (data == 'reload') {
							$('*').css('cursor','wait');
							location.reload();
						} else {
							$('#'+context+'_country, #'+context+'_state').prop('disabled', false);
						}
					}
				});
			}
		});
	}

	//load city list
	pok_load_city = function(context) {
		if (context !== 'billing' && context !== 'shipping') return;
		var state = $('#'+context+'_state').val();
		if (state) {
			$('#'+context+'_city, #'+context+'_district').attr('disabled', true);
			$.post(ajaxurl, {
				action: 'pok_get_list_city',
				pok_action: local.nonce_get_list_city,
				province_id: state
			}, function(data, status) {
				var arr = $.parseJSON(data);
				if (state != 0 && (status != "success" || Array.isArray(arr))) {
					$('.'+context+'_city_field').attr('title','').removeClass('pok_loading');
					$('#'+context+'_city').attr('disabled', false);
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
				
				$('#'+context+'_city').attr('disabled', false).trigger(context+'_city_options_loaded', arr);
				$('#'+context+'_district').attr('disabled', false);
			});
		}
	}

	//load district list
	pok_load_district = function(context) {
		if (context !== 'billing' && context !== 'shipping') return;
		// if (local.billing_country != 'ID') return;
		var city = $('#'+context+'_city').val();
		if (parseInt(city)) {
			$('#'+context+'_district').attr('disabled',true);
			$.post(ajaxurl, {
				action: 'pok_get_list_district',
				pok_action: local.nonce_get_list_district,
				city_id: city        
			}, function(data,status) {
				var arr = $.parseJSON(data);
				if (city != 0 && (status != "success" || Array.isArray(arr))) {
					$('#'+context+'_district').attr('disabled', false);
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
				$('#'+context+'_district').attr('disabled', false).trigger(context+'_district_options_loaded', arr);
			});
		} else {
			$('#'+context+'_district').val('').empty().append('<option value="">'+local.labelSelectDistrict+'</option>'); 
		}
	}

	pok_check_country('billing');
	pok_check_country('shipping');

	if ( 'ID' === local.billing_country ) {
		$('#fieldset-billing').on( 'change', '#billing_state', function() {
			pok_load_city( 'billing' );
		});
	}
	if ( 'ID' === local.shipping_country ) {
		$('#fieldset-shipping').on( 'change', '#shipping_state', function() {
			pok_load_city( 'shipping' );
		});
	}
	if (local.enableDistrict) {
		$('#fieldset-billing').on( 'change', '#billing_city', function() {
			pok_load_district('billing');
		});
		$('#fieldset-shipping').on( 'change', '#shipping_city', function() {
			pok_load_district('shipping');
		});
	}

	pok_load_user_data = function() {
		if (parseInt(local.billing_state) && local.billing_country == 'ID') {
			$('#billing_state').val(local.billing_state).trigger('change');
			if (parseInt(local.billing_city)) {
				$('#billing_city').on('billing_city_options_loaded', function(e, city_list) {
					if (city_list[local.billing_city]) {
						$('#billing_city').val(local.billing_city).trigger('change');
					}
				});
				if (parseInt(local.billing_district) && local.enableDistrict) {
					$('#billing_district').on('billing_district_options_loaded', function(e, district_list) {
						if (district_list[local.billing_district]) {
							$('#billing_district').val(local.billing_district).trigger('change');
						}
					});
				}
			}
		}
		if (parseInt(local.shipping_state) && local.shipping_country == 'ID') {
			$('#shipping_state').val(local.shipping_state).trigger('change');
			if (parseInt(local.shipping_city)) {
				$('#shipping_city').on('shipping_city_options_loaded', function(e, city_list) {
					if (city_list[local.shipping_city]) {
						$('#shipping_city').val(local.shipping_city).trigger('change');
					}
				});
				if (parseInt(local.shipping_district) && local.enableDistrict) {
					$('#shipping_district').on('shipping_district_options_loaded', function(e, district_list) {
						if (district_list[local.shipping_district]) {
							$('#shipping_district').val(local.shipping_district).trigger('change');
						}
					});
				}
			}
		}
	}
	pok_load_user_data();

	$( '#fieldset-billing .select2, #fieldset-shipping .select2' ).select2();

})(jQuery);