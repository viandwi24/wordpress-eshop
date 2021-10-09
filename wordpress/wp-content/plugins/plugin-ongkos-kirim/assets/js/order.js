(function($){
	var local = pok_order_data;

	//load returning user data
	pok_load_returning_user_data = function() {
		if ( 'ID' === local.billing_country && parseInt(local.billing_state) ) {
			$('#_billing_state').val(local.billing_state);
			if (parseInt(local.billing_city)) {
				$('#_billing_city').on('billing_city_options_loaded', function(e, city_list) {
					var value = $('#_billing_city').data('value') || local.billing_city;
					if (city_list[value]) {
						$('#_billing_city').val(value).trigger('change');
					}
				});
				if (parseInt(local.billing_district) && local.enableDistrict) {
					$('#_billing_district').on('billing_district_options_loaded', function(e, district_list) {
						var value = $('#_billing_district').data('value') || local.billing_district;
						if (district_list[value]) {
							$('#_billing_district').val(value).trigger('change');
						}
					});
				}
			}
		}
		if ( 'ID' === local.shipping_country && parseInt(local.shipping_state) ) {
			$('#_shipping_state').val(local.shipping_state);
			if (parseInt(local.shipping_city)) {
				$('#_shipping_city').on('shipping_city_options_loaded', function(e, city_list) {
					var value = $('#_shipping_city').data('value') || local.shipping_city;
					if (city_list[value]) {
						$('#_shipping_city').val(value).trigger('change');
					}
				});
				if (parseInt(local.shipping_district) && local.enableDistrict) {
					$('#_shipping_district').on('shipping_district_options_loaded', function(e, district_list) {
						var value = $('#_shipping_district').data('value') || local.shipping_district;
						if (district_list[value]) {
							$('#_shipping_district').val(value).trigger('change');
						}
					});
				}
			}
		}
	}

	//load city list
	pok_load_city = function(context) {
		if (context !== 'billing' && context !== 'shipping') return;
		if ( ( 'billing' === context && ( 'ID' !== local.billing_country && 'ID' !== $('#_billing_country').val() ) ) || ( 'shipping' === context && ( 'ID' !== local.shipping_country && 'ID' !== $('#_shipping_country').val() ) ) ) return;
		var state = $('#_'+context+'_state').val();
		if (state) {
			if ( parseInt( state ) === parseInt( local[context+'_state'] ) && local[context+'_city_options'] ) {
				var html = '';
				for ( var i in local[context+'_city_options'] ) {
					html += '<option value="' + i + '">' + local[context+'_city_options'][i] + '</option>';
				}
				$( '#_'+context+'_city' ).html(html).trigger(context+'_city_options_loaded', local[context+'_city_options']);
			} else {
				$('._'+context+'_city_field, ._'+context+'_district_field').attr('title',local.labelLoadingCity).addClass('pok_loading');
				$('#_'+context+'_city, #_'+context+'_district').attr('disabled', true);
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

					$('#_'+context+'_city').val('').empty().append('<option value="">'+local.labelSelectCity+'</option>');
					$('#_'+context+'_district').val('').empty().append('<option value="">'+local.labelSelectDistrict+'</option>'); 
					$.each(arr, function (i,v) {
						if (v != '' && v != '0') {
						   $('#_'+context+'_city').append('<option value="'+i+'">'+v+'</option>');       
						}
					});
					// $('#_'+context+'_city').val( $('#_'+context+'_city').attr('data-value') );
					
					$('.'+context+'_city_field, .'+context+'_district_field').removeAttr('title').removeClass('pok_loading');
					$('#_'+context+'_city').attr('disabled', false).trigger(context+'_city_options_loaded', arr);
					$('#_'+context+'_district').attr('disabled', false);
				});
			}
		}
	}

	//load district list
	pok_load_district = function(context) {
		if (context !== 'billing' && context !== 'shipping') return;
		if ( ( 'billing' === context && ( 'ID' !== local.billing_country && 'ID' !== $('#_billing_country').val() ) ) || ( 'shipping' === context && ( 'ID' !== local.shipping_country && 'ID' !== $('#_shipping_country').val() ) ) ) return;
		var city = $('#_'+context+'_city').val();
		if (parseInt(city)) {
			if ( parseInt( city ) === parseInt( local[context+'_city'] ) && local[context+'_district_options'] ) {
				var html = '';
				for ( var i in local[context+'_district_options'] ) {
					html += '<option value="' + i + '">' + local[context+'_district_options'][i] + '</option>';
				}
				$( '#_'+context+'_district' ).html(html).trigger(context+'_district_options_loaded', local[context+'_district_options']);
			} else {
				$('.'+context+'_district_field').attr('title',local.labelLoadingDistrict).addClass('pok_loading');
				$('#_'+context+'_district').attr('disabled',true);
				$.post(ajaxurl, {
					action: 'pok_get_list_district',
					pok_action: local.nonce_get_list_district,
					city_id: city        
				}, function(data,status) {
					var arr = $.parseJSON(data);
					if (city != 0 && (status != "success" || Array.isArray(arr))) {
						$('.'+context+'_district_field').removeAttr('title').removeClass('pok_loading');
						$('#_'+context+'_district').attr('disabled', false);
						if (confirm( local.labelFailedDistrict )) {
							return pok_load_district(context);
						}
						return;
					}

					$('#_'+context+'_district').val('').empty().append('<option value="">'+local.labelSelectDistrict+'</option>'); 
					$.each(arr, function (i,v) {
						if (v != '' && v != '0') {               
						   $('#_'+context+'_district').append('<option value="'+i+'">'+v+'</option>');       
						}
					});
					// $('#_'+context+'_district').val( $('#_'+context+'_district').attr('data-value') );

					$('.'+context+'_district_field').removeClass('woocommerce-validated').removeAttr('title').removeClass('pok_loading');
					$('#_'+context+'_district').attr('disabled', false).trigger(context+'_district_options_loaded', arr);
				});
			}
		} else {
			$('#_'+context+'_district').val('').empty().append('<option value="">'+local.labelSelectDistrict+'</option>'); 
		}
	}

	$('#order_data').on( 'change', '#_billing_state', function() {
		pok_load_city( 'billing' );
	});
	$('#order_data').on( 'change', '#_shipping_state', function() {
		pok_load_city( 'shipping' );
	});
	if (local.enableDistrict) {
		$('#order_data').on( 'change', '#_billing_city', function() {
			pok_load_district('billing');
		});
		$('#order_data').on( 'change', '#_shipping_city', function() {
			pok_load_district('shipping');
		});
	}
	pok_load_returning_user_data();

	var pok_meta_box_order = {
		load_billing: function( force ) {
			if ( true === force || window.confirm( woocommerce_admin_meta_boxes.load_billing ) ) {

				// Get user ID to load data for
				var user_id = $( '#customer_user' ).val();

				if ( ! user_id ) {
					window.alert( woocommerce_admin_meta_boxes.no_customer_selected );
					return false;
				}

				var data = {
					user_id : user_id,
					action  : 'woocommerce_get_customer_details',
					security: woocommerce_admin_meta_boxes.get_customer_details_nonce
				};

				$( this ).closest( 'div.order_data_column' ).block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});

				$('#_billing_city').val('').empty().append('<option value="">'+local.labelSelectCity+'</option>'); 
				$('#_billing_district').val('').empty().append('<option value="">'+local.labelSelectDistrict+'</option>'); 

				$.ajax({
					url: woocommerce_admin_meta_boxes.ajax_url,
					data: data,
					type: 'POST',
					success: function( response ) {
						if ( response && response.billing ) {
							if ( response.billing.state_options ) {
								var html = '';
								for ( var i in response.billing.state_options ) {
									html += '<option value="' + i + '">' + response.billing.state_options[i] + '</option>';
								}
								$( '#_billing_state' ).html(html);
							}
							if ( response.billing.city_options ) {
								var html = '';
								for ( var i in response.billing.city_options ) {
									html += '<option value="' + i + '">' + response.billing.city_options[i] + '</option>';
								}
								$( '#_billing_city' ).html(html);
							}
							if ( response.billing.district_options ) {
								var html = '';
								for ( var i in response.billing.district_options ) {
									html += '<option value="' + i + '">' + response.billing.district_options[i] + '</option>';
								}
								$( '#_billing_district' ).html(html);
							}
							$.each( response.billing, function( key, data ) {
								$( '#_billing_' + key ).val( data );
							});
						}
						$( 'div.order_data_column' ).unblock();
					}
				});
			}
			return false;
		},

		load_shipping: function( force ) {
			if ( true === force || window.confirm( woocommerce_admin_meta_boxes.load_billing ) ) {

				// Get user ID to load data for
				var user_id = $( '#customer_user' ).val();

				if ( ! user_id ) {
					window.alert( woocommerce_admin_meta_boxes.no_customer_selected );
					return false;
				}

				var data = {
					user_id : user_id,
					action  : 'woocommerce_get_customer_details',
					security: woocommerce_admin_meta_boxes.get_customer_details_nonce
				};

				$( this ).closest( 'div.order_data_column' ).block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});

				$('#_shipping_city').val('').empty().append('<option value="">'+local.labelSelectCity+'</option>'); 
				$('#_shipping_district').val('').empty().append('<option value="">'+local.labelSelectDistrict+'</option>'); 

				$.ajax({
					url: woocommerce_admin_meta_boxes.ajax_url,
					data: data,
					type: 'POST',
					success: function( response ) {
						if ( response && response.shipping ) {
							if ( response.shipping.state_options ) {
								var html = '';
								for ( var i in response.shipping.state_options ) {
									html += '<option value="' + i + '">' + response.shipping.state_options[i] + '</option>';
								}
								$( '#_shipping_state' ).html(html);
							}
							if ( response.shipping.city_options ) {
								var html = '';
								for ( var i in response.shipping.city_options ) {
									html += '<option value="' + i + '">' + response.shipping.city_options[i] + '</option>';
								}
								$( '#_shipping_city' ).html(html);
							}
							if ( response.shipping.district_options ) {
								var html = '';
								for ( var i in response.shipping.district_options ) {
									html += '<option value="' + i + '">' + response.shipping.district_options[i] + '</option>';
								}
								$( '#_shipping_district' ).html(html);
							}
							$.each( response.shipping, function( key, data ) {
								$( '#_shipping_' + key ).val( data );
							});
						}
						$( 'div.order_data_column' ).unblock();
					}
				});
			}
			return false;
		},

		copy_billing_to_shipping: function() {
			if ( window.confirm( woocommerce_admin_meta_boxes.copy_billing ) ) {
				$('#_shipping_state').html( $('#_billing_state').html() );
				$('#_shipping_city').html( $('#_billing_city').html() );
				$('#_shipping_district').html( $('#_billing_district').html() );
				$('.order_data_column [name^="_billing_"]').each( function() {
					var input_name = $(this).attr('name');
					input_name     = input_name.replace( '_billing_', '_shipping_' );
					$( '#' + input_name ).val( $(this).val() );
				});
			}
			return false;
		},

		add_shipping: function() {
			if ( 'ID' !== $('#_shipping_country').val() ) {
				alert(local.labelOnlyIndonesia);
				return false;
			}
			if ( local.enableDistrict ) {
				var destination = $('#_shipping_district').val();
			} else {
				var destination = $('#_shipping_city').val();
			}
			if ( destination ) {
				tb_show( local.labelSelectShipping, "#TB_inline?width=500&inlineId=pok-switch-shipping" );
				$('.pok-order-shipping-result .loading').removeClass('hidden');
				$('.pok-order-shipping-result .results').addClass('hidden');
				$('.pok-order-shipping-result .no-result').addClass('hidden');
				$('.pok-order-shipping-result .results tbody').html('');
				var order_id = $(this).data('order-id');
				$.ajax({
					url: ajaxurl,
					type: "POST",
					data: {
						action : 'pok_get_cost',
						destination : destination,
						order_id : order_id,
						pok_action : pok_nonces.get_cost
					},
					dataType:'json',
					cache: false,
					success: function(arr){
						if ( 0 !== arr.length ) {
							var options = '';
						  	$('.pok-order-shipping-result .loading').addClass('hidden');
						  	$('.pok-order-shipping-result .no-result').addClass('hidden');
						  	$.each(arr, function(key,value) {
						  		options += "<tr class='pok-add-shipping-option' data-name='" + value.label + "' data-cost='" + value.cost + "' data-meta='" + JSON.stringify(value.meta) + "'><td class='courier'>" + value.courier_name + "</td><td class='service'>" + value.meta.service + "</td><td class='etd'>" + value.meta.etd + "</td><td class='cost'>" + value.cost_display + "</td></tr>";
							});
							$('.pok-order-shipping-result .results tbody').html(options);
							$('.pok-order-shipping-result .results').removeClass('hidden');
						} else {
							$('.pok-order-shipping-result .loading').addClass('hidden');
							$('.pok-order-shipping-result .no-result').removeClass('hidden');
							$('.pok-order-shipping-result .results').addClass('hidden');
						}
					},
					error: function(err) {
						console.log(err);
					}
				});
			} else {
				tb_remove();
				if ( local.enableDistrict ) {
					alert(local.labelNoDistrict);
				} else {
					alert(local.labelNoCity);
				}
				return false;
			}
		},

		switch_shipping: function() {
			if ( 'ID' !== $('#_shipping_country').val() ) {
				alert(local.labelOnlyIndonesia);
				return false;
			}
			if ( local.enableDistrict ) {
				var destination = $('#_shipping_district').val();
			} else {
				var destination = $('#_shipping_city').val();
			}
			if ( destination ) {
				tb_show( local.labelSelectShipping, "#TB_inline?width=500&inlineId=pok-switch-shipping" );
				$('.pok-order-shipping-result .loading').removeClass('hidden');
				$('.pok-order-shipping-result .results').addClass('hidden');
				$('.pok-order-shipping-result .no-result').addClass('hidden');
				$('.pok-order-shipping-result .results tbody').html('');
				var order_id = $(this).data('order-id');
				var item_id  = $(this).data('id');
				var weight   = $(this).data('weight');
				var origin   = $(this).data('origin');
				$.ajax({
					url: ajaxurl,
					type: "POST",
					data: {
						action : 'pok_get_cost',
						destination : destination,
						order_id : order_id,
						weight : weight,
						origin : origin,
						pok_action : pok_nonces.get_cost
					},
					dataType:'json',
					cache: false,
					success: function(arr){
						if ( 0 !== arr.length ) {
							var options = '';
						  	$('.pok-order-shipping-result .loading').addClass('hidden');
						  	$('.pok-order-shipping-result .no-result').addClass('hidden');
						  	$.each(arr, function(key,value) {
						  		options += "<tr class='pok-switch-shipping-option' data-name='" + value.label + "' data-id='" + item_id + "' data-cost='" + value.cost + "' data-meta='" + JSON.stringify(value.meta) + "'><td class='courier'>" + value.courier_name + "</td><td class='service'>" + value.meta.service + "</td><td class='etd'>" + value.meta.etd + "</td><td class='cost'>" + value.cost_display + "</td></tr>";
							});
							$('.pok-order-shipping-result .results tbody').html(options);
							$('.pok-order-shipping-result .results').removeClass('hidden');
						} else {
							$('.pok-order-shipping-result .loading').addClass('hidden');
							$('.pok-order-shipping-result .no-result').removeClass('hidden');
							$('.pok-order-shipping-result .results').addClass('hidden');
						}
					},
					error: function(err) {
						console.log(err);
					}
				});
			} else {
				tb_remove();
				if ( local.enableDistrict ) {
					alert(local.labelNoDistrict);
				} else {
					alert(local.labelNoCity);
				}
				return false;
			}
		},

		insert_shipping: function() {
			tb_remove();
			pok_meta_box_order.block();
			var label 	= $(this).data('name');
			var cost 	= $(this).data('cost');
			var meta    = $(this).data('meta');
			var order_id = $('#post_ID').val();
			$.ajax({
				url: ajaxurl,
				data: {
					action : 'pok_insert_order_shipping',
					label : label,
					cost : cost,
					order_id : order_id,
					meta : meta,
					pok_action : pok_nonces.set_order_shipping
				},
				type: 'POST',
				success: function( response ) {
					if ( response.success ) {
						$( 'table.woocommerce_order_items tbody#order_shipping_line_items' ).append( response.data.html );
					} else {
						window.alert( response.data.error );
					}
					pok_meta_box_order.unblock();
				}
			});
		},

		change_shipping: function() {
			tb_remove();
			pok_meta_box_order.block();
			var label 	= $(this).data('name');
			var cost 	= $(this).data('cost');
			var meta    = $(this).data('meta');
			var item_id = $(this).data('id');
			var order_id = $('#post_ID').val();
			$.ajax({
				url: ajaxurl,
				data: {
					action : 'pok_change_order_shipping',
					label : label,
					cost : cost,
					order_id : order_id,
					meta : meta,
					item_id : item_id,
					pok_action : pok_nonces.set_order_shipping
				},
				type: 'POST',
				success: function( response ) {
					if ( response.success ) {
						$( 'table.woocommerce_order_items tbody#order_shipping_line_items [data-order_item_id="'+item_id+'"]' ).replaceWith( response.data.html );
					} else {
						window.alert( response.data.error );
					}
					pok_meta_box_order.unblock();
				}
			});
		},

		block: function() {
			$( '#woocommerce-order-items' ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},

		unblock: function() {
			$( '#woocommerce-order-items' ).unblock();
		},
	}

	$( '#customer_user' ).on( 'change', function() {
		$( 'a.edit_address' ).click();
		$( 'div.order_data_column' ).block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		pok_meta_box_order.load_billing( true );
		pok_meta_box_order.load_shipping( true );
	} );
	$( '#woocommerce-order-items').on( 'click', '.add-order-ongkir', pok_meta_box_order.add_shipping );
	$( '#woocommerce-order-items').on( 'click', '.switch-order-ongkir', pok_meta_box_order.switch_shipping );
	$( 'body' ).on( 'click', '.pok-order-shipping-result .pok-add-shipping-option', pok_meta_box_order.insert_shipping );
	$( 'body' ).on( 'click', '.pok-order-shipping-result .pok-switch-shipping-option', pok_meta_box_order.change_shipping );
	$( '#order_data' ).on('click', 'a.pok_load_customer_billing', pok_meta_box_order.load_billing );
	$( '#order_data' ).on('click', 'a.pok_load_customer_shipping', pok_meta_box_order.load_shipping );
	$( '#order_data' ).on('click', 'a.pok_billing-same-as-shipping', pok_meta_box_order.copy_billing_to_shipping );
	$( 'a.load_customer_billing' ).addClass('pok_load_customer_billing').removeClass('load_customer_billing');
	$( 'a.load_customer_shipping' ).addClass('pok_load_customer_shipping').removeClass('load_customer_shipping');
	$( 'a.billing-same-as-shipping' ).addClass('pok_billing-same-as-shipping').removeClass('billing-same-as-shipping');

	$( '#order_data .select2' ).select2();

})(jQuery);