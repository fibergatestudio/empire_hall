<?php
class ModelCatalogMenu extends Model {
    
    
    public function getMenuDescriptions($menu_id) {
		$menu_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "menu_description WHERE menu_id = '" . (int)$menu_id . "'");

		foreach ($query->rows as $result) {
			$menu_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'href'       => $result['href']
			);
		}

		return $menu_description_data;
    }
    
    public function getMenu($menu_id) {
        
        $query = $this->db->query("SELECT DISTINCT *, (SELECT GROUP_CONCAT(md1.name ORDER BY level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') FROM " . DB_PREFIX . "menu_path mp LEFT JOIN " . DB_PREFIX . "menu_description md1 ON (mp.path_id = md1.menu_id AND mp.menu_id !=mp.path_id) WHERE mp.menu_id = m.menu_id AND md1.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY mp.path_id) as path FROM " . DB_PREFIX . "menu m LEFT JOIN " . DB_PREFIX . "menu_description md2 ON (m.menu_id = md2.menu_id) WHERE m.menu_id = '".(int)$menu_id."' AND md2.language_id = '" . (int)$this->config->get('config_language_id') . "' ");
        
              
	return $query->row;
    }
    
    public function addMenu($data) {
        if(!isset($data['target_blank'])){ $data['target_blank'] = 0; }        
        $this->db->query("INSERT INTO " . DB_PREFIX . "menu SET parent_id = '".(int)$data['parent_id']."', status = '".(int)$data['status']."', target_blank = '".(int)$data['target_blank']."', sort_order = '".(int)$data['sort_order']."'");

		$menu_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "menu SET image = '" . $this->db->escape($data['image']) . "' WHERE menu_id = '" . (int)$menu_id . "'");
		}

		foreach ($data['menu_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "menu_description SET menu_id = '" . (int)$menu_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', href = '" . $this->db->escape($value['href']) . "'");
		}
                
                $level = 0;

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "menu_path` WHERE menu_id = '" . (int)$data['parent_id'] . "' ORDER BY `level` ASC");

		foreach ($query->rows as $result) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "menu_path` SET `menu_id` = '" . (int)$menu_id . "', `path_id` = '" . (int)$result['path_id'] . "', `level` = '" . (int)$level . "'");

			$level++;
		}

		$this->db->query("INSERT INTO `" . DB_PREFIX . "menu_path` SET `menu_id` = '" . (int)$menu_id . "', `path_id` = '" . (int)$menu_id . "', `level` = '" . (int)$level . "'");

		$this->cache->delete('menu');

		return $menu_id;
    }
    
    public function editMenu($menu_id, $data) {
		if(!isset($data['target_blank'])){ $data['target_blank'] = 0; }
		$this->db->query("UPDATE " . DB_PREFIX . "menu SET parent_id = '".(int)$data['parent_id']."', status = '".(int)$data['status']."', target_blank = '".(int)$data['target_blank']."', sort_order = '".(int)$data['sort_order']."' WHERE menu_id = '".(int)$menu_id."'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "menu SET image = '" . $this->db->escape($data['image']) . "' WHERE menu_id = '" . (int)$menu_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "menu_description WHERE menu_id = '" . (int)$menu_id . "'");

		foreach ($data['menu_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "menu_description SET menu_id = '" . (int)$menu_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', href = '" . $this->db->escape($value['href']) . "'");
		}
                
                $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "menu_path` WHERE path_id = '" . (int)$menu_id . "' ORDER BY level ASC");

		if ($query->rows) {
			foreach ($query->rows as $menu_path) {
				// Delete the path below the current one
				$this->db->query("DELETE FROM `" . DB_PREFIX . "menu_path` WHERE menu_id = '" . (int)$menu_path['menu_id'] . "' AND level < '" . (int)$menu_path['level'] . "'");

				$path = array();

				// Get the nodes new parents
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "menu_path` WHERE menu_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Get whats left of the nodes current path
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "menu_path` WHERE menu_id = '" . (int)$menu_path['menu_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Combine the paths with a new level
				$level = 0;

				foreach ($path as $path_id) {
					$this->db->query("REPLACE INTO `" . DB_PREFIX . "menu_path` SET menu_id = '" . (int)$menu_path['menu_id'] . "', `path_id` = '" . (int)$path_id . "', level = '" . (int)$level . "'");

					$level++;
				}
			}
		} else {
			// Delete the path below the current one
			$this->db->query("DELETE FROM `" . DB_PREFIX . "menu_path` WHERE menu_id = '" . (int)$menu_id . "'");

			// Fix for records with no paths
			$level = 0;

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "menu_path` WHERE menu_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

			foreach ($query->rows as $result) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "menu_path` SET menu_id = '" . (int)$menu_id . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

				$level++;
			}

			$this->db->query("REPLACE INTO `" . DB_PREFIX . "menu_path` SET menu_id = '" . (int)$menu_id . "', `path_id` = '" . (int)$menu_id . "', level = '" . (int)$level . "'");
		}

		$this->cache->delete('menu');
    }
    
    public function deleteMenu($menu_id) {

		$this->db->query("DELETE FROM " . DB_PREFIX . "menu_path WHERE menu_id = '" . (int)$menu_id . "'");

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "menu_path WHERE path_id = '" . (int)$menu_id . "'");

		foreach ($query->rows as $result) {
			$this->deleteMenu($result['menu_id']);
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "menu WHERE menu_id = '" . (int)$menu_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "menu_description WHERE menu_id = '" . (int)$menu_id . "'");

		$this->cache->delete('menu');
	}
    
    
    public function getMenus($data = array()){
        
        $sql = "SELECT mp.menu_id AS menu_id, GROUP_CONCAT(md1.name ORDER BY mp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, m1.parent_id,m1.sort_order FROM " . DB_PREFIX . "menu_path mp LEFT JOIN " . DB_PREFIX . "menu m1 ON (mp.menu_id = m1.menu_id) LEFT JOIN " . DB_PREFIX . "menu c2 ON (mp.path_id = c2.menu_id) LEFT JOIN " . DB_PREFIX . "menu_description md1 ON (mp.path_id = md1.menu_id) LEFT JOIN " . DB_PREFIX . "menu_description md2 ON (mp.menu_id = md2.menu_id) WHERE md1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND md2.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND md2.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sql .= " GROUP BY mp.menu_id";

		$sort_data = array(
			'name'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY menu_id";
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
    
    public function getTotalMenus() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "menu");

		return $query->row['total'];
	}
    
}
?>
