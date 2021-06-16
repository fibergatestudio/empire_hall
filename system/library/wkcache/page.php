<?php

namespace Wkcache;

require DIR_SYSTEM . '../vendor/autoload.php';
 
use phpFastCache\CacheManager;
use phpFastCache\Util\Directory;

class Page {

    public $cache;
    public $customer_id = 0;

    function __construct($registry) {
        $this->config = $registry->get('config');
        $this->session = $registry->get('session');
        $this->cart = $registry->get('cart');
        $this->db = $registry->get('db');
        $this->no_cache = false;
        
        if(isset($this->session->data['customer_id'])) {
            $this->customer_id = $this->session->data['customer_id'];
        }

        if($this->session->getId() && !$this->customer_id) {
            $result = $this->db->query("SELECT * FROM ".DB_PREFIX."cart WHERE session_id = '".$this->db->escape($this->session->getId())."' ")->row;
            if($result) {
                $this->no_cache = true;
            }
        }

        $foldername = 'pagecache';
        
        if($this->customer_id && $this->config->get('module_oc_cache_page_cache_on_login')) {
            $foldername = 'pagecache-'.$this->customer_id;
        }

        if(!file_exists(DIR_STORAGE."wkcache/".$foldername)) {
            @mkdir(DIR_STORAGE."wkcache/".$foldername);
        }

        CacheManager::setDefaultConfig([
            "path" => DIR_STORAGE . "wkcache/".$foldername
        ]);
        
        $this->pageCache = CacheManager::getInstance('files');
    }

    public function getConfig() {
        return $this->config;
    }

    public function clearCache() {
        $this->pageCache->clear();
    }

    public function getSize($path) {
        return Directory::dirSize($path);
    }

    public function set($key, $data) {
        if($this->config->get('module_oc_cache_status') && $this->config->get('module_oc_cache_page_cache_status')) {
            $numberOfSeconds = $this->config->get('module_oc_cache_page_cache_expiry') ? $this->config->get('module_oc_cache_page_cache_expiry') : 86400;
            $this->cache->set($data)->expiresAfter($numberOfSeconds);
            $this->pageCache->save($this->cache);
            return $this->cache->get();
        }
    }

    public function get($key) {
        $this->cache = $this->pageCache->getItem($key);
        return $this->cache;
    }

    public function delete($key) {
        $this->cache = $this->pageCache->delete($key);
        return $this->clean();
    }



}


?>