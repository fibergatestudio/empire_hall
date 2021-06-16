<?php
class ControllerExtensionModuleFeatured extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/featured');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['products'] = array();

		$data['all_offers'] = $this->url->link('product/category', 'path=1');

		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}
		$this->load->model( 'account/wishlist' );
        $data['logged'] = $this->customer->isLogged();

          /**
          * opencart cache code start here
          */
        $lang_store_id = '.'.(int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id');
          if($this->config->get('module_oc_cache_status') && (isset($this->config->get('module_oc_cache_featured')['status']) && $this->config->get('module_oc_cache_featured')['status'])){
          static  $product_container  = array();
                  $getCacheInstance   = $this->webkulcache->get_InstanceCache('files');
                  $CachedString       = $getCacheInstance->getItem('product_featured');
                  $product_container  = $CachedString->get('product_featured'.$lang_store_id);
          }
          /**
            * opencart cache code end here
            */
      
        if (!empty($setting['product'])) {
            $products = array_slice($setting['product'], 0, (int)$setting['limit']);

            if ($products) {

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
				$data['ee_type'] = 'module_featured';
				$ee_data = array('type' => $data['ee_type']);
				foreach ($products as $product_id) {
					$ee_data['products'][] = $product_id;
				}
				$data['ee_impression_data'] = json_encode($ee_data);
			}
			/** EET Module */
            
                foreach ($products as $product_id) {
                    
        /**
        * opencart cache code start here
        */
        if($this->config->get('module_oc_cache_status') && (isset($this->config->get('module_oc_cache_featured')['status']) && $this->config->get('module_oc_cache_featured')['status'])){
            if(!isset($product_container['product_featured'.$lang_store_id][$product_id])){
                $product_info  = $this->model_catalog_product->getProduct($product_id);
                $product_container['product_featured'.$lang_store_id][$product_info['product_id']] = $product_info;
                if($this->config->get('module_oc_cache_featured')['expire'])
                  $CachedString->set($product_container)->expiresAfter($this->config->get('module_oc_cache_featured')['expire']);
                else
                  $CachedString->set($product_container);

                $getCacheInstance->save($CachedString);
            }else{
              $product_info = $product_container['product_featured'.$lang_store_id][$product_id];
            }
        }else{
          $product_info   = $this->model_catalog_product->getProduct($product_id);
        }
        /**
        * opencart cache code end here
        */
      
                    if ($product_info) {
                        $customer_wishlist = $this->model_account_wishlist->getWishlistProductId($product_info['product_id']);

                        if(in_array($product_info['product_id'], $customer_wishlist)) {
                            $product_info['is_wishlist'] = true;
                        } else {
                            $product_info['is_wishlist'] = false;
                        }

                        $filter = array(
                            'product' => $product_info,
                            'width'   => $setting['width'],
                            'height'  => $setting['height']
                          );

                        $data['products'][] = $this->product->getProduct($filter);

                    }
                }
            }
        }

		return $this->load->view('extension/module/featured', $data);
	}
}