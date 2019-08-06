<?php
class ModelExtensionModuleUniViewed extends Model {	
	public function getViewed($products) {
		$product_data = array();
		
		foreach ($products as $product) {
			$product_data[] = $this->model_catalog_product->getProduct((int)$product);
		}

		return $product_data;
	}
}
?>