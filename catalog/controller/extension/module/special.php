<?php
class ControllerExtensionModuleSpecial extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/special');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['products'] = array();

		$filter_data = array(
			'sort'  => 'pd.name',
			'order' => 'ASC',
			'start' => 0,
			'limit' => $setting['limit']
		);
		
		$module_products = array();

		$results = $this->model_catalog_product->getProductSpecials($filter_data);

		if ($results) {
			foreach ($results as $result) {
				$product_info = $this->model_catalog_product->getProduct($result['product_id']);

				if ($product_info) {
					$module_products[] = $product_info;
				}
			}
			
			$data['product_listing'] = $this->load->controller('product/product_listing', $module_products);

			return $this->load->view('extension/module/special', $data);
		}
	}
}