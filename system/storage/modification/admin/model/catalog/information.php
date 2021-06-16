<?php
class ModelCatalogInformation extends Model {
	public function addInformation($data) {
    $default_lang = $this->db->query("SELECT language_id FROM " . DB_PREFIX . "language WHERE code = '" . $this->config->get('config_language') . "'")->row['language_id'];
    $isInsertOrEdit = (strpos(__METHOD__, 'edit') !== false) ? 'edit' : 'insert';
		$this->db->query("INSERT INTO " . DB_PREFIX . "information SET image = '" . $data['image'] . "', sort_order = '" . (int)$data['sort_order'] . "', bottom = '" . (isset($data['bottom']) ? (int)$data['bottom'] : 0) . "', status = '" . (int)$data['status'] . "'");

		$information_id = $this->db->getLastId();


      if (version_compare(VERSION, '3', '>=')) {
        $currentKeywordsQuery = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'information_id=" . (int)$information_id . "'")->rows;
        $this->{'db'}->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'information_id=" . (int)$information_id . "'");
      } else {
        $currentKeywordsQuery = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE query = 'information_id=" . (int)$information_id . "'")->rows;
        $this->{'db'}->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'information_id=" . (int)$information_id . "'");
      }
      
      $currentKeywords = array();
      
      foreach ($currentKeywordsQuery as $curKeyword) {
        $currentKeywords[(isset($curKeyword['store_id']) ? $curKeyword['store_id'] : 0)][(isset($curKeyword['language_id']) ? $curKeyword['language_id'] : $default_lang)] = $curKeyword['keyword'];
      }
      
      if ($this->config->get('mlseo_multistore')) {
        $this->load->model('catalog/seo_package');
        $this->model_catalog_seo_package->setSeoDescriptions('information', $data, $information_id, $isInsertOrEdit, $currentKeywords);
      }
      
		foreach ($data['information_description'] as $language_id => $value) {

      if ($this->config->get('mlseo_enabled')) {
        if (isset($data['keyword'])) {
          unset($data['keyword']);
        }
        
        if ($this->config->get('mlseo_'.$isInsertOrEdit.'autotitle')) {
          $value['title'] = ($value['title']) ? $value['title'] : $data['information_description'][$default_lang]['title'];
          $value['description'] = ($value['description']) ? $value['description'] : $data['information_description'][$default_lang]['description'];
        }
        
        $this->load->model('tool/seo_package');
        
        $data['information_id'] = $information_id; // add id into dataset for use with patterns
        
        $seo_kw = '';
        
        if (empty($value['seo_keyword']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autourl')) {
          $seo_kw = $this->model_tool_seo_package->transformInformation($this->config->get('mlseo_information_url_pattern'), $language_id, $data);
        } else if (!empty($value['seo_keyword'])) {
          $seo_kw = html_entity_decode($value['seo_keyword'], ENT_QUOTES, 'UTF-8');
        }
        
        if ($seo_kw) {
          $seo_kw = $this->model_tool_seo_package->filter_seo($seo_kw, 'information', $information_id, $language_id);
        }
      
        $url_alias_table = version_compare(VERSION, '3', '>=') ? 'seo_url' : 'url_alias';
        
        if ($this->config->get('mlseo_backup')) {
          $currentKeyword = isset($currentKeywords[0][$language_id]) ? $currentKeywords[0][$language_id] : '';
          
          if ($currentKeyword && $currentKeyword != $seo_kw) {
            $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "url_redirect SET query = '/" . $this->db->escape($currentKeyword) . "', redirect = 'information/information&information_id=" . (int) $information_id . "', language_id = '" . (int) $language_id . "'");
          }
        }
        
        if (version_compare(VERSION, '3', '>=')) {
          $this->db->query("INSERT INTO " . DB_PREFIX . $url_alias_table . " SET query = 'information_id=" . (int)$information_id . "', language_id = '" . (int)$language_id . "', keyword = '" . $this->db->escape($seo_kw) . "', store_id = 0");
        } else if ($this->config->get('mlseo_ml_mode')) {
          $this->db->query("INSERT INTO " . DB_PREFIX . $url_alias_table . " SET query = 'information_id=" . (int)$information_id . "', language_id = '" . (int)$language_id . "', keyword = '" . $this->db->escape($seo_kw) . "'");
        } else {
          $this->db->query("INSERT INTO " . DB_PREFIX . $url_alias_table . " SET query = 'information_id=" . (int)$information_id . "', keyword = '" . $this->db->escape($seo_kw) . "'");
        }
      
        if (!$value['meta_title'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autoseotitle')) {
          $value['meta_title'] = $this->model_tool_seo_package->transformInformation($this->config->get('mlseo_information_title_pattern'), $language_id, $data);
        }
        if (!trim(strip_tags(html_entity_decode($value['description'], ENT_QUOTES, 'UTF-8'))) && $this->config->get('mlseo_'.$isInsertOrEdit.'autodesc')) {
          $value['description'] = $this->model_tool_seo_package->transformInformation($this->config->get('mlseo_category_full_desc_pattern'), $language_id, $data);
        }
        if (!$value['meta_description'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autometadesc')) {
          $value['meta_description'] = $this->model_tool_seo_package->transformInformation($this->config->get('mlseo_information_description_pattern'), $language_id, $data);
        }
        if (!$value['meta_keyword'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autometakeyword')) {
          $value['meta_keyword'] = $this->model_tool_seo_package->transformInformation($this->config->get('mlseo_information_keyword_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h1']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh1')) {
          $value['seo_h1'] = $this->model_tool_seo_package->transformInformation($this->config->get('mlseo_information_h1_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h2']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh2')) {
          $value['seo_h2'] = $this->model_tool_seo_package->transformInformation($this->config->get('mlseo_information_h2_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h3']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh3')) {
          $value['seo_h3'] = $this->model_tool_seo_package->transformInformation($this->config->get('mlseo_information_h3_pattern'), $language_id, $data);
        }
      }
      
			
        $value['seo_h1'] = empty($value['seo_h1']) ? '' : $value['seo_h1'];
        $value['seo_h2'] = empty($value['seo_h2']) ? '' : $value['seo_h2'];
        $value['seo_h3'] = empty($value['seo_h3']) ? '' : $value['seo_h3'];
        
        $extra_fields = '';
        if ($this->config->get('mlseo_enabled')) {
          $extra_fields = "seo_keyword = '" . $this->db->escape($seo_kw) . "', seo_h1 = '" . $this->db->escape($value['seo_h1']) . "', seo_h2 = '" . $this->db->escape($value['seo_h2']) . "', seo_h3 = '" . $this->db->escape($value['seo_h3']) . "', ";
          if (substr(VERSION, 0, 1) == 1) {
            $extra_fields .= "meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', ";
          }
        }
        
        $this->db->query("INSERT INTO " . DB_PREFIX . "information_description SET " . $extra_fields . " information_id = '" . (int)$information_id . "', language_id = '" . (int)$language_id . "', h1 = '" . $this->db->escape($value['h1']) . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}


    if (isset($data['meta_robots'])) {
      $this->db->query("UPDATE " . DB_PREFIX . "information SET meta_robots = '" . $this->db->escape($data['meta_robots']) . "' WHERE information_id = '" . (int)$information_id . "'");
    }
      
		if (isset($data['information_store'])) {
			foreach ($data['information_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "information_to_store SET information_id = '" . (int)$information_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
                
        if (isset($data['cover_image'])) {
			foreach ($data['cover_image'] as $image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "information_image SET information_id = '" . (int)$information_id . "', title = '" . $this->db->escape($image['title']) . "', image = '" . $this->db->escape($image['image']) . "', sort_order = '" . (int)$image['sort_order'] . "'");
				$information_image_id = $this->db->getLastId();
				foreach ($image['title'] as $key => $title) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "information_image_description SET "
							. "information_image_id = '" . (int)$information_image_id . "', "
							. "information_id = '" . (int)$information_id . "', "
							. "language_id = '" . (int)$key . "', "
							. "title = '" . $this->db->escape($title) . "'");
				}				
			}
		}
		
		// SEO URL
		if (isset($data['information_seo_url'])) {
			foreach ($data['information_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'information_id=" . (int)$information_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}
		
		if (isset($data['information_layout'])) {
			foreach ($data['information_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "information_to_layout SET information_id = '" . (int)$information_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

        if(isset($data['information_triggers'])){
            foreach ($data['information_triggers'] as $language_id => $desc) {
                foreach($desc as $value){
                    $this->db->query("INSERT INTO " . DB_PREFIX . "information_triggers SET information_id = '" . (int)$information_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) ."', image = '".$this->db->escape($value['image'])."', image_width = '" . (int)$this->db->escape($value['image_width']) . "', image_height = '" . (int)$this->db->escape($value['image_height']) . "'");
                }
            }
        }

		
        $this->cache->delete('information');
        if($this->config->get('module_oc_cache_status') && file_exists(DIR_APPLICATION . '../vendor/autoload.php')){
          $webkulcache = $this->load->library('cart/webkulcache');
          $this->webkulcache->deleteCache(array('information_module'), 'files');
        }
      

		return $information_id;
	}

	public function editInformation($information_id, $data) {
    $default_lang = $this->db->query("SELECT language_id FROM " . DB_PREFIX . "language WHERE code = '" . $this->config->get('config_language') . "'")->row['language_id'];
    $isInsertOrEdit = (strpos(__METHOD__, 'edit') !== false) ? 'edit' : 'insert';
		$this->db->query("UPDATE " . DB_PREFIX . "information SET image = '" . $data['image'] . "',  sort_order = '" . (int)$data['sort_order'] . "', bottom = '" . (isset($data['bottom']) ? (int)$data['bottom'] : 0) . "', status = '" . (int)$data['status'] . "' WHERE information_id = '" . (int)$information_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "information_description WHERE information_id = '" . (int)$information_id . "'");


      if (version_compare(VERSION, '3', '>=')) {
        $currentKeywordsQuery = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'information_id=" . (int)$information_id . "'")->rows;
        $this->{'db'}->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'information_id=" . (int)$information_id . "'");
      } else {
        $currentKeywordsQuery = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE query = 'information_id=" . (int)$information_id . "'")->rows;
        $this->{'db'}->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'information_id=" . (int)$information_id . "'");
      }
      
      $currentKeywords = array();
      
      foreach ($currentKeywordsQuery as $curKeyword) {
        $currentKeywords[(isset($curKeyword['store_id']) ? $curKeyword['store_id'] : 0)][(isset($curKeyword['language_id']) ? $curKeyword['language_id'] : $default_lang)] = $curKeyword['keyword'];
      }
      
      if ($this->config->get('mlseo_multistore')) {
        $this->load->model('catalog/seo_package');
        $this->model_catalog_seo_package->setSeoDescriptions('information', $data, $information_id, $isInsertOrEdit, $currentKeywords);
      }
      
		foreach ($data['information_description'] as $language_id => $value) {

      if ($this->config->get('mlseo_enabled')) {
        if (isset($data['keyword'])) {
          unset($data['keyword']);
        }
        
        if ($this->config->get('mlseo_'.$isInsertOrEdit.'autotitle')) {
          $value['title'] = ($value['title']) ? $value['title'] : $data['information_description'][$default_lang]['title'];
          $value['description'] = ($value['description']) ? $value['description'] : $data['information_description'][$default_lang]['description'];
        }
        
        $this->load->model('tool/seo_package');
        
        $data['information_id'] = $information_id; // add id into dataset for use with patterns
        
        $seo_kw = '';
        
        if (empty($value['seo_keyword']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autourl')) {
          $seo_kw = $this->model_tool_seo_package->transformInformation($this->config->get('mlseo_information_url_pattern'), $language_id, $data);
        } else if (!empty($value['seo_keyword'])) {
          $seo_kw = html_entity_decode($value['seo_keyword'], ENT_QUOTES, 'UTF-8');
        }
        
        if ($seo_kw) {
          $seo_kw = $this->model_tool_seo_package->filter_seo($seo_kw, 'information', $information_id, $language_id);
        }
      
        $url_alias_table = version_compare(VERSION, '3', '>=') ? 'seo_url' : 'url_alias';
        
        if ($this->config->get('mlseo_backup')) {
          $currentKeyword = isset($currentKeywords[0][$language_id]) ? $currentKeywords[0][$language_id] : '';
          
          if ($currentKeyword && $currentKeyword != $seo_kw) {
            $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "url_redirect SET query = '/" . $this->db->escape($currentKeyword) . "', redirect = 'information/information&information_id=" . (int) $information_id . "', language_id = '" . (int) $language_id . "'");
          }
        }
        
        if (version_compare(VERSION, '3', '>=')) {
          $this->db->query("INSERT INTO " . DB_PREFIX . $url_alias_table . " SET query = 'information_id=" . (int)$information_id . "', language_id = '" . (int)$language_id . "', keyword = '" . $this->db->escape($seo_kw) . "', store_id = 0");
        } else if ($this->config->get('mlseo_ml_mode')) {
          $this->db->query("INSERT INTO " . DB_PREFIX . $url_alias_table . " SET query = 'information_id=" . (int)$information_id . "', language_id = '" . (int)$language_id . "', keyword = '" . $this->db->escape($seo_kw) . "'");
        } else {
          $this->db->query("INSERT INTO " . DB_PREFIX . $url_alias_table . " SET query = 'information_id=" . (int)$information_id . "', keyword = '" . $this->db->escape($seo_kw) . "'");
        }
      
        if (!$value['meta_title'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autoseotitle')) {
          $value['meta_title'] = $this->model_tool_seo_package->transformInformation($this->config->get('mlseo_information_title_pattern'), $language_id, $data);
        }
        if (!trim(strip_tags(html_entity_decode($value['description'], ENT_QUOTES, 'UTF-8'))) && $this->config->get('mlseo_'.$isInsertOrEdit.'autodesc')) {
          $value['description'] = $this->model_tool_seo_package->transformInformation($this->config->get('mlseo_category_full_desc_pattern'), $language_id, $data);
        }
        if (!$value['meta_description'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autometadesc')) {
          $value['meta_description'] = $this->model_tool_seo_package->transformInformation($this->config->get('mlseo_information_description_pattern'), $language_id, $data);
        }
        if (!$value['meta_keyword'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autometakeyword')) {
          $value['meta_keyword'] = $this->model_tool_seo_package->transformInformation($this->config->get('mlseo_information_keyword_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h1']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh1')) {
          $value['seo_h1'] = $this->model_tool_seo_package->transformInformation($this->config->get('mlseo_information_h1_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h2']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh2')) {
          $value['seo_h2'] = $this->model_tool_seo_package->transformInformation($this->config->get('mlseo_information_h2_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h3']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh3')) {
          $value['seo_h3'] = $this->model_tool_seo_package->transformInformation($this->config->get('mlseo_information_h3_pattern'), $language_id, $data);
        }
      }
      
			
        $value['seo_h1'] = empty($value['seo_h1']) ? '' : $value['seo_h1'];
        $value['seo_h2'] = empty($value['seo_h2']) ? '' : $value['seo_h2'];
        $value['seo_h3'] = empty($value['seo_h3']) ? '' : $value['seo_h3'];
        
        $extra_fields = '';
        if ($this->config->get('mlseo_enabled')) {
          $extra_fields = "seo_keyword = '" . $this->db->escape($seo_kw) . "', seo_h1 = '" . $this->db->escape($value['seo_h1']) . "', seo_h2 = '" . $this->db->escape($value['seo_h2']) . "', seo_h3 = '" . $this->db->escape($value['seo_h3']) . "', ";
          if (substr(VERSION, 0, 1) == 1) {
            $extra_fields .= "meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', ";
          }
        }
        
        $this->db->query("INSERT INTO " . DB_PREFIX . "information_description SET " . $extra_fields . " information_id = '" . (int)$information_id . "', language_id = '" . (int)$language_id . "', h1 = '" . $this->db->escape($value['h1']) . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "information_to_store WHERE information_id = '" . (int)$information_id . "'");


    if (isset($data['meta_robots'])) {
      $this->db->query("UPDATE " . DB_PREFIX . "information SET meta_robots = '" . $this->db->escape($data['meta_robots']) . "' WHERE information_id = '" . (int)$information_id . "'");
    }
      
		if (isset($data['information_store'])) {
			foreach ($data['information_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "information_to_store SET information_id = '" . (int)$information_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
                
        $this->db->query("DELETE FROM " . DB_PREFIX . "information_image WHERE information_id = '" . (int)$information_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "information_image_description WHERE information_id = '" . (int)$information_id . "'");
                
        if (isset($data['cover_image'])) {
			foreach ($data['cover_image'] as $image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "information_image SET information_id = '" . (int)$information_id . "', image = '" . $this->db->escape($image['image']) . "', sort_order = '" . (int)$image['sort_order'] . "'");
				$information_image_id = $this->db->getLastId();
				foreach ($image['title'] as $key => $title) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "information_image_description SET "
							. "information_image_id = '" . (int)$information_image_id . "', "
							. "information_id = '" . (int)$information_id . "', "
							. "language_id = '" . (int)$key . "', "
							. "title = '" . $this->db->escape($title) . "'");
				}
			}
		}
		
		
      if (!$this->config->get('mlseo_enabled')) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'information_id=" . (int)$information_id . "'");
      }
      

		if (isset($data['information_seo_url'])) {
			foreach ($data['information_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (trim($keyword)) {
						$this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'information_id=" . (int)$information_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "information_to_layout` WHERE information_id = '" . (int)$information_id . "'");

		if (isset($data['information_layout'])) {
			foreach ($data['information_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "information_to_layout` SET information_id = '" . (int)$information_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

        $this->db->query("DELETE FROM " . DB_PREFIX . "information_triggers WHERE information_id = '" . (int)$information_id . "'");

        if(isset($data['information_triggers'])){
            foreach ($data['information_triggers'] as $language_id => $desc) {
                foreach($desc as $value){
                    $this->db->query("INSERT INTO " . DB_PREFIX . "information_triggers SET information_id = '" . (int)$information_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) ."', image = '".$this->db->escape($value['image'])."', image_width = '" . (int)$this->db->escape($value['image_width']) . "', image_height = '" . (int)$this->db->escape($value['image_height']) . "'");
                }
            }
        }

		
        $this->cache->delete('information');
        if($this->config->get('module_oc_cache_status') && file_exists(DIR_APPLICATION . '../vendor/autoload.php')){
          $webkulcache = $this->load->library('cart/webkulcache');
          $this->webkulcache->deleteCache(array('information_module'), 'files');
        }
      
	}

	public function deleteInformation($information_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "information` WHERE information_id = '" . (int)$information_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "information_description` WHERE information_id = '" . (int)$information_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "information_to_store` WHERE information_id = '" . (int)$information_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "information_to_layout` WHERE information_id = '" . (int)$information_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "information_triggers` WHERE information_id = '" . (int)$information_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'information_id=" . (int)$information_id . "'");

		
        $this->cache->delete('information');
        if($this->config->get('module_oc_cache_status') && file_exists(DIR_APPLICATION . '../vendor/autoload.php')){
          $webkulcache = $this->load->library('cart/webkulcache');
          $this->webkulcache->deleteCache(array('information_module'), 'files');
        }
      
	}

	public function getInformation($information_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "information WHERE information_id = '" . (int)$information_id . "'");

		return $query->row;
	}

	public function getInformations($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'";

			$sort_data = array(
				'id.title',
				'i.sort_order'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY id.title";
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
		} else {
			$information_data = $this->cache->get('information.' . (int)$this->config->get('config_language_id'));

			if (!$information_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY id.title");

				$information_data = $query->rows;

				$this->cache->set('information.' . (int)$this->config->get('config_language_id'), $information_data);
			}

			return $information_data;
		}
	}

	public function getInformationDescriptions($information_id) {
		$information_description_data = array();

		
      $extra_select = '';
      
      $url_alias_table = version_compare(VERSION, '3', '>=') ? 'seo_url' : 'url_alias';
      
      if ($this->config->get('mlseo_enabled')) {
        if (version_compare(VERSION, '3', '>=') || ($this->config->get('mlseo_multistore') && $this->config->get('mlseo_ml_mode'))) {
          $extra_select = ",(SELECT keyword FROM " . DB_PREFIX . $url_alias_table . " u WHERE query = 'information_id=".$information_id."' AND (u.language_id = d.language_id OR u.language_id = 0) AND store_id = 0 LIMIT 1) AS seo_keyword";
        } else if ($this->config->get('mlseo_multistore')) {
          $extra_select = ",(SELECT keyword FROM " . DB_PREFIX . $url_alias_table . " u WHERE query = 'information_id=".$information_id."' AND store_id = 0 LIMIT 1) AS seo_keyword";
        } else if ($this->config->get('mlseo_ml_mode')) {
          $extra_select = ",(SELECT keyword FROM " . DB_PREFIX . $url_alias_table . " u WHERE query = 'information_id=".$information_id."' AND (u.language_id = d.language_id OR u.language_id = 0) LIMIT 1) AS seo_keyword";
        } else {
          $extra_select = ",(SELECT keyword FROM " . DB_PREFIX . $url_alias_table . " WHERE query = 'information_id=".$information_id."' LIMIT 1) AS seo_keyword";
        }
      }
      
      $query = $this->db->query("SELECT * ".$extra_select." FROM " . DB_PREFIX . "information_description d WHERE information_id = '" . (int)$information_id . "'");

		foreach ($query->rows as $result) {
			$information_description_data[$result['language_id']] = array(
				'title'				=> $result['title'],
        'meta_title'     => $result['meta_title'],
        'meta_description'       => $result['meta_description'],
        'meta_keyword'       => $result['meta_keyword'],
        'seo_keyword'     => isset($result['seo_keyword']) ? $result['seo_keyword'] : '',
        'seo_h1'       => isset($result['seo_h1']) ? $result['seo_h1'] : '',
        'seo_h2'       => isset($result['seo_h2']) ? $result['seo_h2'] : '',
        'seo_h3'       => isset($result['seo_h3']) ? $result['seo_h3'] : '',
				'h1'				=> $result['h1'],
				'description'		=> $result['description'],
				'meta_title'		=> $result['meta_title'],
				'meta_description'	=> $result['meta_description'],
				'meta_keyword'		=> $result['meta_keyword']
			);
		}

		return $information_description_data;
	}

    public function getInformationTriggers($information_id) {
        $triggers = array();

        $this->load->model('tool/image');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_triggers WHERE information_id = '" . (int)$information_id . "'");

        foreach ($query->rows as $key=>$result) {
            $triggers[$result['language_id']][$key] = array(
                'title'             => $result['title'],
        'meta_title'     => $result['meta_title'],
        'meta_description'       => $result['meta_description'],
        'meta_keyword'       => $result['meta_keyword'],
        'seo_keyword'     => isset($result['seo_keyword']) ? $result['seo_keyword'] : '',
        'seo_h1'       => isset($result['seo_h1']) ? $result['seo_h1'] : '',
        'seo_h2'       => isset($result['seo_h2']) ? $result['seo_h2'] : '',
        'seo_h3'       => isset($result['seo_h3']) ? $result['seo_h3'] : '',
                'image'             => $result['image'],
                'thumb'             => $result['image'] ? $this->model_tool_image->resize($result['image'], 100, 100):$this->model_tool_image->resize("placeholder.png", 100, 100),
                'image_width'       => $result['image_width'],
                'image_height'      => $result['image_height'],
                'description'       => $result['description']
            );
        }

        return $triggers;
    }

	public function getInformationStores($information_id) {
		$information_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_to_store WHERE information_id = '" . (int)$information_id . "'");

		foreach ($query->rows as $result) {
			$information_store_data[] = $result['store_id'];
		}

		return $information_store_data;
	}

	public function getInformationSeoUrls($information_id) {
		$information_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'information_id=" . (int)$information_id . "'");

		foreach ($query->rows as $result) {
			$information_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $information_seo_url_data;
	}

	public function getInformationLayouts($information_id) {
		$information_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_to_layout WHERE information_id = '" . (int)$information_id . "'");

		foreach ($query->rows as $result) {
			$information_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $information_layout_data;
	}

	public function getTotalInformations() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "information");

		return $query->row['total'];
	}

	public function getTotalInformationsByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "information_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}
	
	public function getNewInfoId() {
		$query = $this->db->query("SHOW TABLE STATUS WHERE `Name` = '" . DB_PREFIX . "information'");
		
		return $query->row['Auto_increment'];
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
}