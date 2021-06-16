<?php
class ModelCatalogCategory extends Model {
	public function getCategoryPath($category_id){
		$path = '';
		$category = $this->db->query("SELECT c.parent_id, c.category_id FROM " . DB_PREFIX . "category c WHERE c.category_id = " .(int)($category_id));

		if($category->row['parent_id'] != 0){
			$path .= $this->getCategoryPath($category->row['parent_id']) . '_';
		}

		$path .= $category->row['category_id'];

		return $path;
	}

	public function getCategoryName($category_id) {
		$query = $this->db->query("SELECT DISTINCT cd.name FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");

		return $query->row['name'];
	}
	
	public function getCategory($category_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");

    if ($this->config->get('mlseo_enabled') && $query->num_rows && $this->config->get('mlseo_multistore') && $this->config->get('config_store_id')) {
      $this->load->model('tool/seo_package');
      $seoDescription = $this->model_tool_seo_package->getSeoDescription('category', $query->row['category_id']);
      
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
        $query->row['name'] = $seoDescription['name'];
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

	public function getCategories($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");

    if ($this->config->get('mlseo_enabled') && $query->num_rows && $this->config->get('mlseo_multistore') && $this->config->get('config_store_id')) {
      $this->load->model('tool/seo_package');
      foreach ($query->rows as &$row) {
        $seoDescription = $this->model_tool_seo_package->getSeoDescription('category', $row['category_id']);
        if (!empty($seoDescription['name'])) {
          $row['name'] = $seoDescription['name'];
        }
      }
    }
      

		return $query->rows;
	}

	
                  private static $getCacheInstance = null;
                  public function getCategoryFilters($category_id) {
                     /**
                     * opencart cache code start here
                     */
                    $cache_filter_status = false;
                    if($this->config->get('module_oc_cache_status') && (isset($this->config->get('module_oc_cache_filter_module')['status']) && $this->config->get('module_oc_cache_filter_module')['status'])){
                      $cache_filter_status = true;
                        $lang_store_id = '.'.(int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id');

                        if (self::$getCacheInstance == null) {
                          self::$getCacheInstance = $this->webkulcache->get_InstanceCache('files');
                        }
                      $CachedString     = self::$getCacheInstance->getItem('filter_module');
                      $filter_container = $CachedString->get('filter_module'.$lang_store_id);
                        if(isset($filter_container['filter_module'.$lang_store_id][$category_id])){
                            return $filter_container['filter_module'.$lang_store_id][$category_id];
                        }
                    }
                    /**
                    * opencart cache code end here
                    */
              
		$implode = array();

		$query = $this->db->query("SELECT filter_id FROM " . DB_PREFIX . "category_filter WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$implode[] = (int)$result['filter_id'];
		}

		$filter_group_data = array();

		if ($implode) {
			$filter_group_query = $this->db->query("SELECT DISTINCT f.filter_group_id, fgd.name, fg.sort_order FROM " . DB_PREFIX . "filter f LEFT JOIN " . DB_PREFIX . "filter_group fg ON (f.filter_group_id = fg.filter_group_id) LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (fg.filter_group_id = fgd.filter_group_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY f.filter_group_id ORDER BY fg.sort_order, LCASE(fgd.name)");

			foreach ($filter_group_query->rows as $filter_group) {
				$filter_data = array();

				$filter_query = $this->db->query("SELECT DISTINCT f.filter_id, fd.name FROM " . DB_PREFIX . "filter f LEFT JOIN " . DB_PREFIX . "filter_description fd ON (f.filter_id = fd.filter_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND f.filter_group_id = '" . (int)$filter_group['filter_group_id'] . "' AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY f.sort_order, LCASE(fd.name)");

				foreach ($filter_query->rows as $filter) {
					$filter_data[] = array(
						'filter_id' => $filter['filter_id'],
						'name'      => $filter['name']
					);
				}

				if ($filter_data) {
					$filter_group_data[] = array(
						'filter_group_id' => $filter_group['filter_group_id'],
						'name'            => $filter_group['name'],
						'filter'          => $filter_data
					);
				}
			}
		}


                /**
                 * opencart cache code start here
                 */
                 if($cache_filter_status){
                    $filter_container['filter_module'.$lang_store_id][$category_id] = $filter_group_data;
                    if($this->config->get('module_oc_cache_filter_module')['expire'])
                      $CachedString->set($filter_container)->expiresAfter($this->config->get('module_oc_cache_filter_module')['expire']);
                    else
                      $CachedString->set($filter_container);

                    self::$getCacheInstance->save($CachedString);
                  }
                /**
                * opencart cache code end here
                */
              
		return $filter_group_data;
	}

	public function getCategoryLayoutId($category_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return (int)$query->row['layout_id'];
		} else {
			return 0;
		}
	}

	public function getTotalCategoriesByCategoryId($parent_id = 0) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");

		return $query->row['total'];
	}

	public function getMenus($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "menu m LEFT JOIN " . DB_PREFIX . "menu_description md ON (m.menu_id = md.menu_id) WHERE m.parent_id = '" . (int)$parent_id . "' AND md.language_id = '" . (int)$this->config->get('config_language_id') . "'  AND m.status = '1' ORDER BY m.sort_order, LCASE(md.name)");

		return $query->rows;
	}

	public function getMenusHeader($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "menu_header m LEFT JOIN " . DB_PREFIX . "menu_header_description md ON (m.menu_id = md.menu_id) WHERE m.parent_id = '" . (int)$parent_id . "' AND md.language_id = '" . (int)$this->config->get('config_language_id') . "'  AND m.status = '1' ORDER BY m.sort_order, LCASE(md.name)");

		return $query->rows;
	}
}
