<?php 
class ControllerExtensionModuleUniLoginRegister extends Controller {
	public function index() {
		if(isset($this->request->post['flag'])) {
			$this->load->language('account/register');
			$this->load->language('extension/module/uni_login_register');
		
			$data['login_link'] = $this->url->link('account/account', '', 'SSL');
			$data['register_link'] = $this->url->link('account/register', '', 'SSL');
		
			$uniset = $this->config->get('config_unishop2');
			$lang_id = $this->config->get('config_language_id');
		
			$data['show_login'] = isset($uniset['show_login']) ? $uniset['show_login'] : '';
			$data['login_mail_text'] = $uniset[$lang_id]['login_mail_text'];
			$data['login_password_text'] = $uniset[$lang_id]['login_password_text'];
			$data['show_login_forgotten'] = isset($uniset['show_login_forgotten']) ? $uniset['show_login_forgotten'] : '';
			$data['show_login_register'] = isset($uniset['show_login_register']) ? $uniset['show_login_register'] : '';
			$data['show_register'] = isset($uniset['show_register']) ? $uniset['show_register'] : '';
			$data['show_name'] = isset($uniset['show_name']) ? $uniset['show_name'] : '';
			$data['register_name_text'] = $uniset[$lang_id]['register_name_text'];
			$data['show_lastname'] = isset($uniset['show_lastname']) ? $uniset['show_lastname'] : '';
			$data['register_lastname_text'] = $uniset[$lang_id]['register_lastname_text'];
			$data['show_phone'] = isset($uniset['show_phone']) ? $uniset['show_phone'] : '';
			$data['register_phone_text'] = $uniset[$lang_id]['register_phone_text'];
			$data['register_phone_mask'] = $uniset['register_phone_mask'];
			$data['register_mail_text'] = $uniset[$lang_id]['register_mail_text'];
			$data['register_password_text'] = $uniset[$lang_id]['register_password_text'];
		
			$data['show_register_login'] = isset($uniset['show_register_login']) ? $uniset['show_register_login'] : '';
		
			$data['register_link'] = $this->url->link('account/register', '', true);
		
			$data['logged'] = $this->customer->isLogged();
		
			$this->load->model('account/customer_group');
			$data['customer_groups'] = [];
			
			if (is_array($this->config->get('config_customer_group_display'))) {
				$customer_groups = $this->model_account_customer_group->getCustomerGroups();
			
				foreach ($customer_groups as $customer_group) {
					if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
						$data['customer_groups'][] = $customer_group;
					}
				}
			}
		
			if (isset($this->request->post['customer_group_id'])) {
				$data['customer_group_id'] = $this->request->post['customer_group_id'];
			} else {
				$data['customer_group_id'] = $this->config->get('config_customer_group_id');
			}
		
			$data['text_agree'] = '';
		
			if ($this->config->get('config_account_id') && isset($uniset['show_register_confirm'])) {
				$this->load->model('catalog/information');
			
				$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
				$data['text_agree'] = $information_info ? sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_account_id'), true), $information_info['title'], $information_info['title']) : '';
			}
			
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
				$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '');
			} else {
				$data['captcha'] = '';
			}
		
			$type = isset($this->request->post['type']) ? $this->request->post['type'] : '';
		
			if($type == 'login' || $type == 'register') {
				$template = $type;
			} else {
				return false;
			}
		
			$this->response->setOutput($this->load->view('extension/module/uni_'.$template, $data));
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
	
	public function login() {
		$this->load->model('account/customer');
		$this->load->language('extension/module/uni_login_register');
		
		$json = [];
		
		$email = isset($this->request->post['email']) ? htmlspecialchars(strip_tags($this->request->post['email'])) : '';
		$password = isset($this->request->post['password']) ? htmlspecialchars(strip_tags($this->request->post['password'])) : '';
	
		if (!$this->customer->login($email, $password)) {
			$json['error'] = $this->language->get('error_popup_login');
		} else {
			$json['redirect'] = $this->url->link('account/account', '', true);
		}
		
		if (!$json) {
			unset($this->session->data['guest']);
			unset($this->session->data['shipping_country_id']);	
			unset($this->session->data['shipping_zone_id']);	
			unset($this->session->data['shipping_postcode']);
			unset($this->session->data['payment_country_id']);	
			unset($this->session->data['payment_zone_id']);	
		}
		
		$this->response->setOutput(json_encode($json));	
	}
	
	public function register() {
		$this->load->language('account/register');
		$this->load->language('extension/module/uni_login_register');
		
		$this->load->model('account/customer');
		
		$uniset = $this->config->get('config_unishop2');
		$lang_id = $this->config->get('config_language_id');
		
		$json = [];
						
		if (isset($uniset['show_name']) && isset($this->request->post['firstname'])) {
			if ((utf8_strlen($this->request->post['firstname']) < 3) || (utf8_strlen($this->request->post['firstname']) > 32)) {
				$json['error']['firstname'] = $this->language->get('error_firstname');
			}
		}
			
		if (isset($uniset['show_lastname']) && isset($this->request->post['lastname'])) {
			if ((utf8_strlen($this->request->post['lastname']) < 3) || (utf8_strlen($this->request->post['lastname']) > 32)) {
				$json['error']['lastname'] = $this->language->get('error_lastname');
			}
		}
		
		if (isset($uniset['show_phone']) && isset($this->request->post['telephone'])) {
			if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
				$json['error']['telephone'] = $this->language->get('error_telephone');
			}
		}
			
		$email = isset($this->request->post['email']) ? htmlspecialchars(strip_tags($this->request->post['email'])) : '';
		
		if ((utf8_strlen($email) > 96) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$json['error']['email'] = $this->language->get('error_email');
		}
	
		if ($this->model_account_customer->getTotalCustomersByEmail($email)) {
			$json['error']['email'] = $this->language->get('error_exists');
		}
			
		$this->load->model('account/customer_group');
			
		if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->post['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}
			
		$password = isset($this->request->post['password']) ? htmlspecialchars(strip_tags($this->request->post['password'])) : '';
	
		if ((utf8_strlen($password) < 4) || (utf8_strlen($password) > 20)) {
			$json['error']['password'] = $this->language->get('error_password');
		}
			
		if(isset($uniset['show_register_confirm'])) {
			if ($this->config->get('config_account_id')) {
				$this->load->model('catalog/information');
				
				$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
					
				if ($information_info && !isset($this->request->post['confirm'])) {
						$json['error']['confirm'] = sprintf($this->language->get('error_agree'), $information_info['title']);
				}
			}
		}
		
		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
			$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

			if ($captcha) {
				$json['error']['captcha'] = $captcha;
			}
		}
		
		if (!$json) {
			$data['firstname'] = isset($this->request->post['firstname']) ? $this->request->post['firstname'] : '';
			$data['lastname'] = isset($this->request->post['lastname']) ? $this->request->post['lastname'] : '';
			$data['email'] = $email;
			$data['telephone'] = isset($this->request->post['telephone']) ? $this->request->post['telephone'] : '';
			$data['password'] = $password;
			$data['customer_group_id'] = $customer_group_id;
			$data['fax'] = '';
			$data['company'] = '';
			$data['address_1'] = '';
			$data['address_2'] = '';
			$data['postcode'] = '';
			$data['city'] = '';
			$data['country_id'] = $this->config->get('config_country_id') ? $this->config->get('config_country_id') : 0;
			$data['zone_id'] = $this->config->get('config_zone_id') ? $this->config->get('config_zone_id') : 0;	
		
			$customer_id = $this->model_account_customer->addCustomer($data);
		
			$this->session->data['account'] = 'register';
							  	  
			if ($this->customer->login($email, $password)) {
				$json['redirect'] = $this->url->link('account/account', '', true);
			}
			
			$this->load->model('account/customer_group');
			
			$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);
			
			if ($customer_group_info['approval']) {
				$json['appruv'] = $this->language->get('text_appruv');
			}
			
			unset($this->session->data['guest']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);	
			unset($this->session->data['payment_methods']);
		}	
		
		$this->response->setOutput(json_encode($json));	
	}
}
?>