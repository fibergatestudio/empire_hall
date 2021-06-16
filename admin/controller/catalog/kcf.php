<?php
class ControllerCatalogKcf extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/kcf');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/kcf');

		$this->getList();
	}
	

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'od.name';
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
			'href' => $this->url->link('catalog/kcf', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/kcf/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/kcf/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['options'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$option_total = $this->model_extension_kcf->getTotalKcf();

		$results = $this->model_extension_kcf->getKcfs($filter_data);

		foreach ($results as $result) {
			
			if(!isset($result['kcf_id'])){ $result['kcf_id'] = ''; }
			if(!isset($result['sort_order'])){ $result['sort_order'] = ''; }
			$data['options'][] = array(
				'kcf_id'  => $result['kcf_id'],
				'name'       => $result['name'],
				'edit'       => $this->url->link('catalog/kcf/edit', 'user_token=' . $this->session->data['user_token'] . '&kcf_id=' . $result['kcf_id'] . $url, true)
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

		$data['sort_name'] = $this->url->link('catalog/kcf', 'user_token=' . $this->session->data['user_token'] . '&sort=od.name' . $url, true);
		$data['sort_sort_order'] = $this->url->link('catalog/kcf', 'user_token=' . $this->session->data['user_token'] . '&sort=o.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $option_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/kcf', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($option_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($option_total - $this->config->get('config_limit_admin'))) ? $option_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $option_total, ceil($option_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/kcf_list', $data));
	}	
	
	
	
	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['option_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['feeld_name'])) {
			$data['error_warning'] = $this->error['feeld_name'];
		} else {
			$data['error_warning'] = array();
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
			'href' => $this->url->link('catalog/kcf', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
		
		$data['cancel'] = $this->url->link('catalog/kcf', 'user_token=' . $this->session->data['user_token'], true);

		$data['kcf_types'] = $this->model_extension_kcf->getKcfTypes();
		$data['kcffeeld_types'] = $this->model_extension_kcf->getKcfFeeldTypes();
		
		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();	
		
		if(!isset($this->request->get['kcf_id'])){
			$kcf_id = 0;
		} else {
			$kcf_id = $this->request->get['kcf_id'];
		}

		$data['kcf_info'] = $this->model_extension_kcf->getKcf($kcf_id);


		
		
		$this->load->model('tool/image');
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/kcf_form', $data));
	}
	
	public function setkcf() {
		
		$json = array();
		$this->load->model('extension/kcf');
		
		if(isset($this->request->post['kcfid'])){
			
			if($this->request->post['kcfid']){
				// это kcf_id - id самого набора полей и оттуда можено взять набор полей, привязанных к этому набору
				$kcfid = $this->request->post['kcfid'];
				// тип страницы - категория, товар или информационка
				$typepage = $this->request->post['typepage'];
				// это id категории, товара или информационной страницы в зависимости от typepage
				$kcfv_page_id = $this->request->post['kcfv_page_id'];

				$this->model_extension_kcf->setKcf($kcfid, $typepage, $kcfv_page_id);
			} else {
				// 0 - значит выбран тип полей "Не выбрано"
				$kcfid = 0;
				// тип страницы - категория, товар или информационка
				$typepage = $this->request->post['typepage'];
				// это id категории, товара или информационной страницы в зависимости от typepage
				$kcfv_page_id = $this->request->post['kcfv_page_id'];

				$this->model_extension_kcf->setKcf($kcfid, $typepage, $kcfv_page_id);
			}		
			
			// Теперь получим нужные поля и соберем в верстку
			$this->load->model('localisation/language');
			$data = array();
			$data['languages'] = $this->model_localisation_language->getLanguages();	
			$data['kcf_feelds'] = $this->model_extension_kcf->getKcfFeelds($typepage, $kcfv_page_id);
			$json['html'] = $this->load->view('catalog/kcfjson', $data);
			$json['success'] = 'success';
		} else {
			$json['error'] = 'error';
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));		
	}

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

	public function add() {
		$this->load->language('extension/module/kcf');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/kcf');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_kcf->addKcf($this->request->post); 

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

			$this->response->redirect($this->url->link('catalog/kcf', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('extension/module/kcf');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/kcf');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_kcf->editKcf($this->request->get['kcf_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('catalog/kcf', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('extension/module/kcf');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/kcf');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $option_id) {
				$this->model_extension_kcf->delKcf($option_id);
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

			$this->response->redirect($this->url->link('catalog/kcf', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}


	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/kcf')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach($this->request->post['kfc_description'] as $value){
			if(!$value['name']){
				$this->error['feeld_name'] = $this->language->get('error_feeld_name');
			}
		}
		
		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/kcf')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('catalog/product');

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->language('extension/module/kcf');

			$this->load->model('extension/kcf');

			$this->load->model('tool/image');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			);

			$options = $this->model_extension_kcf->getOptions($filter_data);

			foreach ($options as $option) {
				$option_value_data = array();

				if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
					$option_values = $this->model_extension_kcf->getOptionValues($option['option_id']);

					foreach ($option_values as $option_value) {
						if (is_file(DIR_IMAGE . $option_value['image'])) {
							$image = $this->model_tool_image->resize($option_value['image'], 50, 50);
						} else {
							$image = $this->model_tool_image->resize('no_image.png', 50, 50);
						}

						$option_value_data[] = array(
							'option_value_id' => $option_value['option_value_id'],
							'name'            => strip_tags(html_entity_decode($option_value['name'], ENT_QUOTES, 'UTF-8')),
							'image'           => $image
						);
					}

					$sort_order = array();

					foreach ($option_value_data as $key => $value) {
						$sort_order[$key] = $value['name'];
					}

					array_multisort($sort_order, SORT_ASC, $option_value_data);
				}

				$type = '';

				if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox') {
					$type = $this->language->get('text_choose');
				}

				if ($option['type'] == 'text' || $option['type'] == 'textarea') {
					$type = $this->language->get('text_input');
				}

				if ($option['type'] == 'file') {
					$type = $this->language->get('text_file');
				}

				if ($option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
					$type = $this->language->get('text_date');
				}

				$json[] = array(
					'option_id'    => $option['option_id'],
					'name'         => strip_tags(html_entity_decode($option['name'], ENT_QUOTES, 'UTF-8')),
					'category'     => $type,
					'type'         => $option['type'],
					'option_value' => $option_value_data
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
}