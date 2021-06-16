<?php
class ModelDesignBanner extends Model {
	public function addBanner($data) {

        if($this->config->get('module_oc_cache_status') && file_exists(DIR_APPLICATION . '../vendor/autoload.php')){
          $webkulcache = $this->load->library('cart/webkulcache');
          $this->webkulcache->deleteCache(array('banner_design'), 'files');
        }
      
		$this->db->query("INSERT INTO " . DB_PREFIX . "banner SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "'");

		$banner_id = $this->db->getLastId();

		if (isset($data['banner_image'])) {
			foreach ($data['banner_image'] as $language_id => $value) {
				foreach ($value as $banner_image) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "banner_image SET banner_id = '" . (int)$banner_id . "', language_id = '" . (int)$language_id . "', title = '" .  $this->db->escape($banner_image['title']) . "', description = '" .  $this->db->escape($banner_image['description']) . "', link = '" .  $this->db->escape($banner_image['link']) . "', image = '" .  $this->db->escape($banner_image['image']) . "', image_sm = '" .  $this->db->escape($banner_image['image_sm']) . "', image_md = '" .  $this->db->escape($banner_image['image_md']) . "', image_lg = '" .  $this->db->escape($banner_image['image_lg']) . "', sort_order = '" .  (int)$banner_image['sort_order'] . "', title_button = '" .  $this->db->escape($banner_image['title_button']) . "'");
				}
			}
		}

		return $banner_id;
	}

	/*//temp
	 * public function addSmImageCol(){
        $this->db->query("ALTER TABLE " . DB_PREFIX . "banner_image ADD image_sm VARCHAR(255) NOT NULL AFTER image");
    }*/

   /*//temp
    public function addMdImageCol(){
        $this->db->query("ALTER TABLE " . DB_PREFIX . "banner_image ADD image_md VARCHAR(255) NOT NULL AFTER image_sm");
    }*/

    /*//temp
    public function addLgImageCol(){
        $this->db->query("ALTER TABLE " . DB_PREFIX . "banner_image ADD image_lg VARCHAR(255) NOT NULL AFTER image_md");
    }*/


	public function editBanner($banner_id, $data) {

        if($this->config->get('module_oc_cache_status') && file_exists(DIR_APPLICATION . '../vendor/autoload.php')){
          $webkulcache = $this->load->library('cart/webkulcache');
          $this->webkulcache->deleteCache(array('banner_design'), 'files');
        }
      
		$this->db->query("UPDATE " . DB_PREFIX . "banner SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "' WHERE banner_id = '" . (int)$banner_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int)$banner_id . "'");

		if (isset($data['banner_image'])) {
			foreach ($data['banner_image'] as $language_id => $value) {
				foreach ($value as $banner_image) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "banner_image SET banner_id = '" . (int)$banner_id . "', language_id = '" . (int)$language_id . "', title = '" .  $this->db->escape($banner_image['title']) . "', description = '" .  $this->db->escape($banner_image['description']) . "', link = '" .  $this->db->escape($banner_image['link']) . "', image = '" .  $this->db->escape($banner_image['image']) . "', image_sm = '" .  $this->db->escape($banner_image['image_sm']) . "', image_md = '" .  $this->db->escape($banner_image['image_md']) . "', image_lg = '" .  $this->db->escape($banner_image['image_lg']) . "', sort_order = '" .  (int)$banner_image['sort_order'] . "', title_button = '" .  $this->db->escape($banner_image['title_button']) . "'");				}
			}
		}
	}

	public function deleteBanner($banner_id) {

        if($this->config->get('module_oc_cache_status') && file_exists(DIR_APPLICATION . '../vendor/autoload.php')){
          $webkulcache = $this->load->library('cart/webkulcache');
          $this->webkulcache->deleteCache(array('banner_design'), 'files');
        }
      
		$this->db->query("DELETE FROM " . DB_PREFIX . "banner WHERE banner_id = '" . (int)$banner_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int)$banner_id . "'");
	}

	public function getBanner($banner_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "banner WHERE banner_id = '" . (int)$banner_id . "'");

		return $query->row;
	}

	public function getBanners($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "banner";

		$sort_data = array(
			'name',
			'status'
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

	public function getBannerImages($banner_id) {
		$banner_image_data = array();

		$banner_image_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int)$banner_id . "' ORDER BY sort_order ASC");

		foreach ($banner_image_query->rows as $banner_image) {
			$banner_image_data[$banner_image['language_id']][] = array(
				'title'      => $banner_image['title'],
				'description'=> $banner_image['description'],
				'link'       => $banner_image['link'],
				'title_button'       => $banner_image['title_button'],
				'image'      => $banner_image['image'],
				'image_sm'      => $banner_image['image_sm'],
				'image_md'      => $banner_image['image_md'],
				'image_lg'      => $banner_image['image_lg'],
				'sort_order' => $banner_image['sort_order']
			);
		}

		return $banner_image_data;
	}

	public function getTotalBanners() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "banner");

		return $query->row['total'];
	}
}
