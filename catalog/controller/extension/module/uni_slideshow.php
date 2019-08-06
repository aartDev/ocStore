<?php
class ControllerExtensionModuleUniSlideshow extends Controller {
	public function index($setting) {
		static $module = 0;
		
		$lang_id = $this->config->get('config_language_id');
		
		$this->load->model('tool/image');
		
		$this->document->addStyle('catalog/view/theme/unishop2/stylesheet/slideshow.css');
		
		$data['effect_in'] = $setting['effect_in'];
		$data['effect_out'] = $setting['effect_out'];
		$data['delay'] = $setting['delay'];
		$data['hide'] = isset($setting['hide']) ? true : false;
		$data['width'] = $setting['width'];
		$data['height'] = $setting['height'];

		$data['banners'] = [];
		
		$results = $setting['slides'] ? $setting['slides'] : [];
		
		if(count($results) > 1) { 
			$sort_order = []; 

			foreach ($results as $key => $value) {
				$sort_order[$key] = $value['sort'];
			}
		
			array_multisort($sort_order, SORT_ASC, $results);
		}

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$data['banners'][] = array(
					'image' 	=> $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']),
					'title' 	=> isset($result['title'][$lang_id]) ? $result['title'][$lang_id] : '',
					'text' 		=> isset($result['text'][$lang_id]) ? $result['text'][$lang_id] : '',
					'link' 	 	=> isset($result['link'][$lang_id]) ? $result['link'][$lang_id] : '',
					'button' 	=> isset($result['button'][$lang_id]) ? $result['button'][$lang_id] : '',
				);
			}
		}

		$data['module'] = $module++;

		return $this->load->view('extension/module/uni_slideshow', $data);
	}
}