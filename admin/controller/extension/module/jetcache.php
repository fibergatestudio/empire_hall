<?php
/* All rights reserved belong to the module, the module developers http://opencartadmin.com */
// https://opencartadmin.com © 2011-2019 All Rights Reserved
// Distribution, without the author's consent is prohibited
// Commercial license
if (!class_exists('ControllerExtensionModuleJetcache', false)) {
	class ControllerExtensionModuleJetcache extends Controller {
		private $error = array();
		public function index() {
				$this->control('jetcache/jetcache');
				$this->controller_jetcache_jetcache->index($this->registry);
		}
		public function uninstall() {
			if ($this->validate()) {
				$this->control('jetcache/jetcache');
				$this->controller_jetcache_jetcache->uninstall($this->registry);
			}
		}
		public function install() {
			$this->control('jetcache/jetcache');
			$this->controller_jetcache_jetcache->install($this->registry);
		}
		protected function validate() {
			if (!$this->user->hasPermission('modify', 'extension/module/jetcache')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}
			return !$this->error;
		}
		public function control($cont) {
			$file = DIR_APPLICATION . 'controller/' . $cont . '.php';
			$class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $cont);
			if (file_exists($file)) {
				include_once($file);
				$this->registry->set('controller_' . str_replace('/', '_', $cont), new $class($this->registry));
			} else {
				trigger_error('Error: Could not load controller ' . $cont . '!');
				exit();
			}
		}
 	}
}
