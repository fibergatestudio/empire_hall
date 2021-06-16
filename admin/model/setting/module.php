<?php
class ModelSettingModule extends Model {
	public function addModule($code, $data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "module` SET `name` = '" . $this->db->escape($data['name']) . "', `code` = '" . $this->db->escape($code) . "', `setting` = '" . $this->db->escape(json_encode($data)) . "'");
		
		$module_id = $this->db->getLastId();
		if($code == 'html'){
			$this->addHtmlTrigger($module_id, $data);
		}		
	}
	
	public function editModule($module_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "module` SET `name` = '" . $this->db->escape($data['name']) . "', `setting` = '" . $this->db->escape(json_encode($data)) . "' WHERE `module_id` = '" . (int)$module_id . "'");
            
		if(isset($data['product_desc_img'])){
			$this->addHtmlTrigger($module_id, $data);
		}		
	}

	public function deleteModule($module_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "module` WHERE `module_id` = '" . (int)$module_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "layout_module` WHERE `code` LIKE '%." . (int)$module_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "html_trigger` WHERE `module_id` = '" . (int)$module_id . "'");
	}
		
	public function getModule($module_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `module_id` = '" . (int)$module_id . "'");

		if ($query->row) {
			return json_decode($query->row['setting'], true);
		} else {
			return array();
		}
	}
	
	public function getModules() {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` ORDER BY `code`");

		return $query->rows;
	}	
		
	public function getModulesByCode($code) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `code` = '" . $this->db->escape($code) . "' ORDER BY `name`");

		return $query->rows;
	}	
	
	public function deleteModulesByCode($code) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "module` WHERE `code` = '" . $this->db->escape($code) . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "layout_module` WHERE `code` LIKE '" . $this->db->escape($code) . "' OR `code` LIKE '" . $this->db->escape($code . '.%') . "'");
	}	
	
	public function addHtmlTrigger($module_id, $data=array()){
		$this->db->query("DELETE FROM " . DB_PREFIX . "html_trigger WHERE module_id = '" . (int)$module_id . "'");
		if(isset($data['product_desc_img'])){
			foreach ($data['product_desc_img'] as $language_id =>$desc) {
				foreach($desc as $value){
				  $this->db->query("INSERT INTO " . DB_PREFIX . "html_trigger SET module_id = '" . (int)$module_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) ."', image = '".$this->db->escape($value['image'])."' ");
				}
			}
		}
	}

	public function getHtmlTrigger($module_id) {
		$trigger_description_data = array();
		$this->load->model('tool/image');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "html_trigger WHERE module_id = '" . (int)$module_id . "'");

		foreach ($query->rows as $key=>$result) {
			$trigger_description_data[$result['language_id']][$key] = array(
				'title'             => $result['title'],
				'image'             => $result['image'],
				'thumb'             => $result['image'] ? $this->model_tool_image->resize($result['image'], 100, 100):$this->model_tool_image->resize("placeholder.png", 100, 100),
				'description'       => $result['description']
			);
		}
		return $trigger_description_data;
	}
}