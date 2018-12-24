jQuery(document).ready(function($){
	//$.blockUI();
	
	jQuery(document.body).on('change', 'td.easyShipping select#zone_name', function(){
		$("table.wc-shipping-zone-settings").block({message:null,overlayCSS:{background:"#fff",opacity:.6}});
		var cnt = jQuery(this).val();
		var output = '';
		 $.ajax({
            type : 'post',
            dataType: 'json',
            data : {
                'cnt'              : cnt,
                'action'           : 'getContryState' 
            },
            url : easyAjax,
            success:function(data){
            	console.log(data);
                if(data.message == 'success'){
                	$("table.wc-shipping-zone-settings").unblock();
                	$.each(data.state, function(k,v){
                		output+='<option value="'+k+'">'+v+'</option>';
                	});
                	jQuery('select#zone_locations').html(output);
                }
            }
        });
	}); // End select#zone_name

	// Enable save button
	jQuery(document.body).on('keyup', 'input#delivery_area', function(){
		var thisVal = jQuery(this).val();
		jQuery('.wc-shipping-zone-name').text(thisVal);
		if(thisVal != ''){
 			$('button#easy_submit').prop('disabled', false);
		}else{
			$('button#easy_submit').prop('disabled', true);
		}
	}); // End input#delivery_area


	//====================== S A V E  D A T A ========================//
	jQuery(document.body).on('click', 'button#easy_submit', function(e){
		e.preventDefault();
		$("table.wc-shipping-zone-settings").block({message:null,overlayCSS:{background:"#fff",opacity:.6}});
		var formData = {
			country_name: 	jQuery('select[name="country_name"]').val(),
			state: 			jQuery('select[name="zone_locations"]').val(),
			city: 			jQuery('input[name="city"]').val(),
			delivery_area: 	jQuery('input[name="delivery_area"]').val(),
			zipcode: 		jQuery('input[name="zipcode"]').val(),
			min_amount: 	jQuery('input[name="min_amount"]').val(),
			max_amount: 	jQuery('input[name="max_amount"]').val(),
			charge: 		jQuery('input[name="delivery_charge"]').val(),
			action: 		'saveEasyData'
		};

		$.ajax({
            type : 'post',
            dataType: 'json',
            data : formData,
            url : easyAjax,
            success:function(data){
            	console.log(data);
                if(data.message == 'success'){
                	$("table.wc-shipping-zone-settings").unblock();
                }
            }
        });
	});



	//=================== U P D A T E  D A T A ================ *//
	jQuery(document.body).on('click', 'button#easy_update', function(e){
		e.preventDefault();
		$("table.wc-shipping-zone-settings").block({message:null,overlayCSS:{background:"#fff",opacity:.6}});
		var formData = {
			id: 			jQuery(this).data('upid'),
			country_name: 	jQuery('select[name="country_name"]').val(),
			state: 			jQuery('select[name="zone_locations"]').val(),
			city: 			jQuery('input[name="city"]').val(),
			delivery_area: 	jQuery('input[name="delivery_area"]').val(),
			zipcode: 		jQuery('input[name="zipcode"]').val(),
			min_amount: 	jQuery('input[name="min_amount"]').val(),
			max_amount: 	jQuery('input[name="max_amount"]').val(),
			charge: 		jQuery('input[name="delivery_charge"]').val(),
			action: 		'saveEasyData'
		};

		$.ajax({
            type : 'post',
            dataType: 'json',
            data : formData,
            url : easyAjax,
            success:function(data){
            	console.log(data);
                if(data.message == 'success'){
                	$("table.wc-shipping-zone-settings").unblock();
                }
            }
        });
	});


	//=================== D E L E T E  D A T A ================ *//
	jQuery(document.body).on('click', 'a.easy_shipping_d', function(e){
		e.preventDefault();
		$("table.wc-shipping-zones").block({message:null,overlayCSS:{background:"#fff",opacity:.6}});
		var tr = jQuery(this).closest('tr');
		var formData = {
			id: 			jQuery(this).closest('tr').data('id'),
			action: 		'deleteEasyData'
		};

		$.ajax({
            type : 'post',
            dataType: 'json',
            data : formData,
            url : easyAjax,
            success:function(data){
                if(data.message == 'success'){
                	tr.remove();
                	$("table.wc-shipping-zones").unblock();
                }
            }
        });
	}); // End 

	//=================== D E L E T E  D A T A ================ *//
	jQuery(document.body).on('click', 'button#active_easy_save', function(e){
		e.preventDefault();
		$("table.wc-shipping-zones").block({message:null,overlayCSS:{background:"#fff",opacity:.6}});
		
		var formData = {
			active: 		(jQuery('input[name="active_easy"]').is(':checked'))?1:0,
			action: 		'activeShippingMethod'
		};

		$.ajax({
            type : 'post',
            dataType: 'json',
            data : formData,
            url : easyAjax,
            success:function(data){
            	
                if(data.message == 'success'){
                	$("table.wc-shipping-zones").unblock();
                	window.location.reload();
                }
            }
        });
	}); // End 


	/////================= Location Based Price ====================//
	jQuery(document).on('change', 'select#cityprice', function(){
		var thisText = jQuery(this).find('option:selected').text();
		var thisVal = $('select[name="cityprice"]').val();
		var output = '<p class="form-field singleLocationPrice"><label for="elp_price">'+thisText+' ('+woocommerce_admin_meta_boxes.currency_format_symbol+')</label><input type="number" class="short" style="" step="0.01" min="0" name="_elp_price['+thisVal+']" id="_elp_price_'+thisVal+'" value="" placeholder=""><span class="deletelocationPrice"><span class="dashicons dashicons-no-alt"></span></span></p>';
		jQuery(output).insertAfter(jQuery(this).closest('p.cityprice_field'));
		jQuery(this).find('option:selected').remove();
	});
	

	//==================== Delete Location based price ==================//
	jQuery(document.body).on('click', 'span.deletelocationPrice', function(){
		$("#woocommerce-product-data").block({message:null,overlayCSS:{background:"#fff",opacity:.6}});
		var tax_id = jQuery(this).data('tax_id');
		var name = jQuery(this).data('name');
		var newOp = '<option value="'+tax_id+'">'+name+'</option>';
		var select = jQuery('select#cityprice');
		var thisItem = jQuery(this);

		if(typeof tax_id !== 'undefined' ){
		var fdata = {
			post_id: 	woocommerce_admin_meta_boxes.post_id,
			tax_id: 	tax_id,
			action: 	'deleteLocaionPrice'
		};
		$.ajax({
            type : 'post',
            dataType: 'json',
            data : fdata,
            url : easyAjax,
            success:function(data){
            	console.log(data);
                if(data.message == 'success'){
                	$("#woocommerce-product-data").unblock();
                	select.append(newOp);
                	thisItem.closest('p.singleLocationPrice').remove();
                }
            }
        });
		}else{
			$("#woocommerce-product-data").unblock();
			thisItem.closest('p.singleLocationPrice').remove();
		}

	});


	/*
	* Save Easy Settings
	*/
	$(document.body).on('click', 'button#easy_settings_save', function(e){
		e.preventDefault();
		$(".woocommerce table.form-table").block({message:null,overlayCSS:{background:"#fff",opacity:.6}});
		var v_popup2 	= ($('input[name="v_popup2"]').is(':checked'))?'yes':'no';
		var v_country 	= ($('input[name="v_country"]').is(':checked'))?'yes':'no';
		var v_state 	= ($('input[name="v_state"]').is(':checked'))?'yes':'no';
		var v_city 		= ($('input[name="v_city"]').is(':checked'))?'yes':'no';

		var fromData = {
			v_popup2:v_popup2,
			v_country:v_country,
			v_state:v_state,
			v_city:v_city,
			action:'updateEasySettings'
		};
		$.ajax({
            type : 'post',
            dataType: 'json',
            data : fromData,
            url : easyAjax,
            success:function(data){
            	console.log(data);
                if(data.message == 'success'){
                	$(".woocommerce table.form-table").unblock();

                }
            }
        });

	});



}); // End document ready