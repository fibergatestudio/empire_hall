<?php
class ModelExportFacebookRemarketing extends Model {
	public function getCategory() {
		$query = $this->db->query("SELECT cd.name, c.category_id, c.parent_id FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' AND c.sort_order <> '-1'");

		return $query->rows;
	}

	public function getProduct($allowed_categories, $out_of_stock_id, $vendor_required = true) {
		$query = $this->db->query("SELECT p.*, pd.name, pd.description, md.name AS manufacturer, p2c.category_id, ps.price AS special, cd.name as category_name FROM " . DB_PREFIX . "product p JOIN " . DB_PREFIX . "product_to_category AS p2c ON (p.product_id = p2c.product_id) " . ($vendor_required ? '' : 'LEFT ') . "JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)  LEFT JOIN " . DB_PREFIX . "manufacturer_description md ON (md.manufacturer_id = m.manufacturer_id) LEFT JOIN " . DB_PREFIX . "category_description cd ON (p2c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "product_special ps ON (p.product_id = ps.product_id) AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ps.date_start < NOW() AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()) WHERE p2c.category_id IN (" . $this->db->escape($allowed_categories) . ") AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1' AND (p.quantity > '0' OR p.stock_status_id != '" . (int)$out_of_stock_id . "') GROUP BY p.product_id");

		return $query->rows;
	}
	
	public function getProductAttributes($product_id) {
		$query = $this->db->query("SELECT pa.attribute_id, pa.text, ad.name
			FROM " . DB_PREFIX . "product_attribute pa
			LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (pa.attribute_id = ad.attribute_id)
			WHERE pa.product_id = '" . (int)$product_id . "'
				AND pa.language_id = '" . (int)$this->config->get('config_language_id') . "'
				AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "'
				ORDER BY pa.attribute_id");
		return $query->rows;
	}
	
	public function getCategoryGoogle($category_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "facebook_remarketing_category_to_category WHERE category_id = '" . (int)$category_id . "'");

		return $query->row;
	}
	
	
}
?>
