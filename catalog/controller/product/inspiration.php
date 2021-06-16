<?php
class ControllerProductInspiration extends Controller {
    public function index() {
        $this->load->language('product/inspiration');

        $this->load->model('catalog/inspiration');

        $this->load->model('tool/image');
        $curr_lang =  $this->language->get('code');
        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();
        $data['logged'] = $this->customer->isLogged();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('product/inspiration')
        );

        $language_id = $this->config->get('config_language_id');

        $data['page_subtitle'] = html_entity_decode($this->config->get('config_inspiration_subtitle')[$language_id]);
        $data['page_description'] = html_entity_decode($this->config->get('config_inspiration_text')[$language_id]);

        $data['inspirations'] = array();

        if($this->cache->get('inspiration.index.getInspirations'."_".$curr_lang)){
            $results = $this->cache->get('inspiration.index.getInspirations'."_".$curr_lang);
        }else{
            $results = $this->model_catalog_inspiration->getInspirations();
            $this->cache->set('inspiration.index.getInspirations'."_".$curr_lang, $results);
        }

        foreach ($results as $result) {

            if (is_file(DIR_IMAGE . $result['image'])) {
                $image = $this->model_tool_image->resize($result['image'], 1400, 680);
                $image_small = $this->model_tool_image->resize($result['image'], 1400, 680);
            } else {
                $image = $this->model_tool_image->resize('placeholder.png', 1400, 680);
                $image_small = $this->model_tool_image->resize('placeholder.png', 680, 680);
            }

            $data['inspirations'][] = array(
                'name'  => $result['name'],
                'subtext' => html_entity_decode($result['subtext']),
                'image' => $image,
                'image_small' => $image_small,
                'href'  => $this->url->link('product/inspiration/info', 'inspiration_id=' . $result['inspiration_id'])
            );
        }

        $data['continue'] = $this->url->link('common/home');

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('product/inspiration_list', $data));
    }

    public function info() {
        $this->load->language('product/inspiration');
        $this->load->model('catalog/inspiration');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        $curr_lang =  $this->language->get('code');
        if (isset($this->request->get['inspiration_id'])) {
            $inspiration_id = (int)$this->request->get['inspiration_id'];
        } else {
            $inspiration_id = 0;
        }
        $data['logged'] = $this->customer->isLogged();
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('product/inspiration')
        );

        if($this->cache->get('inspiration.info.getInspiration'.$inspiration_id."_".$curr_lang)){
            $inspiration_info = $this->cache->get('inspiration.info.getInspiration'.$inspiration_id."_".$curr_lang);
        }else{
            $inspiration_info = $this->model_catalog_inspiration->getInspiration($inspiration_id);
            $this->cache->set('inspiration.info.getInspiration'.$inspiration_id."_".$curr_lang, $inspiration_info);
        }

        if ($inspiration_info) {
            $this->document->setTitle($inspiration_info['name']);

            $data['breadcrumbs'][] = array(
                'text' => $inspiration_info['name'],
                'href' => $this->url->link('product/inspiration')
            );

            $data['heading_title'] = $inspiration_info['name'];
            $data['heading_subtitle'] = html_entity_decode($inspiration_info['subtext']);
            $data['description'] = html_entity_decode($inspiration_info['description']);

            if (is_file(DIR_IMAGE . $inspiration_info['image2'])) {
                $data['image'] = $this->model_tool_image->resize($inspiration_info['image2'], 1860, 860);
            } else {
                $data['image'] = '';
            }

            $data['products'] = array();

            if($this->cache->get('inspiration.info.getProductInspiration'.$inspiration_id."_".$curr_lang)){
                $results = $this->cache->get('inspiration.info.getProductInspiration'.$inspiration_id."_".$curr_lang);
            }else{
                $results = $this->model_catalog_product->getProductInspiration($inspiration_id);
                $this->cache->set('inspiration.info.getProductInspiration'.$inspiration_id."_".$curr_lang, $results);
            }


            $this->load->model( 'account/wishlist' );

            foreach ($results as $result) {

                $customer_wishlist = $this->model_account_wishlist->getWishlistProductId($result['product_id']);

                if(in_array($result['product_id'], $customer_wishlist)) {
                    $result['is_wishlist'] = true;
                } else {
                    $result['is_wishlist'] = false;
                }

                $filter = array(
                    'product' => $result,
                    'width'   => $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'),
                    'height'  => $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height')
                );

                $data['products'][] = $this->product->getProduct($filter);

            }

            $data['continue'] = $this->url->link('common/home');

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('product/inspiration_info', $data));
        } else {
            $url = '';

            if (isset($this->request->get['inspiration_id'])) {
                $url .= '&inspiration_id=' . $this->request->get['inspiration_id'];
            }

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_error'),
                'href' => $this->url->link('product/inspiration/info', $url)
            );

            $this->document->setTitle($this->language->get('text_error'));

            $data['heading_title'] = $this->language->get('text_error');

            $data['text_error'] = $this->language->get('text_error');

            $data['continue'] = $this->url->link('common/home');

            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

            $data['header'] = $this->load->controller('common/header');
            $data['footer'] = $this->load->controller('common/footer');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');

            $this->response->setOutput($this->load->view('error/not_found', $data));
        }
    }
}
