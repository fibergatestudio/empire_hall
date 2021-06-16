<?php
class ControllerProductManufacturer extends Controller {
    public function index() {
        $this->load->language('product/manufacturer');

        $this->load->model('catalog/manufacturer');

        $this->load->model('tool/image');
        $curr_lang =  $this->language->get('code');
        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['logged'] = $this->customer->isLogged();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('product/manufacturer')
        );

        $data['brands'] = array();

        if($this->cache->get('manufacturer.index.getManufacturers'."_".$curr_lang)){
            $results = $this->cache->get('manufacturer.index.getManufacturers'."_".$curr_lang);
        }else{
           $results = $this->model_catalog_manufacturer->getManufacturers();
           $this->cache->set('manufacturer.index.getManufacturers'."_".$curr_lang, $results);
        }

        foreach ($results as $result) {

            if (is_file(DIR_IMAGE . $result['image'])) {
                $image = HTTPS_SERVER . 'image/' . $result['image'];
            } else {
                $image = $this->model_tool_image->resize('placeholder.png', 158, 79);
            }

            $data['brands'][] = array(
                'name'  => $result['name'],
                'sort_order'  => $result['sort_order'],
                'image' => $image,
                'href'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id'])
            );
        }
        usort($data['brands'], function($a, $b){
            return ($a['sort_order'] - $b['sort_order']);
        });
        $data['continue'] = $this->url->link('common/home');

      if ($this->config->get('mlseo_enabled')) {
        $this->load->model('tool/seo_package');
        
        if ($this->config->get('mlseo_microdata')) {
          if (version_compare(VERSION, '2', '>=')) {
            $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('microdata', 'manufacturer', $data));
          } else {
            $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('microdata', 'manufacturer', $this->data));
          }
        }
      }
      

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('product/manufacturer_list', $data));
    }

    public function info() {
        $this->load->language('product/manufacturer');
        $this->load->model('catalog/manufacturer');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        $curr_lang =  $this->language->get('code');
        if (isset($this->request->get['manufacturer_id'])) {
            $manufacturer_id = (int)$this->request->get['manufacturer_id'];
        } else {
            $manufacturer_id = 0;
        }
        $data['logged'] = $this->customer->isLogged();
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_brands'),
            'href' => $this->url->link('product/manufacturer')
        );


        if($this->cache->get('manufacturer.info.getManufacturer'.$manufacturer_id."_".$curr_lang)){
            $manufacturer_info = $this->cache->get('manufacturer.info.getManufacturer'.$manufacturer_id."_".$curr_lang);
        }else{
            $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);
            $this->cache->set('manufacturer.info.getManufacturer'.$manufacturer_id."_".$curr_lang, $manufacturer_info);
        }

        if ($manufacturer_info) {
            
      if ($this->config->get('mlseo_enabled')) {
        $this->document->setTitle((!empty($manufacturer_info['meta_title']) ? $manufacturer_info['meta_title'] : $manufacturer_info['name']));
        
        if (!empty($manufacturer_info['meta_keyword'])) {
          $this->document->setKeywords($manufacturer_info['meta_keyword']);
        }
        
        if (!empty($manufacturer_info['meta_description'])) {
          $this->document->setDescription($manufacturer_info['meta_description']);
        }
      } else {
        $this->document->setTitle($manufacturer_info['name']);
      }
      
            $this->document->setTwitterCard('summary');
            $this->document->setArticleAuthor('https://www.facebook.com/EmpireHall.com.ua/');

            if ($this->config->get('mlseo_enabled')) {
                $this->document->setTwitterTitile((!empty($manufacturer_info['meta_title']) ? $manufacturer_info['meta_title'] : $manufacturer_info['name']));
                if (!empty($manufacturer_info['meta_description'])) {
                    $this->document->setTwitterDescription($manufacturer_info['meta_description']);
                }
            } else {
                $this->document->setTwitterTitile($manufacturer_info['name']);
            }

            $data['breadcrumbs'][] = array(
                'text' => $manufacturer_info['name'],
                'href' => $this->url->link('product/manufacturer')
            );

            $data['heading_title'] = !empty($manufacturer_info['seo_h1']) && $this->config->get('mlseo_enabled') ? $manufacturer_info['seo_h1'] : $manufacturer_info['name'];
        
        if (version_compare(VERSION, '2', '>=')) {
          $data['seo_h1'] = !empty($manufacturer_info['seo_h1']) ? $manufacturer_info['seo_h1'] : '';
          $data['seo_h2'] = !empty($manufacturer_info['seo_h2']) ? $manufacturer_info['seo_h2'] : '';
          $data['seo_h3'] = !empty($manufacturer_info['seo_h3']) ? $manufacturer_info['seo_h3'] : '';
          $data['description'] = !empty($manufacturer_info['description']) ? html_entity_decode($manufacturer_info['description'], ENT_QUOTES, 'UTF-8') : '';
        } else {
          $this->data['seo_h1'] = !empty($manufacturer_info['seo_h1']) ? $manufacturer_info['seo_h1'] : '';
          $this->data['seo_h2'] = !empty($manufacturer_info['seo_h2']) ? $manufacturer_info['seo_h2'] : '';
          $this->data['seo_h3'] = !empty($manufacturer_info['seo_h3']) ? $manufacturer_info['seo_h3'] : '';
          $this->data['description'] = !empty($manufacturer_info['description']) ? html_entity_decode($manufacturer_info['description'], ENT_QUOTES, 'UTF-8') : '';
        }
      
            $data['description'] = html_entity_decode($manufacturer_info['description']);

            $data['collections_title'] = sprintf($this->language->get('text_coll_title'), $manufacturer_info['name']);
            $data['products_title'] = sprintf($this->language->get('text_prod_title'), $manufacturer_info['name']);

            if (is_file(DIR_IMAGE . $manufacturer_info['image3'])) {
                $data['image_bg'] = $this->model_tool_image->resize($manufacturer_info['image3'], 1400, 680);
            } else {
                $data['image_bg'] = '';
            }

            if (is_file(DIR_IMAGE . $manufacturer_info['image'])) {
                $data['image'] = $this->model_tool_image->resize($manufacturer_info['image'], 200, 140);
                $data['ogimage'] = $this->model_tool_image->resize($manufacturer_info['image'], 200, 200);
                $this->document->setOGImage($data['ogimage']);
                $this->document->setTwitterImage($data['ogimage']);
            } else {
                $data['image'] = $this->model_tool_image->resize('placeholder.png', 200, 140);
            }

            $data['categories'] = array();

          /*  if($this->cache->get('manufacturer.collections_category'.$manufacturer_id)){
                $collections_category = $this->cache->get('manufacturer.collections_category'.$manufacturer_id);
            }else{
                $collections_category = $this->model_catalog_manufacturer->getCollectionsCategory($manufacturer_id);
                $this->cache->set('manufacturer.collections_category'.$manufacturer_id, $collections_category);
            }*/

            $collections_category = $this->model_catalog_manufacturer->getCollectionsCategory($manufacturer_id);


            foreach ($collections_category as $category) {
                $collections = array();

                if($this->cache->get('manufacturer.info.get_collections'.$category['collection_id'].'_'.$manufacturer_id."_".$curr_lang)){
                    $get_collections = $this->cache->get('manufacturer.info.get_collections'.$category['collection_id'].'_'.$manufacturer_id."_".$curr_lang);
                }else{
                    $get_collections = $this->model_catalog_manufacturer->getCollectionsItems($category['collection_id'], $manufacturer_id);
                    $this->cache->set('manufacturer.info.get_collections'.$category['collection_id'].'_'.$manufacturer_id."_".$curr_lang, $get_collections);
                }
               // $get_collections = $this->model_catalog_manufacturer->getCollectionsItems($category['collection_id'], $manufacturer_id);
                if ($get_collections) {
                    $url = '';

                    foreach ($get_collections as $collection) {
                        if (is_file(DIR_IMAGE . $collection['image'])) {
                            $image = $this->model_tool_image->resize($collection['image'], 430, 524);
                        } else {
                            $image = $this->model_tool_image->resize('placeholder.png', 430, 524);
                        }

                        //   $url = '&collection_id=' . $category['collection_id'] . '&brand_id=' . $category['manufacturer_id'];
//                        $url = 'manufacturer_id=' . $category['manufacturer_id'].'&collection_id=' . $category['collection_id'].'&collection_item_id=' . $collection['collection_item_id'];
                        $url = 'manufacturer_id=' . $category['manufacturer_id'] . '&collection_item_id=' . $collection['collection_item_id'];
                        $collections[] = array(
                            'name' => $collection['name'],
                            'image' => $image,
                            'href' => $this->url->link('product/manufacturer/infoCollection', $url)
                        );
                    }
                }

                $data['categories'][] = array(
                    'id'          => $category['collection_id'],
                    'collections' => $collections,
                    'name'        => $category['name']
                );
            }


            $data['products'] = array();

            $filter_data = array(
                'filter_manufacturer_id' => $manufacturer_id
            );

            if($this->cache->get('manufacturer.info.products'.$manufacturer_id."_".$curr_lang)){
                $results = $this->cache->get('manufacturer.info.products'.$manufacturer_id."_".$curr_lang);
            }else{
                $results = $this->model_catalog_product->getProducts($filter_data);

			/** EET Module */
			if (isset($page) && isset($limit)) {
				$ee_position = ($page - 1) * $limit + 1;
			} else {
				$ee_position = 1;
			}
			$data['ee_tracking'] = $this->config->get('module_ee_tracking_status');
			if ($data['ee_tracking'] && $results) {
				$data['ee_impression'] = $this->config->get('module_ee_tracking_impression_status');
				$data['ee_impression_log'] = $this->config->get('module_ee_tracking_log') ? $this->config->get('module_ee_tracking_impression_log') : false;
				$data['ee_click'] = $this->config->get('module_ee_tracking_click_status');
				$data['ee_cart'] = $this->config->get('module_ee_tracking_cart_status');
				$data['ee_ga_callback'] = $this->config->get('module_ee_tracking_advanced_settings') ? $this->config->get('module_ee_tracking_ga_callback') : 0;
				$data['ee_generate_cid'] = $this->config->get('module_ee_tracking_advanced_settings') ? $this->config->get('module_ee_tracking_generate_cid') : 0;
				$ee_class_array = preg_split('/(?=[A-Z])/', get_class($this));
				$data['ee_type'] = strtolower(array_pop($ee_class_array));
				$ee_data = array('type' => $data['ee_type']);
				$ee_data['position'] = $ee_position;
				foreach ($results as $item) {
					$ee_data['products'][] = $item['product_id'];
				}
				$data['ee_impression_data'] = json_encode($ee_data);
			}
			/** EET Module */
            
                $this->cache->set('manufacturer.info.products'.$manufacturer_id."_".$curr_lang, $results);
            }

            $this->load->model( 'account/wishlist' );
            $this->load->language('product/product');
            $data['all_prod_manufacture'] = str_replace(HTTPS_SERVER, $this->url->link('product/category', 'path=1').'/', $this->url->link('product/manufacturer/info','manufacturer_id='.$manufacturer_id,$url ));
            // print_r(HTTPS_SERVER);

$i=0;
            foreach ($results as $result) {
if($i>10){break;}else{
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
$i++;
            }

            $data['continue'] = $this->url->link('common/home');

      if ($this->config->get('mlseo_enabled')) {
        $this->load->model('tool/seo_package');
        
        if ($this->config->get('mlseo_microdata')) {
          if (version_compare(VERSION, '2', '>=')) {
            $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('microdata', 'manufacturer', $data));
          } else {
            $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('microdata', 'manufacturer', $this->data));
          }
        }
      }
      

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('product/manufacturer_info', $data));
        }
        else {
            $url = '';

            if (isset($this->request->get['manufacturer_id'])) {
                $url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
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
                'href' => $this->url->link('product/manufacturer/info', $url)
            );

            $this->document->setTitle($this->language->get('text_error'));

            $data['heading_title'] = $this->language->get('text_error');

            $data['text_error'] = $this->language->get('text_error');

            $data['continue'] = $this->url->link('common/home');

      if ($this->config->get('mlseo_enabled')) {
        $this->load->model('tool/seo_package');
        
        if ($this->config->get('mlseo_microdata')) {
          if (version_compare(VERSION, '2', '>=')) {
            $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('microdata', 'manufacturer', $data));
          } else {
            $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('microdata', 'manufacturer', $this->data));
          }
        }
      }
      

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

    public function infoCollection() {


        $this->load->language('product/category');

        $this->load->model('catalog/collection');

        $this->load->model('catalog/product');

        $this->load->model('tool/image');
        $curr_lang =  $this->language->get('code');
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

        if (isset($this->request->get['manufacturer_id'])) {
            $brand_id = $this->request->get['manufacturer_id'];
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
        $data['breadcrumbs_json'] = array();


        $data['breadcrumbs'][] = array(
            'text'  => $this->language->get('text_home'),
            'check' => 'no',
            'href'  => $this->url->link('common/home')
        );
        $data['breadcrumbs_json'][] = array(
            'text'  => $this->language->get('text_home'),
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
                        'href'  => $this->url->link('product/manufacturer/infoCollection', 'path=' . $path . $url)
                    );
                    $data['breadcrumbs_json'][] = array(
                        'text'  => $category_info['name'],
                        'href'  => $this->url->link('product/manufacturer/infoCollection', 'path=' . $path . $url)
                    );
                }
            }
        } else {
            $category_id = 0;
        }



        if($this->cache->get('manufacturer.infoCollection.category_info'.$category_id."_".$curr_lang)){
            $category_info = $this->cache->get('manufacturer.infoCollection.category_info'.$category_id."_".$curr_lang);
        }else{
            $category_info = $this->model_catalog_collection->getCollectionCategory($category_id);
            $this->cache->set('manufacturer.infoCollection.category_info'.$category_id."_".$curr_lang, $category_info);
        }


        if($this->cache->get('manufacturer.infoCollection.collection_info'.$collection_item_id.'_'.$category_id.'_'.$brand_id."_".$curr_lang)){
            $collection_info = $this->cache->get('manufacturer.infoCollection.collection_info'.$collection_item_id.'_'.$category_id.'_'.$brand_id."_".$curr_lang);
        }else{
            $collection_info = $this->model_catalog_collection->getCollectionsItem($collection_item_id, $category_id, $brand_id);
            $this->cache->set('manufacturer.infoCollection.collection_info'.$collection_item_id.'_'.$category_id.'_'.$brand_id."_".$curr_lang, $collection_info);
        }



//        echo '<pre style="display:none;">';
//        print_r($category_info);
//        echo '</pre>';

        if ($category_info && $collection_info) {
            if (isset($brand_id)) {
                $this->load->model('catalog/manufacturer');
                $brand = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);

                $data['breadcrumbs'][] = array(
                    'text'  => $brand['name'],
                    'check' => 'no',
                    'href'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $brand['manufacturer_id'])
                );
                $data['breadcrumbs_json'][] = array(
                    'text'  => $brand['name'],
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
            $data['breadcrumbs_json'][] = array(
                'text'  => $collection_info['name'],
                'href'  => $this->url->link('product/collection')
            );

            $SEO_title = $collection_info['name'].' '.$brand['name'].$this->language->get('text_seo_title_collection');
            $SEO_description = $brand['name'].' '.$collection_info['name'].' '.$this->language->get('text_seo_descr_collection');

            $this->document->setTitle($SEO_title);
            $this->document->setDescription($SEO_description);
            $this->document->setKeywords($category_info['meta_keyword']);

            $data['heading_title'] = $brand['name'].' '.$collection_info['name'];

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

            if($this->cache->get('manufacturer.infoCollection.getProductCollections'.$collection_item_id.'_'.$filter_data['sort'].'_'.$filter_data['order'].'_'.$filter_data['start'].'_'.$filter_data['limit']."_".$curr_lang)){
                 $results = $this->cache->get('manufacturer.infoCollection.getProductCollections'.$collection_item_id.'_'.$filter_data['sort'].'_'.$filter_data['order'].'_'.$filter_data['start'].'_'.$filter_data['limit']."_".$curr_lang);
            }else{
                 $results = $this->model_catalog_product->getProductCollections($collection_item_id, $filter_data);

			/** EET Module */
			if (isset($page) && isset($limit)) {
				$ee_position = ($page - 1) * $limit + 1;
			} else {
				$ee_position = 1;
			}
			$data['ee_tracking'] = $this->config->get('module_ee_tracking_status');
			if ($data['ee_tracking'] && $results) {
				$data['ee_impression'] = $this->config->get('module_ee_tracking_impression_status');
				$data['ee_impression_log'] = $this->config->get('module_ee_tracking_log') ? $this->config->get('module_ee_tracking_impression_log') : false;
				$data['ee_click'] = $this->config->get('module_ee_tracking_click_status');
				$data['ee_cart'] = $this->config->get('module_ee_tracking_cart_status');
				$data['ee_ga_callback'] = $this->config->get('module_ee_tracking_advanced_settings') ? $this->config->get('module_ee_tracking_ga_callback') : 0;
				$data['ee_generate_cid'] = $this->config->get('module_ee_tracking_advanced_settings') ? $this->config->get('module_ee_tracking_generate_cid') : 0;
				$ee_class_array = preg_split('/(?=[A-Z])/', get_class($this));
				$data['ee_type'] = strtolower(array_pop($ee_class_array));
				$ee_data = array('type' => $data['ee_type']);
				$ee_data['position'] = $ee_position;
				foreach ($results as $item) {
					$ee_data['products'][] = $item['product_id'];
				}
				$data['ee_impression_data'] = json_encode($ee_data);
			}
			/** EET Module */
            
                 $this->cache->set('manufacturer.infoCollection.getProductCollections'.$collection_item_id.'_'.$filter_data['sort'].'_'.$filter_data['order'].'_'.$filter_data['start'].'_'.$filter_data['limit']."_".$curr_lang, $results);
            }

            $this->load->model( 'account/wishlist' );
            foreach ($results as $result) {
                $customer_wishlist = $this->model_account_wishlist->getWishlistProductId($result['product_id']);

                if (in_array($result['product_id'], $customer_wishlist)) {
                    $result['is_wishlist'] = true;
                } else {
                    $result['is_wishlist'] = false;
                }

                $filter = array(
                    'product' => $result,
                    'width' => $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'),
                    'height' => $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height')
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

//            $data['sorts'][] = array(
//                'text'  => $this->language->get('text_default'),
//                'value' => 'p.viewed-DESC',
//                'href'  => $this->url->link('product/collection', 'collection_item_id=' . $this->request->get['collection_item_id'] . '&collection_id=' . $this->request->get['collection_id'] . '&brand_id=' . $this->request->get['brand_id'] . '&sort=p.viewed&order=DESC' . $url)
//            );
//
//            $data['sorts'][] = array(
//                'text'  => $this->language->get('text_price_desc'),
//                'value' => 'p.price-DESC',
//                'href'  => $this->url->link('product/collection', 'collection_item_id=' . $this->request->get['collection_item_id'] . '&collection_id=' . $this->request->get['collection_id'] . '&brand_id=' . $this->request->get['brand_id'] . '&sort=p.price&order=DESC' . $url)
//            );
//
//            $data['sorts'][] = array(
//                'text'  => $this->language->get('text_price_asc'),
//                'value' => 'p.price-ASC',
//                'href'  => $this->url->link('product/collection', 'collection_item_id=' . $this->request->get['collection_item_id'] . '&collection_id=' . $this->request->get['collection_id'] . '&brand_id=' . $this->request->get['brand_id'] . '&sort=p.price&order=ASC' . $url)
//            );
//
//            $data['sorts'][] = array(
//                'text'  => $this->language->get('text_name_desc'),
//                'value' => 'pd.name-DESC',
//                'href'  => $this->url->link('product/collection', 'collection_item_id=' . $this->request->get['collection_item_id'] . '&collection_id=' . $this->request->get['collection_id'] . '&brand_id=' . $this->request->get['brand_id'] . '&sort=pd.name&order=DESC' . $url)
//            );
//
//            $data['sorts'][] = array(
//                'text'  => $this->language->get('text_name_asc'),
//                'value' => 'pd.name-ASC',
//                'href'  => $this->url->link('product/collection', 'collection_item_id=' . $this->request->get['collection_item_id'] . '&collection_id=' . $this->request->get['collection_id'] . '&brand_id=' . $this->request->get['brand_id'] . '&sort=pd.name&order=ASC' . $url)
//            );

            $url = '';
            if (isset($this->request->get['manufacturer_id'])) {
                $url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
            }
            if (isset($this->request->get['collection_id'])) {
//                $url .= '&collection_id=' . $this->request->get['collection_id'];
            }
            if (isset($this->request->get['collection_item_id'])) {
                $url .= '&collection_item_id=' . $this->request->get['collection_item_id'];
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
            if (isset($this->request->get['manufacturer_id'])) {
                $url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
            }
//            if (isset($this->request->get['collection_id'])) {
//                $url .= '&collection_id=' . $this->request->get['collection_id'];
//            }
            if (isset($this->request->get['collection_item_id'])) {
                $url .= '&collection_item_id=' . $this->request->get['collection_item_id'];
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
            $pagination->url = $this->url->link('product/manufacturer/infoCollection' . $url . '&page={page}');

            $data['pagination'] = $pagination->render();

            $data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

            // http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html

      if ($page > 1 AND $this->config->get('mlseo_pagination_canonical')) {
         $this->document->addLink($this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id']), 'canonical');
      }
      
            if ($page == 1) {
                $this->document->addLink($this->url->link('product/manufacturer/infoCollection', $url), 'canonical');
            } else {
                $this->document->addLink($this->url->link('product/manufacturer/infoCollection', $url), 'canonical');
            }

            if ($page > 1) {
                $this->document->addLink($this->url->link('product/manufacturer/infoCollection', (($page - 2) ? '&page='. ($page - 1) : '')), 'prev');
            }

            if ($limit && ceil($product_total / $limit) > $page) {
                $this->document->addLink($this->url->link('product/manufacturer/infoCollection', '&page='. ($page + 1)), 'next');
            }

            $data['sort'] = $sort;
            $data['order'] = $order;
            $data['limit'] = $limit;

            $data['continue'] = $this->url->link('common/home');

      if ($this->config->get('mlseo_enabled')) {
        $this->load->model('tool/seo_package');
        
        if ($this->config->get('mlseo_microdata')) {
          if (version_compare(VERSION, '2', '>=')) {
            $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('microdata', 'manufacturer', $data));
          } else {
            $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('microdata', 'manufacturer', $this->data));
          }
        }
      }
      

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

      if ($this->config->get('mlseo_enabled')) {
        $this->load->model('tool/seo_package');
        
        if ($this->config->get('mlseo_microdata')) {
          if (version_compare(VERSION, '2', '>=')) {
            $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('microdata', 'manufacturer', $data));
          } else {
            $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('microdata', 'manufacturer', $this->data));
          }
        }
      }
      




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
