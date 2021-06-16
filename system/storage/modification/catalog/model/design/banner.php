<?php
class ModelDesignBanner extends Model {
	
                  private static $getCacheInstance = null;
                  public function getBanner($banner_id) {
                    /**
                     * opencart cache code start here
                     */
                    $cache_banner_status = false;
                    if($this->config->get('module_oc_cache_status') && (isset($this->config->get('module_oc_cache_banner_module')['status']) && $this->config->get('module_oc_cache_banner_module')['status'])){
                      $cache_banner_status = true;
                        $lang_store_id = '.'.(int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id');

                        if (self::$getCacheInstance == null) {
                          self::$getCacheInstance = $this->webkulcache->get_InstanceCache('files');
                        }
                      $CachedString     = self::$getCacheInstance->getItem('banner_design');
                      $banner_container = $CachedString->get('banner_design'.$lang_store_id);
                        if(isset($banner_container['banner_design'.$lang_store_id][$banner_id])){
                            return $banner_container['banner_design'.$lang_store_id][$banner_id];
                        }
                    }
                    /**
                    * opencart cache code end here
                    */
              
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "banner b LEFT JOIN " . DB_PREFIX . "banner_image bi ON (b.banner_id = bi.banner_id) WHERE b.banner_id = '" . (int)$banner_id . "' AND b.status = '1' AND bi.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY bi.sort_order ASC");

                 /**
                 * opencart cache code start here
                 */
                 if($cache_banner_status){
                    $banner_container['banner_design'.$lang_store_id][$banner_id] = $query->rows;
                    if($this->config->get('module_oc_cache_banner_module')['expire'])
                      $CachedString->set($banner_container)->expiresAfter($this->config->get('module_oc_cache_banner_module')['expire']);
                    else
                      $CachedString->set($banner_container);

                    self::$getCacheInstance->save($CachedString);
                  }
                /**
                * opencart cache code end here
                */
              

        if ($this->config->get('mlseo_banners')) {
          foreach($query->rows as &$row) {
            if ($row['link'] && strstr($row['link'], 'http') === false) {
              $route = $row['link'];
              
              if ($params = strstr($row['link'], '&')) {
                $route = str_replace(array($params, 'index.php?route='), '', $row['link']);
              } else {
                $params = '';
              }
              
              $row['link'] = $this->url->link($route, str_replace('&amp;', '&', $params));
            }
          }
        }
       
		return $query->rows;
	}
}
