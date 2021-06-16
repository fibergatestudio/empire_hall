<?php
class ControllerCatalogStorestockcity extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/storestockcity');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/storestockcity');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/storestockcity');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/storestockcity');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_storestockcity->addStorestockcity($this->request->post);

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

			$this->response->redirect($this->url->link('catalog/storestockcity', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/storestockcity')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['storestockcity_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 64)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}

		return !$this->error;
	}
	
	public function delete() {
		$this->load->language('catalog/storestockcity');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/storestockcity');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $sscity_id) {
				$this->model_catalog_storestockcity->deleteStorestockcity($sscity_id);
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

			$this->response->redirect($this->url->link('catalog/storestockcity', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
	
	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'sscd.name';
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
			'href' => $this->url->link('catalog/storestockcity', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/storestockcity/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/storestockcity/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['storestockcities'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$sscity_total = $this->model_catalog_storestockcity->getTotalStorestockcity();

		$results = $this->model_catalog_storestockcity->getStorestockcity($filter_data);

		foreach ($results as $result) {
			$data['storestockcities'][] = array(
				'sscity_id'			 => $result['sscity_id'],
				'name'               => $result['name'],
				'sort_order'         => $result['sort_order'],
				'edit'               => $this->url->link('catalog/storestockcity/edit', 'user_token=' . $this->session->data['user_token'] . '&sscity_id=' . $result['sscity_id'] . $url, true)
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

		$data['sort_name'] = $this->url->link('catalog/storestockcity', 'user_token=' . $this->session->data['user_token'] . '&sort=sscd.name' . $url, true);
		$data['sort_sort_order'] = $this->url->link('catalog/storestockcity', 'user_token=' . $this->session->data['user_token'] . '&sort=ssc.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $sscity_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/storestockcity', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($sscity_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($sscity_total - $this->config->get('config_limit_admin'))) ? $sscity_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $sscity_total, ceil($sscity_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/storestockcity_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['sscity_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

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
			'href' => $this->url->link('catalog/storestockcity', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['sscity_id'])) {
			$data['action'] = $this->url->link('catalog/storestockcity/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/storestockcity/edit', 'user_token=' . $this->session->data['user_token'] . '&sscity_id=' . $this->request->get['sscity_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/storestockcity', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['sscity_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$sscity_info = $this->model_catalog_storestockcity->getStorestockcityInfo($this->request->get['sscity_id']);
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['sscity_description'])) {
			$data['storestockcity_description'] = $this->request->post['storestockcity_description'];
		} elseif (isset($this->request->get['sscity_id'])) {
			$data['storestockcity_description'] = $this->model_catalog_storestockcity->getStorestockcityDescriptions($this->request->get['sscity_id']);
		} else {
			$data['storestockcity_description'] = array();
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($sscity_info)) {
			$data['sort_order'] = $sscity_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/storestockcity_form', $data));
	}

	public function edit() {
		$this->load->language('catalog/storestockcity');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/storestockcity');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_storestockcity->editStorestockcity($this->request->get['sscity_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('catalog/storestockcity', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/storestockcity')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

//		$this->load->model('catalog/attribute');
//
//		foreach ($this->request->post['selected'] as $sscity_id) {
//			$attribute_total = $this->model_catalog_attribute->getTotalAttributesByAttributeGroupId($sscity_id);
//
//			if ($attribute_total) {
//				$this->error['warning'] = sprintf($this->language->get('error_attribute'), $attribute_total);
//			}
//		}

		return !$this->error;
	}
}