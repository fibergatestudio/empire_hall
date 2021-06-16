<?php
class ModelCatalogManufacturer extends Model {
	public function addManufacturer($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer SET sort_order = '" . (int)$data['sort_order'] . "', hide_price = '" . (int)$data['hide_price'] . "'");

		$manufacturer_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET image = '" . $this->db->escape($data['image']) . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		}

		if (isset($data['image2'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET image2 = '" . $this->db->escape($data['image2']) . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		}

		if (isset($data['image3'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET image3 = '" . $this->db->escape($data['image3']) . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		}

        foreach ($data['manufacturer_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_description SET manufacturer_id = '" . (int)$manufacturer_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', subtext = '" . $this->db->escape($value['subtext']) . "'");
        }

		if (isset($data['manufacturer_store'])) {
			foreach ($data['manufacturer_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_to_store SET manufacturer_id = '" . (int)$manufacturer_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		// SEO URL
        if (isset($data['manufacturer_seo_url'])) {
            foreach ($data['manufacturer_seo_url'] as $store_id => $language) {
                foreach ($language as $language_id => $keyword) {
                    if (!empty($keyword)) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'manufacturer_id=" . (int)$manufacturer_id . "', keyword = '" . $this->db->escape($keyword) . "'");
                    } else {
                        if($language_id == 1) {
                            $seo_url = $data['manufacturer_description'][2]['name'] . '_en';
                            if(isset($data['manufacturer_description'][1]['name']) && $data['manufacturer_description'][1]['name'] != $data['manufacturer_description'][2]['name']) {
                                $seo_url = $data['manufacturer_description'][1]['name'];
                            }
                        }
                        if($language_id == 2) {
                            $seo_url = $data['manufacturer_description'][2]['name'];
                        }
                        if($language_id == 3) {
                            $seo_url = $data['manufacturer_description'][2]['name'] . '_ua';
                            if(isset($data['manufacturer_description'][3]['name']) && $data['manufacturer_description'][3]['name'] != $data['manufacturer_description'][2]['name']) {
                                $seo_url = $data['manufacturer_description'][3]['name'];
                            }
                        }

                        $this->translit->setSeoURL('manufacturer_id', $language_id, (int)$manufacturer_id, $seo_url);
                    }
                }
            }
        }

		$this->cache->delete('manufacturer');

		return $manufacturer_id;
	}

	public function editManufacturer($manufacturer_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET sort_order = '" . (int)$data['sort_order'] . "', hide_price = '" . (int)$data['hide_price'] . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET image = '" . $this->db->escape($data['image']) . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		}

		if (isset($data['image2'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET image2 = '" . $this->db->escape($data['image2']) . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		}

		if (isset($data['image3'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET image3 = '" . $this->db->escape($data['image3']) . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		}

        $this->db->query("DELETE FROM " . DB_PREFIX . "manufacturer_description WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

        foreach ($data['manufacturer_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_description SET manufacturer_id = '" . (int)$manufacturer_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', subtext = '" . $this->db->escape($value['subtext']) . "'");
        }

		$this->db->query("DELETE FROM " . DB_PREFIX . "manufacturer_to_store WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		if (isset($data['manufacturer_store'])) {
			foreach ($data['manufacturer_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_to_store SET manufacturer_id = '" . (int)$manufacturer_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'manufacturer_id=" . (int)$manufacturer_id . "'");

        if (isset($data['manufacturer_seo_url'])) {
            foreach ($data['manufacturer_seo_url'] as $store_id => $language) {
                foreach ($language as $language_id => $keyword) {
                    if (!empty($keyword)) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'manufacturer_id=" . (int)$manufacturer_id . "', keyword = '" . $this->db->escape($keyword) . "'");
                    } else {
                        if($language_id == 1) {
                            $seo_url = $data['manufacturer_description'][2]['name'] . '_en';
                            if(isset($data['manufacturer_description'][1]['name']) && $data['manufacturer_description'][1]['name'] != $data['manufacturer_description'][2]['name']) {
                                $seo_url = $data['manufacturer_description'][1]['name'];
                            }
                        }
                        if($language_id == 2) {
                            $seo_url = $data['manufacturer_description'][2]['name'];
                        }
                        if($language_id == 3) {
                            $seo_url = $data['manufacturer_description'][2]['name'] . '_ua';
                            if(isset($data['manufacturer_description'][3]['name']) && $data['manufacturer_description'][3]['name'] != $data['manufacturer_description'][2]['name']) {
                                $seo_url = $data['manufacturer_description'][3]['name'];
                            }
                        }

                        $this->translit->setSeoURL('manufacturer_id', $language_id, (int)$manufacturer_id, $seo_url);
                    }
                }
            }
        }






		$this->cache->delete('manufacturer');
	}

	public function deleteManufacturer($manufacturer_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "manufacturer` WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "manufacturer_description` WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "manufacturer_to_store` WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'manufacturer_id=" . (int)$manufacturer_id . "'");

		$this->cache->delete('manufacturer');
	}

	public function getManufacturer($manufacturer_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_description md ON (m.manufacturer_id = md.manufacturer_id) WHERE m.manufacturer_id = '" . (int)$manufacturer_id . "' AND md.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getManufacturers($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_description md ON (m.manufacturer_id = md.manufacturer_id) WHERE md.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sort_data = array(
			'name',
			'sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
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

    public function getManufacturerDescriptions($manufacturer_id) {
        $manufacturer_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer_description WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

        foreach ($query->rows as $result) {
            $manufacturer_description_data[$result['language_id']] = array(
                'name'             => $result['name'],
                'subtext'          => $result['subtext'],
                'description'      => $result['description']
            );
        }

        return $manufacturer_description_data;
    }

	public function getManufacturerStores($manufacturer_id) {
		$manufacturer_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer_to_store WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		foreach ($query->rows as $result) {
			$manufacturer_store_data[] = $result['store_id'];
		}

		return $manufacturer_store_data;
	}

	public function getManufacturerSeoUrls($manufacturer_id) {
		$manufacturer_seo_url_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'manufacturer_id=" . (int)$manufacturer_id . "'");

		foreach ($query->rows as $result) {
			$manufacturer_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $manufacturer_seo_url_data;
	}

	public function getTotalManufacturers() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "manufacturer");

		return $query->row['total'];
	}
}
