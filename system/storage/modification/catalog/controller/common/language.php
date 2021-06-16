<?php
class ControllerCommonLanguage extends Controller {
    public function index() {
        $this->load->language('common/language');

        $data['action'] = $this->url->link('common/language/language', '', $this->request->server['HTTPS']);

        $data['code'] = $this->session->data['language'];

        $this->load->model('localisation/language');

        $data['languages'] = array();

        $results = $this->model_localisation_language->getLanguages();

        foreach ($results as $result) {
            if ($result['status']) {
                $data['languages'][] = array(
                    'name' => $result['name'],
                    'code' => $result['code']
                );
            }
        }

        if (!isset($this->request->get['route'])) {
            $data['redirect'] = 'common/home';
        } else {
            $url_data = $this->request->get;

            unset($url_data['_route_']);

            $route = $url_data['route'];

            unset($url_data['route']);

      unset($url_data['site_language']);
      unset($url_data['_route_']);
    

            $url = '';

            if ($url_data) {
                $url = '&' . urldecode(http_build_query($url_data, '', '&'));
            }

            $data['redirect'] = $route . $url;
        }

        return $this->load->view('common/language', $data);
    }

    public function language() {
        if (isset($this->request->post['code']) && !$this->config->get('mlseo_store_mode')) {
      $lgCodes = array_flip((array) $this->config->get('mlseo_lang_codes'));
      
      if (!empty($lgCodes[$this->request->post['code']])) {
        $this->config->set('config_language', $this->request->post['code']);
        $this->config->set('config_language_id', $lgCodes[$this->request->post['code']]);
      }
      
            $this->session->data['language'] = $this->request->post['code'];

            $this->load->model('localisation/language');

            $this->config->set('config_language_id', $this->model_localisation_language->getLanguageByCode($this->request->post['code'])['language_id']);
        }

        if (isset($this->request->post['redirect'])) {
            $urlRedirect = '';
            $parseUrl = parse_url(str_replace('&amp;', '&', $this->request->post['redirect']));

            if (isset($parseUrl['query'])) {
                $parts = explode('/', $parseUrl['path']);
                if (utf8_strlen(reset($parts)) == 0) {
                    array_shift($parts);
                }

                // remove any empty arrays from trailing
                if (utf8_strlen(end($parts)) == 0) {
                    array_pop($parts);
                }
                foreach ($parts as $part) {
                    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE keyword = '" . $this->db->escape($part) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");
                    if ($query->num_rows) {
                        if ($this->config->get('config_language_id') != $query->row['language_id']) {
                            $url = explode('=', $query->row['query']);
                            if (isset($url[0]) && isset($url[1])) {
                                $query2 = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = '" . $this->db->escape($url[0] . '=' . $url[1]) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
                            } else {
                                $query2 = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = '" . $this->db->escape($url[0]) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
                            }
                            if ($query2->num_rows) {
                                $urlRedirect .= '/'. $query2->row['keyword'] . '/?' . $parseUrl['query'];
                            }
                        }
                    }
                }

                $this->response->redirect(getUrl().$urlRedirect);
            } elseif ($parseUrl['path'] == '/') {
                
      if (!empty($this->request->post["code"])) {
        $this->load->model('localisation/language');
        $languagesByCode = $this->model_localisation_language->getLanguages();
        
        if (isset($languagesByCode[$this->request->post['code']]['language_id'])) {
          $this->config->set('config_language_id', $languagesByCode[$this->request->post['code']]['language_id']);
        }
      }
      
      if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
        $connection = 'SSL';
      } else {
        $connection = 'NONSSL';
      }
    
      $redir_route = $this->request->post['redirect'];
      
      if (substr($redir_route, 0, 1) == '/') {
        $this->response->redirect($this->request->post['redirect']); 
      }
      
      if (substr($redir_route, 0, 4) == 'http') {
        $redir_route = 'common/home';
      }
      
      if ($redir_params = strstr($redir_route, '&')) {
        $redir_route = str_replace($redir_params, '', $redir_route);
      } else {
        $redir_params = '';
      }
      
      // handle sub-stores rewriting
      if ($this->config->get('mlseo_store_mode')) {
        // set store currency
        $this->session->data['currency'] = $this->config->get('config_currency');
        unset($this->session->data['shipping_method']);
        unset($this->session->data['shipping_methods']);
        
        $lang_to_store = $this->config->get('mlseo_lang_to_store');
      }
      
      if (!empty($lang_to_store)) {
        $this->response->redirect(str_replace(array(rtrim($this->config->get('config_url'), '/'), rtrim($this->config->get('config_ssl'), '/')), $lang_to_store[$this->request->post['code']], $this->url->link($redir_route, str_replace('&amp;', '&', $redir_params), $connection)));
      }
      
      $this->response->redirect($this->url->link($redir_route, str_replace('&amp;', '&', $redir_params), $connection));
      
            } else {
                $parts = explode('/', $parseUrl['path']);

                if (utf8_strlen(reset($parts)) == 0) {
                    array_shift($parts);
                }

                // remove any empty arrays from trailing
                if (utf8_strlen(end($parts)) == 0) {
                    array_pop($parts);
                }

                foreach ($parts as $part) {
                    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE keyword = '" . $this->db->escape($part) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");
                    if ($query->num_rows) {
                        if ($this->config->get('config_language_id') != $query->row['language_id']) {
                            $url = explode('=', $query->row['query']);
                            if (isset($url[0]) && isset($url[1])) {
                                $query2 = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = '" . $this->db->escape($url[0] . '=' . $url[1]) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
                            } else {
                                $query2 = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = '" . $this->db->escape($url[0]) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
                            }
                            if ($query2->num_rows) {
                                $urlRedirect .= '/'. $query2->row['keyword'];
                            }
                        }
                    }
                    else {
                        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tltblog_url_alias WHERE keyword = '" . $this->db->escape($part) . "'");
                        if ($query->num_rows) {
                            if ($this->config->get('config_language_id') != $query->row['language_id']) {
                                $url = explode('=', $query->row['query']);
                                $query3 = $this->db->query("SELECT * FROM " . DB_PREFIX . "tltblog_url_alias WHERE query = '" . $this->db->escape($url[0] . '=' . $url[1]) . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
                                if ($query3->num_rows) {
                                    $urlRedirect .= '/'. $query3->row['keyword'];
                                }	else {
                                    if ($this->config->has('tltblog_path')) {
                                        $path_array = $this->config->get('tltblog_path');

                                        $selector = false;
                                        foreach($path_array as $key => $pa){
                                            if($pa==$part){
                                                $selector = $key;
                                            }
                                        }
                                        if($selector){
                                            $urlRedirect .= '/'. $path_array[(int)$this->config->get('config_language_id')];
                                        }
                                    }
                                }

                            }
                        }
                    }
                }
                if ($urlRedirect) {
                    $this->response->redirect(getUrl().$urlRedirect);
                    $this->log->write(getUrl().$urlRedirect);
                } else {
                    $this->log->write($this->request->post['redirect']);
                    
      if (!empty($this->request->post["code"])) {
        $this->load->model('localisation/language');
        $languagesByCode = $this->model_localisation_language->getLanguages();
        
        if (isset($languagesByCode[$this->request->post['code']]['language_id'])) {
          $this->config->set('config_language_id', $languagesByCode[$this->request->post['code']]['language_id']);
        }
      }
      
      if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
        $connection = 'SSL';
      } else {
        $connection = 'NONSSL';
      }
    
      $redir_route = $this->request->post['redirect'];
      
      if (substr($redir_route, 0, 1) == '/') {
        $this->response->redirect($this->request->post['redirect']); 
      }
      
      if (substr($redir_route, 0, 4) == 'http') {
        $redir_route = 'common/home';
      }
      
      if ($redir_params = strstr($redir_route, '&')) {
        $redir_route = str_replace($redir_params, '', $redir_route);
      } else {
        $redir_params = '';
      }
      
      // handle sub-stores rewriting
      if ($this->config->get('mlseo_store_mode')) {
        // set store currency
        $this->session->data['currency'] = $this->config->get('config_currency');
        unset($this->session->data['shipping_method']);
        unset($this->session->data['shipping_methods']);
        
        $lang_to_store = $this->config->get('mlseo_lang_to_store');
      }
      
      if (!empty($lang_to_store)) {
        $this->response->redirect(str_replace(array(rtrim($this->config->get('config_url'), '/'), rtrim($this->config->get('config_ssl'), '/')), $lang_to_store[$this->request->post['code']], $this->url->link($redir_route, str_replace('&amp;', '&', $redir_params), $connection)));
      }
      
      $this->response->redirect($this->url->link($redir_route, str_replace('&amp;', '&', $redir_params), $connection));
      
                }
            }
        } else {
            $this->response->redirect($this->url->link('common/home'));
        }
    }
}