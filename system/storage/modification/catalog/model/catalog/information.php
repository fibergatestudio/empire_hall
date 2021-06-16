<?php
class ModelCatalogInformation extends Model {
	public function getInformation($information_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE i.information_id = '" . (int)$information_id . "' AND id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1'");

    if ($this->config->get('mlseo_enabled') && $query->num_rows && $this->config->get('mlseo_multistore') && $this->config->get('config_store_id')) {
      $this->load->model('tool/seo_package');
      $seoDescription = $this->model_tool_seo_package->getSeoDescription('information', $query->row['information_id']);
      
      if (!empty($seoDescription['meta_title'])) {
        $query->row['meta_title'] = $seoDescription['meta_title'];
      }
      
      if (!empty($seoDescription['meta_description'])) {
        $query->row['meta_description'] = $seoDescription['meta_description'];
      }
      
      if (!empty($seoDescription['meta_keyword'])) {
        $query->row['meta_keyword'] = $seoDescription['meta_keyword'];
      }
      
      if (!empty($seoDescription['name'])) {
        $query->row['title'] = $seoDescription['name'];
      }
      
      if (isset($seoDescription['description']) && trim(strip_tags($seoDescription['description']))) {
        $query->row['description'] = $seoDescription['description'];
      }
      
      if (!empty($seoDescription['seo_h1'])) {
        $query->row['seo_h1'] = $seoDescription['seo_h1'];
      }
      
      if (!empty($seoDescription['seo_h2'])) {
        $query->row['seo_h2'] = $seoDescription['seo_h2'];
      }
      
      if (!empty($seoDescription['seo_h3'])) {
        $query->row['seo_h3'] = $seoDescription['seo_h3'];
      }
    }
      

		return $query->row;
	}

	
                  private static $getCacheInstance = null;
                  public function getInformations() {
                     /**
                     * opencart cache code start here
                     */
                    $cache_information_status = false;
                    if($this->config->get('module_oc_cache_status') && (isset($this->config->get('module_oc_cache_information_module')['status']) && $this->config->get('module_oc_cache_information_module')['status'])){
                      $cache_information_status = true;
                        $lang_store_id = '.'.(int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id');

                        if (self::$getCacheInstance == null) {
                          self::$getCacheInstance = $this->webkulcache->get_InstanceCache('files');
                        }
                      $CachedString     = self::$getCacheInstance->getItem('information_module');
                      $info_container = $CachedString->get('information_module'.$lang_store_id);
                        if(isset($info_container['information_module'.$lang_store_id])){
                            return $info_container['information_module'.$lang_store_id];
                        }
                    }
                    /**
                    * opencart cache code end here
                    */
              
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1' ORDER BY i.sort_order, LCASE(id.title) ASC");

    if ($this->config->get('mlseo_enabled') && $query->num_rows && $this->config->get('mlseo_multistore') && $this->config->get('config_store_id')) {
      $this->load->model('tool/seo_package');
      foreach ($query->rows as &$row) {
        $seoDescription = $this->model_tool_seo_package->getSeoDescription('information', $row['information_id']);
        if (!empty($seoDescription['name'])) {
          $row['title'] = $seoDescription['name'];
        }
      }
    }
      


                /**
                 * opencart cache code start here
                 */
                 if($cache_information_status){
                    $info_container['information_module'.$lang_store_id] = $query->rows;
                    if($this->config->get('module_oc_cache_information_module')['expire'])
                      $CachedString->set($info_container)->expiresAfter($this->config->get('module_oc_cache_information_module')['expire']);
                    else
                      $CachedString->set($info_container);

                    self::$getCacheInstance->save($CachedString);
                  }
                /**
                * opencart cache code end here
                */
              
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

                /**
                 * opencart cache code start here
                 */
                 if($cache_information_status){
                    $info_container['information_module'.$lang_store_id] = $query->rows;
                    if($this->config->get('module_oc_cache_information_module')['expire'])
                      $CachedString->set($info_container)->expiresAfter($this->config->get('module_oc_cache_information_module')['expire']);
                    else
                      $CachedString->set($info_container);

                    self::$getCacheInstance->save($CachedString);
                  }
                /**
                * opencart cache code end here
                */
              
            return $query->rows;
        } else {
            return false;
        }
    }
}