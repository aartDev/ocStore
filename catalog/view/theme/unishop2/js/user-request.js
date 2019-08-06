$(function() {	
	if(uniJsVars.notify) {
		var notify = function() {
			$('.add_to_cart.disabled').each(function() {
				var p_id = Number($(this).attr('class').replace(/\D+/g,''));
				$(this).unbind('click').attr('onclick', 'uniRequestOpen("'+uniJsVars.notify_text+'", '+p_id+');').removeAttr('disabled').css('cursor', 'pointer');
			});
		};
	
		notify();
	
		$(document).ajaxStop(function() {
			setTimeout(function() { 
				notify();
			}, 300);
		});
	}
});

function uniRequestOpen(reason, id) {
	if(typeof(reason) == 'undefined') reason = '';
	if(typeof(id) == 'undefined') id = '';
	
	uniAddCss('catalog/view/theme/unishop2/stylesheet/request.css');
	uniAddJs('catalog/view/theme/unishop2/js/jquery.maskedinput.min.js');
	
	$.ajax({
		url:'index.php?route=extension/module/uni_request',
		type:'post',
		data:{'reason':reason, 'id':id, 'flag':true},
		dataType:'html',
		success: function(data) {			
			$('html body').append(data);
			$('#modal-request-form').addClass(uniJsVars.popup_effect_in).modal('show');
		}
	});
}

function uniRequestSend() {
	var form = '#modal-request-form';
	
	$.ajax({
		url: 'index.php?route=extension/module/uni_request/mail',
		type: 'post',
		data: $(form+' input, '+form+' textarea').serialize()+'&location='+encodeURIComponent(window.location.href),
		dataType: 'json',
		beforeSend: function() {
			$('.callback_button').button('loading');
		},
		complete: function() {
			$('.callback_button').button('reset');
		},
		success: function(json) {			
			$(form+' .text-danger').remove();

			if (json['error']) {
				for (i in json['error']) {
					form_error(form, i);
				}
				
				uniFlyAlert('danger', json['error']);
			}
			
			if (json['success']) {
				if(uniJsVars.callback_metric_id && uniJsVars.callback_metric_target) {
					new Function('yaCounter'+uniJsVars.callback_metric_id+'.reachGoal(\''+uniJsVars.callback_metric_target+'\')')();
				}
			
				if(uniJsVars.callback_analityc_category && uniJsVars.callback_analityc_action) {
					ga('send', 'event', uniJsVars.callback_analityc_category, uniJsVars.callback_analityc_action);
				}
				
				$('.callback').html($('<div class="callback_success">'+json['success']+'</div>').fadeIn());
			}
		}
	});
}