function quick_order(id) {
	uniAddCss('catalog/view/theme/unishop2/stylesheet/quick_order.css');
	uniAddJs('catalog/view/theme/unishop2/js/jquery.maskedinput.min.js');
	
	$.ajax({
		url:'index.php?route=extension/module/uni_quick_order',
		type:'post',
		data:{'id':id, 'flag':true},
		dataType:'html',
		success:function(data) {
			$('html body').append(data);
			$('#modal-quick-order').addClass(uniJsVars.popup_effect_in).modal('show');
		}
	});
}

function uniQuickOrderAdd() {
	var form = '#modal-quick-order';
	
	$.ajax({
		url: 'index.php?route=extension/module/uni_quick_order/add',
		type: 'post',
		data: $(form+' input, '+form+' textarea, '+form+' select').serialize(),
		dataType: 'json',
		beforeSend: function() {
			$(form+' .add_to_cart').button('loading');
		},
		complete: function() {
			$(form+' .add_to_cart').button('reset');
		},
		success: function(json) {
			$('.text-danger').remove();
				
			$('.form-group').removeClass('has-error');

			if (json['error']) {
				if (json['error']['option']) {
					for (i in json['error']['option']) {							
						
						var element = $('#quick_order #input-option' + i.replace('_', '-'));
						
						if (element.parent().hasClass('input-group')) {
							element.parent().after('<div class="text-danger">'+json['error']['option'][i]+'</div>');
						} else {
							element.after('<div class="text-danger">'+json['error']['option'][i]+'</div>');
						}
					}
					
					uniFlyAlert('danger', json['error']['option']);
				}
			}
			
			if (json['error_c']) {
				for (i in json['error_c']) {
					//form_error(form, i, json['error_c'][i]);
				}
				
				uniFlyAlert('danger', json['error_c']);
			}
		
			if (json['success']) {			
				dataLayer.push({
					'ecommerce':{
						'currencyCode':uniJsVars.currency,
						'purchase':{
							'actionField':{
								'id':json['success']['order_id'],
								'goal_id': uniJsVars.quickorder_goal_id
							},
							'products':json['success']['products']
						}
					}
				});
				
				$('#quick_order').html('<div class="row"><div class="col-xs-12">'+json['success']['text']+'</div></div>')
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
	});
}