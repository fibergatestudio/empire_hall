<?php
class ModelCatalogStorestock extends Model {
	
	public function getTotalStorestocks() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "store_stock");

		return $query->row['total'];
	}

	public function getStorestocks($data = array()) {
		$sql = "SELECT ss.sstock_id, ss.sscity_id, ss.sort_order, ssd.store_name  FROM " . DB_PREFIX . "store_stock ss "
				. "LEFT JOIN " . DB_PREFIX . "store_stock_description ssd ON (ssd.sstock_id = ss.sstock_id) "
				. "WHERE ssd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND ssd.store_name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sort_data = array(
			'ssd.store_name',
			'ss.sscity_id',
			'ss.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY ssd.store_name";
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
	
	public function addStorestock($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "store_stock SET sscity_id = '" . (int)$data['sscity_id'] . "', phone1 = '" . $this->db->escape($data['phone1']) . "', phone2 = '" . $this->db->escape($data['phone2']) . "', point_x = '" . $this->db->escape($data['point_x']) . "', point_y = '" . $this->db->escape($data['point_y']) . "', link1 = '" . $this->db->escape($data['link1']) . "', link2 = '" . $this->db->escape($data['link2']) . "', shipping = '" . (int)$data['shipping'] . "', sort_order = '" . (int)$data['sort_order'] . "'");

		$sstock_id = $this->db->getLastId();

		foreach ($data['storestock_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "store_stock_description SET sstock_id = '" . (int)$sstock_id . "', language_id = '" . (int)$language_id . "', store_name = '" . $this->db->escape($value['store_name']) . "', store_addr = '" . $this->db->escape($value['store_addr']) . "'");
		}

		return $sstock_id;
	}
	
	public function getStorestockCity($sscity_id) {
		$query = $this->db->query("SELECT sscd.name as city FROM " . DB_PREFIX . "store_stock_city_description sscd WHERE sscd.sscity_id = '" . (int)$sscity_id . "' AND sscd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row['city'];
	}

	public function deleteStorestock($sstock_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "store_stock_description WHERE sstock_id = '" . (int)$sstock_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "store_stock WHERE sstock_id = '" . (int)$sstock_id . "'");
	}

	public function getStorestock($sstock_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store_stock ss WHERE ss.sstock_id = '" . (int)$sstock_id . "'");

		return $query->row;
	}

	public function getStorestockDescriptions($sstock_id) {
		$storestock_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store_stock_description WHERE sstock_id = '" . (int)$sstock_id. "'");

		foreach ($query->rows as $result) {
			$storestock_data[$result['language_id']] = array('store_name' => $result['store_name'], 'store_addr' => $result['store_addr']);
		}

		return $storestock_data;
	}

	public function editStorestock($sstock_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "store_stock SET "
				. "sscity_id = '" . (int)$data['sscity_id'] . "', "
				. "phone1 = '" . $this->db->escape($data['phone1']) . "', "
				. "phone2 = '" . $this->db->escape($data['phone2']) . "', "
				. "link1 = '" . $this->db->escape($data['link1']) . "', "
				. "link2 = '" . $this->db->escape($data['link2']) . "', "
				. "point_x = '" . $this->db->escape($data['point_x']) . "', "
				. "point_y = '" . $this->db->escape($data['point_y']) . "', "
				. "shipping = '" . (int)$data['shipping'] . "', "
				. "sort_order = '" . (int)$data['sort_order'] . "' "
				. "WHERE sstock_id = '" . (int)$sstock_id. "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "store_stock_description WHERE sstock_id = '" . (int)$sstock_id . "'");

		foreach ($data['storestock_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "store_stock_description SET "
					. "sstock_id = '" . (int)$sstock_id . "', "
					. "language_id = '" . (int)$language_id . "', "
					. "store_name = '" . $this->db->escape($value['store_name']) . "', "
					. "store_addr = '" . $this->db->escape($value['store_addr']) . "'");
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	




	public function getTotalAttributesByAttributeGroupId($attribute_group_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "attribute WHERE attribute_group_id = '" . (int)$attribute_group_id . "'");

		return $query->row['total'];
	}
}
