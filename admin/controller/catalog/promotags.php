<?php
class ControllerCatalogPromoTags extends Controller {
	private $error = array();

  	public function index() {
		$this->load->language('catalog/promotags');

		$this->document->setTitle($this->language->get('heading_title')); 

		$this->load->model('catalog/promotags');

		$this->getList();
  	}

  	public function add() {
    	$this->load->language('catalog/promotags');

    	$this->document->setTitle($this->language->get('heading_title')); 

		$this->load->model('catalog/promotags');

    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_promotags->addPromoTags($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/promotags', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
    	}

    	$this->getForm();
  	}

  	public function edit() {
    	$this->load->language('catalog/promotags');

    	$this->document->setTitle($this->language->get('heading_title')); 

		$this->load->model('catalog/promotags');

    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_promotags->editPromoTags($this->request->get['promo_tags_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/promotags', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
		}

    	$this->getForm();
  	}

  	public function delete() {
    	$this->load->language('catalog/promotags');

    	$this->document->setTitle($this->language->get('heading_title')); 

		$this->load->model('catalog/promotags');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $promo_tags_id) {
				$this->model_catalog_promotags->deletePromoTags($promo_tags_id);
	  		}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/promotags', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
		}

    	$this->getList();
  	}

	public function repair() {
		$this->load->language('catalog/promotags');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/promotags');

		if ($this->validateRepair()) {
			$this->model_catalog_promotags->repairPromotags();

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('catalog/promotags', 'user_token=' . $this->session->data['user_token'], 'SSL'));
		}

		$this->getList();
	}


	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/promotags', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL')
		);

		$data['add'] = $this->url->link('catalog/promotags/add', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('catalog/promotags/delete', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');
		$data['repair'] = $this->url->link('catalog/promotags/repair', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');

		$data['promotags'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$this->load->model('tool/image');
		$promotags_total = $this->model_catalog_promotags->getTotalPromoTags($data);
		$results = $this->model_catalog_promotags->getPromoTags($data);

		foreach ($results as $result) {			
			$data['promotags'][] = array(
				'promo_tags_id' 	=> $result['promo_tags_id'],
				'promo_text'    	=> $result['promo_text'],
				'sort_order'    	=> $result['sort_order'],
				'selected'   		=> isset($this->request->post['selected']) && in_array($result['promo_tags_id'], $this->request->post['selected']),
				'edit'        		=> $this->url->link('catalog/promotags/edit', 'user_token=' . $this->session->data['user_token'] . '&promo_tags_id=' . $result['promo_tags_id'] . $url, 'SSL'),
				'delete'      		=> $this->url->link('catalog/promotags/delete', 'user_token=' . $this->session->data['user_token'] . '&promo_tags_id=' . $result['promo_tags_id'] . $url, 'SSL')
			);
		}

 		$data['user_token'] = $this->session->data['user_token'];

 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		if (isset($this->request->get['filter_promo_text'])) {
			$filter_promo_text = $this->request->get['filter_promo_text'];
		} else {
			$filter_promo_text = NULL;
		}

		if (isset($this->request->get['filter_promo_link'])) {
			$filter_promo_link = $this->request->get['filter_promo_link'];
		} else {
			$filter_promo_link = NULL;
		}

		if (isset($this->request->get['filter_sort_order'])) {
			$filter_sort_order = $this->request->get['filter_sort_order'];
		} else {
			$filter_sort_order = NULL;
		}

		$url = '';

		if (isset($this->request->get['filter_promo_text'])) {
			$url .= '&filter_promo_text=' . $this->request->get['filter_promo_text'];
		}
		
		if (isset($this->request->get['filter_promo_link'])) {
			$url .= '&filter_promo_link=' . $this->request->get['filter_promo_link'];
		}

		if (isset($this->request->get['filter_sort_order'])) {
			$url .= '&filter_sort_order=' . $this->request->get['filter_sort_order'];
		} 

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_promo_text'] = $this->url->link('catalog/promotags', 'user_token=' . $this->session->data['user_token'] . '&sort=ptd.promo_text' . $url, 'SSL');
		$data['sort_sort_order'] = $this->url->link('catalog/promotags', 'user_token=' . $this->session->data['user_token'] . '&sort=pt.sort_order' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $promotags_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/promotags', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($promotags_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($promotags_total - $this->config->get('config_limit_admin'))) ? $promotags_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $promotags_total, ceil($promotags_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/promotags_list', $data));
	}

	protected function getForm() {
		
 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

 		if (isset($this->error['promo_text'])) {
			$data['error_promo_text'] = $this->error['promo_text'];
		} else {
			$data['error_promo_text'] = '';
		}
		

   		if (isset($this->error['sort_order'])) {
			$data['error_sort_order'] = $this->error['sort_order'];
		} else {
			$data['error_sort_order'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_promo_text'])) {
			$url .= '&filter_promo_text=' . $this->request->get['filter_promo_text'];
		}

		if (isset($this->request->get['filter_promo_link'])) {
			$url .= '&filter_promo_link=' . $this->request->get['filter_promo_link'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/promotags', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL')
		);

		$data['add'] = $this->url->link('catalog/promotags/add', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('catalog/promotags/delete', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');
		$data['repair'] = $this->url->link('catalog/promotags/repair', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');
   		
   		$this->load->model('localisation/language');
   		$languages = $this->model_localisation_language->getLanguages();
   		$data['languages'] = $languages;

		if (!isset($this->request->get['promo_tags_id'])) {
			$data['action'] = $this->url->link('catalog/promotags/add', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('catalog/promotags/edit', 'user_token=' . $this->session->data['user_token'] . '&promo_tags_id=' . $this->request->get['promo_tags_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('catalog/promotags', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->get['promo_tags_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
                    $promotags_info = $this->model_catalog_promotags->getPromoTag($this->request->get['promo_tags_id']);
                }
		              
                
                if (isset($this->request->post['promo_text'])) {
			$data['promo_text'] = $this->request->post['promo_text'];
		} elseif (isset($this->request->get['promo_tags_id'])) {
			$data['promo_text'] = $this->model_catalog_promotags->getPromoTagDescription($this->request->get['promo_tags_id']);
		} else {
			$data['promo_text'] = array();
		}
                
		
				
		if (isset($this->request->post['sort_order'])) {
                        $data['sort_order'] = $this->request->post['sort_order'];
                } elseif (isset($promotags_info)) {
                        $data['sort_order'] = $promotags_info['sort_order'];
                } else {	
                        $data['sort_order'] = '';
                }
                
                if (isset($this->request->post['class'])) {
                        $data['class'] = $this->request->post['class'];
                } elseif (isset($promotags_info)) {
                        $data['class'] = $promotags_info['class'];
                } else {	
                        $data['class'] = '';
                }

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/promotags_form', $data));
  	}

	protected function validateForm() {
            if (!$this->user->hasPermission('modify', 'catalog/promotags')) {
                    $this->error['warning'] = $this->language->get('error_permission');
            }
            
            if(isset($this->request->post['promo_text'])){
                foreach ($this->request->post['promo_text'] as $language_code => $value) {
                    if ((utf8_strlen($value) < 1) || (utf8_strlen($value) > 64)) {
                        $this->error['promo_text'][$language_code] = $this->language->get('error_promo_text');
                    }
                }
            }

            if (!$this->error) {
                    return TRUE;
            } else {
                    if (!isset($this->error['warning'])) {
                            $this->error['warning'] = $this->language->get('error_required_data');
                    }
                    return FALSE;
            }
  	}

	protected function validateDelete() {
    	if (!$this->user->hasPermission('modify', 'catalog/promotags')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}
		
		$this->load->model('catalog/promotags');

		foreach ($this->request->post['selected'] as $promotags_id) {
  			$promotags_top_right = $this->model_catalog_promotags->getTotalProductsByPromoTagsTopRightId($promotags_id);

			if ($promotags_top_right) {
	  			$this->error['warning'] = sprintf($this->language->get('error_promotags'), $promotags_top_right);	
			}
	  	} 
		
		if (!$this->error) {
	  		return TRUE;
		} else {
	  		return FALSE;
		}
  	}

	protected function validateRepair() {
		if (!$this->user->hasPermission('modify', 'catalog/promotags')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}
?>