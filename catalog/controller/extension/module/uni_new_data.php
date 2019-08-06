<?php  
class ControllerExtensionModuleUniNewData extends Controller {
	public function index($data = []) {
		$type = isset($data['type']) ? $data['type'] : '';
		
		$start = microtime(true); 
		
		switch($type) {
			case 'header':
				$result = $this->getHeaderData();
				break;
			case 'footer':
				$result = $this->getFooterData();
				break;
			case 'menu':
				$result = $this->getMenuData();
				break;
			case 'catalog':
				$result = $this->getCatalogData($data);
				break;
			case 'product':
				$result = $this->getProductData($data);
				break;
			case 'cart':
				$result = $this->getCartData($data);
				break;
			case 'contact':
				$result = $this->getContactData();
				break;
			default:
				//$this->response->redirect($this->url->link('error/not_found', '', true));
				$result = [];
		}
		
		$finish = microtime(true);
		
		//echo 'Время выполнения скрипта: '.$type.' '.round(($finish - $start), 4).' сек.<br />';
		
		return $result;
	}
	
	private function getHeaderData() {
		$data['store_id'] = $this->config->get('config_store_id');
		$data['shop_name'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
			
		$this->load->language('extension/module/uni_othertext');

		$data['customer_name'] = $this->customer->getFirstName();
			
		$uniset = $this->config->get('config_unishop2');
		$lang_id = $this->config->get('config_language_id');
			
		$data['uni_merged_css'] = isset($uniset['merge_css']) ? $this->getMergedStyle() : '';
		$data['custom_style'] = isset($uniset['custom_style']) && !isset($uniset['merge_css']) ? $uniset['custom_style'] : '';
		$data['uni_merged_js'] = isset($uniset['merge_js']) ? $this->getMergedScript() : '';
			
		$data['theme_color'] = ($uniset['menu_type'] == 1) ? $uniset['main_menu_bg'] : $uniset['main_menu2_bg'];
		$data['save_date'] = isset($uniset['save_date']) ? $uniset['save_date'] : strtotime('now');
		$data['show_meta_robots'] = isset($uniset['show_meta_robots']) && (isset($this->request->get['page']) || isset($this->request->get['limit']) || isset($this->request->get['sort'])) ? true : false;
		$data['default_view'] = isset($uniset['default_view']) ? $uniset['default_view'] : 'grid';
		
		$data['user_js'] = isset($uniset['user_js']) ? html_entity_decode($uniset['user_js'], ENT_QUOTES, 'UTF-8') : '';
		$data['show_login'] = isset($uniset['show_login']) ? true : false;
		$data['show_register'] = isset($uniset['show_register']) ? true : false;
		$data['headerlinks'] = isset($uniset[$lang_id]['headerlinks']) ? $uniset[$lang_id]['headerlinks'] : [];
		$data['callback'] = isset($uniset['show_callback']) ? true : false;
		$data['search_mob_hide'] = isset($uniset['search_mob_hide']) ? true : false;
		$data['search_phone_change'] = isset($uniset['search_phone_change']) ? true : false;
			
		$phrase_array = explode(',', isset($uniset[$lang_id]['search_phrase']) ? $uniset[$lang_id]['search_phrase'] : '');
		$data['search_phrase'] = $phrase_array ? $phrase_array[array_rand($phrase_array)] : '';
			
		$data['main_phones'] = [];
			
		if(isset($uniset[$lang_id]['main_phones'])) {
			foreach($uniset[$lang_id]['main_phones'] as $phone) {		
					
				$href = '';
					
				if(isset($phone['type']) && $phone['type'] != '') {
					if($phone['type'] == '?call' || $phone['type'] == '?chat') {
						$href = str_replace(' ', '', 'skype:'.$phone['number'].$phone['type']);
					} else {
						$href = str_replace(' ', '', $phone['type'].$phone['number']);
					}
				}
			
				$data['main_phones'][] = array(
					'text'		=> $phone['text'],
					'href'		=> $href,
					'number'	=> $phone['number'],
					'icon' 		=> $phone['icon'],
				);
			}
		}
			
		$data['phones'] = [];
			
		if(isset($uniset[$lang_id]['contacts'])) {
			foreach($uniset[$lang_id]['contacts'] as $contact) {		
								
				$href = '';
					
				if(isset($contact['type']) && $contact['type'] != '') {
					if($contact['type'] == '?call' || $contact['type'] == '?chat') {
						$href = str_replace(' ', '', 'skype:'.$contact['number'].$contact['type']);
					} else {
						$href = str_replace(' ', '', $contact['type'].$contact['number']);
					}
				}
			
				$data['phones'][] = array(
					'href'		=> $href,
					'number'	=> $contact['number'],
					'icon' 		=> $contact['icon'],
				);
			}
		}
			
		$data['text_in_add_contacts'] = isset($uniset[$lang_id]['text_in_add_contacts']) ? html_entity_decode($uniset[$lang_id]['text_in_add_contacts'], ENT_QUOTES, 'UTF-8') : '';
		$data['text_in_add_contacts_position'] = isset($uniset['text_in_add_contacts_position']) ? true : false;
			
		return $data;
	}
	
	private function getFooterData() {
		$this->load->language('extension/module/uni_othertext');
			
		$uniset = $this->config->get('config_unishop2');
		$lang_id = $this->config->get('config_language_id');
		$currency = $this->session->data['currency'];
			
		if(isset($uniset['show_search'])) {
			$this->document->addScript('catalog/view/theme/unishop2/js/live-search.js');
		}
			
		if(isset($uniset['show_callback']) || isset($uniset['show_fly_callback']) || $this->config->get('uni_request')) {
			$this->document->addScript('catalog/view/theme/unishop2/js/user-request.js');
		}
			
		if(isset($uniset['liveprice'])) {
			$this->document->addScript('catalog/view/theme/unishop2/js/live-price.js');
		}
			
		if(isset($uniset['fly_menu']['desktop']) || isset($uniset['fly_menu']['mobile']) || isset($uniset['show_fly_cart'])) {
			$this->document->addScript('catalog/view/theme/unishop2/js/fly-menu-cart.js');
		}
			
		if(isset($uniset['show_quick_order'])) {
			$this->document->addScript('catalog/view/theme/unishop2/js/quick-order.js');
		}
			
		if(isset($uniset['show_login']) || isset($uniset['show_register'])) {
			$this->document->addScript('catalog/view/theme/unishop2/js/login-register.js');
		}
			
		$this_route = isset($this->request->get['route']) ? $this->request->get['route'] : '';
			
		$uni_routes = [
			'product/uni_reviews',
			'product/category',
			'product/special',
			'product/search',
			'product/manufacturer/info',
		];
			
		if((isset($uniset['button_showmore']) || isset($uniset['ajax_pagination'])) && in_array($this_route, $uni_routes)) {
			$this->document->addScript('catalog/view/theme/unishop2/js/showmore-ajaxpagination.js');
		}
			
		$data['show_fly_cart'] = isset($uniset['show_fly_cart']) ? true : false;
		$data['show_fly_callback'] = isset($uniset['show_fly_callback']) ? true : false;
		$data['fly_callback_text'] = isset($uniset['show_fly_callback']) ? $uniset[$lang_id]['fly_callback_text'] : '';
	
		$data['subscribe'] = isset($uniset['show_subscribe']) ? $this->load->controller('extension/module/uni_subscribe') : '';
						
		$data['footer_column'] = isset($uniset[$lang_id]['footer_column']) ? $uniset[$lang_id]['footer_column'] : [];
		
		$footerlinks = isset($uniset[$lang_id]['footerlinks']) ? $uniset[$lang_id]['footerlinks'] : [];
		
		$data['footerlinks'] = [];
		
		foreach($footerlinks as $footerlink) {
			$data['footerlinks'][$footerlink['column']][] = [
				'title'	=> $footerlink['title'],
				'link'	=> $footerlink['link']
			];
		}
		
		$data['footer_text'] = isset($uniset[$lang_id]['footer_text']) ? html_entity_decode($uniset[$lang_id]['footer_text'], ENT_QUOTES, 'UTF-8') : '';
		$data['footer_address'] = isset($uniset['footer_address']) ? $this->config->get('config_address') : '';
		$data['footer_open'] = isset($uniset['footer_work_time']) ? $this->config->get('config_open') : '';
		$data['footer_phone'] = isset($uniset['footer_phone']) ? $this->config->get('config_telephone') : '';
		$data['footer_mail'] = isset($uniset['footer_mail']) ? $this->config->get('config_email') : '';
			
		$data['socials'] = isset($uniset['socials']) ? $uniset['socials'] : [];
		$data['payment_icons'] = isset($uniset['payment_icons']) ? $uniset['payment_icons'] : [];
		
		if(isset($uniset['payment_icons_custom'])) {
			foreach($uniset['payment_icons_custom'] as $icon) {
				if($icon != '') {
					$data['payment_icons'][] = $icon;
				}
			}
		}
		
		$data['wishlist'] = [];
		
		if ($this->customer->isLogged() && isset($uniset['show_fly_wishlist'])) {
			$this->load->model('account/wishlist');
			
			$data['wishlist'] = [
				'total' => $this->model_account_wishlist->getTotalWishlist(),
				'href'	=> $this->url->link('account/wishlist', '', true)
			];
		}
		
		$data['compare'] = [];
		
		if(isset($uniset['show_fly_compare'])) {
			$data['compare'] = [
				'total' => isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0,
				'href'	=> $this->url->link('product/compare', '', true)
			];
		}
			
		$data['notification'] = isset($uniset['notification']['status']) ? $this->load->controller('extension/module/uni_notification') : '';
		
		$uni_request = $this->config->get('uni_request') ? $this->config->get('uni_request') : [];
		
		$js_vars = array(
			'menu_blur' 				=> isset($uniset['main_menu_blur']) ? $uniset['main_menu_blur'] : false,
			'change_opt_img' 			=> isset($uniset['change_opt_img']) ? true : false,
			'additional_image' 			=> isset($uniset['show_additional_image']) ? true : false,
			'ajax_pagination' 			=> isset($uniset['ajax_pagination']) ? true : false,
			'showmore' 					=> isset($uniset['button_showmore']) ? true : false,
			'showmore_text' 			=> $this->language->get('button_show_more'),
			'cart_btn_icon'				=> isset($uniset[$lang_id]['cart_btn_icon']) ? $uniset[$lang_id]['cart_btn_icon'] : '',
			'cart_btn_text'				=> isset($uniset[$lang_id]['cart_btn_text']) ? $uniset[$lang_id]['cart_btn_text'] : '',
			'cart_btn_icon_incart' 		=> isset($uniset[$lang_id]['cart_btn_icon_incart']) ? $uniset[$lang_id]['cart_btn_icon_incart'] : '',
			'cart_btn_text_incart' 		=> isset($uniset[$lang_id]['cart_btn_text_incart']) ? $uniset[$lang_id]['cart_btn_text_incart'] : '',
			'cart_popup_disable' 		=> isset($uniset['cart_popup_disable']) ? true : false,
			'cart_popup_autohide' 		=> isset($uniset['cart_popup_autohide']) ? true : false,
			'cart_popup_autohide_time' 	=> isset($uniset['cart_popup_autohide_time']) ? $uniset['cart_popup_autohide_time'] : 5,
			'notify'					=> isset($uni_request['heading_notify'][$lang_id]) ? true : false,
			'notify_text' 				=> isset($uni_request['heading_notify'][$lang_id]) ? $uni_request['heading_notify'][$lang_id] : '',
			'popup_effect_in' 			=> isset($uniset['popup_effect_in']) && $uniset['popup_effect_in'] != 'disabled' ? 'fade animated '.$uniset['popup_effect_in'] : '',
			'popup_effect_out' 			=> isset($uniset['popup_effect_out']) && $uniset['popup_effect_out'] != 'disabled' ? 'fade animated '.$uniset['popup_effect_out'] : '',
			'alert_effect_in' 			=> isset($uniset['alert']['effect']['in']) && $uniset['alert']['effect']['in'] != 'disabled' ? 'animated '.$uniset['alert']['effect']['in'] : '',
			'alert_effect_out' 			=> isset($uniset['alert']['effect']['out']) && $uniset['alert']['effect']['out'] != 'disabled' ? 'animated '.$uniset['alert']['effect']['out'] : '',
			'alert_time' 				=> isset($uniset['alert']['time']) ? $uniset['alert']['time'] : 5,
			'fly_menu_desktop' 			=> isset($uniset['fly_menu']['desktop']) ? true : false,
			'fly_menu_mobile' 			=> isset($uniset['fly_menu']['mobile']) ? true : false,
			'fly_menu_product' 			=> isset($uniset['fly_menu']['product']) ? true : false,
			'fly_cart' 					=> isset($uniset['show_fly_cart']) ? true : false,
			'currency'					=> $currency,
			'curr_symbol_l' 			=> $this->currency->getSymbolLeft($currency),
			'curr_symbol_r' 			=> $this->currency->getSymbolRight($currency),
			'curr_decimal' 				=> $this->currency->getDecimalPlace($currency),
			'curr_decimal_p' 			=> $this->language->get('decimal_point'),
			'curr_thousand_p' 			=> $this->language->get('thousand_point'),
			'callback_metric_id'		=> isset($uniset['callback_metric_id']) ? $uniset['callback_metric_id'] : '',
			'callback_metric_target'	=> isset($uniset['callback_metric_target']) ? $uniset['callback_metric_target'] : '',
			'callback_analityc_category'=> isset($uniset['callback_analityc_category']) ? $uniset['callback_analityc_category'] : '',
			'callback_analityc_action'	=> isset($uniset['callback_analityc_action']) ? $uniset['callback_analityc_action'] : '',
			'quickorder_goal_id'		=> isset($uniset['quickorder_metric_id']) ? $uniset['quickorder_metric_id'] : 0,
			'checkout_goal_id'			=> isset($uniset['checkout_metric_id']) ? $uniset['checkout_metric_id'] : 0
		);
		
		$data['js_vars'] = base64_encode(json_encode($js_vars));
		
		return $data;
	}
	
	private function getMenuData() {
		$uniset = $this->config->get('config_unishop2');
		$lang_id = (int)$this->config->get('config_language_id');
		$store_id = (int)$this->config->get('config_store_id');
			
		$route = isset($this->request->get['route']) ? $this->request->get['route'] : '';
		$data['home'] = ($route == 'common/home') || !$route ? true : false;
		$menu_schema = isset($uniset['menu_schema']) && $uniset['menu_type'] == 1 ? $uniset['menu_schema'] : [];
			
		$data['menu_expanded'] = in_array($route, $menu_schema) || (!$route && in_array('common/home', $menu_schema)) ? true : false;
		$data['text_menu'] = isset($uniset[$lang_id]['text_menu']) ? $uniset[$lang_id]['text_menu'] : '';
		$data['menu_type'] = isset($uniset['menu_type']) ? $uniset['menu_type'] : 1;
			
		$headerlinks2 = isset($uniset[$lang_id]['headerlinks2']) ? $uniset[$lang_id]['headerlinks2'] : [];
			
		$data['headerlinks2'] = $data['additional_link'] = [];
			
		if($headerlinks2) {
			foreach($headerlinks2 as $headerlink2) {
				if(isset($headerlink2['show_in_cat'])) {
					$children_data = [];
						
					if(isset($headerlink2['children'])) {
						foreach ($headerlink2['children'] as $child) {
								
							$children2_data = [];
							
							if(isset($child['children'])) {
								foreach ($child['children'] as $child2) {
									$children2_data[] = array(
										'name'  => $child2['name'],
										'href'  => $child2['href']
									);
								}
							}
							
							$children_data[] = array(
								'name'  	=> $child['name'],
								'href'  	=> $child['href'],
								'disabled'	=> !$child['href'] ? true : false,
								'children'	=> $children2_data
							);
						}
					}
					
					$data['additional_link'][] = array(
						'name' 		=> $headerlink2['title'],
						'icon'		=> isset($headerlink2['icon']) ? $headerlink2['icon'] : '',
						'children'	=> $children_data,
						'column'	=> isset($headerlink2['column']) ? $headerlink2['column'] : 1,
						'href'		=> $headerlink2['link'],
						'disabled'	=> !$headerlink2['link'] ? true : false
					);
				} else {
					$data['headerlinks2'][] = array(
						'name' 		=> $headerlink2['title'],
						'icon'		=> isset($headerlink2['icon']) ? $headerlink2['icon'] : '',
						'column'	=> isset($headerlink2['column']) ? $headerlink2['column'] : 1,
						'href'		=> $headerlink2['link']
					);
				}
			}
		}
		
		return $data;
	}
	
	private function getCatalogData($setting) {
		$uniset = $this->config->get('config_unishop2');
		$lang_id = (int)$this->config->get('config_language_id');
		
		$this->load->language('extension/module/uni_othertext');
			
		$data['shop_name'] = $this->config->get('config_name');
		
		$route = isset($this->request->get['route']) ? $this->request->get['route'] : '';
		$menu_schema = isset($uniset['menu_schema']) ? $uniset['menu_schema'] : [];
		$data['menu_expanded'] = ($uniset['menu_type'] == 1 && in_array($route, $menu_schema)) ? true : false;
		
		$data['cat_desc_pos'] = $uniset['cat_desc_pos'];
		$data['subcategory_column'] = isset($uniset['subcategory_column']) ? implode(' ', $uniset['subcategory_column']) : '';
		$data['subcategory_mobile_view'] = isset($uniset['subcategory_mobile_view']) ? $uniset['subcategory_mobile_view'] : 'default';
		
		$data['show_grid_button'] = isset($uniset['show_grid_button']) ? true : false;
		$data['show_grid2_button'] = isset($uniset['show_grid2_button']) ? true : false;
		$data['show_list_button'] = isset($uniset['show_list_button']) ? true : false;
		$data['show_compact_button'] = isset($uniset['show_compact_button']) ? true : false;		

		$data['show_quick_order_text'] = isset($uniset['show_quick_order_text']) ? $uniset['show_quick_order_text'] : '';			
		$data['quick_order_icon'] = isset($uniset['show_quick_order']) ? $uniset[$lang_id]['quick_order_icon'] : '';
		$data['quick_order_title'] = isset($uniset['show_quick_order']) ? $uniset[$lang_id]['quick_order_title'] : '';
		$data['show_rating'] = isset($uniset['show_rating']) ? true : false;
		$data['wishlist_btn_disabled'] = isset($uniset['wishlist_btn_disabled']) ? true : false;
		$data['compare_btn_disabled'] = isset($uniset['compare_btn_disabled']) ? true : false;
		$data['show_attr_name'] = isset($uniset['show_attr_name']) ? true : false;
		$data['show_options'] = isset($uniset['show_options']) ? true : false;
		
		return $data;
	}
	
	private function getProductData($product_info = []) {
		
		if(!isset($product_info['product_id'])) {
			return [];
		}
		
		$this->load->language('extension/module/uni_othertext');
		$this->load->language('extension/module/uni_request');
		
		$this->load->model('extension/module/uni_new_data');
		
		$uniset = $this->config->get('config_unishop2');
		$lang_id = $this->config->get('config_language_id');
			
		$viewed_id = isset($this->request->cookie['viewed_id']) ? explode(',', $this->request->cookie['viewed_id']) : [];
			
		if (!in_array($product_info['product_id'], $viewed_id)) {
			array_unshift($viewed_id, $product_info['product_id']);
			setcookie('viewed_id', implode(',', array_slice($viewed_id, 0, 20)), time()+86400, '/');
		}
			
		$currency = $this->session->data['currency'];
			
		$data['hide_breadcrumbs'] = isset($uniset['hide_breadcrumbs']) ? true : false;
		$data['show_model'] = isset($uniset['show_product_model']) ? true : false;
		$data['show_manuf'] = isset($uniset['show_product_manuf']) ? true : false;
		$data['show_reward'] = isset($uniset['show_product_reward']) ? $uniset['show_product_reward'] : '';
		$data['show_length'] = isset($uniset['show_product_length']) ? $uniset['show_product_length'] : '';
			
		$data['uni_popup_img_effect_in'] = isset($uniset['popup_img_effect_in']) ? 'animated '.$uniset['popup_img_effect_in'] : false;
		$data['uni_popup_img_effect_out'] = isset($uniset['popup_img_effect_out']) ? 'animated '.$uniset['popup_img_effect_out'] : false;
		
		$data['show_quick_order_text_product'] = isset($uniset['show_quick_order_text_product']) ? true : false;
		$data['quick_order_icon'] = isset($uniset['show_quick_order']) ? $uniset[$lang_id]['quick_order_icon'] : '';
		$data['quick_order_title'] = isset($uniset['show_quick_order']) ? $uniset[$lang_id]['quick_order_title'] : '';
			
		$data['text_related'] = isset($uniset[$lang_id]['related_title']) ? $uniset[$lang_id]['related_title'] : $this->language->get('text_related');
			
		$data['quantity'] = $product_info['quantity'];
			
		$data['show_attr_group'] = $uniset['show_product_attr_group'];
		$data['show_attr_item'] = $uniset['show_product_attr_item'];
		$data['show_attr'] = isset($uniset['show_product_attr']) ? true : false;
		
		$data['show_rating'] = isset($uniset['show_rating']) ? true : false;
			
		if($product_info['quantity'] > 0) {
			$data['cart_btn_icon'] = $uniset[$lang_id]['cart_btn_icon'];
			$data['cart_btn_text'] = $uniset[$lang_id]['cart_btn_text'];
			$data['cart_btn_class'] = '';
			$data['quick_order'] = isset($uniset['show_quick_order']) ? true : false;
		} else {
			$data['cart_btn_icon'] = $uniset[$lang_id]['cart_btn_icon_disabled'];
			$data['cart_btn_text'] = $uniset[$lang_id]['cart_btn_text_disabled'];
			$data['cart_btn_class'] = $uniset['cart_btn_disabled'];
			$data['quick_order'] = isset($uniset['show_quick_order_quantity']) ? true : false;
		}
			
		$data['wishlist_btn_disabled'] = isset($uniset['wishlist_btn_disabled']) ? true : false;
		$data['compare_btn_disabled'] = isset($uniset['compare_btn_disabled']) ? true : false;
			
		$data['sku'] = !isset($uniset['sku_as_sticker']) ? $product_info['sku'] : '';
		$data['upc'] = !isset($uniset['upc_as_sticker']) ? $product_info['upc'] : '';
		$data['ean'] = !isset($uniset['ean_as_sticker']) ? $product_info['ean'] : '';
		$data['jan'] = !isset($uniset['jan_as_sticker']) ? $product_info['jan'] : '';
		$data['isbn'] = !isset($uniset['isbn_as_sticker']) ? $product_info['isbn'] : '';
		$data['mpn'] = !isset($uniset['mpn_as_sticker']) ? $product_info['mpn'] : '';
		$data['location'] = $product_info['location'];
			
		$data['text_sku'] = $uniset[$lang_id]['sku_text'];
		$data['text_upc'] = $uniset[$lang_id]['upc_text'];
		$data['text_ean'] = $uniset[$lang_id]['ean_text'];
		$data['text_jan'] = $uniset[$lang_id]['jan_text'];
		$data['text_isbn'] = $uniset[$lang_id]['isbn_text'];
		$data['text_mpn'] = $uniset[$lang_id]['mpn_text'];
		$data['text_location'] = $uniset[$lang_id]['location_text'];
			
		$data['weight'] = ($product_info['weight'] > 0) ? round($product_info['weight'], 2).' '.$this->weight->getUnit($product_info['weight_class_id']) : '';
		$data['length'] = ($product_info['length'] > 0 && $product_info['width'] > 0 && $product_info['height'] > 0) ? round($product_info['length'], 2).'&times;'.round($product_info['width'], 2).'&times;'.round($product_info['height'], 2).' '.$this->length->getUnit($product_info['length_class_id']) : '';
			
		$data['socialbutton'] = isset($uniset['socialbutton']) ? $uniset['socialbutton'] : [];
		
		$data['product_banner_position'] = $uniset['product_banner_position'];
		
		$data['product_banners'] = [];
		
		if(isset($uniset[$lang_id]['product_banners'])) {
			foreach($uniset[$lang_id]['product_banners'] as $banner) {
				if($banner['text']) {
					$data['product_banners'][] = array(
						'icon' 			=> $banner['icon'],
						'text' 			=> html_entity_decode($banner['text'], ENT_QUOTES, 'UTF-8'),
						'link' 			=> $banner['link'],
						'link_popup' 	=> isset($banner['link_popup']) ? true : false,
						'hide' 			=> isset($banner['hide']) ? true : false,
					);
				}
			}
		}
			
		$data['uni_product_tabs'] = [];
			
		$uni_request = $this->config->get('uni_request');
			
		if(isset($uni_request['question_list'])) {
			$this->document->addStyle('catalog/view/theme/unishop2/stylesheet/request.css');
			
			$data['uni_product_tabs'][] = [
				'id'			=> 'question',
				'icon' 			=> 'fa fa-question',
				'title' 		=> $this->language->get('tab_question'),
				'description'	=> ''
			];
		}
			
		if(isset($uniset['show_additional_tab'])) {
			$data['uni_product_tabs'][] = [
				'id'			=> 'additional',
				'icon' 			=> $uniset[$lang_id]['additional_tab_icon'],
				'title' 		=> $uniset[$lang_id]['additional_tab_title'],
				'description'	=> html_entity_decode($uniset[$lang_id]['additional_tab_text'], ENT_QUOTES, 'UTF-8')
			];
		}
			
		if (isset($uniset['show_related_news']) && $this->config->get('uni_news')) {
			
			$news_related = $this->load->controller('extension/module/uni_news_related');
				
			if($news_related) {
				$data['uni_product_tabs'][] = [
					'id'			=> 'news',
					'icon' 			=> $uniset[$lang_id]['related_news_icon'],
					'title' 		=> $uniset[$lang_id]['related_news_title'],
					'description'	=> $news_related
				];
			}
		}
			
		$data['manufacturer_descr'] = [];
		
		if(isset($uniset['show_manufacturer'])) {
			$data['manufacturer_position'] = (isset($uniset['manufacturer_position']) ? $uniset['manufacturer_position'] : '');
			$data['manufacturer_title'] = $uniset[$lang_id]['manufacturer_title'];
				
			$this->load->model('tool/image');
			$manufacturer_descr = $this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id']);
				
			if(isset($manufacturer_descr['description']) && $manufacturer_descr['description'] != '') {
				$data['manufacturer_descr'] = [
					'name'			=> $manufacturer_descr['name'],
					'description'	=> utf8_substr(strip_tags(html_entity_decode($manufacturer_descr['description'], ENT_QUOTES, 'UTF-8')), 0, $uniset['manufacturer_text_length']),
					'image'			=> $manufacturer_descr['image'] ? $this->model_tool_image->resize($manufacturer_descr['image'], $uniset['manufacturer_logo_w'], $uniset['manufacturer_logo_h']) : '',
					'href'			=> $this->url->link('product/manufacturer/info&manufacturer_id='.$product_info['manufacturer_id'])
				];
			}
		}
		
		$product_info['product_page'] = true;
			
		$new_data = $this->model_extension_module_uni_new_data->getNewData($product_info);
			
		$data['product']['stickers'] = $new_data['stickers'];
		$data['special_timer'] = $new_data['special_date_end'];
		$data['product']['quantity_indicator'] = $new_data['quantity_indicator'];
			
		$data['price_value'] = $this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'))*$this->currency->getValue($currency);
		$data['special_value'] = $this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax'))*$this->currency->getValue($currency);
		$data['discounts_value'] = $new_data['discounts'];
			
		$data['show_plus_minus_review'] = isset($uniset['show_plus_minus_review']) ? true : false;
		$data['plus_minus_review_required'] = isset($uniset['plus_minus_review_required']) ? true : false;
			
		$data['auto_related'] = isset($uniset['similar']['show']) ? $this->load->controller('extension/module/uni_auto_related') : '';
			
		$data['change_opt_img_p'] = isset($uniset['change_opt_img_p']) ? true : false;
		
		$data['microdata'] = [
			'name' 			=> $product_info['name'],
			'model' 		=> $product_info['model'],
			'sku' 			=> !isset($uniset['sku_as_sticker']) ? $product_info['sku'] : '',
			'mpn' 			=> !isset($uniset['mpn_as_sticker']) ? $product_info['mpn'] : '',
			'image'			=> $this->model_tool_image->resize($product_info['image'] ? $product_info['image'] : 'placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')),
			'category' 		=> $product_info['category_name'],
			'manufacturer'	=> $product_info['manufacturer'],
			'description' 	=> strip_tags($product_info['description']),
			'price' 		=> $this->tax->calculate($product_info['special'] ? $product_info['special'] : $product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'))*$this->currency->getValue($currency),
			'code' 			=> $currency,
			'review_status'	=> $this->config->get('config_review_status'),
			'reviews' 		=> $product_info['reviews'],
			'rating' 		=> $product_info['rating'],
			'url' 			=> $this->url->link('product/product', '&product_id='.$this->request->get['product_id'])
		];
		
		return $data;
	}
	
	private function getCartData($product = []) {
		
		if(!isset($product['product_id'])) {
			return [];
		}
		
		$option = $product['option'];
		$product_options = $product['options'];
		
		$currency = $this->session->data['currency'];
			
		$options = '';
			
		$product_price = $this->tax->calculate($product['special'] ? $product['special'] : $product['price'], $product['tax_class_id'], $this->config->get('config_tax'))*$this->currency->getValue($currency);
			
		foreach ($product_options as $key => $product_option) {
			if (!empty($option[$product_option['product_option_id']])) {
				
				$options .= (($key > 0) ? ', ' : '').$product_option['name'].':';
						
				if($product_option['type'] == 'select' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'radio') {
					foreach ($product_option['product_option_value'] as $value) {
						$option_id_arr = is_array($option[$product_option['product_option_id']]) ? $option[$product_option['product_option_id']] : array($option[$product_option['product_option_id']]);
							
						if(in_array($value['product_option_value_id'], $option_id_arr)) {
							$option_price = $this->tax->calculate($value['price'], $product['tax_class_id'], $this->config->get('config_tax'))*$this->currency->getValue($currency);
								
							$product_price = ($value['price_prefix'] == '+') ? $product_price + $option_price : $product_price - $option_price;
								
							$options .= ' '.$value['name'];
						}
					}
				} elseif($product_option['type'] == 'file') {
					$this->load->model('tool/upload');
						
					$upload_info = $this->model_tool_upload->getUploadByCode($option[$product_option['product_option_id']]);

					$options .= $upload_info ? ' '.$upload_info['name'] : '';
				} else {
					$options .= ' '.$option[$product_option['product_option_id']];
				}
			}
		}
			
		return [
			'id'		=> $product['product_id'], 
			'name' 		=> $product['name'], 
			'brand' 	=> isset($product['manufacturer']) ? $product['manufacturer'] : '', 
			'variant' 	=> $options, 
			'quantity'	=> $product['quantity'], 
			'price' 	=> $product_price
		];
	}
	
	private function getContactData() {
		$uniset = $this->config->get('config_unishop2');
		$lang_id = $this->config->get('config_language_id');
		$shop_telephone = $this->config->get('config_telephone');
		$shop_email = $this->config->get('config_email');
			
		$main_contacts = isset($uniset[$lang_id]['main_phones']) ? $uniset[$lang_id]['main_phones'] : [];
		$addtional_contacts = isset($uniset[$lang_id]['contacts']) ? $uniset[$lang_id]['contacts'] : [];
		
		$contacts = array_merge($main_contacts, $addtional_contacts);
		
		$data['main_contacts'] = [];
		
		foreach($contacts as $contact) {
			if($contact['number'] != $shop_telephone && $contact['number'] != $shop_email) {
				$data['contacts'][] = [
					'icon' 		=> $contact['icon'],
					'number' 	=> $contact['number'],
				];
			}
		}
		
		$data['shop_name'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
		$data['text_in_contacts'] = isset($uniset[$lang_id]['text_in_contacts']) ? html_entity_decode($uniset[$lang_id]['text_in_contacts'], ENT_QUOTES, 'UTF-8') : '';
		$data['contact_map'] = html_entity_decode($uniset['maps'], ENT_QUOTES, 'UTF-8');
		$data['shop_email'] = $shop_email;
		
		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}
		
		$data['microdata'] = [
			'name'			=> $this->config->get('config_name'),
			'image' 		=> (is_file(DIR_IMAGE . $this->config->get('config_logo'))) ?  $server.'image/'.$this->config->get('config_logo') : '',
			'url' 			=> $server,
			'description'	=> $this->config->get('config_meta_description'),
			'email'			=> $shop_email,
			'telephone'		=> $shop_telephone,
			'address'		=> $this->config->get('config_address'),
			'open_hours'	=> nl2br($this->config->get('config_open')),
			'currency'		=> $this->session->data['currency']
		];
		
		return $data;
	}
	
	private function getMergedStyle($main = '') {
		$uniset = $this->config->get('config_unishop2');
		$store_id = (int)$this->config->get('config_store_id');
		
		$route = isset($this->request->get['route']) ? $this->request->get['route'] : '';
		
		$stop_routes = [
			//'checkout/simplecheckout'
		];
		
		if(in_array($route, $stop_routes)) {
			return false;
		}
		
		$uni_styles = [
			'catalog/view/theme/unishop2/stylesheet/bootstrap.min.css',
			'catalog/view/theme/unishop2/stylesheet/stylesheet.css',
			'catalog/view/theme/unishop2/stylesheet/font-awesome.min.css',
			'catalog/view/theme/unishop2/stylesheet/userstyle-'.$store_id.'.css',
			'catalog/view/theme/unishop2/stylesheet/animate.css',
		];
		
		$styles = $this->document->getStyles();
		
		foreach($styles as $style) {
			$uni_styles[] = $style['href'];
		}
		
		if(isset($uniset['custom_style']) && $uniset['custom_style'] != '') {
			$uni_styles[] = 'catalog/view/theme/unishop2/stylesheet/'.$uniset['custom_style'];
		}
		
		$name = 'uni-merged.'.substr(md5(implode(',', $uni_styles)), 0, 8).'.min';
		
		if (!file_exists(DIR_TEMPLATE.'unishop2/stylesheet/'.$name.'.css')) {
			$contents = '';
		
			foreach($uni_styles as $filename) {
				
				$filename = strpos($filename, '?v=') ? substr($filename, 0, strpos($filename, '?v=')) : $filename;
				
				if(file_exists($filename)) {
					$handle = fopen($filename, "r");
					$contents .= fread($handle, filesize($filename));
					fclose($handle);
				}
			}
		
			$contents = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $contents);
			$contents = str_replace(': ', ':', $contents);
			$contents = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $contents);
		
			$handle = fopen(DIR_TEMPLATE.'unishop2/stylesheet/'.$name.'.css', 'w');
			$result = fwrite($handle, $contents);
			fclose($handle);
		}
		
		return $name;
	}
	
	private function getMergedScript($footer = '') {
		
		$route = isset($this->request->get['route']) ? $this->request->get['route'] : '';
		
		$stop_routes = [
			//'checkout/simplecheckout'
		];
		
		if(in_array($route, $stop_routes)) {
			return false;
		}
		
		$uni_scripts = [
			'catalog/view/theme/unishop2/js/jquery-2.1.1.min.js',
			//'catalog/view/theme/unishop2/js/jquery-3.4.0.min.js',
			'catalog/view/theme/unishop2/js/bootstrap.min.js',
			'catalog/view/theme/unishop2/js/common.js',
			'catalog/view/theme/unishop2/js/menu-aim.min.js',
			'catalog/view/theme/unishop2/js/owl.carousel.min.js'
		];
		
		$scripts = $this->document->getScripts();
		
		$uni_scripts = array_merge($uni_scripts, $scripts);

		$name = 'uni-merged.'.substr(md5(implode(',', $uni_scripts)), 0, 8).'.min';
		
		if (!file_exists(DIR_TEMPLATE.'unishop2/js/'.$name.'.js')) {
			
			$contents = '';
			
			$uniset = $this->config->get('config_unishop2');
			
			$google_min = isset($uniset['merge_js_closure']) ? true : false;
			
			foreach($uni_scripts as $filename) {
				
				$filename = strpos($filename, '?v=') ? substr($filename, 0, strpos($filename, '?v=')) : $filename;
				
				if(file_exists($filename)) {
					$handle = fopen($filename, "r");
					$data = fread($handle, filesize($filename));
					fclose($handle);
				
					if($google_min && substr($filename, -6) != 'min.js') {
						$output = $this->GoogleMin($data);
				
						if($output) {
							$data = $output;
						} else {
							$this->log->write('Warning: Google Closure Compiler not compile '.$filename);
						}
					}
				
					$contents .= $data;
				}
			}
			
			//$contents = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $contents);
			$contents = preg_replace('/^\/\/!.+(?:\r\n|\r|\n)/m', '', $contents);
			
			$handle = fopen(DIR_TEMPLATE.'unishop2/js/'.$name.'.js', 'w');
			$result = fwrite($handle, $contents);
			fclose($handle);
		}
		
		return $name;
	}
	
	private function GoogleMin($data) {
		$post_data = http_build_query(
			array(
				'compilation_level'	=> 'SIMPLE_OPTIMIZATIONS',
				'js_code' 			=> $data,
				'output_format' 	=> 'text',
				'output_info' 		=> 'compiled_code'
			),
			null,
			'&'
		);
			
		$curl = curl_init('https://closure-compiler.appspot.com/compile');
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
		$result = curl_exec($curl);
		curl_close($curl);
		
		return ($result !== false && $result != '' && substr($result, 0, 5) != 'Error') ? $result : '';
	}
}
?>