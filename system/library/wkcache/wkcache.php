<?php
// use WkCache\WkCache;
namespace Wkcache;

use Wkcache\Htaccesspage;
use Wkcache\Combiner;
use Wkcache\Jsmin;
use Wkcache\Page;
use Wkcache\Wkdocument;
use Wkcache\Wkresponse;
use Cart\WebkulCache;


define('WEBCACHE_VERSION', '1.0.0');


final class Wkcache {

	/**
     * @var \Registry
     * */
	private $registry;

	/**
     *  @var \WkCache
     * */
	private static $instance;

	/** @var WkCache|Cache */
	public $cache;

	/** @var \WkCache\Minifier */
	public $minifier;

	/** @var \WkCache\Settings */
	public $settings;

	/** @var Document */
	public $document;
		/** @var wkresponse */
		public $wkresponse;
	
  private $language_id = null;

  private $currency_id = null;

  private $store_id = null;

  private $product_data = array();

  public function __construct($registryc) {

		if (self::$instance !== null) {
			// die('Webkul Cache Already instantiated!');
		}	
		global $registry, $config, $loader, $session;

	
			self::$instance = $this;
		
			$this->registry = $registry;
			$this->htaccess = new Htaccesspage($registry);
			$this->combiner = new Combiner($registry);
			$this->jsmin = new Jsmin($registry);
			$this->wkresponse = new Wkresponse($registry);
			$registry->set('document',new Wkdocument($this->registry));
			$registry->set('wkresponse',new Wkresponse($registry));
			$this->wkresponse =$registry->get('wkresponse');
			$registry->set('webkulcache',new WebkulCache($this->registry));
			$registry->set('pagecache',new Page($this->registry));
		
	}

    public static function getInstance() {
		return self::$instance;
	}

	public function changeOutput($output) {
		return $this->wkresponse->setOutput($output);
	}

	public function getRegistry() {
		return $this->registry;
	}

	public function getLanguageId() {
		if ($this->language_id === null) {
			$this->language_id = (int)$this->config('config_language_id');
		}

		return $this->language_id;
	}

	public function getStoreId() {
		if ($this->store_id === null) {
			$this->store_id = (int)$this->config('config_store_id');
		}

		return $this->store_id;
	}

	public function staticAssetUrl($url) {
		return self::$base_href . $url;
	}

	public function config($key) {
		return $this->registry->get('config')->get($key);
	}

	public function loadController($route, $args = array()) {
		return $this->registry->get('load')->controller($route, $args);
	}

	public function loadView($route, $data = array()) {
		return $this->registry->get('load')->view($route, $data);
    }

}
