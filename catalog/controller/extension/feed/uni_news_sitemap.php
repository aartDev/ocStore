<?php
class ControllerExtensionFeedUniNewsSitemap extends Controller {
	public function index() {
		
		$settings = $this->config->get('uni_news');
		
		if (isset($settings['sitemap'])) {
			$output  = '<?xml version="1.0" encoding="UTF-8"?>';
			$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" >';

			$this->load->model('extension/module/uni_news');
			$this->load->model('localisation/language');

			$output .= $this->getCategories(0);

			$output .= '</urlset>';

			$this->response->addHeader('Content-Type: application/xml');
			$this->response->setOutput($output);
		}
	}

	protected function getCategories($parent_id, $current_path = '') {
		$output = '';
		
		//$languages = $this->model_localisation_language->getLanguages();

		$results = $this->model_extension_module_uni_news->getCategories($parent_id);

		foreach ($results as $result) {
			if (!$current_path) {
				$news_path = $result['category_id'];
			} else {
				$news_path = $current_path.'_'.$result['category_id'];
			}

			$output .= '<url>';
			$output .= '<loc>' . $this->url->link('information/uni_new', 'news_path='.$news_path).'</loc>';
			$output .= '<changefreq>weekly</changefreq>';
			$output .= '<priority>0.7</priority>';
			$output .= '</url>';

			$news = $this->model_extension_module_uni_news->getNews(array('filter_category_id' => $result['category_id']));
			
			foreach ($news as $news) {
				$output .= '<url>';
				$output .= '<loc>'.$this->url->link('information/uni_news_story', 'news_path='.$news_path.'&news_id='.$news['news_id']).'</loc>';
				
				//foreach($languages as $lang){
					//$this->config->set('config_language_id', $lang['language_id']);	
					//$output .= '<xhtml:link rel="alternate" hreflang="'.$lang['code'].'" href="'.$this->url->link('information/uni_news_story', 'news_path='.$news_path . '&news_id='.$news['news_id']).'"/>';
				//}
				
				$output .= '<lastmod>'.$news['date_added'].'</lastmod>';
				$output .= '<changefreq>weekly</changefreq>';
				$output .= '<priority>1.0</priority>';
				$output .= '</url>';
			}

			$output .= $this->getCategories($result['category_id'], $news_path);
		}

		return $output;
	}
}
