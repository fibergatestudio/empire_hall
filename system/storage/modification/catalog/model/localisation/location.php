<?php
class ModelLocalisationLocation extends Model {
	
                  private static $getCacheInstance = null;
                  public function getLocation($location_id) {
                     /**
                       * opencart cache code start here
                       */
                      $cache_store_location_status = false;
                      if($this->config->get('module_oc_cache_status') && (isset($this->config->get('module_oc_cache_store_location')['status']) && $this->config->get('module_oc_cache_store_location')['status'])){
                        $cache_store_location_status = true;
                          $lang_store_id = '.'.(int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id');

                          if (self::$getCacheInstance == null) {
                            self::$getCacheInstance = $this->webkulcache->get_InstanceCache('files');
                          }
                        $CachedString     = self::$getCacheInstance->getItem('store_location');
                        $store_location_container = $CachedString->get('store_location'.$lang_store_id);
                          if(isset($store_location_container['store_location'.$lang_store_id][$location_id])){
                              return $store_location_container['store_location'.$lang_store_id][$location_id];
                          }
                      }
                      /**
                      * opencart cache code end here
                      */
              
		$query = $this->db->query("SELECT location_id, name, address, geocode, telephone, fax, image, open, comment FROM " . DB_PREFIX . "location WHERE location_id = '" . (int)$location_id . "'");


                /**
                 * opencart cache code start here
                 */
                 if($cache_store_location_status){
                    $store_location_container['store_location'.$lang_store_id][$location_id] = $query->row;
                    if($this->config->get('module_oc_cache_store_location')['expire'])
                      $CachedString->set($store_location_container)->expiresAfter($this->config->get('module_oc_cache_store_location')['expire']);
                    else
                      $CachedString->set($store_location_container);

                    self::$getCacheInstance->save($CachedString);
                  }
                /**
                * opencart cache code end here
                */
              
		return $query->row;
	}
}