<?php
class ModelCatalogInformation extends Model {
	public function getInformation($information_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE i.information_id = '" . (int)$information_id . "' AND id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1'");

		return $query->row;
	}

	public function getInformations() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1' ORDER BY i.sort_order, LCASE(id.title) ASC");

		return $query->rows;
	}

	public function getInformationLayoutId($information_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_to_layout WHERE information_id = '" . (int)$information_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return (int)$query->row['layout_id'];
		} else {
			return 0;
		}
	}
        
	public function getInformationGallery($information_id) {

		$mass_result = array();
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_image WHERE information_id = '" . (int)$information_id . "' ORDER BY sort_order ASC");

			foreach ($query->rows as $value) {
				$mass_title = array();
				$query_title = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_image_description WHERE information_image_id = '" . (int)$value['information_image_id'] . "'");
				foreach ($query_title->rows as $value_title) {
					$mass_title[$value_title['language_id']] = $value_title['title'];

				}
				$mass_result[] = array(
					'information_image_id' => $value['information_image_id'],
					'information_id' => $value['information_id'],
					'image' => $value['image'],
					'sort_order' => $value['sort_order'],
					'title' => $mass_title
				);
			}
		
		return $mass_result;
	}

    public function getInformationTriggers($information_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_triggers WHERE information_id = '" . (int)$information_id . "' AND language_id = '".(int)$this->config->get('config_language_id')."'");

        if($query->num_rows){
            return $query->rows;
        } else {
            return false;
        }
    }
}