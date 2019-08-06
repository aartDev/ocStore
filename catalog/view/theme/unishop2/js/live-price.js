var uniLivePrice = {
	init:function() {
		var base = this, blocks = '.quantity input, .option input[type="checkbox"], .option input[type="radio"], .option select';
		
		$(document).on('change', '.product-thumb '+blocks+', .product-block '+blocks, function() { 
			base.ChangePrice(this);  
		});
		
		$('.quantity input').each(function() {
			if($(this).val() > 1) {
				base.ChangePrice(this); 
			}
		});
	},
	ChangePrice:function(el) {
		var base = this, step = 0, level = 10, this_elem = $(el), elem;
	
		while(step < level) {
			this_elem = this_elem.parent();
		
			if(this_elem.hasClass('product-thumb') || this_elem.hasClass('product-block')){
				elem = this_elem;
				break;
			}
		
			step++;
		}
	
		if(elem) {
			var quantity = elem.find('.quantity input').val() ? elem.find('.quantity input').val() : 1, 
				option_price = 0;
				
			var elem2 = elem.find('.price'), 
				price = elem2.data('price'), 
				price2 = elem2.data('old-price'), 
				special = elem2.data('special'), 
				special2 = elem2.data('old-special');
				
			var old_price = price2 ? price2 : price, 
				old_special = special2 ? special2 : special,
				price_elem = elem2.find('.price-old'), 
				special_elem = elem2.find('.price-new');
	
			var discounts = elem2.data('discount');
	
			if(discounts && special <= 0) {
				discounts = JSON.parse(discounts.replace(/'/g, '"'));
	
				for (i in discounts) {
					d_quantity = parseFloat(discounts[i]['quantity']);
					d_price = parseFloat(discounts[i]['price']);
		
					if((quantity >= d_quantity) && (d_price < price)) {
						price = d_price;
					}
				}
			}
	
			elem.find('input:checked, option:selected').each(function() {
				if ($(this).data('prefix') == '+') {
					option_price += parseFloat($(this).data('price'));
				}
				if ($(this).data('prefix') == '-') {
					option_price -= parseFloat($(this).data('price'));
				}
				if ($(this).data('prefix') == '*') {
					price *= parseFloat($(this).data('price'));
					special *= parseFloat($(this).data('price'));
				}
				if ($(this).data('prefix') == '/') {
					price /= parseFloat($(this).data('price'));
					special /= parseFloat($(this).data('price'));
				}
				if ($(this).data('prefix') == '=') {
					option_price += parseFloat($(this).data('price'));
					price = 0;
					special = 0;
				}
			});
	
			new_price = (price + option_price) * quantity;
			new_special = (special + option_price) * quantity;

			if(special) {
				base.AnimatePrice(old_price, new_price, price_elem);
				base.AnimatePrice(old_special, new_special, special_elem);
			} else {
				base.AnimatePrice(old_price, new_price, elem2);
			}
	
			elem2.data('old-price', new_price);
			elem2.data('old-special', new_special);
		}
	},
	AnimatePrice:function(old_price, new_price, elem){
		var base = this;
		
		if(new_price != old_price) {
			$({val:old_price}).animate({val:new_price}, {
				duration:300,
				step: function(val) {
					elem.text(base.PriceFormat(val));
				}
			});
		}
	},
	PriceFormat:function(n) { 
		var base = this;
		
		c = uniJsVars.curr_decimal != 0 ? uniJsVars.curr_decimal : '';
		d = uniJsVars.curr_decimal_p;
		t = uniJsVars.curr_thousand_p;
		s_left = uniJsVars.curr_symbol_l;
		s_right = uniJsVars.curr_symbol_r;
		i = parseInt(n = Math.abs(n).toFixed(c)) + ''; 
		j = ((j = i.length) > 3) ? j % 3 : 0; 
		
		return s_left + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '') + s_right; 
	}
};
	
$(function() {	
	uniLivePrice.init();
});