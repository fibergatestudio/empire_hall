<?php
class ControllerCatalogInspiration extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('catalog/inspiration');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/inspiration');

        $this->getList();
    }

    public function add() {
        $this->load->language('catalog/inspiration');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/inspiration');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_catalog_inspiration->addInspiration($this->request->post);

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

            $this->response->redirect($this->url->link('catalog/inspiration', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('catalog/inspiration');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/inspiration');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_catalog_inspiration->editInspiration($this->request->get['inspiration_id'], $this->request->post);

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

            $this->response->redirect($this->url->link('catalog/inspiration', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('catalog/inspiration');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/inspiration');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $inspiration_id) {
                $this->model_catalog_inspiration->deleteInspiration($inspiration_id);
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

            $this->response->redirect($this->url->link('catalog/inspiration', 'user_token=' . $this->session->data['user_token'] . $url, true));
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
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('catalog/inspiration', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['add'] = $this->url->link('catalog/inspiration/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('catalog/inspiration/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['inspirations'] = array();

        $filter_data = array(
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $inspiration_total = $this->model_catalog_inspiration->getTotalInspirations();

        $results = $this->model_catalog_inspiration->getInspirations($filter_data);

        foreach ($results as $result) {
            $data['inspirations'][] = array(
                'inspiration_id'  => $result['inspiration_id'],
                'name'            => $result['name'],
                'sort_order'      => $result['sort_order'],
                'edit'            => $this->url->link('catalog/inspiration/edit', 'user_token=' . $this->session->data['user_token'] . '&inspiration_id=' . $result['inspiration_id'] . $url, true)
            );
        }

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

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_name'] = $this->url->link('catalog/inspiration', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
        $data['sort_sort_order'] = $this->url->link('catalog/inspiration', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url, true);

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $inspiration_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('catalog/inspiration', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($inspiration_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($inspiration_total - $this->config->get('config_limit_admin'))) ? $inspiration_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $inspiration_total, ceil($inspiration_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/inspiration_list', $data));
    }

    protected function getForm() {
        $data['text_form'] = !isset($this->request->get['inspiration_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        if (isset($this->error['keyword'])) {
            $data['error_keyword'] = $this->error['keyword'];
        } else {
            $data['error_keyword'] = '';
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
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('catalog/inspiration', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!isset($this->request->get['inspiration_id'])) {
            $data['action'] = $this->url->link('catalog/inspiration/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('catalog/inspiration/edit', 'user_token=' . $this->session->data['user_token'] . '&inspiration_id=' . $this->request->get['inspiration_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('catalog/inspiration', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['inspiration_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $inspiration_info = $this->model_catalog_inspiration->getInspiration($this->request->get['inspiration_id']);
        }
        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('setting/store');

        $data['stores'] = array();

        $data['stores'][] = array(
            'store_id' => 0,
            'name'     => $this->language->get('text_default')
        );

        $stores = $this->model_setting_store->getStores();

        foreach ($stores as $store) {
            $data['stores'][] = array(
                'store_id' => $store['store_id'],
                'name'     => $store['name']
            );
        }

        if (isset($this->request->post['inspiration_description'])) {
            $data['inspiration_description'] = $this->request->post['inspiration_description'];
        } elseif (isset($this->request->get['inspiration_id'])) {
            $data['inspiration_description'] = $this->model_catalog_inspiration->getInspirationDescriptions($this->request->get['inspiration_id']);
        } else {
            $data['inspiration_description'] = array();
        }
        if (isset($this->request->post['image'])) {
            $data['image'] = $this->request->post['image'];
        } elseif (!empty($inspiration_info)) {
            $data['image'] = $inspiration_info['image'];
        } else {
            $data['image'] = '';
        }

        $this->load->model('tool/image');

        if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
            $data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
        } elseif (!empty($inspiration_info) && is_file(DIR_IMAGE . $inspiration_info['image'])) {
            $data['thumb'] = $this->model_tool_image->resize($inspiration_info['image'], 100, 100);
        } else {
            $data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        if (isset($this->request->post['image2'])) {
            $data['image2'] = $this->request->post['image2'];
        } elseif (!empty($inspiration_info)) {
            $data['image2'] = $inspiration_info['image2'];
        } else {
            $data['image2'] = '';
        }

        if (isset($this->request->post['image2']) && is_file(DIR_IMAGE . $this->request->post['image2'])) {
            $data['thumb2'] = $this->model_tool_image->resize($this->request->post['image2'], 100, 100);
        } elseif (!empty($inspiration_info) && is_file(DIR_IMAGE . $inspiration_info['image2'])) {
            $data['thumb2'] = $this->model_tool_image->resize($inspiration_info['image2'], 100, 100);
        } else {
            $data['thumb2'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($inspiration_info)) {
            $data['sort_order'] = $inspiration_info['sort_order'];
        } else {
            $data['sort_order'] = '';
        }

        if (isset($this->request->post['inspiration_related'])) {
            $products = $this->request->post['inspiration_related'];
        } elseif (isset($this->request->get['inspiration_id'])) {
            $products = $this->model_catalog_inspiration->getProductRelated($this->request->get['inspiration_id']);
        } else {
            $products = array();
        }

        $data['inspiration_relateds'] = array();
        $this->load->model('catalog/product');

        foreach ($products as $product_id) {
            $related_info = $this->model_catalog_product->getProduct($product_id);

            if ($related_info) {
                $data['inspiration_relateds'][] = array(
                    'product_id' => $related_info['product_id'],
                    'name'       => $related_info['name']
                );
            }
        }


        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();
        if (isset($this->request->post['inspiration_seo_url'])) {
            $data['inspiration_seo_url'] = $this->request->post['inspiration_seo_url'];
        } elseif (isset($this->request->get['inspiration_id'])) {
            $data['inspiration_seo_url'] = $this->model_catalog_inspiration->getInspirationSeoUrls($this->request->get['inspiration_id']);
        } else {
            $data['inspiration_seo_url'] = array();
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/inspiration_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'catalog/inspiration')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        foreach ($this->request->post['inspiration_description'] as $language_id => $value) {
            if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 255)) {
                $this->error['name'][$language_id] = $this->language->get('error_name');
            }
        }

        if ($this->request->post['inspiration_seo_url']) {
            $this->load->model('design/seo_url');

            foreach ($this->request->post['inspiration_seo_url'] as $store_id => $language) {
                foreach ($language as $language_id => $keyword) {
                    if (!empty($keyword)) {
                        if (count(array_keys($language, $keyword)) > 1) {
                            $this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
                        }

                        $seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);

                        foreach ($seo_urls as $seo_url) {
                            if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['inspiration_id']) || (($seo_url['query'] != 'inspiration_id=' . $this->request->get['inspiration_id'])))) {
                                $this->error['keyword'][$store_id][$language_id] = $this->language->get('error_keyword');
                            }
                        }
                    }
                }
            }
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'catalog/inspiration')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}