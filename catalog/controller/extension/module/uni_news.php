<?php
class ControllerExtensionModuleUniNews extends Controller {
	public function index($setting) {
		static $module = 0;
		
		$this->load->language('extension/module/uni_othertext');
		$this->load->language('extension/module/uni_news');
		
		$this->load->model('extension/module/uni_news');
		$this->load->model('tool/image');
		
		$lang_id = $this->config->get('config_language_id');
		$store_id = (int)$this->config->get('config_store_id');
		
		$this->document->addStyle('catalog/view/theme/unishop2/stylesheet/news.css');
		
		$data['heading_title'] = $setting['title'][$lang_id] ? $setting['title'][$lang_id] : $this->language->get('heading_title');
		$data['type_view'] = isset($setting['view_type']) ? 'grid' : 'carousel';
		
		$category_id = isset($setting['category']) ? $setting['category'] : 0;
		
		$cache_name = 'unishop.news.short.'.$category_id.'.'.$lang_id.'.'.$store_id;
		
		$data['news'] = $this->cache->get($cache_name);
		
		if(!$data['news']) {
			
			$filter_data = array(
				'filter_category_id' => $category_id,
				'filter_sub_category'=> isset($setting['sub_category']) ? true : false,
				'limit'				 => $setting['limit'],
				'start'				 => 0,
			);

			$results = $this->model_extension_module_uni_news->getNews($filter_data);
			
			$data['news'] = [];

			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], isset($setting['thumb_width']) ? $setting['thumb_width'] : 320, isset($setting['thumb_height']) ? $setting['thumb_height'] : 240);
				} else {
					$image = $image = $this->model_tool_image->resize('placeholder.png', isset($setting['thumb_width']) ? $setting['thumb_width'] : 320, isset($setting['thumb_height']) ? $setting['thumb_height'] : 240);
				}

				$description = utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, isset($setting['numchars']) ? $setting['numchars'] : 200) . '..';
				
				$news_category = $this->model_extension_module_uni_news->getCategory($result['category_id']);

				$data['news'][] = array(
					'name'        	=> $result['name'],
					'image'			=> $image,
					'description'	=> $description,
					'href'         	=> $this->url->link('information/uni_news_story', 'news_id='.$result['news_id']),
					'category_name' => isset($news_category['name']) ? $news_category['name'] : '',
					'category_href' => isset($news_category['href']) ? $this->url->link('information/uni_news', 'news_path='.$news_category['href']) : '',
					'viewed'   		=> $result['viewed'],
					'posted'   		=> date('d.m.y', strtotime($result['date_added'])),
				);
			}
		
			$this->cache->set($cache_name, $data['news']);
		}
		
		$data['module'] = $module++;

		return $this->load->view('extension/module/uni_news', $data);
	}
}
?>