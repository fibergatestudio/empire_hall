<?php
	class ControllerCommonHeader extends Controller {
		public function index() {
		$data['ee_js_position'] = $this->config->get('module_ee_tracking_js_position');
		$data['ee_js_version'] = $this->config->get('module_ee_tracking_js_version');

      $this->load->model('tool/seo_package');
      $this->model_tool_seo_package->metaRobots();
      $this->model_tool_seo_package->checkCanonical();
      $this->model_tool_seo_package->hrefLang();
      $this->model_tool_seo_package->richSnippets();
      $this->model_tool_seo_package->ggAnalytics();
      
      if (version_compare(VERSION, '2', '>=')) {
        $data['mlseo_meta'] = $this->document->renderSeoMeta();
      } else {
        $this->data['mlseo_meta'] = $this->document->renderSeoMeta();
      }
      
      $seoTitlePrefix = $this->config->get('mlseo_title_prefix');
      $seoTitlePrefix = isset($seoTitlePrefix[$this->config->get('config_store_id').$this->config->get('config_language_id')]) ? $seoTitlePrefix[$this->config->get('config_store_id').$this->config->get('config_language_id')] : '';
      
      $seoTitleSuffix = $this->config->get('mlseo_title_suffix');
      $seoTitleSuffix = isset($seoTitleSuffix[$this->config->get('config_store_id').$this->config->get('config_language_id')]) ? $seoTitleSuffix[$this->config->get('config_store_id').$this->config->get('config_language_id')] : '';

      if (version_compare(VERSION, '2', '<')) {
        if ($this->config->get('mlseo_fix_search')) {
          $this->data['mlseo_fix_search'] = true;
          $this->data['csp_search_url'] = $this->url->link('product/search');
          $this->data['csp_search_url_param'] = $this->url->link('product/search', 'search=%search%');
        }
      }
      
			// Analytics
			$this->load->model('setting/extension');
			$this->load->model('catalog/category');
			
			$data['analytics'] = array();
			
			$analytics = $this->model_setting_extension->getExtensions('analytics');
			
			foreach ($analytics as $analytic) {
				if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
					$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
				}
			}
			$curr_lang =  $this->language->get('code');
			if ($this->request->server['HTTPS']) {
				$server = $this->config->get('config_ssl');
			} else {
				$server = $this->config->get('config_url');
			}
			
			
			$data['not_lighthouse'] = (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome-Lighthouse') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'GTmetrix') === false && strpos($_SERVER['HTTP_USER_AGENT'], '(X11; Linux x86_64) AppleWebKit') === false);
			$data['ogtitle'] = $this->document->getOGTitle();
			$data['ogdesc'] = $this->document->getOGDescription();
			$data['ogimage'] = $this->document->getOGImage();
			$data['ogsite_name'] = $this->document->getOGImage();
			
			
        //$data['title'] = $this->document->getTitle();
        $data['title'] = (isset($seoTitlePrefix) ? $seoTitlePrefix : '') . $this->document->getTitle() . (isset($seoTitleSuffix) ? $seoTitleSuffix : '');
      
			$data['base'] = $server;
			$data['description'] = $this->document->getDescription();
			$data['keywords'] = $this->document->getKeywords();
			$data['links'] = $this->document->getLinks();
			$data['styles'] = $this->document->getStyles();

    // OCFilter start
    $data['noindex'] = $this->document->isNoindex();
    // OCFilter end
      
			$data['scripts'] = $this->document->getScripts('header');
			$data['lang'] = $this->language->get('code');
			$data['direction'] = $this->language->get('direction');
			$data['tlt_metatags'] = $this->document->getTLTMetaTags();
			$data['is_logged'] = $this->customer->isLogged();
			$data['client_name'] = $this->customer->getFirstName();
			$data['current_url'] = $_SERVER['REQUEST_URI'];
			$data['is_filter'] = isset($this->request->get['filter_ocfilter']);
			
			$data['twittercard'] = $this->document->getTwitterCard();
			$data['twitterimage'] = $this->document->getTwitterImage();
			$data['twittertitle'] = $this->document->getTwitterTitile();
			$data['twitterdescription'] = $this->document->getTwitterDescription();
			$data['articleauthor'] = $this->document->getArticleAuthor();
			
			if(isset($this->request->get['route'])) {
				$data['route_url'] =  $this->request->get['route'];
			}
			
			
			$data['open_to_index'] = false;
			if(($data['current_url'] == '/' || $data['current_url'] == 'home' || $data['route_url'] == 'product/category' || $data['route_url'] == 'product/product'
			|| (($data['route_url'] == 'product/manufacturer/info' || $data['route_url'] == 'product/manufacturer')) || isset($this->request->get['collection_id'])) && $data['is_filter'] == false){
				$data['open_to_index'] = true;
				}elseif($data['is_filter'] == true){
				$filters = explode(';', $this->request->get['filter_ocfilter']);
				if(count($filters) == 1 && strpos($filters[0], 'm') !== false){
					$exclude_categories = $this->config->get('config_exclude_category');
					$category = empty($this->request->get['path']) ? 0 : (int) array_pop(explode('_', $this->request->get['path']));
					if((strpos($filters[0], ',') === false) && !in_array($category, $exclude_categories)){
						$data['open_to_index'] = true;
					}
				}
			}
			
			$data['ogurl'] = $_SERVER['REQUEST_SCHEME'].'://'. $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			
			if(empty($data['ogtitle'])){
				$data['ogtitle'] = $data['title'];
			}
			if(empty($data['ogdesc'])){
				$data['ogdesc'] = $data['description'];
			}
			
			$data['name'] = $this->config->get('config_name');
			
			if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
				$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
			} else {
				$data['logo'] = '';
			}
			
			// Socials
			$data['instagram'] = $this->config->get('config_instagram');
			$data['pinterest'] = $this->config->get('config_pinterest');
			$data['facebook'] = $this->config->get('config_facebook');
			$data['youtube'] = $this->config->get('config_youtube');
			
			$this->load->language('common/header');
			
			// Wishlist
			if ($this->customer->isLogged()) {
				$this->load->model('account/wishlist');
				$data['count_wishlist'] = $this->model_account_wishlist->getTotalWishlist();
			} else {
				$data['count_wishlist'] = (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0);
			}
			
			$data['count_products'] = $this->cart->countProducts();
			
			$data['home'] = $this->url->link('common/home');
			$data['forgot'] = $this->url->link('account/forgotten', '', true);
			$data['wishlist'] = $this->url->link('account/wishlist', '', true);
			$data['logged'] = $this->customer->isLogged();
			$data['account'] = $this->url->link('account/account', '', true);
			$data['register'] = $this->url->link('account/register', '', true);
			$data['order'] = $this->url->link('account/order', '', true);
			$data['checkout'] = $this->url->link('checkout/checkout', '', true);
			$data['store_link'] = $this->url->link('product/category', 'path=1');
			$data['brands_link'] = $this->url->link('product/manufacturer', '', true);
			$data['inspiration_link'] = $this->url->link('product/inspiration', '', true);
			$data['telephone'] = $this->config->get('config_telephone');
			$data['store_name'] = $this->config->get('config_name');
			
			$data['language'] = $this->load->controller('common/language');
			$data['currency'] = $this->load->controller('common/currency');
			$data['search'] = $this->load->controller('common/search');
			$data['cart'] = $this->load->controller('common/cart');

        	$data['module_oc_cache_status'] = false;

          if(isset($this->request->get['route'])){
        				$this->request->get['route'] = $this->request->get['route'];
        			} else {
        				$this->request->get['route'] = 'common/home';
        			}

        	if($this->config->get('module_oc_cache_status') && !preg_match("/account\/|checkout\/|common\/cart\/info/", $this->request->get['route'])) {


			  $data['module_oc_cache_status'] = true;
		    $data['oc_cache'] = $this->load->controller('common/oc_cache');

			$_config_keys = array(
				'module_oc_cache_image_lazyload',
				'module_oc_cache_image_webp',
				'module_oc_cache_combine_js',
        'module_oc_cache_js_cdn',
        'module_oc_cache_css_cdn',
		    );

		    foreach($_config_keys as $_config_key) {
			   $data[$_config_key] = $this->config->get($_config_key);
		    }

		}
      
			$data['menu'] = $this->load->controller('common/menu');
			
			
			$data['inspiration_link_config']= $this->config->get('module_inspiration_status');
			
			$data['body_class'] = '';
			
			if (isset($this->request->get['route']) && $this->request->get['route'] == 'common/home') {
				$data['body_class'] = 'main-page main';
			} elseif (isset($this->request->get['route']) && $this->request->get['route'] == 'error/not_found') {
				$data['body_class'] = 'error-page';
			} elseif (isset($this->request->get['inspiration_id'])) {
				$data['body_class'] = 'inspiration-page';
			} elseif (isset($this->request->get['information_id']) && ($this->request->get['information_id'] == '6' || $this->request->get['information_id'] == '4')) {
				$data['body_class'] = 'custome-page custome';
			} else {
				$data['body_class'] = '';
			}
			
			// Menu
			if($this->cache->get('header.menu'."_".$curr_lang)){
				$menus = $this->cache->get('header.menu'."_".$curr_lang);
				}else{
				$menus = $this->model_catalog_category->getMenus(0);
				$this->cache->set('header.menu'."_".$curr_lang, $menus);
			}
			
			foreach ($menus as $menu) {
				$data['menus'][] = array(
                'name'			=> $menu['name'],
                'href'			=> $menu['href'] ? $server.$menu['href'] : false
				);
			}
			
			
			
			$data['categories'] = $this->cache->get('common.header.categories.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));
			
			if (!$data['categories']) {
				$this->load->model('catalog/category');
				$categories = $this->model_catalog_category->getCategories(0);

				foreach ($categories as $category) {
					$children_data = array();
					
					$children = $this->model_catalog_category->getCategories($category['category_id']);
					
					if ($category['category_id'] != 157) {
						foreach($children as $child) {
							$children_data_third = array();
							
							$childs_third = $this->model_catalog_category->getCategories($child['category_id']);
	
							foreach ($childs_third as $third) {
								$children_data_fourth = array();
								
								$childs_fourth = $this->model_catalog_category->getCategories($third['category_id']);
		
								foreach ($childs_fourth as $fourth) {
									$children_data_fourth[] = array(
										'category_id' => $fourth['category_id'],
										'name' => $fourth['name'],
										'href' => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'] . '_' . $third['category_id'] . '_' . $fourth['category_id'])
									);
								}
								
								$children_data_third[] = array(
									'category_id' => $third['category_id'],
									'name' => $third['name'],
									'children_fourth' => $children_data_fourth,
									'href' => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'] . '_' . $third['category_id'])
								);
							}
							
							$children_data[] = array(
								'category_id' => $child['category_id'],
								'name' => $child['name'],
								'children_third' => $children_data_third,
								'href' => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
							);
							
						}
						
						$data['categories'][] = array(
							'category_id' => $category['category_id'],
							'name'        => $category['name'],
							'children'    => $children_data,
							'href'        => $this->url->link('product/category', 'path=' . $category['category_id'])
						);
					}
				}
				
				$this->cache->set('common.header.categories.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $data['categories']);
			}
			
			return $this->load->view('common/header', $data);
		}
	}
