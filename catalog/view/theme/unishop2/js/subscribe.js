$(function() {
	$('#subscribe').on('click', 'button', function() {
		
		var form = $(this).closest('#subscribe'), data = form.find('input').serialize(), btn = form.find('button');
	
		$('.text-danger, .tooltip').remove();
		
		$.ajax({
			url:'index.php?route=extension/module/uni_subscribe/add',
			type:'post',
			data:data,
			dataType:'json',
			beforeSend: function() {
				btn.button('loading');
			},
			complete: function() {
				btn.button('reset');
			},
			success: function(json) {
				if (json['error']) {
					//form_error('#subscribe', 'email', json['error']);
					uniFlyAlert('danger', json['error']);
				}
				
				if (json['alert']) {
					$('#subscribe .subscribe-input > div').addClass('show-pass');
					$('#subscribe .subscribe-input input').attr('disabled', false);
				} else {
					$('#subscribe .email, .pass').removeClass('show-pass');
				}

				if (json['success']) {
					uniModalWindow('modal-subscribe-success', 'sm', json['success_title'], json['success']);
				}
			}
		});
	});
});