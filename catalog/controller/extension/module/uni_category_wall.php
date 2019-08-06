<?php
class ControllerExtensionModuleUniCategoryWall extends Controller {
	public function index($setting) {
		static $module = 0;
		
		$this->load->language('extension/module/uni_category_wall');
	
		$this->load->model('extension/module/uni_category_wall');
		$this->load->model('catalog/category');
		$this->load->model('tool/image');
		
		$this->document->addStyle('catalog/view/theme/unishop2/stylesheet/category_wall.css');
		
		$store_id = (int)$this->config->get('config_store_id');
		$lang_id = (int)$this->config->get('config_language_id');
		$md5_name = substr(md5($setting['name']), 0, 8);
		
		$data['heading_title'] = isset($setting['title'][$lang_id]) ? $setting['title'][$lang_id] : '';

		$data['categories'] = [];
		
		$data['columns'] = $setting['columns'] ? implode(' ', $setting['columns']) : '';
		
		$result = isset($setting['categories'][$store_id]) ? $setting['categories'][$store_id] : [];
		
		$categories = $this->model_extension_module_uni_category_wall->getCategories($result, $md5_name);
		
		if($categories) {
			foreach($categories as $category) {
			
				$childs_data = [];
			
				if($category['children']) {
					foreach($category['children'] as $child) {
						$childs_data[] = array(
							'category_id'	=> $child['category_id'],
							'name' 			=> $child['name'],
							'href' 			=> $this->url->link('product/category', 'path='.$category['category_id'].'_'.$child['category_id'])
						);
					}
				}
					
				$data['categories'][] = array(
					'category_id' 	=> $category['category_id'],
					'name' 			=> $category['name'],
					'image' 		=> $this->model_tool_image->resize($category['image'], 320, 240),
					'href'        	=> $this->url->link('product/category', 'path='.$category['category_id']),
					'childs'		=> $childs_data
				);
			}
		}

		$data['module'] = $module++;

		return $this->load->view('extension/module/uni_category_wall', $data);
	}
}