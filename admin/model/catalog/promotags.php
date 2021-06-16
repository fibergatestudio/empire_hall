<?php
class ModelCatalogPromoTags extends Model {
	public function addPromoTags($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "promo_tags SET class = '" . $this->db->escape($data['class']) . "', sort_order = '" . (int)$data['sort_order'] . "'");

		$promo_tags_id = $this->db->getLastId();

		foreach($data['promo_text'] as $language_id => $promo_text){
                    $this->db->query("INSERT INTO " . DB_PREFIX . "promo_tags_description SET promo_text = '" . $this->db->escape($promo_text) . "', language_id = '" . $language_id . "', promo_tags_id = '" . (int)$promo_tags_id . "'");
		}

	}

	public function editPromoTags($promo_tags_id, $data) {
            
		$this->db->query("UPDATE " . DB_PREFIX . "promo_tags SET class = '" . $this->db->escape($data['class']) . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE promo_tags_id = '" . (int)$promo_tags_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "promo_tags_description WHERE promo_tags_id = '" . (int)$promo_tags_id . "'");
                
		foreach($data['promo_text'] as $language_id => $promo_text){
                    $this->db->query("INSERT INTO " . DB_PREFIX . "promo_tags_description SET promo_text = '" . $this->db->escape($promo_text) . "', language_id = '" . $language_id . "', promo_tags_id = '" . (int)$promo_tags_id . "'");
		}

	}

	public function deletePromoTags($promo_tags_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "promo_tags WHERE promo_tags_id = '" . (int)$promo_tags_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "promo_tags_description WHERE promo_tags_id = '" . (int)$promo_tags_id . "'");
		$this->cache->delete('promotags');
	}
	
	public function getPromoTag($promo_tags_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "promo_tags WHERE promo_tags_id = '" . (int)$promo_tags_id . "'");
		return $query->row;
	}      
        
        public function getPromoTagDescription($promo_tags_id) {
		$promo_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "promo_tags_description WHERE promo_tags_id = '" . (int)$promo_tags_id . "'");

		foreach ($query->rows as $result) {
			$promo_description_data[$result['language_id']] = $result['promo_text'];
		}

		return $promo_description_data;
	}
		
	public function getPromoTags($data = array()) {
            
			$sql = "SELECT * FROM " . DB_PREFIX . "promo_tags pt LEFT JOIN " . DB_PREFIX . "promo_tags_description ptd ON(ptd.promo_tags_id = pt.promo_tags_id) WHERE ptd.language_id = '". (int)$this->config->get('config_language_id') ."'";
			
			$sort_data = array(
				'ptd.promo_text',
				'pt.sort_order'
			);	
			
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];	
			} else {
				$sql .= " ORDER BY promo_text";	
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
	public function getTotalProductsByPromoTagsTopRightId($promotags_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE promo_top_right = '" . (int)$promotags_id . "'");
		return $query->row['total'];
	}
	
	/*
	public function getTotalProductsByPromoTagsTopLeftId($promotags_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE promo_top_left = '" . (int)$promotags_id . "'");

		return $query->row['total'];
	}
	
	public function getTotalProductsByPromoTagsBottomRightId($promotags_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE promo_bottom_left = '" . (int)$promotags_id . "'");

		return $query->row['total'];
	}
	
	public function getTotalProductsByPromoTagsBottomLeftId($promotags_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE promo_bottom_right = '" . (int)$promotags_id . "'");

		return $query->row['total'];
	}
	*/
	
	public function getTotalPromoTags($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "promo_tags";
		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	
}
?>