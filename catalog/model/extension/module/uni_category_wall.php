<?php
class ModelExtensionModuleUniCategoryWall extends Model {	
	public function getCategories($categories, $name) {	
		$store_id = (int)$this->config->get('config_store_id');
		$lang_id = (int)$this->config->get('config_language_id');
		
		$md5_name = substr(md5($name), 0, 8);
		$cache_name = 'category.unishop.catwall.'.$md5_name.'.'.$lang_id.'.'.$store_id;
		
		$result = $this->cache->get($cache_name);
		
		if(!$result) {	
			
			$result = [];
			
			foreach($categories as $category_id => $category_arr) {
			
				$query = $this->db->query("SELECT c.category_id, cd.name, c.image FROM `".DB_PREFIX."category` c LEFT JOIN `".DB_PREFIX."category_description` cd ON (c.category_id = cd.category_id) LEFT JOIN ".DB_PREFIX."category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.category_id = '".(int)$category_id."' AND cd.language_id = '".$lang_id."' AND c2s.store_id = '".$store_id."' AND c.status = '1'");
	
				$category_data = $query->row;
	
				if($category_data) {
					$children_data = [];
				
					if($category_arr && is_array($category_arr)) {
						$categories = implode(',', $category_arr);
				
						$query = $this->db->query("SELECT c.category_id, cd.name FROM `".DB_PREFIX."category` c LEFT JOIN `".DB_PREFIX."category_description` cd ON (c.category_id = cd.category_id) LEFT JOIN ".DB_PREFIX ."category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.category_id in (".$this->db->escape($categories).") AND cd.language_id = '".$lang_id."' AND c2s.store_id = '".$store_id."' AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");
							
						$children_data = $query->rows;
					}
					
					$result[$category_data['category_id']] = $category_data;
					$result[$category_data['category_id']]['children'] = $children_data;
				}
			}
			
			if($result) {
				$this->cache->set($cache_name, $result);
			}
		}
		
		return $result;
	}
}
?>