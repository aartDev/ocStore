<?php  
class ControllerExtensionModuleUniCustomMenu extends Controller {
	public function index($setting) {
	
		$this->load->model('extension/module/uni_custom_menu');
		
		$this->document->addStyle('catalog/view/theme/unishop2/stylesheet/custom_menu.css');
		
		$data['custom_menu'] = $this->model_extension_module_uni_custom_menu->getCustomMenu();
		
		$language_id = $this->config->get('config_language_id');
		$data['head'] = $setting['uni_custom_menu_module'][$language_id]['head'];
		$data['style'] = $setting['uni_custom_menu_module']['style'];
		if(isset($setting['uni_custom_menu_module']['in_module'])) {
			$data['in_module'] = $setting['uni_custom_menu_module']['in_module'];
		} else {
			$data['in_module'] = array();
		}
		
		return $this->load->view('extension/module/uni_custom_menu', $data);
  	}

}
?>