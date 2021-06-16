<?php

class ControllerCommonOcCache extends Controller {
	public function index() {

		$this->load->library('wkcache/validator');
		$this->validator->chkComposer();

		$this->load->model('extension/module/oc_cache');
		$this->document->addScript('catalog/view/javascript/cache_panel.css');
	
		if($this->config->get('module_oc_cache_status') && $this->config->get('module_oc_cache_frontpanel_status')){

			$data['clear_cache_status'] = $this->config->get('module_oc_cache_frontpanel_clear_status');
			$data['cache_message'] 		= $this->config->get('module_oc_cache_frontpanel_cachemsg');
			$data['cache_background'] 	= $this->config->get('module_oc_cache_frontpanel_backgroundcolor');
			$data['cache_color'] 		= $this->config->get('module_oc_cache_frontpanel_color');
			$data['cache_expiretime_show'] = $this->config->get('module_oc_cache_frontpanel_expiretime');
			$data['cache_position'] 	= $this->config->get('module_oc_cache_frontpanel_position');

			$getDriverStats 			= $this->webkulcache->getStats('files');

			if (!$this->config->get('config_theme') === 'journal3' || !$this->config->get('config_theme') === 'theme_journal3' || !$this->config->get('config_template') === 'journal3') {
		   	 // $this->document->addScript("catalog/view/javascript/jquery.lwtCountdown-1.0.js");
		  }

			$data['expire_date'] 		= (array)$getDriverStats['expire_time'];
			// $data['time_script'] 		=  '';
			if(isset($_COOKIE['demo_timezone'])){
				$getCustomerTimeZone 		= $_COOKIE['demo_timezone'];
			}else{
				$getCustomerTimeZone 		= $this->model_extension_module_oc_cache->getCustomerTimeZone();
			}


			$today = date("Y-m-d H:i:s");
			$data['expire_date']['date'] = date("Y-m-d H:i:s",strtotime($today) + $this->config->get('module_oc_cache_page_cache_expiry'));

			$cache_clear_time 			= new DateTime($data['expire_date']['date'], new DateTimeZone($this->config->get('module_oc_cache_timezone')));
			$cache_clear_time->setTimezone(new DateTimeZone($getCustomerTimeZone));

			if(isset($this->session->data['expire_zero_set']) && $this->session->data['expire_zero_set']){
				$cache_clear_time 			= new DateTime(null,new DateTimeZone($this->config->get('module_oc_cache_timezone')));
				$data['cache_clear_date_time'] = $cache_clear_time->format('Y-m-d H:i:s');
				$data['cache_clear_date_time_twig'] = $cache_clear_time->format('Y/m/d');

				unset($this->session->data['expire_zero_set']);
			}else{
				$data['cache_clear_date_time'] = $cache_clear_time->format('Y-m-d H:i:s');
				$data['cache_clear_date_time_twig'] = $cache_clear_time->format('Y/m/d');
			}

			return $this->load->view('common/oc_cache_panel', $data);
		}else{
			return false;
		}
	}

	public function clear(){
		$this->load->library('wkcache/validator');
		$this->validator->chkComposer();
		$page = $this->load->library('wkcache/page');
		$this->page->clearCache();
		$getCacheDirectory = $this->webkulcache->clearCacheFiles('files');
		SELF::delete_assets(DIR_STORAGE . 'wkcache');
		SELF::delete_assets(DIR_APPLICATION . 'view/wkcache/assets');
		// if($getCacheDirectory){
		// 	$this->session->data['expire_zero_set'] = true;
		// }
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode(true));
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
}
