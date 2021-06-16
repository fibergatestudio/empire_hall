<?php
class ControllerExtensionModuleLastview extends Controller {
    public function index($setting)
    {
        if (isset($this->session->data['products_id'])) {
            if (!in_array($this->request->get['product_id'], $this->session->data['products_id'])) {
                $this->session->data['products_id'][] = $this->request->get['product_id'];
            }
        } else {
            $this->session->data['products_id'][] = $this->request->get['product_id'];
        }

        $this->load->language('extension/module/lastview');

        $data['cart'] = $this->url->link('checkout/cart');
        $data['wishlist'] = $this->url->link('account/wishlist');
        $data['compare'] = $this->url->link('product/compare');

        $this->load->model('catalog/product');

        $this->load->model('tool/image');
        
        $module_products = array();

        if(isset($this->session->data['products_id']))
            $this->session->data['products_id'] = array_slice($this->session->data['products_id'], -$setting['limit']);

        if (isset($this->session->data['products_id'])) {
            foreach ($this->session->data['products_id'] as $result_id) {
                /* not used
                $result = $this->model_catalog_product->getProduct($result_id);

                $filter = array(
                    'product' => $result,
                    'width'   => $setting['width'],
                    'height'  => $setting['height']
                );
                $data['products'][] = $this->product->getProduct($filter);
                */

                $product_info = $this->model_catalog_product->getProduct($result_id);

                if ($product_info) {
                    $module_products[] = $product_info;
                }
            }
        }
        
        $data['product_listing'] = $this->load->controller('product/product_listing', $module_products);

        return $this->load->view('extension/module/lastview', $data);
    }
}