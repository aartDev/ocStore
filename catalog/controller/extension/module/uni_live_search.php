<?php
class ControllerExtensionModuleUniLiveSearch extends Controller {
	public function index() {
		if(isset($this->request->post['flag'])) {
			$this->load->model('catalog/product');
			$this->load->model('tool/image');
		
			$this->load->language('extension/module/uni_othertext');
		
			$uniset = $this->config->get('config_unishop2');
			$language_id = $this->config->get('config_language_id');
		
			$data['show_search'] = isset($uniset['show_search']) ? $uniset['show_search'] : '';
			
			$search = isset($this->request->post['filter_name']) ? $this->request->post['filter_name'] : '';
			$search_sort = isset($uniset['search_sort']) ? $uniset['search_sort'] : '';
			$search_order = isset($uniset['search_order']) ? $uniset['search_order'] : '';
		
			$data['search_phrase'] = urlencode($search);
			
			if(isset($uniset['search_description'])) {
				$data['search_description'] = $search_description = true;
			} else {
				$data['search_description'] = $search_description = false;
			}
			
			if(isset($this->request->post['category_id'])) {
				$data['category_id'] = $category_id = $this->request->post['category_id'];
			} else {
				$data['category_id'] = $category_id = 0;
			}

			$data['products'] = [];
		
			$currency = $this->session->data['currency'];
		
			if ($search) {
				$filter_data = array(
					'filter_name'         => $search,
					'filter_tag'          => $search,
					'filter_description'  => $search_description,
					'filter_category_id'  => $category_id,
					'filter_sub_category' => 1,
					'sort'                => $search_sort,
					'order'               => $search_order,
					'start'               => 0,
					'limit'               => $uniset['search_limit']
				);
				
				$results = $this->model_catalog_product->getProducts($filter_data);
				$results_total = $this->model_catalog_product->getTotalProducts($filter_data);

				foreach ($results as $result) {
					if ($result['image']) {
						$image = $this->model_tool_image->resize($result['image'], $uniset['search_image_w'], $uniset['search_image_h']);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $uniset['search_image_w'], $uniset['search_image_h']);
					}

					if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $currency);
					} else {
						$price = false;
					}

					if ((float)$result['special']) {
						$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $currency);
					} else {
						$special = false;
					}

					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $currency);
					} else {
						$tax = false;
					}

					if ($this->config->get('config_review_status')) {
						$rating = (int)$result['rating'];
					} else {
						$rating = false;
					}
					
					$data['products'][] = array(
						'product_id'  	=> $result['product_id'],
						'image'      	=> isset($uniset['show_search_image']) ? $image : '',
						'name' 			=> utf8_substr(strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')), 0, $uniset['search_name_length']) . '..',
						'description' 	=> isset($uniset['show_search_description']) ? utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $uniset['search_description_length']) . '..' : '',
						'rating'		=> isset($uniset['show_search_rating']) ? $rating : -1,
						'price'      	=> isset($uniset['show_search_price']) ? $price : '',
						'special'     	=> $special,
						'href'       	=> $this->url->link('product/product', 'product_id=' . $result['product_id'])
					);
				}
			
				$data['products_total'] = $results_total;
				$data['show_more'] = $results_total > $uniset['search_limit'] ? true : false;
			}
		
			$this->response->setOutput($this->load->view('extension/module/uni_live_search', $data));
		} else {
			$this->load->language('extension/module/uni_othertext');
			
			$this->document->setTitle($this->language->get('text_error'));
			
			$data['breadcrumbs'] = [];
			
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			);
			
	     	$data['breadcrumbs'][] = array(
	        	'href'      => $this->url->link('extension/module/uni_request'),
	        	'text'      => $this->language->get('text_error'),
	     	);
			
			$data['heading_title'] = $this->language->get('text_error');
		
			$data['continue'] = $this->url->link('common/home');
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');
				
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
				
			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}
}
