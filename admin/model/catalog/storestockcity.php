<?php
class ModelCatalogStorestockcity extends Model {

	public function getTotalStorestockcity() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "store_stock_city");

		return $query->row['total'];
	}	

	public function getStorestockcity($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "store_stock_city ssc "
				. "LEFT JOIN " . DB_PREFIX . "store_stock_city_description sscd ON (ssc.sscity_id = sscd.sscity_id) "
				. "WHERE sscd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$sort_data = array(
			'sscd.name',
			'ssc.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY sscd.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}	
	
	public function addStorestockcity($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "store_stock_city SET sort_order = '" . (int)$data['sort_order'] . "'");

		$sscity_id = $this->db->getLastId();

		foreach ($data['storestockcity_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "store_stock_city_description SET sscity_id = '" . (int)$sscity_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		return $sscity_id;
	}

	public function deleteStorestockcity($sscity_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "store_stock_city WHERE sscity_id = '" . (int)$sscity_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "store_stock_city_description WHERE sscity_id = '" . (int)$sscity_id . "'");
	}

	public function getStorestockcityInfo($sscity_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store_stock_city WHERE sscity_id = '" . (int)$sscity_id . "'");

		return $query->row;
	}
	
	public function getStorestockcityDescriptions($sscity_id) {
		$storestockcity_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store_stock_city_description WHERE sscity_id = '" . (int)$sscity_id . "'");

		foreach ($query->rows as $result) {
			$storestockcity_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $storestockcity_data;
	}

	public function editStorestockcity($sscity_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "store_stock_city SET sort_order = '" . (int)$data['sort_order'] . "' WHERE sscity_id = '" . (int)$sscity_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "store_stock_city_description WHERE sscity_id = '" . (int)$sscity_id . "'");

		foreach ($data['storestockcity_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "store_stock_city_description SET sscity_id = '" . (int)$sscity_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}
	}
}