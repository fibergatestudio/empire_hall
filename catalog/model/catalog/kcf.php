<?php
class ModelCatalogKcf extends Model {
	
	public function getFeelds($kcf_type, $information_id) {
		$query = $this->db->query("SELECT * FROM oc_kcf_feeld_value kfv LEFT JOIN oc_kcf_feeld kf ON (kf.kcffeeld_id = kfv.kcfv_feeld_id) WHERE kfv.kcfv_type = '" . $kcf_type . "' AND kfv.kcfv_page_id = '" . $information_id . "' AND kfv.kcfv_language_id = '" . (int)$this->config->get('config_language_id') . "'");

		if ($query->num_rows) {
			return $query->rows;
		} else {
			return array();
		}
	}
}