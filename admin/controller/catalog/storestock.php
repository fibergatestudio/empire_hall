<?php
class ControllerCatalogStorestock extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/storestock');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/storestock');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/storestock');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/storestock');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_storestock->addStorestock($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/storestock', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/storestock');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/storestock');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_storestock->editStorestock($this->request->get['sstock_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/storestock', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/storestock');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/storestock');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $sstock_id) {
				$this->model_catalog_storestock->deleteStorestock($sstock_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/storestock', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'ssd.store_name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/storestock', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/storestock/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/storestock/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['storestocks'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$storestock_total = $this->model_catalog_storestock->getTotalStorestocks();

		$results = $this->model_catalog_storestock->getStorestocks($filter_data);

		foreach ($results as $result) {
			$data['storestocks'][] = array(
				'sstock_id'		  => $result['sstock_id'],
				'store_name'      => $result['store_name'],
				'sscity'		  => $this->model_catalog_storestock->getStorestockCity($result['sscity_id']),
				'sort_order'      => $result['sort_order'],
				'edit'            => $this->url->link('catalog/storestock/edit', 'user_token=' . $this->session->data['user_token'] . '&sstock_id=' . $result['sstock_id'] . $url, true)
			);
		}
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('catalog/storestock', 'user_token=' . $this->session->data['user_token'] . '&sort=ssd.store_name' . $url, true);
		$data['sort_city'] = $this->url->link('catalog/storestock', 'user_token=' . $this->session->data['user_token'] . '&sort=ss.sscity_id' . $url, true);
		$data['sort_sort_order'] = $this->url->link('catalog/storestock', 'user_token=' . $this->session->data['user_token'] . '&sort=ss.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $storestock_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/storestock', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($storestock_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($storestock_total - $this->config->get('config_limit_admin'))) ? $storestock_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $storestock_total, ceil($storestock_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/storestock_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['sstock_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		if (isset($this->error['attribute_group'])) {
			$data['error_attribute_group'] = $this->error['attribute_group'];
		} else {
			$data['error_attribute_group'] = '';
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/storestock', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['sstock_id'])) {
			$data['action'] = $this->url->link('catalog/storestock/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/storestock/edit', 'user_token=' . $this->session->data['user_token'] . '&sstock_id=' . $this->request->get['sstock_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/storestock', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['sstock_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$storestock_info = $this->model_catalog_storestock->getStorestock($this->request->get['sstock_id']);
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['storestock_description'])) {
			$data['storestock_description'] = $this->request->post['storestock_description'];
		} elseif (isset($this->request->get['sstock_id'])) {
			$data['storestock_description'] = $this->model_catalog_storestock->getStorestockDescriptions($this->request->get['sstock_id']);
		} else {
			$data['storestock_description'] = array();
		}

		$this->load->model('catalog/storestockcity');

		$data['cities'] = $this->model_catalog_storestockcity->getStorestockcity();

		if (isset($this->request->post['sort_order'])) {
			$data['sscity_id'] = $this->request->post['sscity_id'];
		} elseif (!empty($storestock_info)) {
			$data['sscity_id'] = $storestock_info['sscity_id'];
		} else {
			$data['sscity_id'] = 0;
		}
		
		if (isset($this->request->post['shipping'])) {
			$data['shipping'] = $this->request->post['shipping'];
		} elseif (!empty($storestock_info)) {
			$data['shipping'] = $storestock_info['shipping'];
		} else {
			$data['shipping'] = 0;
		}
		
		if (isset($this->request->post['shipping'])) {
			$data['shipping'] = $this->request->post['shipping'];
		} elseif (!empty($storestock_info)) {
			$data['shipping'] = $storestock_info['shipping'];
		} else {
			$data['shipping'] = 0;
		}
		
		if (isset($this->request->post['phone1'])) {
			$data['phone1'] = $this->request->post['phone1'];
		} elseif (!empty($storestock_info)) {
			$data['phone1'] = $storestock_info['phone1'];
		} else {
			$data['phone1'] = '';
		}
		
		if (isset($this->request->post['phone2'])) {
			$data['phone2'] = $this->request->post['phone2'];
		} elseif (!empty($storestock_info)) {
			$data['phone2'] = $storestock_info['phone2'];
		} else {
			$data['phone2'] = '';
		}
		
		if (isset($this->request->post['link1'])) {
			$data['link1'] = $this->request->post['link1'];
		} elseif (!empty($storestock_info)) {
			$data['link1'] = $storestock_info['link1'];
		} else {
			$data['link1'] = '';
		}
		
		if (isset($this->request->post['link2'])) {
			$data['link2'] = $this->request->post['link2'];
		} elseif (!empty($storestock_info)) {
			$data['link2'] = $storestock_info['link2'];
		} else {
			$data['link2'] = '';
		}
		
		if (isset($this->request->post['point_x'])) {
			$data['point_x'] = $this->request->post['point_x'];
		} elseif (!empty($storestock_info)) {
			$data['point_x'] = $storestock_info['point_x'];
		} else {
			$data['point_x'] = '';
		}
		
		if (isset($this->request->post['point_y'])) {
			$data['point_y'] = $this->request->post['point_y'];
		} elseif (!empty($storestock_info)) {
			$data['point_y'] = $storestock_info['point_y'];
		} else {
			$data['point_y'] = '';
		}		

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($storestock_info)) {
			$data['sort_order'] = $storestock_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/storestock_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/storestock')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['storestock_description'] as $language_id => $value) {
			if ((utf8_strlen($value['store_name']) < 1) || (utf8_strlen($value['store_name']) > 64)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/storestock');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_catalog_storestock->getAttributes($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'sstock_id'    => $result['sstock_id'],
					'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'attribute_group' => $result['attribute_group']
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/storestock')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('catalog/product');

		return !$this->error;
	}	
}
