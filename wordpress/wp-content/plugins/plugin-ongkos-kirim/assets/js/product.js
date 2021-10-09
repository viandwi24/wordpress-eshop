jQuery(function($) {

	var pok_result_wrapper = $('.pok-shipping-estimation-result-wrapper');

	function pok_check_shipping_estimation( destination ) {
		var product_id 	= $('.pok_shipping_product').val();
		var qty 		= $('.pok_shipping_qty').val();
		var destination = $('.pok_check_shipping').val();
		var origin 		= $('.pok_shipping_origin').val();

		if ( ! qty ) {
			pok_result_wrapper.html( '<p>' + pok.insertQty + '</p>' );
		} else if ( '0' === destination ) {
			pok_result_wrapper.html( '<p>' + pok.selectDestination + '</p>' );
		} else {
			pok_result_wrapper.html( '<p>' + pok.loading + '</p>' );
			$.ajax({
				url: pok.ajaxurl,
				type: "POST",
				data: {
					action : 'pok_get_estimated_cost',
					destination : destination,
					product_id : product_id,
					origin : origin,
					qty : qty,
					pok_action : pok_nonces.get_cost
				},
				dataType:'json',
				cache: false,
				success: function(result){
				  	pok_result_wrapper.html(result.html);
				},
				error: function(err) {
					console.log(err);
				}
			});
		}
	}

	$('.pok_check_shipping, .pok_shipping_qty').on('change', function() {
		pok_check_shipping_estimation();
	});

	$('[href="#tab-shipping_estimation"]').on('click', function() {
		pok_check_shipping_estimation();
	});

	// $('.init-select2').each(function() {
	// 	$(this).select2();
	// });
	
	$( '.select2-ajax' ).each(function() {
		var action 	= $(this).data('action');
		var phrase	= $(this).val();
		var nonce 	= $(this).data('nonce');
		$(this).select2({
			ajax: {
				url: pok.ajaxurl,
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

	$('#select_province').on( 'change', function() {
		var province_id = $(this).val();
		pok_load_city_list( province_id, 'custom' );
	} );

	$('#select_city').on( 'change', function() {
		if ( pok.enableDistrict ) {
			var city_id = $(this).val();
			pok_load_district_list( city_id, 'custom' );
		}
	});

	function pok_load_city_list( province_id ) {
		$('#select_city, #select_district').prop('disabled',true);
		var arrCity  = '<option value="0">' + pok.labelSelectCity + '</option>';
	  	var arrDistrict  = '<option value="0">' + pok.labelSelectDistrict + '</option>';
		if ( '0' !== province_id && '' !== province_id ) {
			$.ajax({
				url: pok.ajaxurl,
				type: "POST",
				data: {
					action : 'pok_get_list_city',
					province_id : province_id,
					pok_action : pok_nonces.get_list_city
				},
				dataType:'json',
				cache: false,
				success: function(arr){
				  	var selectList = '';
				  	$('#select_city').val('').empty().append(arrCity);
					$('#select_district').val('').empty().append(arrDistrict); 
				  	$.each(arr, function(key,value) {
						var data = {};
						arrCity += '<option value='+ key + '>'+ value +'</option>';
					});
					$('#select_city, #select_district').prop('disabled',false);
					$('#select_city').html(arrCity).trigger('setvalue').trigger('change');
				},
				error: function(err) {
					console.log(err);
				}
			});
		} else {
			$('#select_city').prop('disabled',false).html(arrCity).trigger('change');
			$('#select_district').prop('disabled',false).html(arrDistrict);
		}
	}

	function pok_load_district_list( city_id ) {
		$('#select_district').prop('disabled',true);
	  	var arrDistrict  = '<option value="0">' + pok.labelSelectDistrict + '</option>';
		if ( '0' !== city_id && '' !== city_id ) {
			$.ajax({
				url: pok.ajaxurl,
				type: "POST",
				data: {
					action : 'pok_get_list_district',
					city_id : city_id,
					pok_action : pok_nonces.get_list_district
				},
				dataType:'json',
				cache: false,
				success: function(arr){
				  	var selectList = '';
				  	$('#select_district').val('').empty().append(arrDistrict); 
				  	$.each(arr, function(key,value) {
						var data = {};
						arrDistrict += '<option value='+ key + '>'+ value +'</option>';
					});
					$('#select_district').html(arrDistrict).trigger('setvalue').prop('disabled',false);
			  	}
			});
		} else {
			$('#select_district').html(arrDistrict).prop('disabled',false);
		}
	}

})
