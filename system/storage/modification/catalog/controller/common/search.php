<?php
class ControllerCommonSearch extends Controller {
	public function index() {
		$this->load->language('common/search');

		$data['text_search'] = $this->language->get('text_search');

		if (isset($this->request->get['search'])) {
			$data['search'] = $this->request->get['search'];
		} else {
			$data['search'] = '';
		}


        if ($this->config->get('mlseo_fix_search')) {
          $data['csp_search_url'] = $this->url->link('product/search');
          $data['csp_search_url_param'] = $this->url->link('product/search', 'search=%search%');
          
          return $this->load->view('common/csp_search', $data);
        }
      
		return $this->load->view('common/search', $data);
	}
}