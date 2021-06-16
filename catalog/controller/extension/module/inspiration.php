<?php
class ControllerExtensionModuleInspiration extends Controller {
    public function index() {

        $this->load->language('extension/module/inspiration');

        $curr_lang =  $this->language->get('code');

        
        $data['inspiration_link'] = $this->url->link('product/inspiration');

		$data['inspirations'] = $this->cache->get('extension.module.inspiration.inspirations.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));
		
		if (!$data['inspirations']) {
			
			$this->load->model('catalog/inspiration');
			$this->load->model('catalog/product');
			$this->load->model('tool/image');

           
			$all_inspirations = $this->model_catalog_inspiration->getInspirations();

			if ($all_inspirations) {
				foreach ($all_inspirations as $inspiration) {

					if (is_file(DIR_IMAGE . $inspiration['image'])) {
						$image = $this->model_tool_image->resize($inspiration['image'], 895, 604);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', 895, 604);
					}

					$products = array();

					$results = $this->model_catalog_product->getProductInspiration($inspiration['inspiration_id']);


					foreach ($results as $result) {
						$filter = array(
							'product' => $result,
							'width'   => $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'),
							'height'  => $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height')
						);

						$products[] = $this->product->getProduct($filter);

					}

					$data['inspirations'][] = array(
						'id'        => $inspiration['inspiration_id'],
						'image'     => $image,
						'products'  => $products,
						'name'      => $inspiration['name']
					);
				}
				
				$this->cache->set('extension.module.inspiration.inspirations.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $data['inspirations']);
			}
		
		}
		

        return $this->load->view('extension/module/inspiration', $data);
    }
}