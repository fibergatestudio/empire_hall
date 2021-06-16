<?php
class ControllerExtensionModuleBestSeller extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/bestseller');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

        $curr_lang =  $this->language->get('code');

		$data['products'] = array();
		
		$module_products = array();


        if($this->cache->get('home.bestseller'."_".$curr_lang)){
            $results = $this->cache->get('home.bestseller'."_".$curr_lang);
        }else{
            $results = $this->model_catalog_product->getBestSellerProducts($setting['limit']);
            $this->cache->set('home.bestseller'."_".$curr_lang, $results);
        }

		if ($results) {
			foreach ($results as $result) {
				$product_info = $this->model_catalog_product->getProduct($result['product_id']);

				if ($product_info) {
					$module_products[] = $product_info;
				}
			}

			$data['product_listing'] = $this->load->controller('product/product_listing', $module_products);

			return $this->load->view('extension/module/bestseller', $data);
		}
	}
}
