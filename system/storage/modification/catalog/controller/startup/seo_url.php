<?php
class ControllerStartupSeoUrl extends Controller {
	public function index() {
		// Add rewrite to url class
		if ($this->config->get('config_seo_url')) {
			$this->url->addRewrite($this);

      // OCFilter start
      if ($this->registry->has('ocfilter')) {
  			$this->url->addRewrite($this->ocfilter);
  		}
      // OCFilter end
      
		}

      if ($this->config->get('mlseo_enabled')) {
        // fix &amp; issue
        if (version_compare(VERSION, '2', '<')) {
          $_SERVER['REQUEST_URI'] = str_replace('&amp;', '&', $_SERVER['REQUEST_URI']);
        }
        
        // consider index.html as homepage too
        if ($_SERVER['REQUEST_URI'] == '/index.html') {
          return new Action('common/home');
        }
        
        // HTTP redirect
        if ($this->config->get('mlseo_redirect_http') && !empty($_SERVER['HTTP_HOST'])) {
          $isSSL = !empty($_SERVER['HTTPS']);
          $isWWW = strtolower(substr($_SERVER['HTTP_HOST'], 0, 4)) == 'www.';
          
          $redirLoc = false;
          
          if ($this->config->get('mlseo_redirect_http') == 1 && ($isSSL || $isWWW)) {
            $redirLoc = 'Location: ' . urldecode('http://'.str_replace('www.', '', $_SERVER['HTTP_HOST']).$_SERVER['REQUEST_URI']);
          } else if ($this->config->get('mlseo_redirect_http') == 2 && ($isSSL || !$isWWW)) {
            $redirLoc = 'Location: ' . urldecode('http://www.'.str_replace('www.', '', $_SERVER['HTTP_HOST']).$_SERVER['REQUEST_URI']);
          } else if ($this->config->get('mlseo_redirect_http') == 3 && (!$isSSL || $isWWW)) {
            $redirLoc = 'Location: ' . urldecode('https://'.str_replace('www.', '', $_SERVER['HTTP_HOST']).$_SERVER['REQUEST_URI']);
          } else if ($this->config->get('mlseo_redirect_http') == 4 && (!$isSSL || !$isWWW)) {
            $redirLoc = 'Location: ' . urldecode('https://www.'.str_replace('www.', '', $_SERVER['HTTP_HOST']).$_SERVER['REQUEST_URI']);
          } else if ($this->config->get('mlseo_redirect_http') == 5 && (!$isSSL)) {
            $redirLoc = 'Location: ' . urldecode('https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
          } else if ($this->config->get('mlseo_redirect_http') == 6 && ($isSSL)) {
            $redirLoc = 'Location: ' . urldecode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
          } else if ($this->config->get('mlseo_redirect_http') == 7 && (!$isWWW)) {
            $redirLoc = 'Location: ' . urldecode('http'.($isSSL?'s':'').'://www.'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
          } else if ($this->config->get('mlseo_redirect_http') == 8 && ($isWWW)) {
            $redirLoc = 'Location: ' . urldecode('http'.($isSSL?'s':'').'://'.str_replace('www.', '', $_SERVER['HTTP_HOST']).$_SERVER['REQUEST_URI']);
          }
          
          if ($redirLoc) {
            header('HTTP/1.1 301 Moved Permanently');
            header('CSP-Redir: http (Mode:'.$this->config->get('mlseo_redirect_http').', SSL:'.(int)$isSSL.', WWW:'.(int)$isWWW.')', false);
            header($redirLoc);
            exit;
          }
        }
        
        // redirection manager
        if (!empty($_SERVER['HTTP_HOST'])) {
          $raw_url = 'http' . (!empty($_SERVER['HTTPS']) ? 's' : '') . '://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $raw_uri = $_SERVER['REQUEST_URI'];
          $url = urldecode($raw_url);
          $uri = urldecode($raw_uri);
          
          if ($this->config->get('mlseo_redirect_dynamic') && strpos(parse_url($uri, PHP_URL_QUERY), '_route_=') === 0) {
            $redir_url = $this->config->get('config_url') . str_replace('_route_=', '', parse_url($uri, PHP_URL_QUERY));
            header('HTTP/1.1 301 Moved Permanently');
            header('CSP-Redir: dynamic 1', false);
            header('Location: ' . str_replace('&amp;', '&', $redir_url));
          }
          
          if ($this->config->get('mlseo_redirect_dynamic') && isset($this->request->get['route']) && $this->request->get['route'] !== 'account/login' && !(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
            $redir_request = $_GET;
            $redir_route = $this->request->get['route'];
            unset($redir_request['route']);
            unset($redir_request['_route_']);
            
            if (!empty($_SERVER['HTTPS'])) {
              $redir_url = str_replace('&amp;', '&', $this->url->link($redir_route, http_build_query($redir_request, '', '&'), $_SERVER['HTTPS']));
            } else {
              $redir_url = str_replace('&amp;', '&', $this->url->link($redir_route, http_build_query($redir_request, '', '&')));
            }
            
            $redir_url = trim($redir_url);
            
            if (rtrim($redir_url, '/') != rtrim($url, '/') && !strpos($redir_url, 'route=')) {
              header('HTTP/1.1 301 Moved Permanently');
              header('CSP-Redir: dynamic 2', false);
              header('Location: ' . $redir_url); 
              exit;
            }
          }
          
          if ($this->config->get('mlseo_redirect')) {
            // regex require to be specifically defined to avoid unexpected match
            //$redirect = $this->db->query("SELECT redirect, language_id FROM " . DB_PREFIX . "url_redirect WHERE query = '" . $this->db->escape($raw_url) . "' OR query = '" . $this->db->escape($raw_uri) . "' OR ('".$this->db->escape($raw_url)."' REGEXP query) OR ('".$this->db->escape($raw_uri)."' REGEXP query) LIMIT 1")->row;
            //$redirect = $this->db->query("SELECT redirect, language_id FROM " . DB_PREFIX . "url_redirect WHERE query = '" . $this->db->escape($raw_url) . "' OR query = '" . $this->db->escape($raw_uri) . "' LIMIT 1")->row;
            //$redirect = $this->db->query("SELECT redirect, language_id FROM " . DB_PREFIX . "url_redirect WHERE query = '" . $this->db->escape(urldecode($raw_url)) . "' OR query = '" . $this->db->escape(urldecode($raw_uri)) . "' LIMIT 1")->row;
            
            $redirect = $this->db->query("SELECT redirect, language_id FROM " . DB_PREFIX . "url_redirect WHERE 
              query = '" . $this->db->escape(urldecode($raw_url)) . "' OR query = '" . $this->db->escape(urldecode($raw_uri)) . "'
              OR (redirect LIKE 'product/product&product_id=%' AND query =  '" . $this->db->escape(urldecode(substr(strrchr($raw_uri, '/'), 0))) . "') LIMIT 1")->row;
           
            if(!empty($redirect['redirect'])) {
              $lang = $redirect['language_id'];
              $redirect = $redirect['redirect'];
              
              if ($lang) {
                $this->load->model('localisation/language');
                $languagesArray = $this->model_localisation_language->getLanguages();
                
                if(count($languagesArray) > 1) {
                  $languages = array();
                  foreach ($languagesArray as $result) { $languages[$result['language_id']] = $result; }
                  $this->config->set('config_language_id', $languages[$lang]['language_id']);
                  $this->config->set('config_language', $languages[$lang]['code']);
                  $this->session->data['language'] = $languages[$lang]['code'];
                }
              }
              
              if ((substr($redirect, 0, 1) != '/') && (substr($redirect, 0, 4) != 'http')) {
                if ($params = strstr($redirect, '&')) {
                  $route = str_replace(array($params, 'index.php?route='), '', $redirect);
                } else {
                  $route = str_replace('index.php?route=', '', $redirect);
                  $params = '';
                }
                
                $redirect = str_replace('&amp;', '&', $this->url->link($route, substr(str_replace('&amp;', '&', $params), 1)));
              }
              
              if ($redirect != $url) {
                header('HTTP/1.1 301 Moved Permanently');
                header('CSP-Redir: url', false);
                header('Location: ' . $redirect);
                exit;
              }
            }
          }
        }
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

			
      // Friendly urls
      /*
      if ($this->config->get('mlseo_friendly') && !empty($parts[0])) {
        $ml_mode = '';
        
        if ($this->config->get('mlseo_multistore')) {
          $ml_mode .= " AND (`store_id` = '" . (int)$this->config->get('config_store_id') . "' OR `store_id` = 0)";
        }
        
        if ($this->config->get('mlseo_ml_mode')) {
          $ml_mode .= " AND (`language_id` = '" . (int)$this->config->get('config_language_id') . "' OR `language_id` = 0)";
        }
        
        if ($this->config->get('mlseo_multistore') && $this->config->get('mlseo_ml_mode')) {
          $ml_mode .= "ORDER BY store_id DESC, language_id DESC";
        } else if ($this->config->get('mlseo_ml_mode')) {
          $ml_mode .= "ORDER BY language_id DESC";
        } else if ($this->config->get('mlseo_multistore')) {
          $ml_mode .= "ORDER BY store_id DESC";
        }
        
        $seoUrlTable = version_compare(VERSION, '3', '>=') ? 'seo_url' : 'url_alias';
        /*
        $sk_query = $this->db->query("SELECT * FROM " . DB_PREFIX . $seoUrlTable . " WHERE query LIKE '%product/special%'")->row;
        $special_keyword = isset($sk_query['keyword']) ? $sk_query['keyword'] : '';
        
        if ($special_keyword && strpos($this->request->get['_route_'], $special_keyword) !== false) {
          $special_parts = explode('/', $this->request->get['_route_']);
          $this->request->get['_route_'] = $special_keyword;
          
          if (!empty($special_parts[1]) && strpos($special_parts[1], 'page-')!==false) {
            $this->request->get['page'] = str_replace('page-','',$special_parts[1]);
          }
        }
        *
        
        $friendly_qry = $this->db->query("SELECT * FROM " . DB_PREFIX . $seoUrlTable . " WHERE query LIKE 'route=%' AND keyword = '" . $this->db->escape($parts[0]) . "' " . $ml_mode);
        
        if (!empty($friendly_qry->row['query'])) {
          $this->request->get['route'] = str_replace('route=', '', $friendly_qry->row['query']);
          array_shift($parts);
          //return new Action($this->request->get['route']); // do not return to process other data
        }
      }
      */
      
      // Absolute url
      if ($this->config->get('mlseo_url_absolute')) {
        $fullUrl = (!empty($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $fullUrl = str_replace(array($this->config->get('config_url'), $this->config->get('config_ssl')), '', $fullUrl);
        
        $searchWithWildcard = '';
        if (strpos($fullUrl, '?')) {
          $searchWithWildcard = " OR redirect = '" .  $this->db->escape(strstr($fullUrl, '?', true).'*') . "' ";
        }
        
        $urlAbsolute = $this->db->query("SELECT query, language_id FROM " . DB_PREFIX . "url_absolute WHERE redirect = '" . $this->db->escape($fullUrl) . "' " . $searchWithWildcard . " LIMIT 1")->row;
        
        if (!empty($urlAbsolute['query'])) {
           parse_str('route='.$urlAbsolute['query'], $this->request->get);
           $parts = array();
        }
      }
      
      if ($this->config->get('mlseo_tag') && !empty($parts[0]) && !empty($parts[1]) && $parts[0] === $this->config->get('mlseo_fpp_tag_'.$this->config->get('config_language_id'))) {
        $this->request->get['route'] = 'product/search';
        $this->request->get['tag'] = str_replace('-', ' ', $parts[1]);
        
        $parts = array();
      }
      
      if ($this->config->get('mlseo_search') && !empty($parts[0]) && $parts[0] === $this->config->get('mlseo_fpp_search_'.$this->config->get('config_language_id'))) {
        $this->request->get['route'] = 'product/search';
        
        if (!empty($parts[1])) {
          $this->request->get['search'] = str_replace('-', ' ', urldecode($parts[1]));
          unset($parts[1]);
        }
        
        unset($parts[0]);
      }
      
      $seoIsCategory = false;
      
      $seoSortNames = $this->config->get('mlseo_sortname_'.$this->config->get('config_language_id')) ? $this->config->get('mlseo_sortname_'.$this->config->get('config_language_id')) : 'name|price|rating|model';
      $seoSortOrders = $this->config->get('mlseo_order_'.$this->config->get('config_language_id')) ? $this->config->get('mlseo_order_'.$this->config->get('config_language_id')) : 'asc|desc';
      $seoSortKeyword = $this->config->get('mlseo_sort_'.$this->config->get('config_language_id')) ? $this->config->get('mlseo_sort_'.$this->config->get('config_language_id')) : 'sort';
      
      $partsIteration = 0;
      
      foreach ($parts as $part) {
        $partsIteration++;
        
        if ($this->config->get('mlseo_enabled') && $this->config->get('mlseo_sort') && preg_match('~^'.$seoSortKeyword.'-('.$seoSortNames.')-('.$seoSortOrders.')$~', $part, $sortParts)) {
          $sortNames = explode('|', $seoSortNames);
          $sortOrders = explode('|', $seoSortOrders);
          
          if (count($sortNames) == 4) {
            if (isset($this->request->get['route']) && $this->request->get['route'] == 'product/special') {
              $sortKey = array_search($sortParts[1], array_combine(array('pd.name', 'ps.price', 'rating', 'p.model'), $sortNames));
            } else {
              $sortKey = array_search($sortParts[1], array_combine(array('pd.name', 'p.price', 'rating', 'p.model'), $sortNames));
            }
          }
          
          if (count($sortOrders) == 2) {
            $sortOrder = array_search($sortParts[2], array_combine(array('ASC', 'DESC'), $sortOrders));
          }
          
          if (isset($sortKey) && in_array($sortKey, array('pd.name', 'ps.price', 'p.price', 'rating', 'p.model'))) {
            $this->request->get['sort'] = $sortKey;
            $this->request->get['order'] = $sortOrder;
          
            continue;
          }
        }
        
        if ($this->config->get('mlseo_enabled') && $this->config->get('mlseo_sort') && preg_match('~^'.($this->config->get('mlseo_limit_'.$this->config->get('config_language_id')) ? $this->config->get('mlseo_limit_'.$this->config->get('config_language_id')) : 'limit').'-(\d{1,3})$~', $part, $sortParts)) {
          $this->request->get['limit'] = $sortParts[1];
          continue;
        }
        
        //if ($this->config->get('mlseo_enabled') && $this->config->get('mlseo_pagination') && (!empty($this->request->get['path']) || !empty($this->request->get['search']) || !empty($this->request->get['manufacturer_id']) || (isset($this->request->get['route']) && $this->request->get['route'] == 'product/special')) && preg_match('/page-(\d+)/', $part, $page)) {
        if ($this->config->get('mlseo_enabled') && $this->config->get('mlseo_pagination') && preg_match('/^page-(\d+)$/', $part, $page)) {
          $this->request->get['page'] = $page[1];
          continue;
        }
        
        if (!$this->config->get('mlseo_multistore') && $this->config->get('config_store_id')) {
          $currentSubStore = $this->config->get('config_store_id');
          $this->config->set('config_store_id', 0);
        }
      

				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE (keyword = '" . $this->db->escape($part) . "' OR keyword = '" . $this->db->escape($this->request->get['_route_']) . "') AND store_id = '" . (int)$this->config->get('config_store_id') . "'");
      
        if ($this->config->get('mlseo_enabled') && $this->config->get('mlseo_absolute') && $query->num_rows > 1) {
          $currUrlValues = array();
          
          foreach ($query->rows as $key => $row) {
            // delete duplicate language
            if (in_array($row['query'], $currUrlValues)) {
              unset($query->rows[$key]);
              continue;
            }
            
            // if absolute enabled and we are not in subcategory and something else than category is found, then show this entry (manufacturer), else remove non-categories entries
            $currUrlValues[] = $row['query'];
            
            if (!$seoIsCategory && strpos($row['query'], 'category_id=') !== 0) {
              $query->row = $row;
              $query->rows = array($row);
              break;
            } if ($seoIsCategory && strpos($row['query'], 'manufacturer_id=') === 0) {
              unset($query->rows[$key]);
            }
          }
          
          $query->row = reset($query->rows);
          $query->num_rows = count($query->rows);
        }
      

        if (!empty($currentSubStore)) {
          $this->config->set('config_store_id', $currentSubStore);
        }
        
                if ($this->config->get('mlseo_store_mode') && $this->config->get('mlseo_disable_other_store_links') && $this->request->get['route'] != 'journal3/blog') {
          // generate 404 if store mode prefix and not current language
          if ($partsIteration == count($parts)) {
            $hasValidLanguage = false;
            foreach ($query->rows as $checkStoreLang) {
              if (!isset($checkStoreLang['language_id']) || $checkStoreLang['language_id'] == $this->config->get('config_language_id')) {
                $hasValidLanguage = true;
                break;
              }
            }
            
            if (!$hasValidLanguage) {
              $this->request->get['route'] = '';
              continue;
            }
          }
        }
      
				if ($query->num_rows) {

					$url = explode('=', $query->row['query']);


          if ($url[0] == 'route') {
            $this->request->get['route'] = $url[1];
          }
          
          if (isset($url[1]) && !in_array($url[0], array('route', 'product_id', 'category_id', 'information_id', 'manufacturer_id', 'blog_article_id'))) {
            $this->request->get[$url[0]] = $url[1];
          }
      
					if ($url[0] == 'product_id') {
						$this->request->get['product_id'] = $url[1];
					}

					if ($url[0] == 'category_id') {

            $seoIsCategory = true;
            
            if ($this->config->get('mlseo_enabled') && $this->config->get('mlseo_absolute') && $query->num_rows > 1) {
              $parent_id = 0;
              
              if (!empty($this->request->get['path'])) {
                $parent_id = str_replace('_', '', strrchr($this->request->get['path'], '_'));
                
                if(!$parent_id) {
                  $parent_id = $this->request->get['path'];
                }
              }
              
              if (version_compare(VERSION, '3', '>=')) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url u left join " . DB_PREFIX . "category c on c.category_id = REPLACE(u.query, 'category_id=', '') WHERE u.keyword = '" . $this->db->escape($part) . "' AND c.parent_id = '" . $this->db->escape($parent_id) . "'");
              } else {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias u left join " . DB_PREFIX . "category c on c.category_id = REPLACE(u.query, 'category_id=', '') WHERE u.keyword = '" . $this->db->escape($part) . "' AND c.parent_id = '" . $this->db->escape($parent_id) . "'");
              }
              
              if(isset($query->row['query'])) {
                $url = explode('=', $query->row['query']);
              }
            }
      
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

					if (empty($this->request->get['route']) && $url[0] != 'route' && isset ($query->row['query']) && $query->row['query'] && $url[0] != 'information_id' && $url[0] != 'manufacturer_id' && $url[0] != 'category_id' && $url[0] != 'product_id' && $url[0] != 'collection_id' && $url[0] != 'collection_item_id' && $url[0] != 'inspiration_id') {
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

        if (isset($this->request->get['route']) && $this->config->get('mlseo_redirect_canonical') && !in_array($this->request->get['route'], array('account/login', 'error/not_found', 'product/search', 'journal3/blog/post', 'journal3/blog')) && !isset($this->request->get['mfp']) && !isset($this->request->get['sort']) && !isset($this->request->get['limit']) && !empty($_SERVER['HTTP_HOST']) && !strpos($_SERVER['REQUEST_URI'], '/mfp/')) {
          $url = urldecode('http' . (!empty($_SERVER['HTTPS']) ? 's' : '') . '://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
          $uri = urldecode($_SERVER['REQUEST_URI']);
          
          $redir_request = $this->request->get; // no use of $_GET here
          $redir_route = $this->request->get['route'];
          unset($redir_request['route'], $redir_request['_route_']);
          
          if (!empty($_SERVER['HTTPS'])) {
            if ($this->request->get['route'] == 'product/category' && $this->config->get('mlseo_redirect_canonical') == '2' && !empty($redir_request['path'])) {
              $this->load->model('tool/path_manager');
              
              $cat_id = strstr($redir_request['path'], '_') ? substr(strrchr($redir_request['path'], '_'), 1) : $redir_request['path'];
              
              unset($redir_request['path']);
              $extra_params = '';
              if($redir_request) {
                $extra_params = '&'.http_build_query($redir_request, '', '&');
              }
              
              $redir_url = str_replace('&amp;', '&', $this->url->link('product/category', 'path=' . ($this->config->get('mlseo_fpp_cat_canonical') ? $this->model_tool_path_manager->getFullCategoryPath($cat_id) : $cat_id).$extra_params, $_SERVER['HTTPS']));
            } else {
              $redir_url = str_replace('&amp;', '&', $this->url->link($redir_route, http_build_query($redir_request, '', '&'), $_SERVER['HTTPS']));
            }
          } else {
            if ($this->request->get['route'] == 'product/category' && $this->config->get('mlseo_redirect_canonical') == '2' && !empty($redir_request['path'])) {
              $this->load->model('tool/path_manager');
              
              $cat_id = strstr($redir_request['path'], '_') ? substr(strrchr($redir_request['path'], '_'), 1) : $redir_request['path'];
              
              unset($redir_request['path']);
              $extra_params = '';
              if($redir_request) {
                $extra_params = '&'.http_build_query($redir_request, '', '&');
              }
              
              $redir_url = str_replace('&amp;', '&', $this->url->link('product/category', 'path=' . ($this->config->get('mlseo_fpp_cat_canonical') ? $this->model_tool_path_manager->getFullCategoryPath($cat_id).$extra_params : $cat_id)));
            } else {
              $redir_url = str_replace('&amp;', '&', $this->url->link($redir_route, http_build_query($redir_request, '', '&')));
            }
          }
          
          $redir_url = trim($redir_url);
          
          if (!strpos($redir_url, 'route=') && (($redir_url != str_replace('&amp;', '&', $url)) && (urldecode($redir_url) != str_replace('&amp;', '&', $url))) && !isset($redir_request['blogpath'])) {
            header('HTTP/1.1 301 Moved Permanently');
            header('CSP-Redir: canonical', false);
            header('Location: ' . $redir_url); 
            exit;
          }
        }
      
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

        if (!empty($this->session->data['language']) && !($this->session->data['language'] == $this->config->get('config_language') || $this->session->data['language'] == substr($this->config->get('config_language'), 0, 2))) {
          $this->load->model('localisation/language');
          $languagesById = $this->model_localisation_language->getLanguages();
          $languages = array();
          foreach ($languagesById as $result) {
            $languages[$result['code']] = $result;
            if (strpos($result['code'], '-')) {
              $languages[substr($result['code'], 0, 2)] = $result;
            }
          }
          $this->config->set('config_language_id', $languages[ $this->session->data['language'] ]['language_id']);
        }
        
        $lang = isset($this->session->data['language']) ? $this->session->data['language'] : $this->config->get('config_language');
        
        $ml_mode = $url_append = '';
        
        if ($this->config->get('mlseo_multistore')) {
          //$ml_mode .= " AND (`store_id` = '" . (int)$this->config->get('config_store_id') . "' OR `store_id` = 0)";
          $ml_mode .= " AND (`store_id` = '" . (int)$this->config->get('config_store_id') . "')";
        }
        
        if ($this->config->get('mlseo_ml_mode')) {
          $ml_mode .= " AND (`language_id` = '" . (int)$this->config->get('config_language_id') . "' OR `language_id` = 0)";
        }
        
        if ($this->config->get('mlseo_multistore') && $this->config->get('mlseo_ml_mode')) {
          $ml_mode .= "ORDER BY store_id DESC, language_id DESC";
        } else if ($this->config->get('mlseo_ml_mode')) {
          $ml_mode .= "ORDER BY language_id DESC";
        } else if ($this->config->get('mlseo_multistore')) {
          $ml_mode .= "ORDER BY store_id DESC";
        }
      
		$url_info = parse_url(str_replace('&amp;', '&', $link));

		$url = '';

		$data = array();

		parse_str($url_info['query'], $data);

		foreach ($data as $key => $value) {
			if (isset($data['route'])) {
				if (($data['route'] == 'product/manufacturer/info' && $key == 'manufacturer_id') || ($data['route'] == 'information/information' && $key == 'information_id')) {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "' AND (`store_id` = '" . (int)$this->config->get('config_store_id') . "') AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

					if ($query->num_rows && $query->row['keyword']) {
        if ($this->config->get('mlseo_enabled') && !($data['route'] == 'product/product' && $key == 'manufacturer_id')) $url .= '/' . $query->row['keyword']; else
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

							$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "' AND (`store_id` = '" . (int)$this->config->get('config_store_id') . "') AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
							if ($query->num_rows && $query->row['keyword']) {
								$url .= '/' . $query->row['keyword'];

								unset($data[$key]);
							}
						}
					}

				
          } elseif ($this->config->get('mlseo_enabled') && $this->config->get('mlseo_sort') && $data['route'] == 'product/product' && in_array($key, array('limit', 'sort', 'order'))) {
            unset($data[${'key'}]);
            continue;
          } elseif ($this->config->get('mlseo_enabled') && $key == 'route' && $value == 'common/home') {
            $url .= '/';
          } elseif ($this->config->get('mlseo_enabled') && $this->config->get('mlseo_tag') && $key == 'route' && $value == 'product/search' && !empty($data['tag']) && $this->config->get('mlseo_fpp_tag_'.$this->config->get('config_language_id'))) {
            if ($this->config->get('mlseo_ascii_'.$this->config->get('config_language_id'))) {
              include_once(DIR_SYSTEM . 'library/gkd_urlify.php');
              $data['tag'] = URLify::downcode($data['tag'], substr($this->config->get('config_language'), 0, 2));
            }
            
            $url = '/'.$this->config->get('mlseo_fpp_tag_'.$this->config->get('config_language_id')).'/'.str_replace(' ', '-', $data['tag']);
            unset($data['tag']);
          } elseif ($this->config->get('mlseo_enabled') && $this->config->get('mlseo_search') && $key == 'route' && $value == 'product/search' && $this->config->get('mlseo_fpp_search_'.$this->config->get('config_language_id'))) {
            $url = '/'.$this->config->get('mlseo_fpp_search_'.$this->config->get('config_language_id'));
            
            if (!empty($data['search'])) {
              $url .= '/'.$data['search'];
              unset($data['search']);
            }
          } elseif ($this->config->get('mlseo_enabled') && $this->config->get('mlseo_pagination') && $key == 'page' && $url && isset($data['route']) && ($data['route'] == 'product/category' || $data['route'] == 'product/search' || $data['route'] == 'product/manufacturer/info' || $data['route'] == 'journal2/blog' || ($data['route'] == 'product/special' && !empty($data['page'])))) {
            $url_append .= '/page-'.$value;
            unset($data['page']);
          } elseif ($this->config->get('mlseo_enabled') && $this->config->get('mlseo_sort') && $key == 'limit') {
            $url .= '/'.($this->config->get('mlseo_limit_'.$this->config->get('config_language_id')) ? $this->config->get('mlseo_limit_'.$this->config->get('config_language_id')) : 'limit').'-'.$value;
            unset($data['limit']);
          } elseif ($this->config->get('mlseo_enabled') && $this->config->get('mlseo_sort') && $key == 'sort') {
            if ($value != 'p.sort_order') {
              $seoSortNames = $this->config->get('mlseo_sortname_'.$this->config->get('config_language_id')) ? $this->config->get('mlseo_sortname_'.$this->config->get('config_language_id')) : 'name|price|rating|model';
              $seoSortOrders = $this->config->get('mlseo_order_'.$this->config->get('config_language_id')) ? $this->config->get('mlseo_order_'.$this->config->get('config_language_id')) : 'asc|desc';
              $seoSortKeyword = $this->config->get('mlseo_sort_'.$this->config->get('config_language_id')) ? $this->config->get('mlseo_sort_'.$this->config->get('config_language_id')) : 'sort';
      
              $sortNames = explode('|', $seoSortNames);
              $sortOrders = explode('|', $seoSortOrders);
              
              if (count($sortNames) == 4) {
                if (isset($data['route']) && $data['route'] == 'product/special') {
                  $sortKey = array_search($value, array_combine($sortNames, array('pd.name', 'ps.price', 'rating', 'p.model')));
                } else {
                  $sortKey = array_search($value, array_combine($sortNames, array('pd.name', 'p.price', 'rating', 'p.model')));
                }
              }
              
              if (isset($data['order']) && count($sortOrders) == 2) {
                $sortOrder = array_search($data['order'], array_combine($sortOrders, array('ASC', 'DESC')));
              }
                
              if (isset($sortKey)) {
                $url .= '/'.$seoSortKeyword.'-'.$sortKey;
                
                if (isset($data['order'])) {
                  $url .= '-'.$sortOrder;
                }
              }
            }
            
            unset($data['sort'], $data['order']);
          } elseif ($this->config->get('mlseo_enabled') && $this->config->get('mlseo_friendly')
              && $value != 'common/home' && is_string($value) && !in_array($key, array('path', 'product_id', 'category_id', 'manufacturer_id', 'information_id', 'journal_blog_post_id', 'journal_blog_category_id', 'blog_id'))
              && !in_array($data['route'], array('offers/salescombopge', 'customerpartner/profile', 'customerpartner/profile/collection', 'news/article', 'news/ncategory'))
            ) {
            if (isset($data['journal_blog_tag'])) { $is_journal3_blog = true; }
            
            if (version_compare(VERSION, '3', '>=')) {
              $query = $this->db->query("SELECT keyword FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($key . '=' . $value) . "'" . $ml_mode);
            } else {
              $query = $this->db->query("SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $this->db->escape($key . '=' . $value) . "'" . $ml_mode);
            }
            if (${'query'}->num_rows) {
              $url .= '/' . $query->row["keyword"];
              //$url .= $query->row['keyword'] ? $this->config->get('mlseo_extension') : '';
              if($key != 'route') unset($data[$key]);
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

		  /*if ($this->config->get('mlseo_flag') && !isset($this->request->get["_route_"]) && !isset($this->request->get["route"])) {
        if ($this->config->get('mlseo_default_lang') == substr($this->session->data['language'], 0, 2) || $this->config->get('mlseo_default_lang') == $this->session->data['language']) {
          if (version_compare(VERSION, '2', '>=')) return new Action('common/home');
          else return $this->forward('common/home');
        } else {
          if (version_compare(VERSION, '2', '>=')) $this->response->redirect($this->url->link('common/home'));
          else $this->redirect($this->url->link('common/home'));
        }
      }
      */
      
      if ($this->config->get('advanced_sitemap_rewrite')) {
        $uri = str_replace(array($this->config->get('config_url'), $this->config->get('config_ssl')), '', urldecode('http' . (!empty($_SERVER['HTTPS']) ? 's' : '') . '://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
        $sitemapFolder = '';
        if ($uri == $sitemapFolder.'sitemap.xml') {
          $this->request->get['route'] = 'feed/advanced_sitemap';
          return new Action($this->request->get['route']);
        } else if (preg_match('/^'.$sitemapFolder.'sitemap(?:-(\w{3,}))?(?:-(\w{2}-\w{2}|\w{2}))?(?:-(\d+))?\.xml$/', $uri, $advStmpVars)) {
          if (preg_match('/^((\w{2}-\w{2})|\w{2})$/', $advStmpVars[1])) {
            $this->request->get['route'] = 'feed/advanced_sitemap';
            $this->request->get['lang'] = $advStmpVars[1];
          } else {
            $sitemapConf = $this->config->get('advanced_sitemap_cfg');
            if (!empty($sitemapConf['custom_links_include'])) {
              $sitemapTypes = array();
              foreach (explode("\n", $sitemapConf['custom_links_include']) as $k => $v) {
                if (strpos($v, '@')!==false) {
                  $type = trim(explode('@',$v,2)[0]);
                  if (!in_array($type, $sitemapTypes)) {
                    $sitemapTypes[] = $type;
                  }
                }
              }
            }
            if (isset($advStmpVars[1]) && isset($sitemapTypes) && in_array($advStmpVars[1], $sitemapTypes)) {
              $this->request->get['route'] = 'feed/advanced_sitemap/custom';
              $this->request->get['type'] = $advStmpVars[1];
            } else {
              $this->request->get['route'] = !empty($advStmpVars[1]) ? 'feed/advanced_sitemap/'.$advStmpVars[1] : 'feed/advanced_sitemap';
            }
            $this->request->get['lang'] = isset($advStmpVars[2]) ? $advStmpVars[2] : '';
          }
          
          $this->request->get['page'] = isset($advStmpVars[3]) ? $advStmpVars[3] : 1;
          
          return new Action($this->request->get['route']);
        } else if (preg_match('/^'.$sitemapFolder.'product-grid(?:-(\d+))?.xml$/', $uri, $advStmpVars)) {
          $this->request->get['route'] = 'feed/advanced_sitemap/product';
          $this->request->get['page'] = isset($advStmpVars[1]) ? $advStmpVars[1] : 1;
          $this->request->get['grid'] = 1;
          
          return new Action($this->request->get['route']);
        }
      }
      
      // Friendly urls
      if ($this->config->get('mlseo_friendly') && !empty($this->request->get['_route_'])) {
        if ($this->config->get('mlseo_extension')) {
          $route = rtrim(str_replace($this->config->get('mlseo_extension'), '', $this->request->get['_route_']), '/');
        } else {
          $route = rtrim($this->request->get['_route_'], '/');
        }
        
        $ml_mode = '';
        
        if ($this->config->get('mlseo_multistore')) {
          $ml_mode .= " AND (`store_id` = '" . (int)$this->config->get('config_store_id') . "' OR `store_id` = 0)";
        }
        
        if ($this->config->get('mlseo_ml_mode')) {
          $ml_mode .= " AND (`language_id` = '" . (int)$this->config->get('config_language_id') . "' OR `language_id` = 0)";
        }
        
        if ($this->config->get('mlseo_multistore') && $this->config->get('mlseo_ml_mode')) {
          $ml_mode .= "ORDER BY store_id DESC, language_id DESC";
        } else if ($this->config->get('mlseo_ml_mode')) {
          $ml_mode .= "ORDER BY language_id DESC";
        } else if ($this->config->get('mlseo_multistore')) {
          $ml_mode .= "ORDER BY store_id DESC";
        }
        
        $seoUrlTable = version_compare(VERSION, '3', '>=') ? 'seo_url' : 'url_alias';
        
        $friendly_qry = $this->db->query("SELECT * FROM " . DB_PREFIX . $seoUrlTable . " WHERE query LIKE 'route=%' AND keyword = '" . $this->db->escape($route) . "' " . $ml_mode);
        
        if (!empty($friendly_qry->row['query'])) {
          $this->request->get['route'] = str_replace('route=', '', $friendly_qry->row['query']);
          return new Action($this->request->get['route']);
        }
      }
      
      //if (isset($this->request->get['_route_'])) {
      
      if (!empty($this->request->get['_route_'])) {
        if ($this->config->get('mlseo_extension')) {
          $this->request->get['_route_'] = str_replace($this->config->get('mlseo_extension'), '', $this->request->get['_route_']);
        }

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
