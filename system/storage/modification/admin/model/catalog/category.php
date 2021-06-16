<?php
class ModelCatalogCategory extends Model {
	public function addCategory($data) {
    $default_lang = $this->db->query("SELECT language_id FROM " . DB_PREFIX . "language WHERE code = '" . $this->config->get('config_language') . "'")->row['language_id'];
    $isInsertOrEdit = (strpos(__METHOD__, 'edit') !== false) ? 'edit' : 'insert';
		$this->db->query("INSERT INTO " . DB_PREFIX . "category SET parent_id = '" . (int)$data['parent_id'] . "', `top` = '" . (isset($data['top']) ? (int)$data['top'] : 0) . "', `column` = '" . (int)$data['column'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', date_modified = NOW(), date_added = NOW()");

		$category_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "category SET image = '" . $this->db->escape($data['image']) . "' WHERE category_id = '" . (int)$category_id . "'");
		}


      if (version_compare(VERSION, '3', '>=')) {
        $currentKeywordsQuery = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'category_id=" . (int)$category_id . "'")->rows;
        $this->{'db'}->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'category_id=" . (int)$category_id . "'");
      } else {
        $currentKeywordsQuery = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE query = 'category_id=" . (int)$category_id . "'")->rows;
        $this->{'db'}->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'category_id=" . (int)$category_id . "'");
      }
      
      $currentKeywords = array();
      
      foreach ($currentKeywordsQuery as $curKeyword) {
        $currentKeywords[(isset($curKeyword['store_id']) ? $curKeyword['store_id'] : 0)][(isset($curKeyword['language_id']) ? $curKeyword['language_id'] : $default_lang)] = $curKeyword['keyword'];
      }
      
      if ($this->config->get('mlseo_multistore')) {
        $this->load->model('catalog/seo_package');
        $this->model_catalog_seo_package->setSeoDescriptions('category', $data, $category_id, $isInsertOrEdit, $currentKeywords);
      }
      
		foreach ($data['category_description'] as $language_id => $value) {
		    if (!isset($value['h1'])) { $value['h1'] =  $value['name'];}

      if ($this->config->get('mlseo_enabled')) {
        if (isset($data['keyword'])) {
          unset($data['keyword']);
        }
        
        if ($this->config->get('mlseo_'.$isInsertOrEdit.'autotitle')) {
          $value['name'] = ($value['name']) ? $value['name'] : $data['category_description'][$default_lang]['name'];
          $value['description'] = ($value['description']) ? $value['description'] : $data['category_description'][$default_lang]['description'];
        }

        $this->load->model('tool/seo_package');
        
        $data['category_id'] = $category_id; // add id into dataset for use with patterns
        
        $seo_kw = '';
        
        if (empty($value['seo_keyword']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autourl')) {
          $seo_kw = $this->model_tool_seo_package->transformCategory($this->config->get('mlseo_category_url_pattern'), $language_id, $data);
        } else if (!empty($value['seo_keyword'])) {
          $seo_kw = html_entity_decode($value['seo_keyword'], ENT_QUOTES, 'UTF-8');
        }
        
        if ($seo_kw) {
          $seo_kw = $this->model_tool_seo_package->filter_seo($seo_kw, 'category', $category_id, $language_id);
        }

        $url_alias_table = version_compare(VERSION, '3', '>=') ? 'seo_url' : 'url_alias';
        
        if ($this->config->get('mlseo_backup')) {
          $currentKeyword = isset($currentKeywords[0][$language_id]) ? $currentKeywords[0][$language_id] : '';
          
          if ($currentKeyword && $currentKeyword != $seo_kw) {
            $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "url_redirect SET query = '/" . $this->db->escape($currentKeyword) . "', redirect = 'product/category&path=" . (int) $category_id . "', language_id = '" . (int) $language_id . "'");
          }
        }
        
        if (version_compare(VERSION, '3', '>=')) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET query = 'category_id=" . (int)$category_id . "', language_id = '" . (int)$language_id . "', keyword = '" . $this->db->escape($seo_kw) . "', store_id = 0");
        } else if ($this->config->get('mlseo_ml_mode')) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'category_id=" . (int)$category_id . "', language_id = '" . (int)$language_id . "', keyword = '" . $this->db->escape($seo_kw) . "'");
        } else {
          $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'category_id=" . (int)$category_id . "', keyword = '" . $this->db->escape($seo_kw) . "'");
        }
        
        if (!$value['meta_title'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autoseotitle')) {
          $value['meta_title'] = $this->model_tool_seo_package->transformCategory($this->config->get('mlseo_category_title_pattern'), $language_id, $data);
        }
        if (!trim(strip_tags(html_entity_decode($value['description'], ENT_QUOTES, 'UTF-8'))) && $this->config->get('mlseo_'.$isInsertOrEdit.'autodesc')) {
          $value['description'] = $this->model_tool_seo_package->transformCategory($this->config->get('mlseo_category_full_desc_pattern'), $language_id, $data);
        }
        if (!$value['meta_description'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autometadesc')) {
          $value['meta_description'] = $this->model_tool_seo_package->transformCategory($this->config->get('mlseo_category_description_pattern'), $language_id, $data);
        }
        if (!$value['meta_keyword'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autometakeyword')) {
          $value['meta_keyword'] = $this->model_tool_seo_package->transformCategory($this->config->get('mlseo_category_keyword_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h1']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh1')) {
          $value['seo_h1'] = $this->model_tool_seo_package->transformCategory($this->config->get('mlseo_category_h1_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h2']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh2')) {
          $value['seo_h2'] = $this->model_tool_seo_package->transformCategory($this->config->get('mlseo_category_h2_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h3']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh3')) {
          $value['seo_h3'] = $this->model_tool_seo_package->transformCategory($this->config->get('mlseo_category_h3_pattern'), $language_id, $data);
        }
      }
      
			
        $value['seo_h1'] = empty($value['seo_h1']) ? '' : $value['seo_h1'];
        $value['seo_h2'] = empty($value['seo_h2']) ? '' : $value['seo_h2'];
        $value['seo_h3'] = empty($value['seo_h3']) ? '' : $value['seo_h3'];
        
        $extra_fields = '';
        if ($this->config->get('mlseo_enabled')) {
          $extra_fields = "seo_keyword = '" . $this->db->escape($seo_kw) . "', seo_h1 = '" . $this->db->escape($value['seo_h1']) . "', seo_h2 = '" . $this->db->escape($value['seo_h2']) . "', seo_h3 = '" . $this->db->escape($value['seo_h3']) . "', ";
          if (substr(VERSION, 0, 1) == 1) {
            $extra_fields .= "meta_title = '" . $this->db->escape($value['meta_title']) . "', ";
          }
        }
        
        $this->db->query("INSERT INTO " . DB_PREFIX . "category_description SET " . $extra_fields . " category_id = '" . (int)$category_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', h1 = '" . $this->db->escape($value['h1']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		// MySQL Hierarchical Data Closure Table Pattern
		$level = 0;

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$data['parent_id'] . "' ORDER BY `level` ASC");

		foreach ($query->rows as $result) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$category_id . "', `path_id` = '" . (int)$result['path_id'] . "', `level` = '" . (int)$level . "'");

			$level++;
		}

		$this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$category_id . "', `path_id` = '" . (int)$category_id . "', `level` = '" . (int)$level . "'");

		if (isset($data['category_filter'])) {
			foreach ($data['category_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_filter SET category_id = '" . (int)$category_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		if (isset($data['category_store'])) {
			foreach ($data['category_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_to_store SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['category_seo_url'])) {
			foreach ($data['category_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'category_id=" . (int)$category_id . "', keyword = '" . $this->db->escape($keyword) . "'");
                    } else {
                        if($language_id == 1) {
                            $seo_url = $data['category_description'][2]['name'] . '_en';
                            if(isset($data['category_description'][1]['name']) && $data['category_description'][1]['name'] != $data['category_description'][2]['name']) {
                                $seo_url = $data['category_description'][1]['name'];
                            }
                        }
                        if($language_id == 2) {
                            $seo_url = $data['category_description'][2]['name'];
                        }
                        if($language_id == 3) {
                            $seo_url = $data['category_description'][2]['name'] . '_ua';
                            if(isset($data['category_description'][3]['name']) && $data['category_description'][3]['name'] != $data['category_description'][2]['name']) {
                                $seo_url = $data['category_description'][3]['name'];
                            }
                        }

                        $this->translit->setSeoURL('category_id', $language_id, (int)$category_id, $seo_url);
                    }
				}
			}
		}

		// Set which layout to use with this category
		if (isset($data['category_layout'])) {
			foreach ($data['category_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_to_layout SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		
        $this->cache->delete('category');
        if($this->config->get('module_oc_cache_status') && file_exists(DIR_APPLICATION . '../vendor/autoload.php')){
          $webkulcache = $this->load->library('cart/webkulcache');
          $this->webkulcache->deleteCache(array('category_module','header_category'), 'files');
        }
      

		return $category_id;
	}

	public function editCategory($category_id, $data) {
    $default_lang = $this->db->query("SELECT language_id FROM " . DB_PREFIX . "language WHERE code = '" . $this->config->get('config_language') . "'")->row['language_id'];
    $isInsertOrEdit = (strpos(__METHOD__, 'edit') !== false) ? 'edit' : 'insert';
		$this->db->query("UPDATE " . DB_PREFIX . "category SET parent_id = '" . (int)$data['parent_id'] . "', `top` = '" . (isset($data['top']) ? (int)$data['top'] : 0) . "', `column` = '" . (int)$data['column'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', date_modified = NOW() WHERE category_id = '" . (int)$category_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "category SET image = '" . $this->db->escape($data['image']) . "' WHERE category_id = '" . (int)$category_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "category_description WHERE category_id = '" . (int)$category_id . "'");


      if (version_compare(VERSION, '3', '>=')) {
        $currentKeywordsQuery = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'category_id=" . (int)$category_id . "'")->rows;
        $this->{'db'}->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'category_id=" . (int)$category_id . "'");
      } else {
        $currentKeywordsQuery = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE query = 'category_id=" . (int)$category_id . "'")->rows;
        $this->{'db'}->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'category_id=" . (int)$category_id . "'");
      }
      
      $currentKeywords = array();
      
      foreach ($currentKeywordsQuery as $curKeyword) {
        $currentKeywords[(isset($curKeyword['store_id']) ? $curKeyword['store_id'] : 0)][(isset($curKeyword['language_id']) ? $curKeyword['language_id'] : $default_lang)] = $curKeyword['keyword'];
      }
      
      if ($this->config->get('mlseo_multistore')) {
        $this->load->model('catalog/seo_package');
        $this->model_catalog_seo_package->setSeoDescriptions('category', $data, $category_id, $isInsertOrEdit, $currentKeywords);
      }
      
		foreach ($data['category_description'] as $language_id => $value) {
            if (!isset($value['h1'])) { $value['h1'] =  $value['name'];}

      if ($this->config->get('mlseo_enabled')) {
        if (isset($data['keyword'])) {
          unset($data['keyword']);
        }
        
        if ($this->config->get('mlseo_'.$isInsertOrEdit.'autotitle')) {
          $value['name'] = ($value['name']) ? $value['name'] : $data['category_description'][$default_lang]['name'];
          $value['description'] = ($value['description']) ? $value['description'] : $data['category_description'][$default_lang]['description'];
        }

        $this->load->model('tool/seo_package');
        
        $data['category_id'] = $category_id; // add id into dataset for use with patterns
        
        $seo_kw = '';
        
        if (empty($value['seo_keyword']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autourl')) {
          $seo_kw = $this->model_tool_seo_package->transformCategory($this->config->get('mlseo_category_url_pattern'), $language_id, $data);
        } else if (!empty($value['seo_keyword'])) {
          $seo_kw = html_entity_decode($value['seo_keyword'], ENT_QUOTES, 'UTF-8');
        }
        
        if ($seo_kw) {
          $seo_kw = $this->model_tool_seo_package->filter_seo($seo_kw, 'category', $category_id, $language_id);
        }

        $url_alias_table = version_compare(VERSION, '3', '>=') ? 'seo_url' : 'url_alias';
        
        if ($this->config->get('mlseo_backup')) {
          $currentKeyword = isset($currentKeywords[0][$language_id]) ? $currentKeywords[0][$language_id] : '';
          
          if ($currentKeyword && $currentKeyword != $seo_kw) {
            $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "url_redirect SET query = '/" . $this->db->escape($currentKeyword) . "', redirect = 'product/category&path=" . (int) $category_id . "', language_id = '" . (int) $language_id . "'");
          }
        }
        
        if (version_compare(VERSION, '3', '>=')) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET query = 'category_id=" . (int)$category_id . "', language_id = '" . (int)$language_id . "', keyword = '" . $this->db->escape($seo_kw) . "', store_id = 0");
        } else if ($this->config->get('mlseo_ml_mode')) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'category_id=" . (int)$category_id . "', language_id = '" . (int)$language_id . "', keyword = '" . $this->db->escape($seo_kw) . "'");
        } else {
          $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'category_id=" . (int)$category_id . "', keyword = '" . $this->db->escape($seo_kw) . "'");
        }
        
        if (!$value['meta_title'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autoseotitle')) {
          $value['meta_title'] = $this->model_tool_seo_package->transformCategory($this->config->get('mlseo_category_title_pattern'), $language_id, $data);
        }
        if (!trim(strip_tags(html_entity_decode($value['description'], ENT_QUOTES, 'UTF-8'))) && $this->config->get('mlseo_'.$isInsertOrEdit.'autodesc')) {
          $value['description'] = $this->model_tool_seo_package->transformCategory($this->config->get('mlseo_category_full_desc_pattern'), $language_id, $data);
        }
        if (!$value['meta_description'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autometadesc')) {
          $value['meta_description'] = $this->model_tool_seo_package->transformCategory($this->config->get('mlseo_category_description_pattern'), $language_id, $data);
        }
        if (!$value['meta_keyword'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autometakeyword')) {
          $value['meta_keyword'] = $this->model_tool_seo_package->transformCategory($this->config->get('mlseo_category_keyword_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h1']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh1')) {
          $value['seo_h1'] = $this->model_tool_seo_package->transformCategory($this->config->get('mlseo_category_h1_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h2']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh2')) {
          $value['seo_h2'] = $this->model_tool_seo_package->transformCategory($this->config->get('mlseo_category_h2_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h3']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh3')) {
          $value['seo_h3'] = $this->model_tool_seo_package->transformCategory($this->config->get('mlseo_category_h3_pattern'), $language_id, $data);
        }
      }
      
			
        $value['seo_h1'] = empty($value['seo_h1']) ? '' : $value['seo_h1'];
        $value['seo_h2'] = empty($value['seo_h2']) ? '' : $value['seo_h2'];
        $value['seo_h3'] = empty($value['seo_h3']) ? '' : $value['seo_h3'];
        
        $extra_fields = '';
        if ($this->config->get('mlseo_enabled')) {
          $extra_fields = "seo_keyword = '" . $this->db->escape($seo_kw) . "', seo_h1 = '" . $this->db->escape($value['seo_h1']) . "', seo_h2 = '" . $this->db->escape($value['seo_h2']) . "', seo_h3 = '" . $this->db->escape($value['seo_h3']) . "', ";
          if (substr(VERSION, 0, 1) == 1) {
            $extra_fields .= "meta_title = '" . $this->db->escape($value['meta_title']) . "', ";
          }
        }
        
        $this->db->query("INSERT INTO " . DB_PREFIX . "category_description SET " . $extra_fields . " category_id = '" . (int)$category_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', h1 = '" . $this->db->escape($value['h1']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		// MySQL Hierarchical Data Closure Table Pattern
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE path_id = '" . (int)$category_id . "' ORDER BY level ASC");

		if ($query->rows) {
			foreach ($query->rows as $category_path) {
				// Delete the path below the current one
				$this->db->query("DELETE FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$category_path['category_id'] . "' AND level < '" . (int)$category_path['level'] . "'");

				$path = array();

				// Get the nodes new parents
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Get whats left of the nodes current path
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$category_path['category_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Combine the paths with a new level
				$level = 0;

				foreach ($path as $path_id) {
					$this->db->query("REPLACE INTO `" . DB_PREFIX . "category_path` SET category_id = '" . (int)$category_path['category_id'] . "', `path_id` = '" . (int)$path_id . "', level = '" . (int)$level . "'");

					$level++;
				}
			}
		} else {
			// Delete the path below the current one
			$this->db->query("DELETE FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$category_id . "'");

			// Fix for records with no paths
			$level = 0;

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

			foreach ($query->rows as $result) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET category_id = '" . (int)$category_id . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

				$level++;
			}

			$this->db->query("REPLACE INTO `" . DB_PREFIX . "category_path` SET category_id = '" . (int)$category_id . "', `path_id` = '" . (int)$category_id . "', level = '" . (int)$level . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "category_filter WHERE category_id = '" . (int)$category_id . "'");

		if (isset($data['category_filter'])) {
			foreach ($data['category_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_filter SET category_id = '" . (int)$category_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "category_to_store WHERE category_id = '" . (int)$category_id . "'");

		if (isset($data['category_store'])) {
			foreach ($data['category_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_to_store SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		// SEO URL
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'category_id=" . (int)$category_id . "'");

		if (isset($data['category_seo_url'])) {
			foreach ($data['category_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'category_id=" . (int)$category_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					} else {
					    if($language_id == 1) {
                            $seo_url = $data['category_description'][2]['name'] . '_en';
					        if(isset($data['category_description'][1]['name']) && $data['category_description'][1]['name'] != $data['category_description'][2]['name']) {
                                $seo_url = $data['category_description'][1]['name'];
                            }
                        }
                        if($language_id == 2) {
                            $seo_url = $data['category_description'][2]['name'];
                        }
                        if($language_id == 3) {
                            $seo_url = $data['category_description'][2]['name'] . '_ua';
                            if(isset($data['category_description'][3]['name']) && $data['category_description'][3]['name'] != $data['category_description'][2]['name']) {
                                $seo_url = $data['category_description'][3]['name'];
                            }
                        }

						$this->translit->setSeoURL('category_id', $language_id, (int)$category_id, $seo_url);
					}
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "'");

		if (isset($data['category_layout'])) {
			foreach ($data['category_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_to_layout SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		
        $this->cache->delete('category');
        if($this->config->get('module_oc_cache_status') && file_exists(DIR_APPLICATION . '../vendor/autoload.php')){
          $webkulcache = $this->load->library('cart/webkulcache');
          $this->webkulcache->deleteCache(array('category_module','header_category'), 'files');
        }
      
	}

	public function deleteCategory($category_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "category_path WHERE category_id = '" . (int)$category_id . "'");

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_path WHERE path_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$this->deleteCategory($result['category_id']);
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "category_description WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "category_filter WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "category_to_store WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'category_id=" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_category WHERE category_id = '" . (int)$category_id . "'");

		
        $this->cache->delete('category');
        if($this->config->get('module_oc_cache_status') && file_exists(DIR_APPLICATION . '../vendor/autoload.php')){
          $webkulcache = $this->load->library('cart/webkulcache');
          $this->webkulcache->deleteCache(array('category_module','header_category'), 'files');
        }
      
	}

	public function repairCategories($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category WHERE parent_id = '" . (int)$parent_id . "'");

		foreach ($query->rows as $category) {
			// Delete the path below the current one
			$this->db->query("DELETE FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$category['category_id'] . "'");

			// Fix for records with no paths
			$level = 0;

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$parent_id . "' ORDER BY level ASC");

			foreach ($query->rows as $result) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET category_id = '" . (int)$category['category_id'] . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

				$level++;
			}

			$this->db->query("REPLACE INTO `" . DB_PREFIX . "category_path` SET category_id = '" . (int)$category['category_id'] . "', `path_id` = '" . (int)$category['category_id'] . "', level = '" . (int)$level . "'");

			$this->repairCategories($category['category_id']);
		}
	}

	public function getCategory($category_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT GROUP_CONCAT(cd1.name ORDER BY level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id AND cp.category_id != cp.path_id) WHERE cp.category_id = c.category_id AND cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY cp.category_id) AS path FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (c.category_id = cd2.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getCategories($data = array()) {
		$sql = "SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, c1.parent_id, c1.sort_order FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id) WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND cd2.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sql .= " GROUP BY cp.category_id";

		$sort_data = array(
			'name',
			'sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY sort_order";
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

	public function getCategoryDescriptions($category_id) {
		$category_description_data = array();

		
      $extra_select = '';
      
      $url_alias_table = version_compare(VERSION, '3', '>=') ? 'seo_url' : 'url_alias';
      
      if ($this->config->get('mlseo_enabled')) {
        if (version_compare(VERSION, '3', '>=') || ($this->config->get('mlseo_multistore') && $this->config->get('mlseo_ml_mode'))) {
          $extra_select = ",(SELECT keyword FROM " . DB_PREFIX . $url_alias_table . " u WHERE query = 'category_id=".$category_id."' AND (u.language_id = d.language_id OR u.language_id = 0) AND store_id = 0 LIMIT 1) AS seo_keyword";
        } else if ($this->config->get('mlseo_multistore')) {
          $extra_select = ",(SELECT keyword FROM " . DB_PREFIX . $url_alias_table . " u WHERE query = 'category_id=".$category_id."' AND store_id = 0 LIMIT 1) AS seo_keyword";
        } else if ($this->config->get('mlseo_ml_mode')) {
          $extra_select = ",(SELECT keyword FROM " . DB_PREFIX . $url_alias_table . " u WHERE query = 'category_id=".$category_id."' AND (u.language_id = d.language_id OR u.language_id = 0) LIMIT 1) AS seo_keyword";
        } else {
          $extra_select = ",(SELECT keyword FROM " . DB_PREFIX . $url_alias_table . " WHERE query = 'category_id=".$category_id."' LIMIT 1) AS seo_keyword";
        }
      }
      
      $query = $this->db->query("SELECT * ".$extra_select." FROM " . DB_PREFIX . "category_description d WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
        'meta_title'     => isset($result['meta_title']) ? $result['meta_title'] : '',
        'seo_keyword'     => isset($result['seo_keyword']) ? $result['seo_keyword'] : '',
        'seo_h1'       => isset($result['seo_h1']) ? $result['seo_h1'] : '',
        'seo_h2'       => isset($result['seo_h2']) ? $result['seo_h2'] : '',
        'seo_h3'       => isset($result['seo_h3']) ? $result['seo_h3'] : '',
				'meta_title'       => $result['meta_title'],
				'h1'			   => $result['h1'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'description'      => $result['description']
			);
		}

		return $category_description_data;
	}

	public function getCategoryPath($category_id) {
		$query = $this->db->query("SELECT category_id, path_id, level FROM " . DB_PREFIX . "category_path WHERE category_id = '" . (int)$category_id . "'");

		return $query->rows;
	}

	public function getCategoryFilters($category_id) {
		$category_filter_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_filter WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_filter_data[] = $result['filter_id'];
		}

		return $category_filter_data;
	}

	public function getCategoryStores($category_id) {
		$category_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_to_store WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_store_data[] = $result['store_id'];
		}

		return $category_store_data;
	}

	public function getCategorySeoUrls($category_id) {
		$category_seo_url_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'category_id=" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $category_seo_url_data;
	}

	public function getCategoryLayouts($category_id) {
		$category_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $category_layout_data;
	}

	public function getTotalCategories() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category");

		return $query->row['total'];
	}

	public function getTotalCategoriesByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}

	public function getNewCategoryId() {
		$query = $this->db->query("SHOW TABLE STATUS WHERE `Name` = '" . DB_PREFIX . "category'");

		return $query->row['Auto_increment'];
	}
}
