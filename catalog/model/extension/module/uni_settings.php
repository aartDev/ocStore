<?php
class ModelExtensionModuleUniSettings extends Model {	
	public function getSetting() {
		$store_id = (int)$this->config->get('config_store_id');
		
		$data = $this->cache->get('unishop.settings.'.$store_id);
		
		if (!$data) {
			$data = [];
			
			$query = $this->db->query("SELECT data FROM `".DB_PREFIX."uni_setting` WHERE store_id = '".$store_id."'");
			
			if($query->rows) {
				$data = json_decode($query->row['data'], true);
				$this->cache->set('unishop.settings.'.$store_id, $data);
				
				$this->setStyle($data);
				$this->removeMerged();
				
				$this->cache->delete('product.unishop');
			}
		}
		
		$this->config->set('config_unishop2', $data);
	}
	
	private function removeMerged() {
		$styles = glob(DIR_TEMPLATE.'unishop2/stylesheet/uni-merged*');
		
		if($styles) {
			foreach($styles as $file) {
				if (file_exists($file)) {
					unlink($file);
				}
			}
		}
		
		$scripts = glob(DIR_TEMPLATE.'unishop2/js/uni-merged*');
		
		if($scripts) {
			foreach($scripts as $file) {
				if (file_exists($file)) {
					unlink($file);
				}
			}
		}
	}
	
	private function setStyle($set) {		
		$store_id = (int)$this->config->get('config_store_id');
			
		$style = '';
		
		//if background image or background color
		if((isset($set['background_image']) && $set['background_image'] != '') || (isset($set['background_color']) && $set['background_color'] != 'fff' && $set['background_color'] != 'ffffff')) {
			if(isset($set['background_image']) && $set['background_image'] != '') {
				$style .= 'body {background-image:url("/image/'.$set['background_image'].'")}';
			}
			if(isset($set['background_color'])) {
				$style .= 'body {background-color:#'.$set['background_color'].'}';
			}
			$style .= '@media (min-width:767px) {';
			$style .= 'header{margin:0 auto;padding:0 5px;background:#fff}';
			$style .= 'main{margin:0 auto;padding:20px 5px;background:#fff}';
			$style .= 'footer{margin:0 auto;padding-left:5px;padding-right:5px;opacity:.9}';
			$style .= '#subscribe{margin:-15px -5px 15px !important}';
			$style .= '}';
			$style .= '@media (max-width:767px) {body {background:#fff}}';
		}
		
		//basic  elements
		$style .= 'body {color:#'.$set['text_color'].'}';
		$style .= 'h1 {color:#'.$set['h1_color'].'}';
		$style .= 'h2 {color:#'.$set['h2_color'].'}';
		$style .= 'h3 {color:#'.$set['h3_color'].'}';
		$style .= 'h4 {color:#'.$set['h4_color'].'}';
		$style .= 'h5 {color:#'.$set['h5_color'].'}';
		$style .= 'div.heading {color:#'.$set['h3_heading_color'].'}';
		$style .= 'a, #phone .open_callback, #phone .open_callback span, #phone .additional-phone > span, .uni-module:before {color:#'.$set['a_color'].'}';
		$style .= 'a:hover, a:focus, a:active, #phone .open_callback:hover, #phone .open_callback span:hover, #phone .additional-phone span.selected {color:#'.$set['a_color_hover'].'}';
		$style .= '.nav-tabs > li > a {color:#'.$set['a_color'].'}';
		$style .= '.nav-tabs > li.active > a, .nav-tabs > li.active >a:focus, .nav-tabs > li.active > a:hover {color:#'.$set['a_color_hover'].'}';
		$style .= '.rating i, .rating sup a {color:#'.$set['rating_star_color'].'}';
		$style .= 'label.input input[type="radio"]:checked + span, label.input input[type="checkbox"]:checked + span {background:#'.$set['checkbox_radiobutton_bg'].'}';
		$style .= '.noUi-horizontal .noUi-handle {background:#'.$set['checkbox_radiobutton_bg'].'}';
		$style .= '.tooltip-inner {color:#'.$set['tooltip_color'].';background:#'.$set['tooltip_bg'].'}';
		$style .= '.tooltip.top .tooltip-arrow {border-top-color:#'.$set['tooltip_bg'].' !important}';
		$style .= '.tooltip.bottom .tooltip-arrow {border-bottom-color:#'.$set['tooltip_bg'].' !important}';
		$style .= '.tooltip.left .tooltip-arrow {border-left-color:#'.$set['tooltip_bg'].' !important}';
		$style .= '.tooltip.right .tooltip-arrow {border-right-color:#'.$set['tooltip_bg'].' !important}';
			
		$style .= '.form-control.input-warning{border-color:#'.$set['text_alert_color'].'}';
		$style .= '.text-danger{color:#'.$set['text_alert_color'].'}';
		
		$style .= isset($set['menu_type']) && $set['menu_type'] == 2 ? '.breadcrumb.col-md-offset-4.col-lg-offset-3{margin-left:0 !important}' : '';
		
		$style .= '.attr_value{color:#'.$set['text_color'].'}';
			
		$style .= '.option label input[type="radio"] + span, .option label input[type="checkbox"] + span, .option select{color:#'.$set['options_color'].';background:#'.$set['options_bg'].'}';
		$style .= '.option label input[type="radio"]:checked + span, .option label input[type="checkbox"]:checked + span{color:#'.$set['options_color_active'].';background:#'.$set['options_bg_active'].'}';
		$style .= '.option .option-image label img{border-color:#'.$set['options_bg'].'}';
		$style .= '.option .option-image:hover img, .option .option-image input[type="radio"]:checked + img{border-color:#'.$set['options_bg_active'].'}';
		$style .= '.option-image-popup.module{width:'.$set['options_popup_img_width'].'px}';
		$style .= '.option-image-popup.product{width:'.$set['options_popup_img_width_p'].'px}';
		$style .= '.option-image-popup.quick-order{width:'.$set['options_popup_img_width_q'].'px}';
				
		//top menu
		$style .= '#top {background:#'.$set['top_menu_bg'].'}';
		$style .= '#top li > a, #top .btn-group > .btn {color:#'.$set['top_menu_color'].'}';
		$style .= '#top li > a:hover, #top .btn-group > .btn:hover, #top .btn-group.open > .btn {color:#'.$set['top_menu_color_hover'].'}';
		$style .= ($set['top_menu_color'] > 'EEEEEE') ? '@media (max-width:992px) {#top .btn-group ul li a {color:#777}}' : '';
			
		//search block
		$style .= '.search .btn {color:#'.$set['search_btn_color'].' !important;background:#'.$set['search_btn_bg'].' !important}';
		$style .= '.search input[name="search"]{color:#'.$set['search_input_color'].'}';
		$style .= '.search .search-btn {color:#'.$set['search_input_color'].'}';
		$style .= '.search input::-webkit-input-placeholder{color:#'.$set['search_input_color'].'}';
		$style .= '.search input::-moz-placeholder{color:#'.$set['search_input_color'].' }';
		$style .= '.search input:-ms-input-placeholder{color:#'.$set['search_input_color'].'}';
		$style .= '.search input:-input-placeholder{color:#'.$set['search_input_color'].'}';
			
		//phone block
		$style .= '#phone .main-phone{color:#'.$set['main_phone_color'].'}';
		$style .= '#phone .dropdown-menu li a i, #phone .dropdown-menu li a span{color:#'.$set['additional_phone_color'].'}';
			
		//cart block
		$style .= 'header #cart .btn i{color:#'.$set['cart_color'].'}';
		$style .= '#cart > .btn span {color:#'.$set['cart_color_total'].';background:#'.$set['cart_bg_total'].'}';
			
		//main menu
		//if($set['menu_type'] == 1) {
		$style .= '#menu {color:#'.$set['main_menu_color'].' !important;background:#'.$set['main_menu_bg'].' !important}';
		$style .= '#menu .btn-navbar {color:#'.$set['main_menu_color'].'}';
		$style .= '#menu .nav {background:#'.$set['main_menu_parent_bg'].'}';
		$style .= '#menu .nav > li > a, #menu .nav > li > .visible-xs i {color:#'.$set['main_menu_parent_color'].'}';
		$style .= '#menu .nav > li:hover > a, #menu .nav > li:hover > .visible-xs i {color:#'.$set['main_menu_parent_color_hover'].'}';
		$style .= '#menu .nav > li > .dropdown-menu .visible-xs i {color:#'.$set['main_menu_children_color'].'}';
		$style .= '#menu .nav > li > .dropdown-menu {background:#'.$set['main_menu_children_bg'].'}';
		$style .= '#menu .nav > li:hover {background:#'.$set['main_menu_children_bg'].'}';
		$style .= '#menu .nav > li.has-children:before {background:#'.$set['main_menu_children_bg'].'}';
		$style .= '#menu .nav > li ul li a {color:#'.$set['main_menu_children_color'].'}';
		$style .= '#menu .nav > li ul li a:hover {color:#'.$set['main_menu_children_color_hover'].'}';
		$style .= '#menu .nav > li ul li i {color:#'.$set['main_menu_children_color'].'}';
		$style .= '#menu .nav > li ul li ul li a {color:#'.$set['main_menu_children_color2'].'}';
		$style .= '#menu .nav > li ul li ul li a:hover {color:#'.$set['main_menu_children_color2_hover'].'}';
		$style .= '#menu .nav > li ul li ul li i {color:#'.$set['main_menu_children_color2'].'}';
		//}
		
		//main menu second level top position
		if(isset($set['main_menu_sec_lev_pos'])) {
			$style .= '@media (min-width:992px){';
			$style .= '#menu:not(.menu2) .nav > li.has-children {position:static}';
			$style .= '#menu:not(.menu2) .nav > li.has-children:hover {border-right:solid 1px transparent}';
			$style .= '#menu:not(.menu2) .nav > li.has-children .dropdown-menu {top:0;min-height:100%;border-left:0;}';
			$style .= '}';
		}
		
		//main menu type2
		if($set['menu_type'] == 2) {
			//$style .= '@media (min-width:992px) {';
			$style .= '#menu.menu2 {color:#'.$set['main_menu2_color'].' !important;background:#'.$set['main_menu2_bg'].' !important}';
			$style .= '#menu.menu2 .nav > li > a, #menu.menu2 .btn-navbar, #menu.menu2 .nav > li > .visible-xs i {color:#'.$set['main_menu2_color'].'}';
			$style .= '#menu.menu2 .nav > li > .dropdown-menu {background:#'.$set['main_menu2_children_bg'].'}';
			$style .= '#menu.menu2 .nav > li ul li a {color:#'.$set['main_menu2_children_color'].'}';
			$style .= '#menu.menu2 .nav > li ul li a:hover {color:#'.$set['main_menu2_children_color_hover'].'}';
			$style .= '#menu.menu2 .nav > li ul li ul li a {color:#'.$set['main_menu2_children_color2'].'}';
			$style .= '#menu.menu2 .nav > li ul li ul li a:hover {color:#'.$set['main_menu2_children_color2_hover'].'}';
			//$style .= '}';
			$style .= '/* menu for max 992 */';
			$style .= '@media (max-width:992px) {';
			$style .= '#menu.menu2 .nav {color:#'.$set['main_menu2_color'].' !important;background:#'.$set['main_menu2_bg'].' !important}';
			$style .= '}';
			$style .= '#menu .additional .btn i, #menu .additional .btn:hover i {color:#'.$set['main_menu2_color'].'}';
		}
			
		//custom menu
		$style .= '#custom_menu .nav {background:#'.$set['main_menu_parent_bg'].'}';
		$style .= '#custom_menu .nav > li > a, #custom_menu .nav li > .visible-xs i {color:#'.$set['main_menu_parent_color'].'}';
		$style .= '#custom_menu .nav > li:hover > a, #custom_menu .nav > li:hover > .visible-xs i {color:#'.$set['main_menu_parent_color_hover'].'}';
		$style .= '#custom_menu .nav > li > .dropdown-menu {background:#'.$set['main_menu_children_bg'].'}';
		$style .= '#custom_menu .nav > li:hover {background:#'.$set['main_menu_children_bg'].'}';
		$style .= '#custom_menu .nav > li.has_chidren:hover:before {background:#'.$set['main_menu_children_bg'].'}';
		$style .= '#custom_menu .nav > li ul > li > a {color:#'.$set['main_menu_children_color'].'}';
		$style .= '#custom_menu .nav > li ul li ul > li > a {color:#'.$set['main_menu_children_color2'].'}';
			
		//other menu
		$style .= '.list-group a {color:#'.$set['main_menu_parent_color'].';background:#'.$set['main_menu_parent_bg'].'}';
		$style .= '.list-group a.active, .list-group a.active:hover, .list-group a:hover {color:#'.$set['main_menu_parent_color_hover'].';background:#'.$set['main_menu_children_bg'].'}';
			
		//right menu
		$style .= '#menu2  {background:#'.$set['right_menu_bg'].'}';
		$style .= '#menu2 a, #menu2 .btn i, #menu2 .dropdown-menu>li>a {color:#'.$set['right_menu_color'].'}';
		$style .= '#menu2 a:hover, #menu2 .btn:hover i, #menu2 .dropdown-menu>li>a:hover {color:#'.$set['right_menu_color_hover'].'}';
		$style .= '@media (max-width:992px){';
		
		list($r, $g, $b) = sscanf($set['right_menu_color'], '%2x%2x%2x');
		list($r1, $g1, $b1) = sscanf($set['right_menu_bg'], '%2x%2x%2x');
		
		$right_menu_color = ($r >= $r1 && $g >= $g1 && $b >= $b1) ? $set['right_menu_bg'] : $set['right_menu_color'];
		
		$style .= '#menu2 a, #menu2 a:hover {color:#'.$right_menu_color.'}';
		$style .= '}';
		
		//buttons
		$style .= '.btn.btn-default {color:#'.$set['btn_default_color'].';background:#'.$set['btn_default_bg'].'}';
		$style .= '.btn.btn-default:hover, .btn.btn-default:focus {color:#'.$set['btn_default_color_hover'].';background:#'.$set['btn_default_bg_hover'].'}';
		$style .= '.btn.btn-primary {color:#'.$set['btn_primary_color'].';background:#'.$set['btn_primary_bg'].'}';
		$style .= '.btn.btn-primary:hover, .btn.btn-primary:focus {color:#'.$set['btn_primary_color_hover'].';background:#'.$set['btn_primary_bg_hover'].'}';
		$style .= '.btn.btn-danger {color:#'.$set['btn_danger_color'].';background:#'.$set['btn_danger_bg'].'}';
		$style .= '.btn.btn-danger:hover, .btn.btn-danger:focus {color:#'.$set['btn_danger_color_hover'].';background:#'.$set['btn_danger_bg_hover'].'}';
		
		//special timer
		$style .= '.uni-timer .text {color:#'.$set['special_timer_text_color'].'}';
		$style .= '.special-timer .heading, .uni-timer .colon {color:#'.$set['special_timer_bg'].'}';
		$style .= '.uni-timer .digits {color:#'.$set['special_timer_color'].';background:#'.$set['special_timer_bg'].'}';
		
		//stock indicator
		$style .= '.stock-indicator.status-5 span.full {background:#'.$set['stock_i_c_5'].'}';
		$style .= '.stock-indicator.status-4 span.full {background:#'.$set['stock_i_c_4'].'}';
		$style .= '.stock-indicator.status-3 span.full {background:#'.$set['stock_i_c_3'].'}';
		$style .= '.stock-indicator.status-2 span.full {background:#'.$set['stock_i_c_2'].'}';
		$style .= '.stock-indicator.status-1 span.full {background:#'.$set['stock_i_c_1'].'}';
		$style .= '.stock-indicator span.empty {background:#'.$set['stock_i_c_0'].'}';	
		
		//fly menu
		$style .= '#fly-menu #menu, #fly-menu #menu .navbar-header, #fly-menu .menu-block-m, #fly-menu #search .btn, #fly-menu .search-block-m, #fly-menu .account-block > i, #fly-menu .cart-block > i {background:#'.$set['fly_menu']['bg'].'}';
		$style .= '#fly-menu #menu #category, #fly-menu #menu .btn-navbar, #fly-menu .menu-block-m, #fly-menu #search .btn, #fly-menu .search-block-m, #fly-menu #phone .phone > div, #fly-menu .account-block > i, #fly-menu .cart-block > i {color:#'.$set['fly_menu']['color'].'}';
		
		if($set['menu_type'] == 2) {
			//$style .= '@media (min-width:992px) {';
			$style .= '#fly-menu #menu .nav > li > a, #fly-menu #menu .nav > li > a + .visible-xs i, #menu.menu2 .btn-navbar {color:#'.$set['main_menu2_color'].'}';
			$style .= '#fly-menu #menu, #fly-menu #menu .navbar-header, #fly-menu #menu #category, #fly-menu #menu .btn-navbar, #fly-menu #menu .nav {color:#'.$set['main_menu2_color'].' !important;background:#'.$set['main_menu2_bg'].' !important}';
			$style .= '#fly-menu #menu .nav > li:hover{background:rgba(0, 0, 0, 0.06) !important}';
			$style .= '#fly-menu #menu .nav > li.has_children:hover:before{background:transparent}';
			$style .= '#fly-menu #menu .nav > li ul li a {color:#'.$set['main_menu2_children_color'].'}';
			$style .= '#fly-menu #menu .nav > li ul li a:hover {color:#'.$set['main_menu2_children_color_hover'].'}';
			$style .= '#fly-menu #menu .nav > li ul li ul li a {color:#'.$set['main_menu2_children_color2'].'}';
			$style .= '#fly-menu #menu .nav > li ul li ul li a:hover {color:#'.$set['main_menu2_children_color2_hover'].'}';
			
			$style .= '#fly-menu .cart-block > span {color:#'.$set['cart_color_total'].';background:#'.$set['cart_bg_total'].'}';
			//$style .= '}';
		}
			
		//slideshow
		$style .= '.swiper-viewport .title{color:#'.$set['slideshow_title_color'].';background:#'.$set['slideshow_title_bg'].'}';
		$style .= '.swiper-viewport .swiper-pager .swiper-button-next:before, .swiper-viewport .swiper-pager .swiper-button-prev:before {color:#'.$set['slideshow_pagination_bg_active'].' !important}';
		$style .= isset($set['hide_slideshow_title']) ? '.swiper-viewport .title{display:none}' : '';
		$style .= '.swiper-viewport .swiper-pagination .swiper-pagination-bullet{background:#'.$set['slideshow_pagination_bg'].' !important}';
		$style .= '.swiper-viewport .swiper-pagination .swiper-pagination-bullet-active{background:#'.$set['slideshow_pagination_bg_active'].' !important}';
			
		//unislideshow
		$style .= '.uni-slideshow .title{color:#'.$set['unislideshow_title_color'].'}';
		$style .= '.uni-slideshow .text{color:#'.$set['unislideshow_text_color'].'}';
		$style .= '.uni-slideshow .btn{color:#'.$set['unislideshow_button_color'].';background:#'.$set['unislideshow_button_bg'].'}';
		$style .= '.uni-slideshow .owl-nav .owl-prev, .uni-slideshow .owl-nav .owl-next {color:#'.$set['unislideshow_nav_bg_active'].' !important}';
		$style .= '.uni-slideshow .owl-dots .owl-dot span{background:#'.$set['unislideshow_nav_bg'].' !important}';
		$style .= '.uni-slideshow .owl-dots .owl-dot.active span{background:#'.$set['unislideshow_nav_bg_active'].' !important}';
			
		//carousel
		$style .= '.owl-carousel .owl-dots .owl-dot span {background:#'.$set['carousel_pagination_bg'].'}';
		$style .= '.owl-carousel .owl-dots .owl-dot.active span {background:#'.$set['carousel_pagination_bg_active'].'}';
		$style .= '.owl-carousel .owl-dots .owl-dot.active span:after {border-color:#'.$set['carousel_pagination_bg_active'].'}';
		$style .= '.owl-carousel .owl-nav > div {color:#'.$set['slideshow_pagination_bg_active'].'}';

		//banners
		$style .= '.uni-banner > div:hover .btn-primary{color:#'.$set['btn_primary_color_hover'].' !important;background:#'.$set['btn_primary_bg_hover'].' !important}';
			
		//home text banners
		$style .= '.home_banners > div > div {background:#'.$set['home_banners_bg'].';color:#'.$set['home_banners_text_color'].'}';
		$style .= '.home_banners > div i {color:#'.$set['home_banners_icon_color'].'}';
		
		//cat description
		$style .= ($set['cat_desc_pos'] == 'bottom') ? '.category-page.category-info{display:none}' : '';
			
		//product-thumb
		$style .= '.product-thumb .caption > a{color:#'.$set['product_thumb_h4_color'].'}';
		$style .= '.product-thumb .caption > a:hover{color:#'.$set['product_thumb_h4_color_hover'].'}';
		
		//product
		$style .= '#product .additional a.selected:after {border-color:#'.$set['a_color'].'}';
			
		//price
		$style .= '.price {color:#'.$set['price_color'].'}';
		$style .= '.price .price-old {color:#'.$set['price_color_old'].'}';
		$style .= '.price .price-new {color:#'.$set['price_color_new'].'}';
			
		//cart btn
		$style .= '.add_to_cart {color:#'.$set['cart_btn_color'].';background:#'.$set['cart_btn_bg'].'}';
		$style .= '.product-thumb:hover .add_to_cart, .add_to_cart:hover, .add_to_cart:focus, .add_to_cart:active {color:#'.$set['cart_btn_color_hover'].';background:#'.$set['cart_btn_bg_hover'].'}';
		$style .= '.add_to_cart.in_cart, .add_to_cart.in_cart:hover, .product-thumb:hover .add_to_cart.in_cart {color:#'.$set['cart_btn_color_incart'].';background:#'.$set['cart_btn_bg_incart'].'}';
		$style .= '.add_to_cart.disabled, .add_to_cart.disabled:hover, .product-thumb:hover .add_to_cart.disabled {color:#'.$set['cart_btn_color_disabled'].';background:#'.$set['cart_btn_bg_disabled'].'}';				
			
		//quick order btn
		$style .= '.btn.quick_order {color:#'.$set['quick_order_btn_color'].' !important;background:#'.$set['quick_order_btn_bg'].' !important}';
		$style .= '.btn.quick_order:hover, .btn.quick_order:focus, .btn.quick_order:active {color:#'.$set['quick_order_btn_color_hover'].' !important;background:#'.$set['quick_order_btn_bg_hover'].' !important}';
		$style .= isset($set['show_quick_order_always']) ? '.btn.quick_order {opacity:1}' : '';
			
		//wishlist&compare btn
		$style .= '.wishlist, .wishlist a {color:#'.$set['wishlist_btn_color'].'}';
		$style .= '.wishlist:hover, .wishlist a:hover, .wishlist:focus {color:#'.$set['wishlist_btn_color_hover'].'}';
		$style .= '.compare, .compare a {color:#'.$set['compare_btn_color'].'}';
		$style .= '.compare:hover, .compare a:hover, .compare:focus {color:#'.$set['compare_btn_color_hover'].'}';
			
		//stickers
		$style .= '.sticker .reward {color:#'.$set['sticker_reward_text_color'].';background:#'.$set['sticker_reward_background_color'].'}';
		$style .= '.sticker .special {color:#'.$set['sticker_special_text_color'].';background:#'.$set['sticker_special_background_color'].'}';
		$style .= '.sticker .bestseller {color:#'.$set['sticker_bestseller_text_color'].';background:#'.$set['sticker_bestseller_background_color'].'}';
		$style .= '.sticker .new {color:#'.$set['sticker_new_text_color'].';background:#'.$set['sticker_new_background_color'].'}';
		$style .= '.sticker .sku {color:#'.$set['sticker_sku_text_color'].';background:#'.$set['sticker_sku_background_color'].'}';
		$style .= '.sticker .upc {color:#'.$set['sticker_upc_text_color'].';background:#'.$set['sticker_upc_background_color'].'}';
		$style .= '.sticker .ean {color:#'.$set['sticker_ean_text_color'].';background:#'.$set['sticker_ean_background_color'].'}';
		$style .= '.sticker .jan {color:#'.$set['sticker_jan_text_color'].';background:#'.$set['sticker_jan_background_color'].'}';
		$style .= '.sticker .isbn {color:#'.$set['sticker_isbn_text_color'].';background:#'.$set['sticker_isbn_background_color'].'}';
		$style .= '.sticker .mpn {color:#'.$set['sticker_mpn_text_color'].';background:#'.$set['sticker_mpn_background_color'].'}';
			
		//product text banners
		$style .= '#product-banners .item {color:#'.$set['product_banners_text_color'].';background:#'.$set['product_banners_bg'].'}';
		$style .= '#product-banners .item i {color:#'.$set['product_banners_icon_color'].'}';
		
		//pagination
		$style .= '.pagination li a, .pagination li a:hover, .pagination li a:visited{color:#'.$set['pagination_color'].' !important;background:#'.$set['pagination_bg'].' !important}';
		$style .= '.pagination li.active span{color:#'.$set['pagination_color_active'].' !important;background:#'.$set['pagination_bg_active'].'!important}';
			
		//footer
		$style .= 'footer{background:#'.$set['footer_bg'].'}';
		$style .= 'footer h5{color:#'.$set['footer_h5_color'].'}';
		$style .= 'footer, footer .text-danger, footer a, footer a:hover, footer a:visited{color:#'.$set['footer_text_color'].'}';
		
		//subscribe
		$style .= '#subscribe .subscribe-info {color:#'.$set['subscribe_text_color'].'}';
		$style .= '#subscribe .subscribe-info div {color:#'.$set['subscribe_points_color'].'}';
		$style .= '#subscribe .subscribe-input input {color:#'.$set['subscribe_input_color'].';background:#'.$set['subscribe_input_bg'].'}';
		
		$style .= '#subscribe .subscribe-input input::-webkit-input-placeholder{color:#'.$set['subscribe_input_color'].'}';
		$style .= '#subscribe .subscribe-input input::-moz-placeholder{color:#'.$set['subscribe_input_color'].' }';
		$style .= '#subscribe .subscribe-input input:-ms-input-placeholder{color:#'.$set['subscribe_input_color'].'}';
		$style .= '#subscribe .subscribe-input input:-input-placeholder{color:#'.$set['subscribe_input_color'].'}';
		
		$style .= '#subscribe .subscribe-button .btn {color:#'.$set['subscribe_button_color'].';background:#'.$set['subscribe_button_bg'].'}';
		$style .= '#subscribe .subscribe-button .btn > * {color:#'.$set['subscribe_button_color'].'}';
		
		//fly wishlist & compare
		$style .= '.fly-wishlist, .fly-wishlist .total {color:#'.$set['fly_wishlist_color'].';background:#'.$set['fly_wishlist_bg'].'}';
		$style .= '.fly-compare, .fly-compare .total {color:#'.$set['fly_compare_color'].';background:#'.$set['fly_compare_bg'].'}';
		
		//fly callback button
		$style .= '.fly-callback {color:#'.$set['fly_callback_color'].';background:#'.$set['fly_callback_bg'].'}';
		$style .= '.fly-callback:before, .fly-callback:after {border:solid 1px;border-color:#'.$set['fly_callback_bg'].' transparent}';
		$style .= isset($set['hide_fly_callback']) ? '@media (max-width:767px){.fly-callback, .fly-callback2 {display:none !important}}' : '';
		
		//notification window
		$notification = isset($set['notification']) ? $set['notification'] : [];
		if($notification) {
			$style .= '#uni-notification .wrapper {color:#'.$notification['color'].';background:#'.$notification['bg'].'}';
		}
		
		//manufacturer module
		if($set['menu_type'] == 1) {
			$style .= '#manufacturer_module .heading, #manufacturer_module .heading:after {color:#'.$set['main_menu_color'].' !important;background:#'.$set['main_menu_bg'].' !important}';
		} else {
			$style .= '#manufacturer_module .heading, #manufacturer_module .heading:after {color:#'.$set['main_menu2_color'].' !important;background:#'.$set['main_menu2_bg'].' !important}';
		}
		
		//alerts
		$style .= '.alert-success{color:#'.$set['alert']['success']['color'].';background:#'.$set['alert']['success']['bg'].'}';
		$style .= '.alert-success a{color:#'.$set['alert']['success']['color'].'}';
		$style .= '.alert-warning{color:#'.$set['alert']['warning']['color'].';background:#'.$set['alert']['warning']['bg'].'}';
		$style .= '.alert-warning a{color:#'.$set['alert']['warning']['color'].'}';
		$style .= '.alert-danger{color:#'.$set['alert']['danger']['color'].';background:#'.$set['alert']['danger']['bg'].'}';
		$style .= '.alert-danger a{color:#'.$set['alert']['danger']['color'].'}';
		
		//blur on hover menu
		if($set['main_menu_blur'] == 1) {
			$style .= '.blur > *{filter:blur(2px);-webkit-filter:blur(2px)}';
			$style .= '#top:after {display:block;position:fixed;z-index:9;top:0;bottom:0;left:0;width:100%;content:"";background:#fff;visibility:hidden;opacity:0;transition:opacity linear .1s}';
			$style .= '#top.blur:after{visibility:visible;opacity:.5}';
		} elseif($set['main_menu_blur'] == 2) {
			$style .= '.blur > *{filter:blur(2px);-webkit-filter:blur(2px)}';
			$style .= '#top:after {display:block;position:fixed;z-index:9;top:0;bottom:0;left:0;width:100%;content:"";background:#000;visibility:hidden;opacity:0;transition:opacity linear .1s}';
			$style .= '#top.blur:after{visibility:visible;opacity:.5}';
		}
		
		//blur on popup show
		if(isset($set['popup_blur'])) {
			$style .= 'body.modal-open header, body.modal-open main, body.modal-open footer{filter:blur(2px)}';
		}
		
		//blur on popup img show
		if(isset($set['popup_img_blur'])) {
			$style .= 'body.magnific-open > *:not(.mfp-wrap){filter:blur(2px);transition:.1s}';
		}
		
		//user css
		if(isset($set['user_css'])) {
			$style .= html_entity_decode($set['user_css'], ENT_QUOTES, 'UTF-8');
		}
			
		$style_file = fopen(DIR_TEMPLATE.'unishop2/stylesheet/userstyle-'.$store_id.'.css', 'w');
		fwrite($style_file, $style);
		fclose($style_file);
	}
}
?>