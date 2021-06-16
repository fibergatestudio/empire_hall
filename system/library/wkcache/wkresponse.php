<?php
namespace Wkcache;

/**
 * @package		Webkul Cahce
 * @author		Webkul
 * @copyright	Copyright (c) 20011 - 2020, Webkul Soft, Ltd. (https://www.webkul.com/)
 * @license		https://webkul.com
 * @link		https://www.webkul.com
*/
class Wkresponse {
	private $headers = array();
	private $level = 0;
	private $output;

	public function addHeader($header) {
		$this->headers[] = $header;
	}

	/**
	 *
	 *
	 * @param	string	$output
 	*/
	 public function setOutput($output) {
		global $registry;

		$this->config = $registry->get('config');
		
		if(!$this->config->get('module_oc_cache_status')) {
			return $output;	
		}		

        if(!defined('DIR_CATALOG')) {
			// $registry->set('request', new \Request($registry));
			// $this->request = $registry->get('request');
			$this->registry = $registry;

      if (isset($_SERVER['REQUEST_URI'])) {

				require_once(DIR_SYSTEM.'library/wkcache/page.php');

				$page = new \Wkcache\Page($this->registry);

				$exclude  = array(
					'checkout/cart',
					'checkout/cart/add',
					'checkout/cart/remove',
					'common/cart/info',
				);

				$key = str_replace('/','-', $_SERVER['REQUEST_URI']);

				$key = $page->customer_id.'-'.$key;

				$cache = $page->get($key);

				$ignored_route = explode("\n",$this->config->get('module_oc_cache_ignore_route'));
				$ignored_route = array_map("trim", $ignored_route);
				$ignored_route = array_map("html_entity_decode", $ignored_route);

				foreach ($ignored_route as $k=>$ignore) {
					if($ignore != '')
					if (strpos($_SERVER['REQUEST_URI'],$ignore) != false) {
						return $output;
					}
				}

        require_once(DIR_SYSTEM.'library/wkcache/tiny-html-minifier.php');

				$minifier = new \Wkcache\TinyHtmlMinifier();

				if (is_null($cache->get()) && !preg_match("/account\/|checkout\/|common\/cart\/info/", $_SERVER['REQUEST_URI']) && !preg_match("/api\/wkrestapi/",$_SERVER['REQUEST_URI'])) {
				
					$output = $minifier->fn_minify_html($output);
					// $output = str_replace('<link', '<link async rel="dns-prefetch" ', $output);
					// $output = str_replace('rel="stylesheet"', 'rel="stylesheet" disabled ', $output);
					$output = str_replace('rel="import"', 'rel="import" async ', $output);
					//$output = str_replace('<script', '<script async defer ', $output);

	        $page->set($key,$output);

		} else if (preg_match("/account\/|checkout\/|common\/cart\/info/",  $_SERVER['REQUEST_URI']) || preg_match("/api\/wkrestapi/",$_SERVER['REQUEST_URI'])){
					//$output = $minifier->fn_minify_html($output);
					// $output = str_replace('<link', '<link async rel="dns-prefetch" ', $output);
					// $output = str_replace('rel="stylesheet"', 'rel="stylesheet" disabled ', $output);
					$output = str_replace('rel="import"', 'rel="import" async ', $output);
					//$output = str_replace('<script', '<script async defer ', $output);
					if (!headers_sent()) {
						foreach ($this->headers as $header) {
							header($header, true);
						}
					}

					//$page->set($key,$output);
				}
			}
		}

		return $output;
	}
	/**
	 *
	 *
	 * @param	string	$data
	 * @param	int		$level
	 *
	 * @return	string
 	*/
	private function compress($data, $level = 0) {
		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false)) {
			$encoding = 'gzip';
		}

		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false)) {
			$encoding = 'x-gzip';
		}

		if (!isset($encoding) || ($level < -1 || $level > 9)) {
			return $data;
		}

		if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
			return $data;
		}

		if (headers_sent()) {
			return $data;
		}

		if (connection_status()) {
			return $data;
		}

		$this->addHeader('Content-Encoding: ' . $encoding);

		return gzencode($data, (int)$level);
	}
}
