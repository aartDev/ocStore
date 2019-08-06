$(function() {
	if(uniJsVars.showmore && $('.product-layout').length && $('.pagination_wrap .active').next().find('a').length) {
		
		var show_more = '<div class="show-more" style="margin:0 0 20px;text-align:center"><button type="button" class="btn btn-lg btn-default"><i class="fa fa-sync-alt"></i><span>'+uniJsVars.showmore_text+'</span></button></div>'
		
		$('.pagination_wrap').before(show_more);
	
		$('.show-more .btn').on('click', function() {
			var url = $('.pagination_wrap .active').next().find('a').attr('href');
		
			if(typeof(url) == 'undefined' || url == '') return;
	
			$.ajax({
				url: url,
				type: 'get',
				dataType: 'html',
				beforeSend: function() {
					$('.show-more .btn i').addClass('spin');
				},
				success: function(data) {
					
					var $products = $(data);
						
					$products.find('.product-thumb').hide();
					
					$('.products-block').append($products.find('.products-block').html());
					$('.pagination_wrap').html($products.find('.pagination_wrap').html());
			
					if(!$('.pagination_wrap .active').next().find('a').length) {
						$('.show-more').hide();
					}
			
					$('.show-more .btn i').removeClass('spin');
					
					$('.products-block .product-thumb').fadeIn();
				
					window.history.pushState('', '', url);
				}
			});
		});
	}

	if(uniJsVars.ajax_pagination && $('.products-block').length) {
		$(document).on('click', '.pagination_wrap a', function(e) {
		
			e.preventDefault();
		
			var url = $(this).attr('href');
	
			$.ajax({
				url: url,
				type: 'get',
				dataType: 'html',
				beforeSend: function() {
					$('html body').append('<div class="full-width-loading"></div>');
				},
				complete: function() {
					uniSelectView.init();
					scroll_to('.products-block');
				},
				success: function(data) {
					$('.products-block').html($(data).find('.products-block').html());
					$('.pagination_wrap').html($(data).find('.pagination_wrap').html());
				
					if(!$('.pagination_wrap .active').next().find('a').length) {
						$('.show-more').hide();
					} else {
						$('.show-more').show();
					}
				
					$('.full-width-loading').remove();
				
					window.history.pushState('', '', url);
				}
			});
		});
	}
});