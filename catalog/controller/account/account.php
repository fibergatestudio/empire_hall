<?php
class ControllerAccountAccount extends Controller {
    private $error = array();

	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', true);
			$this->response->redirect($this->url->link('common/home', '', true));
		}

		$this->load->language('account/account');
		$this->load->model('account/customer');
		$this->load->model('localisation/zone');

		$this->document->setTitle($this->language->get('text_account'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_account_customer->editCustomer($this->customer->getId(), $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('account/account', '', true));
        }

        if (isset($this->error['firstname'])) {
            $data['error_firstname'] = $this->error['firstname'];
        } else {
            $data['error_firstname'] = '';
        }

        if (isset($this->error['lastname'])) {
            $data['error_lastname'] = $this->error['lastname'];
        } else {
            $data['error_lastname'] = '';
        }

        if (isset($this->error['email'])) {
            $data['error_email'] = $this->error['email'];
        } else {
            $data['error_email'] = '';
        }

        if (isset($this->error['telephone'])) {
            $data['error_telephone'] = $this->error['telephone'];
        } else {
            $data['error_telephone'] = '';
        }

        $data['action'] = $this->url->link('account/account', '', true);

		$data['regions'] = $this->model_localisation_zone->getZonesByCountryId($this->config->get('config_country_id'));

        if ($this->request->server['REQUEST_METHOD'] != 'POST') {
            $customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
        }

        if (isset($this->request->post['firstname'])) {
            $data['firstname'] = $this->request->post['firstname'];
        } elseif (!empty($customer_info)) {
            $data['firstname'] = $customer_info['firstname'];
        } else {
            $data['firstname'] = '';
        }

        if (isset($this->request->post['lastname'])) {
            $data['lastname'] = $this->request->post['lastname'];
        } elseif (!empty($customer_info)) {
            $data['lastname'] = $customer_info['lastname'];
        } else {
            $data['lastname'] = '';
        }
        if (isset($this->request->post['date'])) {
            $data['day_birthday'] = $this->request->post['date'];
        } elseif (!empty($customer_info)) {
            $data['day_birthday'] = $customer_info['birthday'];
        } else {
            $data['day_birthday'] = '';
        }

        if (isset($this->request->post['email'])) {
            $data['email'] = $this->request->post['email'];
        } elseif (!empty($customer_info)) {
            $data['email'] = $customer_info['email'];
        } else {
            $data['email'] = '';
        }

        if (isset($this->request->post['telephone'])) {
            $data['telephone'] = $this->request->post['telephone'];
        } elseif (!empty($customer_info)) {
            $data['telephone'] = $customer_info['telephone'];
        } else {
            $data['telephone'] = '';
        }

        if (isset($this->request->post['zone_id'])) {
            $data['zone_id'] = $this->request->post['zone_id'];
        } elseif (!empty($customer_info)) {
            $data['zone_id'] = $customer_info['zone_id'];
        } else {
            $data['zone_id'] = '';
        }

        if (isset($this->request->post['city_id'])) {
            $data['city_id'] = $this->request->post['city_id'];
        } elseif (!empty($customer_info)) {
            $data['city_id'] = $customer_info['city_id'];
        } else {
            $data['city_id'] = '';
        }

        if (isset($this->request->post['street'])) {
            $data['street'] = $this->request->post['street'];
        } elseif (!empty($customer_info)) {
            $data['street'] = $customer_info['street'];
        } else {
            $data['street'] = '';
        }

        if (isset($this->request->post['house'])) {
            $data['house'] = $this->request->post['house'];
        } elseif (!empty($customer_info)) {
            $data['house'] = $customer_info['house'];
        } else {
            $data['house'] = '';
        }

        if (isset($this->request->post['room'])) {
            $data['room'] = $this->request->post['room'];
        } elseif (!empty($customer_info)) {
            $data['room'] = $customer_info['room'];
        } else {
            $data['room'] = '';
        }
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		
		$this->response->setOutput($this->load->view('account/account', $data));
	}

    protected function validate() {
        $this->load->language('account/edit');

        if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
            $this->error['firstname'] = $this->language->get('error_firstname');
        }

        if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
            $this->error['lastname'] = $this->language->get('error_lastname');
        }

        if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error['email'] = $this->language->get('error_email');
        }

        if (($this->customer->getEmail() != $this->request->post['email']) && $this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
            $this->error['warning'] = $this->language->get('error_exists');
        }

        if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
            $this->error['telephone'] = $this->language->get('error_telephone');
        }

        return !$this->error;
    }

    public function getCities() {
        $this->load->model('localisation/zone');

        $json = array();

        $json['cities'] = $this->model_localisation_zone->getCitiesByZoneId($this->request->post['area']);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
