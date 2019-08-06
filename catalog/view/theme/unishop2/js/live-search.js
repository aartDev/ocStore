uniLiveSearch = {
	init:function() {
		var base = this; 
		
		base.inputs = 'header input[name="search"], #fly-menu input[name="search"]';
		base.minlength = 3;
		base.timer;
		base.delay = 500;
		
		$(document).on('click', base.inputs, function() {
			base.click(this);
		});
		
		$(document).on('keyup', base.inputs, function() {
			base.keyUp(this);
		});
		
		$('header, main, footer').on('click', function() {
			$('.live-search').hide();
		});
		
		uniAddCss('catalog/view/theme/unishop2/stylesheet/livesearch.css');
		
		$(base.inputs).attr('autocomplete', 'off');
		$(base.inputs).parent().parent().append('<div id="live-search" class="live-search" style="display:none"><ul><li class="loading"></li></ul></div>');
	},
	click:function(el) {
		var base = this, $elem = $(el).parent().parent();
		
		if ($elem.find('.live-search ul li').length > 1) {
			$elem.find('.live-search').show();
		}
	},
	keyUp:function(el) {
		var base = this, $this = $(el), $elem = $this.parent().parent();
		
		if ($this.val().length >= base.minlength) {
			
			$elem.find('.live-search ul').html('<li class="loading"></li>');
			$elem.find('.live-search').show();
		
			clearTimeout(base.timer);
			
			base.timer = setTimeout(function(){
				$.ajax({
					url:'index.php?route=extension/module/uni_live_search',
					type:'post',
					data:{'filter_name': $this.val(), 'category_id': $elem.find('input[name=\'filter_category_id\']').val(), 'flag': true},
					dataType:'html',
					success: function(html) {
						$('.live-search ul').html(html);
					}
				});
			}, base.delay);
		} else {
			$elem.find('.live-search').hide();
		}
	}
}

$(function() {
	uniLiveSearch.init();
});