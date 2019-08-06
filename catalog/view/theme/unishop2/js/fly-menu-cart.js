 var uniFlyMenu = {
	init:function(){
		var base = this;
			
		base.product = uniJsVars.fly_menu_product;
			
		if($('#unicheckout').length) {
			return;
		}
		
		$('#fly-menu').remove();
		
		base.windowWidth = $(window).width();
		base.breakpoint = 972,
		base.desktop_menu = (uniJsVars.fly_menu_desktop && base.windowWidth > base.breakpoint) ? true : false,
		base.mobile_menu = (uniJsVars.fly_menu_mobile && base.windowWidth <= base.breakpoint) ? true : false;
		
		base.reinit();
		
		$(window).scroll(function(){		
			if($(this).scrollTop() > 100) {
				if(!$('#fly-menu').length) {
					
					if(!base.desktop_menu && !base.mobile_menu) {
						return;
					}
					
					var html = '';
					
					if(base.desktop_menu) {
						if(base.product && $('#product').length) {
							html += '<div class="product-block col-md-8 col-lg-8 col-xxl-12">';
							html += '<div>';
							html += '<div class="name"><span>'+$('.heading-h1 h1').text()+'</span></div>';
							html += '<div class="price">'+$('#product .price').html()+'</div>';
				
							var btn = $('#product').find('.add_to_cart');
				
							html += '<button type="button" class="'+btn.attr('class').replace('btn-lg', '')+'">'+btn.html()+'</button>';
							html += '</div>';
							html += '</div>';
						} else {
							html += '<div class="menu-block col-md-3 col-lg-3 col-xxl-4"><div id="menu" class="menu3">'+$('header #menu').html()+'</div></div>';
							html += '<div class="search-block col-md-5 col-lg-5 col-xxl-8"><div id="search3">'+$('header #search').html()+'</div></div>';
						}
			
						html += '<div class="phone-block col-xs-3 col-md-2 col-lg-2 col-xxl-4"><div id="phone">'+$('header #phone').html()+'</div></div>';
					} 
		
					if(base.mobile_menu){
						html += '<div class="menu-block-m col-xs-3">';
						html += '<i class="fa fa-bars"></i>';
						html += '<div class="menu-wrap"><div id="menu" class="menu4">'+$('header #menu').html()+'</div><div class="overlay"></div></div>';
						html += '</div>';
			
						html += '<div class="search-block-m col-xs-3">';
						html += '<i class="fa fa-search"></i>';
						html += '<div class="search-wrap">'+$('header #search').html()+'<div class="overlay"></div></div>';
						html += '</div>';
					}
		
					if(base.desktop_menu || base.mobile_menu) {
						html += '<div class="account-block col-xs-3 col-md-1 col-lg-1 col-xxl-2">';
						html += '<i class="fa fa-user"></i>';
						html += '<div class="account-wrap"><ul class="dropdown-menu dropdown-menu-right">'+$('#top #account ul').html()+'</ul></div>';
						html += '</div>';
		
						html += '<div class="cart-block col-xs-3 col-md-1 col-lg-1 col-xxl-2">';
						html += '<i class="fa fa-shopping-bag"></i>';
						html += '<span id="cart-total">'+$('header #cart #cart-total').text()+'</span>';
						html += '<div class="cart-wrap"><div id="cart"><ul class="dropdown-menu dropdown-menu-right">'+$('header #cart ul').html()+'</ul></div></div>';
						html += '</div>';
					}

					if(html != '') {
						
						uniAddCss('catalog/view/theme/unishop2/stylesheet/flymenu.css');
						
						$('html body').append('<div id="fly-menu"><div class="container"><div class="row">'+html+'</div></div></div>');
					
						var menu = $('#fly-menu'),
							block = menu.find('.row > div'),
							block_i = block.find('> i');
					
						$(document).ajaxStop(function() {
							$('#fly-menu #cart ul').html($('header #cart ul').html());
							$('#fly-menu #cart-total').text($('header #cart-total').text());
						});
					
						block_i.on('click', function(){
							$(this).parent().toggleClass('show');
							
							block.not($(this).parent()).removeClass('show');
							
							if(block.hasClass('show')){
								$('html body').addClass('fly-menu-open');
							} else {
								$('html body').removeClass('fly-menu-open');
							}
						});
					
						$('main, footer, #fly-menu .overlay').on('click', function(){
							block.removeClass('show');
							$('html body').removeClass('fly-menu-open');
						});
						
						if(base.desktop_menu) {
							uniMenuAim();
						
							if(uniJsVars.menu_blur) {
								uniMenuBlur();
							}
						
							if(base.product && $('#product').length) {
								$(document).on('change', '#product input', function() {
									setTimeout(function() { 
										$('#fly-menu .product-block .price').html($('#product .price').html());
									}, 300);
								});
	
								$('#fly-menu .product-block button').click(function() {
									$('#button-cart').click();
								});
			
								$('#fly-menu .name').mouseover(function () {
									var boxWidth = $(this).width();
									
									$text = $('#fly-menu .name span');
									$textWidth = $('#fly-menu .name span').width();

									if ($textWidth > boxWidth) {
										$($text).animate({left: -(($textWidth+20) - boxWidth)}, 500);
									}
								}).mouseout(function () {
									$($text).stop().animate({left: 0}, 500);
								});
							}
						}
					
						if(base.mobile_menu) {
							$('.menu-block-m > i').on('click', function() {
								$('.menu-wrap li').removeClass('open');
							});
						
							$('.search-block-m > i').on('click', function() {
								if($('.search-block-m').hasClass('show')) {
									$('.search-block-m .form-control').focus();
								}
							});
						}
					}
				}
			}
			
			if($(this).scrollTop() > 200) {
				$('#fly-menu').addClass('show');
			} else {
				$('#fly-menu, #fly-menu .row > div').removeClass('show');
				$('html body').removeClass('fly-menu-open');
			}
		});
	},
	reinit:function() {
        var base = this, lastwindowWidth = $(window).width();
		
		base.resizer = function() {
            if ($(window).width() !== lastwindowWidth) {
				base.init();
            };
		}
			
		$(window).resize(function() {
			setTimeout(function() { 
				base.resizer();
			}, 300);
		});
	}
 };
 
$(function() {
	if(uniJsVars.fly_menu_desktop || uniJsVars.fly_menu_mobile) {
		uniFlyMenu.init();
		fly_menu_enabled = 1;
	} else {
		fly_menu_enabled = 0;
	}
	
	if(uniJsVars.fly_cart && !fly_menu_enabled) {
		if($(window).width() > 992) {
			$(window).scroll(function(){		
				$(this).scrollTop() > 200 ? $('#cart').addClass('fly') : $('#cart').removeClass('fly');
			});
		}
	}
});