<?php
class ControllerAccountWishList extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/wishlist', '', true);

			$this->response->redirect($this->url->link('common/home', '', true));
		}

		$this->load->language('account/wishlist');

		$this->load->model('account/wishlist');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

        $data['logged'] = $this->customer->isLogged();

		if (isset($this->request->get['remove'])) {
			// Remove Wishlist
			$this->model_account_wishlist->deleteWishlist($this->request->get['remove']);

			$this->session->data['success'] = $this->language->get('text_remove');

			$this->response->redirect($this->url->link('account/wishlist'));
		}

		$this->document->setTitle($this->language->get('heading_title'));
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/wishlist')
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['products'] = array();
        $data['shopping'] = $this->url->link('product/category', 'path=59');
        $data['phone'] = $this->config->get('config_telephone');
        $data['phone2'] = $this->config->get('config_telephone2');

		$results = $this->model_account_wishlist->getWishlist();

		if ($results) {
            foreach ($results as $result) {



                $product_info = $this->model_catalog_product->getProduct($result['product_id']);

                if ($product_info) {
                    if ($product_info['image'] && file_exists(DIR_IMAGE.$product_info['image'])) {
                        $image = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_height'));
                    } else {
                        $image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_height'));

                    }



                        $customer_wishlist = $this->model_account_wishlist->getWishlistProductId($result['product_id']);

                        if(in_array($result['product_id'], $customer_wishlist)) {
                            $result['is_wishlist'] = true;
                        } else {
                            $result['is_wishlist'] = false;
                        }


                    if ($product_info['quantity'] <= 0) {
                        $stock = $product_info['stock_status'];
                    } elseif ($this->config->get('config_stock_display')) {
                        $stock = $product_info['quantity'];
                    } else {
                        $stock = $this->language->get('text_instock');
                    }

                    if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                        $price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    } else {
                        $price = false;
                    }

                    if ((float)$product_info['special']) {
                        $special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    } else {
                        $special = false;
                    }

                    $data['products'][] = array(
                        'product_id' => $product_info['product_id'],
                        'thumb'      => $image,
                        'is_wishlist'      =>  $result['is_wishlist'],
                        'name'       => $product_info['name'],
                        'model'      => $product_info['model'],
                        'stock'      => $stock,
                        'price'      => $price,
                        'special'    => $special,
                        'href'       => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
                        'remove'     => $this->url->link('account/wishlist', 'remove=' . $product_info['product_id'])
                    );
                } else {
                    $this->model_account_wishlist->deleteWishlist($result['product_id']);
                }
            }
        }

		$data['continue'] = $this->url->link('account/account', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/wishlist', $data));
	}

    public function add() {
        $this->load->language('account/wishlist');

        $json = array();

        if (isset($this->request->post['product_id'])) {
            $product_id = $this->request->post['product_id'];
        } else {
            $product_id = 0;
        }

        $this->load->model('catalog/product');

        $product_info = $this->model_catalog_product->getProduct($product_id);

        if ($product_info) {
            if ($this->customer->isLogged()) {
                $this->load->model('account/wishlist');
                $this->model_account_wishlist->addWishlist($this->request->post['product_id']);

                $json['success'] = true;
                $json['not_logged'] = false;
                $json['total'] = $this->model_account_wishlist->getTotalWishlist();

            } else {
                $json['not_logged'] = true;
                $json['success'] = false;
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    public function remove() {
        $json = array();

        if (isset($this->request->post['product_id'])) {
            $product_id = $this->request->post['product_id'];
        } else {
            $product_id = 0;
        }

        $this->load->model('catalog/product');

        if ($this->customer->isLogged()) {
            $this->load->model('account/wishlist');
            $this->model_account_wishlist->deleteWishlist($product_id);

            $this->session->data['success'] = $this->language->get('text_remove');

            $json['success'] = true;
            $json['not_logged'] = false;
            $json['total'] = $this->model_account_wishlist->getTotalWishlist();

        } else {
            $json['not_logged'] = true;
            $json['success'] = false;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}
