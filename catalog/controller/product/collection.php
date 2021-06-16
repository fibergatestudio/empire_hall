<?php
class ControllerProductCollection extends Controller {
    public function index() {
        $this->load->language('product/category');

        $this->load->model('catalog/collection');

        $this->load->model('catalog/product');

        $this->load->model('tool/image');

        $data['logged'] = $this->customer->isLogged();

        if (isset($this->request->get['collection_item_id'])) {
            $collection_item_id = $this->request->get['collection_item_id'];
        } else {
            $collection_item_id = '';
        }

        if (isset($this->request->get['collection_id'])) {
            $collection_id = $this->request->get['collection_id'];
        } else {
            $collection_id = '';
        }

        if (isset($this->request->get['brand_id'])) {
            $brand_id = $this->request->get['brand_id'];
        } else {
            $brand_id = '';
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'p.viewed';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        if (isset($this->request->get['limit'])) {
            $limit = (int)$this->request->get['limit'];
        } else {
            $limit = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'  => $this->language->get('text_home'),
            'check' => 'no',
            'href'  => $this->url->link('common/home')
        );

        if (isset($collection_id)) {
            $url = '';
            $path = '';

            $parts = explode('_', (string)$this->request->get['collection_id']);

            $category_id = (int)array_pop($parts);

            foreach ($parts as $path_id) {
                if (!$path) {
                    $path = (int)$path_id;
                } else {
                    $path .= '_' . (int)$path_id;
                }

                $category_info = $this->model_catalog_collection->getCollectionCategory($path_id);

                if ($category_info) {
                    $data['breadcrumbs'][] = array(
                        'text'  => $category_info['name'],
                        'check' => 'no',
                        'href'  => $this->url->link('product/collection', 'path=' . $path . $url)
                    );
                }
            }
        } else {
            $category_id = 0;
        }


        $category_info = $this->model_catalog_collection->getCollectionCategory($category_id);
        $collection_info = $this->model_catalog_collection->getCollectionsItem($collection_item_id, $category_id, $brand_id);


        if ($category_info && $collection_info) {
            if (isset($brand_id)) {
                $this->load->model('catalog/manufacturer');
                $brand = $this->model_catalog_manufacturer->getManufacturer($this->request->get['brand_id']);

                $data['breadcrumbs'][] = array(
                    'text'  => $brand['name'],
                    'check' => 'no',
                    'href'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $brand['manufacturer_id'])
                );
            }

            $data['breadcrumbs'][] = array(
                'text'  => $category_info['name'],
                'check' => 'yes',
                'href'  => $this->url->link('product/collection', 'collection_item_id=' . $this->request->get['collection_item_id'])
            );

            $data['breadcrumbs'][] = array(
                'text'  => $collection_info['name'],
                'check' => 'no',
                'href'  => $this->url->link('product/collection')
            );

            $this->document->setTitle($collection_info['name']);
            $this->document->setDescription($category_info['meta_description']);
            $this->document->setKeywords($category_info['meta_keyword']);

            $data['heading_title'] = $collection_info['name'];

            $data['products'] = array();

            $filter_data = array(
                'sort'               => $sort,
                'order'              => $order,
                'start'              => ($page - 1) * $limit,
                'limit'              => $limit
            );

            $product_total = $this->model_catalog_product->getTotalProductCollections($collection_item_id);

            if ($product_total == 1) {
                $data['count_products'] = sprintf($this->language->get('text_one_product'), $product_total);
            } else if ($product_total > 1 && $product_total < 5) {
                $data['count_products'] = sprintf($this->language->get('text_two_product'), $product_total);
            } else {
                $data['count_products'] = sprintf($this->language->get('text_five_product'), $product_total);
            }

            $results = $this->model_catalog_product->getProductCollections($collection_item_id, $filter_data);

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

            $url = '';

            if (isset($this->request->get['filter'])) {
                $url .= '&filter=' . $this->request->get['filter'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $data['sorts'] = array();

            $data['sorts'][] = array(
                'text'  => $this->language->get('text_default'),
                'value' => 'p.viewed-DESC',
                'href'  => $this->url->link('product/collection', 'collection_item_id=' . $this->request->get['collection_item_id'] . '&collection_id=' . $this->request->get['collection_id'] . '&brand_id=' . $this->request->get['brand_id'] . '&sort=p.viewed&order=DESC' . $url)
            );

            $data['sorts'][] = array(
                'text'  => $this->language->get('text_price_desc'),
                'value' => 'p.price-DESC',
                'href'  => $this->url->link('product/collection', 'collection_item_id=' . $this->request->get['collection_item_id'] . '&collection_id=' . $this->request->get['collection_id'] . '&brand_id=' . $this->request->get['brand_id'] . '&sort=p.price&order=DESC' . $url)
            );

            $data['sorts'][] = array(
                'text'  => $this->language->get('text_price_asc'),
                'value' => 'p.price-ASC',
                'href'  => $this->url->link('product/collection', 'collection_item_id=' . $this->request->get['collection_item_id'] . '&collection_id=' . $this->request->get['collection_id'] . '&brand_id=' . $this->request->get['brand_id'] . '&sort=p.price&order=ASC' . $url)
            );

            $data['sorts'][] = array(
                'text'  => $this->language->get('text_name_desc'),
                'value' => 'pd.name-DESC',
                'href'  => $this->url->link('product/collection', 'collection_item_id=' . $this->request->get['collection_item_id'] . '&collection_id=' . $this->request->get['collection_id'] . '&brand_id=' . $this->request->get['brand_id'] . '&sort=pd.name&order=DESC' . $url)
            );

            $data['sorts'][] = array(
                'text'  => $this->language->get('text_name_asc'),
                'value' => 'pd.name-ASC',
                'href'  => $this->url->link('product/collection', 'collection_item_id=' . $this->request->get['collection_item_id'] . '&collection_id=' . $this->request->get['collection_id'] . '&brand_id=' . $this->request->get['brand_id'] . '&sort=pd.name&order=ASC' . $url)
            );

            $url = '';

            if (isset($this->request->get['collection_item_id'])) {
                $url .= '&collection_item_id=' . $this->request->get['collection_item_id'];
            }

            if (isset($this->request->get['collection_id'])) {
                $url .= '&collection_id=' . $this->request->get['collection_id'];
            }

            if (isset($this->request->get['brand_id'])) {
                $url .= '&brand_id=' . $this->request->get['brand_id'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            $data['limits'] = array();

            $limits = array_unique(array($this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit'), 60,90));

            sort($limits);

            $url = '';

            if (isset($this->request->get['collection_item_id'])) {
                $url .= '&collection_item_id=' . $this->request->get['collection_item_id'];
            }

            if (isset($this->request->get['collection_id'])) {
                $url .= '&collection_id=' . $this->request->get['collection_id'];
            }

            if (isset($this->request->get['brand_id'])) {
                $url .= '&brand_id=' . $this->request->get['brand_id'];
            }
            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $pagination = new PaginationFront();
            $pagination->total = $product_total;
            $pagination->page = $page;
            $pagination->limit = $limit;
            $pagination->prev_txt = $this->language->get('text_pagination_prev');
            $pagination->last_txt = $this->language->get('text_pagination_last');
            $pagination->url = $this->url->link('product/collection' . $url . '&page={page}');

            $data['pagination'] = $pagination->render();

            $data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

            // http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
            if ($page == 1) {
                $this->document->addLink($this->url->link('product/collection' . $url), 'canonical');
            } else {
                $this->document->addLink($this->url->link('product/collection' . $url), 'canonical');
            }

            if ($page > 1) {
                $this->document->addLink($this->url->link('product/collection', (($page - 2) ? '&page='. ($page - 1) : '')), 'prev');
            }

            if ($limit && ceil($product_total / $limit) > $page) {
                $this->document->addLink($this->url->link('product/collection', '&page='. ($page + 1)), 'next');
            }

            $data['sort'] = $sort;
            $data['order'] = $order;
            $data['limit'] = $limit;

            $data['continue'] = $this->url->link('common/home');

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('product/collection', $data));
        } else {
            $url = '';

            if (isset($this->request->get['path'])) {
                $url .= '&path=' . $this->request->get['path'];
            }

            if (isset($this->request->get['filter'])) {
                $url .= '&filter=' . $this->request->get['filter'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_error'),
                'href' => $this->url->link('product/category', $url)
            );

            $this->document->setTitle($this->language->get('text_error'));

            $data['continue'] = $this->url->link('common/home');




            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('error/not_found', $data));
        }
    }
}
