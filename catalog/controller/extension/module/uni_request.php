<?php  
class ControllerExtensionModuleUniRequest extends Controller {
	public function index() {
		if (isset($this->request->post['reason'])) {
			$this->load->language('extension/module/uni_othertext');
			$this->load->language('extension/module/uni_request');
			$this->load->language('account/register');
	
			$uniset = $this->config->get('config_unishop2');
			$lang_id = $this->config->get('config_language_id');
		
			$settings = $this->config->get('uni_request') ? $this->config->get('uni_request') : [];
		
			$data['name_text'] = $uniset[$lang_id]['callback_name_text'];
			$data['phone_text'] = $uniset[$lang_id]['callback_phone_text'];
			$data['mail_text'] = $uniset[$lang_id]['callback_mail_text'];
			$data['comment_text'] = $uniset[$lang_id]['callback_comment_text'];
		
			$data['reason'] = isset($this->request->post['reason']) && $this->request->post['reason'] != '' ? htmlspecialchars(strip_tags($this->request->post['reason'])) : '';
		
			if ($settings) {
				switch ($data['reason']) {
					case $settings['heading_notify'][$lang_id]:
						$data['show_phone'] = isset($settings['notify_phone']) ? true : false;
						$data['show_email'] = isset($settings['notify_email']) ? true : false;
						$data['show_comment'] = false;
						break;
					case $settings['heading_question'][$lang_id]:
						$data['show_phone'] = isset($settings['question_phone']) ? true : false;
						$data['show_email'] = isset($settings['question_email']) ? true : false;
						$data['show_comment'] = true;
						break;
					default:
						$data['show_phone'] = true;
						$data['show_email'] = true;
						$data['show_comment'] = true;
						break;
				}
			} else {
				$data['show_phone'] = true;
				$data['show_email'] = true;
				$data['show_comment'] = true;
			}
		
			$data['product_id'] = isset($this->request->post['id']) && $this->request->post['id'] != '' ? (int)$this->request->post['id'] : '';
			$data['phone_mask'] = isset($uniset['callback_phone_mask']) ? $uniset['callback_phone_mask'] : '';
		
			$data['show_reason1'] = isset($uniset['show_reason1']) ? true : false;
			$data['text_reason1'] = $uniset[$lang_id]['text_reason1'];
			$data['show_reason2'] = isset($uniset['show_reason2']) ? true : false;
			$data['text_reason2'] = $uniset[$lang_id]['text_reason2'];
			$data['show_reason3'] = isset($uniset['show_reason3']) ? true : false;
			$data['text_reason3'] = $uniset[$lang_id]['text_reason3'];
		
			$data['text_agree'] = '';
		
			if ($this->config->get('config_account_id') && isset($uniset['callback_confirm'])) {
				$this->load->model('catalog/information');
			
				$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
				$data['text_agree'] = $information_info ? sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_account_id'), true), $information_info['title'], $information_info['title']) : '';
			}
		
			$this->response->setOutput($this->load->view('extension/module/uni_request_form', $data));
		
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
	
	public function requests() {	
		$uniset = $this->config->get('config_unishop2');
		$settings = $this->config->get('uni_request') ? $this->config->get('uni_request') : [];
		
		if($settings && isset($settings['question_list'])) { 
			$this->load->model('extension/module/uni_request');
			
			$this->load->language('extension/module/uni_othertext');
			$this->load->language('extension/module/uni_request');
			$this->load->language('product/product');
			$this->load->language('product/review');
			$this->load->language('account/register');
		
			$lang_id = $this->config->get('config_language_id');
		
			$data['type'] = $settings['heading_question'][$lang_id];
			$data['show_phone'] = isset($settings['question_phone']) ? true : false;
			$data['show_email'] = isset($settings['question_email']) ? true : false;
			$data['show_email_required'] = isset($settings['question_email_required']) ? true : false;
			$data['show_captcha'] = isset($settings['question_captcha']) ? true : false;
		
			$data['text_agree'] = '';
		
			if ($this->config->get('config_account_id') && isset($uniset['callback_confirm'])) {
				$this->load->model('catalog/information');
			
				$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
				$data['text_agree'] = $information_info ? sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_account_id'), true), $information_info['title'], $information_info['title']) : '';
			}
		
			$this->customer->isLogged();
		
			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
			} else {
				$page = 1;
			}
		
			$limit = 5;
	
			$data['requests'] = [];
		
			$product_id = isset($this->request->get['p_id']) ? (int)$this->request->get['p_id'] : 0;
		
			$data['request_guest'] = 1;
			$data['product_id'] = $product_id;
	
			$filter_data = array(
				'product_id' 	=> $product_id,
				'start' 		=> ($page - 1) * $limit,
				'limit'         => $limit,
			);
	
			$results = $this->model_extension_module_uni_request->getRequests($filter_data);
		
			$data['requests_total'] = $results_total = $this->model_extension_module_uni_request->getTotalRequests($filter_data);
		
			foreach ($results as $result) {
				$data['requests'][] = array(
					'name' 			=> $result['name'],
					'date_added' 	=> date($this->language->get('date_format_short'), strtotime($result['date_added'])),
					'comment' 		=> nl2br($result['comment']),
					'admin_comment' => nl2br($result['admin_comment']),
				);
			}
		
			$pagination = new Pagination();
			$pagination->total = $results_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('extension/module/uni_request/requests', 'p_id=' . $product_id . '&page={page}');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($results_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($results_total - 5)) ? $results_total : ((($page - 1) * 5) + 5), $results_total, ceil($results_total / 5));
			
			if ($this->config->get('captcha_'.$this->config->get('config_captcha').'_status')) {
				$data['captcha'] = $this->load->controller('extension/captcha/'.$this->config->get('config_captcha'), $this->error);
			} else {
				$data['captcha'] = '';
			}
		
			$this->response->setOutput($this->load->view('extension/module/uni_request_list', $data));
		} else {
			$this->load->language('extension/module/uni_request');
			
			$this->document->setTitle($this->language->get('text_error'));
			
	     	$data['breadcrumbs'][] = array(
	        	'href'      => $this->url->link('information/news'),
	        	'text'      => $this->language->get('text_error'),
	        	'separator' => $this->language->get('text_separator')
	     	);
		
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
	
	public function mail() {
		$this->load->language('extension/module/uni_othertext');
		$this->load->language('extension/module/uni_request');
		$this->load->language('account/register');
		
		$uniset = $this->config->get('config_unishop2');
		$lang_id = $this->config->get('config_language_id');
		$settings = $this->config->get('uni_request') ? $this->config->get('uni_request') : [];
		
		$data['show_phone'] = isset($settings['question_phone']) ? true : false;
		$data['show_email'] = isset($settings['question_email']) ? true : false;
		$data['show_captcha'] = isset($settings['question_captcha']) ? true : false;
		
		$type = isset($this->request->post['type']) ? htmlspecialchars(strip_tags($this->request->post['type'])) : '';
		$type = isset($this->request->post['reason']) ? htmlspecialchars(strip_tags($this->request->post['reason'])) : $type;
		
		$product_id = '';
		if (isset($this->request->post['product_id'])) {
			$product_id = (int)$this->request->post['product_id'];
			$this->load->model('catalog/product');
			$product_info = $this->model_catalog_product->getProduct($product_id);
		}
		
		$customer_name = isset($this->request->post['name']) ? htmlspecialchars(strip_tags($this->request->post['name'])) : '';
		$customer_phone = isset($this->request->post['phone']) ? htmlspecialchars(strip_tags($this->request->post['phone'])) : '';
		$customer_mail = isset($this->request->post['mail']) ? htmlspecialchars(strip_tags($this->request->post['mail'])) : '';
		$customer_comment = isset($this->request->post['comment']) ? htmlspecialchars(strip_tags($this->request->post['comment'])) : '';
		$product_id = isset($this->request->post['product_id']) ? (int)$this->request->post['product_id'] : '';
		$location = isset($this->request->post['location']) ? html_entity_decode(strip_tags(trim($this->request->post['location']))) : '';
		$question_form = isset($this->request->post['question_form']) ? (int)$this->request->post['question_form'] : '';
		
		$json = [];
		
		if (utf8_strlen($customer_name) < 3 || utf8_strlen($customer_name) > 45) {
			$json['error']['name'] = $this->language->get('text_error_name');
		}
		
		if (isset($this->request->post['phone']) && (utf8_strlen($customer_phone) < 3 || utf8_strlen($customer_phone) > 25)) {
			$json['error']['phone'] = $this->language->get('text_error_phone');
		}
		
		$notify_email_required = isset($settings['notify_email_required']) ? true : false;
		$heading_notify = isset($settings['heading_notify'][$lang_id]) ? $settings['heading_notify'][$lang_id] : '';
		$question_email_required = isset($settings['question_email_required']) ? true : false;
		$heading_question = isset($settings['heading_question'][$lang_id]) ? $settings['heading_question'][$lang_id] : '';
		
		$mail_reqired = true;
		
		if ($heading_notify == $type && !$notify_email_required) {
			$mail_reqired = false;
		} else if ($heading_question == $type && !$question_email_required) {
			$mail_reqired = false;
		}
		
		if($mail_reqired) {
			if (isset($this->request->post['mail']) && ((utf8_strlen($customer_mail) > 50) || !filter_var($customer_mail, FILTER_VALIDATE_EMAIL))) {
				$json['error']['mail'] = $this->language->get('text_error_mail');
			}
		}

		if (isset($this->request->post['comment']) && ((utf8_strlen($customer_comment) < 5 || utf8_strlen($customer_comment) > 300))) {
			$json['error']['comment'] = $this->language->get('text_error_comment');
		}
		
		if(isset($uniset['callback_confirm'])) {
			if ($this->config->get('config_account_id')) {
				$this->load->model('catalog/information');
				
				$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
					
				if ($information_info && !isset($this->request->post['confirm'])) {
						$json['error']['confirm'] = sprintf($this->language->get('error_agree'), $information_info['title']);
				}
			}
		}
		
		if ($data['show_captcha'] && $question_form && $this->config->get('captcha_'.$this->config->get('config_captcha').'_status')) {
			$captcha = $this->load->controller('extension/captcha/'.$this->config->get('config_captcha').'/validate');

			if ($captcha) {
				$json['error']['captcha'] = $captcha;
			}
		}
		
		if(!isset($json['error'])) {
			$text = $product_id ? $this->language->get('text_product').$product_info['name'].'<br />' : '';
			$text .= $this->language->get('text_name').$customer_name.'<br />';
			$text .= $this->language->get('text_phone').$customer_phone.'<br />';
			$text .= $this->language->get('text_mail').$customer_mail.'<br />';
			$text .= $this->language->get('text_comment').$customer_comment.'<br />';
			$text .= $this->language->get('text_location').$location.'<br />';
		
			$subject = $type && $product_id ? sprintf($this->language->get('text_reason'), $type, $product_info['name']) : sprintf($this->language->get('text_reason2'), $type);
			
			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($this->config->get('config_email'));
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($this->config->get('config_name'));
			$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$mail->setHtml(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
			
			$mail->send();
			
			$emails = explode(',', $this->config->get('config_mail_alert_email'));
			
			foreach ($emails as $email) {
				if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$mail->setTo($email);
					$mail->send();
				}
			}
			
			$request_data = array(
				'type' 			=> $type,
				'name'			=> $customer_name,
				'phone'			=> $customer_phone,
				'mail'			=> $customer_mail,
				'comment'		=> $customer_comment,
				'product_id'	=> $product_id,
				'status'		=> '1',
			); 
			
			if ($this->config->get('uni_request')) {
				$this->load->model('extension/module/uni_request');
				$this->model_extension_module_uni_request->addRequest($request_data);
			}
				
			$json['success'] = (isset($settings['heading_question'][$lang_id]) && $settings['heading_question'][$lang_id] == $type) ? $this->language->get('text_success2') : $this->language->get('text_success');
		}
		
		$this->response->setOutput(json_encode($json));
	}
}
?>