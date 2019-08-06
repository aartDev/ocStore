<?php
class ControllerExtensionModuleUniFiveInOne extends Controller {
	public function index($setting) {
		static $module = 0;
		
		$this->load->language('extension/module/uni_othertext');

		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$this->load->model('extension/module/uni_five_in_one');
		$this->load->model('extension/module/uni_new_data');
		
		$store_id = (int)$this->config->get('config_store_id');
		$lang_id = (int)$this->config->get('config_language_id');
		$currency = $this->session->data['currency'];
		$customer_group_id = (int)$this->config->get('config_customer_group_id');
		
		$uniset = $this->config->get('config_unishop2');
		$settings = isset($setting['set'][$store_id]) ? $setting['set'][$store_id] : [];
		
		$data['show_quick_order_text'] = isset($uniset['show_quick_order_text']) ? $uniset['show_quick_order_text'] : '';			
		$data['quick_order_icon'] = isset($uniset['show_quick_order']) ? $uniset[$lang_id]['quick_order_icon'] : '';
		$data['quick_order_title'] = isset($uniset['show_quick_order']) ? $uniset[$lang_id]['quick_order_title'] : '';
		$data['show_rating'] = isset($uniset['show_rating']) ? true : false;
		$data['wishlist_btn_disabled'] = isset($uniset['wishlist_btn_disabled']) ? true : false;
		$data['compare_btn_disabled'] = isset($uniset['compare_btn_disabled']) ? true : false;
		$data['show_attr_name'] = isset($uniset['show_attr_name']) ? true : false;
		$data['show_options'] = isset($uniset['show_options']) ? true : false;
		
		/*
		$module_items = $uniset['module_items'];
		
		$items_string = '{';
		
		foreach($module_items as $key => $item) {
			$items_string .= $item['key'].':{items:'.$item['item'].'}';
			
			if($key+1 < count($module_items)) {
				$items_string .= ', ';
			}
		}
		
		$items_string .= '}';
		
		$data['items_string'] = $items_string;
		*/
		
		$md5_name =  substr(md5($setting['name']), 0, 8);
		$cache_name = 'product.unishop.five.in.one.'.$md5_name.'.'.$currency.'.'.$customer_group_id.'.'.$lang_id.'.'.$store_id;

		$i = 0;
		
		$tabs = $this->cache->get($cache_name);
		
		if(!$tabs) {
			
			$tabs = [];
			
			if(count($settings) > 1) {
				foreach ($settings as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $settings);
			}
			
			foreach($settings as $key => $tab_settings) {
				if(isset($tab_settings['status'])) {
			
					$tabs[$i]['title'] = isset($tab_settings['title'][$lang_id]) ? $tab_settings['title'][$lang_id] : '';
					$tabs[$i]['type'] = isset($tab_settings['type']) ? 'grid' : 'carousel';
					
					$products = $tabs[$i]['products'] = [];
					
					switch($key) {
						case 'latest':
							$limit = isset($tab_settings['limit']) ? $tab_settings['limit'] : 5;
							$products = $this->model_extension_module_uni_five_in_one->getLatest($limit);
							break;
						case 'special':
							$limit = isset($tab_settings['limit']) ? $tab_settings['limit'] : 5;
							$products = $this->model_extension_module_uni_five_in_one->getSpecial($limit);
							break;
						case 'bestseller':
							$limit = isset($tab_settings['limit']) ? $tab_settings['limit'] : 5;
							$products = $this->model_extension_module_uni_five_in_one->getBestseller($limit);
							break;
						case 'popular':
							$limit = isset($tab_settings['limit']) ? $tab_settings['limit'] : 5;
							$products = $this->model_extension_module_uni_five_in_one->getPopular($limit);
							break;
						default:
							$limit = isset($tab_settings['limit']) ? $tab_settings['limit'] : 5;
							$results = array_slice(isset($tab_settings['product']) ? $tab_settings['product'] : [], 0, (int)$limit);
							$products = $this->model_extension_module_uni_five_in_one->getFeatured($results);
					}
			
					foreach ($products as $product) {
						if($product['image']) {
							$image = $this->model_tool_image->resize($product['image'], $tab_settings['thumb_width'], $tab_settings['thumb_height']);
						} else {
							$image = $this->model_tool_image->resize('placeholder.png', $tab_settings['thumb_width'], $tab_settings['thumb_width']);
						}

						if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
							$price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $currency);
						} else {
							$price = false;
						}

						if ((float)$product['special']) {
							$special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')), $currency);
						} else {
							$special = false;
						}

						if ($this->config->get('config_tax')) {
							$tax = $this->currency->format((float)$product['special'] ? $product['special'] : $product['price'], $currency);
						} else {
							$tax = false;
						}

						if ($this->config->get('config_review_status')) {
							$rating = $product['rating'];
						} else {
							$rating = false;
						}
						
						$img_size = [
							'width'	  => $tab_settings['thumb_width'], 
							'height'  => $tab_settings['thumb_height']
						];
				
						$new_data = $this->model_extension_module_uni_new_data->getNewData($product, $img_size);
						$show_description = isset($uniset['show_description']) && !isset($uniset['show_description_alt']) || isset($uniset['show_description_alt']) && !$new_data['attributes'] ? true : false;
				
						if($new_data['special_date_end']) {
							$tabs[$i]['show_timer'] = true;
						}
				
						if($product['quantity'] > 0) {
							$show_quantity = isset($uniset['show_quantity_cat']) ? true : false;
							$cart_btn_icon = $uniset[$lang_id]['cart_btn_icon'];
							$cart_btn_text = $uniset[$lang_id]['cart_btn_text'];
							$cart_btn_class = '';
							$quick_order = isset($uniset['show_quick_order']) ? true : false;
						} else {
							$show_quantity = isset($uniset['show_quantity_cat_all']) ? true : false;
							$cart_btn_icon = $uniset[$lang_id]['cart_btn_icon_disabled'];
							$cart_btn_text = $uniset[$lang_id]['cart_btn_text_disabled'];
							$cart_btn_class = $uniset['cart_btn_disabled'];
							$quick_order = isset($uniset['show_quick_order_quantity']) ? true : false;
						}

						$tabs[$i]['products'][] = array(
							'product_id' 		=> $product['product_id'],
							'thumb'   	 		=> $image,
							'name'    			=> $product['name'],
							'description' 		=> utf8_substr(strip_tags(html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_'.$this->config->get('config_theme').'_product_description_length')) . '..',
							'tax'         		=> $tax,
							'price'   	 		=> $price,
							'special' 	 		=> $special,
							'rating'     		=> $rating,
							'reviews'    		=> sprintf($this->language->get('text_reviews'), (int)$product['reviews']),
							'num_reviews' 		=> isset($uniset['show_rating_count']) ? $product['reviews'] : '',
							'href'    	 		=> $this->url->link('product/product', 'product_id='.$product['product_id']),
							'additional_image'	=> $new_data['additional_image'],
							'stickers' 			=> $new_data['stickers'],
							'special_date_end' 	=> $new_data['special_date_end'],
							'minimum' 			=> $product['minimum'] ? $product['minimum'] : 1,
							'quantity_indicator'=> $new_data['quantity_indicator'],
							'price_value' 		=> $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))*$this->currency->getValue($currency),
							'special_value' 	=> $this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax'))*$this->currency->getValue($currency),
							'discounts'			=> $new_data['discounts'],
							'attributes' 		=> $new_data['attributes'],
							'options'			=> $new_data['options'],
							'show_description'	=> $show_description,
							'show_quantity'		=> $show_quantity,
							'cart_btn_icon'		=> $cart_btn_icon,
							'cart_btn_text'		=> $cart_btn_text,
							'cart_btn_class'	=> $cart_btn_class,
							'quick_order'		=> $quick_order,
						);
					}
					
					if($tabs[$i]['products']) {
						$i++;
					}
				}
			}
			
			$this->cache->set($cache_name, $tabs);
		}
		
		$data['tabs'] = $tabs;
		$data['module'] = $module++;
		
		return $this->load->view('extension/module/uni_five_in_one', $data);
	}
}