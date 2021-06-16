<?php
class ControllerErrorNotFound extends Controller {
	public function index() {

  if ($this->config->get('mlseo_404') && $this->config->get('mlseo_404_log')) {
    $logUrl = true;
    
    if ($this->config->get('mlseo_404_filter')) {
      foreach (explode('|', $this->config->get('mlseo_404_filter')) as $part) {
        if (!$part) continue;
        if (strpos($_SERVER['REQUEST_URI'], $part) !== false) {
          $logUrl = false;
        }
      }
    }
    
    if ($this->config->get('mlseo_404_filter_ext')) {
      if (pathinfo($_SERVER['REQUEST_URI'], PATHINFO_EXTENSION)) {
        if (in_array(pathinfo($_SERVER['REQUEST_URI'], PATHINFO_EXTENSION), explode('|', $this->config->get('mlseo_404_filter_ext')))) {
          $logUrl = false;
        }
      }
    }
    
    if ($logUrl) {
     $this->load->model('tool/seo_package');
     $this->model_tool_seo_package->addUrl404(urldecode('http' . (!empty($_SERVER['HTTPS']) ? 's' : '') . '://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
    }
  }
  
  if ($this->config->get('mlseo_404') && $this->config->get('mlseo_404_redir')) {
    $seoSortNames = $this->config->get('mlseo_sortname_'.$this->config->get('config_language_id')) ? $this->config->get('mlseo_sortname_'.$this->config->get('config_language_id')) : 'name|price|rating|model';
    $seoSortOrders = $this->config->get('mlseo_order_'.$this->config->get('config_language_id')) ? $this->config->get('mlseo_order_'.$this->config->get('config_language_id')) : 'asc|desc';
    $seoSortKeyword = $this->config->get('mlseo_sort_'.$this->config->get('config_language_id')) ? $this->config->get('mlseo_sort_'.$this->config->get('config_language_id')) : 'sort';
    
    $parts = explode('/', $_SERVER['REQUEST_URI']);
    
    foreach ($parts as $key => $part) {
      if (preg_match('~^'.$seoSortKeyword.'-('.$seoSortNames.')-('.$seoSortOrders.')$~', $part)) {
        unset($parts[$key]);
      } else if (preg_match('~^'.($this->config->get('mlseo_limit_'.$this->config->get('config_language_id')) ? $this->config->get('mlseo_limit_'.$this->config->get('config_language_id')) : 'limit').'-(\d{1,3})$~', $part)) {
        unset($parts[$key]);
      } else if (preg_match('/page-(\d+)/', $part, $page)) {
        unset($parts[$key]);
      }
    }
    
    if (end($parts)) {
      $last_part = trim(str_replace(array('-', '_', $this->config->get('mlseo_extension')), ' ', end($parts)));
      if (strpos($last_part, '?')) {
        $last_part = strstr($last_part, '?', true);
      }
      
      if ($this->config->get('mlseo_404_redir') == 'first_word') {
        $words = explode(' ', $last_part);
        $search_404 = reset($words);
      } else if ($this->config->get('mlseo_404_redir') == 'last_word') {
        $words = explode(' ', $last_part);
        $search_404 = end($words);
      } else {
        $search_404 = $last_part;
      }
      
      if (substr($search_404, -5) == '.html') {
        $search_404 = substr($search_404, 0, -5);
      } else if (substr($search_404, -4) == '.php') {
        $search_404 = substr($search_404, 0, -4);
      }
      
      if (false) {
        // redirection mode without 404
        $redir = $this->url->link('product/search', 'search='.urlencode($search_404).'&description=1');
        $this->response->redirect($redir);
      } else {
        // search page with 404 code
        $this->request->get['search'] = urldecode($search_404);
        $this->request->get['description'] = 1;
        $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');
        return new Action('product/search');
      }
    }
  }
      
		$this->load->language('error/not_found');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		if (isset($this->request->get['route'])) {
			$url_data = $this->request->get;

			unset($url_data['_route_']);

			$route = $url_data['route'];

			unset($url_data['route']);

			$url = '';

			if ($url_data) {
				$url = '&' . urldecode(http_build_query($url_data, '', '&'));
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link($route, $url, $this->request->server['HTTPS'])
			);
		}

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

		$this->response->setOutput($this->load->view('error/not_found', $data));
	}
}