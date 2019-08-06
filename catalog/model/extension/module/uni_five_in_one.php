<?php
class ModelExtensionModuleUniFiveInOne extends Model {	
	public function getLatest($limit) {
		$products = [];
		
		$query = $this->db->query("SELECT p.product_id FROM `".DB_PREFIX."product` p LEFT JOIN `".DB_PREFIX."product_to_store` p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '".(int)$this->config->get('config_store_id')."' ORDER BY p.date_added DESC LIMIT ".(int)$limit);

		foreach ($query->rows as $result) {
			$products[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
		}

		return $products;
	}
	
	public function getSpecial($limit) {
		$products = [];
		
		$query = $this->db->query("SELECT DISTINCT ps.product_id FROM `".DB_PREFIX."product_special` ps LEFT JOIN `".DB_PREFIX."product` p ON (ps.product_id = p.product_id) LEFT JOIN ".DB_PREFIX."product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '".(int)$this->config->get('config_store_id')."' AND ps.customer_group_id = '".(int)$this->config->get('config_customer_group_id')."' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) GROUP BY ps.product_id LIMIT ".(int)$limit);

		foreach ($query->rows as $result) {
			$products[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
		}

		return $products;
	}
	
	public function getPopular($limit) {
		$products = [];
		
		$query = $this->db->query("SELECT p.product_id FROM `".DB_PREFIX."product` p LEFT JOIN `".DB_PREFIX."product_to_store` p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '".(int)$this->config->get('config_store_id')."' ORDER BY p.viewed DESC, p.date_added DESC LIMIT ".(int)$limit);
	
		foreach ($query->rows as $result) {
			$products[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
		}

		return $products;
	}
	
	public function getBestseller($limit) {
		$products = [];
		
		$query = $this->db->query("SELECT op.product_id, SUM(op.quantity) AS total FROM `".DB_PREFIX."order_product` op LEFT JOIN `".DB_PREFIX."order` o ON (op.order_id = o.order_id) LEFT JOIN `".DB_PREFIX."product` p ON (op.product_id = p.product_id) LEFT JOIN `".DB_PREFIX."product_to_store` p2s ON (p.product_id = p2s.product_id) WHERE o.order_status_id > '0' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '".(int)$this->config->get('config_store_id')."' GROUP BY op.product_id ORDER BY total DESC LIMIT ".(int)$limit);

		foreach ($query->rows as $result) {
			$products[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
		}

		return $products;
	}
	
	public function getFeatured($results) {
		$products = [];
		
		foreach ($results as $product_id) {
			$products[] = $this->model_catalog_product->getProduct($product_id);
		}

		return $products;
	}
}
?>