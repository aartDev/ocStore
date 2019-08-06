<?php
class ModelExtensionModuleUniCustomMenu extends Model {
	public function getCustomMenu(){
		$this->load->model('tool/image');
	 
		$data = array();
		$query = $this->db->query("SELECT * FROM ".DB_PREFIX."uni_custom_menu h LEFT JOIN ".DB_PREFIX."uni_custom_menu_description hd ON (h.menu_id = hd.menu_id) where hd.language_id = '".(int)$this->config->get('config_language_id')."' AND h.parent_id=0 and h.status=1 ORDER BY h.sort_order");

			
		foreach($query->rows as $category){
			$query1 = $this->db->query("SELECT * FROM ".DB_PREFIX."uni_custom_menu h LEFT JOIN ".DB_PREFIX."uni_custom_menu_description hd ON (h.menu_id = hd.menu_id) where hd.language_id = '" . (int)$this->config->get('config_language_id') . "' and h.parent_id='".(int)$category['menu_id']."' and  h.status=1 order by h.sort_order");
				
			$child = array();
			
			foreach($query1->rows as $category1){
				
				$child2 = array();
				
				$query2 = $this->db->query("SELECT * FROM ".DB_PREFIX."uni_custom_menu h LEFT JOIN ".DB_PREFIX."uni_custom_menu_description hd ON (h.menu_id = hd.menu_id) where hd.language_id = '".(int)$this->config->get('config_language_id')."' and h.status=1 and h.parent_id='".(int)$category1['menu_id']."' order by h.sort_order");
				
				foreach($query2->rows as $category2){
					$child2[] = array(
						'id' 		=> $category2['menu_id'], 
						'name' 		=> $category2['name'], 
						'column'	=> $category2['column'], 
						'link'		=> $category2['link'], 
						'sub_menu' 	=> ''
					);
				}
				
				$child [] = array(
					'id' 		=> $category1['menu_id'], 
					'name' 		=> $category1['name'], 
					'image' 	=> $category1['image'] ? $this->model_tool_image->resize($category1['image'], 20, 20) : '',
					'link' 		=> $category1['link'], 
					'sub_menu' 	=> $child2
				);
			} 
				
				
			$data[] = array(
				'id' 		=> $category['menu_id'],
				'name' 		=> $category['name'],
				'image' 	=> $category['image'] ? $this->model_tool_image->resize($category['image'], 20, 20) : '',
				'link' 		=> $category['link'],
				'column' 	=> $category['column'],
				'sub_menu'	=> $child 
			);
		}
			
		return $data;
	}	
}
?>