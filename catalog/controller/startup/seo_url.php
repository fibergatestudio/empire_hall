<?php
class ControllerStartupSeoUrl extends Controller {
	public function index() {
		// Add rewrite to url class
		if ($this->config->get('config_seo_url')) {
			$this->url->addRewrite($this);
		}

		// Decode URL
		if (!isset($this->request->get['_route_'])) {
			$this->validateNoRoute();
		} else {
			$parts = explode('/', $this->request->get['_route_']);

			// remove any empty arrays from trailing
			if (utf8_strlen(end($parts)) == 0) {
				array_pop($parts);
			}

			foreach ($parts as $part) {

				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE keyword = '" . $this->db->escape($part) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");
				if ($query->num_rows) {

					$url = explode('=', $query->row['query']);

					if ($url[0] == 'product_id') {
						$this->request->get['product_id'] = $url[1];
					}

					if ($url[0] == 'category_id') {
						if (!isset($this->request->get['path'])) {
							$this->request->get['path'] = $url[1];
						} else {
							$this->request->get['path'] .= '_' . $url[1];
						}
					}

                    if ($url[0] == 'collection_item_id') {
                        $this->request->get['collection_item_id'] = $url[1];
                    }

                    if ($url[0] == 'collection_id') {
                        $this->request->get['collection_id'] = $url[1];
                    }

					if ($url[0] == 'manufacturer_id') {
						$this->request->get['manufacturer_id'] = $url[1];
					}

                    if ($url[0] == 'inspiration_id') {
                        $this->request->get['inspiration_id'] = $url[1];
                    }

					if ($url[0] == 'information_id') {
						$this->request->get['information_id'] = $url[1];
					}

					if ($query->row['query'] && $url[0] != 'information_id' && $url[0] != 'manufacturer_id' && $url[0] != 'category_id' && $url[0] != 'product_id' && $url[0] != 'collection_id' && $url[0] != 'collection_item_id' && $url[0] != 'inspiration_id') {
						$this->request->get['route'] = $query->row['query'];
					}

					if (isset($this->request->get['route']) && isset($this->request->get['manufacturer_id']) && $this->request->get['route'] == 'product/collection') {
                        $this->request->get['brand_id'] = $this->request->get['manufacturer_id'];
                        unset($this->request->get['manufacturer_id']);
                    }

				} else {
					$this->request->get['route'] = 'extension/tltblog/tltblog_seo';

					break;
				}
			}

			if (!isset($this->request->get['route'])) {
				if (isset($this->request->get['product_id'])) {
					$this->request->get['route'] = 'product/product';
				} elseif (isset($this->request->get['path'])) {
					$this->request->get['route'] = 'product/category';
				} elseif (isset($this->request->get['manufacturer_id'])) {
                    if(isset($this->request->get['collection_id'])){
                        $this->request->get['route'] = 'product/manufacturer/infoCollection';
                    }else{
                        $this->request->get['route'] = 'product/manufacturer/info';
                    }
					//$this->request->get['route'] = 'product/manufacturer/info';
				} elseif (isset($this->request->get['information_id'])) {
					$this->request->get['route'] = 'information/information';
				} elseif (isset($this->request->get['inspiration_id'])) {
                    $this->request->get['route'] = 'product/inspiration/info';
                }
			}

            if(isset($this->request->get['route']) && $this->request->get['route'] == 'product/manufacturer/infoCollection' && empty($this->request->get['collection_id']) && !empty($this->request->get['collection_item_id'])){
                $collection_item_id = $this->request->get['collection_item_id'];
                $query = $this->db->query("SELECT `collection_id` FROM " . DB_PREFIX . "collection_item_to_category WHERE `collection_item_id` = $collection_item_id");
                if($query->num_rows){
                    $this->request->get['collection_id'] = $query->row['collection_id'];
                }
            }

			$this->validate();
		}
	}

	public function rewrite($link) {
		$url_info = parse_url(str_replace('&amp;', '&', $link));

		$url = '';

		$data = array();

		parse_str($url_info['query'], $data);

		foreach ($data as $key => $value) {
			if (isset($data['route'])) {
				if (($data['route'] == 'product/manufacturer/info' && $key == 'manufacturer_id') || ($data['route'] == 'information/information' && $key == 'information_id')) {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

					if ($query->num_rows && $query->row['keyword']) {
						$url .= '/' . $query->row['keyword'];

						unset($data[$key]);
					}
				} elseif (($data['route'] == 'product/product' && $key == 'product_id')) {
					if ($this->config->get('config_seo_url_include_path')) {
						$path = $this->getPathByProduct($value);

						$url = '';
						if ($path) {
							foreach (explode('_', (string)$path) as $cat_id) {
								$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape('category_id=' . (int)$cat_id) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

								if ($query->num_rows && $query->row['keyword']) {
									$url .= '/' . $query->row['keyword'];
								}

							}

							$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
							if ($query->num_rows && $query->row['keyword']) {
								$url .= '/' . $query->row['keyword'];

								unset($data[$key]);
							}
						}
					}

				} elseif ($key == 'path') {

					$category = explode('_', $value);
					$category = end($category);
					$path = $this->getPathByCategory($category);
					$url = '';
					if ($path) {
						foreach (explode('_', (string)$path) as $cat_id) {
							$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape('category_id=' . (int)$cat_id) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
							if ($query->num_rows && $query->row['keyword']) {
								$url .= '/' . $query->row['keyword'];
							} else {
								$url = '';

								break;
							}
						}
						unset($data[$key]);
					}

					unset($data[$key]);
				} elseif ($data['route'] == 'product/collection') {
                    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($data['route']) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
                    if ($query->num_rows && $query->row['keyword']) {
                        $url = '/' . $query->row['keyword'];
                        if(isset($data['collection_item_id']) && $data['collection_item_id']){
                            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape('collection_item_id=' . (int)$data['collection_item_id']) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
                            if ($query->num_rows && $query->row['keyword']) {
                                $url .= '/' . $query->row['keyword'];
                                unset($data['collection_item_id']);
                            }
                        }
                        if(isset($data['collection_id']) && $data['collection_id']){
                            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape('collection_id=' . (int)$data['collection_id']) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
                            if ($query->num_rows && $query->row['keyword']) {
                                $url .= '/' . $query->row['keyword'];
                                unset($data['collection_id']);
                            }
                        }
                        if(isset($data['brand_id']) && $data['brand_id']){
                            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape('manufacturer_id=' . (int)$data['brand_id']) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
                            if ($query->num_rows && $query->row['keyword']) {
                                $url .= '/' . $query->row['keyword'];
                                unset($data['brand_id']);
                            }
                        }
                    } else if ($data['route'] == 'common/home') {
                        $url = '/';
                    } else {
                        $url = '';
                    }
                    unset($data[$key]);
                }  elseif ($data['route'] == 'product/manufacturer/infoCollection') {
                    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($data['route']) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
                    if ($query->num_rows && $query->row['keyword']) {

                        if(isset($data['manufacturer_id']) && $data['manufacturer_id']){
                            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape('manufacturer_id=' . (int)$data['manufacturer_id']) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
                            if ($query->num_rows && $query->row['keyword']) {
                                $url = '/' . $query->row['keyword'];
                                unset($data['manufacturer_id']);
                            }
                        }
                        
//                        if(isset($data['collection_id']) && $data['collection_id']){
                        if(!empty($data['collection_id']) || !empty($data['collection_item_id'])){
                            $url .= '/collection';
                        }

                    } else if ($data['route'] == 'common/home') {
                        $url = '/';
                    } else {
                        $url = '';
                    }
                    unset($data[$key]);
                } elseif ($data['route'] == 'product/inspiration/info') {
                    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($data['route']) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
                    if ($query->num_rows && $query->row['keyword']) {
                        $url = '/' . $query->row['keyword'];
                        if(isset($data['inspiration_id']) && $data['inspiration_id']){
                            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape('inspiration_id=' . (int)$data['inspiration_id']) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
                            if ($query->num_rows && $query->row['keyword']) {
                                $url .= '/' . $query->row['keyword'];
                                unset($data['inspiration_id']);
                            }
                        }
                    } else if ($data['route'] == 'common/home') {
                        $url = '/';
                    } else {
                        $url = '';
                    }

                    unset($data[$key]);
                } else {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($data['route']) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
					if ($query->num_rows && $query->row['keyword']) {
						$url = '/' . $query->row['keyword'];
					} else if ($data['route'] == 'common/home') {
						$url = '/';
					} else {
						$url = '';
					}
				}
			}
		}


		if ($url) {
			unset($data['route']);

			$query = '';

			if ($data) {
				foreach ($data as $key => $value) {
					$query .= '&' . rawurlencode((string)$key) . '=' . rawurlencode((is_array($value) ? http_build_query($value) : (string)$value));
				}

				if ($query) {
					$query = '?' . str_replace('&', '&amp;', trim($query, '&'));
				}
			}

			$resultUrl = $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url . $query;

			return $resultUrl;
		} else {
			return $link;
		}
	}

	private function getPathByProduct($product_id) {
		$product_id = (int)$product_id;
		if ($product_id < 1) return false;

		static $path = null;
		if (!isset($path)) {
			$path = $this->cache->get('product.seopath');
			if (!isset($path)) $path = array();
		}

		if (!isset($path[$product_id])) {
			$query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . $product_id . "' ORDER BY main_category DESC LIMIT 1");

			$path[$product_id] = $this->getPathByCategory($query->num_rows ? (int)$query->row['category_id'] : 0);

			$this->cache->set('product.seopath', $path);
		}

		return $path[$product_id];
	}

	private function getPathByCategory($category_id) {
		$category_id = (int)$category_id;
		if ($category_id < 1) return false;

		static $path = null;
		if (!isset($path)) {
			$path = $this->cache->get('category.seopath');
			if (!isset($path)) $path = array();
		}

		if (!isset($path[$category_id])) {
			$max_level = 10;

			$sql = "SELECT CONCAT_WS('_'";
			for ($i = $max_level-1; $i >= 0; --$i) {
				$sql .= ",t$i.category_id";
			}
			$sql .= ") AS path FROM " . DB_PREFIX . "category t0";
			for ($i = 1; $i < $max_level; ++$i) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "category t$i ON (t$i.category_id = t" . ($i-1) . ".parent_id)";
			}
			$sql .= " WHERE t0.category_id = '" . $category_id . "'";

			$query = $this->db->query($sql);

			$path[$category_id] = $query->num_rows ? $query->row['path'] : false;

			$this->cache->set('category.seopath', $path);
		}

		return $path[$category_id];
	}

	private function validate()
	{
		if (isset($this->request->get['route']) && $this->request->get['route'] == 'error/not_found') {
			return;
		}
		if (ltrim($this->request->server['REQUEST_URI'], '/') =='sitemap.xml') {
			$this->request->get['route'] = 'feed/google_sitemap';
			return;
		}

		if(empty($this->request->get['route'])) {
			$this->request->get['route'] = 'common/home';
		}

		if (isset($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			return;
		}

		if (isset($this->request->get['_route_'])) {

			if (isset($this->request->get['product_id'])) {
				if (isset($this->request->get['path'])) {

					if ($this->request->get['path'] != $this->getPathByProduct($this->request->get['product_id'])) {
						$this->response->redirect($this->url->link('product/product','path=' . $this->getPathByProduct($this->request->get['product_id']) . '&product_id=' . $this->request->get['product_id'], true));
					}
				} else {
					if ($this->getPathByProduct($this->request->get['product_id'])) {
						$this->response->redirect($this->url->link('product/product',
							'path=' . $this->getPathByProduct($this->request->get['product_id']) . '&product_id=' . $this->request->get['product_id'], true));
					}
				}
			}

			if (isset($this->request->get['path']) && !isset($this->request->get['product_id'])) {
				$category = explode('_', $this->request->get['path']);
				$category = end($category);
				if ($this->request->get['path'] != $this->getPathByCategory($category) && !isset($this->request->get['filter_ocfilter'])) {
					$this->response->redirect($this->url->link('product/category',
						'path=' . $this->getPathByCategory($category), true));
				} elseif ($this->request->get['path'] != $this->getPathByCategory($category) && isset($this->request->get['filter_ocfilter'])) {
					$this->response->redirect($this->url->link('product/category',
						'path=' . $this->getPathByCategory($category) . '&filter_ocfilter='.$this->request->get['filter_ocfilter'], true));
				}
			}
		}
	}

	private function validateNoRoute()
	{
		if (isset($this->request->get['route']) && $this->request->get['route'] == 'error/not_found') {
			return;
		}
		if (ltrim($this->request->server['REQUEST_URI'], '/') =='sitemap.xml') {
			$this->request->get['route'] = 'feed/google_sitemap';
			return;
		}

		if(empty($this->request->get['route'])) {
			$this->request->get['route'] = 'common/home';
		}

		if (isset($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			return;
		}

		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$config_ssl = substr($this->config->get('config_ssl'), 0, $this->strpos_offset('/', $this->config->get('config_ssl'), 3) + 1);
			$url = str_replace('&amp;', '&', $config_ssl . ltrim($this->request->server['REQUEST_URI'], '/'));
			$seo = str_replace('&amp;', '&', $this->url->link($this->request->get['route'], $this->getQueryString(array('route')), true));
		} else {
			$config_url = substr($this->config->get('config_url'), 0, $this->strpos_offset('/', $this->config->get('config_url'), 3) + 1);
			$url = str_replace('&amp;', '&', $config_url . ltrim($this->request->server['REQUEST_URI'], '/'));
			$seo = str_replace('&amp;', '&', $this->url->link($this->request->get['route'], $this->getQueryString(array('route')), false));
		}

		if (rawurldecode($url) != rawurldecode($seo)) {
			header($this->request->server['SERVER_PROTOCOL'] . ' 302 Found');
			$this->response->redirect($seo);
		}
	}

	private function strpos_offset($needle, $haystack, $occurrence) {
		// explode the haystack
		$arr = explode($needle, $haystack);
		// check the needle is not out of bounds
		switch($occurrence) {
			case $occurrence == 0:
				return false;
			case $occurrence > max(array_keys($arr)):
				return false;
			default:
				return strlen(implode($needle, array_slice($arr, 0, $occurrence)));
		}
	}

	private function getQueryString($exclude = array()) {
		if (!is_array($exclude)) {
			$exclude = array();
		}

		return urldecode(http_build_query(array_diff_key($this->request->get, array_flip($exclude))));
	}

}
