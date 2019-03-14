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

	jQuery(document.body).on('click', 'table.wc-shipping-zones.lists a.edit', function(e){
		e.preventDefault();
		var name = jQuery(this).data('name');
		var html = 	'<div class="form-group">'
					+'<input class="form-control" type="text" value="" name="'+name+'" />';
					+'</div>';
	});

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
		if(easy.entry_type == 'new'){
		var deliveryAreas = [];	
			$.each(jQuery('.singleDeliverArea'), function(t, m){
				var index = jQuery('.singleDeliverArea').index(jQuery(this));		
				deliveryAreas[index] = {};
				deliveryAreas[index]['delivery_area'] = jQuery(this).find('input[name="delivery_area"]').val();
				deliveryAreas[index]['zipcode'] = jQuery(this).find('input[name="zipcode"]').val();
				deliveryAreas[index]['min_amount'] = jQuery(this).find('input[name="min_amount"]').val();
				deliveryAreas[index]['max_amount'] = jQuery(this).find('input[name="max_amount"]').val();
				deliveryAreas[index]['charge'] = jQuery(this).find('input[name="delivery_charge"]').val();
			});
			var deliverJsonArray = JSON.stringify(deliveryAreas);
			var formData = {
				country_name: 	jQuery('select[name="country_name"]').val(),
				state: 			jQuery('select[name="zone_locations"]').val(),
				city: 			jQuery('input[name="city"]').val(),
				delivery_area: 	deliverJsonArray,
				action: 		'saveEasyData'
			};
			//console.log(formData);
		}

		$.ajax({
            type : 'post',
            dataType: 'json',
            data : formData,
            url : easyAjax,
            success:function(data){
			//	console.log(data);
                if(data.message == 'success'){
                	$("table.wc-shipping-zone-settings").unblock();
                	window.location.replace(easy.easy_page);
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
				//console.log(data);
                if(data.message == 'success'){
                	$("table.wc-shipping-zone-settings").unblock();
                	window.location.replace(easy.easy_page);
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
		var output = '<div class="locationpricesinglewrap"><p class="form-field singleLocationPrice">';
		output +='<label for="elp_price"><strong>'+thisText+'</strong> (Regular price)</label><input type="number" class="short" style="" step="0.01" min="0" name="_elp_price['+thisVal+']" id="_elp_price_'+thisVal+'" value="" placeholder=""><span class="deletelocationPrice"><span class="dashicons dashicons-no-alt"></span></span>';
		output +='</p>';
		output += '<p class="form-field singleRegularLocationPrice">';
		output +='<label for="elp_sales_price"><strong>'+thisText+'</strong> (Sales price)</label><input type="number" class="short" style="" step="0.01" min="0" name="_elp_sales_price['+thisVal+']" id="_elp_sales_price_'+thisVal+'" value="" placeholder="">';
		output +='</p></div>';
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
                	thisItem.closest('div.locationpricesinglewrap').remove();
                }
            }
        });
		}else{
			$("#woocommerce-product-data").unblock();
			thisItem.closest('div.locationpricesinglewrap').remove();
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
            	//console.log(data);
                if(data.message == 'success'){
                	$(".woocommerce table.form-table").unblock();

                }
            }
        });

	});


	/*
	* Easy Admin Seach on list
	*/
	jQuery(document.body).on('click', 'button#easy_earch_button', function(e){
		e.preventDefault();
		$(".woocommerce table.wc-shipping-zones.widefat").block({message:null,overlayCSS:{background:"#fff",opacity:.6}});
		var serchvar  = $('input#search_easy').val();
		var fromData = {
			action: 'adminSearchDB',
			search: serchvar
		};
		var htmle = '';
		$.ajax({
            type : 'post',
            dataType: 'json',
            data : fromData,
            url : easyAjax,
            success:function(data){
            	console.log(data);
                if(data.message == 'success'){
                	$(".woocommerce table.wc-shipping-zones.widefat").unblock();
					$.each(data.results, function(v, k){
						console.log('K '+ k.s_id);
						htmle +='<tr data-id="'+k.s_id+'"><td width="1%" class="wc-shipping-zone-select">'
						+'<input value="'+k.s_id+'" type="checkbox" name="selecteasy[]">'
					+'</td><td class="wc-shipping-zone-name">'
						+'<a href="'+easy.easy_page+'&zone_id='+k.s_id+'">'+k.delivery_area+'</a>'
						+'<div class="row-actions">'
							+'<a href="'+easy.easy_page+'&zone_id='+k.s_id+'">Edit</a> | <a href="#" class="easy_shipping_d wc-shipping-zone-delete">Delete</a>'
						+'</div>'
						+'</td><td class="wc-shipping-city">'+k.city+'</td>'
						+'<td class="wc-shipping-state">'+k.state+'</td>'
						+'<td width="1%" class="wc-shipping-min">'+easy.wc_currency_samble+k.min_amount+'</td>'
						+'<td width="1%" class="wc-shipping-max">'+easy.wc_currency_samble+k.max_amount+'</td>'
						+'<td class="wc-shipping-charge">'+easy.wc_currency_samble+k.charge+'</td></tr>'
					});

					jQuery('.woocommerce table.wc-shipping-zones.widefat').find('tbody').slideUp('slow', function(){
						jQuery('.woocommerce table.wc-shipping-zones.widefat').find('tbody').html(htmle);
						jQuery('.woocommerce table.wc-shipping-zones.widefat').find('tbody').slideDown('slow');
					});
                }
            }
        });
		
	});

	/*
	* All checkbox select using single click
	*/
	jQuery(document.body).on('click', 'input#selectalleasy', function(){
		if($(this).is(':checked')){
			$('input[name="selecteasy[]').click();
			$('input[name="selectalleasy"]').prop('checked', true);
		}else{
			$('input[name="selecteasy[]"], input[name="selectalleasy"]').prop('checked', false);
			$('div.editarea_wrap').remove();
		}
	});

	jQuery(document.body).on('change', 'input[name="selecteasy[]"]', function(){
		$('div.editarea_wrap').remove();
		var output = '<div class="editarea_wrap">'
			+'<div id="edit_multiple_area">'
			+'<label for="change_area">Change Neighborhoods</label>'
			+'<input type="text" id="change_area" name="change_area" class="form-control" />'
			+'<button disabled type="submit" id="actionNeighborChange" class="button button-primary">Change</button>'
			+'</div></div>';
		if($('input[name="selecteasy[]"]:checkbox:checked').length > 0){
			$(output).insertAfter($(this).closest('table'));
		}
	});

	/* 
	* Active button if multiple change text
	*/
	jQuery(document.body).on('keyup', 'input#change_area', function(){
		if($(this).val() != ''){
			$('button#actionNeighborChange').prop('disabled', false);
		}else{
			$('button#actionNeighborChange').prop('disabled', true);
		}
	});
	/*
	* Action change naiborhood
	*/
	jQuery(document.body).on('click', 'button#actionNeighborChange', function(e){
		e.preventDefault();
		$(".woocommerce table.wc-shipping-zones.widefat").block({message:null,overlayCSS:{background:"#fff",opacity:.6}});
		var val = [];
        $('input[name="selecteasy[]"]:checkbox:checked').each(function(i){
          val[i] = $(this).val();
		});
		var changedVal = $('input#change_area').val();
		var fromData = {
			action: 'changeneighborAreaMultiple',
			dbids: val, 
			areaname: changedVal 
		};
		$.ajax({
            type : 'post',
            dataType: 'json',
            data : fromData,
            url : easyAjax,
            success:function(data){
            	//console.log(data);
                if(data.message == 'success'){
                	$(".woocommerce table.wc-shipping-zones.widefat").unblock();
					window.location.reload();
                }
            }
        });
	});

	/*
	* Admin Shipping list
	*/
	if($('#dataTable').length > 0){
		$('#dataTable').DataTable({
			"columnDefs": [
				{ "orderable": false, "targets": 0 }
			],
			"order": [[ 1, "asc" ]]
		});
	}

	/*
	* Multiple zipcode fields
	*/
	jQuery(document.body).on('click', 'button.addzipcode', function(){
		var index = jQuery('.singleDeliverArea').index(jQuery(this).closest('.singleDeliverArea'));
		var output = '<li><input class="mzipcode" type="text" value="" name="zipcodes['+index+']"/><span class="delete dashicons dashicons-dismiss"></span></li>';
		jQuery(this).closest('div.addbuttons').prev('ul.ziplist').append(output);
	});

	/*
	* Delete single zip code box
	*/
	jQuery(document.body).on('click', 'table.wc-shipping-zone-settings .singleDeliverArea ul.ziplist li span.delete, table.wc-shipping-zone-settings .singleDeliverArea span.delete.area', function(){
		if(!jQuery(this).hasClass('area')) jQuery(this).closest('li').remove();
		if(jQuery(this).hasClass('area')) jQuery(this).closest('.singleDeliverArea').remove();
	});

	/*
	* Add Extra Delivery Area
	*/
	jQuery(document.body).on('click', 'button.addmorearea', function(){
		var outputa = '<div class="singleDeliverArea"><span class="delete area dashicons dashicons-dismiss"></span>'
		+'<input type="text" data-attribute="delivery_area" id="delivery_area" name="delivery_area" placeholder="Neighborhood..." value="" class="wc-shipping-zone-region-select">'
		+'<input type="text" data-attribute="delivery_zipcode" id="delivery_zipcode" name="zipcode" placeholder="000000, 000000"  value="" class="wc-shipping-zone-region-select" />'
		+'<input type="number" data-attribute="min_amount" step="0.01" id="min_amount" name="min_amount" placeholder="Min Amount "  value="" class="wc-shipping-zone-region-select" />'
		+'<input type="number" data-attribute="max_amount" step="0.01" id="max_amount" name="max_amount" placeholder="Max Amount "  value="" class="wc-shipping-zone-region-select" />'
		+'<input type="number" data-attribute="delivery_charge" id="delivery_charge" step="0.01" name="delivery_charge" placeholder="Delivery Charge"  value="" class="wc-shipping-zone-region-select" />'
		+'</div>';
		jQuery(outputa).insertBefore(jQuery(this).closest('div.addbuttons'));
	});

}); // End document ready