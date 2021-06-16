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

			return $this->load->view('extension/module/special', $data);
		}
	}
}