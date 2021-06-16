<?php
class ControllerProductProduct extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('product/product');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);
        $curr_lang =  $this->language->get('code');
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
        $this->load->model('tool/image');

        if (!$this->customer->isLogged()) {
            $data['logins_wishlist']=true;
        }


      // path manager
      if (isset($this->request->get['product_id']) && ((!isset($this->request->get['path']) && $this->config->get('mlseo_fpp_breadcrumbs') == '1') || ($this->config->get('mlseo_fpp_breadcrumbs') == '2')) && is_array($this->request->get)) {
        unset($this->request->get['path']);
        $this->load->model('tool/path_manager');
        $this->request->get = $this->model_tool_path_manager->getFullProductPath($this->request->get['product_id'], true) + $this->request->get;
      }
      
		if (isset($this->request->get['path'])) {
			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = $path_id;
				} else {
					$path .= '_' . $path_id;
				}


                if($this->cache->get('product.index.category_info_path_id'.$path_id."_".$curr_lang)){
                    $category_info = $this->cache->get('product.index.category_info_path_id'.$path_id."_".$curr_lang);
                }else{
                    $category_info = $this->model_catalog_category->getCategory($path_id);
                    $this->cache->set('product.index.category_info_path_id'.$path_id."_".$curr_lang, $category_info);
                }

				if ($category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],
						'href' => $this->url->link('product/category', 'path=' . $path)
					);
				}
			}

			// Set the last category breadcrumb

            if($this->cache->get('product.index.category_info'.$category_id."_".$curr_lang)){
                $category_info = $this->cache->get('product.index.category_info'.$category_id."_".$curr_lang);
            }else{
                $category_info = $this->model_catalog_category->getCategory($category_id);
                $this->cache->set('product.index.category_info'.$category_id."_".$curr_lang, $category_info);
            }


			if ($category_info) {
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

				if (isset($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}

				$data['breadcrumbs'][] = array(
					'text' => $category_info['name'],
					'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . (!isset($this->request->get['manufacturer_id']) && !isset($this->request->get['search']) && !isset($this->request->get['tag']) ? $url : ''))
				);

				$data['combination_link'] = $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url);

                $data['products'] = array();



                if($this->cache->get('product.index.getProductRelatedCategory'.$this->request->get['product_id'].'_'.$category_id."_".$curr_lang)){
                    $results = $this->cache->get('product.index.getProductRelatedCategory'.$this->request->get['product_id'].'_'.$category_id."_".$curr_lang);
                }else{
                    $results = $this->model_catalog_product->getProductRelatedCategory($this->request->get['product_id'], $category_id);

			/** EET Module */
			$ee_position = 1;
			$data['ee_tracking'] = $this->config->get('module_ee_tracking_status');
			if ($data['ee_tracking']) {
				$data['ee_detail'] = $this->config->get('module_ee_tracking_detail_status');
				$data['ee_detail_log'] = $this->config->get('module_ee_tracking_log') ? $this->config->get('module_ee_tracking_detail_log') : false;
				$data['ee_click'] = $this->config->get('module_ee_tracking_click_status');
				$data['ee_click_log'] = $this->config->get('module_ee_tracking_click_log') ? $this->config->get('module_ee_tracking_click_log') : false;
				$data['ee_cart'] = $this->config->get('module_ee_tracking_cart_status');
				$data['ee_ga_callback'] = $this->config->get('module_ee_tracking_advanced_settings') ? $this->config->get('module_ee_tracking_ga_callback') : 0;
				$data['ee_generate_cid'] = $this->config->get('module_ee_tracking_advanced_settings') ? $this->config->get('module_ee_tracking_generate_cid') : 0;
				$data['ee_type'] = 'related';
				$ee_data = array('type' => $data['ee_type']);
				if ($results) {
					$data['ee_impression'] = $this->config->get('module_ee_tracking_impression_status');
					$data['ee_impression_log'] = $this->config->get('module_ee_tracking_log') ? $this->config->get('module_ee_tracking_impression_log') : false;
					$ee_data['position'] = $ee_position;
					foreach ($results as $item) {
						$ee_data['products'][] = $item['product_id'];
					}
					$data['ee_impression_data'] = json_encode($ee_data);
				} else {
					$data['ee_impression'] = false;
				}
				$data['ee_create_click'] = false;
				if ($data['ee_click'] && $this->config->get('module_ee_tracking_advanced_settings') && $this->config->get('module_ee_tracking_compatibility') && isset($this->request->server['HTTP_REFERER']) && $this->request->server['HTTP_REFERER']) {
					$data['ee_create_click'] = true;
					$data['ee_create_click_data'] = json_encode(array(
						'product_id' => $this->request->get['product_id'],
						'url'    => html_entity_decode($this->request->server['HTTP_REFERER'], ENT_QUOTES, 'UTF-8'),
					));
					if(isset($this->request->server['REQUEST_URI']) && strpos($this->request->server['HTTP_REFERER'], $this->request->server['REQUEST_URI']) !== false) {
						$data['ee_create_click'] = false;
					}
				}
			}
			/** EET Module */
            
                    $this->cache->set('product.index.getProductRelatedCategory'.$this->request->get['product_id'].'_'.$category_id."_".$curr_lang, $results);
                }

                foreach ($results as $result) {
                    $filter = array(
                        'product' => $result,
                        'width'   => $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'),
                        'height'  => $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height')
                    );


                    $data['products'][] = $this->product->getProduct($filter);

                }
            } else {
			    $data['products'] = array();
                $data['combination_link'] = $this->url->link('common/home');
            }
		}

		$this->load->model('catalog/manufacturer');

		if (isset($this->request->get['manufacturer_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_brand'),
				'href' => $this->url->link('product/manufacturer')
			);

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

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

            if($this->cache->get('product.index.manufacturer_info'.$this->request->get['manufacturer_id']."_".$curr_lang)){
                $manufacturer_info = $this->cache->get('product.index.manufacturer_info'.$this->request->get['manufacturer_id']."_".$curr_lang);
            } else{
                $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);
                $this->cache->set('product.index.manufacturer_info'.$this->request->get['manufacturer_id']."_".$curr_lang, $manufacturer_info);
            }

			if ($manufacturer_info) {
				$data['breadcrumbs'][] = array(
					'text' => $manufacturer_info['name'],
					'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url)
				);
			}
		}

		if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {
			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
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
				'text' => $this->language->get('text_search'),
				'href' => $this->url->link('product/search', $url)
			);
		}

		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}

        if($this->cache->get('product.index.product_info'.$product_id."_".$curr_lang)){
            $product_info = $this->cache->get('product.index.product_info'.$product_id."_".$curr_lang);
        } else{
            $product_info = $this->model_catalog_product->getProduct($product_id);
            $this->cache->set('product.index.product_info'.$product_id."_".$curr_lang, $product_info);
        }

		if ($product_info) {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
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
				'text' => $product_info['name'],
				'href' => $this->url->link('product/product', $url . '&product_id=' . $this->request->get['product_id'])
			);

			
      if ($this->config->get('mlseo_enabled')) {
        $this->document->setTitle(!empty($product_info['meta_title']) ? $product_info['meta_title'] : $product_info['name']);
        if (version_compare(VERSION, '2', '>=')) {
          $data['image_alt'] = !empty($product_info['image_alt']) ? $product_info['image_alt'] : '';
          $data['image_title'] = !empty($product_info['image_title']) ? $product_info['image_title'] : '';
        } else {
          $this->data['image_alt'] = !empty($product_info['image_alt']) ? $product_info['image_alt'] : '';
          $this->data['image_title'] = !empty($product_info['image_title']) ? $product_info['image_title'] : '';
        }
      } else {
        $this->document->setTitle($product_info['meta_title']);
      }
      
			$this->document->setDescription($product_info['meta_description']);
			$this->document->setKeywords($product_info['meta_keyword']);
			$this->document->addLink($this->url->link('product/product', 'product_id=' . $this->request->get['product_id']), 'canonical');

			$data['heading_title'] = $product_info['name'];


      if (version_compare(VERSION, '2', '>=')) {
        if ($this->config->get('mlseo_fpp_noprodbreadcrumb')) {
          array_pop($data['breadcrumbs']);
        }
        
        //$data['heading_title'] = $product_info['name'];
        
        $data["heading_title"] = !empty($product_info['seo_h1']) && $this->config->get('mlseo_enabled') ? $product_info['seo_h1'] : $product_info['name'];
        $data['seo_h1'] = !empty($product_info['seo_h1']) ? $product_info['seo_h1'] : '';
        $data['seo_h2'] = !empty($product_info['seo_h2']) ? $product_info['seo_h2'] : '';
        $data['seo_h3'] = !empty($product_info['seo_h3']) ? $product_info['seo_h3'] : '';
      } else {
        if ($this->config->get('mlseo_fpp_noprodbreadcrumb')) {
          array_pop($this->data['breadcrumbs']);
        }
        
        //$this->data['heading_title'] = $product_info['name'];
        
        $this->data["heading_title"] = !empty($product_info['seo_h1']) && $this->config->get('mlseo_enabled') ? $product_info['seo_h1'] : $product_info['name'];
        $this->data['seo_h1'] = !empty($product_info['seo_h1']) ? $product_info['seo_h1'] : '';
        $this->data['seo_h2'] = !empty($product_info['seo_h2']) ? $product_info['seo_h2'] : '';
        $this->data['seo_h3'] = !empty($product_info['seo_h3']) ? $product_info['seo_h3'] : '';
      }
      
      $this->load->model('catalog/review');
      
      $data['seo_reviews'] = '';
      
      if ($this->config->get('mlseo_reviews')) {
        $gkd_seo_reviews = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'], 0, (int)$this->config->get('mlseo_reviews'));
        
        if (count($gkd_seo_reviews)) {
          $data['seo_reviews'] .= '<div class="seo_reviews">';
            foreach ($gkd_seo_reviews as $review) {
              $data['seo_reviews'] .= '<table class="table table-striped table-bordered seo_review">';
              $data['seo_reviews'] .= '<tr>';
              $data['seo_reviews'] .= '  <td style="width: 50%;"><strong>' . $review['author']. '</strong></td>';
              $data['seo_reviews'] .= '  <td class="text-right">' . $review['date_added']. '</td>';
              $data['seo_reviews'] .= '</tr>';
              $data['seo_reviews'] .= '<tr>';
              $data['seo_reviews'] .= '  <td colspan="2"><p>' . $review['text']. '</p>';
              for ($i = 1; $i <= 5; $i++) { 
                if ($review['rating'] < $i) {
                  $data['seo_reviews'] .= '    <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>';
                } else {
                  $data['seo_reviews'] .= '    <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i></span>';
                }
              }
              $data['seo_reviews'] .= '  </td>';
              $data['seo_reviews'] .= '</tr>';
              $data['seo_reviews'] .= '</table>';
            }
          $data['seo_reviews'] .= '</div>';
        }
      }
      
      if (!empty($product_info['meta_robots'])) {
        $this->document->addSeoMeta('<meta name="robots" content="'.$product_info['meta_robots'].'"/>'."\n");
      }
      
      if ($this->config->get('mlseo_header_lm_product')) {
        $array_lm = array(strtotime($product_info['date_modified']));
        
        if (strtotime($product_info['date_available']) < strtotime(date('Y-m-d'))) {
          $array_lm[] = strtotime($product_info['date_available']);
        }
        
        $special_query = $this->db->query("SELECT date_start, date_end FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = '".(int)$product_id."' AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))")->row;
        
        if (!empty($special_query['date_start']) && strtotime($special_query['date_start']) < strtotime(date('Y-m-d'))) {
          $array_lm[] = strtotime($special_query['date_start']);
        }
        
        if (!empty($special_query['date_end']) && strtotime($special_query['date_end']) < strtotime(date('Y-m-d'))) {
          $array_lm[] = strtotime($special_query['date_end']);
        }
        
        $review_query = $this->db->query("SELECT date_modified FROM " . DB_PREFIX . "review WHERE product_id = '" . (int)$product_id . "' AND status = '1' ORDER BY date_modified DESC LIMIT 1")->row;
        
        if (!empty($review_query['date_modified']) && strtotime($review_query['date_modified']) < strtotime(date('Y-m-d'))) {
          $array_lm[] = strtotime($review_query['date_modified']);
        }
        
        $gkd_header_lm_date = max($array_lm);
        
        $this->response->addHeader('Last-Modified: '.date('D, d M Y H:i:s', $gkd_header_lm_date).' GMT');
      }
      
			$data['product_href'] = $this->url->link('product/product', $url . '&product_id=' . $this->request->get['product_id']);

			$data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
			$data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));

			$this->load->model('catalog/review');

			$data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);

			$data['product_id'] = (int)$this->request->get['product_id'];
			$data['manufacturer'] = $product_info['manufacturer'];
			$data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
			$data['model'] = $product_info['model'];
			$data['reward'] = $product_info['reward'];
			$data['points'] = $product_info['points'];
			$data['video'] = $product_info['video'];
			$data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');

            $data['is_wishlist'] = false;

            $this->load->model('account/wishlist');

            $customer_wishlist = $this->model_account_wishlist->getWishlistProductId($this->request->get['product_id']);

            if(in_array($this->request->get['product_id'], $customer_wishlist)) {
                $data['is_wishlist'] = true;
            } else {
                $data['is_wishlist'] = false;
            }

			if ($product_info['quantity'] <= 0) {
				$data['stock'] = $this->language->get('text_outstock');
			} else {
				$data['stock'] = sprintf($this->language->get('text_instock'), $product_info['quantity']);
			}
            $data['quantity'] = $product_info['quantity'];
			$this->load->model('tool/image');


            if ($product_info['image'] && file_exists(DIR_IMAGE.$product_info['image'])) {
				//$data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
				$data['popup'] = '/image/' . $product_info['image'];
			} else {
				$data['popup'] = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
			}

            if ($product_info['image'] && file_exists(DIR_IMAGE.$product_info['image'])) {
				$data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
				//$data['thumb'] = '/image/' . $product_info['image'];
			} else {
				$data['thumb'] = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
			}

            $this->document->setOGImage($data['thumb']);

			$data['images'] = array();
			


            if($this->cache->get('product.index.getProductImages'.$this->request->get['product_id']."_".$curr_lang)){
                $results = $this->cache->get('product.index.getProductImages'.$this->request->get['product_id']."_".$curr_lang);
            }else{
                $results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);
                $this->cache->set('product.index.getProductImages'.$this->request->get['product_id']."_".$curr_lang, $results);
            }


			foreach ($results as $result) {
                if ($result['image'] && file_exists(DIR_IMAGE.$result['image'])&&$result['image']!='catalog/products/') {
                    $data['images'][] = array(
                        'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')),
                        //'popup_big' => $this->model_tool_image->resize($result['image'], 800, 800),
                        'thumb' => $this->model_tool_image->cropsize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height')),
						
						'popup_big' => '/image/' . $result['image'],
						//'popup' => '/image/' . $result['image'],
						//'thumb' => '/image/' . $result['image'],
                    ); 
                }
//                else{
//                   $data['images'][] = array(
//                       'popup' => $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')),
//                       'thumb' => $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'))
//                    );
//                }

			}

            if($this->cache->get('product.index.getProductCollectionId'.$this->request->get['product_id']."_".$curr_lang)){
                $res = $this->cache->get('product.index.getProductCollectionId'.$this->request->get['product_id']."_".$curr_lang);
            }else{
                $res = $this->model_catalog_product->getProductCollectionId($this->request->get['product_id']);
                $this->cache->set('product.index.getProductCollectionId'.$this->request->get['product_id']."_".$curr_lang, $res);
            }



            $data['manufacturer'] = $product_info['manufacturer'];
            $data['manufacturer_href']  = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);

            if(isset($res['collection_name'])){
                $data['collection'] = $res['collection_name'];
                //$data['collection_href']  = $this->url->link('product/manufacturer/infoCollection', 'manufacturer_id=' . $product_info['manufacturer_id'].'&collection_id='.$res['collection_id'].'&collection_item_id='.$res['collection_item_id']);
                $data['collection_href']  = $this->url->link('product/manufacturer/infoCollection', 'manufacturer_id=' . $product_info['manufacturer_id'].'&collection_item_id='.$res['collection_item_id']);
            }
//            echo '<pre >';
//            print_r($data);
//            echo '</pre>';

			$data['no_image'] = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
			$data['no_image_thumb'] = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'));
            if ($this->customer->isLogged() || (!$this->config->get('config_customer_price') && empty($product_info['hide_price']))) {

				$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['price'] = false;
			}

			if ((float)$product_info['special'] && empty($product_info['hide_price'])) {
				$data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['special'] = false;
			}

			if ($this->config->get('config_tax')) {
				$data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
			} else {
				$data['tax'] = false;
			}

			$discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);

			$data['discounts'] = array();

			foreach ($discounts as $discount) {
				$data['discounts'][] = array(
					'quantity' => $discount['quantity'],
					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])
				);
			}

			$data['options'] = array();

            if($this->cache->get('product.index.getProductOptions'.$this->request->get['product_id']."_".$curr_lang)){
                $product_options = $this->cache->get('product.index.getProductOptions'.$this->request->get['product_id']."_".$curr_lang);
            }else{
                $product_options = $this->model_catalog_product->getProductOptions($this->request->get['product_id']);
                $this->cache->set('product.index.getProductOptions'.$this->request->get['product_id']."_".$curr_lang, $product_options);
            }

			foreach ($product_options as $option) {
				$product_option_value_data = array();

				foreach ($option['product_option_value'] as $option_value) {
					if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
						if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
							$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
						} else {
							$price = false;
						}

						$product_option_value_data[] = array(
							'product_option_value_id' => $option_value['product_option_value_id'],
							'option_value_id'         => $option_value['option_value_id'],
							'name'                    => $option_value['name'],
							'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
							'price'                   => $price,
							'price_prefix'            => $option_value['price_prefix']
						);
					}
				}

				$data['options'][] = array(
					'product_option_id'    => $option['product_option_id'],
					'product_option_value' => $product_option_value_data,
					'option_id'            => $option['option_id'],
					'name'                 => $option['name'],
					'type'                 => $option['type'],
					'value'                => $option['value'],
					'required'             => $option['required']
				);
			}

			if ($product_info['minimum']) {
				$data['minimum'] = $product_info['minimum'];
			} else {
				$data['minimum'] = 1;
			}

			$data['review_status'] = $this->config->get('config_review_status');

			if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {
				$data['review_guest'] = true;
			} else {
				$data['review_guest'] = false;
			}

			if ($this->customer->isLogged()) {
				$data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
			} else {
				$data['customer_name'] = '';
			}

			$data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
			$data['rating'] = (int)$product_info['rating'];

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
			} else {
				$data['captcha'] = '';
			}

			$data['share'] = $this->url->link('product/product', 'product_id=' . (int)$this->request->get['product_id']);


            if($this->cache->get('product.index.getProductAttributes'.$this->request->get['product_id']."_".$curr_lang)){
                $data['attribute_groups'] = $this->cache->get('product.index.getProductAttributes'.$this->request->get['product_id']."_".$curr_lang);
            }else{
                $data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);
                $this->cache->set('product.index.getProductAttributes'.$this->request->get['product_id']."_".$curr_lang, $data['attribute_groups']);
            }


			$data['tags'] = array();

			if ($product_info['tag']) {
				$tags = explode(',', $product_info['tag']);

				foreach ($tags as $tag) {
					$data['tags'][] = array(
						'tag'  => trim($tag),
						'href' => $this->url->link('product/search', 'tag=' . trim($tag))
					);
				}
			}



			$data['recurrings'] = $this->model_catalog_product->getProfiles($this->request->get['product_id']);

			$this->model_catalog_product->updateViewed($this->request->get['product_id']);

      if ($this->config->get('mlseo_enabled')) {
        $this->load->model('tool/seo_package');
        
        if ($this->config->get('mlseo_opengraph')) {
          if (version_compare(VERSION, '2', '>=')) {
            $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('opengraph', 'product', $data + array('product_info' => $product_info)));
          } else {
            $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('opengraph', 'product', $this->data + array('product_info' => $product_info)));
          }
        }

        if ($this->config->get('mlseo_tcard')) {
          if (version_compare(VERSION, '2', '>=')) {
            $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('tcard', 'product', $data + array('product_info' => $product_info)));
          } else {
            $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('tcard', 'product', $this->data + array('product_info' => $product_info)));
          }
        }

        if ($this->config->get('mlseo_microdata')) {
          if (version_compare(VERSION, '2', '>=')) {
            $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('microdata', 'product', $data + array('product_info' => $product_info)));
          } else {
            $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('microdata', 'product', $this->data + array('product_info' => $product_info)));
          }
        }
      }
      

			$this->load->model ('design/layout');
			if (isset ( $this->request->get ['route'] )) {
				$route = (string) $this->request->get ['route'];
			} else {
				$route = 'common/home';
			}
			$layout_template = $this->model_design_layout->getLayoutTemplate($route);
			$isLayoutRoute = true;
			if(!$layout_template){
				$layout_template = 'product';
				$isLayoutRoute = false;
			}
			// get general layout template
			if(!$isLayoutRoute){
				$layout_id = $this->model_catalog_product->getProductLayoutId($this->request->get['product_id']);
				if($layout_id){
					$tmp_layout_template = $this->model_design_layout->getGeneralLayoutTemplate($layout_id);
					if($tmp_layout_template)
						$layout_template = $tmp_layout_template;
				}
			}
			
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('product/'.$layout_template, $data));
		} else {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
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
				'href' => $this->url->link('product/product', $url . '&product_id=' . $product_id)
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

	public function review() {

    // SEO Package - redirect non-ajax requests
    if($this->config->get('mlseo_redir_reviews') && !(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
      header('HTTP/1.1 301 Moved Permanently');
      header('CSP-Redir: review', false);
      header('Location: ' . $this->url->link('product/product', 'product_id=' . $this->request->get['product_id']));
    }
      
		$this->load->language('product/product');

		$this->load->model('catalog/review');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['reviews'] = array();

		$review_total = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id']);

		$results = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'], ($page - 1) * 5, 5);

		foreach ($results as $result) {
			$data['reviews'][] = array(
				'author'     => $result['author'],
				'text'       => nl2br($result['text']),
				'rating'     => (int)$result['rating'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$pagination = new Pagination();
		$pagination->total = $review_total;
		$pagination->page = $page;
		$pagination->limit = 5;
		$pagination->url = $this->url->link('product/product/review', 'product_id=' . $this->request->get['product_id'] . '&page={page}');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($review_total - 5)) ? $review_total : ((($page - 1) * 5) + 5), $review_total, ceil($review_total / 5));

		$this->response->setOutput($this->load->view('product/review', $data));
	}

	public function write() {
		$this->load->language('product/product');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
				$json['error'] = $this->language->get('error_name');
			}

			if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
				$json['error'] = $this->language->get('error_text');
			}

			if (empty($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
				$json['error'] = $this->language->get('error_rating');
			}

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

				if ($captcha) {
					$json['error'] = $captcha;
				}
			}

			if (!isset($json['error'])) {
				$this->load->model('catalog/review');

				$this->model_catalog_review->addReview($this->request->get['product_id'], $this->request->post);

				$json['success'] = $this->language->get('text_success');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getRecurringDescription() {
		$this->load->language('product/product');
		$this->load->model('catalog/product');

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		if (isset($this->request->post['recurring_id'])) {
			$recurring_id = $this->request->post['recurring_id'];
		} else {
			$recurring_id = 0;
		}

		if (isset($this->request->post['quantity'])) {
			$quantity = $this->request->post['quantity'];
		} else {
			$quantity = 1;
		}

		$product_info = $this->model_catalog_product->getProduct($product_id);
		
		$recurring_info = $this->model_catalog_product->getProfile($product_id, $recurring_id);

		$json = array();

		if ($product_info && $recurring_info) {
			if (!$json) {
				$frequencies = array(
					'day'        => $this->language->get('text_day'),
					'week'       => $this->language->get('text_week'),
					'semi_month' => $this->language->get('text_semi_month'),
					'month'      => $this->language->get('text_month'),
					'year'       => $this->language->get('text_year'),
				);

				if ($recurring_info['trial_status'] == 1) {
					$price = $this->currency->format($this->tax->calculate($recurring_info['trial_price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$trial_text = sprintf($this->language->get('text_trial_description'), $price, $recurring_info['trial_cycle'], $frequencies[$recurring_info['trial_frequency']], $recurring_info['trial_duration']) . ' ';
				} else {
					$trial_text = '';
				}

				$price = $this->currency->format($this->tax->calculate($recurring_info['price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				if ($recurring_info['duration']) {
					$text = $trial_text . sprintf($this->language->get('text_payment_description'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				} else {
					$text = $trial_text . sprintf($this->language->get('text_payment_cancel'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				}

				$json['success'] = $text;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
