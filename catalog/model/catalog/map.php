<?php
class ModelCatalogMap extends Model {
	public function getMapPoints() {
		$query = $this->db->query("SELECT *, (SELECT sscd.name FROM oc_store_stock_city_description sscd WHERE sscd.sscity_id = ss.sscity_id) as city FROM " . DB_PREFIX . "store_stock ss LEFT JOIN " . DB_PREFIX . "store_stock_description ssd ON (ssd.sstock_id = ss.sstock_id) WHERE ssd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ss.sort_order DESC");

		return $query->rows;
	}
}