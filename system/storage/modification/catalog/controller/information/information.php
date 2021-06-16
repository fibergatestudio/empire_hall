<?php
class ControllerInformationInformation extends Controller {
	public function index() {
		$this->load->language('information/information');
		$this->load->model('catalog/information');
		$this->load->model('tool/image');
		$data['breadcrumbs'] = array();
		$data['gallerys'] = array();
		$data['action_form'] = $this->url->link('information/contact/sendMessage');
        $curr_lang =  $this->language->get('code');
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		if (isset($this->request->get['information_id'])) {
			$information_id = (int)$this->request->get['information_id'];
		} else {
			$information_id = 0;
		}



        if($this->cache->get('information.index.getInformation'.$information_id."_".$curr_lang)){
            $information_info = $this->cache->get('information.index.getInformation'.$information_id."_".$curr_lang);
        }else{
            $information_info = $this->model_catalog_information->getInformation($information_id);
            $this->cache->set('information.index.getInformation'.$information_id."_".$curr_lang, $information_info);
        }


		if ($information_info) {
			$this->document->setTitle(!empty($information_info['meta_title']) && $this->config->get('mlseo_enabled') ? $information_info['meta_title'] : $information_info['title']);
			$this->document->setDescription($information_info['meta_description']);
			$this->document->setKeywords($information_info['meta_keyword']);

			$data['breadcrumbs'][] = array(
				'text' => $information_info['title'],
				'href' => $this->url->link('information/information', 'information_id=' .  $information_id)
			);
			$data['breadcrumbs_total'] = count($data['breadcrumbs']);

			$data['heading_title'] = !empty($information_info['seo_h1']) && $this->config->get('mlseo_enabled') ? $information_info['seo_h1'] : $information_info['title'];
        
        if (version_compare(VERSION, '2', '>=')) {
          $data['seo_h1'] = !empty($information_info['seo_h1']) ? $information_info['seo_h1'] : '';
          $data['seo_h2'] = !empty($information_info['seo_h2']) ? $information_info['seo_h2'] : '';
          $data['seo_h3'] = !empty($information_info['seo_h3']) ? $information_info['seo_h3'] : '';
        } else {
          $this->data['seo_h1'] = !empty($information_info['seo_h1']) ? $information_info['seo_h1'] : '';
          $this->data['seo_h2'] = !empty($information_info['seo_h2']) ? $information_info['seo_h2'] : '';
          $this->data['seo_h3'] = !empty($information_info['seo_h3']) ? $information_info['seo_h3'] : '';
        }
        
        $this->load->model('tool/seo_package');
  
        if ($this->config->get('mlseo_opengraph')) {
          if (version_compare(VERSION, '2', '>=')) {
            $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('opengraph', 'info', $data));
          } else {
            $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('opengraph', 'info', $this->data));
          }
          
          if ($this->config->get('mlseo_microdata')) {
            if (version_compare(VERSION, '2', '>=')) {
              $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('microdata', 'information', $data));
            } else {
              $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('microdata', 'information', $this->data));
            }
          }
        }
        
        if (!empty($information_info['meta_robots'])) {
          $this->document->addSeoMeta('<meta name="robots" content="'.$information_info['meta_robots'].'"/>'."\n");
        }
      
			
			$data['h1'] = $information_info['h1'];

			$data['description'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');

			$data['store_name'] = $this->config->get('config_name');
			$data['store_subtitle'] = $this->config->get('config_owner');
			$data['about_text'] = $this->config->get('config_about_text')[$this->config->get('config_language_id')];

			$data['continue'] = $this->url->link('common/home');

			$this->load->model ('design/layout');
			if (isset ( $this->request->get ['route'] )) {
				$route = ( string ) $this->request->get ['route'];
			} else {
				$route = 'common/home';
			}
			$layout_template = $this->model_design_layout->getLayoutTemplate($route);
			$isLayoutRoute = true;
			if(!$layout_template){
				$layout_template = 'information';
				$isLayoutRoute = false;
			}
			// get general layout template
			if(!$isLayoutRoute){
				$layout_id = $this->model_catalog_information->getInformationLayoutId($information_id);
				if($layout_id){
					$tmp_layout_template = $this->model_design_layout->getGeneralLayoutTemplate($layout_id);
					if($tmp_layout_template)
						$layout_template = $tmp_layout_template;
				}
			}

            if($this->cache->get('information.index.getInformationGallery'.$information_id."_".$curr_lang)){
                $gallerys = $this->cache->get('information.index.getInformationGallery'.$information_id."_".$curr_lang);
            }else{
                $gallerys = $this->model_catalog_information->getInformationGallery($information_id);
                $this->cache->set('information.index.getInformationGallery'.$information_id."_".$curr_lang, $gallerys);
            }

			if($gallerys){
				foreach($gallerys as $result) {
					if (isset($result['image']) && is_file(DIR_IMAGE . $result['image'])) {
						$thumb = $this->model_tool_image->resize($result['image'], 266, 399);
						$popup = HTTP_SERVER . 'image/' . $result['image'];
					} else {
						$thumb = $this->model_tool_image->resize("placeholder.png", 266, 399);
						$popup = $this->model_tool_image->resize("placeholder.png", 534, 798);
					}
					$data['gallerys'][] = array(
						'title' => $result['title'][$this->config->get('config_language_id')],
						'thumb' => $thumb,
						'popup' => $popup,
					);
				}
			}			

			if ($information_info['image']) {
			    if ($information_id == 4) {
                   // $data['image'] = $this->model_tool_image->resize($information_info['image'], 594, 715);
                   $data['image'] = HTTP_SERVER . 'image/' . $information_info['image'];
                } else {
                    $data['image'] = $this->model_tool_image->resize($information_info['image'], 594, 715);
                }
			} else {
				$data['image'] = $this->model_tool_image->resize("placeholder.png", 550, 330);
			}

			if ($information_id == 4) {
                $this->load->model('catalog/manufacturer');

                $data['brands'] = array();

                $manufacturers = array();
                if($this->cache->get('information.index.getManufacturers'."_".$curr_lang)){
                    $manufacturers = $this->cache->get('information.index.getManufacturers'."_".$curr_lang);
                }else{
                    $this->model_catalog_manufacturer->getManufacturers();
                    $this->cache->set('information.index.getManufacturers'."_".$curr_lang, $manufacturers);
                }

                foreach ($manufacturers as $result) {
                    if (is_file(DIR_IMAGE . $result['image'])) {
                        $data['brands'][] = array(
                            'name'   => $result['name'],
                            'image'  => HTTPS_SERVER . 'image/' . $result['image']
                        );
                    }
                }
            }
			
			/* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! */
			/* ------ KCF ------ KCF ------ KCF ------ KCF ------ KCF ------ KCF ------ KCF ------ KCF ------- */
			/* выбор набора полей */
			$this->load->model('catalog/kcf');
			$kcf_type = 3; //значит информационка
			$data['kcf_feelds_value'] = array();
			$kcf_feelds = $this->model_catalog_kcf->getFeelds($kcf_type, $information_id);
			foreach ($kcf_feelds as $val) {
				switch ($val['type']) {
					case 1:
						$type = 'text';
						break;
					case 2:
						$type = 'textarea';
						break;
					case 3:
						$type = 'image';
						break;				
					default:
						$type = false;
						break;
				}
				if($type){
					if($type == 'image'){
						if($val['kcfv_value']){
							$value =  HTTP_SERVER . 'image/' . $val['kcfv_value'];
						} else {
							$value = $val['kcfv_value'];
						}
					} else {
						$value = html_entity_decode($val['kcfv_value'], ENT_QUOTES, 'UTF-8');
					}
					$data['kcf_feelds_value'][$val['kcffeeld_id']] = array(
						'type' => $type,
						'value' => $value
					);
				}
			}
			/* ------ END KCF ------ END KCF ------ END KCF ------ END KCF ------ END KCF ------ END KCF ------ */
			/* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! */			
			
			/* ----------------------- Для Карты ----------------------- */
			$data['map_points'] = array();
			if(isset($information_id) && $information_id == 8){
				$this->load->model('catalog/map');
				$data['map_points'] = $this->model_catalog_map->getMapPoints();
				$data['stocks'] = array();
				foreach ($data['map_points'] as $valuem) {
					$data['stocks'][$valuem['sscity_id']][] = array(
						'sstock_id' => $valuem['sstock_id'],
						'city' => $valuem['city'],
						'point_x' => $valuem['point_x'],
						'point_y' => $valuem['point_y'],
						'phone1' => $valuem['phone1'],
						'phone2' => $valuem['phone2'],
						'shipping' => $valuem['shipping'],
						'link1' => $valuem['link1'],
						'link2' => $valuem['link2'],
						'store_name' => $valuem['store_name'],
						'store_addr' => $valuem['store_addr']
					);
				}
			};
			/* ----------------------- END Для Карты ----------------------- */

            // Triggers
            $triggers = $this->model_catalog_information->getInformationTriggers($information_id);

            $data['triggers'] = array();

                if($triggers) {
                    foreach ($triggers as $trigger) {

                        if ($trigger['image_width'] && $trigger['image_height']) {
                            $image_width = $trigger['image_width'];
                            $image_height = $trigger['image_height'];
                        } else {
                            $image_width = 100;
                            $image_height = 100;
                        }

                        if ($trigger['image']) {
                            $image = $this->model_tool_image->resize($trigger['image'], $image_width, $image_height);
                        } else {
                            $image = $this->model_tool_image->resize('placeholder.png', $image_width, $image_height);
                        }

                        $data['triggers'][] = array(
                            'title'            => $trigger['title'],
                            'image'            => $image,
                            'description'      => html_entity_decode($trigger['description'], ENT_QUOTES, 'UTF-8')
                        );
                    }
                }
            // END Triggers

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('information/'.$layout_template, $data));
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('information/information', 'information_id=' . $information_id)
			);
			$data['breadcrumbs_total'] = count($data['breadcrumbs']);
			
			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

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

	public function agree() {
		$this->load->model('catalog/information');

		if (isset($this->request->get['information_id'])) {
			$information_id = (int)$this->request->get['information_id'];
		} else {
			$information_id = 0;
		}

		$output = '';

		$information_info = $this->model_catalog_information->getInformation($information_id);

		if ($information_info) {
			$output .= html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8') . "\n";
		}

		$this->response->setOutput($output);
	}
}