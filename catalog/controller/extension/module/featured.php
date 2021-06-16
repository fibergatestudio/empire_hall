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
        if (!empty($setting['product'])) {
            $products = array_slice($setting['product'], 0, (int)$setting['limit']);

            if ($products) {
                foreach ($products as $product_id) {
                    $product_info = $this->model_catalog_product->getProduct($product_id);
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