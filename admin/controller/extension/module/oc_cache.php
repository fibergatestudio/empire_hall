<?php

define('WKCACHE_VERSION', '3.0.0.0');

class ControllerExtensionModuleOcCache extends Controller {
	public function install() {
		$this->load->model('extension/module/oc_cache');
		$this->model_extension_module_oc_cache->createTables();
		$this->load->model('setting/event');
		$this->model_setting_event->addEvent('oc_cache', 'catalog/controller/checkout/cart/add', 'tool/cache_event/deleteCache');
	}

	public function uninstall() {
		$this->load->model('setting/event');
		$this->model_setting_event->deleteEvent('oc_cache');
	}

	private $error = array();

	private $post_data	=	array(
		'module_oc_cache_status',
		'module_oc_cache_google_speedtest_api_key',
		'module_oc_cache_featured',
		'module_oc_cache_latest',
		'module_oc_cache_category',
		'module_oc_cache_product',
		'module_oc_cache_manufacturer',
		'module_oc_cache_menu_layout',
		'module_oc_cache_banner_module',
		'module_oc_cache_filter_module',
		'module_oc_cache_information_module',
		'module_oc_cache_store_location',
		'module_oc_cache_timezone',
		'module_oc_cache_minify_css',
		'module_oc_cache_combine_css',
		'module_oc_cache_css_cdn',
		'module_oc_cache_minify_js',
		'module_oc_cache_combine_js',
		'module_oc_cache_js_cdn',
		'module_oc_cache_page_cache_status',
		'module_oc_cache_page_cache_expiry',
		'module_oc_cache_page_cache_on_login',
		'module_oc_cache_ignore_route',
		'module_oc_cache_frontpanel_status',
		'module_oc_cache_frontpanel_clear_status',
		'module_oc_cache_frontpanel_position',
		'module_oc_cache_frontpanel_backgroundcolor',
		'module_oc_cache_frontpanel_color',
		'module_oc_cache_frontpanel_cachemsg',
		'module_oc_cache_frontpanel_expiretime',
		'module_oc_cache_compression_status',
		'module_oc_cache_css_compression',
		'module_oc_cache_css_compression_level',
		'module_oc_cache_js_compression',
		'module_oc_cache_js_compression_level',
		'module_oc_cache_html_compression',
		'module_oc_cache_html_compression_level',
		'module_oc_cache_image_compression',
		'module_oc_cache_image_compression_quality',
		//advanced
		'module_oc_cache_image_webp',
		'module_oc_cache_image_sprite',
		'module_oc_cache_image_lazyload',
		'module_oc_cache_leverage_browser_cache',
		'module_oc_cache_image_multi_sizing',

		'module_oc_cache_rss_xml_compression',
		'module_oc_cache_xhtml_xml_compression',
		'module_oc_cache_image_icon_compression',
		'module_oc_cache_xml_compression',

		'module_oc_cache_leverage_image',
		'module_oc_cache_leverage_videos',
		'module_oc_cache_leverage_css',
		'module_oc_cache_leverage_js',
		'module_oc_cache_leverage_other',
		'module_oc_cache_resever_compressed_data',

		'module_oc_cache_old_browser_bug',
		'module_oc_cache_deflate_proxy_server', //dependent to old browser
	);

	public	function composerChk() {
		$this->load->library('wkcache/validator');
		$this->validator->getComposer();
	}

	public function index() {

		$webkulcache = $this->load->library('cart/webkulcache');

		$this->composerChk();

		$data = array();

		$data =  $this->load->language('extension/module/oc_cache');

		$this->document->setTitle($this->language->get('heading_title1'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_oc_cache', $this->request->post);

			$this->load->model('extension/module/oc_cache');

			$this->model_extension_module_oc_cache->truncateTable();

			if (isset($this->request->post['module_oc_cache_status']) && $this->request->post['module_oc_cache_status']) {
				define('WEBKUL_CACHE', true);
			} else {
				define('WEBKUL_CACHE', false);
			}

			if (isset($this->request->post['module_oc_cache_compression_status'])) {
				$this->compression($this->request->post);
				$this->serveCompressedData($this->request->post['module_oc_cache_resever_compressed_data']);
			}

			if(isset($this->request->post['module_oc_cache_leverage_browser_cache'])) {
				$this->leverageCaching($this->request->post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data['cache_zonelist'] = $this->getTimeZone();

		foreach ($this->post_data as $post) {
			if(isset($this->request->post[$post])){
				$data[$post] = $this->request->post[$post];
			}else{
				$data[$post] = $this->config->get($post);
			}
		}

		foreach ($this->error as $key => $error) {
			if($key !== 'oc_cache_status')
				if (isset($this->error[$key])) {
					$data[$key.'_error'] = $this->error[$key];
				} else {
					$data[$key.'_error'] = '';
				}
		}


		$data['cache_file_info'] = array();

		$cache_index = $this->getCacheIndex();

		$path = glob(DIR_MODIFICATION.'*');

		$webkulcache = $this->load->library('cart/webkulcache');

		foreach ($cache_index as $index) {
			if(count($path) !== 1){
				$filePath = $this->webkulcache->getCacheFileSize($index, 'files');
				if(isset($filePath) && $filePath){
					if(file_exists($filePath)){
						$file = new SplFileInfo($filePath);
						$data['cache_file_info'][$index] = array(
							'path' 		=> $filePath,
							'keyword' 	=> $index,
							'size' 		=> $file->getSize() . ' bytes');
					}
					else{
						$data['cache_file_info'][$index] = array(
							'path' 		=> $filePath,
							'keyword' 	=> '',
							'size' 		=> 0);
					}
				}
			}
		}

		if(count($path) !== 1){
			$getStoreTimeZone = $this->getStoreTimeZone();
			$data['getDriverStats'] = $this->webkulcache->getStats('files');
			$expire_date = (array)$data['getDriverStats']['expire_time'];
			$getCurrentDateTime = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone($this->config->get('oc_cache_timezone') ? $this->config->get('oc_cache_timezone') : $getStoreTimeZone));

			$currect_date = (array)$getCurrentDateTime;

			$data['dateTime_diff'] = abs( strtotime( $currect_date['date'] ) - strtotime( $expire_date['date'] ) );
		}else{
			$data['getDriverStats'] = $expire_date = array();
			$data['dateTime_diff'] = 0;
		}

		$data['clear_cache_option'] = $this->getClearCacheOption();

		if (isset($this->error)) {
           foreach($this->error as $key => $error) {
			$data[$key] = $this->error[$key];
		   }
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

		$data['cdn_css'] = $this->getCDNcss();

		$data['cdn_js'] = $this->getCDNjs();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/oc_cache', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/oc_cache', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$data['user_token'] = $this->session->data['user_token'];

		$data['agetypes'] = $this->getAgeType();

		$page = $this->load->library('wkcache/page');
		$data['total_page_cache_size'] = 0;

		if (is_dir(DIR_STORAGE . "wkcache")) {
			$cdir = scandir(DIR_STORAGE . "wkcache");
			foreach ($cdir as $key => $value) {

				if (!in_array($value,array(".", ".."))) {
					if (is_dir(DIR_STORAGE."wkcache/" . $value)) {
						$data['total_page_cache_size'] += $this->page->getSize(DIR_STORAGE."wkcache/" . $value);
					}
				}
			} 	
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/oc_cache', $data));
	}

    protected function getCDNcss() {
		return array(
			'bootstrap',
			'font-awesome',
		);
	}

	protected function getCDNjs() {
		return array(
			'jquery',
			'bootstrap',
		);
	}

	protected function getClearCacheOption() {
		return array(
			'ocmod',
			'log_file',
			'image',
			'all_store'
		);
	}

	protected function getCacheIndex() {
		return array(
			'product_featured',
			'product_latest',
			'category_module',
			'product',
			'product_images',
			'product_options',
			'manufacturer',
			'header_category',
			'banner_design',
			'filter_module',
			'information_module',
			'store_location',
		);
	}

	protected function getAgeType() {
        return array(
			'years',
			'months',
			'weeks',
			'days',
			'hours',
			'minutes',
			'seconds'
		);
	}

	protected function validate() {

		if (!$this->user->hasPermission('modify', 'extension/module/oc_cache')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if(isset($this->request->post['module_oc_cache_image_compression_quality']) && ($this->request->post['module_oc_cache_image_compression_quality'] != 0 || (int)$this->request->post['module_oc_cache_image_compression_quality'] < 0 || (int)$this->request->post['module_oc_cache_image_compression_quality'] > 100)) {
			$this->error['error_module_oc_cache_image_compression_quality'] = $this->language->get('image_compression_quality_error');
		}

		$this->validateSettingTab();

		$this->validateCombine_MinifyTab();

		$this->validateImageOptTab();

		$this->validateCompressionTab();

		$this->validateLeverageTab();

		$this->load->library('wkcache/validator');

		$this->validateFrontPanelSettingTab();

		$this->validatePageCacheTab();

		if(isset($this->error) && !empty($this->error)) {
           $this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}


	protected function getIO_FieldKeys() {
		return  array(
			'image_webp',
			'image_lazyload',
		);
	}

    protected function getCompression_FieldKeys() {
		return  array(
			'compression_status',
			'css_compression',
			'js_compression',
			'html_compression',
			'image_compression',
			'xml_compression',
			'image_icon_compression',
			'xhtml_xml_compression',
			'rss_xml_compression',
			'resever_compressed_data'
		);
	}

	protected function getLaverageTableIndex() {
		return array(
			'leverage_image',
			'leverage_videos',
			'leverage_css',
			'leverage_js',
			'leverage_other',
		);
	}

	protected function getFG_CM_1() {
		return array(
			'minify_css',
			'combine_css',
			'minify_js',
			'combine_js',
		);
	}

    protected function validatePageCacheTab() {
		$arrayT = array(
			'page_cache_status',
			'page_cache_on_login'
		);

		$in_array_value = array(1,0);

		foreach ($arrayT as $value) {
			if(isset($this->request->post['module_oc_cache_'.$value])) {
				$val = (int)$this->request->post['module_oc_cache_'.$value];
				if(!in_array($val,$in_array_value)) {
					$this->error['error_oc_cache_'.$value] = $this->language->get('error_check_oc_cache_'.$value);

				}
			} else {
				$this->error['error_oc_cache_'.$value] = $this->language->get('error_empty_oc_cache_'.$value);
			}
		}

		$key = 'page_cache_expiry';

		if(isset($this->request->post['module_oc_cache_'.$key])) {
			if($this->request->post['module_oc_cache_'.$key] && !$this->validator->checkIsNumber($this->request->post['module_oc_cache_'.$key])){
				$this->error['error_oc_cache_'.$key] = $this->language->get('error_check_oc_cache_'.$key);
			    return;
			}

			$max_time = $this->getTotalSecondsInMonth();

			if ($this->request->post['module_oc_cache_'.$key] > $max_time) {
				$this->error['error_oc_cache_'.$key] = $this->language->get('error_cache_expire_check');
				return;
			}
		} else {
			$this->error['error_oc_cache_'.$key] = $this->language->get('error_empty_oc_cache_'.$key);
		}

		$key = 'ignore_route';

        if(isset($this->request->post['module_oc_cache_'.$key])) {
			if($this->request->post['module_oc_cache_'.$key]) {
				$lines = explode(PHP_EOL, $this->request->post['module_oc_cache_'.$key]);
				foreach($lines as $line) {
					// if(!$this->validator->checkRoute($line,$this->registry)) {
					// 	$this->error['error_oc_cache_'.$key] = $this->language->get('error_check_oc_cache_'.$key);
					// 	break;
					// }
				}
			}
		} else {
			$this->error['error_oc_cache_'.$key] = $this->language->get('error_empty_oc_cache_'.$key);
		}
	}

	protected function getTotalSecondsInMonth() {
	    return 2.628e+6;
	}

    protected function validateFrontPanelSettingTab() {
		$array = array(
			'frontpanel_status',
			'frontpanel_clear_status',
			'frontpanel_expiretime'
		);

		$in_array_value = array(1,0);

		foreach ($array as $value) {
			$this->validatePostInArray($value,$in_array_value,false);
		}

		if(isset($this->request->post['module_oc_cache_frontpanel_cachemsg']) && $this->request->post['module_oc_cache_frontpanel_cachemsg']) {
			if(!$this->validator->lengthRange($this->request->post['module_oc_cache_frontpanel_cachemsg'],array('min'=> 0,'max'=>128))) {
				$this->error['error_oc_cache_frontpanel_cachemsg'] = $this->language->get('error_check_oc_cache_frontpanel_cachemsg');
			    return;
			}
		}

		$in_array_value = array('top','bottom');

		$key = 'frontpanel_position';

		$this->validatePostInArray($key,$in_array_value,false);

		$colorKey = array(
			'frontpanel_backgroundcolor',
			'frontpanel_color',
		);

		foreach ($colorKey as $value) {
			if(isset($this->request->post['module_oc_cache_'.$value])) {
				if($this->request->post['module_oc_cache_'.$value]) {
					if(!$this->validator->checkColor($this->request->post['module_oc_cache_'.$value])) {
						$this->error['error_oc_cache_'.$value] = $this->language->get('error_check_oc_cache_'.$value);
					}
				}
			} else {
				$this->error['error_oc_cache_'.$value] = $this->language->get('error_empty_oc_cache_'.$value);
			}
		}
	}

	protected function validateLeverageTab() {
		$timezonAllowed = array();
        if(isset($this->request->post['module_oc_cache_leverage_browser_cache'])) {
            if(!in_array($this->request->post['module_oc_cache_leverage_browser_cache'],array(0,1))){
				$this->error['error_oc_cache_leverage_browser_cache'] = $this->language->get('error_status_check');
				return;
		    }
		} else {
			$this->error['error_oc_cache_leverage_browser_cache'] = $this->language->get('error_empty_oc_cache_leverage_browser_cache');
			return;
		}

		if(!$this->request->post['module_oc_cache_leverage_browser_cache']) {
           return;
		}

        $allTableIndex = $this->getLaverageTableIndex();

        foreach($allTableIndex as $value) {
		   if(isset($this->request->post['module_oc_cache_'.$value]) && is_array($this->request->post['module_oc_cache_'.$value]) && !empty($this->request->post['module_oc_cache_'.$value]))  {
				$passData = $this->request->post['module_oc_cache_'.$value];
				$this->checkLaverageTable($passData,$value);
			}
		}

	}

	protected function checkLaverageTable($passedData = array(),$value){
		if(is_array($passedData) && !empty($passedData)) {
			// check expiry

			if(isset($passedData['time']) && $passedData['time']) {
				if(!$this->validator->checkIsNumber($passedData['time'])) {
					$this->error['error_time_'.$value] = $this->language->get('error_time_check');
					return;
				}
			} else {
				$this->error['error_time_'.$value] = $this->language->get('error_time_empty');
				return;
			}

			if(isset($passedData['status'])) {
				if(!in_array($passedData['status'],array(0,1))) {
					$this->error['error_status_'.$value] = $this->language->get('error_status_check');
					return;
				}

			} else {
				$this->error['error_status_'.$value] = $this->language->get('error_status_empty');
				return;
			}

			$ageType = $this->getAgeType();

			if(isset($passedData['type'])) {
				if(!in_array($passedData['type'],$ageType)) {
					$this->error['error_type_'.$value] = $this->language->get('error_type_check');
					return;
				}
				$time = $this->getMaxValueOnCurrentAgeType($passedData['type']);
				if ($passedData['time'] > $time) {
					$this->error['error_time_'.$value] = $this->language->get('error_time_check');
					return;
				}
			} else {
				$this->error['error_type_'.$value] = $this->language->get('error_type_empty');
				return;
			}
		}
		return;
	}

    protected function getMaxValueOnCurrentAgeType($type = '') {
		$max  = 0;
		if($type) {
			switch ($type) {
				case 'years': $max = 1;
					break;
				case 'months': $max = 12;
					break;
				case 'days': $max = 365;
					break;
				case 'weeks': $max = 52;
					break;
				case 'hours': $max = 8760;
					break;
				case 'minutes': $max = 525600;
					break;
				case 'seconds': $max = 3.154e+7;
					break;
				default: $max  = 0;
			}
		}
		return $max;
	}

    protected function validateCompressionTab() {
		$in_array_value = array(0,1);

		$field_IO_group = $this->getCompression_FieldKeys();

		$is_value_required = false;

		foreach ($field_IO_group as $value) {
			$this->validatePostInArray($value,$in_array_value,$is_value_required);
		}
	}

	protected function validateImageOptTab() {
		$in_array_value = array(0,1);

		$field_IO_group = $this->getIO_FieldKeys();

        $is_value_required = false;
		foreach ($field_IO_group as $value) {
			$this->validatePostInArray($value,$in_array_value,$is_value_required);
		}
	}

    protected function validateCombine_MinifyTab() {
		$in_array_value = array(0,1);

		$is_value_required = false;

		$field_group_1 = $this->getFG_CM_1();

		foreach ($field_group_1 as $value) {
			$this->validatePostInArray($value,$in_array_value,$is_value_required);
		}

		$in_array_value = $this->getCDNcss();
		$this->validateCDNPostInArray('css_cdn',$in_array_value);
		$in_array_value = $this->getCDNjs();
		$this->validateCDNPostInArray('js_cdn',$in_array_value);
	}

	protected function validateCDNPostInArray($value ,$in_arr_value,$is_value_required = true) {
		if(isset($this->request->post['module_oc_cache_'.$value])) {
			if(is_array($this->request->post['module_oc_cache_'.$value]) && !empty($this->request->post['module_oc_cache_'.$value])) {
				foreach ($this->request->post['module_oc_cache_'.$value] as $key => $value){
					if(!in_array($key,$in_arr_value)) {
						$this->error['error_oc_cache_'.$value] = $this->language->get('error_check_oc_cache_'.$value);
					}
				}
			}
		}
	}

	protected function validatePostInArray($value ,$in_arr_value,$is_value_required = true) {
		if(isset($this->request->post['module_oc_cache_'.$value])) {
			if($is_value_required && (!empty($this->request->post['module_oc_cache_'.$value]) || !$this->request->post['module_oc_cache_'.$value])) {
				if(!in_array($this->request->post['module_oc_cache_'.$value],$in_arr_value)) {
					$this->error['error_oc_cache_'.$value] = $this->language->get('error_check_oc_cache_'.$value);
				}
			}
		} else {
			$this->error['error_oc_cache_'.$value] = $this->language->get('error_empty_oc_cache_'.$value);
		}
	}

    protected function validateSettingTab() {

		$timezonAllowed = array();

        if(isset($this->request->post['module_oc_cache_status'])) {
            if(!in_array($this->request->post['module_oc_cache_status'],array(0,1))){
				$this->error['error_status'] = $this->language->get('error_status_check');
				return;
		    }
		} else {
			$this->error['error_status'] = $this->language->get('error_status_empty');
			return;
		}

		$timezonAllowed = $this->getTimeZone();

	    if(isset($this->request->post['module_oc_cache_timezone'])) {
			if(!in_array($this->request->post['module_oc_cache_timezone'],array_keys($timezonAllowed))){
				$this->error['error_timezone'] = $this->language->get('error_timezone_check');
				return;
		    }
		} else {
			$this->error['error_timezone'] = $this->language->get('error_timezone_empty');
			return;
		}

        $allLayout = $this->getAllLayoutModules();

        foreach($allLayout as $value) {
		   if(isset($this->request->post['module_oc_cache_'.$value]) && is_array($this->request->post['module_oc_cache_'.$value]) && !empty($this->request->post['module_oc_cache_'.$value]))  {
				$passData = $this->request->post['module_oc_cache_'.$value];
				$this->checkTheTable($passData,$value);

			}
		}
	}

    protected function getAllLayoutModules() {
	   return array(
			'featured',
			'latest',
			'category',
			'product',
			'manufacturer',
			'menu_layout',
			'banner_module',
			'filter_module',
			'information_module',
			'store_location',
		);
	}

	protected function checkTheTable($passedData = array(),$value){
		if(is_array($passedData) && !empty($passedData)) {


			// check expiry
			if(isset($passedData['expire'])) {
				if(!$this->validator->checkIsNumber($passedData['expire']) || ($passedData['expire'] > 2592000000))
					$this->error['error_expire_'.$value] = $this->language->get('error_expire_check');
					return;
			} else {
				$this->error['error_expire_'.$value] = $this->language->get('error_expire_empty');
				return;
			}
			//check status
			if(isset($passedData['status']) && $passedData['status']) {
				if(!in_array($passedData['status'],array(0,1)))
					$this->error['error_status_'.$value] = $this->language->get('error_status_check');
					return;
			} else {
				$this->error['error_status_'.$value] = $this->language->get('error_status_empty');
				return;
			}
		}
		return;
	}

    public function checkTimeZone() {

		$this->load->library('wkcache/validator');

		if(isset($this->post['module_oc_cache_featured']) && is_array($this->post['module_oc_cache_featured']) && !empty($this->post['module_oc_cache_featured']))
			$data_arr = $this->post['module_oc_cache_featured'];
			if(isset($data_arr['expire']) && $data_arr['expire'])
			    if($this->validator->checkIsNumber($data_arr['expire']))
					return false;
				return true;
			return true;
		return true;
	}


	public function clearPageCache() {
		$this->load->library('wkcache/validator');

		$this->validator->getComposer();

		SELF::delete_assets(DIR_STORAGE . 'wkcache');
		SELF::delete_assets(DIR_CATALOG . 'view/wkcache/assets');

		$page = $this->load->library('wkcache/page');
		$json = array();
		try{
			
			$this->page->clearCache();
			$json['success'] = true;
			$json['size'] = $this->page->getSize(DIR_STORAGE."wkcache/pagecache");
		} catch(Exception $ex) {
			$json = $ex;
			$json['success'] = false;
		}
		$this->response->setOutput(json_encode($json));
	}

    /**
	 * create deflate module acceess rules function
	 *
	 * @param [type] $type
	 * @param boolean $disabled
	 * @return void
	 */
    private function createDeflateRulesHtaccess($type, $disabled = true) {
	    $basic = "AddOutputFilterByType DEFLATE ".$type;
	    if($disabled)
		   $basic = "#".$basic;
	    return $basic;
	}
	/**
	 * compression with defalte gzip function
	 *
	 * @param [type] $data
	 * @return void
	 */

	public function compression($data) {
		$this->load->library('wkcache/validator');
		$this->validator->getComposer();
		$this->registry->set('_cachehataccessPage', new wkcache\Htaccesspage($this->registry));
		$this->_cachehataccessPage->gzipCompression($data);
	}

	public function leverageCaching($data) {
		$this->load->library('wkcache/validator');
		$this->validator->getComposer();
		$this->registry->set('_cachehataccessPage', new wkcache\Htaccesspage($this->registry));
		$this->_cachehataccessPage->leverageCaching($data);
	}

	public function serveCompressedData($status = false) {
		$this->load->library('wkcache/validator');
		$this->validator->getComposer();
		$this->registry->set('_cachehataccessPage', new wkcache\Htaccesspage($this->registry));
		$this->_cachehataccessPage->serveGzipData($status);
	}

	public function dashboard() {
		$this->load->library('wkcache/validator');
		$this->validator->getComposer();
		$json = array();
		if($this->request->server['REQUEST_METHOD'] == 'POST') {
			if(isset($this->request->post['api_key']) && $this->request->post['api_key']) {
				$url = "https://www.googleapis.com/pagespeedonline/v4/runPagespeed?url=";

				if($this->request->server['HTTPS']) {
					$url .= HTTPS_CATALOG;
				} else {
					$url .= HTTP_CATALOG;
				}

				$url .= "&key=".$this->request->post['api_key'];


				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HEADER, false);
				$response = curl_exec($curl);
				curl_close($curl);

				$response = json_decode($response, true);
				if($response && isset($response['responseCode']) && $response['responseCode'] == 200) {
					$json['success'] = true;
					$json['msg'] = "Got the response";
					$json['result'] = $response;
				} else {
					$json['success'] = false;
					$json['msg'] = "Got no response!";
				}

			} else {
				$json['success'] = false;
				$json['msg'] = "Did not get api key!";
			}
		} else {
			$json['success'] = false;
			$json['msg'] = "There is some issue, please try again later!";
		}
		$this->response->setOutput(json_encode($json));
	}

	public function clearCache(){
		$this->load->library('wkcache/validator');
		$this->validator->getComposer();
		$webkulcache = $this->load->library('cart/webkulcache');
		$json = array();

		if(isset($this->request->get['cache_index']) && $this->request->get['cache_index']){
			$this->load->language('extension/module/oc_cache');
			$lang_store_id = '.'.(int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id');
			$getCacheInstance 	= $this->webkulcache->get_InstanceCache('files');
			$CachedString     	= $getCacheInstance->getItem($this->request->get['cache_index']);
            $cache_index_container 	= $CachedString->get($this->request->get['cache_index'].$lang_store_id);

			$status = $getCacheInstance->deleteItem($this->request->get['cache_index']);

            if(isset($status) && $status == 1) {
            	$this->session->data['success'] = $this->language->get('text_success_cache_clear');
            	$json['success'] = true;
            }else{
            	$json['error'] = $this->language->get('error_clear_cache');
            }
		}
		$this->response->setOutput(json_encode($json));
	}

	function delete_assets($dirname) {
		if (is_dir($dirname)) {
			$dir_handle = opendir($dirname);
			if (!$dir_handle) {
				return false;
			}	
			while($file = readdir($dir_handle)) {
				if ($file != "." && $file != "..") {
					 if (!is_dir($dirname."/".$file))
						  unlink($dirname."/".$file);
					 else
					 SELF::delete_assets($dirname.'/'.$file);
				}
		  }
		  closedir($dir_handle);
		  return true;	 
		}
}

	public function deleteCache(){
		$this->load->library('wkcache/validator');
		$this->validator->getComposer();
		$json = array();

		if(isset($this->request->get['cache_option']) && $this->request->get['cache_option']){
			$webkulcache = $this->load->library('cart/webkulcache');
			$this->load->language('extension/module/oc_cache');

			if($this->request->get['cache_option'] == 'ocmod'){
				if (!$this->user->hasPermission('modify', 'marketplace/modification')) {
					$json['warning'] = $this->language->get('error_permission');
				}
				if(!$json){
					$json['success'] = $this->session->data['success'] = $this->clearOcmod();
				}
			}

			if($this->request->get['cache_option'] == 'log_file'){
				if (!$this->user->hasPermission('modify', 'marketplace/modification')) {
					$json['warning'] = $this->language->get('error_permission');
				}
				if(!$json){
					$json['success'] = $this->session->data['success'] = $this->clearLog();
				}
			}

			if($this->request->get['cache_option'] == 'image'){
				if (!$this->user->hasPermission('modify', 'marketplace/modification')) {
					$json['warning'] = $this->language->get('error_permission');
				}
				if(!$json){
					$json['success'] = $this->session->data['success'] = $this->clearImages();
				}
			}

			if($this->request->get['cache_option'] == 'all_store'){
				$this->webkulcache->clearCacheFiles('files');
				$this->clearOcmod();
				$this->clearLog();
				$this->clearImages();
				$json['success'] = $this->session->data['success'] = $this->language->get('text_success_all_store');
			}
		}

		$this->response->setOutput(json_encode($json));
	}

  public function getStoreTimeZone(){
			$this->load->library('wkcache/validator');
			$this->validator->getComposer();
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, 'http://ip-api.com/json');
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
			$content = curl_exec($curl);
			$customer_time_zone = json_decode($content);
			curl_close($curl);

			return $customer_time_zone->timezone;
	}

	public function clearOcmod(){
		$this->load->library('wkcache/validator');
		$this->validator->getComposer();
		$files = array();
			// Make path into an array
			$path = array(DIR_MODIFICATION . '*');

			// While the path array is still populated keep looping through
			while (count($path) != 0) {
				$next = array_shift($path);

				foreach (glob($next) as $file) {
					// If directory add to path array
					if (is_dir($file)) {
						$path[] = $file . '/*';
					}
					// Add the file to the files to be deleted array
					$files[] = $file;
				}
			}
			// Reverse sort the file array
			rsort($files);

			// Clear all modification files
			foreach ($files as $file) {
				if ($file != DIR_MODIFICATION . 'index.html') {
					// If file just delete
					if (is_file($file)) {
						unlink($file);

					// If directory use the remove directory function
					} elseif (is_dir($file)) {
						rmdir($file);
					}
				}
			}
		return $this->language->get('text_success_ocmod');
	}

	public function clearLog(){
		$this->load->library('wkcache/validator');
		$this->validator->getComposer();
		$handle = fopen(DIR_LOGS . 'ocmod.log', 'w+');
		fclose($handle);
		return $this->language->get('text_success_log');
	}

	public function clearImages(){
		$this->load->library('wkcache/validator');
		$this->validator->getComposer();
		$files = array();
		$path = array(DIR_IMAGE.'/cache/' . '*');

		// While the path array is still populated keep looping through
		while (count($path) != 0) {
			$next = array_shift($path);

			foreach (glob($next) as $file) {
				// If directory add to path array
				if (is_dir($file)) {
					$path[] = $file . '/*';
				}
				// Add the file to the files to be deleted array
				$files[] = $file;
			}
		}
		// Reverse sort the file array
		rsort($files);
		// Clear all images files
		foreach ($files as $file) {
			if ($file != DIR_IMAGE . 'index.html') {
				// If file just delete
				if (is_file($file)) {
					unlink($file);

				// If directory use the remove directory function
				} elseif (is_dir($file)) {
					rmdir($file);
				}
			}
		}
		return $this->language->get('text_success_image');
	}
	protected function getTimeZone(){
		return array(
			'Kwajalein' => '(GMT-12:00) International Date Line West',
			'Pacific/Midway' => '(GMT-11:00) Midway Island',
			'Pacific/Samoa' => '(GMT-11:00) Samoa',
			'Pacific/Honolulu' => '(GMT-10:00) Hawaii',
			'America/Anchorage' => '(GMT-09:00) Alaska',
			'America/Los_Angeles' => '(GMT-08:00) Pacific Time (US &amp; Canada)',
			'America/Tijuana' => '(GMT-08:00) Tijuana, Baja California',
			'America/Denver' => '(GMT-07:00) Mountain Time (US &amp; Canada)',
			'America/Chihuahua' => '(GMT-07:00) Chihuahua',
			'America/Mazatlan' => '(GMT-07:00) Mazatlan',
			'America/Phoenix' => '(GMT-07:00) Arizona',
			'America/Regina' => '(GMT-06:00) Saskatchewan',
			'America/Tegucigalpa' => '(GMT-06:00) Central America',
			'America/Chicago' => '(GMT-06:00) Central Time (US &amp; Canada)',
			'America/Mexico_City' => '(GMT-06:00) Mexico City',
			'America/Monterrey' => '(GMT-06:00) Monterrey',
			'America/New_York' => '(GMT-05:00) Eastern Time (US &amp; Canada)',
			'America/Bogota' => '(GMT-05:00) Bogota',
			'America/Lima' => '(GMT-05:00) Lima',
			'America/Rio_Branco' => '(GMT-05:00) Rio Branco',
			'America/Indiana/Indianapolis' => '(GMT-05:00) Indiana (East)',
			'America/Caracas' => '(GMT-04:30) Caracas',
			'America/Halifax' => '(GMT-04:00) Atlantic Time (Canada)',
			'America/Manaus' => '(GMT-04:00) Manaus',
			'America/Santiago' => '(GMT-04:00) Santiago',
			'America/La_Paz' => '(GMT-04:00) La Paz',
			'America/St_Johns' => '(GMT-03:30) Newfoundland',
			'America/Argentina/Buenos_Aires' => '(GMT-03:00) Georgetown',
			'America/Sao_Paulo' => '(GMT-03:00) Brasilia',
			'America/Godthab' => '(GMT-03:00) Greenland',
			'America/Montevideo' => '(GMT-03:00) Montevideo',
			'Atlantic/South_Georgia' => '(GMT-02:00) Mid-Atlantic',
			'Atlantic/Azores' => '(GMT-01:00) Azores',
			'Atlantic/Cape_Verde' => '(GMT-01:00) Cape Verde Is.',
			'Europe/Dublin' => '(GMT) Dublin',
			'Europe/Lisbon' => '(GMT) Lisbon',
			'Europe/London' => '(GMT) London',
			'Africa/Monrovia' => '(GMT) Monrovia',
			'Atlantic/Reykjavik' => '(GMT) Reykjavik',
			'Africa/Casablanca' => '(GMT) Casablanca',
			'Europe/Belgrade' => '(GMT+01:00) Belgrade',
			'Europe/Bratislava' => '(GMT+01:00) Bratislava',
			'Europe/Budapest' => '(GMT+01:00) Budapest',
			'Europe/Ljubljana' => '(GMT+01:00) Ljubljana',
			'Europe/Prague' => '(GMT+01:00) Prague',
			'Europe/Sarajevo' => '(GMT+01:00) Sarajevo',
			'Europe/Skopje' => '(GMT+01:00) Skopje',
			'Europe/Warsaw' => '(GMT+01:00) Warsaw',
			'Europe/Zagreb' => '(GMT+01:00) Zagreb',
			'Europe/Brussels' => '(GMT+01:00) Brussels',
			'Europe/Copenhagen' => '(GMT+01:00) Copenhagen',
			'Europe/Madrid' => '(GMT+01:00) Madrid',
			'Europe/Paris' => '(GMT+01:00) Paris',
			'Africa/Algiers' => '(GMT+01:00) West Central Africa',
			'Europe/Amsterdam' => '(GMT+01:00) Amsterdam',
			'Europe/Berlin' => '(GMT+01:00) Berlin',
			'Europe/Rome' => '(GMT+01:00) Rome',
			'Europe/Stockholm' => '(GMT+01:00) Stockholm',
			'Europe/Vienna' => '(GMT+01:00) Vienna',
			'Europe/Minsk' => '(GMT+02:00) Minsk',
			'Africa/Cairo' => '(GMT+02:00) Cairo',
			'Europe/Helsinki' => '(GMT+02:00) Helsinki',
			'Europe/Riga' => '(GMT+02:00) Riga',
			'Europe/Sofia' => '(GMT+02:00) Sofia',
			'Europe/Tallinn' => '(GMT+02:00) Tallinn',
			'Europe/Vilnius' => '(GMT+02:00) Vilnius',
			'Europe/Athens' => '(GMT+02:00) Athens',
			'Europe/Bucharest' => '(GMT+02:00) Bucharest',
			'Europe/Istanbul' => '(GMT+02:00) Istanbul',
			'Asia/Jerusalem' => '(GMT+02:00) Jerusalem',
			'Asia/Amman' => '(GMT+02:00) Amman',
			'Asia/Beirut' => '(GMT+02:00) Beirut',
			'Africa/Windhoek' => '(GMT+02:00) Windhoek',
			'Africa/Harare' => '(GMT+02:00) Harare',
			'Asia/Kuwait' => '(GMT+03:00) Kuwait',
			'Asia/Riyadh' => '(GMT+03:00) Riyadh',
			'Asia/Baghdad' => '(GMT+03:00) Baghdad',
			'Africa/Nairobi' => '(GMT+03:00) Nairobi',
			'Asia/Tbilisi' => '(GMT+03:00) Tbilisi',
			'Europe/Moscow' => '(GMT+03:00) Moscow',
			'Europe/Volgograd' => '(GMT+03:00) Volgograd',
			'Asia/Tehran' => '(GMT+03:30) Tehran',
			'Asia/Muscat' => '(GMT+04:00) Muscat',
			'Asia/Baku' => '(GMT+04:00) Baku',
			'Asia/Yerevan' => '(GMT+04:00) Yerevan',
			'Asia/Yekaterinburg' => '(GMT+05:00) Ekaterinburg',
			'Asia/Karachi' => '(GMT+05:00) Karachi',
			'Asia/Tashkent' => '(GMT+05:00) Tashkent',
			'Asia/Kolkata' => '(GMT+05:30) Calcutta',
			'Asia/Colombo' => '(GMT+05:30) Sri Jayawardenepura',
			'Asia/Katmandu' => '(GMT+05:45) Kathmandu',
			'Asia/Dhaka' => '(GMT+06:00) Dhaka',
			'Asia/Almaty' => '(GMT+06:00) Almaty',
			'Asia/Novosibirsk' => '(GMT+06:00) Novosibirsk',
			'Asia/Rangoon' => '(GMT+06:30) Yangon (Rangoon)',
			'Asia/Krasnoyarsk' => '(GMT+07:00) Krasnoyarsk',
			'Asia/Bangkok' => '(GMT+07:00) Bangkok',
			'Asia/Jakarta' => '(GMT+07:00) Jakarta',
			'Asia/Brunei' => '(GMT+08:00) Beijing',
			'Asia/Chongqing' => '(GMT+08:00) Chongqing',
			'Asia/Hong_Kong' => '(GMT+08:00) Hong Kong',
			'Asia/Urumqi' => '(GMT+08:00) Urumqi',
			'Asia/Irkutsk' => '(GMT+08:00) Irkutsk',
			'Asia/Ulaanbaatar' => '(GMT+08:00) Ulaan Bataar',
			'Asia/Kuala_Lumpur' => '(GMT+08:00) Kuala Lumpur',
			'Asia/Singapore' => '(GMT+08:00) Singapore',
			'Asia/Taipei' => '(GMT+08:00) Taipei',
			'Australia/Perth' => '(GMT+08:00) Perth',
			'Asia/Seoul' => '(GMT+09:00) Seoul',
			'Asia/Tokyo' => '(GMT+09:00) Tokyo',
			'Asia/Yakutsk' => '(GMT+09:00) Yakutsk',
			'Australia/Darwin' => '(GMT+09:30) Darwin',
			'Australia/Adelaide' => '(GMT+09:30) Adelaide',
			'Australia/Canberra' => '(GMT+10:00) Canberra',
			'Australia/Melbourne' => '(GMT+10:00) Melbourne',
			'Australia/Sydney' => '(GMT+10:00) Sydney',
			'Australia/Brisbane' => '(GMT+10:00) Brisbane',
			'Australia/Hobart' => '(GMT+10:00) Hobart',
			'Asia/Vladivostok' => '(GMT+10:00) Vladivostok',
			'Pacific/Guam' => '(GMT+10:00) Guam',
			'Pacific/Port_Moresby' => '(GMT+10:00) Port Moresby',
			'Asia/Magadan' => '(GMT+11:00) Magadan',
			'Pacific/Fiji' => '(GMT+12:00) Fiji',
			'Asia/Kamchatka' => '(GMT+12:00) Kamchatka',
			'Pacific/Auckland' => '(GMT+12:00) Auckland',
			'Pacific/Tongatapu' => '(GMT+13:00) Nukualofa'
		);
	}
}
