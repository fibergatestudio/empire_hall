<?php
class ControllerExtensionModuleLatest extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/latest');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['products'] = array();

		$filter_data = array(
			'sort'  => 'p.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => $setting['limit']
		);
		
		$module_products = array();

		
        /**
        * opencart cache code start here
        */
        $lang_store_id = '.'.(int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id');
        if($this->config->get('module_oc_cache_status') && (isset($this->config->get('module_oc_cache_latest')['status']) && $this->config->get('module_oc_cache_latest')['status'])){
          static  $product_container  = array();
          $getCacheInstance   = $this->webkulcache->get_InstanceCache('files');
          $CachedString       = $getCacheInstance->getItem('product_latest');
          $product_container  = $CachedString->get('product_latest'.$lang_store_id);
          if(!isset($product_container['product_latest'.$lang_store_id])){
            $results = $this->model_catalog_product->getProducts($filter_data);
            $product_container['product_latest'.$lang_store_id] = $results;
            if($this->config->get('module_oc_cache_latest')['expire'])
              $CachedString->set($product_container)->expiresAfter($this->config->get('module_oc_cache_latest')['expire']);
            else
              $CachedString->set($product_container);

            $getCacheInstance->save($CachedString);
          } else {
            $results = $product_container['product_latest'.$lang_store_id];
          }
        } else {
          $results = $this->model_catalog_product->getProducts($filter_data);
        }
        /**
        * opencart cache code end here
        */
      

		if ($results) {

            /** EET Module */
			$ee_position = 1;
			$data['ee_tracking'] = $this->config->get('module_ee_tracking_status');
			if ($data['ee_tracking']) {
				$data['ee_impression'] = $this->config->get('module_ee_tracking_impression_status');
				$data['ee_impression_log'] = $this->config->get('module_ee_tracking_log') ? $this->config->get('module_ee_tracking_impression_log') : false;
				$data['ee_click'] = $this->config->get('module_ee_tracking_click_status');
				$data['ee_cart'] = $this->config->get('module_ee_tracking_cart_status');
				$data['ee_ga_callback'] = $this->config->get('module_ee_tracking_advanced_settings') ? $this->config->get('module_ee_tracking_ga_callback') : 0;
				$data['ee_generate_cid'] = $this->config->get('module_ee_tracking_advanced_settings') ? $this->config->get('module_ee_tracking_generate_cid') : 0;
				$ee_class_array = preg_split('/(?=[A-Z])/', get_class($this));
				$ee_class_name = array_pop($ee_class_array);
				$data['ee_type'] = 'module_' . strtolower($ee_class_name);
				$ee_data = array('type' => $data['ee_type']);
				foreach ($results as $result) {
					$ee_data['products'][] = $result['product_id'];
				}
				$data['ee_impression_data'] = json_encode($ee_data);
			}
			/** EET Module */
            
			foreach ($results as $result) {
				$product_info = $this->model_catalog_product->getProduct($result['product_id']);

				if ($product_info) {
					$module_products[] = $product_info;
				}
			}
			
			$data['product_listing'] = $this->load->controller('product/product_listing', $module_products);

			return $this->load->view('extension/module/latest', $data);
		}
	}
}
