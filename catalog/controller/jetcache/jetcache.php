<?php
/* All rights reserved belong to the module, the module developers https://opencartadmin.com */
// https://opencartadmin.com © 2011-2020 All Rights Reserved
// Distribution, without the author's consent is prohibited
// Commercial license
if (!class_exists('ControllerJetcacheJetcache', false)) {
class ControllerJetcacheJetcache extends Controller {
	protected $data;
	protected $template;
	protected $seocms_settings;
	protected $sc_cache_name;
	protected $token_name;
	protected $jetcache_cont_number;
  	protected $flag_cache_access_output = false;
	protected $jetcache_buildcache = false;
	protected $count_model_cached = 0;
	protected $count_cont_cached = 0;
	protected $count_query_cached = 0;
	protected $jc_ajax = false;
	protected $jc_cont_log = array();
	protected $template_engine;
	protected $template_directory;
	protected $langcode_all;
	protected $jc_cont_document;
	protected $url_link_ssl;
	protected $url_protocol;
	protected $admin_logged = false;
	protected $user_id = false;
	protected $webp = false;
	protected $config_url;
	protected $html_ajax;
	protected $flag_scripts;
	protected $last_modified = '';
	protected $total_wishlist = 0;
	protected $flag_access_exeptions;
	protected $externalCSS = Array();
	protected $inlineCSS = Array();
	protected $externalJS = Array();
	protected $inlineJS = Array();
	protected $css_combine;
	protected $js_combine;
	protected $route;
	protected $outputHTML;
	protected $is_mobile = false;
	protected $is_tablet = false;
	protected $is_desktop = false;

	public $request_uri_trim;
	public $jetcache_settings;


	public function jetcache_construct() {
 		if (!$this->registry->get('jetcache_construct')) {
            $this->registry->set('jetcache_construct', true);
            if (!defined('VERSION')) return false;
			if (!defined('SC_VERSION')) define('SC_VERSION', (int) substr(str_replace('.', '', VERSION), 0, 2));
	        if (SC_VERSION > 23) {
	        	$this->token_name = 'user_token';
	        } else {
	        	$this->token_name = 'token';
	        }

		    if ((isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == '1')) || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https') || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) == 'on'))) {
		    	$this->url_link_ssl = true;
		    	$this->url_protocol = 'https';

				$this->config_url = $this->config->get('config_ssl');
				if (!$this->config_url) $this->config_url = HTTPS_SERVER;


		    } else {
		    	if (SC_VERSION < 20) {
		    		$this->url_link_ssl = 'NONSSL';
		    	} else {
		    		$this->url_link_ssl = false;
		    	}
		    	$this->url_protocol = 'http';

				$this->config_url = $this->config->get('config_url');
				if (!$this->config_url) $this->config_url = HTTP_SERVER;
		    }
            $this->request_uri_trim = str_replace('&amp;', '&', trim(ltrim($this->registry->get('request')->server['REQUEST_URI'], '/')));
            $this->jc_ajax = false;
			if (isset($this->registry->get('request')->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->registry->get('request')->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
				$this->jc_ajax = true;

				$this->jetcache_buildcache = false;
				$jetcache_headers = getallheaders();
				if (isset($jetcache_headers['JETCACHE_BUILDCACHE'])) {
					$this->jetcache_buildcache = true;
					$this->jc_ajax = false;
				}
			}

		    if (isset($this->registry->get('request')->post['jc_cont_ajax'])) {
            	$this->jc_ajax = true;
		    }

			if (defined('HTTP_CATALOG')) {
				$this->registry->set('admin_work', true);
				$this->jc_ajax = true;
			}

			if ($this->config->get('ascp_settings') != '') {
				$this->seocms_settings = $this->config->get('ascp_settings');
			} else {
				$this->seocms_settings = Array();
			}
			$this->jetcache_settings = $this->config->get('asc_jetcache_settings');

	        $this->setOutputRegistry($this->registry);

			if ($this->customer->isLogged() && SC_VERSION > 20) {
				$this->load->model('account/wishlist');
				$this->total_wishlist = $this->model_account_wishlist->getTotalWishlist();
			} else {
				if (isset($this->session->data['wishlist']) && !empty($this->session->data['wishlist'])) {
					$this->total_wishlist = count($this->session->data['wishlist']);
				} else {
                	$this->total_wishlist = 0;
				}
			}

			if (isset($this->session->data['customer_id'])) {
	        	$customer_id = $this->session->data['customer_id'];
			} else {
				$customer_id = 0;
			}

            if (SC_VERSION > 20) {
                if (isset($this->jetcache_settings['pages_status']) && $this->jetcache_settings['pages_status']) {
	                if (isset($this->jetcache_settings['cart_interval']) && $this->jetcache_settings['cart_interval'] != '') {
		                $cart_interval = (int)$this->jetcache_settings['cart_interval'];
	                } else {
	                	$cart_interval = 60;
	                }
	                if (SC_VERSION < 23) {
	                	$this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE customer_id = '0' AND date_added < DATE_SUB(NOW(), INTERVAL " . $cart_interval . " MINUTE)");
	                } else {
						$this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE (api_id > '0' OR customer_id = '0') AND date_added < DATE_SUB(NOW(), INTERVAL " . $cart_interval . " MINUTE)");
					}
                }

				$cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE customer_id = '" . (int)$customer_id . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");

	            if ($cart_query->num_rows > 0) {
	                foreach ($cart_query->rows as $row) {
	                	unset($row['date_added']);
	                	$customer_id_cart[] = $row;
	                }
	            	$this->config->set('seocms_jetcache_cart', $customer_id_cart);
	            } else {
	            	$this->config->set('seocms_jetcache_cart', array());
	            }
            } else {
				if (!isset($this->session->data['cart']) || !is_array($this->session->data['cart'])) {
					$this->session->data['cart'] = array();
				}
            	if (isset($this->session->data['cart'])) {
            		$this->config->set('seocms_jetcache_cart', $this->session->data['cart']);
            	}
			}

			if (isset($this->session->data['user_id'])) {
	        	$this->user_id = $this->session->data['user_id'];
			} else {
				$this->user_id = false;
			}

			if (($this->user_id || $this->registry->get('admin_work')) && !$this->jetcache_buildcache) {
				$this->admin_logged = true;
			} else {
				$this->admin_logged = false;
			}

		    if (($this->admin_logged && isset($this->jetcache_settings['jetcache_info_status']) && $this->jetcache_settings['jetcache_info_status']) || (isset($this->jetcache_settings['jetcache_info_demo_status']) && $this->jetcache_settings['jetcache_info_demo_status'])) {
            	if (file_exists(DIR_APPLICATION . 'view/theme/default/stylesheet/jetcache.css')) {
					$this->document->addStyle('catalog/view/theme/default/stylesheet/jetcache.css');
				}
			}

            $this->flag_access_exeptions = array();

            $this->load->model('jetcache/jetcache');
			if (!$this->config->get('asc_cache_auto_clear') || $this->config->get('asc_cache_auto_clear') == '') {
	             $this->model_jetcache_jetcache->editSetting('asc_cache_auto', array('asc_cache_auto_clear' => time()));
			}

	        if (isset($this->jetcache_settings['cache_auto_clear']) && $this->jetcache_settings['cache_auto_clear'] != '' && $this->config->get('asc_cache_auto_clear') != '' && $this->config->get('asc_cache_auto_clear') > 0 ) {
		        if (((time() - $this->config->get('asc_cache_auto_clear')) / 60 / 60) > $this->jetcache_settings['cache_auto_clear']) {
	             // Clear all cache
	             // Save current time to setting - asc_cache_auto_clear
	             $this->model_jetcache_jetcache->editSetting('asc_cache_auto', array('asc_cache_auto_clear' => time()));
	             $this->cacheremove('noaccess', false);
		        }
			}

			$this->webp = $this->getWebp();

	    	if (isset($this->jetcache_settings['cache_mobile_detect']) && $this->jetcache_settings['cache_mobile_detect']) {

				if (!class_exists('jc_Mobile_Detect', false)) {
					loadlibrary('md/mobile_detect');
				}

		        $detect = new jc_Mobile_Detect;

		        if ($detect->isMobile()) {
		        	$this->is_mobile = true;
				}

				if($detect->isTablet()){
		        	$this->is_tablet = true;
		        	$this->is_mobile = true;
				}

				if(!$this->is_tablet && !$this->is_mobile){
		        	$this->is_desktop = true;
				}
	        }
	        return true;
        } else {
        	return false;
        }
	}

    public function getWebp() {

	    $this->webp = false;

	    if (isset($this->jetcache_settings['image_status']) && $this->jetcache_settings['image_status'] && isset($this->jetcache_settings['image_webp_status']) && $this->jetcache_settings['image_webp_status']) {
			if(((isset($this->request->server['HTTP_ACCEPT']) && strpos($this->request->server['HTTP_ACCEPT'], 'image/webp') !== false)
			|| (isset($this->request->server['HTTP_USER_AGENT']) && strpos(strtolower($this->request->server['HTTP_USER_AGENT']), 'google') !== false)
			|| (isset($this->request->server['HTTP_USER_AGENT']) && strpos(strtolower($this->request->server['HTTP_USER_AGENT']), 'chrome/') !== false)
			|| (isset($this->request->server['HTTP_USER_AGENT']) && strpos(strtolower($this->request->server['HTTP_USER_AGENT']), 'yandex') !== false)
			|| (isset($this->request->server['HTTP_USER_AGENT']) && strpos(strtolower($this->request->server['HTTP_USER_AGENT']), 'yandexbot/') !== false)
			|| (isset($this->request->cookie['jetcache_webp']) && $this->request->cookie['jetcache_webp'] == '1')
			)
			) {
				if ((isset($this->request->server['HTTP_USER_AGENT']) && strpos(strtolower($this->request->server['HTTP_USER_AGENT']), 'edge/') === false) &&
					(isset($this->request->server['HTTP_USER_AGENT']) && strpos(strtolower($this->request->server['HTTP_USER_AGENT']), 'edg/') === false)
				) {
			    	$this->webp = true;
		    	}
			} else {
		    	$this->webp = false;
			}
		} else {
			$this->webp = false;
		}

        return $this->webp;
    }

	public function setOutputRegistry($registry) {
		if (is_callable(array('Response', 'jetcache_setRegistry'))) {
			$this->response->jetcache_setRegistry($registry);
		}
		if (is_callable(array('DB', 'jc_setRegistry'))) {
			$this->db->jc_setRegistry($registry);
		}
    }

    private function jetcache_str_access($string_array, $str, $access = true) {
		$access_status = $access;
		$str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
		if (isset($string_array) && $string_array != '') {
			$array_string = explode(PHP_EOL, trim($string_array));
			foreach($array_string as $num => $findme) {
				$findme = trim(html_entity_decode($findme, ENT_QUOTES, 'UTF-8'));
				if (isset($findme[0]) && $findme[0] != '#' && $findme != '' && stripos($str, $findme) !== false) {
					if ($access_status) {
						$access_status = false;
					} else {
						$access_status = true;
					}
					return $access_status;
				}
			}
		}
		return $access_status;
	}

	public function jetcache_minify($output) {
		if (!$this->registry->get('page_fromcache')) {

		if (isset($this->registry->get('request')->get['route'])) {
			$this->route = $this->registry->get('request')->get['route'];
		} else {
			$this->route = 'common/home';
		}

        $this->outputHTML = $output;

        if (isset($this->jetcache_settings['replacers_status']) && $this->jetcache_settings['replacers_status']) {
        	$this->jetcache_replacers();
        }

		if (isset($this->jetcache_settings['minify_css_status']) && $this->jetcache_settings['minify_css_status'] ||
		    isset($this->jetcache_settings['minify_js_status']) && $this->jetcache_settings['minify_js_status'] ||
		    isset($this->jetcache_settings['minify_html_status']) && $this->jetcache_settings['minify_html_status']
		) {
				if (!$this->jc_ajax && isset($this->jetcache_settings['jetcache_widget_status']) && $this->jetcache_settings['jetcache_widget_status']) {

					$access_status = true;
					if (isset($this->jetcache_settings['ex_uri']) && $this->jetcache_settings['ex_uri'] != '') {

						$ex_uri_array = explode(PHP_EOL, trim($this->jetcache_settings['ex_uri']));

					    foreach($ex_uri_array as $num => $ex_uri) {
					    	$ex_uri = trim($ex_uri);
							if (isset($ex_uri[0]) && $ex_uri[0] != '#' && $ex_uri != '' && strpos($this->request_uri_trim, $ex_uri) !== false) {
								$access_status = false;
								break;
							}
					    }
					}

					if ($access_status) {

				        if (stripos($this->outputHTML, '</body>') === false) {
				        	return $this->outputHTML;
				        }
				        if (stripos($this->outputHTML, '</head>') === false) {
				        	return $this->outputHTML;
				        }

						// Remove comment with <script <style <link
						$this->outputHTML = preg_replace_callback(
				        '/<!--.*?-->/is'
				        ,array($this, '_removeCommentHTML')
				        ,$this->outputHTML);

						if (isset($this->jetcache_settings['minify_js_first']) && $this->jetcache_settings['minify_js_first']) {
							$this->jetcache_minify_js();
						} else {
							$this->jetcache_minify_css();
						}
						if (isset($this->jetcache_settings['minify_js_first']) && $this->jetcache_settings['minify_js_first']) {
							$this->jetcache_minify_css();
						} else {
							$this->jetcache_minify_js();
						}

						$this->jetcache_minify_html();
						$this->outputHTML = $this->jetcache_minify_end();
			        }
		        }
        	}
        }
		return $this->outputHTML;
	}

	private function jetcache_replacers() {

		if (isset($this->jetcache_settings['replacers_status']) && $this->jetcache_settings['replacers_status']) {
	    	if (!empty($this->jetcache_settings['replacers'])) {

	            $access_status = true;
				if (isset($this->jetcache_settings['replacers_ex_route']) && $this->jetcache_settings['replacers_ex_route'] != '') {
					$access_status = $this->jetcache_str_access($this->jetcache_settings['replacers_ex_route'], $this->route);
				}

	            if ($access_status) {
                   $this->outputHTML = str_replace(array("\r\n", "\r", "\n", PHP_EOL), '{{EOL}}', $this->outputHTML);
					foreach($this->jetcache_settings['replacers'] as $num => $shortcode) {

						if (isset($shortcode['status']) && $shortcode['status']) {

							$shortcode['in'] = str_replace(array("\r\n", "\r", "\n", PHP_EOL), '{{EOL}}', $shortcode['in']);
                            $shortcode['out'] = str_replace(array("\r\n", "\r", "\n", PHP_EOL), '{{EOL}}', $shortcode['out']);

							if (isset($shortcode['all']) && $shortcode['all']) {
								$this->outputHTML = str_replace(html_entity_decode($shortcode['in'], ENT_QUOTES, 'UTF-8'), html_entity_decode($shortcode['out'], ENT_QUOTES, 'UTF-8'), $this->outputHTML);
							} else {
		                    	$this->outputHTML = $this->str_replace_once(html_entity_decode($shortcode['in'], ENT_QUOTES, 'UTF-8'), html_entity_decode($shortcode['out'], ENT_QUOTES, 'UTF-8'), $this->outputHTML);
							}
						}
					}
                    $this->outputHTML = str_replace('{{EOL}}', PHP_EOL, $this->outputHTML);
				}
	        }
        }
	}

    protected function _removeCommentHTML($script_) {

		if (stripos($script_[0], '<script ') !== false) {
			$script_[0] = '';
		}
		if (stripos($script_[0], '<script>') !== false) {
			$script_[0] = '';
		}
		if (stripos($script_[0], '<style') !== false) {
			if (stripos($script_[0], "'<style") === false && stripos($script_[0], '"<style') === false) {
				$script_[0] = '';
			}
		}
		if (stripos($script_[0], '<link') !== false) {
			if (stripos($script_[0], "'<link") === false && stripos($script_[0], '"<link') === false) {
				$script_[0] = '';
			}
		}
		if (stripos($script_[0], '<script') !== false) {
			if (stripos($script_[0], "'<script") === false && stripos($script_[0], '"<script') === false) {
				$script_[0] = '';
			}
		}
    	return $script_[0];
    }

	private function jetcache_minify_html() {
		if (isset($this->jetcache_settings['minify_html_status']) && $this->jetcache_settings['minify_html_status']) {

			$access_status = true;
			if (isset($this->jetcache_settings['minify_html_ex_route']) && $this->jetcache_settings['minify_html_ex_route'] != '') {
                $access_status = $this->jetcache_str_access($this->jetcache_settings['minify_html_ex_route'], $this->route);
			}

	        if ($access_status) {

				$file_url_compressor[] = DIR_SYSTEM . 'library/jetcache/minify/html/html.php';
				foreach($file_url_compressor as $num => $file_name) {
					if (file_exists($file_name)) {
						if (function_exists('modification')) {
							require_once(modification($file_name));
						} else {
							require_once($file_name);
						}
					}
				}

			    $jc_minify_html = new jc_Minify_HTML($this->outputHTML, array('jsCleanComments' => true));

			    $this->outputHTML = $jc_minify_html->process();
		    }
		}
		return $this->outputHTML;
	}

    private function jetcache_minify_end() {
		if (isset($this->jetcache_settings['minify_js_first']) && $this->jetcache_settings['minify_js_first']) {
			$this->outputHTML = $this->jetcache_minify_end_js();
		} else {
			$this->outputHTML = $this->jetcache_minify_end_css();
		}
		if (isset($this->jetcache_settings['minify_js_first']) && $this->jetcache_settings['minify_js_first']) {
			$this->outputHTML = $this->jetcache_minify_end_css();
		} else {
			$this->outputHTML = $this->jetcache_minify_end_js();
		}
    	return $this->outputHTML;
    }

    private function str_replace_once($search, $replace, $text) {
		//return  str_ireplace($search, $replace, $text);
        return preg_replace_callback('|(' . $search . ')|', function ($match) use ($search, $replace) { return $replace; }, $text, 1);
		/*
		$pos = mb_strpos($text, $search);
		return $pos !== false ? mb_substr($text, 0, $pos) . $replace . mb_substr($text, $pos + mb_strlen($search)) : $text;
		*/
	}

	private function jetcache_minify_end_css() {
        if (isset($this->jetcache_settings['minify_css_status']) && $this->jetcache_settings['minify_css_status']) {

			if (isset($this->jetcache_settings['minify_css_combine_head']) && $this->jetcache_settings['minify_css_combine_head'] != '') {
	        	$output_preload_position = $output_paste_position = html_entity_decode($this->jetcache_settings['minify_css_combine_head'], ENT_QUOTES, 'UTF-8');
	        } else {
	        	$output_preload_position = $output_paste_position = '</head>';
	        }

			if (isset($this->jetcache_settings['minify_css_inline_tie']) && $this->jetcache_settings['minify_css_inline_tie'] != '') {
	        	$output_css_inline_tie = html_entity_decode($this->jetcache_settings['minify_css_inline_tie'], ENT_QUOTES, 'UTF-8');
	        }

	        if (isset($this->jetcache_settings['minify_css_combine_footer']) && $this->jetcache_settings['minify_css_combine_footer']) {
	        	$output_paste_position = '</body>';
	        }

	        $go_combine = false;

			foreach($this->externalCSS as $num => $css_array) {
				if (isset($css_array['combine']) && $css_array['combine']) {
					$go_combine = true;
				}
			}
	        if ($go_combine) {

	        	if (isset($this->jetcache_settings['minify_css_combine_preload']) && $this->jetcache_settings['minify_css_combine_preload']) {
	        		$css_external_name = '<link href="' . $this->css_combine . '" rel="preload" as="style" id="jc_css_combine_preload" onload="this.rel=\'stylesheet\'">';
	        		$this->outputHTML = $this->str_replace_once($output_preload_position, $css_external_name . PHP_EOL . $output_preload_position, $this->outputHTML);
	        	}
                $css_external_name = '<link href="' . $this->css_combine . '" rel="stylesheet" type="text/css" />';
                $this->outputHTML = $this->str_replace_once($output_paste_position, $css_external_name . PHP_EOL . $output_paste_position, $this->outputHTML);
	        }
            $go_preload = false;
            $go_go_preload = false;
			foreach($this->externalCSS as $num => $css_array) {
				if (isset($css_array['combine']) && $css_array['combine']) {
					$this->outputHTML = str_replace($css_array['md5'], '', $this->outputHTML);
				} else {

					if (isset($css_array['preload']) && $css_array['preload']) {
						$go_preload = true;
						$go_go_preload = true;
						$css_type = '';
						$css_rel = 'rel="preload"';
						$css_as = 'as="style" onload="this.rel=\'stylesheet\'"';
					} else {
						$go_preload = false;
						$css_type = 'type="text/css"';
						$css_rel = 'rel="stylesheet"';
						$css_as = '';
					}

					if ((isset($css_array['inline']) && $css_array['inline']) || (isset($css_array['style']) && $css_array['style'])) {

						$css_insert = $css_array[0];
                        if (isset($css_array['inline']) && $css_array['inline']) {
                        	$css_insert = '<style>' . PHP_EOL . $css_insert . PHP_EOL . '</style>';
                        }

						if (isset($css_array['style']) && $css_array['style'] && isset($this->jetcache_settings['minify_css_inline_footer']) && $this->jetcache_settings['minify_css_inline_footer']) {
							$this->outputHTML = str_replace($css_array['md5'], '', $this->outputHTML);
							$this->outputHTML = str_ireplace('</body>', $css_insert . PHP_EOL . '</body>', $this->outputHTML);
						} else {
							if (isset($this->jetcache_settings['minify_css_inline_tie']) && $this->jetcache_settings['minify_css_inline_tie'] != '') {
								$this->outputHTML = $this->str_replace_once($output_css_inline_tie , $css_insert . PHP_EOL . $output_css_inline_tie, $this->outputHTML);
	        				} else {
	        					$this->outputHTML = $this->str_replace_once($css_array['md5'] , $css_insert, $this->outputHTML);
	        				}
							$this->outputHTML = str_replace($css_array['md5'],  '', $this->outputHTML);
						}

					} else {
                        $css_original = $css_array[0];

						if (isset($css_array['preload']) && $css_array['preload']) {

							$preload_replace_in = array('type="text/css"', "type='text/css'", 'rel="stylesheet"', "rel='stylesheet'");
						    $preload_replace_out = array('', '', '', '');

							$css_array[0] = str_ireplace($preload_replace_in, $preload_replace_out, $css_array[0]);

							$preload_replace_in = array('<link ', '<style');
						    $preload_replace_out = array('<link '. $css_rel . ' ' . $css_as . ' ' . $css_type . ' ', '<style '. $css_rel . ' ' . $css_as . ' ' . $css_type . ' ');

							$css_array[0] = str_ireplace($preload_replace_in, $preload_replace_out, $css_array[0]);

						}

                        $css_insert = $css_array[0];

						$access_status = true;
						if (isset($this->jetcache_settings['minify_css_ex_css_footer']) && $this->jetcache_settings['minify_css_ex_css_footer'] != '') {
			                $access_status = $this->jetcache_str_access($this->jetcache_settings['minify_css_ex_css_footer'], $css_insert);
						}

						if ($access_status && isset($this->jetcache_settings['minify_css_footer']) && $this->jetcache_settings['minify_css_footer']) {

							if ($css_rel == 'rel="preload"') {
								$this->outputHTML = $this->str_replace_once($css_array['md5'], $css_insert, $this->outputHTML);
								$this->outputHTML = str_replace($css_array['md5'],  '', $this->outputHTML);

                            	$this->outputHTML = $this->str_replace_once('</body>', $css_original . PHP_EOL . '</body>', $this->outputHTML);
							} else {
								$this->outputHTML = str_replace($css_array['md5'], '', $this->outputHTML);
								$this->outputHTML = str_ireplace('</body>', $css_insert . PHP_EOL . '</body>', $this->outputHTML);
							}

						} else {

							if (isset($css_array['preload']) && $css_array['preload']) {
								$this->outputHTML = $this->str_replace_once($output_preload_position, $css_insert . PHP_EOL . $output_preload_position, $this->outputHTML);
							}

							$this->outputHTML = $this->str_replace_once( $css_array['md5'], $css_original, $this->outputHTML);
							$this->outputHTML = str_replace($css_array['md5'],  '', $this->outputHTML);
						}
					}
				}
			}

        	if ($go_go_preload || ($go_combine && isset($this->jetcache_settings['minify_css_combine_preload']) && $this->jetcache_settings['minify_css_combine_preload'])) {
        		//$css_js = '<script>jc_cssSelectorAll = document.querySelectorAll(\'link[type="text/css_jc"], style[type="text/css_jc"]\'); jc_cssSelectorAll.forEach(function(el){el.rel = "stylesheet"; el.type = "text/css"; });</script>';
            	//$this->outputHTML = str_ireplace('</body>', $css_js . PHP_EOL . '</body>', $this->outputHTML);
            }

        }

		return $this->outputHTML;
	}

	private function jetcache_css_compress($css_file_content, $options) {

		if (isset($this->jetcache_settings['minify_css_compress_status']) && $this->jetcache_settings['minify_css_compress_status']) {
			if (isset($this->jetcache_settings['minify_css_compress_type']) && $this->jetcache_settings['minify_css_compress_type']) {
	        	$css_file_content = jc_Minify_CSS_Compressor::process($css_file_content, $options);
	        } else {
				$css_file_content = preg_replace("!/\*[^*]*\*+([^/][^*]*\*+)*/!", "", $css_file_content);
				$css_file_content = str_replace(array(PHP_EOL, "\r\n", "\r", "\n", "\t", "  ", "    ", "    "), "", $css_file_content);
			}
        } else {
        	$css_file_content = str_ireplace(array('font-face {', 'font-face' . PHP_EOL . '{'), array('font-face{', 'font-face{'), $css_file_content);
        }
	    return $css_file_content;
	}

	private function jetcache_css_rewrite($css_file_content, $options) {
        $css_file_content = str_ireplace('font-face{', 'font-face{font-display:swap;', $css_file_content);
	    $css_file_content = jc_Minify_CSS_UriRewriter::prepend($css_file_content, $options['prependRelativePath']);

	    return $css_file_content;
	}

	private function jetcache_minify_css() {

		if (isset($this->jetcache_settings['minify_css_status']) && $this->jetcache_settings['minify_css_status']) {

            $access_status = true;
			if (isset($this->jetcache_settings['minify_css_ex_route']) && $this->jetcache_settings['minify_css_ex_route'] != '') {
				$access_status = $this->jetcache_str_access($this->jetcache_settings['minify_css_ex_route'], $this->route);
			}

	        if ($access_status) {

				$file_url_compressor[] = DIR_SYSTEM . 'library/jetcache/minify/url/url.php';
				$file_url_compressor[] = DIR_SYSTEM . 'library/jetcache/minify/css/compressor.php';
				foreach($file_url_compressor as $num => $file_name) {
					if (file_exists($file_name)) {
						if (function_exists('modification')) {
							require_once(modification($file_name));
						} else {
							require_once($file_name);
						}
					}
				}
		        $options = array(
		            'compress' => true,
		            'removeCharsets' => true,
		            'preserveComments' => true,
		            'currentDir' => null,
		            'docRoot' => $this->request->server['DOCUMENT_ROOT'],
		            'prependRelativePath' => null,
		            'symlinks' => array(),
		        	);

		        $dir_root = str_ireplace('/system/', '', str_ireplace('\\', '/', DIR_SYSTEM)) . '/';

				if (isset($this->jetcache_settings['minify_css_ex_combine_inline']) && $this->jetcache_settings['minify_css_ex_combine_inline'] != '') {
					$this->jetcache_settings['minify_css_ex_combine'] .= PHP_EOL . $this->jetcache_settings['minify_css_ex_combine_inline'];
				}

				if (isset($this->jetcache_settings['minify_css_in_inline']) && $this->jetcache_settings['minify_css_in_inline'] != '') {
                   	$this->jetcache_settings['minify_css_ex_combine'] .= PHP_EOL . $this->jetcache_settings['minify_css_in_inline'];
				}

                $route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$this->route);
                $cache_css_external = DIR_IMAGE . 'jetcache/css_cache/' . md5($route . $this->config->get('config_language_id') . $this->is_mobile) . '.json';
                if (!file_exists($cache_css_external)) {
                	//External css
	            	/* $output_css = '/[^\'|^\"](<\\s*(?=.*[\'|"]\\s*stylesheet|[\'|"]\\s*text\/css.*)\\s*link\b.+href\\s*=\\s*[\'|"](.*?)[\'|"].*?>)/i'; */
	                $output_css = '/[^\'|^\"](<\\s*(?=.*[\'|"]\\s*stylesheet|[\'|"]\\s*text\/css.*)\\s*link\b.+?[h]ref\\s*=\\s*[\'|"](.*?)[\'|"](.*?)>)/i';
	                $this->outputHTML = preg_replace_callback($output_css, array($this, '_removeExternalCSS'), $this->outputHTML);
	                file_put_contents($cache_css_external, json_encode($this->externalCSS));
                } else {
                	$this->externalCSS = json_decode(file_get_contents($cache_css_external), true);
                	foreach($this->externalCSS as $md5 => $css_array) {
                		$this->outputHTML = str_ireplace($css_array[0], $md5, $this->outputHTML);
                	}
                }
                //Inline css
	            $output_css_inline = '/[^\'\"](<\\s*style\\b[^>]*>([\\s\\S].*?)<\\s*\/style\\s*\>)/is';
                $this->outputHTML = preg_replace_callback($output_css_inline, array($this, '_removeInlineCSS'), $this->outputHTML);

                foreach($this->inlineCSS as $num => $css_array) {
					$this->inlineCSS[$num]['style'] = true;
                }
                $externalCSSmd5 = array();
                foreach($this->externalCSS as $num => $css_array) {
					$css_file = $css_array[1];
					if (strpos($css_file, '?') !== false && strpos($css_file, PHP_EOL) === false) {
                    	$css_file = substr($css_file, 0, strpos($css_file, '?'));
                    }

					if ((isset($this->jetcache_settings['minify_css_combine_status']) && $this->jetcache_settings['minify_css_combine_status']) || (isset($this->jetcache_settings['minify_css_combine_inline']) && $this->jetcache_settings['minify_css_combine_inline'])) {
	                    $minify_css_ex_combine_status = true;
	                    if (isset($css_array['style']) && $css_array['style'] && isset($this->jetcache_settings['minify_css_combine_inline']) && !$this->jetcache_settings['minify_css_combine_inline']) {
	                    	$minify_css_ex_combine_status = false;
	                    }
					} else {
						$minify_css_ex_combine_status = false;
					}

					if ($minify_css_ex_combine_status && isset($this->jetcache_settings['minify_css_ex_combine']) && $this->jetcache_settings['minify_css_ex_combine'] != '') {
						$minify_css_ex_combine_status = $this->jetcache_str_access($this->jetcache_settings['minify_css_ex_combine'], $css_array[1]);
					}

					if ($minify_css_ex_combine_status) {
						$externalCSSmd5[$css_file] = $css_file;
					}
                }

                $output_css_all_md5 = md5($this->route . '_' . $this->admin_logged . '_' . (json_encode($externalCSSmd5)));

                $this->externalCSS = array_merge($this->externalCSS, $this->inlineCSS);

                $css_file_combine = DIR_IMAGE . 'jetcache/css/' . $output_css_all_md5 . '.css';
                $go_combine = false;


	            foreach($this->externalCSS as $num => $css_array) {
	            	if (strpos($css_array[1], 'fonts.googleapis.com/css?') !== false) {
	            		$this->externalCSS[$num][0] = str_ireplace($css_array[1], str_replace('&amp;', '&',  $css_array[1]) . '&display=swap', $this->externalCSS[$num][0]);
	            		$this->externalCSS[$num][1] = $css_array[1] . '&display=swap';
	            		$this->externalCSS[$num]['combine'] = false;
	            	}

					if (isset($this->jetcache_settings['minify_css_preload']) && $this->jetcache_settings['minify_css_preload'] != '') {
						$minify_css_preload = $this->jetcache_str_access($this->jetcache_settings['minify_css_preload'], $css_array[1]);
						if (!$minify_css_preload) {
							$this->externalCSS[$num]['preload'] = true;
						}
					}
                    $url_without = str_replace($this->config_url, '', $css_array[1]);
					$css_file = str_replace(array('"', "'", '..', '<', '>'), array('', '', '', '', ''), $dir_root . ltrim($url_without, '/'));

					if (strpos($css_file, '?') !== false && strpos($css_file, PHP_EOL) === false) {
                    	$css_file = substr($css_file, 0, strpos($css_file, '?'));
                    }

	            	if ((strpos($css_file, PHP_EOL) === false && is_file($css_file) && file_exists($css_file)) || (isset($css_array['style']) && $css_array['style'])) {
	            		if (isset($css_array['style']) && $css_array['style']) {
	            			$css_file_content = $css_array[1];
	            		} else {
	            			$css_file_content = file_get_contents($css_file);
	            		}

						if ((isset($this->jetcache_settings['minify_css_combine_status']) && $this->jetcache_settings['minify_css_combine_status']) || (isset($this->jetcache_settings['minify_css_combine_inline']) && $this->jetcache_settings['minify_css_combine_inline'])) {
	                        $minify_css_ex_combine_status = true;
	                        if (isset($css_array['style']) && $css_array['style'] && isset($this->jetcache_settings['minify_css_combine_inline']) && !$this->jetcache_settings['minify_css_combine_inline']) {
	                        	$minify_css_ex_combine_status = false;
	                        }
						} else {
							$minify_css_ex_combine_status = false;
						}

						if ($minify_css_ex_combine_status && isset($this->jetcache_settings['minify_css_ex_combine']) && $this->jetcache_settings['minify_css_ex_combine'] != '') {
							$minify_css_ex_combine_status = $this->jetcache_str_access($this->jetcache_settings['minify_css_ex_combine'], $css_array[1]);
						}

                        if (isset($css_array['style']) && $css_array['style']) {
                        	$options['prependRelativePath'] = '';
                        } else {
                        	$options['prependRelativePath'] = str_ireplace($dir_root, '/',  str_replace('\\', '/', dirname($css_file)) . '/');
                        }

	                    if ($minify_css_ex_combine_status) {

 		                    $this->externalCSS[$num]['combine'] = true;

	                        if (!file_exists($css_file_combine) || $go_combine) {
	                            $css_file_md5 = md5($css_file);

		                        $css_file_content_cache = DIR_IMAGE . 'jetcache/css_cache/' . $css_file_md5 . '.css';
				                if (!file_exists($css_file_content_cache)) {
			                    	$css_file_content = $this->jetcache_css_compress($css_file_content, $options);
                                    $css_file_content = $this->jetcache_css_rewrite($css_file_content, $options);
									file_put_contents($css_file_content_cache, $css_file_content, LOCK_EX);
								} else {
									$css_file_content = file_get_contents($css_file_content_cache);
								}
	                            $go_combine = true;
			              		file_put_contents($css_file_combine, '/* Jet Cache: ' . $css_array[1].  ' */' . PHP_EOL . $css_file_content . PHP_EOL, FILE_APPEND | LOCK_EX);
		              		}
		            	} else {

		            		$this->externalCSS[$num]['combine'] = false;

							if (isset($this->jetcache_settings['minify_css_in_inline']) && $this->jetcache_settings['minify_css_in_inline'] != '') {
								$minify_css_in_inline_status = true;


								$minify_css_in_inline_status = $this->jetcache_str_access($this->jetcache_settings['minify_css_in_inline'], $css_array[1]);

								if (!$minify_css_in_inline_status) {

									if (isset($this->jetcache_settings['minify_css_inline_compress_status']) && $this->jetcache_settings['minify_css_inline_compress_status']) {
										$css_file_content = $this->jetcache_css_compress($css_file_content, $options);
									}
									$css_file_content = $this->jetcache_css_rewrite($css_file_content, $options);

								    $this->externalCSS[$num]['inline'] = true;
								    $this->externalCSS[$num][0] = $css_file_content;
							    }
		            		}

	                        if (isset($css_array['style']) && $css_array['style']) {

                        		if (isset($this->jetcache_settings['minify_css_inline_compress_status']) && $this->jetcache_settings['minify_css_inline_compress_status']) {
	                        		$css_file_content = $this->jetcache_css_compress($css_file_content, $options);
	                        	}
                                $css_file_content = $this->jetcache_css_rewrite($css_file_content, $options);
                                $this->externalCSS[$num][1] = $css_file_content;
	                        }
		            	}
	            	}
	            }
				if (file_exists($css_file_combine)) {
					$this->css_combine = '/' . str_ireplace($dir_root, '', str_replace('\\', '/', $css_file_combine)) . '?' . filemtime($css_file_combine);
				}
			}
		}
		return $this->outputHTML;
	}

    protected function _removeExternalCSS($script_css) {

        $first = $script_css[0][0];
        $script_css = array_slice($script_css, 1);

        $script_css_md5 = md5(json_encode($script_css[0]));
        $script_css['md5'] = $script_css_md5;
    	//array_push($this->externalCSS , $script_css);
        $this->externalCSS[$script_css_md5] = $script_css;
    	return $first . $script_css_md5;
    }

    protected function _removeInlineCSS($script_css) {
        // Replace regexp first symbol for ...><style> return here > thro <!-- $('head').append('<style>
        $first = $script_css[0][0];
        $script_css = array_slice($script_css, 1);

        $script_css_md5 = md5(json_encode($script_css[0]));
        $script_css['md5'] = $script_css_md5;
    	//array_push($this->inlineCSS , $script_css);
        $this->inlineCSS[$script_css_md5] = $script_css;

    	return $first . $script_css_md5;
    }

	private function jetcache_minify_end_js() {
        if (isset($this->jetcache_settings['minify_js_status']) && $this->jetcache_settings['minify_js_status']) {
	        if (isset($this->jetcache_settings['minify_js_combine_footer']) && $this->jetcache_settings['minify_js_combine_footer']) {
	        	$output_paste_position = '</body>';

	        	if (isset($this->jetcache_settings['minify_js_combine_head']) && $this->jetcache_settings['minify_js_combine_head'] != '') {
	        		$output_paste_position_link = html_entity_decode($this->jetcache_settings['minify_js_combine_head'], ENT_QUOTES, 'UTF-8');
	        	} else {
	        		$output_paste_position_link = '</head>';
	        	}

	        } else {
	        	if (isset($this->jetcache_settings['minify_js_combine_head']) && $this->jetcache_settings['minify_js_combine_head'] != '') {
	        		$output_paste_position = html_entity_decode($this->jetcache_settings['minify_js_combine_head'], ENT_QUOTES, 'UTF-8');
	        	} else {
	        		$output_paste_position = '</head>';
	        	}
	        }

	        $go_combine = false;

			foreach($this->externalJS as $num => $js_array) {
				if (isset($js_array['combine']) && $js_array['combine']) {
					$go_combine = true;
				}
			}
	        if ($go_combine) {
	        	if (isset($this->jetcache_settings['minify_js_combine_preload']) && $this->jetcache_settings['minify_js_combine_preload']) {
	        		$js_external_name = '<script src="' . $this->js_combine . '" type="text/jetcache"></script>';
	        	} else {
	        		$js_external_name = '<script src="' . $this->js_combine . '"></script>';
	        	}
                $this->outputHTML = $this->str_replace_once($output_paste_position, $js_external_name . PHP_EOL . $output_paste_position, $this->outputHTML);

	        	if (isset($this->jetcache_settings['minify_js_combine_footer']) && $this->jetcache_settings['minify_js_combine_footer']) {
	                //$js_external_link = '<link rel="preload" as="script" href="' . $this->js_combine . '">';
	        		//$this->outputHTML = $this->str_replace_once($output_paste_position_link, $js_external_link . PHP_EOL . $output_paste_position_link, $this->outputHTML);
	        	}
	        }

            $go_preload = false;
            $go_go_preload = false;

			foreach($this->externalJS as $num => $js_array) {

				if (isset($js_array['combine']) && $js_array['combine']) {

					$this->outputHTML = str_replace($js_array['md5'], '', $this->outputHTML);

				} else {

					if (isset($js_array['preload']) && $js_array['preload']) {
						$go_preload = true;
						$go_go_preload = true;
						$js_type = 'type="text/jetcache"';
					} else {
						$go_preload = false;
						$js_type = '';
					}

					if ((isset($js_array['inline']) && $js_array['inline']) || (isset($js_array['script']) && $js_array['script'])) {
						if ($go_preload && isset($js_array['script']) && $js_array['script']) {
							$js_insert = $js_array[1];
						} else {
							$js_insert = $js_array[0];
						}

						$access_status = true;
						if (isset($this->jetcache_settings['minify_js_ex_inline_footer']) && $this->jetcache_settings['minify_js_ex_inline_footer'] != '') {
			                $access_status = $this->jetcache_str_access($this->jetcache_settings['minify_js_ex_inline_footer'], $js_insert);
						}

						if ($access_status && isset($js_array['script']) && $js_array['script'] && isset($this->jetcache_settings['minify_js_inline_footer']) && $this->jetcache_settings['minify_js_inline_footer']) {
							$this->outputHTML = str_replace($js_array['md5'], '', $this->outputHTML);
							if ($go_preload) {
								$this->outputHTML = str_ireplace('</body>', '<script ' . $js_type . '>' . $js_insert . '</script>' . PHP_EOL . '</body>', $this->outputHTML);
							} else {
								$this->outputHTML = str_ireplace('</body>', $js_insert . PHP_EOL . '</body>', $this->outputHTML);
							}
						} else {
						    if ($go_preload || (isset($js_array['inline']) && $js_array['inline'])) {

								if (isset($js_array[2]) && stripos($js_array[2], '<script') !== false) {
									$js_script_title = $js_array[2];
									$preload_replace_in = array('"text/javascript"', "'text/javascript'", 'text/javascript', 'type=', 'type =');
								    $preload_replace_out = array('');
									$js_script_title = str_ireplace($preload_replace_in, $preload_replace_out, $js_script_title);

									$preload_replace_in = array('<script');
								    $preload_replace_out = array('<script ' . $js_type . ' ');
									$js_script_title = str_ireplace($preload_replace_in, $preload_replace_out, $js_script_title);
								} else {
									$js_script_title = '<script ' . $js_type . '>';
								}

								//$this->outputHTML = str_ireplace($js_array['md5'], $js_script_title . $js_insert . '</script>', $this->outputHTML);

								if (isset($this->jetcache_settings['minify_js_inline_tie']) && $this->jetcache_settings['minify_js_inline_tie'] != '' && isset($js_array['inline']) && $js_array['inline']) {
									$output_js_inline_tie = html_entity_decode($this->jetcache_settings['minify_js_inline_tie'], ENT_QUOTES, 'UTF-8');
									$this->outputHTML = $this->str_replace_once($output_js_inline_tie, $js_script_title . $js_insert . '</script>' . PHP_EOL . $output_js_inline_tie, $this->outputHTML);

		        				} else {
		        					$this->outputHTML = $this->str_replace_once($js_array['md5'], $js_script_title . $js_insert . '</script>', $this->outputHTML);
		        				}
								$this->outputHTML = str_replace($js_array['md5'],  '', $this->outputHTML);
							} else {
								//$this->outputHTML = str_ireplace($js_array['md5'], $js_insert, $this->outputHTML);
								$this->outputHTML = $this->str_replace_once($js_array['md5'], $js_insert, $this->outputHTML);
								$this->outputHTML = str_replace($js_array['md5'],  '', $this->outputHTML);
							}
						}

					} else {

						if (isset($js_array['preload']) && $js_array['preload']) {
							$preload_replace_in = array('"text/javascript"', "'text/javascript'", 'text/javascript', 'type=', 'type =');
						    $preload_replace_out = array('');
							$js_array[0] = str_ireplace($preload_replace_in, $preload_replace_out, $js_array[0]);

							$preload_replace_in = array('<script');
						    $preload_replace_out = array('<script ' . $js_type . ' ');
							$js_array[0] = str_ireplace($preload_replace_in, $preload_replace_out, $js_array[0]);
						}

                        $js_insert = $js_array[0];

						$access_status = true;
						if (isset($this->jetcache_settings['minify_js_ex_js_footer']) && $this->jetcache_settings['minify_js_ex_js_footer'] != '') {
			                $access_status = $this->jetcache_str_access($this->jetcache_settings['minify_js_ex_js_footer'], $js_insert);
						}

						if ($access_status && isset($this->jetcache_settings['minify_js_footer']) && $this->jetcache_settings['minify_js_footer']) {
							$this->outputHTML = str_replace($js_array['md5'], '', $this->outputHTML);
							$this->outputHTML = str_ireplace('</body>', $js_insert . PHP_EOL . '</body>', $this->outputHTML);
						} else {
							//$this->outputHTML = str_ireplace($js_array['md5'], $js_insert, $this->outputHTML);
							$this->outputHTML = $this->str_replace_once($js_array['md5'], $js_insert, $this->outputHTML);
							$this->outputHTML = str_replace($js_array['md5'],  '', $this->outputHTML);
						}
					}
				}
			}

        	if ($go_go_preload || ($go_combine && isset($this->jetcache_settings['minify_js_combine_preload']) && $this->jetcache_settings['minify_js_combine_preload'])) {

				if (isset($this->jetcache_settings['minify_js_afterload_time_desktop']) && $this->jetcache_settings['minify_js_afterload_time_desktop'] != '') {
                	$time_desktop = $this->jetcache_settings['minify_js_afterload_time_desktop'];
				} else {
					$time_desktop = '5000';
				}

				if (isset($this->jetcache_settings['minify_js_afterload_time_mobile']) && $this->jetcache_settings['minify_js_afterload_time_mobile']) {
                	$time_mobile = $this->jetcache_settings['minify_js_afterload_time_mobile'];
				} else {
					$time_mobile = '7000';
				}
                if ($time_mobile != 'NO') {
                	$time_after_dom_mobile = "document.addEventListener('DOMContentLoaded', function() { setTimeout(jc_afterload, " . $time_mobile . ") }); ";
                } else {
                	$time_after_dom_mobile = '';
                }

				if (isset($this->jetcache_settings['minify_js_afterload_time_desktop']) && $this->jetcache_settings['minify_js_afterload_time_desktop'] != '') {
                	$minify_js_preload_desktop = "document.body.addEventListener('mouseover', jc_afterload); document.addEventListener('mousemove', jc_afterload); document.addEventListener('DOMContentLoaded', function() { setTimeout(jc_afterload, " . $time_desktop . "); }); } </script>";
				} else {
					$minify_js_preload_desktop = "document.addEventListener('DOMContentLoaded', function() { setTimeout(jc_afterload, 1100); }); } </script>";
				}

$js_js = <<<EOF
<script>
jc_afterLoad_state = false;

function jc_vin(i){
	console.log(i);
}

function jc_afterload(){
	if (!jc_afterLoad_state) {
		document.body.removeEventListener('touchstart', jc_afterload); document.body.removeEventListener('touchmove', jc_afterload); document.body.removeEventListener('mouseover', jc_afterload); document.removeEventListener('mousemove', jc_afterload);
		jc_querySelectorAll = document.querySelectorAll('script[type="text/jetcache"]');
		Array.prototype.forEach.call(jc_querySelectorAll, function (el) {
			jc_script = document.createElement('script');
			jc_script.type = 'text/javascript';
	        if (el.src) { jc_script.src = el.src; } else { jc_script.text = el.innerHTML; }
	        if (el.getAttribute('async') === null) { jc_script.async = false; } else { jc_script.async = true; }
	        if (el.getAttribute('defer') === null) { jc_script.defer = false; } else { jc_script.defer = true; }

 			if (el.src) {
	         	if (el.getAttribute('onload') === null) {
	         		jc_script.setAttribute('onload', 'jc_vin("' + el.src + '");');
	         	} else {
	         		//jc_script.setAttribute('onload', 'jc_vin("' + el.src + '");' + el.getAttribute('onload'));
	         	}
         	} else {
         		//console.log(el.innerHTML);
         	}
			jc_script.onerror = function () {
				if (el.src) console.log('JC: Error loading ' + el.src);
			}
			el.parentNode.appendChild(jc_script);
		});
		jc_afterLoad_state = true;
	}
}
var jc_userAgent = navigator.userAgent || navigator.vendor || window.opera;
if (/Android|iPhone|iPad|iPod|Windows Phone|webOS|BlackBerry/i.test(jc_userAgent)) {
	document.body.addEventListener('touchstart', jc_afterload); document.body.addEventListener('touchmove', jc_afterload); $time_after_dom_mobile
} else {
EOF;

$js_js = $js_js . $minify_js_preload_desktop;

            	$this->outputHTML = str_ireplace('</body>', $js_js . PHP_EOL . '</body>', $this->outputHTML);
            }

        }

		return $this->outputHTML;
	}

	private function jetcache_js_compress($js_file_content, $options) {

		if (isset($this->jetcache_settings['minify_js_compress_status']) && $this->jetcache_settings['minify_js_compress_status']) {
			if (isset($this->jetcache_settings['minify_js_compress_type']) && $this->jetcache_settings['minify_js_compress_type']) {
				$js_file_content = jc_JSmin::minify($js_file_content);
			} else {
				$js_file_content = \jc_JShrink\jc_Minifier::minify($js_file_content);
			}
			//Fix JS error, for -->
			$js_file_content = str_replace('-->', PHP_EOL . '-->', $js_file_content);
        }
    	return $js_file_content;
	}

    private function jetcache_js_rewrite($js_file_content, $options) {
	    $js_file_content = jc_Minify_CSS_UriRewriter::prepend($js_file_content, $options['prependRelativePath']);
	    return $js_file_content;
    }

	private function jetcache_minify_js() {

		if (isset($this->jetcache_settings['minify_js_status']) && $this->jetcache_settings['minify_js_status']) {

            $access_status = true;
			if (isset($this->jetcache_settings['minify_js_ex_route']) && $this->jetcache_settings['minify_js_ex_route'] != '') {
				$access_status = $this->jetcache_str_access($this->jetcache_settings['minify_js_ex_route'], $this->route);
			}

	        if ($access_status) {

				$file_url_compressor[] = DIR_SYSTEM . 'library/jetcache/minify/url/url.php';
				$file_url_compressor[] = DIR_SYSTEM . 'library/jetcache/minify/js/js.php';
				$file_url_compressor[] = DIR_SYSTEM . 'library/jetcache/minify/js/minifier.php';
				foreach($file_url_compressor as $num => $file_name) {
					if (file_exists($file_name)) {
						if (function_exists('modification')) {
							require_once(modification($file_name));
						} else {
							require_once($file_name);
						}
					}
				}
		        $options = array(
		            'compress' => true,
		            'removeCharsets' => true,
		            'preserveComments' => true,
		            'currentDir' => null,
		            'docRoot' => $this->request->server['DOCUMENT_ROOT'],
		            'prependRelativePath' => null,
		            'symlinks' => array(),
		        	);

		        $dir_root = str_ireplace('/system/', '', str_ireplace('\\', '/', DIR_SYSTEM)) . '/';

                //External JS
                $route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$this->route);
                $cache_js_external = DIR_IMAGE . 'jetcache/js_cache/' . md5($route . $this->config->get('config_language_id') . $this->is_mobile) . '.json';
                if (!file_exists($cache_js_external)) {
                	$output_js = '/(<\\s*script\\b.+?[s]rc\\s*=\\s*[\'|"](.*?)[\'|"](.*?)>([\\s\\S]*?)<\\s*\/script\\s*\>.*?)/i';
                	$this->outputHTML = preg_replace_callback($output_js, array($this, '_removeExternalJS'), $this->outputHTML);
                	file_put_contents($cache_js_external, json_encode($this->externalJS));
                } else {
                	$this->externalJS = json_decode(file_get_contents($cache_js_external), true);
                	foreach($this->externalJS as $md5 => $js_array) {
                		$this->outputHTML = str_ireplace($js_array[0], $md5, $this->outputHTML);
                	}
                }

	            //Inline JS
	            $output_js_inline = '/((<\\s*script\\b[^>]*>)([\\s\\S]*?)<\\s*\/script>)/is';
                $this->outputHTML = preg_replace_callback($output_js_inline, array($this, '_removeInlineJS'), $this->outputHTML);

                foreach($this->inlineJS as $num => $js_array) {
					$this->inlineJS[$num]['script'] = true;
                }

				if (isset($this->jetcache_settings['minify_js_ex_combine_inline']) && $this->jetcache_settings['minify_js_ex_combine_inline'] != '') {
					$this->jetcache_settings['minify_js_ex_combine'] .= PHP_EOL . $this->jetcache_settings['minify_js_ex_combine_inline'];
				}

				if (isset($this->jetcache_settings['minify_js_in_inline']) && $this->jetcache_settings['minify_js_in_inline'] != '') {
                   	$this->jetcache_settings['minify_js_ex_combine'] .= PHP_EOL . $this->jetcache_settings['minify_js_in_inline'];
				}

                $externalJSCombine = array();
				foreach($this->externalJS as $num => $js_array) {

						if ((isset($this->jetcache_settings['minify_js_combine_status']) && $this->jetcache_settings['minify_js_combine_status']) || (isset($this->jetcache_settings['minify_js_combine_inline']) && $this->jetcache_settings['minify_js_combine_inline'])) {
	                        $minify_js_ex_combine_status = true;
	                        if (isset($js_array['script']) && $js_array['script'] && isset($this->jetcache_settings['minify_js_combine_inline']) && !$this->jetcache_settings['minify_js_combine_inline']) {
	                        	$minify_js_ex_combine_status = false;
	                        }
						} else {
							$minify_js_ex_combine_status = false;
						}

						if ($minify_js_ex_combine_status && isset($this->jetcache_settings['minify_js_ex_combine']) && $this->jetcache_settings['minify_js_ex_combine'] != '') {
							$minify_js_ex_combine_status = $this->jetcache_str_access($this->jetcache_settings['minify_js_ex_combine'], $js_array[1]);
						}

	                    if ($minify_js_ex_combine_status) {

		                    if (strpos($js_array[1], '?') !== false && strpos($js_array[1], PHP_EOL) === false) {
		                    	$js_file = substr($js_array[1], 0, strpos($js_array[1], '?'));
		                    } else {
		                    	$js_file = $js_array[1];
		                    }

                            $hache_combine = md5($js_file);
 		                    $externalJSCombine[$hache_combine] = $js_file;
 		                }
 		        }

				// Before merge, for md5, because different microdata & inline scripts
				// Only combine without ex
                $output_js_all_md5 = md5($this->route . '_' . $this->admin_logged . '_' . (json_encode($externalJSCombine)));

                $this->externalJS = array_merge($this->externalJS, $this->inlineJS);

                $js_file_combine = DIR_IMAGE . 'jetcache/js/' . $output_js_all_md5 . '.js';
                $go_combine = false;

	            foreach($this->externalJS as $num => $js_array) {

					if (isset($this->jetcache_settings['minify_js_preload']) && $this->jetcache_settings['minify_js_preload'] != '') {

						$minify_js_preload = $this->jetcache_str_access($this->jetcache_settings['minify_js_preload'], $js_array[1]);

						if (!$minify_js_preload) {
							$this->externalJS[$num]['preload'] = true;
						}
					}
                    $url_without = str_replace($this->config_url, '', $js_array[1]);
                    // for bug file_exists
					$js_file = str_replace(array('"', "'", '..', '<', '>'), array('', '', '', '', ''), $dir_root . ltrim($url_without, '/'));

                    if (strpos($js_file, '?') !== false && strpos($js_file, PHP_EOL) === false) {
                    	$js_file = substr($js_file, 0, strpos($js_file, '?'));
                    }

	            	if ((strpos($js_file, PHP_EOL) === false && is_file($js_file) && file_exists($js_file)) || (isset($js_array['script']) && $js_array['script'])) {

	            		if (isset($js_array['script']) && $js_array['script']) {
	            			$js_file_content = $js_array[1];
	            		} else {
	            			$js_file_content = file_get_contents($js_file);
	            		}

						if ((isset($this->jetcache_settings['minify_js_combine_status']) && $this->jetcache_settings['minify_js_combine_status']) || (isset($this->jetcache_settings['minify_js_combine_inline']) && $this->jetcache_settings['minify_js_combine_inline'])) {
	                        $minify_js_ex_combine_status = true;
	                        if (isset($js_array['script']) && $js_array['script'] && isset($this->jetcache_settings['minify_js_combine_inline']) && !$this->jetcache_settings['minify_js_combine_inline']) {
	                        	$minify_js_ex_combine_status = false;
	                        }
						} else {
							$minify_js_ex_combine_status = false;
						}

						if ($minify_js_ex_combine_status && isset($this->jetcache_settings['minify_js_ex_combine']) && $this->jetcache_settings['minify_js_ex_combine'] != '') {
							$minify_js_ex_combine_status = $this->jetcache_str_access($this->jetcache_settings['minify_js_ex_combine'], $js_array[1]);
						}

						$minify_js_compress_status = true;
						$minify_js_compress_status = $this->jetcache_str_access($this->jetcache_settings['minify_js_ex_compress'], $js_array[1]);

                        if (isset($js_array['script']) && $js_array['script']) {
                        	$options['prependRelativePath'] = '';
                        } else {
                        	$options['prependRelativePath'] = str_ireplace($dir_root, '/', str_ireplace('\\', '/', dirname($js_file))  . '/');
                        }

	                    if ($minify_js_ex_combine_status) {

 		                    $this->externalJS[$num]['combine'] = true;

	                        if (!file_exists($js_file_combine) || $go_combine) {
	                            $js_file_md5 = md5($js_file);

		                        $js_file_content_cache = DIR_IMAGE . 'jetcache/js_cache/' . $js_file_md5 . '.js';
				                if (!file_exists($js_file_content_cache)) {

									if ($minify_js_compress_status) {
			                    		$options['filename'] = $js_file;
			                    		$js_file_content = $this->jetcache_js_compress($js_file_content, $options);
			                    		$js_file_content = $this->jetcache_js_rewrite($js_file_content, $options);
			                    	}

									file_put_contents($js_file_content_cache, $js_file_content, LOCK_EX);
								} else {
									$js_file_content = file_get_contents($js_file_content_cache);
								}

	                            $go_combine = true;
	                            if (strpos($js_file, PHP_EOL) === false && is_file($js_file) && file_exists($js_file)) {
		                            $try_combine = PHP_EOL . "/* Jet Cache: " . $js_array[1] . " */" . PHP_EOL .  "try{" . PHP_EOL;
	                            	$try_combine_end = PHP_EOL . "}catch(e){console.log('Error in: " . $js_array[1] . ": '+e.message);};" . PHP_EOL;
	                            	//$try_combine = '';
	                            	//$try_combine_end = PHP_EOL;
	                            } else {
	                            	$try_combine = '';
	                            	$try_combine_end = PHP_EOL;
	                            }
			              		file_put_contents($js_file_combine, $try_combine . $js_file_content . $try_combine_end, FILE_APPEND | LOCK_EX);
		              		}
		            	} else {

		            		$this->externalJS[$num]['combine'] = false;

							if (isset($this->jetcache_settings['minify_js_in_inline']) && $this->jetcache_settings['minify_js_in_inline'] != '') {
								$minify_js_in_inline_status = true;
								$minify_js_in_inline_status = $this->jetcache_str_access($this->jetcache_settings['minify_js_in_inline'], $js_array[1]);

								if (!$minify_js_in_inline_status) {
									if ($minify_js_compress_status) {
										if (isset($this->jetcache_settings['minify_js_inline_compress_status']) && $this->jetcache_settings['minify_js_inline_compress_status']) {
											$js_file_content = $this->jetcache_js_compress($js_file_content, $options);
										}
										$js_file_content = $this->jetcache_js_rewrite($js_file_content, $options);
									}

								    $this->externalJS[$num]['inline'] = true;

								    $this->externalJS[$num][0] = $js_file_content;
							    }
		            		}
	                        if (isset($js_array['script']) && $js_array['script']) {
	                        	if ($minify_js_compress_status) {
	                        		if (isset($this->jetcache_settings['minify_js_inline_compress_status']) && $this->jetcache_settings['minify_js_inline_compress_status']) {
	                        			$js_file_content = $this->jetcache_js_compress($js_file_content, $options);
	                        		}
	                        		$js_file_content = $this->jetcache_js_rewrite($js_file_content, $options);
	                        	}
                                $this->externalJS[$num][1] = $js_file_content;
	                        }
		            	}
	            	}
	            }
				if (file_exists($js_file_combine)) {
					$this->js_combine = '/' . str_ireplace($dir_root, '', str_ireplace('\\', '/', $js_file_combine)) . '?' . filemtime($js_file_combine);
				}
			}
		}
		return $this->outputHTML;
	}

    protected function _removeExternalJS($script_js) {
     	if (stripos($script_js[0], "'<script") === false && stripos($script_js[0], '"<script') === false) {
	        $script_js = array_slice($script_js, 1);
	        $script_js_md5 = md5(json_encode($script_js[0]));
	        $script_js['md5'] = $script_js_md5;
	        $this->externalJS[$script_js_md5] = $script_js;
    	    return $script_js_md5;
        } else {
	        return $script_js[0];
        }
    }

    protected function _removeInlineJS($script_js) {
		//if (stripos($script_js[0], "'<script") === false && stripos($script_js[0], '"<script') === false) {
	        $script_js = array_slice($script_js, 1);

	        $script_js_title = $script_js[1];
	        $script_js_content = $script_js[2];

	        $script_js[1] = $script_js_content;
	        $script_js[2] = $script_js_title;

	        $script_js_md5 = md5(json_encode($script_js[0]));
	        $script_js['md5'] = $script_js_md5;
	        $this->inlineJS[$script_js_md5] = $script_js;
	    	return $script_js_md5;
       // } else {
	   //     return $script_js[0];
       // }
    }

	public function jc_lazy($output) {

		if (!$this->jc_ajax && $this->access_exeptions()) {

			if (isset($this->jetcache_settings['lazy_tokens']) && $this->jetcache_settings['lazy_tokens'] != '') {

				if (isset($this->registry->get('request')->get['route'])) {
					$this->route = $this->registry->get('request')->get['route'];
				} else {
					$this->route = 'common/home';
				}

                $access_status = true;
				if (isset($this->jetcache_settings['lazy_ex_route']) && $this->jetcache_settings['lazy_ex_route'] != '') {
					$access_status = $this->jetcache_str_access($this->jetcache_settings['lazy_ex_route'], $this->route);
				}

                if ($access_status) {

					$jc_lazy_js = '<script>
document.addEventListener("DOMContentLoaded",function(){function a(){c=[].slice.call(document.querySelectorAll("img[data-src]"));for(var a=window.pageYOffset,d=0;d<c.length;d++)jc_img=c[d],jc_img.getBoundingClientRect().top<=window.innerHeight&&0<=jc_img.getBoundingClientRect().bottom&&"none"!==getComputedStyle(jc_img).display&&"undefined"!=jc_img.dataset.src&&(jc_img.src=jc_img.dataset.src,delete jc_img.dataset.src);0==c.length&&(document.removeEventListener("scroll",b),window.removeEventListener("resize",b),window.removeEventListener("orientationChange",b))}function b(){d&&clearTimeout(d),d=setTimeout(a(),10)}var c,d;a(),document.addEventListener("scroll",b),window.addEventListener("resize",b),window.addEventListener("orientationChange",b),window.addEventListener("DOMNodeInserted",function(){d=setTimeout(a(),100)})});
</script>';
					$lazy_tokens_array = explode(PHP_EOL, trim($this->jetcache_settings['lazy_tokens']));
				    foreach($lazy_tokens_array as $num => $lazy_token) {
				    	$lazy_token = trim($lazy_token);
						if (isset($lazy_token[0]) && $lazy_token[0] != '#' && $lazy_token != '' && strpos($lazy_token, '|') !== false) {
		                	$tokens_array = explode('|', trim($lazy_token));
		                	$output = str_ireplace(html_entity_decode(trim($tokens_array[0]), ENT_QUOTES, 'UTF-8'), html_entity_decode(trim($tokens_array[1]), ENT_QUOTES, 'UTF-8'), $output);
						}
				    }

					$output = str_ireplace('</head>', $jc_lazy_js . '
</head>', $output);

				}
			}
		}
		return $output;
	}

	public function hook_Registry_get() {
		if ($this->registry->get('seocms_cache_status') && !$this->flag_cache_access_output) {
			if (!$this->registry->get('hook_Registry_get')) {
	            $this->page_from_cache();
	            if (isset($this->jetcache_settings['pages_forsage']) && $this->jetcache_settings['pages_forsage']) {
    	        	$this->registry->set('hook_Registry_get', true);
    	        }
            }
		}
	}

	public function page_from_cache() {
		if ($this->jetcache_settings['jetcache_widget_status'] && $this->jetcache_settings['pages_status']) {

			$this->webp = $this->getWebp();

            if (!$this->webp
            	&& isset($this->jetcache_settings['image_status']) && $this->jetcache_settings['image_status']
            	&& isset($this->jetcache_settings['image_webp_status']) && $this->jetcache_settings['image_webp_status']) {
				if (!isset($this->request->cookie['jetcache_webp'])) {
	            	return false;
	            }
			}

			$settings_name = array();

			$this->set_cache_name($settings_name);

			$this->registry->set('jetcache_page_filename', $this->sc_cache_name);

			if (!$this->config->get('blog_work')) {
				$this->config->set('blog_work', true);
			}

 			$cache_content = $this->cache->get($this->sc_cache_name);

			if ($this->config->get('blog_work')) {
				$this->config->set('blog_work', false);
			}

			if (empty($cache_content)) {

				if (isset($this->jetcache_settings['cachecontrol_status']) && $this->jetcache_settings['cachecontrol_status']) {
				    $this->response->addHeader('Cache-Control: public, max-age=31536000');
				}

				if (isset($this->jetcache_settings['expires_status']) && $this->jetcache_settings['expires_status']) {
					$this->response->addHeader('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 604800));
				}

				if (!$this->jc_ajax && isset($this->jetcache_settings['lastmod_status']) && $this->jetcache_settings['lastmod_status']) {
				    $this->last_modified = time();

					$this->response->addHeader('Last-Modified: ' . gmdate('D, d M Y H:i:s \G\M\T', $this->last_modified));
				}
				return false;
			} else {
				$this->registry->set('jetcache_page_content', true);
			}

            $cache_filename = $this->registry->get('jetcache_cache_filename');

			if (isset($cache_content['output']) && $cache_content['output'] != '' && !$this->registry->get('jetcache_response_set_cache')) {

                if (!isset($cache_content['route'])) {
                	return false;
                }

				$this->registry->get('request')->get['route'] = $cache_content['route'];

				$this->flag_cache_access_output = $this->cache_access_output();

				if (!$this->flag_cache_access_output) {

					if ($this->jc_ajax) {
		                if (file_exists($cache_filename) && isset($this->jetcache_settings['lastmod_status']) && $this->jetcache_settings['lastmod_status'] && isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
							$cache_filemtime = filemtime($cache_filename);
							if ($this->last_modified == '') {
							   	$this->last_modified = $cache_filemtime;
							}
				          	$this->response->addHeader('Last-Modified: ' . gmdate('D, d M Y H:i:s \G\M\T', $this->last_modified));
				        }
					}

					return false;
				}

				$jetcache_content = $cache_content['output'];
			    $jetcache_headers = $cache_content['headers'];
			    $jetcache_time = $cache_content['time'];
			    $jetcache_queries = $cache_content['queries'];
			    $jetcache_filename = $cache_content['filename'];

				if (!empty($jetcache_headers)) {
					$jetcache_headers_now = $this->response->jetcache_getHeaders();

					foreach ($jetcache_headers as $jc_header) {
			    		$jetcache_headers_now_exists = false;
			    		foreach ($jetcache_headers_now as $jc_header_now) {
			    			if ($jc_header == $jc_header_now) {
			    				$jetcache_headers_now_exists = true;
			    				break;
			    			}
			    		}
			    		if (!$jetcache_headers_now_exists) {
			    			$this->response->addHeader($jc_header);
			    		}
					}
			    }

                $this->updateVars();

			    if (($this->admin_logged && isset($this->jetcache_settings['jetcache_info_status']) && $this->jetcache_settings['jetcache_info_status']) || (isset($this->jetcache_settings['jetcache_info_demo_status']) && $this->jetcache_settings['jetcache_info_demo_status']) ) {

					$time_visual['start'] = $this->registry->get('jetcache_opencart_core_start');
				    $time_visual['end'] = microtime(true);

				    $time_visual['load'] = $jetcache_time;
				    $time_visual['queries'] = $jetcache_queries;
				    $time_visual['filename'] = $jetcache_filename;

					$this->registry->set('jetcache_output_visual', $time_visual);
				}

				if (isset($this->jetcache_settings['cachecontrol_status']) && $this->jetcache_settings['cachecontrol_status']) {
				    $this->response->addHeader('Cache-Control: public, max-age=31536000');
				}

				if (isset($this->jetcache_settings['expires_status']) && $this->jetcache_settings['expires_status']) {
					$this->response->addHeader('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 604800));
				}


                if (file_exists($cache_filename) && isset($this->jetcache_settings['lastmod_status']) && $this->jetcache_settings['lastmod_status'] && isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {

					$cache_filemtime = filemtime($cache_filename);
					if ($this->last_modified == '') {
					   	$this->last_modified = $cache_filemtime;
					}

			        if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= ($this->last_modified - 60)) {
                        $this->response->addHeader($this->registry->get('request')->server['SERVER_PROTOCOL'] . '/1.1 304 Not Modified');
			        }
		          	$this->response->addHeader('Last-Modified: ' . gmdate('D, d M Y H:i:s \G\M\T', $this->last_modified));
		        }

				$this->response->setOutput($jetcache_content);

				if ($this->config->get('config_compression') > 0) {
					$this->response->setCompression($this->config->get('config_compression'));
				}

                $this->registry->set('page_fromcache', true);

				$this->response->output();

				exit();
			}
		}
	}

	public function page_to_cache() {
        if ($this->jetcache_settings['jetcache_widget_status'] && $this->jetcache_settings['pages_status'] && !$this->registry->get('page_fromcache')) {

			if (!$this->registry->get('jetcache_page_content') && $this->cache_access_output()) {

	            $settings_name = array();

				if ($this->registry->get('seocms_cache_status')) {

					$cache_output = $this->response->jetcache_getOutput();

					$cache_headers = $this->response->jetcache_getHeaders();

					if (!$this->config->get('blog_work')) {
						$this->config->set('blog_work', true);
						$off_blog_work = true;
					} else {
						$off_blog_work = false;
					}

					if (isset($cache_output) && is_string($cache_output) && $cache_output != '') {

						if ($this->registry->get('jetcache_page_filename')) {
	        				$this->sc_cache_name = $this->registry->get('jetcache_page_filename');
				        } else {
							$this->set_cache_name($settings_name);
						}

					    $sc_time_end = microtime(true);

						$cache['time'] = $sc_time_end - $this->registry->get('jetcache_opencart_core_start');

					    if (is_callable(array('DB', 'get_sc_jetcache_query_count'))) {
					    	$cache['queries'] = $this->db->get_sc_jetcache_query_count();
					    } else {
					    	$cache['queries'] = '';
					    }

						if (isset($this->registry->get('request')->get['route'])) {
							$cache['route'] = $this->registry->get('request')->get['route'];
						} else {
							$cache['route'] = 'common/home';
						}
					    $cache['filename'] = $this->sc_cache_name;
					    $cache['headers'] = $cache_headers;
					    $cache['output'] = $cache_output;

                        if ($this->last_modified == '') {
                        	$this->last_modified = time();
                        }
                        $this->cache->set($this->sc_cache_name, $cache, $this->last_modified);

                        if (isset($this->jetcache_settings['edit_product_id']) && $this->jetcache_settings['edit_product_id']) {
							$jetcache_product_id_pages = $this->registry->get('jetcache_product_id_pages');
				        	if (!empty($jetcache_product_id_pages)) {
					        	$product_id_array = $jetcache_product_id_pages;
					        	foreach ($jetcache_product_id_pages as $product_id => $array_product_id) {
				             		$product_id_array[$product_id][$this->sc_cache_name] = $this->sc_cache_name;
					        	}
					        	$this->registry->set('jetcache_product_id_pages', $product_id_array);
				        	}
			        	}

					 	$this->jetcache_product_id_update('pages');

					 	$this->registry->set('jetcache_product_id_pages', array());
					}

					if ($off_blog_work) {
						$this->config->set('blog_work', false);
					}

					$this->registry->set('jetcache_response_set_cache', true);

				}
	       	}
       	}
	}

	public function cache_access_output($query = false) {

        $access_status = false;
        $home = false;

        if ($this->jc_ajax) {
        	$this->jetcache_settings['jetcache_widget_status'] = false;
        	return $access_status = false;
        }

		if (!isset($this->registry->get('request')->get['route'])) {
			if (!isset($this->registry->get('request')->get['_route_'])) {
            	$home = true;
			}
		}

		if (isset($this->registry->get('request')->get['record_id']) && isset($this->registry->get('request')->get['blog_id'])) {
			unset($this->registry->get('request')->get['blog_id']);
		}

		if ($this->jc_ajax) {
			if (!$this->jetcache_buildcache) {
				return $access_status = false;
			}
		}

		if (isset($this->registry->get('request')->server['REQUEST_METHOD']) && $this->registry->get('request')->server['REQUEST_METHOD'] == 'POST') {
			if (!$this->jetcache_buildcache) {
				return $access_status = false;
			}
		}

		if ($home || (isset($this->registry->get('request')->get['route']) && $this->registry->get('request')->get['route'] != 'error/not_found')) {

	      	if (isset($this->jetcache_settings['store']) && in_array($this->config->get('config_store_id'), $this->jetcache_settings['store'])) {
	       		$access_status = true;
	      	} else {
				return $access_status = false;
			}

			if (isset($this->jetcache_settings['jetcache_widget_status']) && $this->jetcache_settings['jetcache_widget_status']) {
				$access_status = true;
			} else {
				return $access_status = false;
			}

			if (isset($this->jetcache_settings['pages_status']) && $this->jetcache_settings['pages_status']) {
				$access_status = true;
			} else {
				if (!$query) {
					return $access_status = false;
				}
			}

			if (!$this->registry->get('admin_work')) {
	        	$access_status = true;
			} else {
	       		//if (!$this->jetcache_buildcache) {
	       			return $access_status = false;
	       		//}
			}
			$access_status = $this->access_exeptions();

        }

		return $access_status;
	}

	public function info() {

        if ($this->access_exeptions()) {

	    	if ($this->registry->get('seocms_cache_status')) {
	    		$visual_html = '';

				if (!$this->jc_ajax) {

			       	if (($this->admin_logged && isset($this->jetcache_settings['jetcache_info_status']) && $this->jetcache_settings['jetcache_info_status']) || (isset($this->jetcache_settings['jetcache_info_demo_status']) && $this->jetcache_settings['jetcache_info_demo_status']) ) {

						if (is_array($this->registry->get('jetcache_output_visual'))) {
							// page_from_cache
					        $time_visual = $this->registry->get('jetcache_output_visual');
							$visual_html = $this->visual($time_visual);
						} else {

							$time_visual['start'] = $this->registry->get('jetcache_opencart_core_start');
					        $time_visual['end'] = microtime(true);

							$time_visual['load'] = round($time_visual['end'] - $this->registry->get('jetcache_opencart_core_start'), 3);

					        if (is_callable(array('DB', 'jc_setRegistry'))) {
					        	$time_visual['queries'] = $this->db->get_sc_jetcache_query_count();
					        } else {
					        	$time_visual['queries'] = '';
					        }
							$visual_html = $this->visual($time_visual);
						}
					}
				}
	        	return $visual_html;
	        }
       	} else {
       		return NULL;
       	}
	}

	public function visual($arg) {

        if (SC_VERSION > 20) {
        	$this->registry->get('load')->language('jetcache/jetcache');
        } else {
        	$this->registry->get('language')->load('jetcache/jetcache');
        }

        if (SC_VERSION > 15) {
			if ((isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == '1')) || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https') || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) == 'on'))) {
		       	$this->url_link_ssl = true;
	        } else {
	        	$this->url_link_ssl = false;
	        }
        } else {
        	$this->url_link_ssl = 'SSL';
        }
        $this->data['jetcache_url_cache_remove'] = $this->url->link('jetcache/jetcache/cacheremove', '', $this->url_link_ssl);
        $this->data['icon'] = getSCWebDir(DIR_IMAGE).'jetcache/jetcache-icon.png';

        $html = '';

        $this->data['load'] = $arg['load'];

        $this->data['start'] = $arg['start'];
        $this->data['end'] = $arg['end'];
        $this->data['queries'] = $arg['queries'];
        if (isset($arg['filename'])) {
        	$this->data['filename'] = $arg['filename'];
        } else {
        	$this->data['filename'] = '';
        }

        $this->data['cache'] = round($arg['end'] - $arg['start'], 3);

        if ($this->registry->get('jetcache_opencart_core_start'))  {
			$this->data['cache_all'] = round($arg['end'] - $this->registry->get('jetcache_opencart_core_start'), 3);
		} else {
			$this->data['cache_all'] = 0;
		}

        $this->data['rate'] = round($this->data['load'] / $this->data['cache'], 0);

		$this->data['url_jetcache_buy'] = $this->language->get('url_jetcache_buy');
        $this->data['entry_jetcache'] = $this->language->get('entry_jetcache');
        $this->data['entry_jetcache_buy'] = $this->language->get('entry_jetcache_buy');
        $this->data['text_jetcache_loading'] = $this->language->get('text_jetcache_loading');
        $this->data['text_jetcache_cache_remove_fail'] = $this->language->get('text_jetcache_cache_remove_fail');
        $this->data['text_jetcache_url_cache_remove'] = $this->language->get('text_jetcache_url_cache_remove');
        $this->data['entry_jetcache_db'] = $this->language->get('entry_jetcache_db');
        $this->data['text_jetcache_queries'] = $this->language->get('text_jetcache_queries');
        $this->data['entry_jetcache_queries'] = $this->language->get('entry_jetcache_queries');
        $this->data['entry_jetcache_queries_cache'] = $this->language->get('entry_jetcache_queries_cache');
        $this->data['entry_queries_count_cache'] = $this->language->get('entry_queries_count_cache');
        $this->data['entry_queries_time_cache'] = $this->language->get('entry_queries_time_cache');
        $this->data['entry_jetcache_sec'] = $this->language->get('entry_jetcache_sec');
        $this->data['entry_jetcache_opencart_core'] = $this->language->get('entry_jetcache_opencart_core');
        $this->data['entry_jetcache_sec'] = $this->language->get('entry_jetcache_sec');
        $this->data['entry_jetcache_pages'] = $this->language->get('entry_jetcache_pages');
        $this->data['entry_jetcache_withoutcache'] = $this->language->get('entry_jetcache_withoutcache');
        $this->data['entry_jetcache_cache'] = $this->language->get('entry_jetcache_cache');
        $this->data['entry_jetcache_sec'] = $this->language->get('entry_jetcache_sec');

        $this->data['entry_count_model_cached'] = $this->language->get('entry_count_model_cached');
        $this->data['count_model_cached'] = $this->count_model_cached;

        $this->data['entry_count_query_cached'] = $this->language->get('entry_count_query_cached');
        $this->data['count_query_cached'] = $this->count_query_cached;

        $this->data['entry_count_cont_cached'] = $this->language->get('entry_count_cont_cached');
        $this->data['count_cont_cached'] = $this->count_cont_cached;

        if (is_callable(array('DB', 'get_sc_jetcache_query_count'))) {
        	$this->data['queries_cache'] = $this->db->get_sc_jetcache_query_count();
        } else {
        	$this->data['queries_cache'] = '';
        }

        if (is_callable(array('DB', 'get_sc_jetcache_query_count_cache'))) {
        	$this->data['queries_count_cache'] = $this->db->get_sc_jetcache_query_count_cache();
        } else {
        	$this->data['queries_count_cache'] = '';
        }
        if (is_callable(array('DB', 'get_sc_jetcache_query_time_cache'))) {
        	$this->data['queries_time_cache'] = $this->db->get_sc_jetcache_query_time_cache();
        } else {
        	$this->data['queries_time_cache'] = '';
        }

        if ($this->registry->get('jetcache_opencart_core')) {
        	$this->data['jetcache_opencart_core'] = $this->registry->get('jetcache_opencart_core') - $this->registry->get('jetcache_opencart_core_start');
        } else {
        	$this->data['jetcache_opencart_core'] = 0;
        }

        if (isset($this->data['queries']) && $this->data['queries'] > 0 && $this->data['queries_cache'] > 0) {
        	$this->data['round_queries_queries_cache'] = round($this->data['queries'] / $this->data['queries_cache'], 0);
        } else {
        	$this->data['round_queries_queries_cache'] = 0;

        }
        $this->data['round_queries'] = round($this->data['queries'], 3);
        $this->data['round_queries_cache'] = round($this->data['queries_cache'], 3);
        $this->data['round_queries_count_cache'] = round($this->data['queries_count_cache'], 5);
        $this->data['round_queries_time_cache'] = round($this->data['queries_time_cache'], 3);
        $this->data['round_jetcache_opencart_core'] = round($this->data['jetcache_opencart_core'], 3);
		$this->data['round_load'] = round($this->data['load'], 3);
		$this->data['round_cache'] = round($this->data['cache'] - $this->data['jetcache_opencart_core'], 3);
		$this->data['round_cache_all'] = round($this->data['cache_all'], 3);

        if (($this->data['round_queries'] > $this->data['round_queries_cache']) && $this->data['count_query_cached'] == 0) {
        	$this->data['count_query_cached'] = $this->data['round_queries'] - $this->data['round_queries_cache'];
        	$this->data['entry_count_query_cached'] = $this->language->get('entry_jetcache_queries_cached');
        }

        $this_template = 'default';
        $this->data['language'] = $this->language;

        $template = 'agootemplates/jetcache/visual';

        if (SC_VERSION < 30) {
			$template = $template . '.tpl';
			$file_ext = '';
		} else {
			$file_ext = '.tpl';
		}

		if (file_exists(DIR_TEMPLATE . $this_template . '/template/'. $template . $file_ext) && is_file(DIR_TEMPLATE . $this_template . '/template/'. $template . $file_ext)) {
			$this->template = $template;
			if (SC_VERSION < 22) {
				$this->template = $this_template . '/template/'. $template;
			}
			if ($this->registry->get('page_fromcache') && SC_VERSION > 23)  {
				$this->template = $this_template . '/template/' . $template;
			}
		}

		if (SC_VERSION < 20) {
			$this->children = array();
			$html = $this->render();
		} else {

			if (SC_VERSION > 23) {
				$this->template_engine = $this->config->get('template_engine');
				$this->config->set('template_engine', 'template');

				if (!$this->registry->get('page_fromcache')) {
					$this->template_directory = $this->config->get('template_directory');
	            	$this->config->set('template_directory', 'default/template/');
	            }
	        }

	        if (!is_array($this->data))	$this->data = array();

			$html = $this->load->view($this->template, $this->data);

			if (SC_VERSION > 23) {

			 	if (!$this->registry->get('page_fromcache')) {
			 		$this->config->set('template_directory', $this->template_directory);
			 	}

				$this->config->set('template_engine', $this->template_engine);
	        }
		}

		return $html;
	}

	public function query_model_access($class_function) {
        $access_status = false;

		if (isset($class_function[1]['class'])) {
	    	$query_model_class = $class_function[1]['class'];
            $query_model_function = $class_function[1]['function'];
		} else {
			$query_model_class = '';
			$query_model_function = '';
		}
        if (!isset($this->jetcache_settings['query_model']) || empty($this->jetcache_settings['query_model'])) {
        	$this->jetcache_settings = $this->config->get('asc_jetcache_settings');
        }

	    foreach($this->jetcache_settings['query_model'] as $query_model) {
	    	if ($query_model['status']) {
	    		if (($query_model['model'] == $query_model_class && $query_model['method'] == '') || ($query_model['model'] == $query_model_class && $query_model['method'] == $query_model_function)) {
	    			return $access_status = true;
	    		}
	    	}
		}

		return $access_status = false;
	}

    public function access_exeptions() {

		if (isset($this->jetcache_settings['ex_route']) && !empty($this->jetcache_settings['ex_route'])) {

			if (!is_array($this->flag_access_exeptions) && is_bool($this->flag_access_exeptions)) {
				return $this->flag_access_exeptions;
			}
            $this->flag_access_exeptions = false;

			if (isset($this->registry->get('request')->get['route'])) {
				$routes = explode('/', $this->registry->get('request')->get['route']);
			} else {
				$routes = array();
			}
	        $routes_count = count($routes);

		    foreach($this->jetcache_settings['ex_route'] as $ex_route) {
				if ($ex_route['status']) {
		    		$ex_routes = explode('/', $ex_route['route']);
	                $ex_routes_count = count($ex_routes);
					if ($ex_routes_count <= $routes_count) {

	                    $new_array = array();
	                    $prom_array = array();
					    $key_search = array_search('%', $ex_routes);
					    if ($routes_count - $ex_routes_count > 0) {
	                    	$prom_array = array_fill($key_search, $routes_count - $ex_routes_count , '%');
	                    }

	                    array_splice($ex_routes, $key_search, 0, $prom_array);

		                $key = 0;
						foreach ($routes as $routes_val) {
	                    	if ($ex_routes[$key] == '%') {
	                    		$ex_routes[$key] = $routes_val;
	                    	}
							$key++;
		    			}

	                    if ($routes == $ex_routes)  {
	                    	 $this->flag_access_exeptions = false;
	                    	 return $access_status = false;
	                    }
					}
				}
		    }
		}

		if (isset($this->jetcache_settings['ex_uri']) && $this->jetcache_settings['ex_uri'] != '') {

			$ex_uri_array = explode(PHP_EOL, trim($this->jetcache_settings['ex_uri']));

		    foreach($ex_uri_array as $num => $ex_uri) {
		    	$ex_uri = trim($ex_uri);
				if (isset($ex_uri[0]) && $ex_uri[0] != '#' && $ex_uri != '' && strpos($this->request_uri_trim, $ex_uri) !== false) {
					$this->flag_access_exeptions = false;
					return $access_status = false;
				}
		    }
		}
        $this->flag_access_exeptions = true;
        return $this->flag_access_exeptions;

    }

	public function updateVars() {
		// in common/footer
		$this->addOnline();

		if (isset($this->registry->get('request')->get['product_id'])) {
			$this->updateViewed();
		}

		if ((isset($this->registry->get('request')->get['record_id']) && isset($this->registry->get('request')->get['route']) && $this->registry->get('request')->get['route'] == 'record/record') ||
		   	(isset($this->registry->get('request')->get['blog_id']) && isset($this->registry->get('request')->get['route']) && $this->registry->get('request')->get['route'] == 'record/blog'))
		{
			if (isset($this->registry->get('request')->get['record_id'])) {
				$this->countRecordUpdate();
			}
			if ($this->checkAccessBlogRecord()) {
				$this->response->addHeader($this->registry->get('request')->server['SERVER_PROTOCOL'] . '/1.1 200 OK');
			} else {
				$this->response->addHeader($this->registry->get('request')->server['SERVER_PROTOCOL'] . '/1.1 404 Not Found');
			}
		}
	}

	private function countRecordUpdate() {
		$msql = "UPDATE `" . DB_PREFIX . "record` SET `viewed`=`viewed` + 1 WHERE `record_id`='" . (int) ($this->db->escape($this->registry->get('request')->get['record_id'])) . "'";
		$this->db->query($msql);
	}

	private function checkAccessBlogRecord() {
		$check = false;
		if (isset($this->seocms_settings['latest_widget_status']) && $this->seocms_settings['latest_widget_status']) {
			if (!$this->config->get('ascp_customer_groups')) {
				agoo_cont('record/customer', $this->registry);
				$data = $this->controller_record_customer->customer_groups($this->seocms_settings);
				$this->config->set('ascp_customer_groups', $data['customer_groups']);
			} else {
				$data['customer_groups'] = $this->config->get('ascp_customer_groups');
			}

			if (isset($this->registry->get('request')->get['record_id']) && isset($this->registry->get('request')->get['route']) && $this->registry->get('request')->get['route'] == 'record/record') {
	   			$this->load->model('record/record');

				$record_info = $this->model_record_record->getRecord($this->registry->get('request')->get['record_id']);
				if ($record_info) {
					$check = true;
				} else {
					$check = false;
				}
			}
			if (isset($this->registry->get('request')->get['blog_id']) && isset($this->registry->get('request')->get['route']) && $this->registry->get('request')->get['route'] == 'record/blog') {
				$this->load->model('record/blog');

				$blog_info = $this->model_record_blog->getBlog($this->registry->get('request')->get['blog_id']);
				if ($blog_info) {
					$check = true;
				} else {
					$check = false;
				}
			}
		}
		return $check;
	}

	public function addOnline() {
		if ($this->config->get('config_customer_online')) {
			$this->load->model('tool/online');

			if (isset($this->registry->get('request')->server['REMOTE_ADDR'])) {
				$ip = $this->registry->get('request')->server['REMOTE_ADDR'];
			} else {
				$ip = '';
			}

			if (isset($this->registry->get('request')->server['HTTP_HOST']) && isset($this->registry->get('request')->server['REQUEST_URI'])) {
				$url = $this->url_protocol . '://' . $this->registry->get('request')->server['HTTP_HOST'] . $this->registry->get('request')->server['REQUEST_URI'];
			} else {
				$url = '';
			}

			if (isset($this->registry->get('request')->server['HTTP_REFERER'])) {
				$referer = $this->registry->get('request')->server['HTTP_REFERER'];
			} else {
				$referer = '';
			}
            if (SC_VERSION > 20) {
				$this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
			} else {
				$this->model_tool_online->whosonline($ip, $this->customer->getId(), $url, $referer);
			}
		}
	}

    public function updateViewed() {
    	$this->load->model('catalog/product');
    	$this->model_catalog_product->updateViewed((int)$this->registry->get('request')->get['product_id']);
    }

	public function model_method_access($model_model_class, $model_model_function = '') {
        $access_status = false;
		if (isset($this->jetcache_settings['jetcache_model_status']) && !$this->jetcache_settings['jetcache_model_status']) return $access_status;
        if (isset($this->jetcache_settings['model']) && !empty($this->jetcache_settings['model'])) {
	        foreach($this->jetcache_settings['model'] as $number => $model_model) {
	        	if ($model_model['status']) {
	        		if ((strtolower($model_model['model']) == strtolower($model_model_class) && (strtolower($model_model['method']) == '' || $model_model_function == '')) || (strtolower($model_model['model']) == strtolower($model_model_class) && strtolower($model_model['method']) == strtolower($model_model_function))) {
	        			return $access_status = $number;
	        		}
	        	}
			}
		}
		return $access_status = false;
	}

    public function model_to_cache($output, $route, $method, $args) {

			if ($this->jetcache_settings['jetcache_model_status']) {
				$class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', substr($route, 0, strrpos($route, '/')));
	            $settings = $this->model_method_access($class, $method);

				if ($settings !== false) {

					if ($this->cache_access_output(true)) {

		                $setting = $this->jetcache_settings['model'][$settings];

						$settings_name['type'] = 'model';
						$settings_name['route'] = str_replace('/', '_', strtolower($route));
						$settings_name['setting'] = $args;
						$settings_name['tuning'] = $setting;

						$this->set_cache_name($settings_name);

						if (!$this->config->get('blog_work')) {
							$this->config->set('blog_work', true);
							$off_blog_work = true;
						} else {
							$off_blog_work = false;
						}

		                if (isset($settings_name['tuning']['onefile']) && $settings_name['tuning']['onefile']) {
		                	$cache_data = $this->cache->get($this->sc_cache_name);
                            if (!is_array($cache_data)) $cache_data = array();
		                	$hache_args = md5($this->sc_cache_name . (var_export($args, true)));
		                    $cache_data[$hache_args] = $output;
		                    $output = $cache_data;
		                }

						$this->cache->set($this->sc_cache_name, $output);

                        $this->jetcache_product_id_update('model');
                        $this->registry->set('jetcache_product_id_model', array());

						if ($off_blog_work) {
							$this->config->set('blog_work', false);
						}

						return true;
					} else  {
						return false;
					}

		    	} else {
			    	return false;
		    	}

	    	}
    	return false;
    }

    public function model_from_cache($route, $method, $args) {

		if ($this->jetcache_settings['jetcache_model_status']) {
			$class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', substr($route, 0, strrpos($route, '/')));
	        $settings = $this->model_method_access($class, $method);

			if ($settings !== false) {

				if ($this->cache_access_output(true)) {
                   // $this->registry->set('jetcache_product_id_model', array());

		            $setting = $this->jetcache_settings['model'][$settings];

					$settings_name['type'] = 'model';
					$settings_name['route'] = str_replace('/', '_', strtolower($route));
					$settings_name['setting'] = $args;
					$settings_name['tuning'] = $setting;

					$this->set_cache_name($settings_name);

					if (!$this->config->get('blog_work')) {
						$this->config->set('blog_work', true);
						$off_blog_work = true;
					} else {
						$off_blog_work = false;
					}

                    $cache_data = $this->cache->get($this->sc_cache_name);

		            if (isset($settings_name['tuning']['onefile']) && $settings_name['tuning']['onefile']) {
		            	$hache_args = md5($this->sc_cache_name.(var_export($args, true)));
                        if (isset($cache_data[$hache_args])) {
                        	$this->count_model_cached++;
                        	return $cache_data[$hache_args];
                        } else {
                        	return false;
                        }
		            }

                    if ($cache_data !== false) {

                    	$this->count_model_cached++;
                    	return $cache_data;
                    } else {
                    	return false;
                    }

					if ($off_blog_work) {
						$this->config->set('blog_work', false);
					}

				} else  {
					return false;
				}

			} else {
		    	return false;
			}

	    }
    	return false;
    }

	public function writeLog() {

		if (((isset($this->jetcache_settings['query_log_status']) && $this->jetcache_settings['query_log_status']) ||
			(isset($this->jetcache_settings['cont_log_status']) && $this->jetcache_settings['cont_log_status']) ||
			(isset($this->jetcache_settings['session_log_status']) && $this->jetcache_settings['session_log_status'])) && $this->user_id
		) {

				$time_start = $this->registry->get('jetcache_opencart_core_start');

				if ($this->jc_ajax) {
					$ajax_status = 'AJAX';
				} else {
					$ajax_status = '';
				}
				$uri = $this->registry->get('request')->server['REQUEST_URI'];

		        if (SC_VERSION > 20) {
		        	$this->registry->get('load')->language('jetcache/jetcache');
		        } else {
		        	$this->registry->get('language')->load('jetcache/jetcache');
		        }

				if (is_callable(array('DB', 'get_sc_jetcache_db_log'))) {
			      	if (isset($this->jetcache_settings['query_log_file']) && $this->jetcache_settings['query_log_file'] != '' && isset($this->jetcache_settings['query_log_status']) && $this->jetcache_settings['query_log_status']) {
						$jc_db_log = $this->db->get_sc_jetcache_db_log();

					    $jc_log_file = $this->jetcache_settings['query_log_file'];

						$entry_jetcache_page = $this->language->get('entry_jetcache_page');
						$entry_jetcache_page_source = $this->language->get('entry_jetcache_page_source');
						$entry_jetcache_page_time = $this->language->get('entry_jetcache_page_time');
						$entry_jetcache_query_time = $this->language->get('entry_jetcache_query_time');
						$entry_jetcache_query_number = $this->language->get('entry_jetcache_query_number');
						$healthy = array('{Page}', '{Source}', '{Query time}', '{Page time}', '{Query number}');
						$yummy = array($entry_jetcache_page, $entry_jetcache_page_source, $entry_jetcache_page_time, $entry_jetcache_query_time, $entry_jetcache_query_number);
						$jc_db_log = str_ireplace($healthy, $yummy, $jc_db_log);

					    file_put_contents(DIR_LOGS . $jc_log_file, $jc_db_log, FILE_APPEND);

					}
				}

				if (isset($this->jetcache_settings['cont_log_status']) && $this->jetcache_settings['cont_log_status']) {
					$cont_log = '';
					if (!empty($this->jc_cont_log)) {
						$jc_cont_log = $this->jc_cont_log;
						foreach ($this->jc_cont_log as $hache => $cont) {
							if (!isset($cont['end'])) {
								$cont['end'] = $cont['start'];
								$cont['time'] = 0;
							}
							if (isset($cont['cache']) && $cont['cache']) {
								$from_cache = ' ' . $this->language->get('text_log_cache');
							} else {
								$from_cache = '';
							}
							if (isset($this->registry->get('request')->get['route'])) {
								$route = $this->registry->get('request')->get['route'];
							} else {
								$route = 'common/home';
							}
							if (isset($this->jetcache_settings['cont_log_maxtime']) && $this->jetcache_settings['cont_log_maxtime'] != '' && $cont['time'] >= $this->jetcache_settings['cont_log_maxtime']) {
								$cont_log = $cont_log . $ajax_status . '***** ' . $uri . ' ***** ' . $route . ' **********************************' . PHP_EOL;
				                $cont_log = $cont_log . $this->language->get('text_log_route') . $from_cache . ': ' . $cont['route'] . PHP_EOL;
				                foreach ($jc_cont_log as $jc_hache => $jc_cont) {
				                	if (!isset($jc_cont['end'])) {
					                	if (!isset($jc_cont['start'])) {
											$jc_cont['start'] = $cont['start'] = 0;
											$jc_cont['time'] = 0;
										}
										$jc_cont['end'] = $jc_cont['start'];
										$jc_cont['time'] = 0;
									}
				                	if (!isset($jc_cont['start'])) {
										$jc_cont['start'] = $cont['start'] = 0;
										$jc_cont['time'] = 0;
									}

				                	if ((($jc_cont['end'] - $time_start) > ($cont['end']  - $time_start)) && (($jc_cont['start'] - $time_start) < ($cont['start'] - $time_start))) {
				                		$cont_log = $cont_log . $this->language->get('text_log_child') . ': ' . $jc_cont['route'] . PHP_EOL;
				                	}
				                }
				                $cont_log = $cont_log . PHP_EOL . $this->language->get('text_log_start') . ': ' . round($cont['start'] - $time_start, 5) . PHP_EOL;
				                $cont_log = $cont_log . $this->language->get('text_log_end') . ': ' . round($cont['end'] - $time_start, 5) . PHP_EOL;
				                $cont_log = $cont_log . $this->language->get('text_log_time') . ': ' . round($cont['time'], 5) . PHP_EOL . PHP_EOL;

				                if ($cont['data'] != '[]') {

					                if (isset($this->jetcache_settings['cont_log_crop']) && $this->jetcache_settings['cont_log_crop']) {
				                		$cont_data = utf8_substr(html_entity_decode($cont['data'], ENT_QUOTES, 'UTF-8'), 0, 111) ;
					                } else {
					                	$cont_data  = $cont['data'];
					                }

				                	$cont_log = $cont_log . $this->language->get('text_log_data_md5') . ': ' . md5($cont['route'] . $cont['data']) . PHP_EOL . $this->language->get('text_log_data') . ': ' . $cont_data . PHP_EOL . PHP_EOL;
				                }
							}
						}
					}

		        	if (isset($this->jetcache_settings['cont_log_file']) && $this->jetcache_settings['cont_log_file'] != '') {
		            	file_put_contents(DIR_LOGS . $this->jetcache_settings['cont_log_file'], htmlspecialchars($cont_log) . PHP_EOL, FILE_APPEND);
		        	}
				}

				if (isset($this->jetcache_settings['session_log_status']) && $this->jetcache_settings['session_log_status']) {
		        	if (isset($this->jetcache_settings['session_log_file']) && $this->jetcache_settings['session_log_file'] != '') {
		            	file_put_contents(DIR_LOGS . $this->jetcache_settings['session_log_file'], 'SESSION: ' . htmlspecialchars(json_encode($this->session->data)) . PHP_EOL . PHP_EOL . 'COOKIE: ' . htmlspecialchars(json_encode($_COOKIE)) . PHP_EOL . PHP_EOL . PHP_EOL, FILE_APPEND);
		        	} else {
		        		$this->registry->set('log', new Log($this->config->get('config_error_filename')));
		        		$this->log->write('SESSION: ' . json_encode($this->session->data). PHP_EOL . PHP_EOL . 'COOKIE: ' . json_encode($_COOKIE) . PHP_EOL . PHP_EOL . PHP_EOL);
		        	}
				}
		}
	}



    public function query_to_cache($cache_output, $cont_route, $cont_setting = '' ) {
    	if (is_string($cont_route)) {

			$settings_name['type'] = 'query';
			$settings_name['route'] = str_replace('/', '_', $cont_route);
			$settings_name['setting'] = $cont_setting;
			$this->set_cache_name($settings_name);

			if (!$this->config->get('blog_work')) {
				$this->config->set('blog_work', true);
				$off_blog_work = true;
			} else {
				$off_blog_work = false;
			}

	        $this->cache->set($this->sc_cache_name, $cache_output);

			if ($off_blog_work) {
				$this->config->set('blog_work', false);
			}
		}
    }

    public function query_from_cache($cont_route, $cont_setting = '') {
        if (is_string($cont_route)) {

			$settings_name['type'] = 'query';
			$settings_name['route'] = str_replace('/', '_', $cont_route);
			$settings_name['setting'] = $cont_setting;
			$this->set_cache_name($settings_name);

            if (!$this->config->get('blog_work')) {
				$this->config->set('blog_work', true);
				$off_blog_work = true;
			} else {
				$off_blog_work = false;
			}

	        $cache_content = $this->cache->get($this->sc_cache_name);

			if ($off_blog_work) {
				$this->config->set('blog_work', false);
			}

			if (isset($cache_content) && !empty($cache_content)) {
				$this->count_query_cached++;
				return $cache_content;
			}
			return false;
        }
    }

    public function jetcache_cont_access($params) {
		if (isset($this->jetcache_settings['jetcache_widget_status']) && $this->jetcache_settings['jetcache_widget_status'] && (!isset($this->registry->get('request')->server['REQUEST_METHOD'])) || $this->registry->get('request')->server['REQUEST_METHOD'] != 'POST') {
			if (isset($this->jetcache_settings['store']) && in_array($this->config->get('config_store_id'), $this->jetcache_settings['store'])) {
		       	if (isset($this->jetcache_settings['cont_status']) && $this->jetcache_settings['cont_status']) {
			       if (isset($this->jetcache_settings['add_cont']) && !empty($this->jetcache_settings['add_cont'])) {
				       foreach($this->jetcache_settings['add_cont'] as $number => $add_cont) {
	         				if ($params == $add_cont['cont'] && $add_cont['status']) {
	         					$access_status = true;
	         					$this->jetcache_cont_number = $number;
	         					$access_status = $this->access_exeptions();
	         					return $access_status;
	         				}
				       }
			       }
				}
			}
		}
		return false;
    }



    public function cont_from_cache($cont_route, $cont_setting = '') {

        if (is_string($cont_route)) {

        	$this->registry->set('jetcache_product_id_cont', array());

			$settings_name['type'] = 'cont';
			$settings_name['route'] = str_replace('/', '_', $cont_route);
			$settings_name['setting'] = $cont_setting;
			$settings_name['tuning'] = $this->jetcache_settings['add_cont'][$this->jetcache_cont_number];

			$this->set_cache_name($settings_name);

            if (!$this->config->get('blog_work')) {
				$this->config->set('blog_work', true);
				$off_blog_work = true;
			} else {
				$off_blog_work = false;
			}

	        $cache_content = $this->cache->get($this->sc_cache_name);

			if ($off_blog_work) {
				$this->config->set('blog_work', false);
			}

           	if (isset($this->journal3->document)) {

            	if (isset($cache_content['j3_css']) && !empty($cache_content['j3_css'])) {

                    $j3_css_content = $cache_content['j3_css'];

					$j3_css_add = array();

            		if (is_array($j3_css_content)) {

                        $j3_css_add = $j3_css_content;
                        if (!empty($j3_css_add)) {
		            		foreach($j3_css_add as $css_link => $css_array) {

	             				if ($css_array['href'] != 'catalog/view/theme/journal3/stylesheet/style.css') {
	             					if ($css_array['href'] != 'catalog/view/theme/journal3/stylesheet/custom.css') {
	             						$this->journal3->document->addStyle($css_array['href'], $css_array['rel'], $css_array['media']);
	             					}
	             				}
	             			}
             			}
             		}
            	}

				if (isset($cache_content['j3_css_inline'])) {
					$ccs_now = $this->journal3->document->getJCCss();
                    $j3_css_inline_add_array = $this->array_diff_assoc_recursive($cache_content['j3_css_inline'], $ccs_now);
                    foreach ($j3_css_inline_add_array as $j3_css_inline_add_key => $j3_css_inline_add) {
						$this->journal3->document->addCss($j3_css_inline_add, $j3_css_inline_add_key);
					}
				}


				if (isset($cache_content['j3_js_inline'])) {
	            	if (isset($cache_content['j3_js_inline']) && !empty($cache_content['j3_js_inline'])) {

	            		$j3_js_inline_temp = $this->journal3->document->getJs();

	            		$j3_js_inline_add = $this->array_diff_assoc_recursive($cache_content['j3_js_inline'], $j3_js_inline_temp);

	            		if (!empty($j3_js_inline_add)) {
	            			$this->journal3->document->addJs($j3_js_inline_add);
	            		}

	            	}

	            }

            	if (isset($cache_content['j3_js']) && is_array($cache_content['j3_js']) && !empty($cache_content['j3_js'])) {

            		$j3_js_temp['header'] = $this->journal3->document->getScripts('header');
                    $j3_js_temp['footer'] = $this->journal3->document->getScripts('footer');

                    $j3_js_content = $cache_content['j3_js'];

            		$j3_js_add = array();
            		if (is_array($j3_js_temp) && is_array($j3_js_content)) {
	            		$j3_js_add = $this->array_diff_assoc_recursive($j3_js_content, $j3_js_temp);



	            		if (!empty($j3_js_add)) {
	            			foreach($j3_js_add as $pos => $js_links) {
	            				foreach($js_links as $pos_link => $js_link) {
	            					$this->journal3->document->addScript($js_link, $pos);
	            				}
	            			}
	            		}
            		}
            	}

            }

			if (isset($cache_content['output']) && $cache_content['output'] != '') {
				if (isset($cache_content['styles'])) {
					$styles = $cache_content['styles'];
	                foreach($styles as $style => $style_setting) {
						if (isset($style_setting['href']) && isset($style_setting['rel']) && isset($style_setting['media'])) {
	                		$this->document->addStyle($style_setting['href'], $style_setting['rel'], $style_setting['media']);
	                	}
	                }
				}
				if (isset($cache_content['links'])) {
					$links = $cache_content['links'];
	                foreach($links as $link => $link_setting) {
						if (isset($link_setting['href']) && isset($link_setting['rel'])) {
	                		$this->document->addLink($link_setting['href'], $link_setting['rel']);
	                	}
	                }
				}

				if (isset($cache_content['scripts']['header']) && !empty($cache_content['scripts']['header'])) {
					$scripts = $cache_content['scripts']['header'];
	                foreach($scripts as $script => $script_link) {
	                	if (SC_VERSION > 20) {
	                		$this->document->addScript($script_link, 'header');
	                	} else {
	                		$this->document->addScript($script_link);
	                	}
	                }
				}

                if (SC_VERSION > 20) {
					if (isset($cache_content['scripts']['footer']) && !empty($cache_content['scripts']['footer'])) {
						$scripts = $cache_content['scripts']['footer'];
		                foreach($scripts as $script => $script_link) {
		                	$this->document->addScript($script_link, 'footer');
		                }
					}
				}

				if ($cont_route == 'common/footer') {
					$this->addOnline();
				}

				$this->count_cont_cached++;
				return $cache_content['output'];
			} else {
				$this->jc_cont_document['links'] = $this->document->getLinks();
				$this->jc_cont_document['styles'] = $this->document->getStyles();
                if (isset($this->journal3->document)) {
            		$this->jc_cont_document['j3_css'] = $this->journal3->document->getStyles();

            		$this->jc_cont_document['j3_css_inline'] = $this->journal3->document->getJCCss();
            		$this->jc_cont_document['j3_js_inline'] = $this->journal3->document->getJs();

            		$this->jc_cont_document['j3_js']['header'] = $this->journal3->document->getScripts('header');
            		$this->jc_cont_document['j3_js']['footer']  = $this->journal3->document->getScripts('footer');
            		//$this->jc_cont_document['j3_fonts']  = $this->journal3->document->getFonts(false);
            	}

				if (SC_VERSION < 21) {
					$this->jc_cont_document['scripts']['header'] = $this->document->getScripts();
				} else {
					$this->jc_cont_document['scripts']['header'] = $this->document->getScripts('header');
					$this->jc_cont_document['scripts']['footer'] = $this->document->getScripts('footer');
				}

			}

			return false;
        }
    }

    public function cont_to_cache($cache_output, $cont_route, $cont_setting = '' ) {

    	if (is_string($cache_output) && is_string($cont_route)) {

			$settings_name['type'] = 'cont';
			$settings_name['route'] = str_replace('/', '_', $cont_route);
			$settings_name['setting'] = $cont_setting;
			$settings_name['tuning'] = $this->jetcache_settings['add_cont'][$this->jetcache_cont_number];

			$this->set_cache_name($settings_name);

			if (!$this->config->get('blog_work')) {
				$this->config->set('blog_work', true);
				$off_blog_work = true;
			} else {
				$off_blog_work = false;
			}

            $cont_links_new = $this->document->getLinks();
            $cont_links_old = $this->jc_cont_document['links'];
            if (!empty($cont_links_new)) {
            	foreach ($cont_links_new as $link => $options) {
            		if (isset($cont_links_old[$link])) unset($cont_links_new[$link]);
            	}
            	if (!empty($cont_links_new)) {
            		$cache['links'] = $cont_links_new;
            	}
            }

            if (isset($this->journal3->document)) {
            	$cache['j3_css'] = $this->journal3->document->getStyles();
            	$cache['j3_css_inline'] = $this->journal3->document->getJCCss();
            	$cache['j3_js_inline'] = $this->journal3->document->getJs();
            	$cache['j3_js']['header']  = $this->journal3->document->getScripts('header');
                $cache['j3_js']['footer']  = $this->journal3->document->getScripts('footer');
            }

            $cont_styles_new = $this->document->getStyles();
            $cont_styles_old = $this->jc_cont_document['styles'];
            if (!empty($cont_styles_new)) {
            	foreach ($cont_styles_new as $link => $options) {
            		if (isset($cont_styles_old[$link])) unset($cont_styles_new[$link]);
            	}
            	if (!empty($cont_styles_new)) {
            		$cache['styles'] = $cont_styles_new;
            	}
            }


            if (SC_VERSION < 21) {
				$cont_scripts_new['header'] = $this->document->getScripts();
			} else {
				$cont_scripts_new['header'] = $this->document->getScripts('header');
				$cont_scripts_new['footer'] = $this->document->getScripts('footer');
			}

            if (SC_VERSION < 21) {
				$cont_scripts_old['header'] = $this->jc_cont_document['scripts']['header'];
			} else {
				$cont_scripts_old['header'] = $this->jc_cont_document['scripts']['header'];
				$cont_scripts_old['footer'] = $this->jc_cont_document['scripts']['footer'];
			}

            if (!empty($cont_scripts_new['header'])) {
            	foreach ($cont_scripts_new['header'] as $link => $options) {
            		if (isset($cont_scripts_old['header'][$link])) {
            			unset($cont_scripts_new['header'][$link]);
            		}
            	}
            	if (!empty($cont_scripts_new['header'])) {
            		$cache['scripts']['header'] = $cont_scripts_new['header'];
            	}
            }

            if (!empty($cont_scripts_new['footer'])) {
            	foreach ($cont_scripts_new['footer'] as $link => $options) {
            		if (isset($cont_scripts_old['footer'][$link])) {
            			unset($cont_scripts_new['footer'][$link]);
            		}
            	}
            	if (!empty($cont_scripts_new['footer'])) {
            		$cache['scripts']['footer'] = $cont_scripts_new['footer'];
            	}
            }


	        if (isset($settings_name['tuning']['onefile']) && $settings_name['tuning']['onefile']) {
	        	$cache_data = $this->cache->get($this->sc_cache_name);

	        	$hache_args = md5($this->sc_cache_name.(var_export($settings_name['setting'], true)));

	            $cache_data[$hache_args] = $cache_output;
	            $cache_output = $cache_data;
	        }

			$cache['output'] = $cache_output;

	        $this->cache->set($this->sc_cache_name, $cache);

            $this->jetcache_product_id_update('cont');
           // $this->registry->set('jetcache_product_id_cont', array());

			if ($off_blog_work) {
				$this->config->set('blog_work', false);
			}
		}
    }

	private function set_cache_name($settings) {

		if (isset($settings['type'])) {
			$settings_type = $settings['type'];
		} else {
			$settings_type = 'pages';
		}
		if (isset($settings['route'])) {
			$settings_route = $settings['route'];
		} else {
			$settings_route = '';
		}
		if (isset($settings['setting'])) {
			$settings_setting = $settings['setting'];
		} else {
			$settings_setting = array();
		}
		if (isset($settings['tuning'])) {
			$settings_tuning = $settings['tuning'];
		} else {
			$settings_tuning = false;
		}

		$session_data = $this->session->data;
		$cookie_data = $_COOKIE;


        $ex_session_black_flag = false;
		if (isset($this->jetcache_settings['ex_session_black_status']) && $this->jetcache_settings['ex_session_black_status']) {
			if (isset($this->jetcache_settings['ex_session_black']) && $this->jetcache_settings['ex_session_black'] != '') {
                $ex_session_black_flag = true;
				$ex_session_black_array = explode(PHP_EOL, trim($this->jetcache_settings['ex_session_black']));

			    foreach($session_data as $session_key => $session_parameter) {
		            $del_key = true;
		            foreach($ex_session_black_array as $num => $key) {
						if (trim($key) == trim($session_key)) {
							$del_key = false;
						}
		            }
		            if ($del_key) {
		            	unset($session_data[$session_key]);
		            }
				}
			}
		}


		if (isset($this->jetcache_settings['ex_cookie_black']) && $this->jetcache_settings['ex_cookie_black'] != '') {
			$ex_cookie_black_array = explode(PHP_EOL, trim($this->jetcache_settings['ex_cookie_black']));
		    foreach($cookie_data as $cookie_key => $cookie_parameter) {
		        $del_key = true;
		        foreach($ex_cookie_black_array as $num => $key) {
					if (trim($key) == trim($cookie_key)) {
						$del_key = false;
					}
		        }
		        if ($del_key) {
		        	unset($cookie_data[$cookie_key]);
		        }
			}
		} else {
			$cookie_data = array();
		}

		if ($this->jetcache_buildcache) {
			$save_session = $this->session->data;
			unset($session_data['user_id']);
			unset($session_data['account']);
			unset($session_data[$this->token_name]);
		}

		if (isset($session_data[$this->token_name])) {
			unset($session_data[$this->token_name]);
		}
		if (isset($session_data['captcha'])) {
			unset($session_data['captcha']);
		}
		if (isset($session_data['language_old'])) {
			unset($session_data['language_old']);
		}
		if (isset($session_data['currency_old'])) {
			unset($session_data['currency_old']);
		}

        if ($settings_type != 'query') {

	    	$data_cache['cart'] = $this->config->get('seocms_jetcache_cart');

			if (isset($settings_tuning['no_session']) && $settings_tuning['no_session']) {
				$data_cache['session'] = array();
				$data_cache['cookie'] = array();
				$data_cache['total_wishlist'] = 0;
			} else {
				$data_cache['session'] = $session_data;
				$data_cache['cookie'] = $cookie_data;
				$data_cache['total_wishlist'] = $this->total_wishlist;
			}

			if (isset($settings_tuning['no_getpost']) && $settings_tuning['no_getpost']) {
				$data_cache['get'] = array();
				$data_cache['post'] = array();
			} else {

				$data_cache['get'] = $this->registry->get('request')->get;

				if (isset($this->jetcache_settings['ex_get']) && $this->jetcache_settings['ex_get'] != '') {

					$ex_get_array = explode(PHP_EOL, trim($this->jetcache_settings['ex_get'], PHP_EOL));

					foreach($data_cache['get'] as $data_get_param => $data_get) {
						$data_get_param = trim($data_get_param);
						foreach($ex_get_array as $ex_get) {
			                $ex_get = trim($ex_get);
			                if ($data_get_param == $ex_get) {
			                	unset($data_cache['get'][$ex_get]);
			                }
						}
		       		}
		       	}

   				if (isset($settings_tuning['only_get']) && $settings_tuning['only_get'] != '') {
					$new_data_cache_get = array();
					$only_get_array = explode(PHP_EOL, trim($settings_tuning['only_get'], PHP_EOL));
					foreach($data_cache['get'] as $data_get_param => $data_get) {
						$data_get_param = trim($data_get_param);
						foreach($only_get_array as $only_get) {
			                $only_get = trim($only_get);
			                if ($data_get_param == $only_get) {
			                	$new_data_cache_get[$only_get] = $data_cache['get'][$only_get];
			                }
						}
		       		}
                    //if (!empty($new_data_cache_get)) {
                    	$data_cache['get'] = $new_data_cache_get;
                    //}
				}

				$data_cache['post'] = $this->registry->get('request')->post;
				if (isset($data_cache['post']['jc_cont_ajax'])) {
					unset($data_cache['post']['jc_cont_ajax']);
				}

			}

			if (isset($settings_tuning['no_url']) && $settings_tuning['no_url']) {
				$data_cache['url'] = $this->registry->get('request')->server['HTTP_HOST'];
			} else {
				$data_cache['url'] = $this->registry->get('request')->server['HTTP_HOST'] . $this->registry->get('request')->server['REQUEST_URI'];
			}

		}

			if (!empty($settings_setting)) {
				if (isset($settings_tuning['onefile']) && $settings_tuning['onefile']) {
					// Because already in the hash, up in model_to_cache, save array with hash arguments of method
					// $hache_args = md5($this->sc_cache_name . (var_export($args, true)));
		            // $cache_data[$hache_args] = $output;
					$settings_setting = array();
				}
				$data_cache['setting'] = md5(json_encode($settings_setting));
			}

	        unset($data_cache['get']['_route_']);

			if ($settings_type != 'query') {

				if (!$ex_session_black_flag && isset($this->jetcache_settings['ex_session']) && $this->jetcache_settings['ex_session'] != '') {

					$ex_session_array = explode (PHP_EOL, trim($this->jetcache_settings['ex_session'], PHP_EOL));

					foreach($data_cache['session'] as $data_session_param => $data_session) {
						$data_session_param = trim($data_session_param);
						foreach($ex_session_array as $ex_session) {
			                $ex_session = trim($ex_session);
			                if ($data_session_param == $ex_session) {
			                	unset($data_cache['session'][$ex_session]);
			                }
						}
		       		}
	           	}
           	}

			if (!empty($settings_setting)) {
				$data_cache['setting'] = md5(var_export($settings_setting, true));
			} else {
				unset($data_cache['setting']);
			}

			// Main var cache
			$hash = md5($this->config_url . (var_export($data_cache, true)));

			$route_name = '';
			if (isset($this->registry->get('request')->get['route'])) {
				$route_name .= '_'.str_replace('/', '_', $this->registry->get('request')->get['route']);
			}
			unset($data_cache);

			if ($settings_route == '' || (isset($settings_tuning['no_route']) && $settings_tuning['no_route'])) {
				$settings_route = '_';
				if (isset($settings_tuning['no_route']) && $settings_tuning['no_route']) {
					$route_name = '';
				}
			}

            $currency_id = $this->currency->getId($this->session->data['currency']);

            $lang_store_currency_webp_name = $this->config->get('config_language_id') . '_' . $this->config->get('config_store_id') . '_' . $currency_id . '_' . (int)$this->webp ;

            if ($settings_type == 'categories') {
            	$route_name = $settings_route = '_';
            	$hash = md5($this->registry->get('request')->server['HTTP_HOST']);
            }

			if (isset($this->jetcache_settings[$settings_type.'_db_status']) && $this->jetcache_settings[$settings_type.'_db_status']) {
	        	$this->sc_cache_name  = 'blog.db.' . $settings_type . '.' . $hash . '.' . $settings_route . $lang_store_currency_webp_name . $route_name;
			} else {
	        	$this->sc_cache_name = 'blog.jetcache_' . $settings_type . '.' . $lang_store_currency_webp_name . $route_name.'.' . $settings_route . '.' . $hash;
			}

			if ($this->jetcache_buildcache) {
				$this->session->data = $save_session;
			}


		if (isset($this->jetcache_settings['edit_product_id']) && $this->jetcache_settings['edit_product_id']) {

            $jetcache_product_id_cont = $this->registry->get('jetcache_product_id_cont');
            $jetcache_product_id_model = $this->registry->get('jetcache_product_id_model');
            $jetcache_product_id_query = $this->registry->get('jetcache_product_id_query');

            /*
        	$jetcache_product_id_pages = $this->registry->get('jetcache_product_id_pages');
        	if ($settings_type == 'pages' && !empty($jetcache_product_id_pages)) {
	        	$product_id_array = $jetcache_product_id_pages;
	        	foreach ($jetcache_product_id_pages as $product_id => $array_product_id) {
             		$product_id_array[$product_id][$this->sc_cache_name] = $this->sc_cache_name;
	        	}
	        	$this->registry->set('jetcache_product_id_pages', $product_id_array);
        	}
        	*/
        	if ($settings_type == 'cont' && !empty($jetcache_product_id_cont)) {
	        	$product_id_array = $jetcache_product_id_cont;
	        	foreach ($jetcache_product_id_cont as $product_id => $array_product_id) {
             		$product_id_array[$product_id][$this->sc_cache_name] = $this->sc_cache_name;
	        	}
	        	$this->registry->set('jetcache_product_id_cont', $product_id_array);
        	}
        	if ($settings_type == 'model' && !empty($jetcache_product_id_model)) {
	        	$product_id_array = $jetcache_product_id_model;
	        	foreach ($jetcache_product_id_model as $product_id => $array_product_id) {
             		$product_id_array[$product_id][$this->sc_cache_name] = $this->sc_cache_name;
	        	}
	        	$this->registry->set('jetcache_product_id_model', $product_id_array);
        	}
        	if ($settings_type == 'query' && !empty($jetcache_product_id_query)) {
	        	$product_id_array = $jetcache_product_id_query;
	        	foreach ($jetcache_product_id_query as $product_id => $array_product_id) {
             		$product_id_array[$product_id][$this->sc_cache_name] = $this->sc_cache_name;
	        	}
	        	$this->registry->set('jetcache_product_id_query', $product_id_array);
        	}

		} else {
			$this->registry->set('jetcache_product_id_pages', array());
			$this->registry->set('jetcache_product_id_cont', array());
			$this->registry->set('jetcache_product_id_model', array());
			$this->registry->set('jetcache_product_id_query', array());
		}

   		return $this->sc_cache_name;
    }


	public function hook_header_categories($categories) {

		if (!$this->config->get('blog_work')) {
			$this->config->set('blog_work', true);
			$off_blog_work = true;
		} else {
			$off_blog_work = false;
		}
		$settings_name['type'] = 'categories';

		$this->set_cache_name($settings_name);

		if (empty($categories)) {
			$categories = $this->cache->get($this->sc_cache_name);
		} else {
			$this->cache->set($this->sc_cache_name, $categories);
		}

		if ($off_blog_work) {
			$this->config->set('blog_work', false);
		}

		return $categories;

	}


	public function hook_maintenance_index() {

	    if (!$this->registry->get('jetcache_opencart_core')) {
	    	$this->registry->set('jetcache_opencart_core', microtime(true));
	    }

		if ($this->config->get('ascp_settings') != '') {
			$seocms_settings = $this->config->get('ascp_settings');
		} else {
			$seocms_settings = Array();
		}

		if ($this->config->get('ascp_settings') != '') {
			$this->jetcache_settings = $this->config->get('asc_jetcache_settings');
		} else {
			$this->jetcache_settings = Array();
		}
        if (!is_callable(array($this->cache, 'json_error'))) {
			$Cache = $this->registry->get('cache');
			$this->registry->set('cache_old', $Cache);
			loadlibrary('agoo/cache');
			$jcCache = new agooCache($this->registry);
			$jcCache->agooconstruct();
			$this->registry->set('cache', $jcCache);
		}

		if (!$this->registry->get('admin_work') && ((
			(isset($this->jetcache_settings['jetcache_widget_status'])) && $this->jetcache_settings['jetcache_widget_status']) ||
			(isset($seocms_settings['cache_pages'])) && $seocms_settings['cache_pages'])
		) {
			$this->registry->set('seocms_cache_status', true);
		}

		if ($this->registry->get('seocms_cache_status') && !$this->registry->get('contstruct_jetcache_loading')) {
		    $this->registry->set('contstruct_jetcache_loading', $this->jetcache_construct());
	    }

        if (defined('DIR_CATALOG')) {
        	$this->registry->set('log', new Log($this->config->get('config_error_filename')));
        	$this->log->write('Jet Cache: In Front DIR_CATALOG, mark as admin panel. WTF?');
        }

        if (defined('HTTP_CATALOG') || defined('HTTPS_CATALOG')) {
        	$this->registry->set('log', new Log($this->config->get('config_error_filename')));
        	$this->log->write('Jet Cache: In Front HTTP_CATALOG, mark as admin panel. WTF?');
        }

        if (isset($this->jetcache_settings['pages_forsage']) && $this->jetcache_settings['pages_forsage']) {
        	$this->hook_Registry_get();
        }
	}




	public function cont_ajax_response() {
		$cont_ajax_cycle = '';

		if (isset($this->jetcache_settings['cont_ajax_delay']) && $this->jetcache_settings['cont_ajax_delay'] != '') {
			$cont_ajax_delay = $this->jetcache_settings['cont_ajax_delay'];
		} else {
			$cont_ajax_delay = 0;
		}


		if (!isset($this->request->post['jc_cont_ajax'])) {
		$cont_ajax_html = "
		<script>
		$(document).ready(function() {
		   if ($('.jc-cont-ajax').length > 0) {

				function jc_ajax(jc_ajax_delay) {

					$.ajax({
						type: 'POST',
						url: '/".$this->request_uri_trim."',
						data: {jc_cont_ajax: '1'},
						async: true,
						dataType: 'html',
						beforeSend: function() {
						},
						success: function(html){
				            " . $cont_ajax_cycle . "

							$.each($('.jc-cont-ajax'), function(num, value) {
								cont_setting_md5 = $(this).attr('data-set');
								cont_setting_delay = $(this).attr('data-delay');

                                if (cont_setting_delay == jc_ajax_delay) {
						    		jc_cont_ajax_loaded_ = $(html).find('.jc-cont-ajax-loaded-' + cont_setting_md5 + '-' + cont_setting_delay).html();

									if ($(html).find('.jc-cont-ajax-loaded-' + cont_setting_md5 + '-' + cont_setting_delay).length > 1) {
										console.log('Error find|close html tags');
									}
									if (typeof jc_cont_ajax_loaded_ !== 'undefined') {
										$('.jc-cont-ajax-' + cont_setting_md5 + '-' + cont_setting_delay ).replaceWith(jc_cont_ajax_loaded_);
									} else {
										$('.jc-cont-ajax-' + cont_setting_md5 + '-' + cont_setting_delay).replaceWith('');
									}
								}
							});
						}
					});
				}
                var jc_ajax_array = [];
                $.each($('.jc-cont-ajax'), function(num, value) {
					cont_setting_delay = $(this).attr('data-delay');
                   	if (!jc_ajax_array.includes(cont_setting_delay)) {
				    	 jc_ajax_array.push(cont_setting_delay);
    				}
				});
                jc_ajax_array.forEach(function(item, i, arr) {
                	setTimeout(jc_ajax, item, item);
				});
			}
		});
		</script>
		";
		} else {
			$cont_ajax_html = '';
		}
		return $cont_ajax_html;
	}

    public function cont_ajax_get_scripts($cont_setting_md5, $type) {

    	$cont_scripts_file_cache  = 'blog.jetcache_cont_scripts.' . $cont_setting_md5;

		if (!$this->config->get('blog_work')) {
			$this->config->set('blog_work', true);
			$off_blog_work = true;
		} else {
			$off_blog_work = false;
		}

    	if ($type == 'before') {
	    	$cache_content = $this->cache->get($cont_scripts_file_cache);

			if ($off_blog_work) {
				$this->config->set('blog_work', false);
			}

	        if ($cache_content) {

	           	if (isset($this->journal3->document)) {

	            	if (isset($cache_content['j3_css']) && !empty($cache_content['j3_css'])) {

	                    $j3_css_content = $cache_content['j3_css'];

						$j3_css_add = array();

	            		if (is_array($j3_css_content)) {

                            $j3_css_add = $j3_css_content;
	                        if (!empty($j3_css_add)) {
			            		foreach($j3_css_add as $css_link => $css_array) {

		             				if ($css_array['href'] != 'catalog/view/theme/journal3/stylesheet/style.css') {
		             					if ($css_array['href'] != 'catalog/view/theme/journal3/stylesheet/custom.css') {
		             						$this->journal3->document->addStyle($css_array['href'], $css_array['rel'], $css_array['media']);
		             					}
		             				}
		             			}
             				}
	             		}
	            	}

					if (isset($cache_content['j3_css_inline'])) {
						$ccs_now = $this->journal3->document->getJCCss();
	                    $j3_css_inline_add_array = $this->array_diff_assoc_recursive($cache_content['j3_css_inline'], $ccs_now);
	                    foreach ($j3_css_inline_add_array as $j3_css_inline_add_key => $j3_css_inline_add) {
							$this->journal3->document->addCss($j3_css_inline_add, $j3_css_inline_add_key);
						}
					}

					if (isset($cache_content['j3_js_inline'])) {
		            	if (isset($cache_content['j3_js_inline']) && !empty($cache_content['j3_js_inline'])) {

		            		$j3_js_inline_temp = $this->journal3->document->getJs();
		            		$j3_js_inline_add = $this->array_diff_assoc_recursive($cache_content['j3_js_inline'], $j3_js_inline_temp);

		            		if (!empty($j3_js_inline_add)) {
		            			$this->journal3->document->addJs($j3_js_inline_add);
		            		}
		            	}
		            }

	            	if (isset($cache_content['j3_js']) && is_array($cache_content['j3_js']) && !empty($cache_content['j3_js'])) {

	            		$j3_js_temp['header'] = $this->journal3->document->getScripts('header');
	                    $j3_js_temp['footer'] = $this->journal3->document->getScripts('footer');

	                    $j3_js_content = $cache_content['j3_js'];

	            		$j3_js_add = array();
	            		if (is_array($j3_js_temp) && is_array($j3_js_content)) {
		            		$j3_js_add = $this->array_diff_assoc_recursive($j3_js_content, $j3_js_temp);

		            		if (!empty($j3_js_add)) {
		            			foreach($j3_js_add as $pos => $js_links) {
		            				foreach($js_links as $pos_link => $js_link) {
		            					$this->journal3->document->addScript($js_link, $pos);
		            				}
		            			}
		            		}
	            		}
	            	}
	            }

		        if (isset($cache_content['styles'])) {
					$styles = $cache_content['styles'];
			        foreach($styles as $style => $style_setting) {
			        	if (isset($style_setting['href']) && isset($style_setting['rel']) && isset($style_setting['media'])) {
			        		$this->document->addStyle($style_setting['href'], $style_setting['rel'], $style_setting['media']);
			        	}
			        }
				}
				if (isset($cache_content['links'])) {
					$links = $cache_content['links'];
			        foreach($links as $link => $link_setting) {
			        	if (isset($link_setting['href']) && isset($link_setting['rel'])) {
			        		$this->document->addLink($link_setting['href'], $link_setting['rel']);
			        	}
			        }
				}

				if (isset($cache_content['scripts']['header']) && !empty($cache_content['scripts']['header'])) {
					$scripts = $cache_content['scripts']['header'];
	                foreach($scripts as $script => $script_link) {
	                	if (SC_VERSION > 20) {
	                		$this->document->addScript($script_link, 'header');
	                	} else {
	                		$this->document->addScript($script_link);
	                	}
	                }
				}

                if (SC_VERSION > 20) {
					if (isset($cache_content['scripts']['footer']) && !empty($cache_content['scripts']['footer'])) {
						$scripts = $cache_content['scripts']['footer'];
		                foreach($scripts as $script => $script_link) {
		                	$this->document->addScript($script_link, 'footer');
		                }
					}
				}
				return true;
			} else {
				return false;
			}
		}

		if ($type == 'after') {
			$cache_content['links'] = $this->document->getLinks();
			$cache_content['styles'] = $this->document->getStyles();
		    if (isset($this->journal3->document)) {
            	$cache_content['j3_css'] = $this->journal3->document->getStyles();
            	$cache_content['j3_css_inline'] = $this->journal3->document->getJCCss();
            	$cache_content['j3_js_inline'] = $this->journal3->document->getJs();
            	$cache_content['j3_js']['header'] = $this->journal3->document->getScripts('header');
            	$cache_content['j3_js']['footer'] = $this->journal3->document->getScripts('footer');
            	//$cache_content['j3_fonts']  = $this->journal3->document->getFonts(false);
            }

			if (SC_VERSION < 21) {
				$cache_content['scripts']['header'] = $this->document->getScripts();
			} else {
				$cache_content['scripts']['header'] = $this->document->getScripts('header');
				$cache_content['scripts']['footer'] = $this->document->getScripts('footer');
			}


			$this->cache->set($cont_scripts_file_cache, $cache_content);

			if ($off_blog_work) {
				$this->config->set('blog_work', false);
			}
			return true;
		}

    }

	public function hook_Loader_controller($type, $route, $data = array(), $output = NULL) {

		if (!$this->registry->get('admin_work')) {

			if ($this->jetcache_settings['cont_log_status']) {
            	$json_encode_data = json_encode($data);

            	$cont_log_hache = md5($route . $json_encode_data);
            	$this->jc_cont_log[$cont_log_hache]['route'] = $route;
            	$this->jc_cont_log[$cont_log_hache]['data'] = $json_encode_data;
            	$this->jc_cont_log[$cont_log_hache]['md5'] = $cont_log_hache;

			}

			if (isset($data) && !empty($data)) {
				$cont_setting = $data;
			} else {
			   	$cont_setting = '';
			}

			if (isset($this->jetcache_settings['cont_ajax_delay']) && $this->jetcache_settings['cont_ajax_delay'] != '') {
				$cont_ajax_delay = $this->jetcache_settings['cont_ajax_delay'];
			} else {
				$cont_ajax_delay = 0;
			}

		    if ($type == 'before') {

                $this->flag_scripts = false;

                if (isset($this->jetcache_settings['cont_log_status']) && $this->jetcache_settings['cont_log_status']) {
                	$this->jc_cont_log[$cont_log_hache]['start'] = microtime(true);
                }

			    if (isset($this->jetcache_settings['cont_ajax_status']) && $this->jetcache_settings['cont_ajax_status'] && !$this->jc_ajax) {

                    if ($cont_ajax_delay > 0) {
                    	$cont_ajax_delay_loader = "<i class='fa fa-refresh fa-spin'></i>";
                    } else {
                    	$cont_ajax_delay_loader = '';
                    }

                    $cont_setting_md5 = md5($route . json_encode($cont_setting));

                    $this->html_ajax[$cont_setting_md5] = false;

                    if (isset($this->jetcache_settings['cont_ajax_routes'])) {
						$cont_ajax_route_array = $this->jetcache_settings['cont_ajax_routes'];
					} else {
						$cont_ajax_route_array = array();
					}
                    $ajax_flag = true;

				    foreach($cont_ajax_route_array as $num => $cont_ajax_route) {

				    	$ajax_route = trim($cont_ajax_route['route']);

						if ($cont_ajax_route['status'] && $cont_ajax_route != '' && trim($route) == $ajax_route) {

                            if (isset($cont_ajax_route['md5']) && $cont_ajax_route['md5'] != '' && $cont_ajax_route['md5'] == $cont_setting_md5) {
	                            $ajax_flag = true;
                            }

                            if (isset($cont_ajax_route['md5']) && $cont_ajax_route['md5'] != '' && $cont_ajax_route['md5'] != $cont_setting_md5) {
	                            $ajax_flag = false;
                            }

                            if (isset($cont_ajax_route['md5']) && $cont_ajax_route['md5'] == '') {
                            	$ajax_flag = true;
                            }

                            if ($ajax_flag) {

	                            if ($cont_ajax_route['scripts']) {
	                            	$this->flag_scripts = $this->cont_ajax_get_scripts($cont_setting_md5, 'before');
	                            } else {
	                            	$this->flag_scripts = true;
	                            }

	                            if (isset($cont_ajax_route['delay']) && $cont_ajax_route['delay'] != '') {
		                            $cont_ajax_route_delay = (int)$cont_ajax_route['delay'];
	                            } else {
	                            	$cont_ajax_route_delay = $cont_ajax_delay;
	                            }

			                    if ($cont_ajax_route_delay > 0) {
                    				$cont_ajax_delay_loader = "<i class='fa fa-refresh fa-spin'></i>";
			                    } else {
			                    	$cont_ajax_delay_loader = '';
			                    }

								$this->html_ajax[$cont_setting_md5] = "<i class='jc-cont-ajax jc-cont-ajax-" . $cont_setting_md5 . "-" . $cont_ajax_route_delay . "' data-set='" . $cont_setting_md5 . "' data-delay='" . $cont_ajax_route_delay . "'>" . $cont_ajax_delay_loader . "</i>";



	                            if ($this->flag_scripts) {
	                                $ajax_output = $this->html_ajax[$cont_setting_md5];
	                                $this->html_ajax[$cont_setting_md5] = false;
									return $ajax_output;
								}
							}
						}
				    }
			    }

				if ($this->registry->get('seocms_cache_status') && $this->jetcache_cont_access($route)) {

			        if ($this->jetcache_settings['cont_status'] && !isset($this->registry->get('request')->post['jc_cont_ajax'])) {
			        	$cache_from = $this->cont_from_cache($route, $cont_setting);
			        } else {
			        	$cache_from = false;
			        }

			        if ($cache_from) {
		                if ($this->jetcache_settings['cont_log_status']) {
		                	$this->jc_cont_log[$cont_log_hache]['cache'] = true;
							$this->jc_cont_log[$cont_log_hache]['end'] = microtime(true);
							$this->jc_cont_log[$cont_log_hache]['time'] = $this->jc_cont_log[$cont_log_hache]['end'] - $this->jc_cont_log[$cont_log_hache]['start'];
		                }
			        	return $cache_from;
			        } else {
			        	return NULL;
			        }
				}
			}

			if ($type == 'after' && $output != NULL) {
                $cont_setting_md5 = md5($route.json_encode($cont_setting));
                if ($this->registry->get('seocms_cache_status') && isset($this->html_ajax[$cont_setting_md5]) && $this->html_ajax[$cont_setting_md5]) {

                    $this->cont_ajax_get_scripts($cont_setting_md5, 'after');

                    $ajax_output = $this->html_ajax[$cont_setting_md5];
                    $this->html_ajax[$cont_setting_md5] = false;
					return $ajax_output;

                }

				if ($this->registry->get('seocms_cache_status') && $this->jetcache_settings['cont_status'] && !isset($this->registry->get('request')->post['jc_cont_ajax'])) {
			    	if ($this->controller_jetcache_jetcache->jetcache_cont_access($route)) {
		       			$this->cont_to_cache($output, $route, $cont_setting);
					}
				}

				if ($this->jetcache_settings['cont_log_status']) {
					$this->jc_cont_log[$cont_log_hache]['end'] = microtime(true);
					if (isset($this->jc_cont_log[$cont_log_hache]['start'])) {
						$this->jc_cont_log[$cont_log_hache]['time'] = $this->jc_cont_log[$cont_log_hache]['end'] - $this->jc_cont_log[$cont_log_hache]['start'];
					} else {
						$this->jc_cont_log[$cont_log_hache]['time'] = 0;
					}
				}

			    if (isset($this->registry->get('request')->post['jc_cont_ajax'])) {
                    $cont_setting_md5 = md5($route . json_encode($cont_setting));

                    if (isset($this->jetcache_settings['cont_ajax_routes'])) {
						$cont_ajax_route_array = $this->jetcache_settings['cont_ajax_routes'];
					} else {
						$cont_ajax_route_array = array();
					}

                    $ajax_flag = true;

				    foreach($cont_ajax_route_array as $num => $cont_ajax_route) {

				    	$ajax_route = trim($cont_ajax_route['route']);

						if ($cont_ajax_route['status'] && $cont_ajax_route != '' && trim($route) == $ajax_route) {


                            if (isset($cont_ajax_route['md5']) && $cont_ajax_route['md5'] != '' && $cont_ajax_route['md5'] == $cont_setting_md5) {
	                            $ajax_flag = true;
                            }

                            if (isset($cont_ajax_route['md5']) && $cont_ajax_route['md5'] != '' && $cont_ajax_route['md5'] != $cont_setting_md5) {
	                            $ajax_flag = false;
                            }

                            if (isset($cont_ajax_route['md5']) && $cont_ajax_route['md5'] == '') {
                            	$ajax_flag = true;
                            }



                            if ($ajax_flag) {

	                            if (isset($cont_ajax_route['delay']) && $cont_ajax_route['delay'] != '') {
		                            $cont_ajax_route_delay = (int)$cont_ajax_route['delay'];
	                            } else {
	                            	$cont_ajax_route_delay = $cont_ajax_delay;
	                            }

								$this->html_ajax[$cont_setting_md5] = "<i class='jc-cont-ajax-loaded-" . $cont_setting_md5 . "-" . $cont_ajax_route_delay . "'>" . $output . "</i>";

	                            $ajax_output = $this->html_ajax[$cont_setting_md5];
	                            $this->html_ajax[$cont_setting_md5] = false;
								return $ajax_output;
							}
						}
				    }

			    }
			    return $output;
			}

            if ($type == 'after' && $output == NULL) {
		    	return $output;
			}
        } else {
        	return $output;
        }
	}

    public function hook_getProduct($product_id) {

    	if (isset($this->jetcache_settings['edit_product_id']) && $this->jetcache_settings['edit_product_id']) {

		    $jetcache_product_id_pages = $this->registry->get('jetcache_product_id_pages');
		    $jetcache_product_id_pages = (array)$jetcache_product_id_pages;

		    if (!isset($jetcache_product_id_pages[$product_id])) {
		    	$jetcache_product_id_pages[$product_id] = array();
		    	$this->registry->set('jetcache_product_id_pages', $jetcache_product_id_pages);
		    }

		    $jetcache_product_id_cont = $this->registry->get('jetcache_product_id_cont');
		    $jetcache_product_id_cont = (array)$jetcache_product_id_cont;
		    if (!isset($jetcache_product_id_cont[$product_id])) {
		    	$jetcache_product_id_cont[$product_id] = array();
		    	$this->registry->set('jetcache_product_id_cont', $jetcache_product_id_cont);
		    }

		    $jetcache_product_id_model = $this->registry->get('jetcache_product_id_model');
		    $jetcache_product_id_model = (array)$jetcache_product_id_model;
		    if (!isset($jetcache_product_id_model[$product_id])) {
		    	$jetcache_product_id_model[$product_id] = array();
		    	$this->registry->set('jetcache_product_id_model', $jetcache_product_id_model);
		    }
    	}
    }

	public function jetcache_product_id_update($type) {
		if (isset($this->jetcache_settings['edit_product_id']) && $this->jetcache_settings['edit_product_id']) {
            $jetcache_product_id_pages = $this->registry->get('jetcache_product_id_pages');
            $jetcache_product_id_cont = $this->registry->get('jetcache_product_id_cont');
            $jetcache_product_id_model = $this->registry->get('jetcache_product_id_model');

	    	if ($type == 'pages' && !empty($jetcache_product_id_pages)) {
		    	foreach ($jetcache_product_id_pages as $product_id => $product_id_array) {
		    		foreach ($product_id_array as $filecache) {
		    			$this->model_jetcache_jetcache->edit_product_id($product_id, $filecache, $this->jetcache_settings['cache_expire']);
		    		}
		    	}
	    	}
	    	if ($type == 'cont' && !empty($jetcache_product_id_cont)) {
		    	foreach ($jetcache_product_id_cont as $product_id => $product_id_array) {
		    		foreach ($product_id_array as $filecache) {
		    			$this->model_jetcache_jetcache->edit_product_id($product_id, $filecache, $this->jetcache_settings['cache_expire']);
		    		}
		    	}
	    	}
	    	if ($type == 'model' && !empty($jetcache_product_id_model)) {
		    	foreach ($jetcache_product_id_model as $product_id => $product_id_array) {
		    		foreach ($product_id_array as $filecache) {
		    			$this->model_jetcache_jetcache->edit_product_id($product_id, $filecache, $this->jetcache_settings['cache_expire']);
		    		}
		    	}
	    	}
		}

	}

	public function cacheremove($access = 'islogged', $messages = true) {
        $file_deleted = false;

        if ($messages) {
	        if (SC_VERSION > 20) {
	        	$this->registry->get('load')->language('jetcache/jetcache');
	        } else {
	        	$this->registry->get('language')->load('jetcache/jetcache');
	        }
        }

        if (file_exists(DIR_SYSTEM . 'helper/seocmsprofunc.php')) {
			if (!function_exists('loadlibrary')) {
				if (function_exists('modification')) {
					require_once(modification(DIR_SYSTEM . 'helper/seocmsprofunc.php'));
				} else {
					require_once(DIR_SYSTEM . 'helper/seocmsprofunc.php');
				}
			}
        }

        if (is_array($access)) $access = 'islogged';

        if (strtolower($access) != 'noaccess') {
			if (SC_VERSION > 21) {
				$place_user = 'cart/user';
				$class_user = 'Cart\User';
			} else {
				$place_user = 'user';
				$class_user = 'User';
			}

			loadlibrary($place_user);
			$this->registry->set('user', new $class_user($this->registry));
		}

		if (isset($this->session->data['user_id'])) {
	    	$this->user_id = $this->session->data['user_id'];
		} else {
			$this->user_id = false;
		}

		if (strtolower($access) == 'noaccess' || ($this->registry->get('user') && $this->registry->get('user')->hasPermission('modify', 'jetcache/jetcache') && $this->user_id && strtolower($access) == 'islogged')) {

			if (!defined('VERSION')) return; if (!defined('SC_VERSION')) define('SC_VERSION', (int)substr(str_ireplace('.','',VERSION), 0,2));
			$status = false;
			$html = '';
			if (function_exists('modification')) {
				require_once(modification(DIR_SYSTEM . 'library/exceptionizer.php'));
			} else {
				require_once(DIR_SYSTEM . 'library/exceptionizer.php');
			}
	        $exceptionizer = new PHP_Exceptionizer(E_ALL);

            $status = true;

			if (!isset($this->registry->get('request')->get['image'])) {
				$dir_for_clear[] = DIR_CACHE;
				$dir_for_clear[] = DIR_IMAGE . 'jetcache/css/';
				$dir_for_clear[] = DIR_IMAGE . 'jetcache/css_cache/';
				$dir_for_clear[] = DIR_IMAGE . 'jetcache/js/';
				$dir_for_clear[] = DIR_IMAGE . 'jetcache/js_cache/';
			} else {
				$dir_for_clear[] = DIR_IMAGE . 'cache/';
			}

			if (isset($this->registry->get('request')->get['mod'])) {
				$dir_root = str_ireplace('/system/', '', str_ireplace('\\', '/', DIR_SYSTEM)) . '/';
  				$dir_for_clear[] = $dir_root . 'vqmod/vqcache/';

  				if (!is_dir($dir_for_clear)) {
  					if (isset($messages) && $messages) {
  						$html.= $this->language->get('text_cache_remove_fail');
  					}
  					$status = false;
  				}
			}
			if (isset($this->registry->get('request')->post['filename']) && $this->registry->get('request')->post['filename'] != '') {
				if (!$this->config->get('blog_work')) {
					$this->config->set('blog_work', true);
				}

				if ($this->cache->delete($this->db->escape(str_replace('../', '', $this->registry->get('request')->post['filename'])))) {
					$file_deleted = true;
				}

				if ($this->config->get('blog_work')) {
					$this->config->set('blog_work', false);
				}
				$status = false;
			}

            if ($status) {
				foreach ($dir_for_clear as $num => $dir_for_clear_name) {
					$files = $this->getDelFiles($dir_for_clear_name, '*', array('.htaccess', 'exchange1c'));

					if ($files['files']) {
						foreach ($files['files'] as $file) {
							if (file_exists($file)) {
							    try {
									unlink($file);
									$status = true;
							    }  catch (E_WARNING $e) {
					          		$status = false;
							    }
							}
						}
					}

					if ($files['dirs']) {
						krsort($files['dirs']);
						foreach ($files['dirs'] as $file) {
							if (is_dir($file) && file_exists($file)) {
							    try {
									rmdir($file);
									$status_dirs = true;
							    }  catch (E_WARNING $e) {
					          		$status_dirs = false;
							    }
							}
						}
					}
				}

				for ($i = 0; $i < 5; $i++) {
					$table = DB_PREFIX . "jetcache_pages_".$i;
					if ($this->table_exists($table)) {
						$sql = "TRUNCATE TABLE " . $table;
						$query = $this->db->query($sql);
	                }
					$table = DB_PREFIX . "jetcache_cont_".$i;
					if ($this->table_exists($table)) {
						$sql = "TRUNCATE TABLE " . $table;
						$query = $this->db->query($sql);
	                }
					$table = DB_PREFIX . "jetcache_model_".$i;
					if ($this->table_exists($table)) {
						$sql = "TRUNCATE TABLE " . $table;
						$query = $this->db->query($sql);
	                }

                }

				$table = DB_PREFIX . "jetcache_product_cache";
				if ($this->table_exists($table)) {
					$sql = "TRUNCATE TABLE " . $table;
					$query = $this->db->query($sql);
	            }
			}

	        if (isset($messages) && $messages) {
		        if ($status) {
		        	$html.= $this->language->get('text_jetcache_cache_remove_success');
		        } else {
		        	if ($file_deleted) {
	                	$html.= $this->language->get('text_jetcache_file_deleted_success');
		        	} else {
		        		$html.= $this->language->get('text_jetcache_cache_remove_fail');
		        	}
		        }
	        }

		} else {
			if (isset($messages) && $messages) {
				$html = $this->language->get('text_jetcache_no_access');
			}
		}
		if (isset($messages) && $messages) {
			$this->response->setOutput($html);
		}
	}



	private function table_exists($tableName) {
		$found= false;
		$like   = addcslashes($tableName, '%_\\');
		$result = $this->db->query("SHOW TABLES LIKE '" . $this->db->escape($like) . "';");
		$found  = $result->num_rows > 0;
		return $found;
	}

	private function getDelFiles($dir, $ext = "*", $exp = array()) {

		if (function_exists('modification')) {
			require_once(modification(DIR_SYSTEM . 'library/exceptionizer.php'));
		} else {
			require_once(DIR_SYSTEM . 'library/exceptionizer.php');
		}

		$files['files'] = Array();
        $files['dirs'] = Array();

		$dir = str_replace('//', '/', $dir);

		$exceptionizer = new PHP_Exceptionizer(E_ALL);
		try {
			$dir_pieces = explode('/', $dir);
			$dir_end = array_pop($dir_pieces);
			if (is_dir($dir) && !in_array($dir_end, $exp)) {
				$handle = opendir($dir);
				$subfiles = Array();
				while (false !== ($file = readdir($handle))) {
					if ($file != '.' && $file != '..') {
						if (is_dir(rtrim($dir, '/') . '/' . $file)) {
							if (!in_array($file, $exp)) {

								$subfiles = $this->getDelFiles(rtrim($dir, '/') . '/' . $file, $ext, $exp);

								if (!isset($subfiles['dirs'])) $subfiles['dirs'] = $files['dirs'];
								if (!isset($subfiles['files'])) $subfiles['files'] = $files['files'];

                                $files['dirs'][] = rtrim($dir, '/') . '/' . $file;

								$files['files'] = array_merge($files['files'], $subfiles['files']);
								$files['dirs'] = array_merge($files['dirs'], $subfiles['dirs']);
							}
						} else {
							$flie_name = $dir . '/' . $file;
							$flie_name = str_replace('//', '/', $flie_name);
							if ((substr($flie_name, strrpos($flie_name, '.')) == '.' . $ext) || ($ext == '*')) {
								if (!in_array($file, $exp)) {
									$files['files'][] = $flie_name;
								}
							}
						}
					}
				}
				closedir($handle);
			}
			$status = true;
		}
		catch (E_WARNING $e) {
			$status = false;
		}

		if (!isset($files['dirs'])) $files['dirs'] = array();
		if (!isset($files['files'])) $files['files'] = array();

		return $files;
	}

	public function index() {
		return true;
	}


	public function jc_function_exists($function) {

		if (function_exists('ini_get')) {
			$disabled = @ini_get('disable_functions');
		}
		if (extension_loaded('suhosin') && function_exists('ini_get')) {
			$suhosin_disabled = @ini_get('suhosin.executor.func.blacklist');

			if (!empty($suhosin_disabled)) {
				$suhosin_disabled = explode(',', $suhosin_disabled);
				$suhosin_disabled = array_map('trim', $suhosin_disabled);
				$suhosin_disabled = array_map('strtolower', $suhosin_disabled);
				if (function_exists($function) && !in_array($function, $suhosin_disabled)) {
					return true;
				}
				return false;
			}
		}
		return function_exists($function);
	}

	public function jetcache_webp($output) {
        // For test
		//$this->jetcache_settings['image_status'] = true;
		//$this->jetcache_settings['image_webp_status'] = true;
		//$this->webp = true;

        $jc_webp_js = '';

		if ($this->access_exeptions()) {

        $webp_extension = array('jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF');
		if (isset($this->jetcache_settings['image_status']) && $this->jetcache_settings['image_status'] && isset($this->jetcache_settings['image_webp_status']) && $this->jetcache_settings['image_webp_status']) {
            if (!$this->webp) {
            	if (!isset($this->request->cookie['jetcache_webp'])) {

$jc_webp_js =
"<script>
    var jc_webpTests = [{'uri': 'data:image/webp;base64,UklGRkQAAABXRUJQVlA4WAoAAAAQAAAAAQAAAQAAQUxQSAUAAAAAAAAAAABWUDggGAAAADABAJ0BKgIAAgACADQlpAADcAD++5QAAA=='}];
    var jc_webp = jc_webpTests.shift();

    function jc_test_webp(uri) {
      var jc_image = new Image();
      function jc_addResult(event) {
	      if (event && event.type === 'load' && jc_image.width == 2) {
	      	document.cookie = 'jetcache_webp=1; path=/; expires=Tue, 19 Jan 2038 03:14:07 GMT';
	      	console.log('WebP supported');
	      } else {
	      	document.cookie = 'jetcache_webp=0; path=/; expires=Tue, 19 Jan 2038 03:14:07 GMT';
	      	console.log('WebP NOT supported');
	      }
      }
      jc_image.onerror = jc_addResult;
      jc_image.onload = jc_addResult;
      jc_image.src = uri;
    }
    jc_test_webp(jc_webp.uri, function(e) {
      if (e && e.type === 'load') {
        for (var i = 0; i < jc_webpTests.length; i++) {
          jc_test_webp(jc_webpTests[i].uri);
        }
      }
    });
</script>
";
            		$output = str_ireplace('</body>', $jc_webp_js . '</body>', $output);
                }
            } else {
				$array_dir_application = explode('/', rtrim(DIR_APPLICATION, '/'));
				array_pop($array_dir_application);
				$root = implode('/', $array_dir_application) . '/';

				preg_match_all('/<img[^>]+>/i', $output, $images);

				if (isset($this->jetcache_settings['image_ex']) && $this->jetcache_settings['image_ex'] != '') {
					$ex_imaget_array = explode(PHP_EOL, trim($this->jetcache_settings['image_ex'], PHP_EOL));
                }

				foreach($images[0] as $num => $img) {

				 	$webp_file_exists = false;

                    $src_pettern_array = array(0 => '/src=["\']?([^"\']+)["\']/i', 1 => '/data-original=["\']?([^"\']+)["\']/i', 2 => '/data-src=["\']?([^"\']+)["\']/i');

				 	foreach($src_pettern_array as $num => $pattern) {
						preg_match($pattern, $img, $src);

						if (isset($src[1])) {

							$optimize = true;
							if (isset($this->jetcache_settings['image_ex']) && $this->jetcache_settings['image_ex'] != '') {

								foreach($ex_imaget_array as $num  => $image_ex) {
									$image_ex = trim($image_ex);
									if (isset($image_ex[0]) && $image_ex[0] != '#' && $image_ex != '') {
										if (stripos($src[1], $image_ex) === false) {
											$optimize = true;
										} else {
											$optimize = false;
											break;
										}
									}
								}
							}

							if ($optimize) {

					 			$src_original = $src[1];

                                if (stripos($src_original, 'http://') === false && stripos($src_original, 'https://') === false) {
                                	$src_original = $this->config_url . ltrim($src_original, '/');
                                }

					 			$parseurl = parse_url($src_original);
						 		$img_full_path = $root . ltrim($parseurl['path'], '/');

						 		$img_full_pathinfo = pathinfo($img_full_path);

						 		if (isset($img_full_pathinfo['extension']) && in_array($img_full_pathinfo['extension'], $webp_extension)) {
		                            // WTF in url cyrillic?
		                            $img_file_name_array = explode('/', $img_full_path);
		                            $img_file_name = array_pop($img_file_name_array);
		                            $img_file_name = substr($img_file_name, 0, strripos($img_file_name,'.'));

		        					$img_full_path = urldecode($img_full_path);

			                        $cache_path = str_replace($root, '', urldecode($img_full_pathinfo['dirname'])) . '/';

			                        $cache_path_full = DIR_IMAGE . 'cache/catalog/' . $cache_path;

			                        $img_webp_full_path = $cache_path_full .  urldecode($img_file_name) . '.webp';

									if (file_exists($img_webp_full_path)) {
			                        	$webp_file_exists = true;
									} else {

								 		if (file_exists($img_full_path)) {

											if (!is_dir($cache_path_full)) {
			                        	    	$cache_path_full = str_ireplace(DIR_IMAGE, '', $cache_path_full);
			                        	    	$this->mkdirs($cache_path_full, false);
				                        	}

			                                if ($this->jc_make_webp($img_full_path, $img_webp_full_path)) {
								 				$webp_file_exists = true;
								 			} else {
								 				$webp_file_exists = false;
								 			}
								 		}
							 		}

			                        if ($webp_file_exists) {

			                            $img_webp_url = str_replace($root, '', $img_webp_full_path);


										if (isset($this->jetcache_settings['image_webp_relative_url']) && $this->jetcache_settings['image_webp_relative_url']) {
				                            $img_webp_full_url = $img_webp_url;
			                            } else {
			                            	$img_webp_full_url = $this->config_url . $img_webp_url;
			                            }

			                        	$output = str_replace($src[1], $img_webp_full_url, $output);
			                        }
		                        }
	                        }
                        }
					}
				}
	     	}
     	}

        }
     	$response['output'] = $output;
        $response['jc_webp_js'] = $jc_webp_js;

		return $response;
	}

	public function jc_make_webp($image_old, $image_webp) {
		    if (strtolower(PHP_OS) == 'linux') {
	            $webp_version = array();

				$image_webp = str_replace('..', '', strip_tags($image_webp));

	            if (isset($this->jetcache_settings['image_webp_status']) && $this->jetcache_settings['image_webp_status']) {
		            if (isset($this->jetcache_settings['jc_path_webp']) && $this->jetcache_settings['jc_path_webp'] != '') {
		            	$io_path_webp = $this->jetcache_settings['jc_path_webp'];
		            } else {
						$io_path_webp = DIR_SYSTEM . 'library/io/webp/cwebp';
					}
				}
	            clearstatcache();
				if (isset($this->jetcache_settings['image_webp_lossess']) && $this->jetcache_settings['image_webp_lossess']) {
					$image_webp_lossess = '-lossless';
				} else {
					$image_webp_lossess = '';
				}

				if (isset($this->jetcache_settings['image_webp_command']) && $this->jetcache_settings['image_webp_command'] !='' ) {
					$image_webp_command = $this->jetcache_settings['image_webp_command'];
				} else {
					$image_webp_command = '';
				}

				$webp_exec = $io_path_webp . " " . $image_webp_lossess . " " . $image_webp_command . " '" . $image_old . "' -o '" . $image_webp . "' 2>&1";

				if (isset($this->jetcache_settings['image_exec']) && $this->jetcache_settings['image_exec']) {
					exec($webp_exec, $webp_version);
				}

				if ((isset($this->jetcache_settings['image_exec']) && !$this->jetcache_settings['image_exec']) && (isset($this->jetcache_settings['image_proc_open']) && $this->jetcache_settings['image_proc_open'])) {
					$descriptorspec = array(0 => array("pipe", "r"), 1 => array("pipe", "w"));
					$process = proc_open($webp_exec, $descriptorspec, $pipes);
					if (is_resource($process)) {
					    $webp_version = proc_close($process);
					}
			    }

				if (file_exists($image_webp) && (filesize($image_webp) > 0)) {
					return $image_webp;
				} else {
		        	if (file_exists($image_webp)) {
		        		unlink($image_webp);
		        	}
				}

			}
            return false;
	}

	private function mkdirs($dirName) {
    	$dirs = explode('/', rtrim($dirName, '/'));
	    $dir = '';
	    foreach ($dirs as $part) {
		    $dir.= $part . '/';

		    $dir_make = DIR_IMAGE . $dir;

		    if (strlen($dir_make) > 0) {
			   	if (!is_dir($dir_make)) {

				   	mkdir($dir_make);
		   		}
		    }
	    }
	    return true;
	}

	public function jc_quick_mimetype($path) {
		$pathextension = strtolower(trim(substr(strrchr($path, '.'), 1), '.'));
		switch ($pathextension) {
			case 'jpg':
			case 'jpeg':
			case 'jpe':
				return 'image/jpeg';
			case 'png':
				return 'image/png';
			case 'gif':
				return 'image/gif';
			case 'webp':
				return 'image/webp';
			case 'pdf':
				return 'application/pdf';
			default:
				return false;
		}
	}

	public function jc_mimetype( $path, $case ) {
		$type = false;

		if ( 'iq' === $case ) {
			return $this->jc_quick_mimetype( $path );
		}/*
		if ( 'i' === $case && preg_match( '/^RIFF.+WEBPVP8/', file_get_contents( $path, null, null, 0, 16 ) ) ) {
			 return 'image/webp';
		}*/
		if ( 'i' === $case ) {
			$fhandle = fopen( $path, 'rb' );
			if ( $fhandle ) {
				// Read first 12 bytes, which equates to 24 hex characters.
				$magic = bin2hex( fread( $fhandle, 12 ) );
				if ( 0 === strpos( $magic, '52494646' ) && 16 === strpos( $magic, '57454250' ) ) {
					$type = 'image/webp';
					return $type;
				}
				if ( 'ffd8ff' === substr( $magic, 0, 6 ) ) {
					$type = 'image/jpeg';
					return $type;
				}
				if ( '89504e470d0a1a0a' === substr( $magic, 0, 16 ) ) {
					$type = 'image/png';
					return $type;
				}
				if ( '474946383761' === substr( $magic, 0, 12 ) || '474946383961' === substr( $magic, 0, 12 ) ) {
					return $type;
				}
				if ( '25504446' === substr( $magic, 0, 8 ) ) {
					$type = 'application/pdf';
					return $type;
				}
			} else {

			}
		}
		if ( 'b' === $case ) {
			$fhandle = fopen( $path, 'rb' );
			if ( $fhandle ) {
				// Read first 4 bytes, which equates to 8 hex characters.
				$magic = bin2hex( fread( $fhandle, 4 ) );
				// Mac (Mach-O) binary.
				if ( 'cffaedfe' === $magic || 'feedface' === $magic || 'feedfacf' === $magic || 'cefaedfe' === $magic || 'cafebabe' === $magic ) {
					$type = 'application/x-executable';
					return $type;
				}
				// ELF (Linux or BSD) binary.
				if ( '7f454c46' === $magic ) {
					$type = 'application/x-executable';
					return $type;
				}
				// MS (DOS) binary.
				if ( '4d5a9000' === $magic ) {
					$type = 'application/x-executable';
					return $type;
				}
			} else {

			}
		}
		return false;
	}


	public function hook_image($jc_newimage) {

        if (isset($this->jetcache_settings['image_status']) && $this->jetcache_settings['image_status']) {

            if (PHP_OS == 'Darwin' || PHP_OS == 'SunOS' || PHP_OS == 'FreeBSD') { return $jc_newimage; }

         	 if (isset($this->jetcache_settings['image_ex']) && $this->jetcache_settings['image_ex'] != '') {
				$ex_imaget_array = explode(PHP_EOL, trim($this->jetcache_settings['image_ex'], PHP_EOL));
				foreach($ex_imaget_array as $num  => $image_ex) {
					$image_ex = trim($image_ex);
					if (isset($image_ex[0]) && $image_ex[0] != '#' && $image_ex != '') {
						if (stripos($jc_newimage, $image_ex) === false) {
						} else {
							return $jc_newimage;
						}
					}
				}
         	 }

            $jpegmoz_version = $optipng_version = $jpegoptim_version = array();

			$jc_newimage = str_replace('..', '', strip_tags($jc_newimage));
            $io_path_image = DIR_IMAGE . $jc_newimage;

            $io_mimetype = $this->jc_mimetype($io_path_image, 'i');

		    if (strtolower(PHP_OS) == 'linux') {

				if (isset($this->jetcache_settings['image_mozjpeg_command']) && $this->jetcache_settings['image_mozjpeg_command'] !='' ) {
					$image_mozjpeg_command = $this->jetcache_settings['image_mozjpeg_command'];
				} else {
					$image_mozjpeg_command = '';
				}

				if (isset($this->jetcache_settings['image_jpegoptim_command']) && $this->jetcache_settings['image_jpegoptim_command'] !='' ) {
					$image_jpegoptim_command = $this->jetcache_settings['image_jpegoptim_command'];
				} else {
					$image_jpegoptim_command = '';
				}

				if (isset($this->jetcache_settings['image_optipng_command']) && $this->jetcache_settings['image_optipng_command'] !='' ) {
					$image_optipng_command = $this->jetcache_settings['image_optipng_command'];
				} else {
					$image_optipng_command = '';
				}

                if (isset($this->jetcache_settings['jc_path_mozjpeg']) && $this->jetcache_settings['jc_path_mozjpeg'] != '') {
                	$io_path_mozjpeg = $this->jetcache_settings['jc_path_mozjpeg'];
                } else {
		    		$io_path_mozjpeg = DIR_SYSTEM . 'library/io/mozjpeg/cjpeg';
		    	}
                if (isset($this->jetcache_settings['jc_path_optipng']) && $this->jetcache_settings['jc_path_optipng'] != '') {
                	$io_path_optipng = $this->jetcache_settings['jc_path_optipng'];
                } else {
		    		$io_path_optipng = DIR_SYSTEM . 'library/io/optipng/optipng';
		    	}
                if (isset($this->jetcache_settings['jc_path_jpegoptim']) && $this->jetcache_settings['jc_path_jpegoptim'] != '') {
                	$io_path_jpegoptim = $this->jetcache_settings['jc_path_jpegoptim'];
                } else {
		    		$io_path_jpegoptim = DIR_SYSTEM . 'library/io/jpegoptim/jpegoptim';
		    	}

                if (isset($this->jetcache_settings['image_webp_status']) && $this->jetcache_settings['image_webp_status'] && (!isset($this->jetcache_settings['image_webp_mozjpeg']) || !$this->jetcache_settings['image_webp_mozjpeg'])) {

                } else {
	                if (isset($this->jetcache_settings['image_mozjpeg_status']) && $this->jetcache_settings['image_mozjpeg_status']) {
	                    clearstatcache();
						if (isset($this->jetcache_settings['image_mozjpeg_optimize']) && $this->jetcache_settings['image_mozjpeg_optimize']) {
							$image_mozjpeg_optimize = '-optimize';
						} else {
							$image_mozjpeg_optimize = '';
						}

						if (isset($this->jetcache_settings['image_mozjpeg_progressive']) && $this->jetcache_settings['image_mozjpeg_progressive']) {
							$image_mozjpeg_progressive = '-progressive';
						} else {
							$image_mozjpeg_progressive = '';
						}

		                if ($io_mimetype == 'image/jpeg') {
							$io_path_image_tmp = $io_path_image . '.jpg';
							if (file_exists($io_path_image)) {
								rename($io_path_image, $io_path_image_tmp);
							}
							$mozjpeg_exec = $io_path_mozjpeg . " " . $image_mozjpeg_command . " " . $image_mozjpeg_optimize . " " . $image_mozjpeg_progressive . " -outfile '" . $io_path_image . "' '" . $io_path_image_tmp . "' 2>&1";

				  			if (isset($this->jetcache_settings['image_exec']) && $this->jetcache_settings['image_exec']) {
				  				exec($mozjpeg_exec, $jpegmoz_version);
				  			}

							if ((isset($this->jetcache_settings['image_exec']) && !$this->jetcache_settings['image_exec']) && (isset($this->jetcache_settings['image_proc_open']) && $this->jetcache_settings['image_proc_open'])) {
								$descriptorspec = array(0 => array("pipe", "r"), 1 => array("pipe", "w"));
								$process = proc_open($mozjpeg_exec, $descriptorspec, $pipes);
								if (is_resource($process)) {
								    $jpegmoz_version = proc_close($process);
								}
			                }

							if (file_exists($io_path_image) && (filesize($io_path_image) > 0)) {
								if (file_exists($io_path_image_tmp)) {
									unlink($io_path_image_tmp);
								}
								return true;
							} else {
		                   		if (file_exists($io_path_image_tmp)) {
		                   			rename($io_path_image_tmp, $io_path_image);
		                   		}
							}
						}
	                }

	                if (isset($this->jetcache_settings['image_jpegoptim_status']) && $this->jetcache_settings['image_jpegoptim_status']) {
		                clearstatcache();
		                if ($io_mimetype == 'image/jpeg') {
							if (isset($this->jetcache_settings['image_jpegoptim_optimize']) && $this->jetcache_settings['image_jpegoptim_optimize']) {
								$image_jpegoptim_optimize = '--force ';
							} else {
								$image_jpegoptim_optimize = '';
							}

							if (isset($this->jetcache_settings['image_jpegoptim_level']) && $this->jetcache_settings['image_jpegoptim_level'] > 1 && $this->jetcache_settings['image_jpegoptim_level'] < 100) {
								$image_jpegoptim_level = '--max=' . (int)$this->jetcache_settings['image_jpegoptim_level'] . ' ';
							} else {
								$image_jpegoptim_level = '';
							}

							if (isset($this->jetcache_settings['image_jpegoptim_size']) && $this->jetcache_settings['image_jpegoptim_size'] > 1 && $this->jetcache_settings['image_jpegoptim_size'] < 100) {
								$image_jpegoptim_size = '--size=' . (int)$this->jetcache_settings['image_jpegoptim_size'] . '% ';
							} else {
								$image_jpegoptim_size = '';
							}

							if (isset($this->jetcache_settings['image_jpegoptim_strip']) && $this->jetcache_settings['image_jpegoptim_strip']) {
								$image_jpegoptim_strip = '--strip-all --strip-iptc ';
							} else {
								$image_jpegoptim_strip = '';
							}

							if (isset($this->jetcache_settings['image_jpegoptim_progressive']) && $this->jetcache_settings['image_jpegoptim_progressive']) {
								$image_jpegoptim_progressive = '--all-progressive ';
							} else {
								$image_jpegoptim_progressive = '';
							}

							$jpegoptim_exec_string = $io_path_jpegoptim . " " . $image_jpegoptim_command . " " . $image_jpegoptim_optimize . $image_jpegoptim_progressive . $image_jpegoptim_strip . $image_jpegoptim_size . $image_jpegoptim_level . "--overwrite '" . $io_path_image . "'  2>&1";


				  			if (isset($this->jetcache_settings['image_exec']) && $this->jetcache_settings['image_exec']) {
		                		exec($jpegoptim_exec_string, $jpegoptim_version);
				  			}

							if ((isset($this->jetcache_settings['image_exec']) && !$this->jetcache_settings['image_exec']) && (isset($this->jetcache_settings['image_proc_open']) && $this->jetcache_settings['image_proc_open'])) {
								$descriptorspec = array(0 => array("pipe", "r"), 1 => array("pipe", "w"));
								$process = proc_open($jpegoptim_exec_string, $descriptorspec, $pipes);
								if (is_resource($process)) {
								    $jpegoptim_version = proc_close($process);
								}
			                }
		                }
	                }

	                if (isset($this->jetcache_settings['image_optipng_status']) && $this->jetcache_settings['image_optipng_status']) {
	                    clearstatcache();
						if (isset($this->jetcache_settings['optipng_optimize_level'])) {
							$image_optipng_optimize = (int)$this->jetcache_settings['optipng_optimize_level'];
						} else {
							$image_optipng_optimize = '1';
						}

		                if ($io_mimetype == 'image/png') {
		                	$optipng_exec = $io_path_optipng . " " . $image_optipng_command . " -o" . $image_optipng_optimize . " -quiet -strip all '" . $io_path_image . "' 2>&1";
				  			if (isset($this->jetcache_settings['image_exec']) && $this->jetcache_settings['image_exec']) {
		                		exec($optipng_exec, $optipng_version);
				  			}

							if ((isset($this->jetcache_settings['image_exec']) && !$this->jetcache_settings['image_exec']) && (isset($this->jetcache_settings['image_proc_open']) && $this->jetcache_settings['image_proc_open'])) {
								$descriptorspec = array(0 => array("pipe", "r"), 1 => array("pipe", "w"));
								$process = proc_open($optipng_exec, $descriptorspec, $pipes);
								if (is_resource($process)) {
								    $optipng_version = proc_close($process);
								}
			                }
		                }
	                }
		        }
		    }
        }
	}

	private function array_diff_assoc_recursive($array1, $array2) {
	    $difference=array();
	    foreach($array1 as $key => $value) {
	        if( is_array($value) ) {
	            if( !isset($array2[$key]) || !is_array($array2[$key]) ) {
	                $difference[$key] = $value;
	            } else {
	                $new_diff = $this->array_diff_assoc_recursive($value, $array2[$key]);
	                if( !empty($new_diff) )
	                    $difference[$key] = $new_diff;
	            }
	        } else if( !array_key_exists($key,$array2) || $array2[$key] !== $value ) {
	            $difference[$key] = $value;
	        }
	    }
	    return $difference;
	}

}
}
