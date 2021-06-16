<?php
class ModelCatalogManufacturer extends Model {
	
                private static $getCacheInstance = null;
                public function getManufacturer($manufacturer_id) {
                /**
                 * opencart cache code start here
                 */
                  $cache_manufacturer_status = false;
                  if($this->config->get('module_oc_cache_status') && (isset($this->config->get('module_oc_cache_manufacturer')['status']) && $this->config->get('module_oc_cache_manufacturer')['status'])){
                      $cache_manufacturer_status = true;
                      $lang_store_id = '.'.(int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id');
                      if (self::$getCacheInstance == null) {
                          self::$getCacheInstance = $this->webkulcache->get_InstanceCache('files');
                      }
                      $CachedString           = self::$getCacheInstance->getItem('manufacturer');
                      $manufacturer_container = $CachedString->get('manufacturer'.$lang_store_id);
                      if(isset($manufacturer_container['manufacturer'.$lang_store_id][$manufacturer_id])){
                          return $manufacturer_container['manufacturer'.$lang_store_id][$manufacturer_id];
                      }
                    }
                /**
                * opencart cache code end here
                */
              
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_description md ON (m.manufacturer_id = md.manufacturer_id) LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m.manufacturer_id = '" . (int)$manufacturer_id . "' AND m2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND md.language_id = '" . (int)$this->config->get('config_language_id') . "'");


    if ($this->config->get('mlseo_enabled') && !empty($query) && $query->num_rows) {
      $this->load->model('tool/seo_package');
      $seoDescription = $this->model_tool_seo_package->getSeoDescription('manufacturer', $query->row['manufacturer_id']);
      
      $query->row['meta_title'] = isset($seoDescription['meta_title']) ? $seoDescription['meta_title'] : '';
      $query->row['meta_description'] = isset($seoDescription['meta_description']) ? $seoDescription['meta_description'] : '';
      $query->row['meta_keyword'] = isset($seoDescription['meta_keyword']) ? $seoDescription['meta_keyword'] : '';
      $query->row['name'] = !empty($seoDescription['name']) ? $seoDescription['name'] : $query->row['name'];
      if(!empty($seoDescription['description']))
      $query->row['description'] = isset($seoDescription['description']) ? html_entity_decode($seoDescription['description'], ENT_QUOTES, 'UTF-8') : '';
      $query->row['seo_h1'] = isset($seoDescription['seo_h1']) ? $seoDescription['seo_h1'] : '';
      $query->row['seo_h2'] = isset($seoDescription['seo_h2']) ? $seoDescription['seo_h2'] : '';
      $query->row['seo_h3'] = isset($seoDescription['seo_h3']) ? $seoDescription['seo_h3'] : '';
    }
      
		return $query->row;
	}

	public function getManufacturers($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_description md ON (m.manufacturer_id = md.manufacturer_id) LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND md.language_id = '" . (int)$this->config->get('config_language_id') . "'";

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

        if ($this->config->get('mlseo_enabled') && $query->num_rows) {
          $this->load->model('tool/seo_package');
          foreach ($query->rows as &$row) {
            $seoDescription = $this->model_tool_seo_package->getSeoDescription('manufacturer', $row['manufacturer_id']);
            if (!empty($seoDescription['name'])) {
              $row['name'] = $seoDescription['name'];
            }
          }
        }
      

			return $query->rows;
		} else {
			$manufacturer_data = $this->cache->get('manufacturer.' . (int)$this->config->get('config_store_id'));

			if (!$manufacturer_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_description md ON (m.manufacturer_id = md.manufacturer_id) LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND md.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");

				$manufacturer_data = $query->rows;

				$this->cache->set('manufacturer.' . (int)$this->config->get('config_store_id'), $manufacturer_data);
			}

			return $manufacturer_data;
		}
	}

	public function getCollectionsCategory($manufacturer_id) {
	    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "collection c 
	    LEFT JOIN " . DB_PREFIX . "collection_description cd 
	    ON (c.collection_id = cd.collection_id) 
	    WHERE c.manufacturer_id = '" . (int)$manufacturer_id ."' AND 
	    cd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

	    return $query->rows;
    }

    public function getCollectionsItems($category_id, $manufacturer_id) {
        $query = $this->db->query("SELECT cid.name, ci.image, ci.collection_item_id FROM " . DB_PREFIX . "collection_item_to_category citc 
		LEFT JOIN " . DB_PREFIX . "collection_item ci 
		ON (citc.collection_item_id = ci.collection_item_id) 
		LEFT JOIN " . DB_PREFIX . "collection_item_description cid 
		ON (cid.collection_item_id = ci.collection_item_id) 
		LEFT JOIN " . DB_PREFIX . "collection c 
		ON (citc.collection_id = c.collection_id) 
		WHERE citc.collection_id = '" . (int)$category_id . "' 
		AND cid.language_id = '" . (int)$this->config->get('config_language_id') . "' 
		AND ci.status = '1' 
		AND c.manufacturer_id = '" . (int)$manufacturer_id . "'");

        return $query->rows;
    }
}
