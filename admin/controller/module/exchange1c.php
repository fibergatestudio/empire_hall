<?php
class ControllerModuleExchange1c extends Controller {
	private $error = array();

	public function index() {

		$this->load->language('module/exchange1c');
		$this->load->model('tool/image');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->request->post['exchange1c_order_date'] = $this->getSettingValue('exchange1c_order_date');
			$this->model_setting_setting->editSetting('exchange1c', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('module/exchange1c', 'user_token=' . $this->session->data['user_token'], 'SSL'));
		}

		$data['version'] = 'Version 1.6.0';

		$data['heading_title'] = $this->language->get('heading_title');
		$data['entry_username'] = $this->language->get('entry_username');
		$data['entry_password'] = $this->language->get('entry_password');
		$data['entry_allow_ip'] = $this->language->get('entry_allow_ip');
		$data['help_allow_ip'] = $this->language->get('help_allow_ip');
		$data['text_price_default'] = $this->language->get('text_price_default');
		$data['entry_config_price_type'] = $this->language->get('entry_config_price_type');
		$data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$data['entry_quantity'] = $this->language->get('entry_quantity');
		$data['entry_priority'] = $this->language->get('entry_priority');
		$data['entry_action'] = $this->language->get('entry_action');
		$data['entry_flush_product'] = $this->language->get('entry_flush_product');
		$data['entry_flush_category'] = $this->language->get('entry_flush_category');
		$data['entry_flush_manufacturer'] = $this->language->get('entry_flush_manufacturer');
		$data['entry_flush_quantity'] = $this->language->get('entry_flush_quantity');
		$data['entry_flush_attribute'] = $this->language->get('entry_flush_attribute');
		$data['entry_fill_parent_cats'] = $this->language->get('entry_fill_parent_cats');
		$data['entry_seo_url'] = $this->language->get('entry_seo_url');
		$data['entry_seo_url_deadcow'] = $this->language->get('entry_seo_url_deadcow');
		$data['entry_seo_url_translit'] = $this->language->get('entry_seo_url_translit');
		$data['entry_full_log'] = $this->language->get('entry_full_log');
		$data['entry_apply_watermark'] = $this->language->get('entry_apply_watermark');
		$data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		$data['text_image_manager'] = $this->language->get('text_image_manager');
		$data['text_browse'] = $this->language->get('text_browse');
		$data['text_clear'] = $this->language->get('text_clear');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_image'] = $this->language->get('entry_image');

		$data['entry_relatedoptions'] = $this->language->get('entry_relatedoptions');
		$data['entry_relatedoptions_help'] = $this->language->get('entry_relatedoptions_help');
		$data['entry_order_status_to_exchange'] = $this->language->get('entry_order_status_to_exchange');
		$data['entry_order_status_to_exchange_not'] = $this->language->get('entry_order_status_to_exchange_not');
		$data['entry_dont_use_artsync'] = $this->language->get('entry_dont_use_artsync');

		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_tab_general'] = $this->language->get('text_tab_general');
		$data['text_tab_product'] = $this->language->get('text_tab_product');
		$data['text_tab_order'] = $this->language->get('text_tab_order');
		$data['text_tab_manual'] = $this->language->get('text_tab_manual');
		$data['text_empty'] = $this->language->get('text_empty');
		$data['text_max_filesize'] = sprintf($this->language->get('text_max_filesize'), @ini_get('max_file_uploads'));
		$data['text_homepage'] = $this->language->get('text_homepage');
		$data['source_code'] = $this->language->get('source_code');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_order_currency'] = $this->language->get('entry_order_currency');
		$data['entry_order_notify'] = $this->language->get('entry_order_notify');
		$data['entry_upload'] = $this->language->get('entry_upload');
		$data['button_upload'] = $this->language->get('button_upload');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_insert'] = $this->language->get('button_insert');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['button_remove'] = $this->language->get('button_remove');

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['config_icon'])) {
			$data['config_icon'] = $this->request->post['config_icon'];
		} else {
			$data['config_icon'] = $this->getSettingValue('config_icon');
		}

		if (isset($this->request->post['config_icon']) && is_file(DIR_IMAGE . $this->request->post['config_icon'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['config_logo'], 100, 100);
		} elseif ($this->getSettingValue('config_icon') && is_file(DIR_IMAGE . $this->getSettingValue('config_icon'))) {
			$data['thumb'] = $this->model_tool_image->resize($this->getSettingValue('config_icon'), 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		}
		else {
			$data['error_warning'] = '';
		}

 		if (isset($this->error['image'])) {
			$data['error_image'] = $this->error['image'];
		} else {
			$data['error_image'] = '';
		}

		if (isset($this->error['exchange1c_username'])) {
			$data['error_exchange1c_username'] = $this->error['exchange1c_username'];
		}
		else {
			$data['error_exchange1c_username'] = '';
		}

		if (isset($this->error['exchange1c_password'])) {
			$data['error_exchange1c_password'] = $this->error['exchange1c_password'];
		}
		else {
			$data['error_exchange1c_password'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'		=> $this->language->get('text_home'),
			'href'		=> $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
			'separator'	=> false
		);

		$data['breadcrumbs'][] = array(
			'text'		=> $this->language->get('text_module'),
			'href'		=> $this->url->link('extension/module', 'user_token=' . $this->session->data['user_token'], 'SSL'),
			'separator'	=> ' :: '
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/exchange1c', 'user_token=' . $this->session->data['user_token'], 'SSL'),
			'separator' => ' :: '
		);

		$data['user_token'] = $this->session->data['user_token'];

		//$data['action'] = HTTPS_SERVER . 'index.php?route=module/exchange1c&user_token=' . $this->session->data['user_token'];
		$data['action'] = $this->url->link('module/exchange1c', 'user_token=' . $this->session->data['user_token'], 'SSL');

		//$data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/exchange1c&user_token=' . $this->session->data['user_token'];
		$data['cancel'] = $this->url->link('extension/module', 'user_token=' . $this->session->data['user_token'], 'SSL');

		if (isset($this->request->post['exchange1c_username'])) {
			$data['exchange1c_username'] = $this->request->post['exchange1c_username'];
		}
		else {
			$data['exchange1c_username'] = $this->getSettingValue('exchange1c_username');
		}

		if (isset($this->request->post['exchange1c_password'])) {
			$data['exchange1c_password'] = $this->request->post['exchange1c_password'];
		}
		else {
			$data['exchange1c_password'] = $this->getSettingValue('exchange1c_password');
		}

		if (isset($this->request->post['exchange1c_allow_ip'])) {
			$data['exchange1c_allow_ip'] = $this->request->post['exchange1c_allow_ip'];
		}
		else {
			$data['exchange1c_allow_ip'] = $this->getSettingValue('exchange1c_allow_ip');
		}

		if (isset($this->request->post['exchange1c_status'])) {
			$data['exchange1c_status'] = $this->request->post['exchange1c_status'];
		}
		else {
			$data['exchange1c_status'] = $this->getSettingValue('exchange1c_status');
		}

		if (isset($this->request->post['exchange1c_price_type'])) {
			$data['exchange1c_price_type'] = $this->request->post['exchange1c_price_type'];
		}
		else {
			$data['exchange1c_price_type'] = $this->getSettingValue('exchange1c_price_type');
			if(empty($data['exchange1c_price_type'])) {
				$data['exchange1c_price_type'][] = array(
					'keyword'			=> '',
					'customer_group_id'		=> 0,
					'quantity'			=> 0,
					'priority'			=> 0
				);
			}
		}

		if (isset($this->request->post['exchange1c_flush_product'])) {
			$data['exchange1c_flush_product'] = $this->request->post['exchange1c_flush_product'];
		}
		else {
			$data['exchange1c_flush_product'] = $this->getSettingValue('exchange1c_flush_product');
		}

		if (isset($this->request->post['exchange1c_flush_category'])) {
			$data['exchange1c_flush_category'] = $this->request->post['exchange1c_flush_category'];
		}
		else {
			$data['exchange1c_flush_category'] = $this->getSettingValue('exchange1c_flush_category');
		}

		if (isset($this->request->post['exchange1c_flush_manufacturer'])) {
			$data['exchange1c_flush_manufacturer'] = $this->request->post['exchange1c_flush_manufacturer'];
		}
		else {
			$data['exchange1c_flush_manufacturer'] = $this->getSettingValue('exchange1c_flush_manufacturer');
		}

		if (isset($this->request->post['exchange1c_flush_quantity'])) {
			$data['exchange1c_flush_quantity'] = $this->request->post['exchange1c_flush_quantity'];
		}
		else {
			$data['exchange1c_flush_quantity'] = $this->getSettingValue('exchange1c_flush_quantity');
		}

		if (isset($this->request->post['exchange1c_flush_attribute'])) {
			$data['exchange1c_flush_attribute'] = $this->request->post['exchange1c_flush_attribute'];
		}
		else {
			$data['exchange1c_flush_attribute'] = $this->getSettingValue('exchange1c_flush_attribute');
		}

		if (isset($this->request->post['exchange1c_fill_parent_cats'])) {
			$data['exchange1c_fill_parent_cats'] = $this->request->post['exchange1c_fill_parent_cats'];
		}
		else {
			$data['exchange1c_fill_parent_cats'] = $this->getSettingValue('exchange1c_fill_parent_cats');
		}

		if (isset($this->request->post['exchange1c_relatedoptions'])) {
			$data['exchange1c_relatedoptions'] = $this->request->post['exchange1c_relatedoptions'];
		} else {
			$data['exchange1c_relatedoptions'] = $this->getSettingValue('exchange1c_relatedoptions');
		}
		if (isset($this->request->post['exchange1c_order_status_to_exchange'])) {
			$data['exchange1c_order_status_to_exchange'] = $this->request->post['exchange1c_order_status_to_exchange'];
		} else {
			$data['exchange1c_order_status_to_exchange'] = $this->getSettingValue('exchange1c_order_status_to_exchange');
		}

		if (isset($this->request->post['exchange1c_dont_use_artsync'])) {
			$data['exchange1c_dont_use_artsync'] = $this->request->post['exchange1c_dont_use_artsync'];
		} else {
			$data['exchange1c_dont_use_artsync'] = $this->getSettingValue('exchange1c_dont_use_artsync');
		}

		if (isset($this->request->post['exchange1c_seo_url'])) {
			$data['exchange1c_seo_url'] = $this->request->post['exchange1c_seo_url'];
		}
		else {
			$data['exchange1c_seo_url'] = $this->getSettingValue('exchange1c_seo_url');
		}

		if (isset($this->request->post['exchange1c_full_log'])) {
			$data['exchange1c_full_log'] = $this->request->post['exchange1c_full_log'];
		}
		else {
			$data['exchange1c_full_log'] = $this->getSettingValue('exchange1c_full_log');
		}

		if (isset($this->request->post['exchange1c_apply_watermark'])) {
			$data['exchange1c_apply_watermark'] = $this->request->post['exchange1c_apply_watermark'];
		}
		else {
			$data['exchange1c_apply_watermark'] = $this->getSettingValue('exchange1c_apply_watermark');
		}

		if (isset($this->request->post['exchange1c_watermark'])) {
			$data['exchange1c_watermark'] = $this->request->post['exchange1c_watermark'];
		}
		else {
			$data['exchange1c_watermark'] = $this->getSettingValue('exchange1c_watermark');
		}

		if (isset($data['exchange1c_watermark'])) {
			$data['thumb'] = $this->model_tool_image->resize($data['exchange1c_watermark'], 100, 100);
		}
		else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		}

		if (isset($this->request->post['exchange1c_order_status'])) {
			$data['exchange1c_order_status'] = $this->request->post['exchange1c_order_status'];
		}
		else {
			$data['exchange1c_order_status'] = $this->getSettingValue('exchange1c_order_status');
		}

		if (isset($this->request->post['exchange1c_order_currency'])) {
			$data['exchange1c_order_currency'] = $this->request->post['exchange1c_order_currency'];
		}
		else {
			$data['exchange1c_order_currency'] = $this->getSettingValue('exchange1c_order_currency');
		}

		if (isset($this->request->post['exchange1c_order_notify'])) {
			$data['exchange1c_order_notify'] = $this->request->post['exchange1c_order_notify'];
		}
		else {
			$data['exchange1c_order_notify'] = $this->getSettingValue('exchange1c_order_notify');
		}

		// Группы
		$this->load->model('customer/customer_group');
		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		$this->load->model('localisation/order_status');

		$order_statuses = $this->model_localisation_order_status->getOrderStatuses();

		foreach ($order_statuses as $order_status) {
			$data['order_statuses'][] = array(
				'order_status_id' => $order_status['order_status_id'],
				'name'			  => $order_status['name']
			);
		}

		$this->template = 'module/exchange1c';
		$this->children = array(
			'common/header',
			'common/footer'
		);
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('module/exchange1c', $data));
	}

	private function validate() {

		if (!$this->user->hasPermission('modify', 'module/exchange1c')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		}
		else {
			return false;
		}
	}

	public function install() {}

	public function uninstall() {}

	// ---
	public function modeCheckauth() {

		// Проверяем включен или нет модуль
		/*if (!$this->getSettingValue('exchange1c_status')) {
			echo "failure\n";
			echo "1c module OFF";
			exit;
		}*/

		// Разрешен ли IP
		if ($this->getSettingValue('exchange1c_allow_ip') != '') {
			$ip = $_SERVER['REMOTE_ADDR'];
			$allow_ips = explode("\r\n", $this->getSettingValue('exchange1c_allow_ip'));

			if (!in_array($ip, $allow_ips)) {
				echo "failure\n";
				echo "IP is not allowed";
				exit;
			}
		}

		// Авторизуем
		if (($this->getSettingValue('exchange1c_username') != '') && (@$_SERVER['PHP_AUTH_USER'] != $this->getSettingValue('exchange1c_username'))) {
			echo "failure\n";
			echo "error login";
		}

		if (($this->getSettingValue('exchange1c_password') != '') && (@$_SERVER['PHP_AUTH_PW'] != $this->getSettingValue('exchange1c_password'))) {
			echo "failure\n";
			echo "error password";
			exit;
		}

		echo "success\n";
		echo "key\n";
		echo md5($this->getSettingValue('exchange1c_password')) . "\n";
	}

	public function manualImport() {
		$this->load->language('module/exchange1c');

		$cache = DIR_CACHE . 'exchange1c/';
		$json = array();

		if (!empty($this->request->files['file']['name'])) {

			$zip = new ZipArchive;

			if ($zip->open($this->request->files['file']['tmp_name']) === true) {
				$this->modeCatalogInit(false);

				$zip->extractTo($cache);
				$files = scandir($cache);

				foreach ($files as $file) {
					if (is_file($cache . $file)) {
						$this->modeImport($file);
					}
				}

				if (is_dir($cache . 'import_files')) {
					$images = DIR_IMAGE . 'import_files/';

					if (is_dir($images)) {
						$this->cleanDir($images);
					}

					rename($cache . 'import_files/', $images);
				}

			}
			else {
				// Читаем первые 1024 байт и определяем файл по сигнатуре, ибо мало ли, какое у него имя
				$handle = fopen($this->request->files['file']['tmp_name'], 'r');
				$buffer = fread($handle, 512);
				fclose($handle);

				if (strpos($buffer, 'Классификатор')) {
					$this->modeCatalogInit(false);
					move_uploaded_file($this->request->files['file']['tmp_name'], $cache . 'import.xml');
					$this->modeImport('import.xml');

				}
				else if (strpos($buffer, 'ПакетПредложений')) {
					move_uploaded_file($this->request->files['file']['tmp_name'], $cache . 'offers.xml');

                    $this->modeImport('offers.xml');
				}
				else {
					$json['error'] = $this->language->get('text_upload_error');
					exit;
				}
			}

			$json['success'] = $this->language->get('text_upload_success');
		}

		$this->response->setOutput(json_encode($json));
	}

	public function modeCatalogInit($echo = true) {
		$this->load->model('tool/exchange1c');

		// чистим кеш, убиваем старые данные
		$this->cleanCacheDir();

		// Проверяем естль ли БД для хранения промежуточных данных.
		$this->model_tool_exchange1c->checkDbSheme();

		// Очищаем таблицы
		$this->model_tool_exchange1c->flushDb(array(
			'product' 		=> $this->getSettingValue('exchange1c_flush_product'),
			'category'		=> $this->getSettingValue('exchange1c_flush_category'),
			'manufacturer'	=> $this->getSettingValue('exchange1c_flush_manufacturer'),
			'attribute'		=> $this->getSettingValue('exchange1c_flush_attribute'),
			'full_log'		=> $this->getSettingValue('exchange1c_full_log'),
			'apply_watermark'	=> $this->getSettingValue('exchange1c_apply_watermark'),
			'quantity'		=> $this->getSettingValue('exchange1c_flush_quantity')
		));

		$limit = 100000 * 1024;

		//if ($echo) {
			echo "zip=no\n";
			echo "file_limit=".$limit."\n";
		//}

	}

	public function modeSaleInit() {
		$limit = 100000 * 1024;

		echo "zip=no\n";
		echo "file_limit=".$limit."\n";
	}

	public function modeFile() {

		if (!isset($this->request->cookie['key'])) {
			return;
		}

		if ($this->request->cookie['key'] != md5($this->getSettingValue('exchange1c_password'))) {
			echo "failure\n";
			echo "Session error";
			return;
		}

		$cache = DIR_CACHE . 'exchange1c/';

		// Проверяем на наличие имени файла
		if (isset($this->request->get['filename'])) {
			$uplod_file = $cache . $this->request->get['filename'];
		}
		else {
			echo "failure\n";
			echo "ERROR 10: No file name variable";
			return;
		}

		// Проверяем XML или изображения
		if (strpos($this->request->get['filename'], 'import_files') !== false) {
			$cache = DIR_IMAGE;
			$uplod_file = $cache . $this->request->get['filename'];
			$this->checkUploadFileTree(dirname($this->request->get['filename']) , $cache);
		}

		// Получаем данные
		$data = file_get_contents("php://input");

		if ($data !== false) {
			if ($fp = fopen($uplod_file, "wb")) {
				$result = fwrite($fp, $data);

				if ($result === strlen($data)) {
					echo "success\n";

					chmod($uplod_file , 0777);
					//echo "success\n";
				}
				else {
					echo "failure\n";
				}
			}
			else {
				echo "failure\n";
				echo "Can not open file: $uplod_file\n";
				echo $cache;
			}
		}
		else {
			echo "failure\n";
			echo "No data file\n";
		}


	}

	public function modeImport($manual = false) {

		$cache = DIR_CACHE . 'exchange1c/';

		if ($manual) {
			$filename = $manual;
			$importFile = $cache . $filename;
		}
		else if (isset($this->request->get['filename'])) {
			$filename = $this->request->get['filename'];
			$importFile = $cache . $filename;
		}
		else {
			echo "failure\n";
			echo "ERROR 10: No file name variable";
			return 0;
		}

		$this->load->model('tool/exchange1c');

		// Определяем текущую локаль
		$language_id = 2;

		if (strpos($filename, 'import') !== false) {

			$this->model_tool_exchange1c->parseImport($filename, $language_id);

			if ($this->getSettingValue('exchange1c_fill_parent_cats')) {
				$this->model_tool_exchange1c->fillParentsCategories();
			}

			if (!$manual) {
				echo "success\n";
			}

		}
		else if (strpos($filename, 'offers') !== false) {
			//$exchange1c_price_type = $this->getSettingValue('exchange1c_price_type');
                        $exchange1c_price_type = array();
			$this->model_tool_exchange1c->parseOffers($filename, $exchange1c_price_type, $language_id);

			if (!$manual) {
				echo "success\n";
			}
		}
		else {
			echo "failure\n";
			echo $filename;
		}

		$this->cache->delete('product');
		return;
	}

	public function modeQueryOrders() {
		if (!isset($this->request->cookie['key'])) {
			echo "Cookie fail\n";
			return;
		}

		if ($this->request->cookie['key'] != md5($this->getSettingValue('exchange1c_password'))) {
			echo "failure\n";
			echo "Session error";
			return;
		}

		$this->load->model('tool/exchange1c');

		$orders = $this->model_tool_exchange1c->queryOrders(array(
			'from_date'             => $this->getSettingValue('exchange1c_order_date'),
			'exchange_status'       => $this->getSettingValue('exchange1c_order_status_to_exchange'),
			'new_status'            => $this->getSettingValue('exchange1c_order_status'),
			'notify'		=> $this->getSettingValue('exchange1c_order_notify'),
			'currency'		=> $this->getSettingValue('exchange1c_order_currency') ? $this->getSettingValue('exchange1c_order_currency') : 'грн.'
		));

		//echo iconv('utf-8', 'cp1251', $orders);
		echo mb_convert_encoding($orders,"Windows-1251","UTF-8");
	}

	/**
	 * Changing order statuses.
	 */
	public function modeOrdersChangeStatus(){
		if (!isset($this->request->cookie['key'])) {
			echo "Cookie fail\n";
			return;
		}

		if ($this->request->cookie['key'] != md5($this->getSettingValue('exchange1c_password'))) {
			echo "failure\n";
			echo "Session error";
			return;
		}

		$this->load->model('tool/exchange1c');

		$result = $this->model_tool_exchange1c->queryOrdersStatus(array(
			'from_date' 		=> $this->getSettingValue('exchange1c_order_date'),
			'exchange_status'       => $this->getSettingValue('exchange1c_order_status_to_exchange'),
			'new_status'            => $this->getSettingValue('exchange1c_order_status'),
			'notify'		=> $this->getSettingValue('exchange1c_order_notify')
		));

		if($result){
			$this->load->model('setting/setting');
			$config = $this->model_setting_setting->getSetting('exchange1c');
			$config['exchange1c_order_date'] = date('Y-m-d H:i:s');
			$this->model_setting_setting->editSetting('exchange1c', $config);
		}

		if($result)
			echo "success\n";
		else
			echo "fail\n";
	}


	// -- Системные процедуры
	private function cleanCacheDir() {

		// Проверяем есть ли директория
		if (file_exists(DIR_CACHE . 'exchange1c')) {
			if (is_dir(DIR_CACHE . 'exchange1c')) {
				return $this->cleanDir(DIR_CACHE . 'exchange1c/');
			}
			else {
				unlink(DIR_CACHE . 'exchange1c');
			}
		}

		mkdir (DIR_CACHE . 'exchange1c');

		return 0;
	}

	private function checkUploadFileTree($path, $curDir = null) {

		if (!$curDir) $curDir = DIR_CACHE . 'exchange1c/';

		foreach (explode('/', $path) as $name) {

			if (!$name) continue;

			if (file_exists($curDir . $name)) {
				if (is_dir( $curDir . $name)) {
					$curDir = $curDir . $name . '/';
					continue;
				}

				unlink ($curDir . $name);
			}

			mkdir ($curDir . $name );
			$curDir = $curDir . $name . '/';
		}

	}

	private function cleanDir($root, $self = false) {

		$dir = dir($root);

		while ($file = $dir->read()) {
			if ($file == '.' || $file == '..') continue;
			if (file_exists($root . $file)) {
				if (is_file($root . $file)) { unlink($root . $file); continue; }
				if (is_dir($root . $file)) { $this->cleanDir($root . $file . '/', true); continue; }
				var_dump ($file);
			}
			var_dump($file);
		}

		if ($self) {
			if(file_exists($root) && is_dir($root)) {
				rmdir($root); return 0;
			}

			var_dump($root);
		}
		return 0;
	}

        private function getSettingValue ($key) {
		$query = $this->db->query("SELECT value FROM " . DB_PREFIX . "setting WHERE `key` = '" . $this->db->escape($key) . "'");

		if ($query->num_rows) {
			return $query->row['value'];
		} else {
			return null;
		}
        }
}
?>
