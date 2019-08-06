$(function() {
	
	$('#module .col-sm-10 .nav').each(function() {
		$(this).find('li:first a').tab('show');
	});
	
	$('.uni-color').each(function() {
		$(this).css('background', '#'+$(this).val());
		
		var bg = $(this).css('background-color').replace(/[^\d,]/g, '').split(',');

		if(bg[0] > 125 && bg[1] > 125 && bg[2] > 125) {
			$(this).css('color', '#000');
		} else {
			$(this).css('color', '#fff');
		}
	});
	
	set_color('#tab-topmenu');
	
	$('.nav-stacked li a').on('click', function() {
		var id = $(this).attr('href');
		set_color(id);
	});
	
	$('.nav-pills li').not('.new').on('click', function() {
		var destination = $('.nav-pills').offset().top-60;
		$('html, body').animate({scrollTop: destination}, 400);
	});
		
	var adm_new_stick = $('input[name="uni_set[adm_new_stick]"]');
		
	if(adm_new_stick.prop('checked')) {
		$('.nav-tabs > li a span').show();
	}
		
	adm_new_stick.on('change', function() {
		if($(this).prop('checked')) {
			$('.nav-tabs > li a span').show();
		} else {
			$('.nav-tabs > li a span').hide();
		}
	});
		
	$(window).scroll(function(){		
		if($(this).scrollTop()>100) {
			if(!$('.scroll_button').length) {
				$('body').append('<div class="scroll_button"></div>');
				$('.btns').clone().appendTo('.scroll_button');
				$('[data-toggle=\'tooltip\']').tooltip({container:'body', placement:'bottom'});
			}
		} else {
			$('.scroll_button').remove();
		}
	});	
		
	$('input[name="uni_set[save_date]"]').val(Date.now());
	
	$('.container-fluid_new > .nav a').on('click', function(e) {
		e.preventDefault();
	
		if (confirm(uni_text_alert)) {
			location = $(this).attr('href');
		}
	});
	
});
	
	var this_url = window.location.search.split('&');
		user_token = this_url[1];
	
	function set_color(data) {
		$(data+' .uni-color').colorpicker({
			format:'hex',
			hexNumberSignPrefix:false
		}).on('changeColor', function(e) {
			$(this).css('background-color', e.color.toString('hex'));
		   
			var bg = e.color.toRGB();

			if(bg['r'] > 125 && bg['g'] > 125 && bg['b'] > 125) {
				$(this).css('color', '#000');
			} else {
				$(this).css('color', '#fff');
			}
		});
	}
	
	function img_or_ico(id, type) {
		if(type == 'img') {
			$('.'+id).find('.img').addClass('selected');
			$('.'+id).find('.ico').removeClass('selected');
		} else {
			$('.'+id).find('.img').removeClass('selected');
			$('.'+id).find('.ico').addClass('selected');
		}
	}
	
	function popup_icons(id) {
		$('.fontawesome-icon-list').load('index.php?route=extension/module/uni_settings/getIconBlock&'+user_token, function() {
			$('#modal-icons-form').modal('show');
		
			$('#modal-icons-form i').on('click', function() {
				var this_class = $(this).attr('class');
			
				$('#'+id).find('i').attr('class', this_class);
				$('#'+id).next().val(this_class);
			
				$('#modal-icons-form').modal('hide');
			});
		});
	}
	
	function addHeaderLinks(lang_id, data) {
		var headerlinks_num = $('#tab-header #headerlinks-'+lang_id+' .input-group').length+1;

		html = '<div class="input-group">';
		html += '<input type="text" name="uni_set['+lang_id+'][headerlinks]['+headerlinks_num+'][title]" value="" placeholder="'+uni_text_title+' #'+headerlinks_num+'" class="form-control" />';
		html += '<input type="text" name="uni_set['+lang_id+'][headerlinks]['+headerlinks_num+'][link]" value="" placeholder="'+uni_text_link+' #'+headerlinks_num+'" class="form-control" />';
		html += '<span class="btn-default" onclick="$(this).parent().next().remove(); $(this).parent().remove();" title="'+uni_text_delete+'"><i class="fa fa-close"></i></span>';
		html += '</div>';
		html += '<div class="infolink">';
		html += '<a onclick="$(this).toggleClass(\'show\');">'+uni_text_article_link+' <i class="fa fa-caret-down"></i></a>';
		html += '<div>';
		html += data;
		html += '</div>';
		html += '</div>';
											
		$('#tab-header #headerlinks-'+lang_id+' > hr').before(html);
	}
	
	function addHeaderLinks2(lang_id, data) {
		var h2_num = $('#tab-additionalmenu .headerlinks2_'+lang_id+' td > .input-group').length+1;
															
		html  = '<tr class="headerlinks2-'+lang_id+'-'+h2_num+'">';
		html += '<td>';
		html += '<a onclick="img_or_ico($(this).parent().parent().attr(\'class\'), \'img\');" class="img selected">'+uni_text_img+'</a>';
		html += '<a onclick="img_or_ico($(this).parent().parent().attr(\'class\'), \'ico\');" class="ico">'+uni_text_icon+'</a>';
		html += '<div class="main-category-icon">';
		html += '<a href="" id="thumb-image-'+lang_id+'-headerlinks2-'+h2_num+'" data-toggle="image" class="img-thumbnail img selected">';
		html += '<img src="'+uni_img_placeholder+'" alt="" title="" data-placeholder="'+uni_img_placeholder+'" />';
		html += '</a>';
		html += '<a id="'+lang_id+'-t-l-'+h2_num+'" onclick="popup_icons($(this).attr(\'id\'))" class="ico">';
		html += '<i class="fa fa-plus-circle"></i>';
		html += '</a>';
		html += '<input type="hidden" name="uni_set['+lang_id+'][headerlinks2]['+h2_num+'][icon]" value="" id="image-'+lang_id+'-headerlinks2-'+h2_num+'" />';
		html += '</div>';
		html += '<input type="hidden" name="uni_set['+lang_id+'][headerlinks2]['+h2_num+'][icon_type]" value="img" class="form-control icon-type" />';
		html += '</td>';
		html += '<td>';
		html += '<div class="input-group">';
		html += '<input type="text" name="uni_set['+lang_id+'][headerlinks2]['+h2_num+'][title]" value="" placeholder="'+uni_text_title+' #'+h2_num+'" class="form-control" />';
		html += '<input type="text" name="uni_set['+lang_id+'][headerlinks2]['+h2_num+'][link]" value="" placeholder="'+uni_text_link+' #'+h2_num+'" class="form-control" />';
		html += '<span class="input-group-btn btn-default"onclick="$(this).parent().parent().parent().remove();" title="'+uni_text_delete+'"><i class="fa fa-close"></i></span>';
		html += '</div>';
		html += '<div class="infolink">';
		html += '<a onclick="$(this).toggleClass(\'show\');">'+uni_text_article_link+' <i class="fa fa-caret-down"></i></a>';
		html += '<div>';
		html += data;
		html += '</div>';
		html += '</div>';
		html += '<div class="submenu">';
		html += '<a onclick="addHeaderLinks2Sub('+lang_id+', '+h2_num+', this);" title="Добавить ссылку второго уровня" data-toggle="tooltip" class="add-sub btn btn-success"><i class="fa fa-plus"></i></a>';
		html += '<label><input type="checkbox" name="uni_set['+lang_id+'][headerlinks2]['+h2_num+'][show_in_cat]" value="1"><span></span>Показывать в главном меню категорий</label>';
		html += '</div>';
		html += '</td>';
		html += '</tr>';
												
		$('#tab-additionalmenu .headerlinks2_'+lang_id).append(html);
	}
	
	function addHeaderLinks2Sub(lang_id, h2_num, data) {
		
		var elem = $(data), h2sub_num = elem.parent().find('.submenu').length+1;
		
		html  = '<div class="submenu">';
		html  += '<div class="input-group">';
		html  += '<i class="fas fa-level-up-alt"></i>';
		html  += '<input type="text" name="uni_set['+lang_id+'][headerlinks2]['+h2_num+'][children]['+h2sub_num+'][name]" value="" placeholder="Заголовок #'+h2sub_num+'" class="form-control" />';
		html  += '<input type="text" name="uni_set['+lang_id+'][headerlinks2]['+h2_num+'][children]['+h2sub_num+'][href]" value="" placeholder="Ссылка #'+h2sub_num+'" class="form-control" />';
		html  += '<span class="input-group-btn btn-default" onclick="$(this).parent().parent().remove();" title="{{entry_delete}}"><i class="fa fa-close"></i></span>';
		html  += '</div>';
		html  += '<a onclick="addHeaderLinks2Sub2('+lang_id+', '+h2_num+', '+h2sub_num+', this);" title="Добавить ссылку третьего уровня" data-toggle="tooltip" class="add-sub btn btn-info"><i class="fa fa-plus"></i></a>';
		html  += '</div>';
		
		elem.before(html);
		
		$('[data-toggle=\'tooltip\']').tooltip({container:'body', trigger:'hover'});
	}
	
	function addHeaderLinks2Sub2(lang_id, h2_num, h2sub_num, data) {
		
		var elem = $(data), h2sub2_num = elem.parent().find('.submenu2').length+1;
		
		html  = '<div class="submenu2">';
		html  += '<div class="input-group">';
		html  += '<i class="fas fa-level-up-alt"></i>';
		html  += '<input type="text" name="uni_set['+lang_id+'][headerlinks2]['+h2_num+'][children]['+h2sub_num+'][children]['+h2sub2_num+'][name]" value="" placeholder="Заголовок #'+h2sub2_num+'" class="form-control" />';
		html  += '<input type="text" name="uni_set['+lang_id+'][headerlinks2]['+h2_num+'][children]['+h2sub_num+'][children]['+h2sub2_num+'][href]" value="" placeholder="Ссылка #'+h2sub2_num+'" class="form-control" />';
		html  += '<span class="input-group-btn btn-default" onclick="$(this).parent().parent().remove();" title="{{entry_delete}}"><i class="fa fa-close"></i></span>';
		html  += '</div>';
		html  += '</div>';
		
		elem.before(html);
		
		$('[data-toggle=\'tooltip\']').tooltip({container:'body', trigger:'hover'});
	}
	
	function addMainPhones(lang_id, data) {
		var mf_num = $('#tab-header .main-phone-'+lang_id+' .input-group').length+1;

		html  = '';
		html  += '<tr class="main-phone-icon-'+lang_id+'-'+mf_num+'">';
		html  += '<td>';
		html  += '<a onclick="img_or_ico($(this).parent().parent().attr(\'class\'), \'img\');" class="selected img">'+uni_text_img+'</a>';
		html  += '<a onclick="img_or_ico($(this).parent().parent().attr(\'class\'), \'ico\');" class="ico">'+uni_text_icon+'</a>';
		html  += '<div class="main-category-icon">';
		html  += '<a href="" id="thumb-image-'+lang_id+'-main-phone-icon-'+mf_num+'" data-toggle="image" class="img-thumbnail img selected">';
		html  += '<img src="'+uni_img_placeholder+'" alt="" title="" data-placeholder="'+uni_img_placeholder+'" />';
		html  += '</a>';
		html  += '<a id="'+lang_id+'-m-f-'+mf_num+'" onclick="popup_icons($(this).attr(\'id\'))" class="ico">';
		html  += '<i class="fa fa-plus-circle"></i>';
		html  += '</a>';
		html  += '<input type="hidden" name="uni_set['+lang_id+'][main_phones]['+mf_num+'][icon]" value="" id="image-'+lang_id+'-main-phone-icon-'+mf_num+'" />';
		html  += '</div>';
		html  += '</td>';
		html  += '<td>';
		html  += '<div class="input-group">';
		html  += '<input type="text" name="uni_set['+lang_id+'][main_phones]['+mf_num+'][text]" value="" placeholder="'+uni_text_mf_text+' #'+mf_num+'" class="form-control" style="width:133.33px" />';
		html  += '<input type="text" name="uni_set['+lang_id+'][main_phones]['+mf_num+'][number]" value="" placeholder="'+uni_text_mf_number+' #'+mf_num+'" class="form-control" style="width:133.33px" />';
		html  += '<select name="uni_set['+lang_id+'][main_phones]['+mf_num+'][type]" class="form-control" style="width:133.33px" >';
		html  += data;
		html  += '</select>';
		html  += '<span class="input-group-btn btn-default" onclick="$(this).parent().parent().parent().remove()" title="'+uni_text_delete+'"><i class="fa fa-close"></i></span>';
		html  += '</div>';
		html  += '</td>';
		html  += '</tr>';

		$('#tab-header .main-phone-'+lang_id).append(html);
	}
	
	function addContacts(lang_id, data) {
		var c_num = $('#tab-header .additional-contacts-'+lang_id+' .input-group').length+1;
														
		html = '<tr class="additional-contacts-icon-'+lang_id+'-'+c_num+'">';
		html += '<td>';
		html += '<a onclick="img_or_ico($(this).parent().parent().attr(\'class\'), \'img\');" class="selected img">'+uni_text_img+'</a>';
		html += '<a onclick="img_or_ico($(this).parent().parent().attr(\'class\'), \'ico\');" class="ico">'+uni_text_icon+'</a>';
		html += '<div class="main-category-icon">';
		html += '<a href="" id="thumb-image-'+lang_id+'-contacts-icon-'+c_num+'" data-toggle="image" class="img-thumbnail img selected">';
		html += '<img src="'+uni_img_placeholder+'" alt="" title="" data-placeholder="'+uni_img_placeholder+'" />';
		html += '</a>';
		html += '<a id="'+lang_id+'-a-c-'+c_num+'" onclick="popup_icons($(this).attr(\'id\'))" class="ico">';
		html += '<i class="fa fa-plus-circle"></i>';
		html += '</a>';
		html += '<input type="hidden" name="uni_set['+lang_id+'][contacts]['+c_num+'][icon]" value="" id="image-'+lang_id+'-contacts-icon-'+c_num+'" />';
		html += '</div>';
		html += '</td>';
		html += '<td>';
		html += '<div class="input-group">';
		html += '<input type="text" name="uni_set['+lang_id+'][contacts]['+c_num+'][number]" value="" placeholder="'+uni_text_mf_number+' #'+c_num+'" class="form-control" />';
		html += '<select name="uni_set['+lang_id+'][contacts]['+c_num+'][type]" class="form-control header-call">';
		html += data;
		html += '</select>';
		html += '<span class="input-group-btn btn-default" onclick="$(this).parent().parent().parent().remove()" title="'+uni_text_delete+'"><i class="fa fa-close"></i></span>';
		html += '</div>';
		html += '</td>';
		html += '</tr>';
		
		$('#tab-header .additional-contacts-'+lang_id).append(html);
	}
	
	function addFooterLinks(lang_id, data) {
		var f_links_num = $('#tab-footer #footerlinks-'+lang_id+' .input-group').length+1;
		
		html = '<div class="input-group">';
		html += '<input type="text" name="uni_set['+lang_id+'][footerlinks]['+f_links_num+'][title]" value="" placeholder="'+uni_text_title+' #'+f_links_num+'" class="form-control" />';
		html += '<input type="text" name="uni_set['+lang_id+'][footerlinks]['+f_links_num+'][link]" value="" placeholder="'+uni_text_link+' #'+f_links_num+'" class="form-control" />';
		html += '<select name="uni_set['+lang_id+'][footerlinks]['+f_links_num+'][column]" class="form-control">';
		html += data;
		html += '</select>';
		html += '<span class="btn-default" onclick="$(this).parent().remove()" title="'+uni_text_delete+'"><i class="fa fa-close"></i></span>';
		html += '</div>';
		
		f_links_num = f_links_num+1;
		
		$('#tab-footer #footerlinks-'+lang_id+' .add-before').before(html);
	}
	
	function addSocials(data) {
		var socials_num = $('#tab-footer .socials .input-group').length+1;

		html = '<div class="input-group">';
		html += '<select name="uni_set[socials]['+socials_num+'][icon]" class="form-control">';
		html += data;
		html += '</select>';
		html += '<input type="text" name="uni_set[socials]['+socials_num+'][link]" value="" placeholder="'+uni_text_link+'" class="form-control" />';
		html += '<span class="btn-default" onclick="$(this).parent().remove()" title="'+uni_text_delete+'"><i class="fa fa-close"></i></span>';
		html += '</div>';
		
		socials_num = socials_num+1;
		
		$('#tab-footer .socials').append(html);
	}
	
	function addProductBanner(lang_id) {
		var product_banner_num = $('#product-banners-'+lang_id+' .input-group').length+1;

		html  = '';
		html += '<div class="input-group">';
		html += '<span class="input-group-addon open_icon" id="'+lang_id+'_p_b_'+product_banner_num+'" onclick="popup_icons($(this).attr(\'id\'))">'+uni_text_icon+':<i class=""></i></span>';
		html += '<input type="hidden" name="uni_set['+lang_id+'][product_banners]['+product_banner_num+'][icon]" value="" class="form-control" />';
		html += '<input type="text" name="uni_set['+lang_id+'][product_banners]['+product_banner_num+'][text]" value="" placeholder="'+uni_text_mf_text+' #'+product_banner_num+'" class="form-control" />';
		html += '<input type="text" name="uni_set['+lang_id+'][product_banners]['+product_banner_num+'][link]" value="" placeholder="'+uni_text_link+' #'+product_banner_num+'" class="form-control" />';
		html += '<span class="btn-default" onclick="$(this).parent().next().remove(); $(this).parent().remove();" title="'+uni_text_delete+'"><i class="fa fa-close"></i></span>';
		html += '</div>';
		html += '<label><input type="checkbox" name="uni_set['+lang_id+'][product_banners]['+product_banner_num+'][link_popup]" value="1" /><span></span>'+uni_text_link_popup+'</label>';
			
		$('#product-banners-'+lang_id+' > hr').before(html);
	}
	
	function save() {
		$('.note-editable').each(function() {
			$(this).parent().parent().prev().val($(this).html());
		});
		
		$('.cke_wysiwyg_frame').each(function() {
			$(this).parent().parent().parent().prev().val($(this).contents().find('.cke_editable').html())
		});
		
		$('input[name="uni_set[save_date]"]').val(Date.now());
		
		$.ajax({
			url: 'index.php?route=extension/module/uni_settings/save&'+user_token,
			type: 'post',
			data: $('#unishop input, #unishop textarea, #unishop select').serialize(),
			dataType: 'html',
			beforeSend: function() {
				$('.pull-right .btn-success, .scroll_button .btn-success').html('<i class="fa fa-spinner"></i>');
			}, 
			success: function(data) {
				if(data == 'success') {
					$('.pull-right .btn-success, .scroll_button .btn-success').html('<i class="fa fa-check"></i>');
					setTimeout(function() {
						$('.pull-right .btn-success, .scroll_button .btn-success').html('<i class="fa fa-save"></i>');
					}, 1000);
				} else {
					$('.pull-right .btn-success, .scroll_button .btn-success').html('<i class="fa fa-remove"></i>').data('original-title', uni_text_alert_validate).attr('class', 'btn btn-danger');
				}
				
				$.get('index.php?route=marketplace/modification/refresh&'+user_token);
				$.get('index.php?route=catalog/review&'+user_token);
			}
		});
	}
	
	function addTrial() {
		$('.container-fluid_new > .alert').remove();
			
		if (!$('#trial input[name=\'trial\']').is(':checked')) {
			$('.container-fluid_new').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> '+uni_text_error_agree+'</div>');
			return false;
		}
				
		$.ajax({
			url: 'index.php?route=extension/module/uni_settings/addTrial&'+user_token,
			type: 'post',
			dataType: 'json',
			beforeSend: function() {
				$('#trial .btn-primary').button('loading');
			}, 
			complete: function() {
				$('#trial .btn-primary').button('reset');
			},
			success: function(json) {
				if(json['success']) {
					$('#trial .btn-primary').remove();
					window.location.reload();
				} else {
					$('.container-fluid_new').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> '+uni_text_error_trial+'</div>');
				}
			}
		});
	}
	
	function addKey(data) {
		$('.container-fluid_new > .alert').remove();
	
		if ($(data).prev().val() == '') {
			$('.container-fluid_new').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> '+uni_text_error_key_empty+' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			return false;
		}
				
		$.ajax({
			url:'index.php?route=extension/module/uni_settings/addKey&'+user_token,
			type:'post',
			data:$(data).prev().serialize(),
			dataType:'json',
			beforeSend:function() {
				$('#full .btn-primary').button('loading');
			}, 
			complete:function() {
				$('#full .btn-primary').button('reset');
			},
			success:function(json) {
				if(json['success']) {
					$('#full .btn-primary').remove();
					window.location.reload();
				} else {
					$('.container-fluid_new').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> '+uni_text_error_key+' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}
			}
		});
	}
	
	function addKey2() {
		$('.container-fluid_new > .alert').remove();
	
		$.ajax({
			url: 'index.php?route=extension/module/uni_settings/addKey2&'+user_token,
			dataType: 'json',
			beforeSend:function() {
				$('#full2 .btn-primary').button('loading');
			}, 
			complete:function() {
				$('#full2 .btn-primary').button('reset');
			},
			success: function(json) {
				if(json['success']) {
					$('.container-fluid_new').prepend('<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> '+uni_text_full_key_added+' <button type="button" class="close" data-dismiss="alert">&times;</button></div>')
				} else {
					$('.container-fluid_new').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> '+uni_text_error_key2+' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}
			}
		});
	}