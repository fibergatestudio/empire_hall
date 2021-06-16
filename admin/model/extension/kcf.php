<?php
class ModelExtensionKcf extends Model {
	
	public function addKcf($data) {

		$this->db->query("INSERT INTO " . DB_PREFIX . "kcf SET type = '" . $this->db->escape($data['kcf_type']) . "'");
		$kfc_id = $this->db->getLastId();
		foreach ($data['kfc_description'] as $key => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "kcf_description "
					. "SET "
					. "kcf_id = '" . $kfc_id . "', "
					. "language_id = '" . $key . "', "
					. "name = '" . $this->db->escape($value['name']) . "', "
					. "description = '" . $this->db->escape($value['description']) . "'");
		}
		foreach ($data['kcf_feeld'] as $kcf_feeld) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "kcf_feeld SET kcf_id = '" . $kfc_id . "', type = '" . $kcf_feeld['type'] . "'");
			$kcffeeld_id = $this->db->getLastId();
			foreach($kcf_feeld['kcf_feeld_description'] as $key => $value){
				$this->db->query("INSERT INTO " . DB_PREFIX . "kcf_feeld_description "
						. "SET "
						. "kcffeeld_id = '" . $kcffeeld_id . "', "
						. "language_id = '" . $key . "', "
						. "name = '" . $value['name'] . "', "
						. "title = '" . $value['title'] . "'");
			}
		}
	}
	
	public function editKcf($kcf_id,$data) {

		$this->db->query("UPDATE " . DB_PREFIX . "kcf SET type = '" . $this->db->escape($data['kcf_type']) . "' WHERE kcf_id = '" . (int)$kcf_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "kcf_description WHERE kcf_id = '" . (int)$kcf_id . "'");
		foreach ($data['kfc_description'] as $key => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "kcf_description "
					. "SET "
					. "kcf_id = '" . $kcf_id . "', "
					. "language_id = '" . $key . "', "
					. "name = '" . $this->db->escape($value['name']) . "', "
					. "description = '" . $this->db->escape($value['description']) . "'");
		}
		$query = $this->db->query("SELECT kcffeeld_id FROM " . DB_PREFIX . "kcf_feeld WHERE kcf_id = '" . (int)$kcf_id . "'");
		foreach($query->rows as $feelds){
			$this->db->query("DELETE FROM " . DB_PREFIX . "kcf_feeld_description WHERE kcffeeld_id = '" . (int)$feelds['kcffeeld_id'] . "'");
		}
		$this->db->query("DELETE FROM " . DB_PREFIX . "kcf_feeld WHERE kcf_id = '" . (int)$kcf_id . "'");
		foreach ($data['kcf_feeld'] as $kcf_feeld) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "kcf_feeld "
					. "SET "
					. "kcf_id = '" . $kcf_id . "', "
					. "type = '" . (int)$kcf_feeld['type'] . "'");
			
			$kcffeeld_id = $this->db->getLastId();
			
			foreach($kcf_feeld['kcf_feeld_description'] as $key => $value){
				$this->db->query("INSERT INTO " . DB_PREFIX . "kcf_feeld_description "
					. "SET "
					. "kcffeeld_id = '" . $kcffeeld_id . "', "
					. "language_id = '" . $key . "', "	
					. "name = '" . $this->db->escape($value['name']) . "', "
					. "title = '" . $this->db->escape($value['title']) . "'");
			}
		}
	}
	
	public function delKcf($kcf_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "kcf WHERE kcf_id = '" . (int)$kcf_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "kcf_description WHERE kcf_id = '" . (int)$kcf_id . "'");
		$query = $this->db->query("SELECT kcffeeld_id FROM " . DB_PREFIX . "kcf_feeld WHERE kcf_id = '" . (int)$kcf_id . "'");
		foreach($query->rows as $feelds){
			$this->db->query("DELETE FROM " . DB_PREFIX . "kcf_feeld_description WHERE kcffeeld_id = '" . (int)$feelds['kcffeeld_id'] . "'");
		}
		$this->db->query("DELETE FROM " . DB_PREFIX . "kcf_feeld WHERE kcf_id = '" . (int)$kcf_id . "'");
	}
	
	public function getTotalKcf() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "kcf");

		return $query->row['total'];
	}	
	
	public function getKcf($kcf_id) {
		$kcf_info = array();
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "kcf WHERE kcf_id = '" . $kcf_id . "'");
		foreach($query->rows as $val){
			//Получим описание
			$kcf_description = array();
			$query_desc = $this->db->query("SELECT * FROM " . DB_PREFIX . "kcf_description WHERE kcf_id = '" . $kcf_id . "'");
			foreach($query_desc->rows as $val_desc){
				$kcf_description[$val_desc['language_id']] = array(
					'name' => $val_desc['name'],
					'description' => $val_desc['description']
				);
			}
			//получим поля
			$kcf_feelds = array();
			$query_feelds = $this->db->query("SELECT * FROM " . DB_PREFIX . "kcf_feeld WHERE kcf_id = '" . $kcf_id . "'");
			foreach($query_feelds->rows as $val_feeld){
				$feeld_desc = array();
				$feeld_desc_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "kcf_feeld_description WHERE kcffeeld_id = '" . $val_feeld['kcffeeld_id'] . "'");
				foreach($feeld_desc_query->rows as $val_feeld_desc){
					$feeld_desc[$val_feeld_desc['language_id']] = array(
						'kcffeeld_id' => $val_feeld_desc['kcffeeld_id'],
						'name' => $val_feeld_desc['name'],
						'title' => $val_feeld_desc['title']
					);
				}
				$kcf_feelds[] = array(
					'kcffeeld_id' => $val_feeld['kcffeeld_id'],
					'type' => $val_feeld['type'],
					'status' => $val_feeld['status'],
					'feeld_desc' => $feeld_desc
				);
			}
			$kcf_info = array(
				'type' => $val['type'],
				'status' => $val['status'],
				'description' => $kcf_description,
				'kcf_feelds' => $kcf_feelds
			);
		}
		
		return $kcf_info;
	}

	public function getKcfs($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "kcf k "
				. "LEFT JOIN " . DB_PREFIX . "kcf_description kd ON (k.kcf_id = kd.kcf_id) "
				. "WHERE kd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		$sort_data = array(
			'kd.name',
			'k.status'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY kd.name";
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
	
	public function getKcfTypes() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "kcf_type");
		
		return $query->rows;
	}
	
	public function getKcfFeeldTypes() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "kcf_feeld_type");
		
		return $query->rows;
	}
	
	public function getKcfSet($type) {
		$query = $this->db->query("SELECT k.kcf_id, kd.name, kd.description FROM " . DB_PREFIX . "kcf k LEFT JOIN " . DB_PREFIX . "kcf_description kd ON (kd.kcf_id = k.kcf_id) WHERE k.`type` = '" . $type . "' AND kd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		return $query->rows;
	}
	
	public function setKcf($kcfid, $typepage, $kcfv_page_id) {
/*
		// $kcfid --- это kcf_id - id самого набора полей и оттуда можено взять набор полей, привязанных к этому набору
		// $typepage --- тип страницы - категория, товар или информационка
		// $kcfv_page_id --- это id категории, товара или информационной страницы в зависимости от typepage
*/		
		if($kcfid){
					$this->db->query("DELETE FROM " . DB_PREFIX . "kcf_feeld_value WHERE kcfv_page_id = '" . (int)$kcfv_page_id . "' AND kcfv_type = '" . (int)$typepage . "'");		
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "kcf_feeld kf "
											. "INNER JOIN " . DB_PREFIX . "kcf_feeld_description kfd ON (kfd.kcffeeld_id = kf.kcffeeld_id) "
											. "WHERE kf.kcf_id = '" . $kcfid . "'");
					foreach($query->rows as $feelds){
						$this->db->query("INSERT INTO " . DB_PREFIX . "kcf_feeld_value "
										. "SET "
										. "kcfv_type = '" . $typepage . "', "
										. "kcfv_page_id = '" . $kcfv_page_id . "', "
										. "kcfv_feeld_id = '" . $feelds['kcffeeld_id'] . "', "
										. "kcfv_language_id = '" . $feelds['language_id'] . "', "
										. "kcfv_value = ''");
					}

					$this->db->query("DELETE FROM " . DB_PREFIX . "kcf_relations WHERE kcftype_id = '" . (int)$typepage . "' AND kcf_page_id = '" . (int)$kcfv_page_id . "'");
					$this->db->query("INSERT INTO " . DB_PREFIX . "kcf_relations SET kcf_id = '" . $kcfid . "', kcftype_id = '" . $typepage . "', kcf_page_id = '" . $kcfv_page_id . "'");
		} else {
					$this->db->query("DELETE FROM " . DB_PREFIX . "kcf_feeld_value WHERE kcfv_page_id = '" . (int)$kcfv_page_id . "' AND kcfv_type = '" . (int)$typepage . "'");		
					$this->db->query("DELETE FROM " . DB_PREFIX . "kcf_relations WHERE kcftype_id = '" . (int)$typepage . "' AND kcf_page_id = '" . (int)$kcfv_page_id . "'");
		}
	}
	
	public function getKcfFeelds($kcf_type, $kcfv_page_id) {
		$result = array();
		$query = $this->db->query("SELECT kfv.kcfv_feeld_id, kfv.kcfv_language_id, kfv.kcfv_value, kf.`type` FROM " . DB_PREFIX . "kcf_feeld_value kfv "
								. "LEFT JOIN " . DB_PREFIX . "kcf_feeld kf ON (kf.kcffeeld_id = kfv.kcfv_feeld_id) "
								. "WHERE kfv.kcfv_type='" . $kcf_type . "' AND kfv.kcfv_page_id='" . $kcfv_page_id . "'");
		$predresult = $query->rows;
		foreach($predresult as $pr){
			//Получим название полей
			$feeld_name = $this->db->query("SELECT kfd.name, kfd.title FROM oc_kcf_feeld_description kfd WHERE kfd.kcffeeld_id = '" . $pr['kcfv_feeld_id'] . "' AND kfd.language_id = '" . $pr['kcfv_language_id'] . "'")->row;
			$img = '';			
			if($pr['kcfv_language_id']==1){
				$img = 'language/en-gb/en-gb.png';
				$tit = 'English';
			} else if($pr['kcfv_language_id']==2){
				$img = 'language/ru-ru/ru-ru.png';
				$tit = 'Русский';
			} else if($pr['kcfv_language_id']==3){
				$img = 'language/ua-uk/ua-uk.png';
				$tit = 'Украинский';
			}
			$result[$pr['kcfv_language_id']][] = array(
				'kcfv_feeld_id' => $pr['kcfv_feeld_id'],
				'kcfv_lang_img' => $img,
				'kcfv_lang_title' => $tit,
				'kcfv_value' => $pr['kcfv_value'],
				'type' => $pr['type'],
				'name' => $feeld_name['name'],
				'title' => $feeld_name['title']
			);
		}
		return $result;
	}	
	
	public function getKcfUpdateFeeld($page_type, $page_id, $data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "kcf_feeld_value WHERE kcfv_page_id = '" . (int)$page_id . "' AND kcfv_type = '" . (int)$page_type . "'");	
		foreach($data as $kcfv_language_id=>$value){
			foreach($value as $kcfv_feeld_id=>$kcfv_value){
						$this->db->query("INSERT INTO " . DB_PREFIX . "kcf_feeld_value "
										. "SET "
										. "kcfv_type = '" . $page_type . "', "
										. "kcfv_page_id = '" . $page_id . "', "
										. "kcfv_feeld_id = '" . $kcfv_feeld_id . "', "
										. "kcfv_language_id = '" . $kcfv_language_id . "', "
										. "kcfv_value = '" . $kcfv_value . "'");
			}
		}		
	}
	
	public function getKcfTypeNow($page_type, $page_id) {
		$query = $this->db->query("SELECT kcf_id FROM " . DB_PREFIX . "kcf_relations WHERE kcftype_id = '" . $page_type . "' AND kcf_page_id = '" . $page_id . "'");
		
		if(isset($query->row['kcf_id']) && $query->row['kcf_id']){
			return $query->row['kcf_id'];
		} else {
			return array();
		}
	}
}
