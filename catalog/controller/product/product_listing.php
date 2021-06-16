<?php
class ControllerProductProductListing extends Controller {
	public function index($results) {
		$this->load->language('product/product');

		$this->load->model('catalog/category');
		$this->load->model('catalog/product');

		// это индикатор страницы Желаемого в ЛК. там может немного отличаться шаблон превью товара (кнопка удаления из избранного и т.п.)
		if (isset($this->request->get['route']) && $this->request->get['route'] == 'account/wishlist') {
			$data['wishlist_page'] = true;
		} else {
			$data['wishlist_page'] = false;
		}

		$data['logged'] = $this->customer->isLogged();

		$data['text_brand'] = $this->language->get('text_brand');
		$data['text_tax'] = $this->language->get('text_tax');

		$data['products'] = array();
        $this->load->model( 'account/wishlist' );
		foreach ($results as $result) {

            $customer_wishlist = $this->model_account_wishlist->getWishlistProductId($result['product_id']);

            if(in_array($result['product_id'], $customer_wishlist)) {
                $result['is_wishlist'] = true;
            } else {
                $result['is_wishlist'] = false;
            }

			if ($result['image'] && is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
            } else {
				$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
            }

			// $price_array и $special_array понадобятся если по верстке нужно разделить само число и валюту
			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				$price_array = explode(' ', $price);
			} else {
				$price = false;
				$price_array = array();
			}

			if ((float)$result['special']) {
				$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				$special_array = explode(' ', $special);
			} else {
				$special = false;
				$special_array = array();
			}

			if ($this->config->get('config_tax')) {
				$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
			} else {
				$tax = false;
			}
			
			// Description
			$description = utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..';

			// Reviews
			$reviews = sprintf($this->language->get('text_reviews'), (int)$result['reviews']);

			$rating = (int)$result['rating'];

			// href, category
			$main_category_product = $this->model_catalog_product->getMainCategoryByProduct($result['product_id']);

			if ($main_category_product) {
				$path = $this->model_catalog_category->getCategoryPath($main_category_product['category_id']);

				$href = $this->url->link('product/product', 'path=' . $path . '&product_id=' . $result['product_id']);

				$category_name = $main_category_product['name'];
			} else {
				$href = $this->url->link('product/product', 'product_id=' . $result['product_id']);

				$category_name = '';
			}
			// end href

			// Promo
			$date_format = $this->language->get('date_format_short');

			$promo = array();

			$product_promotags = $this->model_catalog_product->getProductPromo($result['product_id']);

			foreach ($product_promotags as $product_promo) {
				$promotags = $this->model_catalog_product->getPromo($product_promo['promo_tags_id']);

				$promo[] = array(
					'id' => $promotags['promo_tags_id'],
					'class' => $promotags['class'],
					'text' => $promotags['promo_text'],
					'date_start' => ($date_format ? date($date_format, strtotime($product_promo['date_start'])) : $product_promo['date_start']),
					'date_end' => ($date_format ? date($date_format, strtotime($product_promo['date_end'])) : $product_promo['date_end']),
				);
			}
			// end Promo

			// Stock (stock_class и stock_svg могут понадобиться для верстки - подставить свои)
			if ($result['quantity'] <= 0) {
				$stock = $result['stock_status'];
				$stock_class = 'not-available';
				$stock_svg = 'close';
				$is_stock = false;
			} else {
				$stock = $this->language->get('text_instock');
				$stock_class = 'in-stock';
				$stock_svg = 'check';
				$is_stock = true;
			}
			// end Stock

			// Wishlist
			$this->load->model('account/wishlist');

			$is_wishlist = false;
			$button_wishlist = $this->language->get('button_wishlist');
			$remove_wishlist = $this->url->link('account/wishlist', 'remove=' . $result['product_id']);
			
			if ($this->customer->isLogged()) {
				$customer_wishlist = $this->model_account_wishlist->getWishlist();
				foreach($customer_wishlist as $item) {
					if($item['product_id'] == $result['product_id']) {
						$is_wishlist = true;
						$button_wishlist = $this->language->get('button_wishlist_already');
					}
				}
			} else {
				if(isset($this->session->data['wishlist'])){
					foreach($this->session->data['wishlist'] as $product_wishlist_id){
						if($product_wishlist_id == $result['product_id']) {
							$is_wishlist = true;
							$button_wishlist = $this->language->get('button_wishlist_already');
						}
					}
				}
			}
			// end Wishlist

			// Compare
			$is_compare = false;
			$button_compare = $this->language->get('button_compare');
			$remove_compare = $this->url->link('product/compare', 'remove=' . $result['product_id']);

			if(isset($this->session->data['compare'])){
				foreach($this->session->data['compare'] as $product_compare_id){
					if($product_compare_id == $result['product_id']) {
						$is_compare = true;
						$button_compare = $this->language->get('button_compare_already');
					}
				}
			}
			// end Compare

			// Attributes
			$attributes = array();

			$attribute_groups = $this->model_catalog_product->getProductAttributes($result['product_id']);

			foreach ($attribute_groups as $attribute_group) {
				foreach ($attribute_group['attribute'] as $attribute) {
					$attributes[] = array(
						'name' => $attribute['name'],
						'text' => $attribute['text'],
					);
				}
			}
			// end Main Attributes

            $data['products'][] = array(
                'product_id'  		=> $result['product_id'],
                'thumb'       		=> $image,
                'name'        		=> $result['name'],
                'model'       		=> $result['model'],
                'manufacturer'		=> $result['manufacturer'],
                'collection'		=> $result['collection'],
                'collection_href'		=> $result['collection_href'],
                'manufacturer_href'		=> $result['manufacturer_href'],
                'description'		=> $description,
                'quantity'		    => $result['quantity'],
                'price'       		=> $price,
                'price_array' 		=> $price_array,
                'special'     		=> $special,
                'special_array' 	=> $special_array,
                'tax'         		=> $tax,
                'minimum'     		=> $result['minimum'] > 0 ? $result['minimum'] : 1,
                'rating'      		=> $rating,
                'reviews'     		=> $reviews,
                'href'        		=> $href,
                'category_name' 	=> $category_name,
                'promo'       		=> $promo,
                'stock'       		=> $stock,
                'stock_class' 		=> $stock_class,
                'stock_svg'			=> $stock_svg,
                'is_stock'    		=> $is_stock,
                'is_wishlist'     	=> $is_wishlist,
                'button_wishlist' 	=> $button_wishlist,
                'remove_wishlist' 	=> $remove_wishlist,
                'is_compare'      	=> $is_compare,
                'button_compare'  	=> $button_compare,
                'remove_compare'  	=> $remove_compare,
                'attributes'      	=> $attributes,
            );

		}
//		echo "<pre>";
//		    print_r($data['products']);
//		echo "</pre>";

		return $this->load->view('product/product_listing', $data);
	}
}
