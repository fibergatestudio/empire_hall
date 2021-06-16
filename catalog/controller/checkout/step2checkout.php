<?php
class ControllerCheckoutStep2checkout extends Controller
{
    private $error = array();

    public function index()
	{
		
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Expires: " . date("r"));
		
		// Validate cart has products and has stock.
		if (!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) {
			$this->response->redirect($this->url->link('common/home'));
		}		

		$this->load->language('checkout/step2checkout');
		$this->load->model('checkout/step2checkout');
		$this->load->model('localisation/location');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('checkout/step2checkout', '', true)
		);
		
		$data['breadcrumbs_total'] = count($data['breadcrumbs']);
		$data['continue_shopping'] = $this->url->link('product/category', 'path=61');

		$data['check_logged'] = $this->customer->isLogged();
		
		// Users data
		if(isset($this->session->data['firstname'])) {
			$data['firstname'] = $this->session->data['firstname'];
		} else if ($this->customer->isLogged()) {
			$data['firstname'] = $this->customer->getFirstName();
		} else {
			$data['firstname'] = '';
		}
		
		if(isset($this->session->data['lastname'])) {
			$data['lastname'] = $this->session->data['lastname'];
		} else if ($this->customer->isLogged()) {
			$data['lastname'] = $this->customer->getLastName();
		} else {
			$data['lastname'] = '';
		}		
		
		if(isset($this->session->data['telephone'])) {
			$data['telephone'] = $this->session->data['telephone'];
		} else if ($this->customer->isLogged()) {
			$data['telephone'] = $this->customer->getTelephone();
		} else {
			$data['telephone'] = '';
		}	
		
		if(isset($this->session->data['email'])) {
			$data['email'] = $this->session->data['email'];
		} else if ($this->customer->isLogged()) {
			$data['email'] = $this->customer->getEmail();
		} else {
			$data['email'] = '';
		}	
		
		if(isset($this->session->data['zone_id'])) {
			$data['zone_id'] = $this->session->data['zone_id'];
		} else if ($this->customer->isLogged()) {
            $data['zone_id'] = 0;
		} else {
			$data['zone_id'] = 0;
		}

		$data['logged'] = $this->customer->isLogged();

		$data['countries'] = $this->model_checkout_step2checkout->getCountries();
		$data['zones'] = $this->model_checkout_step2checkout->getZones($this->config->get('config_country_id'));

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('checkout/step2checkout', $data));		
	}
	
	public function getCartProducts() {
		
		$this->load->model('tool/image');
		$this->load->model('tool/upload');
		$this->load->language('checkout/step2checkout');

		$data = array();
		$data['show_no_price'] = false;
		$my_total = 0;

		$data['count_products'] = $this->cart->countProducts();

		foreach ($this->cart->getProducts() as $product) {
			
			if ($product['image']&& is_file(DIR_IMAGE . $product['image'])) {
				$image = $this->model_tool_image->resize($product['image'], 170, 170);
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', 170, 170);
			}
			$option_data = array();
			
			$product_price = $product['price'];
			foreach ($product['option'] as $option) {
				if ($option['type'] != 'file') {
					$value = $option['value'];
				} else {
					$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);
					if ($upload_info) {
						$value = $upload_info['name'];
					} else {
						$value = '';
					}
				}
				$option_data[] = array(
					'name'  => $option['name'],
					'value' => $value,
					'type'  => $option['type']
				);

			}
			$my_total += $product_price * $product['quantity'];

			if ($my_total <= 0) {
                $data['show_no_price'] = true;
            }

			// Display prices
            if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$unit_price = $this->tax->calculate($product_price, $product['tax_class_id'], $this->config->get('config_tax'));
				
				$price = (float)$unit_price;
				$price = $this->currency->format($this->tax->calculate($price, $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				$total = (float)$unit_price * $product['quantity'];
				$total = $this->currency->format($this->tax->calculate($total, $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$price = false;
				$total = false;
			}
			
			$data['products'][] = array(
				'cart_id'   => $product['cart_id'],
				'thumb'     => $image,
				'name'      => $product['name'],
				'model'     => $product['model'],
				'option'    => $option_data,
				'recurring' => ($product['recurring'] ? $product['recurring']['name'] : ''),
				'quantity'  => $product['quantity'],
				'price'     => $price,
				'total'     => $total,
				'href'      => $this->url->link('product/product', 'product_id=' . $product['product_id'])
			);
		}	

		$order_data = array();

		$totals = array();
		$taxes = $this->cart->getTaxes();
		$total = 0;

		// Because __call can not keep var references so we put them into an array.
		$total_data = array(
			'totals' => &$totals,
			'taxes'  => &$taxes,
			'total'  => &$total
		);

		$this->load->model('setting/extension');

		$sort_order = array();

		$results = $this->model_setting_extension->getExtensions('total');

		foreach ($results as $key => $value) {
			$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
		}

		array_multisort($sort_order, SORT_ASC, $results);

		foreach ($results as $result) {
			if ($this->config->get('total_' . $result['code'] . '_status')) {
				$this->load->model('extension/total/' . $result['code']);

				// We have to put the totals in an array so that they pass by reference.
				$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
			}
		}

		$sort_order = array();

		foreach ($totals as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}

		array_multisort($sort_order, SORT_ASC, $totals);

		foreach ($totals as $total) {
            if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {

                if ($total['code'] == 'total') {
                    $data['totals'][] = array(
                        'title' => $total['title'],
                        'text' => $this->currency->format($total['value'], $this->session->data['currency'])
                    );
                }
            }
		}

		$this->response->setOutput($this->load->view('checkout/st2chcart', $data));		
	}
	
	// не только валидируем, но и создаем часть массива для закза
	public function validateStepFirst() {
		
		$json = array();
		$order_data = array();
		$this->load->model('checkout/step2checkout');
		$this->load->model('setting/extension');
		$this->load->model('account/customer');
		$this->load->model('checkout/order');

		// $this->request->post
		if ((utf8_strlen(trim($this->request->post['firstname'])) < 2) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$json['error']['firstname'] = 'error';
		}	
		if ((utf8_strlen(trim($this->request->post['lastname'])) < 2) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
			$json['error']['lastname'] = 'error';
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$json['error']['email'] = 'error';
		}

		if ((utf8_strlen($this->request->post['telephone']) < 8) || (utf8_strlen($this->request->post['telephone']) > 21) ) {
			$json['error']['telephone'] = 'error';
		}

		if (!$this->cart->hasProducts() || $this->cart->hasProducts() == '0') {
		    $json['no_products'] = true;
		    $json['no_products_href'] = $this->url->link('common/home');
        } else {
		    $json['no_products'] = false;
        }
		
		if (!isset($json['error']) && !$json['no_products']) {

			$totals = array();
			$taxes = $this->cart->getTaxes();
			$total = 0;

			// Because __call can not keep var references so we put them into an array.
			$total_data = array(
				'totals' => &$totals,
				'taxes'  => &$taxes,
				'total'  => &$total
			);

			$sort_order = array();

			$results = $this->model_setting_extension->getExtensions('total');
			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
			}

			array_multisort($sort_order, SORT_ASC, $results);

			foreach ($results as $result) {
				if ($this->config->get('total_' . $result['code'] . '_status')) {
					$this->load->model('extension/total/' . $result['code']);
					// We have to put the totals in an array so that they pass by reference.
					$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
				}
			}

			$sort_order = array();
			foreach ($totals as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $totals);

			$order_data['totals'] = $totals;
			$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
			$order_data['store_id'] = $this->config->get('config_store_id');
			$order_data['store_name'] = $this->config->get('config_name');
			$order_data['affiliate_id'] = 0;
			$order_data['commission'] = 0;
			$order_data['marketing_id'] = 0;
			$order_data['tracking'] = '';
			if ($order_data['store_id']) {
				$order_data['store_url'] = $this->config->get('config_url');
			} else {
				if ($this->request->server['HTTPS']) {
					$order_data['store_url'] = HTTPS_SERVER;
				} else {
					$order_data['store_url'] = HTTP_SERVER;
				}
			}

			if ($this->customer->isLogged()) {
				$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
				$order_data['customer_id'] = $this->customer->getId();
				$order_data['customer_group_id'] = $customer_info['customer_group_id'];
			} else {
				$order_data['customer_id'] = 0;
				$order_data['customer_group_id'] = 1;
			}
			
			$order_data['firstname'] = $this->request->post['firstname'];
			$order_data['lastname'] = $this->request->post['lastname'];
			$order_data['email'] = $this->request->post['email'];
			$order_data['telephone'] = $this->request->post['telephone'];

			$order_data['payment_firstname'] = $this->request->post['firstname'];
			$order_data['payment_lastname'] = $this->request->post['lastname'];
			$order_data['payment_company'] = '';
			$order_data['payment_address_1'] = '';
			$order_data['payment_address_2'] = '';
			$order_data['payment_city'] = '';
			$order_data['payment_postcode'] = '';
			$order_data['payment_zone'] = '';
			$order_data['payment_zone_id'] = '';
			$order_data['payment_country'] = '';
			$order_data['payment_country_id'] = '';
			$order_data['payment_address_format'] = '';
			$order_data['payment_custom_field'] = array();
			$order_data['payment_method'] = '';
			$order_data['payment_code'] = $this->request->post['payment_method'];
			$this->session->data['payment_address'] = array(
				'firstname'      => $this->request->post['firstname'],
				'lastname'       => $this->request->post['lastname'],
				'company'        => '',
				'address_1'      => '',
				'address_2'      => '',
				'postcode'       => '',
				'city'           => '',
				'zone_id'        => '',
				'zone'           => '',
				'zone_code'      => '',
				'country_id'     => '',
				'country'        => '',
				'iso_code_2'     => '',
				'iso_code_3'     => '',
				'address_format' => '',
				'custom_field'   => array()
			);			
			
			$order_data['shipping_company'] = '';
			$order_data['shipping_address_1'] = '';
			$order_data['shipping_address_2'] = '';
			$order_data['shipping_city'] = '';
			$order_data['shipping_postcode'] = '';	
			$order_data['shipping_address_format'] = '';
			$order_data['shipping_custom_field'] = array();
			$order_data['shipping_method'] = '';
			$order_data['shipping_code'] = $this->request->post['shipping_method'];

			if ($this->cart->hasShipping()) {
				$order_data['shipping_firstname'] = $this->request->post['firstname'];
				$order_data['shipping_lastname'] = $this->request->post['lastname'];
				$order_data['shipping_zone'] = '';
				$order_data['shipping_zone_id'] = '';
				$order_data['shipping_country'] = '';
				$order_data['shipping_country_id'] = '';
			} else {
				$order_data['shipping_firstname'] = '';
				$order_data['shipping_lastname'] = '';
				$order_data['shipping_zone'] = '';
				$order_data['shipping_zone_id'] = '';
				$order_data['shipping_country'] = '';
				$order_data['shipping_country_id'] = '';
			}

			$this->session->data['shipping_address'] = array(
				'firstname'      => $this->request->post['firstname'],
				'lastname'       => $this->request->post['lastname'],
				'company'        => '',
				'address_1'      => '',
				'address_2'      => '',
				'postcode'       => '',
				'city'           => '',
				'zone_id'        => '',
				'zone'           => '',
				'zone_code'      => '',
				'country_id'     => '',
				'country'        => '',
				'iso_code_2'     => '',
				'iso_code_3'     => '',
				'address_format' => '',
				'custom_field'   => array()				
			);			
			
			$order_data['products'] = array();
			foreach ($this->cart->getProducts() as $product) {
				$option_data = array();
				foreach ($product['option'] as $option) {
					$option_data[] = array(
						'product_option_id'       => $option['product_option_id'],
						'product_option_value_id' => $option['product_option_value_id'],
						'option_id'               => $option['option_id'],
						'option_value_id'         => $option['option_value_id'],
						'name'                    => $option['name'],
						'value'                   => $option['value'],
						'type'                    => $option['type']
					);
				}
				$order_data['products'][] = array(
					'product_id' => $product['product_id'],
					'name'       => $product['name'],
					'model'      => $product['model'],
					'option'     => $option_data,
					'download'   => $product['download'],
					'quantity'   => $product['quantity'],
					'subtract'   => $product['subtract'],
					'price'      => $product['price'],
					'total'      => $product['total'],
					'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
					'reward'     => $product['reward']
				);
			}
			
			// Gift Voucher
			$order_data['vouchers'] = array();
			if (isset($this->session->data['vouchers']) && !empty($this->session->data['vouchers'])) {
				foreach ($this->session->data['vouchers'] as $voucher) {
					$order_data['vouchers'][] = array(
						'description'      => $voucher['description'],
						'code'             => token(10),
						'to_name'          => $voucher['to_name'],
						'to_email'         => $voucher['to_email'],
						'from_name'        => $voucher['from_name'],
						'from_email'       => $voucher['from_email'],
						'voucher_theme_id' => $voucher['voucher_theme_id'],
						'message'          => $voucher['message'],
						'amount'           => $voucher['amount']
					);
				}
			}

			$order_data['comment'] = '';
			$order_data['total'] = $total_data['total'];

			$order_data['language_id'] = $this->config->get('config_language_id');
			$order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
			$order_data['currency_code'] = $this->session->data['currency'];
			$order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
			$order_data['ip'] = $this->request->server['REMOTE_ADDR'];		
			
			if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
				$order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
			} elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
				$order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
			} else {
				$order_data['forwarded_ip'] = '';
			}

			if (isset($this->request->server['HTTP_USER_AGENT'])) {
				$order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
			} else {
				$order_data['user_agent'] = '';
			}

			if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
				$order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
			} else {
				$order_data['accept_language'] = '';
			}

			$order_data['custom_field'] = array();
			
			if(!isset($this->session->data['order_id'])){
				$this->session->data['order_id'] = $this->model_checkout_order->addOrder($order_data);	
			} else {
				$this->model_checkout_order->editOrder($this->session->data['order_id'], $order_data);
			}
			
			$this->session->data['customer_id'] = $order_data['customer_id'];
			$this->session->data['customer_group_id'] = $order_data['customer_group_id'];
			$this->session->data['firstname'] = $order_data['firstname'];
			$this->session->data['lastname'] = $order_data['lastname'];
			$this->session->data['email'] = $order_data['email'];
			$this->session->data['telephone'] = $order_data['telephone'];
			
			$json['success'] = true;
            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 1);
			$json['redirect'] = $this->url->link('checkout/success');
		}
		
		$this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

	public function validateAddress() {

		$json = array();
		$order_data = array();
		$this->load->model('checkout/step2checkout');
		$this->load->model('setting/extension');
		$this->load->model('account/customer');
		$this->load->model('checkout/order');

		if (isset($this->request->post['city_ref'])) {
            if ($this->request->post['city_ref'] == '0') {
                $json['error']['city_ref'] = 'error';
            }
        }

		if (isset($this->request->post['warehouse_ref'])) {
            if ($this->request->post['warehouse_ref'] == '0') {
                $json['error']['warehouse_ref'] = 'error';
            }
        }

		if (isset($this->request->post['address_1'])) {
            if ((utf8_strlen(trim($this->request->post['address_1'])) < 2) || (utf8_strlen(trim($this->request->post['address_1'])) > 32)) {
                $json['error']['address_1'] = 'error';
            }
        }

		if (isset($this->request->post['city'])) {
            if ((utf8_strlen(trim($this->request->post['city'])) < 2) || (utf8_strlen(trim($this->request->post['city'])) > 32)) {
                $json['error']['city'] = 'error';
            }
        }

		if (isset($this->request->post['postcode'])) {
            if ((utf8_strlen(trim($this->request->post['postcode'])) < 2) || (utf8_strlen(trim($this->request->post['postcode'])) > 32)) {
                $json['error']['postcode'] = 'error';
            }
        }

		if (!isset($json['error'])) {
            if ((isset($this->request->post['city_ref'])) && (isset($this->request->post['warehouse_ref']))) {
                $order_data['check_np'] = true;
                $order_data['city_ref'] = $this->request->post['city_ref'];
                $order_data['warehouse_ref'] = $this->request->post['warehouse_ref'];
                $order_data['payment_address_1'] = $this->request->post['city_ref'] . ': ' . $this->request->post['warehouse_ref'];
            } else {
                $order_data['check_np'] = false;
                $order_data['payment_address_1'] = (isset($this->request->post['address_1'])) ? $this->request->post['address_1'] : '';
            }

			$order_data['payment_address_2'] = '';
			$order_data['payment_city'] = (isset($this->request->post['city'])) ? $this->request->post['city'] : '';
			$order_data['payment_postcode'] = (isset($this->request->post['postcode'])) ? $this->request->post['postcode'] : '';

            if ((isset($this->request->post['city_ref'])) && (isset($this->request->post['warehouse_ref']))) {
                $order_data['shipping_address_1'] = $this->request->post['city_ref'] . ': ' . $this->request->post['warehouse_ref'];
            } else {
                $order_data['shipping_address_1'] = (isset($this->request->post['address_1'])) ? $this->request->post['address_1'] : '';
            }

            $order_data['shipping_address_2'] = '';
			$order_data['shipping_city'] = (isset($this->request->post['city'])) ? $this->request->post['city'] : '';
			$order_data['shipping_postcode'] = (isset($this->request->post['postcode'])) ? $this->request->post['postcode'] : '';

			$order_data['language_id'] = $this->config->get('config_language_id');
			$order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
			$order_data['currency_code'] = $this->session->data['currency'];
			$order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
			$order_data['ip'] = $this->request->server['REMOTE_ADDR'];

			$order_data['custom_field'] = array();

			$this->model_checkout_order->editOrderAddress($this->session->data['order_id'], $order_data);

			$json['success'] = 'success';
		}

		$this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));

	}

	public function updateOrder() {
		
		$json = array();
		$this->load->model('checkout/step2checkout');
		
		if(isset($this->session->data['order_id']) && $this->session->data['order_id']){
			$this->model_checkout_step2checkout->editOrderMethods($this->session->data);
		} else {
			$json['redirect'] = $this->url->link('checkout/step2checkout', '', true);
		}
		
		$this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
	}
	
	public function savemessage() {
		
		$json = array();
		$this->load->model('checkout/step2checkout');
		
		if(isset($this->session->data['order_id']) && $this->session->data['order_id']){
			if(isset($this->request->post['message']) && $this->request->post['message']){
				$this->model_checkout_step2checkout->editOrderMessage($this->session->data['order_id'], $this->request->post['message']);
				$json['success'] = 'success';
			} else {
				$json['error'] = 'message';
			}
		} else {
			$json['error'] = 'order_id';
		}
		$this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));		
	}
	
	public function shippingMethodsFeelds() {
		
        $this->load->language('checkout/checkout');
        $this->load->language('checkout/step2checkout');
		$this->load->model('localisation/location');
		$this->load->model('checkout/step2checkout');
		$data = array();
		$json = array();

		$data['address_pickup'] = $this->config->get('config_address')[$this->config->get('config_language_id')];

		$tpl = 'all';

        if ($this->session->data['shipping_method']['code'] == 'NovaPoshta.NovaPoshta') {
        //прилетел выбранный город
            if (($this->session->data['zone_id'] == 3491) && !isset($this->request->post['cityref']) && !isset($this->request->post['warehouseref'])) {
                $this->session->data['shipping_address']['city_ref'] = '8d5a980d-391c-11dd-90d9-001a92567626';
            }

            if(isset($this->request->post['cityref'])){
                if($this->request->post['cityref']){
                    $this->session->data['shipping_address']['city_ref'] = $this->request->post['cityref'];
                    unset($this->session->data['shipping_address']['warehouse_ref']);
                } else {
                    unset($this->session->data['shipping_address']['city_ref']);
                    unset($this->session->data['shipping_address']['warehouse_ref']);
                }
                $this->model_checkout_step2checkout->editOrderNPost($this->session->data);
            }

            if (isset($this->request->post['warehouseref'])){
                if($this->request->post['warehouseref']){
                    $this->session->data['shipping_address']['warehouse_ref'] = $this->request->post['warehouseref'];
                } else {
                    unset($this->session->data['shipping_address']['warehouse_ref']);
                }
                $this->model_checkout_step2checkout->editOrderNPost($this->session->data);
            }

            if (isset($this->session->data['shipping_method']['code']) && $this->session->data['shipping_method']['code']=='NovaPoshta.NovaPoshta') {
                $tpl = 'npost';
                //получим список городов
                $zone_ref = $this->model_localisation_location->getZoneRefById($this->session->data['zone_id']);
                $data['cities'] = $this->model_localisation_location->getCities($zone_ref);

                if(isset($this->session->data['shipping_address']['city_ref']) && $this->session->data['shipping_address']['city_ref']){
                    $data['city_ref'] = $this->session->data['shipping_address']['city_ref'];
                    //уже выбран город, значит выберем склады
                    $data['warehousies'] = $this->model_localisation_location->getWarehouseByCity($data['city_ref']);
                    if(isset($this->session->data['shipping_address']['warehouse_ref']) && $this->session->data['shipping_address']['warehouse_ref']){
                        $data['warehouse_ref'] = $this->session->data['shipping_address']['warehouse_ref'];
                    }
                }
            }
        } else if ($this->session->data['shipping_method']['code'] == 'item.item') {
            $tpl = 'courier_kiev';
        } else if ($this->session->data['shipping_method']['code'] == 'flat.flat') {
            $tpl = 'post_russia';
        } else if ($this->session->data['shipping_method']['code'] == 'pickup.pickup') {
            $tpl = 'pickup';
        } else {
            $tpl = 'all';
        }
        
        $this->template = 'checkout/shippingfeelds/' . $tpl;
		$json['fields'] = $this->load->view($this->template, $data);

		$this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
	}

    public function login() {

        $json = array();

        if ( $this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateLogin($this->request->post) ) {
            unset($this->session->data['guest']);

            $this->customer->login($this->request->post['auth_email'], $this->request->post['auth_password'], true);
            $json['success'] = true;
            $json['authorize'] = $this->url->link('checkout/step2checkout', '', true);
        }

        if ( isset($this->error['auth_email']) ) {
            $json['error']['auth_email'] = $this->error['auth_email'];
            $json['error']['auth_password'] = $this->error['auth_email'];
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    protected function validateLogin() {

        $this->load->model('account/customer');
        $this->load->language('checkout/step2checkout');

        $login_info = $this->model_account_customer->getLoginAttempts($this->request->post['auth_email']);

        // Check if customer has been approved.
        $customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['auth_email']);

        if ($customer_info && !$customer_info['status']) {
            $this->error['auth_email'] = $this->language->get('error_approved');
        }

        if (!$this->error) {
            if (!$this->customer->login($this->request->post['auth_email'], $this->request->post['auth_password'])) {
                $this->error['auth_email'] = $this->language->get('error_login');

                $this->model_account_customer->addLoginAttempt($this->request->post['auth_email']);
            } else {
                $this->model_account_customer->deleteLoginAttempts($this->request->post['auth_email']);
            }
        }

        return !$this->error;
    }
}