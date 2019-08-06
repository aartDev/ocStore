<?php
class ControllerExtensionModuleUniHomeBanner extends Controller {
	public function index() {		
		$uniset = $this->config->get('config_unishop2');
		$lang_id = $this->config->get('config_language_id');
		
		$data['home_banners'] = [];
		
		if(isset($uniset[$lang_id]['home_banners'])) {
			foreach($uniset[$lang_id]['home_banners'] as $banner) {
				if($banner['text']) {
					$data['home_banners'][] = array(
						'icon' 			=> $banner['icon'],
						'text' 			=> html_entity_decode($banner['text'], ENT_QUOTES, 'UTF-8'),
						'text1' 		=> html_entity_decode($banner['text1'], ENT_QUOTES, 'UTF-8'),
						'link' 			=> $banner['link'],
						'link_popup'	=> isset($banner['link_popup']) ? true : false,
						'hide' 			=> isset($banner['hide']) ? true : false,
					);
				}
			}
		}
		
		return $this->load->view('extension/module/uni_home_banner', $data);
	}
}
?>
