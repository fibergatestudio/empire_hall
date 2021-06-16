<?php
class ControllerExtensionFeedFacebookRemarketing extends Controller {

	private $error = array();

	public function index() {
		$this->load->language('extension/feed/facebook_remarketing');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			if (isset($this->request->post['feed_facebook_remarketing_categories'])) {
				$this->request->post['feed_facebook_remarketing_categories'] = implode(',', $this->request->post['feed_facebook_remarketing_categories']);
			}

			$this->model_setting_setting->editSetting('feed_facebook_remarketing', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=feed', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_select_all'] = $this->language->get('text_select_all');
		$data['text_unselect_all'] = $this->language->get('text_unselect_all');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_data_feed'] = $this->language->get('entry_data_feed');
		$data['entry_shopname'] = $this->language->get('entry_shopname');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_category'] = $this->language->get('entry_category');
		$data['entry_currency'] = $this->language->get('entry_currency');
		$data['entry_in_stock'] = $this->language->get('entry_in_stock');
		$data['entry_out_of_stock'] = $this->language->get('entry_out_of_stock');
		
		$data['text_edit'] = $this->language->get('text_edit');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=feed', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/feed/facebook_remarketing', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/feed/facebook_remarketing', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=feed', true);
		
		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['feed_facebook_remarketing_status'])) {
			$data['feed_facebook_remarketing_status'] = $this->request->post['feed_facebook_remarketing_status'];
		} else {
			$data['feed_facebook_remarketing_status'] = $this->config->get('feed_facebook_remarketing_status');
		}

		$data['data_feed'] = HTTP_CATALOG . 'index.php?route=extension/feed/facebook_remarketing';

		if (isset($this->request->post['feed_facebook_remarketing_shopname'])) {
			$data['feed_facebook_remarketing_shopname'] = $this->request->post['feed_facebook_remarketing_shopname'];
		} else {
			$data['feed_facebook_remarketing_shopname'] = $this->config->get('feed_facebook_remarketing_shopname');
		}

		if (isset($this->request->post['feed_facebook_remarketing_currency'])) {
			$data['feed_facebook_remarketing_currency'] = $this->request->post['feed_facebook_remarketing_currency'];
		} else {
			$data['feed_facebook_remarketing_currency'] = $this->config->get('feed_facebook_remarketing_currency');
		}

		if (isset($this->request->post['feed_facebook_remarketing_in_stock'])) {
			$data['feed_facebook_remarketing_in_stock'] = $this->request->post['feed_facebook_remarketing_in_stock'];
		} elseif ($this->config->get('feed_facebook_remarketing_in_stock')) {
			$data['feed_facebook_remarketing_in_stock'] = $this->config->get('feed_facebook_remarketing_in_stock');
		} else {
			$data['feed_facebook_remarketing_in_stock'] = 7;
		}

		if (isset($this->request->post['feed_facebook_remarketing_out_of_stock'])) {
			$data['feed_facebook_remarketing_out_of_stock'] = $this->request->post['feed_facebook_remarketing_out_of_stock'];
		} elseif ($this->config->get('feed_facebook_remarketing_out_of_stock')) {
			$data['feed_facebook_remarketing_out_of_stock'] = $this->config->get('feed_facebook_remarketing_out_of_stock');
		} else {
			$data['feed_facebook_remarketing_out_of_stock'] = 5;
		}

		$this->load->model('localisation/stock_status');

		$data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

		$this->load->model('catalog/category');

		$data['categories'] = $this->model_catalog_category->getCategories(0);

		if (isset($this->request->post['feed_facebook_remarketing_categories'])) {
			$data['feed_facebook_remarketing_categories'] = $this->request->post['feed_facebook_remarketing_categories'];
		} elseif ($this->config->get('feed_facebook_remarketing_categories') != '') {
			$data['feed_facebook_remarketing_categories'] = explode(',', $this->config->get('feed_facebook_remarketing_categories'));
		} else {
			$data['feed_facebook_remarketing_categories'] = array();
		}

		$this->load->model('localisation/currency');
		$currencies = $this->model_localisation_currency->getCurrencies();
		$allowed_currencies = array_flip(array('RUR', 'RUB', 'BYR', 'KZT', 'UAH'));
		$data['currencies'] = array_intersect_key($currencies, $allowed_currencies);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('extension/feed/facebook_remarketing', $data));
	}

	public function import() {
		$this->load->language('extension/feed/facebook_remarketing');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'extension/feed/facebook_remarketing')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
				// Sanitize the filename
				$filename = basename(html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8'));

				// Allowed file extension types
				if (utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)) != 'txt') {
					$json['error'] = $this->language->get('error_filetype');
				}

				// Allowed file mime types
				if ($this->request->files['file']['type'] != 'text/plain') {
					$json['error'] = $this->language->get('error_filetype');
				}

				// Return any upload error
				if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
					$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
				}
			} else {
				$json['error'] = $this->language->get('error_upload');
			}
		}

		if (!$json) {
			$json['success'] = $this->language->get('text_success');

			$this->load->model('extension/feed/facebook_remarketing');

			// Get the contents of the uploaded file
			$content = file_get_contents($this->request->files['file']['tmp_name']);

			$this->model_extension_feed_facebook_remarketing->import($content);

			unlink($this->request->files['file']['tmp_name']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/feed/facebook_remarketing');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			$filter_data = array(
				'filter_name' => html_entity_decode($filter_name, ENT_QUOTES, 'UTF-8'),
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_extension_feed_facebook_remarketing->getGoogleBaseCategories($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'facebook_remarketing_category_id' => $result['facebook_remarketing_category_id'],
					'name'                    => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function category() {
		$this->load->language('extension/feed/facebook_remarketing');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['facebook_remarketing_categories'] = array();

		$this->load->model('extension/feed/facebook_remarketing');

		$results = $this->model_extension_feed_facebook_remarketing->getCategories(($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['facebook_remarketing_categories'][] = array(
				'facebook_remarketing_category_id' => $result['facebook_remarketing_category_id'],
				'facebook_remarketing_category'    => $result['facebook_remarketing_category'],
				'category_id'             => $result['category_id'],
				'category'                => $result['category']
			);
		}

		$category_total = $this->model_extension_feed_facebook_remarketing->getTotalCategories();

		$pagination = new Pagination();
		$pagination->total = $category_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('extension/feed/facebook_remarketing/category', 'user_token=' . $this->session->data['user_token'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($category_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($category_total - 10)) ? $category_total : ((($page - 1) * 10) + 10), $category_total, ceil($category_total / 10));

		$this->response->setOutput($this->load->view('extension/feed/facebook_remarketing_category', $data));
	}

	public function addCategory() {
		$this->load->language('extension/feed/facebook_remarketing');

		$json = array();

		if (!$this->user->hasPermission('modify', 'extension/feed/facebook_remarketing')) {
			$json['error'] = $this->language->get('error_permission');
		} elseif (!empty($this->request->post['facebook_remarketing_category_id']) && !empty($this->request->post['category_id'])) {
			$this->load->model('extension/feed/facebook_remarketing');

			$this->model_extension_feed_facebook_remarketing->addCategory($this->request->post);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function removeCategory() {
		$this->load->language('extension/feed/facebook_remarketing');

		$json = array();

		if (!$this->user->hasPermission('modify', 'extension/feed/facebook_remarketing')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('extension/feed/facebook_remarketing');

			$this->model_extension_feed_facebook_remarketing->deleteCategory($this->request->post['category_id']);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function install() {
		$this->load->model('extension/feed/facebook_remarketing');

		$this->model_extension_feed_facebook_remarketing->install();
	}

	public function uninstall() {
		$this->load->model('extension/feed/facebook_remarketing');

		$this->model_extension_feed_facebook_remarketing->uninstall();
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/feed/facebook_remarketing')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>
