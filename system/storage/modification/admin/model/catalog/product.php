<?php
class ModelCatalogProduct extends Model {
	public function addProduct($data) {
    $default_lang = $this->db->query("SELECT language_id FROM " . DB_PREFIX . "language WHERE code = '" . $this->config->get('config_language') . "'")->row['language_id'];
    $isInsertOrEdit = (strpos(__METHOD__, 'edit') !== false) ? 'edit' : 'insert';
		$this->db->query("INSERT INTO " . DB_PREFIX . "product SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', video = '" . $this->db->escape($data['video']) . "', upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_added = NOW(), date_modified = NOW()");

		$product_id = $this->db->getLastId();


    if (isset($data['meta_robots'])) {
      $this->db->query("UPDATE " . DB_PREFIX . "product SET meta_robots = '" . $this->db->escape($data['meta_robots']) . "' WHERE product_id = '" . (int)$product_id . "'");
    }
    
    if (isset($data['seo_canonical'])) {
      $this->db->query("UPDATE " . DB_PREFIX . "product SET seo_canonical = '" . $this->db->escape($data['seo_canonical']) . "' WHERE product_id = '" . (int)$product_id . "'");
    }
      
		
        if ($this->config->get('mlseo_'.$isInsertOrEdit.'autoimgname') && !defined('GKD_UNIV_IMPORT') && isset($data['image'])) {
          $this->load->model('tool/seo_package');
          $seo_image_name = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_image_name_pattern'), $this->config->get('config_language_id'), $data);
          
          $path = pathinfo($data['image']);
          
          if (!empty($path['filename'])) {
            $filename = $this->model_tool_seo_package->filter_seo($seo_image_name, 'image', '', '');
            $value = $path['dirname'] . '/' . $filename . '.' . $path['extension'];
            
            if ($data['image'] == $value) {
              $this->db->query("UPDATE " . DB_PREFIX . "product SET image = '". $this->db->escape($value) ."' WHERE product_id = '" . (int)$product_id . "'");
            } else if (file_exists(DIR_IMAGE . $data['image'])) {
              $x = 1;
              
              while (file_exists(DIR_IMAGE . $value)) {
                $value = $path['dirname'] . '/' . $filename . '-' . $x . '.' . $path['extension'];
                $x++;
              }

              if (rename(DIR_IMAGE . $data['image'], DIR_IMAGE . $value)) {
                $this->db->query("UPDATE " . DB_PREFIX . "product SET image = '". $this->db->escape($value) ."' WHERE product_id = '" . (int)$product_id . "'");
                $this->db->query("UPDATE " . DB_PREFIX . "product SET image = '". $this->db->escape($value) ."' WHERE image = '" . $this->db->escape($data['image']) . "'");
                $this->db->query("UPDATE " . DB_PREFIX . "product_image SET image = '". $this->db->escape($value) ."' WHERE image = '" . $this->db->escape($data['image']) . "'");
                
                if (isset(${'data'}['product_image'])) {
                  foreach (${'data'}['product_image'] as $k => $product_image) {
                    ${'data'}['product_image'][$k]['image'] = str_replace($data['image'], $value, $product_image['image']);
                  }
                }
                
                $data['image'] = $value;
              }
            }
          }
        } else if (isset($data['image'])) {
      
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}


      // fix issue with copy in case of not correctly deleted products
      if ($isInsertOrEdit == 'insert') {
        $this->{'db'}->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
      }
      
      if (version_compare(VERSION, '3', '>=')) {
        $currentKeywordsQuery = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$product_id . "'")->rows;
        $this->{'db'}->query("DELETE FROM " . DB_PREFIX."seo_url WHERE query = 'product_id=" . (int)$product_id . "'");
      } else {
        $currentKeywordsQuery = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "'")->rows;
        $this->{'db'}->query("DELETE FROM " . DB_PREFIX."url_alias WHERE query = 'product_id=" . (int)$product_id . "'");
      }
      
      $currentKeywords = array();
      
      foreach ($currentKeywordsQuery as $curKeyword) {
        $currentKeywords[(isset($curKeyword['store_id']) ? $curKeyword['store_id'] : 0)][(isset($curKeyword['language_id']) ? $curKeyword['language_id'] : $default_lang)] = $curKeyword['keyword'];
      }
      
      if ($this->config->get('mlseo_multistore')) {
        $this->load->model('catalog/seo_package');
        $this->model_catalog_seo_package->setSeoDescriptions('product', $data, $product_id, $isInsertOrEdit, $currentKeywords);
      }
      
		foreach ($data['product_description'] as $language_id => $value) {

      if ($this->config->get('mlseo_enabled')) {
        if (isset($data['keyword'])) {
          unset($data['keyword']);
        }
        
        if ($this->config->get('mlseo_'.$isInsertOrEdit.'autotitle')) {
          $value['name'] = ($value['name']) ? $value['name'] : $data['product_description'][$default_lang]['name'];
          $value['description'] = ($value['description']) ? $value['description'] : $data['product_description'][$default_lang]['description'];
        }
        
        $this->load->model('tool/seo_package');
        
        $data['product_id'] = $product_id; // add id into dataset for use with patterns
        
        $seo_kw = '';
        
        if (empty($value['seo_keyword']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autourl')) {
          $seo_kw = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_url_pattern'), $language_id, $data);
        } else if (!empty($value['seo_keyword'])) {
          $seo_kw = html_entity_decode($value['seo_keyword'], ENT_QUOTES, 'UTF-8');
        }
        
        if ($seo_kw) {
          $seo_kw = $this->model_tool_seo_package->filter_seo($seo_kw, 'product', $product_id, $language_id);
        }
        
        $url_alias_table = version_compare(VERSION, '3', '>=') ? 'seo_url' : 'url_alias';
        
        if ($this->config->get('mlseo_backup')) {
          $currentKeyword = isset($currentKeywords[0][$language_id]) ? $currentKeywords[0][$language_id] : '';
          
          if ($currentKeyword && $currentKeyword != $seo_kw) {
            $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "url_redirect SET query = '/" . $this->db->escape($currentKeyword) . "', redirect = 'product/product&product_id=" . (int) $product_id . "', language_id = '" . (int) $language_id . "'");
          }
        }
        
        if (version_compare(VERSION, '3', '>=')) {
          $this->db->query("INSERT INTO " . DB_PREFIX . $url_alias_table . " SET query = 'product_id=" . (int)$product_id . "', language_id = '" . (int)$language_id . "', keyword = '" . $this->db->escape($seo_kw) . "', store_id = 0");
        } else if ($this->config->get('mlseo_ml_mode')) {
          $this->db->query("INSERT INTO " . DB_PREFIX . $url_alias_table . " SET query = 'product_id=" . (int)$product_id . "', language_id = '" . (int)$language_id . "', keyword = '" . $this->db->escape($seo_kw) . "'");
        } else {
          $this->db->query("INSERT INTO " . DB_PREFIX . $url_alias_table . " SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($seo_kw) . "'");
        }
        
        if (!$value['meta_title'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autoseotitle')) {
          $value['meta_title'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_title_pattern'), $language_id, $data);
        }
        if (!trim(strip_tags(html_entity_decode($value['description'], ENT_QUOTES, 'UTF-8'))) && $this->config->get('mlseo_'.$isInsertOrEdit.'autodesc')) {
          $value['description'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_full_desc_pattern'), $language_id, $data);
        }
        if (!$value['meta_description'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autometadesc')) {
          $value['meta_description'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_description_pattern'), $language_id, $data);
        }
        if (!$value['meta_keyword'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autometakeyword')) {
          $value['meta_keyword'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_keyword_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h1']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh1')) {
          $value['seo_h1'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_h1_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h2']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh2')) {
          $value['seo_h2'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_h2_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h3']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh3')) {
          $value['seo_h3'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_h3_pattern'), $language_id, $data);
        }
        if (empty($value['image_title']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoimgtitle')) {
          $value['image_title'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_image_title_pattern'), $language_id, $data);
        }
        if (empty($value['image_alt']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoimgalt')) {
          $value['image_alt'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_image_alt_pattern'), $language_id, $data);
        }
        if (empty($value['tag']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autotags')) {
          $value['tag'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_tag_pattern'), $language_id, $data);
        }
      }
      
			
        $value['seo_h1'] = empty($value['seo_h1']) ? '' : $value['seo_h1'];
        $value['seo_h2'] = empty($value['seo_h2']) ? '' : $value['seo_h2'];
        $value['seo_h3'] = empty($value['seo_h3']) ? '' : $value['seo_h3'];
        $value['image_alt'] = empty($value['image_alt']) ? '' : $value['image_alt'];
        $value['image_title'] = empty($value['image_title']) ? '' : $value['image_title'];
      
        $extra_fields = '';
        if ($this->config->get('mlseo_enabled')) {
          $extra_fields = "seo_keyword = '" . $this->db->escape($seo_kw) . "', seo_h1 = '" . $this->db->escape($value['seo_h1']) . "', seo_h2 = '" . $this->db->escape($value['seo_h2']) . "', seo_h3 = '" . $this->db->escape($value['seo_h3']) . "', image_alt = '" . $this->db->escape($value['image_alt']) . "', image_title = '" . $this->db->escape($value['image_title']) . "', ";
          if (substr(VERSION, 0, 1) == 1) {
            $extra_fields .= "meta_title = '" . $this->db->escape($value['meta_title']) . "', ";
          }
        }
        
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET " . $extra_fields . " product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					// Removes duplicates
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "' AND language_id = '" . (int)$language_id . "'");

						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
				}
			}
		}


    // OCFilter start
		if (isset($data['ocfilter_product_option'])) {
			foreach ($data['ocfilter_product_option'] as $option_id => $values) {
				foreach ($values['values'] as $value_id => $value) {
					if (isset($value['selected'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_option_value_to_product SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$option_id . "', value_id = '" . (string)$value_id . "', slide_value_min = '" . (isset($value['slide_value_min']) ? (float)$value['slide_value_min'] : 0) . "', slide_value_max = '" . (isset($value['slide_value_max']) ? (float)$value['slide_value_max'] : 0) . "'");
					}
				}
			}
		}
		// OCFilter end
      
		if (isset($data['product_recurring'])) {
			foreach ($data['product_recurring'] as $recurring) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int)$product_id . ", customer_group_id = " . (int)$recurring['customer_group_id'] . ", `recurring_id` = " . (int)$recurring['recurring_id']);
			}
		}

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}

		if (isset($data['product_promo'])) {
			foreach ($data['product_promo'] as $product_promo) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_promo SET product_id = '" . (int)$product_id . "', promo_tags_id = '" . (int)$product_promo['promo_tags_id'] . "', date_start = '" . $this->db->escape($product_promo['date_start']) . "', date_end = '" . $this->db->escape($product_promo['date_end']) . "'");
			}
		}

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
			}
		}

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		if(isset($data['main_category_id']) && $data['main_category_id'] > 0) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$data['main_category_id'] . "'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$data['main_category_id'] . "', main_category = 1");
		} elseif(isset($data['product_category'][0])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product_to_category SET main_category = 1 WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$data['product_category'][0] . "'");
		}


        if (empty($data['product_related']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autorelated')) {
          $prod_name = str_replace(array('%', '#', "'", '"'), '', $data['product_description'][$default_lang]['name']);
          $prod_tag = str_replace(array('%', '#', "'", '"'), '', $data['product_description'][$default_lang]['tag']);
          $prod_desc = str_replace(array('\n', '\r', '%', '#', "'", '"'), '', $data['product_description'][$default_lang]['description']);
          
          if ($this->config->get('mlseo_product_related_relevance')) {
            $relevance = $this->config->get('mlseo_product_related_relevance');
          } else {
            $relevance = 2;
          }
          
          if ($this->config->get('mlseo_product_related_no')) {
            $max_items = $this->config->get('mlseo_product_related_no');
          } else {
            $max_items = 5;
          }
          
          $rel_count = $this->db->query("SELECT COUNT(*) AS count FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int) $product_id . "'")->row;
          
          if (isset($rel_count['count']) && $rel_count['count'] < $max_items) {
            $max_items -= $rel_count['count'];
            
            $same_cat = '';
            $process_related = true;
            
            if ($this->config->get('mlseo_product_related_samecat')) {
              if (!empty($data['product_category'])) {
                $data['product_category'] = array_map('intval', $data['product_category']);
                
                $rel_cat = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "category_path WHERE category_id IN (".implode(',', $data['product_category']).") ORDER BY level DESC LIMIT 1")->row;
                
                if (!empty($rel_cat['category_id'])) {
                  $same_cat = "AND pc.category_id = " . (int) $rel_cat['category_id'];
                }
              } else {
                $process_related = false;
              }
            }
            
            if ($process_related) {
              $rel_query = $this->db->query("SELECT DISTINCT ROUND(MATCH (pd.name, pd.description) AGAINST ('" . $prod_name . " " . $prod_tag . " " . $prod_desc . "'), 0) / 5 as relevance,  p.product_id FROM " . DB_PREFIX . "product_description pd LEFT JOIN " . DB_PREFIX . "product p on pd.product_id = p.product_id INNER JOIN " . DB_PREFIX . "product_to_category pc on pd.product_id = pc.product_id WHERE p.product_id <> " . (int) $product_id . " " . $same_cat . "  AND p.status = 1 GROUP BY p.product_id HAVING relevance >= " . (int) $relevance . " ORDER BY relevance DESC LIMIT 0, " . (int) $max_items)->rows;
              
              foreach ($rel_query as $rel) {
                $data['product_related'][] = $rel['product_id'];
              }
            }
            
            if (isset($data["product_related"])) {
              foreach ($data["product_related"] as $related_id) {
                // only insert related to current product to avoid inserting too much related products
                $this->db->query("DELETE FROM `" . DB_PREFIX . "product_related` WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
                $this->db->query("INSERT INTO `" . DB_PREFIX . "product_related` SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
              }
            }
          }
        } else
      
		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $product_reward) {
				if ((int)$product_reward['points'] > 0) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$product_reward['points'] . "'");
				}
			}
		}

		// SEO URL
		if (isset($data['product_seo_url'])) {
			foreach ($data['product_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($keyword) . "'");
                    } else {
                        if($language_id == 1) {
                            $seo_url = $data['product_description'][2]['name'] . '_en';
                            if(isset($data['product_description'][1]['name']) && $data['product_description'][1]['name'] != $data['product_description'][2]['name']) {
                                $seo_url = $data['product_description'][1]['name'];
                            }
                        }
                        if($language_id == 2) {
                            $seo_url = $data['product_description'][2]['name'];
                        }
                        if($language_id == 3) {
                            $seo_url = $data['product_description'][2]['name'] . '_ua';
                            if(isset($data['product_description'][3]['name']) && $data['product_description'][3]['name'] != $data['product_description'][2]['name']) {
                                $seo_url = $data['product_description'][3]['name'];
                            }
                        }

                        $this->translit->setSeoURL('product_id', $language_id, (int)$product_id, $seo_url);
                    }
				}
			}
		}

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}


		
        $this->cache->delete('product');
        if($this->config->get('module_oc_cache_status') && file_exists(DIR_APPLICATION . '../vendor/autoload.php')){
          $webkulcache = $this->load->library('cart/webkulcache');
          $this->webkulcache->deleteCache(array('product','product_images','product_options'), 'files');
        }
      

		return $product_id;
	}

    public function addProduct1c($data) {

        $this->db->query("INSERT INTO " . DB_PREFIX . "product SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', video = '', upc = '" . $this->db->escape($data['upc']) . "', `location` = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '0', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '0', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_added = NOW(), date_modified = NOW()");

        $product_id = $this->db->getLastId();

        foreach ($data['product_description'] as $value) {

      if ($this->config->get('mlseo_enabled')) {
        if (isset($data['keyword'])) {
          unset($data['keyword']);
        }
        
        if ($this->config->get('mlseo_'.$isInsertOrEdit.'autotitle')) {
          $value['name'] = ($value['name']) ? $value['name'] : $data['product_description'][$default_lang]['name'];
          $value['description'] = ($value['description']) ? $value['description'] : $data['product_description'][$default_lang]['description'];
        }
        
        $this->load->model('tool/seo_package');
        
        $data['product_id'] = $product_id; // add id into dataset for use with patterns
        
        $seo_kw = '';
        
        if (empty($value['seo_keyword']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autourl')) {
          $seo_kw = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_url_pattern'), $language_id, $data);
        } else if (!empty($value['seo_keyword'])) {
          $seo_kw = html_entity_decode($value['seo_keyword'], ENT_QUOTES, 'UTF-8');
        }
        
        if ($seo_kw) {
          $seo_kw = $this->model_tool_seo_package->filter_seo($seo_kw, 'product', $product_id, $language_id);
        }
        
        $url_alias_table = version_compare(VERSION, '3', '>=') ? 'seo_url' : 'url_alias';
        
        if ($this->config->get('mlseo_backup')) {
          $currentKeyword = isset($currentKeywords[0][$language_id]) ? $currentKeywords[0][$language_id] : '';
          
          if ($currentKeyword && $currentKeyword != $seo_kw) {
            $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "url_redirect SET query = '/" . $this->db->escape($currentKeyword) . "', redirect = 'product/product&product_id=" . (int) $product_id . "', language_id = '" . (int) $language_id . "'");
          }
        }
        
        if (version_compare(VERSION, '3', '>=')) {
          $this->db->query("INSERT INTO " . DB_PREFIX . $url_alias_table . " SET query = 'product_id=" . (int)$product_id . "', language_id = '" . (int)$language_id . "', keyword = '" . $this->db->escape($seo_kw) . "', store_id = 0");
        } else if ($this->config->get('mlseo_ml_mode')) {
          $this->db->query("INSERT INTO " . DB_PREFIX . $url_alias_table . " SET query = 'product_id=" . (int)$product_id . "', language_id = '" . (int)$language_id . "', keyword = '" . $this->db->escape($seo_kw) . "'");
        } else {
          $this->db->query("INSERT INTO " . DB_PREFIX . $url_alias_table . " SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($seo_kw) . "'");
        }
        
        if (!$value['meta_title'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autoseotitle')) {
          $value['meta_title'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_title_pattern'), $language_id, $data);
        }
        if (!trim(strip_tags(html_entity_decode($value['description'], ENT_QUOTES, 'UTF-8'))) && $this->config->get('mlseo_'.$isInsertOrEdit.'autodesc')) {
          $value['description'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_full_desc_pattern'), $language_id, $data);
        }
        if (!$value['meta_description'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autometadesc')) {
          $value['meta_description'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_description_pattern'), $language_id, $data);
        }
        if (!$value['meta_keyword'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autometakeyword')) {
          $value['meta_keyword'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_keyword_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h1']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh1')) {
          $value['seo_h1'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_h1_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h2']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh2')) {
          $value['seo_h2'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_h2_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h3']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh3')) {
          $value['seo_h3'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_h3_pattern'), $language_id, $data);
        }
        if (empty($value['image_title']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoimgtitle')) {
          $value['image_title'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_image_title_pattern'), $language_id, $data);
        }
        if (empty($value['image_alt']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoimgalt')) {
          $value['image_alt'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_image_alt_pattern'), $language_id, $data);
        }
        if (empty($value['tag']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autotags')) {
          $value['tag'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_tag_pattern'), $language_id, $data);
        }
      }
      
            
        $value['seo_h1'] = empty($value['seo_h1']) ? '' : $value['seo_h1'];
        $value['seo_h2'] = empty($value['seo_h2']) ? '' : $value['seo_h2'];
        $value['seo_h3'] = empty($value['seo_h3']) ? '' : $value['seo_h3'];
        $value['image_alt'] = empty($value['image_alt']) ? '' : $value['image_alt'];
        $value['image_title'] = empty($value['image_title']) ? '' : $value['image_title'];
      
        $extra_fields = '';
        if ($this->config->get('mlseo_enabled')) {
          $extra_fields = "seo_keyword = '" . $this->db->escape($seo_kw) . "', seo_h1 = '" . $this->db->escape($value['seo_h1']) . "', seo_h2 = '" . $this->db->escape($value['seo_h2']) . "', seo_h3 = '" . $this->db->escape($value['seo_h3']) . "', image_alt = '" . $this->db->escape($value['image_alt']) . "', image_title = '" . $this->db->escape($value['image_title']) . "', ";
          if (substr(VERSION, 0, 1) == 1) {
            $extra_fields .= "meta_title = '" . $this->db->escape($value['meta_title']) . "', ";
          }
        }
        
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET " . $extra_fields . " product_id = '" . (int)$product_id . "', language_id = '1', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['name']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");

      if ($this->config->get('mlseo_enabled')) {
        if (isset($data['keyword'])) {
          unset($data['keyword']);
        }
        
        if ($this->config->get('mlseo_'.$isInsertOrEdit.'autotitle')) {
          $value['name'] = ($value['name']) ? $value['name'] : $data['product_description'][$default_lang]['name'];
          $value['description'] = ($value['description']) ? $value['description'] : $data['product_description'][$default_lang]['description'];
        }
        
        $this->load->model('tool/seo_package');
        
        $data['product_id'] = $product_id; // add id into dataset for use with patterns
        
        $seo_kw = '';
        
        if (empty($value['seo_keyword']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autourl')) {
          $seo_kw = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_url_pattern'), $language_id, $data);
        } else if (!empty($value['seo_keyword'])) {
          $seo_kw = html_entity_decode($value['seo_keyword'], ENT_QUOTES, 'UTF-8');
        }
        
        if ($seo_kw) {
          $seo_kw = $this->model_tool_seo_package->filter_seo($seo_kw, 'product', $product_id, $language_id);
        }
        
        $url_alias_table = version_compare(VERSION, '3', '>=') ? 'seo_url' : 'url_alias';
        
        if ($this->config->get('mlseo_backup')) {
          $currentKeyword = isset($currentKeywords[0][$language_id]) ? $currentKeywords[0][$language_id] : '';
          
          if ($currentKeyword && $currentKeyword != $seo_kw) {
            $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "url_redirect SET query = '/" . $this->db->escape($currentKeyword) . "', redirect = 'product/product&product_id=" . (int) $product_id . "', language_id = '" . (int) $language_id . "'");
          }
        }
        
        if (version_compare(VERSION, '3', '>=')) {
          $this->db->query("INSERT INTO " . DB_PREFIX . $url_alias_table . " SET query = 'product_id=" . (int)$product_id . "', language_id = '" . (int)$language_id . "', keyword = '" . $this->db->escape($seo_kw) . "', store_id = 0");
        } else if ($this->config->get('mlseo_ml_mode')) {
          $this->db->query("INSERT INTO " . DB_PREFIX . $url_alias_table . " SET query = 'product_id=" . (int)$product_id . "', language_id = '" . (int)$language_id . "', keyword = '" . $this->db->escape($seo_kw) . "'");
        } else {
          $this->db->query("INSERT INTO " . DB_PREFIX . $url_alias_table . " SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($seo_kw) . "'");
        }
        
        if (!$value['meta_title'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autoseotitle')) {
          $value['meta_title'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_title_pattern'), $language_id, $data);
        }
        if (!trim(strip_tags(html_entity_decode($value['description'], ENT_QUOTES, 'UTF-8'))) && $this->config->get('mlseo_'.$isInsertOrEdit.'autodesc')) {
          $value['description'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_full_desc_pattern'), $language_id, $data);
        }
        if (!$value['meta_description'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autometadesc')) {
          $value['meta_description'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_description_pattern'), $language_id, $data);
        }
        if (!$value['meta_keyword'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autometakeyword')) {
          $value['meta_keyword'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_keyword_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h1']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh1')) {
          $value['seo_h1'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_h1_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h2']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh2')) {
          $value['seo_h2'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_h2_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h3']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh3')) {
          $value['seo_h3'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_h3_pattern'), $language_id, $data);
        }
        if (empty($value['image_title']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoimgtitle')) {
          $value['image_title'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_image_title_pattern'), $language_id, $data);
        }
        if (empty($value['image_alt']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoimgalt')) {
          $value['image_alt'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_image_alt_pattern'), $language_id, $data);
        }
        if (empty($value['tag']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autotags')) {
          $value['tag'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_tag_pattern'), $language_id, $data);
        }
      }
      
            
        $value['seo_h1'] = empty($value['seo_h1']) ? '' : $value['seo_h1'];
        $value['seo_h2'] = empty($value['seo_h2']) ? '' : $value['seo_h2'];
        $value['seo_h3'] = empty($value['seo_h3']) ? '' : $value['seo_h3'];
        $value['image_alt'] = empty($value['image_alt']) ? '' : $value['image_alt'];
        $value['image_title'] = empty($value['image_title']) ? '' : $value['image_title'];
      
        $extra_fields = '';
        if ($this->config->get('mlseo_enabled')) {
          $extra_fields = "seo_keyword = '" . $this->db->escape($seo_kw) . "', seo_h1 = '" . $this->db->escape($value['seo_h1']) . "', seo_h2 = '" . $this->db->escape($value['seo_h2']) . "', seo_h3 = '" . $this->db->escape($value['seo_h3']) . "', image_alt = '" . $this->db->escape($value['image_alt']) . "', image_title = '" . $this->db->escape($value['image_title']) . "', ";
          if (substr(VERSION, 0, 1) == 1) {
            $extra_fields .= "meta_title = '" . $this->db->escape($value['meta_title']) . "', ";
          }
        }
        
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET " . $extra_fields . " product_id = '" . (int)$product_id . "', language_id = '2', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['name']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");

      if ($this->config->get('mlseo_enabled')) {
        if (isset($data['keyword'])) {
          unset($data['keyword']);
        }
        
        if ($this->config->get('mlseo_'.$isInsertOrEdit.'autotitle')) {
          $value['name'] = ($value['name']) ? $value['name'] : $data['product_description'][$default_lang]['name'];
          $value['description'] = ($value['description']) ? $value['description'] : $data['product_description'][$default_lang]['description'];
        }
        
        $this->load->model('tool/seo_package');
        
        $data['product_id'] = $product_id; // add id into dataset for use with patterns
        
        $seo_kw = '';
        
        if (empty($value['seo_keyword']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autourl')) {
          $seo_kw = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_url_pattern'), $language_id, $data);
        } else if (!empty($value['seo_keyword'])) {
          $seo_kw = html_entity_decode($value['seo_keyword'], ENT_QUOTES, 'UTF-8');
        }
        
        if ($seo_kw) {
          $seo_kw = $this->model_tool_seo_package->filter_seo($seo_kw, 'product', $product_id, $language_id);
        }
        
        $url_alias_table = version_compare(VERSION, '3', '>=') ? 'seo_url' : 'url_alias';
        
        if ($this->config->get('mlseo_backup')) {
          $currentKeyword = isset($currentKeywords[0][$language_id]) ? $currentKeywords[0][$language_id] : '';
          
          if ($currentKeyword && $currentKeyword != $seo_kw) {
            $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "url_redirect SET query = '/" . $this->db->escape($currentKeyword) . "', redirect = 'product/product&product_id=" . (int) $product_id . "', language_id = '" . (int) $language_id . "'");
          }
        }
        
        if (version_compare(VERSION, '3', '>=')) {
          $this->db->query("INSERT INTO " . DB_PREFIX . $url_alias_table . " SET query = 'product_id=" . (int)$product_id . "', language_id = '" . (int)$language_id . "', keyword = '" . $this->db->escape($seo_kw) . "', store_id = 0");
        } else if ($this->config->get('mlseo_ml_mode')) {
          $this->db->query("INSERT INTO " . DB_PREFIX . $url_alias_table . " SET query = 'product_id=" . (int)$product_id . "', language_id = '" . (int)$language_id . "', keyword = '" . $this->db->escape($seo_kw) . "'");
        } else {
          $this->db->query("INSERT INTO " . DB_PREFIX . $url_alias_table . " SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($seo_kw) . "'");
        }
        
        if (!$value['meta_title'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autoseotitle')) {
          $value['meta_title'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_title_pattern'), $language_id, $data);
        }
        if (!trim(strip_tags(html_entity_decode($value['description'], ENT_QUOTES, 'UTF-8'))) && $this->config->get('mlseo_'.$isInsertOrEdit.'autodesc')) {
          $value['description'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_full_desc_pattern'), $language_id, $data);
        }
        if (!$value['meta_description'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autometadesc')) {
          $value['meta_description'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_description_pattern'), $language_id, $data);
        }
        if (!$value['meta_keyword'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autometakeyword')) {
          $value['meta_keyword'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_keyword_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h1']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh1')) {
          $value['seo_h1'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_h1_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h2']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh2')) {
          $value['seo_h2'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_h2_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h3']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh3')) {
          $value['seo_h3'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_h3_pattern'), $language_id, $data);
        }
        if (empty($value['image_title']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoimgtitle')) {
          $value['image_title'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_image_title_pattern'), $language_id, $data);
        }
        if (empty($value['image_alt']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoimgalt')) {
          $value['image_alt'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_image_alt_pattern'), $language_id, $data);
        }
        if (empty($value['tag']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autotags')) {
          $value['tag'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_tag_pattern'), $language_id, $data);
        }
      }
      
            
        $value['seo_h1'] = empty($value['seo_h1']) ? '' : $value['seo_h1'];
        $value['seo_h2'] = empty($value['seo_h2']) ? '' : $value['seo_h2'];
        $value['seo_h3'] = empty($value['seo_h3']) ? '' : $value['seo_h3'];
        $value['image_alt'] = empty($value['image_alt']) ? '' : $value['image_alt'];
        $value['image_title'] = empty($value['image_title']) ? '' : $value['image_title'];
      
        $extra_fields = '';
        if ($this->config->get('mlseo_enabled')) {
          $extra_fields = "seo_keyword = '" . $this->db->escape($seo_kw) . "', seo_h1 = '" . $this->db->escape($value['seo_h1']) . "', seo_h2 = '" . $this->db->escape($value['seo_h2']) . "', seo_h3 = '" . $this->db->escape($value['seo_h3']) . "', image_alt = '" . $this->db->escape($value['image_alt']) . "', image_title = '" . $this->db->escape($value['image_title']) . "', ";
          if (substr(VERSION, 0, 1) == 1) {
            $extra_fields .= "meta_title = '" . $this->db->escape($value['meta_title']) . "', ";
          }
        }
        
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET " . $extra_fields . " product_id = '" . (int)$product_id . "', language_id = '3', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['name']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
        }

        if (isset($data['product_store'])) {
            foreach ($data['product_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
            }
        }

        // SEO URL
        if (isset($data['product_seo_url'])) {
            foreach ($data['product_seo_url'] as $store_id => $language) {
                foreach ($language as $language_id => $keyword) {
                    if (!empty($keyword)) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($keyword) . "'");
                    } else {
                        if($language_id == 1) {
                            $seo_url = $data['product_description'][2]['name'] . '_en';
                            if(isset($data['product_description'][1]['name']) && $data['product_description'][1]['name'] != $data['product_description'][2]['name']) {
                                $seo_url = $data['product_description'][1]['name'];
                            }
                        }
                        if($language_id == 2) {
                            $seo_url = $data['product_description'][2]['name'];
                        }
                        if($language_id == 3) {
                            $seo_url = $data['product_description'][2]['name'] . '_ua';
                            if(isset($data['product_description'][3]['name']) && $data['product_description'][3]['name'] != $data['product_description'][2]['name']) {
                                $seo_url = $data['product_description'][3]['name'];
                            }
                        }

                        $this->translit->setSeoURL('product_id', $language_id, (int)$product_id, $seo_url);
                    }
                }
            }
        }

        if (isset($data['product_layout'])) {
            foreach ($data['product_layout'] as $store_id => $layout_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
            }
        }

        
        $this->cache->delete('product');
        if($this->config->get('module_oc_cache_status') && file_exists(DIR_APPLICATION . '../vendor/autoload.php')){
          $webkulcache = $this->load->library('cart/webkulcache');
          $this->webkulcache->deleteCache(array('product','product_images','product_options'), 'files');
        }
      

        return $product_id;
    }

	public function editProduct($product_id, $data) {
    $default_lang = $this->db->query("SELECT language_id FROM " . DB_PREFIX . "language WHERE code = '" . $this->config->get('config_language') . "'")->row['language_id'];
    $isInsertOrEdit = (strpos(__METHOD__, 'edit') !== false) ? 'edit' : 'insert';
		$this->db->query("UPDATE " . DB_PREFIX . "product SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', video = '" . $this->db->escape($data['video']) . "', upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");


    if (isset($data['meta_robots'])) {
      $this->db->query("UPDATE " . DB_PREFIX . "product SET meta_robots = '" . $this->db->escape($data['meta_robots']) . "' WHERE product_id = '" . (int)$product_id . "'");
    }
    
    if (isset($data['seo_canonical'])) {
      $this->db->query("UPDATE " . DB_PREFIX . "product SET seo_canonical = '" . $this->db->escape($data['seo_canonical']) . "' WHERE product_id = '" . (int)$product_id . "'");
    }
      
		
        if ($this->config->get('mlseo_'.$isInsertOrEdit.'autoimgname') && !defined('GKD_UNIV_IMPORT') && isset($data['image'])) {
          $this->load->model('tool/seo_package');
          $seo_image_name = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_image_name_pattern'), $this->config->get('config_language_id'), $data);
          
          $path = pathinfo($data['image']);
          
          if (!empty($path['filename'])) {
            $filename = $this->model_tool_seo_package->filter_seo($seo_image_name, 'image', '', '');
            $value = $path['dirname'] . '/' . $filename . '.' . $path['extension'];
            
            if ($data['image'] == $value) {
              $this->db->query("UPDATE " . DB_PREFIX . "product SET image = '". $this->db->escape($value) ."' WHERE product_id = '" . (int)$product_id . "'");
            } else if (file_exists(DIR_IMAGE . $data['image'])) {
              $x = 1;
              
              while (file_exists(DIR_IMAGE . $value)) {
                $value = $path['dirname'] . '/' . $filename . '-' . $x . '.' . $path['extension'];
                $x++;
              }

              if (rename(DIR_IMAGE . $data['image'], DIR_IMAGE . $value)) {
                $this->db->query("UPDATE " . DB_PREFIX . "product SET image = '". $this->db->escape($value) ."' WHERE product_id = '" . (int)$product_id . "'");
                $this->db->query("UPDATE " . DB_PREFIX . "product SET image = '". $this->db->escape($value) ."' WHERE image = '" . $this->db->escape($data['image']) . "'");
                $this->db->query("UPDATE " . DB_PREFIX . "product_image SET image = '". $this->db->escape($value) ."' WHERE image = '" . $this->db->escape($data['image']) . "'");
                
                if (isset(${'data'}['product_image'])) {
                  foreach (${'data'}['product_image'] as $k => $product_image) {
                    ${'data'}['product_image'][$k]['image'] = str_replace($data['image'], $value, $product_image['image']);
                  }
                }
                
                $data['image'] = $value;
              }
            }
          }
        } else if (isset($data['image'])) {
      
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");


      // fix issue with copy in case of not correctly deleted products
      if ($isInsertOrEdit == 'insert') {
        $this->{'db'}->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
      }
      
      if (version_compare(VERSION, '3', '>=')) {
        $currentKeywordsQuery = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$product_id . "'")->rows;
        $this->{'db'}->query("DELETE FROM " . DB_PREFIX."seo_url WHERE query = 'product_id=" . (int)$product_id . "'");
      } else {
        $currentKeywordsQuery = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "'")->rows;
        $this->{'db'}->query("DELETE FROM " . DB_PREFIX."url_alias WHERE query = 'product_id=" . (int)$product_id . "'");
      }
      
      $currentKeywords = array();
      
      foreach ($currentKeywordsQuery as $curKeyword) {
        $currentKeywords[(isset($curKeyword['store_id']) ? $curKeyword['store_id'] : 0)][(isset($curKeyword['language_id']) ? $curKeyword['language_id'] : $default_lang)] = $curKeyword['keyword'];
      }
      
      if ($this->config->get('mlseo_multistore')) {
        $this->load->model('catalog/seo_package');
        $this->model_catalog_seo_package->setSeoDescriptions('product', $data, $product_id, $isInsertOrEdit, $currentKeywords);
      }
      
		foreach ($data['product_description'] as $language_id => $value) {

      if ($this->config->get('mlseo_enabled')) {
        if (isset($data['keyword'])) {
          unset($data['keyword']);
        }
        
        if ($this->config->get('mlseo_'.$isInsertOrEdit.'autotitle')) {
          $value['name'] = ($value['name']) ? $value['name'] : $data['product_description'][$default_lang]['name'];
          $value['description'] = ($value['description']) ? $value['description'] : $data['product_description'][$default_lang]['description'];
        }
        
        $this->load->model('tool/seo_package');
        
        $data['product_id'] = $product_id; // add id into dataset for use with patterns
        
        $seo_kw = '';
        
        if (empty($value['seo_keyword']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autourl')) {
          $seo_kw = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_url_pattern'), $language_id, $data);
        } else if (!empty($value['seo_keyword'])) {
          $seo_kw = html_entity_decode($value['seo_keyword'], ENT_QUOTES, 'UTF-8');
        }
        
        if ($seo_kw) {
          $seo_kw = $this->model_tool_seo_package->filter_seo($seo_kw, 'product', $product_id, $language_id);
        }
        
        $url_alias_table = version_compare(VERSION, '3', '>=') ? 'seo_url' : 'url_alias';
        
        if ($this->config->get('mlseo_backup')) {
          $currentKeyword = isset($currentKeywords[0][$language_id]) ? $currentKeywords[0][$language_id] : '';
          
          if ($currentKeyword && $currentKeyword != $seo_kw) {
            $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "url_redirect SET query = '/" . $this->db->escape($currentKeyword) . "', redirect = 'product/product&product_id=" . (int) $product_id . "', language_id = '" . (int) $language_id . "'");
          }
        }
        
        if (version_compare(VERSION, '3', '>=')) {
          $this->db->query("INSERT INTO " . DB_PREFIX . $url_alias_table . " SET query = 'product_id=" . (int)$product_id . "', language_id = '" . (int)$language_id . "', keyword = '" . $this->db->escape($seo_kw) . "', store_id = 0");
        } else if ($this->config->get('mlseo_ml_mode')) {
          $this->db->query("INSERT INTO " . DB_PREFIX . $url_alias_table . " SET query = 'product_id=" . (int)$product_id . "', language_id = '" . (int)$language_id . "', keyword = '" . $this->db->escape($seo_kw) . "'");
        } else {
          $this->db->query("INSERT INTO " . DB_PREFIX . $url_alias_table . " SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($seo_kw) . "'");
        }
        
        if (!$value['meta_title'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autoseotitle')) {
          $value['meta_title'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_title_pattern'), $language_id, $data);
        }
        if (!trim(strip_tags(html_entity_decode($value['description'], ENT_QUOTES, 'UTF-8'))) && $this->config->get('mlseo_'.$isInsertOrEdit.'autodesc')) {
          $value['description'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_full_desc_pattern'), $language_id, $data);
        }
        if (!$value['meta_description'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autometadesc')) {
          $value['meta_description'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_description_pattern'), $language_id, $data);
        }
        if (!$value['meta_keyword'] && $this->config->get('mlseo_'.$isInsertOrEdit.'autometakeyword')) {
          $value['meta_keyword'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_keyword_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h1']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh1')) {
          $value['seo_h1'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_h1_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h2']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh2')) {
          $value['seo_h2'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_h2_pattern'), $language_id, $data);
        }
        if (empty($value['seo_h3']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoh3')) {
          $value['seo_h3'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_h3_pattern'), $language_id, $data);
        }
        if (empty($value['image_title']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoimgtitle')) {
          $value['image_title'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_image_title_pattern'), $language_id, $data);
        }
        if (empty($value['image_alt']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autoimgalt')) {
          $value['image_alt'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_image_alt_pattern'), $language_id, $data);
        }
        if (empty($value['tag']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autotags')) {
          $value['tag'] = $this->model_tool_seo_package->transformProduct($this->config->get('mlseo_product_tag_pattern'), $language_id, $data);
        }
      }
      
			
        $value['seo_h1'] = empty($value['seo_h1']) ? '' : $value['seo_h1'];
        $value['seo_h2'] = empty($value['seo_h2']) ? '' : $value['seo_h2'];
        $value['seo_h3'] = empty($value['seo_h3']) ? '' : $value['seo_h3'];
        $value['image_alt'] = empty($value['image_alt']) ? '' : $value['image_alt'];
        $value['image_title'] = empty($value['image_title']) ? '' : $value['image_title'];
      
        $extra_fields = '';
        if ($this->config->get('mlseo_enabled')) {
          $extra_fields = "seo_keyword = '" . $this->db->escape($seo_kw) . "', seo_h1 = '" . $this->db->escape($value['seo_h1']) . "', seo_h2 = '" . $this->db->escape($value['seo_h2']) . "', seo_h3 = '" . $this->db->escape($value['seo_h3']) . "', image_alt = '" . $this->db->escape($value['image_alt']) . "', image_title = '" . $this->db->escape($value['image_title']) . "', ";
          if (substr(VERSION, 0, 1) == 1) {
            $extra_fields .= "meta_title = '" . $this->db->escape($value['meta_title']) . "', ";
          }
        }
        
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET " . $extra_fields . " product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', `name` = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");

		if (!empty($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					// Removes duplicates
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "', product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
				}
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = " . (int)$product_id);


    // OCFilter start
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_option_value_to_product WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['ocfilter_product_option'])) {
			foreach ($data['ocfilter_product_option'] as $option_id => $values) {
				foreach ($values['values'] as $value_id => $value) {
					if (isset($value['selected'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_option_value_to_product SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$option_id . "', value_id = '" . (string)$value_id . "', slide_value_min = '" . (isset($value['slide_value_min']) ? (float)$value['slide_value_min'] : 0) . "', slide_value_max = '" . (isset($value['slide_value_max']) ? (float)$value['slide_value_max'] : 0) . "'");
					}
				}
			}
		}
		// OCFilter end
      
		if (isset($data['product_recurring'])) {
			foreach ($data['product_recurring'] as $product_recurring) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int)$product_id . ", customer_group_id = " . (int)$product_recurring['customer_group_id'] . ", `recurring_id` = " . (int)$product_recurring['recurring_id']);
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_promo WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_promo'])) {
			foreach ($data['product_promo'] as $product_promo) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_promo SET product_id = '" . (int)$product_id . "', promo_tags_id = '" . (int)$product_promo['promo_tags_id'] . "', date_start = '" . $this->db->escape($product_promo['date_start']) . "', date_end = '" . $this->db->escape($product_promo['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		if(isset($data['main_category_id']) && $data['main_category_id'] > 0) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$data['main_category_id'] . "'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$data['main_category_id'] . "', main_category = 1");
		} elseif(isset($data['product_category'][0])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product_to_category SET main_category = 1 WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$data['product_category'][0] . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");


        if (empty($data['product_related']) && $this->config->get('mlseo_'.$isInsertOrEdit.'autorelated')) {
          $prod_name = str_replace(array('%', '#', "'", '"'), '', $data['product_description'][$default_lang]['name']);
          $prod_tag = str_replace(array('%', '#', "'", '"'), '', $data['product_description'][$default_lang]['tag']);
          $prod_desc = str_replace(array('\n', '\r', '%', '#', "'", '"'), '', $data['product_description'][$default_lang]['description']);
          
          if ($this->config->get('mlseo_product_related_relevance')) {
            $relevance = $this->config->get('mlseo_product_related_relevance');
          } else {
            $relevance = 2;
          }
          
          if ($this->config->get('mlseo_product_related_no')) {
            $max_items = $this->config->get('mlseo_product_related_no');
          } else {
            $max_items = 5;
          }
          
          $rel_count = $this->db->query("SELECT COUNT(*) AS count FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int) $product_id . "'")->row;
          
          if (isset($rel_count['count']) && $rel_count['count'] < $max_items) {
            $max_items -= $rel_count['count'];
            
            $same_cat = '';
            $process_related = true;
            
            if ($this->config->get('mlseo_product_related_samecat')) {
              if (!empty($data['product_category'])) {
                $data['product_category'] = array_map('intval', $data['product_category']);
                
                $rel_cat = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "category_path WHERE category_id IN (".implode(',', $data['product_category']).") ORDER BY level DESC LIMIT 1")->row;
                
                if (!empty($rel_cat['category_id'])) {
                  $same_cat = "AND pc.category_id = " . (int) $rel_cat['category_id'];
                }
              } else {
                $process_related = false;
              }
            }
            
            if ($process_related) {
              $rel_query = $this->db->query("SELECT DISTINCT ROUND(MATCH (pd.name, pd.description) AGAINST ('" . $prod_name . " " . $prod_tag . " " . $prod_desc . "'), 0) / 5 as relevance,  p.product_id FROM " . DB_PREFIX . "product_description pd LEFT JOIN " . DB_PREFIX . "product p on pd.product_id = p.product_id INNER JOIN " . DB_PREFIX . "product_to_category pc on pd.product_id = pc.product_id WHERE p.product_id <> " . (int) $product_id . " " . $same_cat . "  AND p.status = 1 GROUP BY p.product_id HAVING relevance >= " . (int) $relevance . " ORDER BY relevance DESC LIMIT 0, " . (int) $max_items)->rows;
              
              foreach ($rel_query as $rel) {
                $data['product_related'][] = $rel['product_id'];
              }
            }
            
            if (isset($data["product_related"])) {
              foreach ($data["product_related"] as $related_id) {
                // only insert related to current product to avoid inserting too much related products
                $this->db->query("DELETE FROM `" . DB_PREFIX . "product_related` WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
                $this->db->query("INSERT INTO `" . DB_PREFIX . "product_related` SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
              }
            }
          }
        } else
      
		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $value) {
				if ((int)$value['points'] > 0) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$value['points'] . "'");
				}
			}
		}

		// SEO URL
		
      if (!$this->config->get('mlseo_enabled')) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$product_id . "'");
      }
      

		if (isset($data['product_seo_url'])) {
			foreach ($data['product_seo_url']as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($keyword) . "'");
                    } else {
                        if($language_id == 1) {
                            $seo_url = $data['product_description'][2]['name'] . '_en';
                            if(isset($data['product_description'][1]['name']) && $data['product_description'][1]['name'] != $data['product_description'][2]['name']) {
                                $seo_url = $data['product_description'][1]['name'];
                            }
                        }
                        if($language_id == 2) {
                            $seo_url = $data['product_description'][2]['name'];
                        }
                        if($language_id == 3) {
                            $seo_url = $data['product_description'][2]['name'] . '_ua';
                            if(isset($data['product_description'][3]['name']) && $data['product_description'][3]['name'] != $data['product_description'][2]['name']) {
                                $seo_url = $data['product_description'][3]['name'];
                            }
                        }

                        $this->translit->setSeoURL('product_id', $language_id, (int)$product_id, $seo_url);
                    }
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		
        $this->cache->delete('product');
        if($this->config->get('module_oc_cache_status') && file_exists(DIR_APPLICATION . '../vendor/autoload.php')){
          $webkulcache = $this->load->library('cart/webkulcache');
          $this->webkulcache->deleteCache(array('product','product_images','product_options'), 'files');
        }
      
	}

    public function editProduct1c($product_id, $data) {
	    $stock_status_id = $data['quantity'] > 0 ? 7 : 6;

        $this->db->query("UPDATE " . DB_PREFIX . "product SET 
                            quantity = '" . (int)$data['quantity'] . "', 
                            price = '" . (float)$data['price'] . "', 
                            stock_status_id = '" . $stock_status_id . "',
                            date_modified = NOW() 
                            WHERE product_id = '" . (int)$product_id . "'");

        
        $this->cache->delete('product');
        if($this->config->get('module_oc_cache_status') && file_exists(DIR_APPLICATION . '../vendor/autoload.php')){
          $webkulcache = $this->load->library('cart/webkulcache');
          $this->webkulcache->deleteCache(array('product','product_images','product_options'), 'files');
        }
      
    }

	public function copyProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p WHERE p.product_id = '" . (int)$product_id . "'");

		if ($query->num_rows) {
			$data = $query->row;

			$data['sku'] = '';
			$data['upc'] = '';
			$data['video'] = '';
			$data['viewed'] = '0';
			$data['keyword'] = '';
			$data['status'] = '0';

			$data['product_attribute'] = $this->getProductAttributes($product_id);

 		// OCFilter start
		$this->load->model('catalog/ocfilter');

		$data['ocfilter_product_option'] = $this->model_catalog_ocfilter->getProductOCFilterValues($product_id);
		// OCFilter end
      
			$data['product_description'] = $this->getProductDescriptions($product_id);
			$data['product_discount'] = $this->getProductDiscounts($product_id);
			$data['product_filter'] = $this->getProductFilters($product_id);
			$data['product_image'] = $this->getProductImages($product_id);
			$data['product_option'] = $this->getProductOptions($product_id);
			$data['product_related'] = $this->getProductRelated($product_id);
			$data['product_reward'] = $this->getProductRewards($product_id);
			$data['product_promo'] = $this->getProductPromo($product_id);
			$data['product_special'] = $this->getProductSpecials($product_id);
			$data['product_category'] = $this->getProductCategories($product_id);
			$data['product_download'] = $this->getProductDownloads($product_id);
			$data['product_layout'] = $this->getProductLayouts($product_id);
			$data['product_store'] = $this->getProductStores($product_id);
			$data['product_recurrings'] = $this->getRecurrings($product_id);

			$this->addProduct($data);
		}
	}

	public function deleteProduct($product_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");

		// OCFilter start
		$this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_option_value_to_product WHERE product_id = '" . (int)$product_id . "'");
		// OCFilter end
      
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_1c WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_recurring WHERE product_id = " . (int)$product_id);
		$this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_product WHERE product_id = '" . (int)$product_id . "'");

		
        $this->cache->delete('product');
        if($this->config->get('module_oc_cache_status') && file_exists(DIR_APPLICATION . '../vendor/autoload.php')){
          $webkulcache = $this->load->library('cart/webkulcache');
          $this->webkulcache->deleteCache(array('product','product_images','product_options'), 'files');
        }
      
	}

	public function getProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getProducts($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";



		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
		}

		if (!empty($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_quantity']) && $data['filter_quantity'] !== '') {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		$sql .= " GROUP BY p.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.price',
			'p.quantity',
			'p.status',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pd.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		//print_r($sql);

		$query = $this->db->query($sql);

		return $query->rows;


	}

	public function getProductsByCategoryId($category_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2c.category_id = '" . (int)$category_id . "' ORDER BY pd.name ASC");

		return $query->rows;
	}

	public function getProductDescriptions($product_id) {
		$product_description_data = array();

		
      $extra_select = '';
      
      $url_alias_table = version_compare(VERSION, '3', '>=') ? 'seo_url' : 'url_alias';
      
      if ($this->config->get('mlseo_enabled')) {
        if (version_compare(VERSION, '3', '>=') || ($this->config->get('mlseo_multistore') && $this->config->get('mlseo_ml_mode'))) {
          $extra_select = ",(SELECT keyword FROM " . DB_PREFIX . $url_alias_table . " u WHERE query = 'product_id=".$product_id."' AND (u.language_id = d.language_id OR u.language_id = 0) AND store_id = 0 LIMIT 1) AS seo_keyword";
        } else if ($this->config->get('mlseo_multistore')) {
          $extra_select = ",(SELECT keyword FROM " . DB_PREFIX . $url_alias_table . " u WHERE query = 'product_id=".$product_id."' AND store_id = 0 LIMIT 1) AS seo_keyword";
        } else if ($this->config->get('mlseo_ml_mode')) {
          $extra_select = ",(SELECT keyword FROM " . DB_PREFIX . $url_alias_table . " u WHERE query = 'product_id=".$product_id."' AND (u.language_id = d.language_id OR u.language_id = 0) LIMIT 1) AS seo_keyword";
        } else {
          $extra_select = ",(SELECT keyword FROM " . DB_PREFIX . $url_alias_table . " WHERE query = 'product_id=".$product_id."' LIMIT 1) AS seo_keyword";
        }
      }
      
      $query = $this->db->query("SELECT * ".$extra_select." FROM " . DB_PREFIX . "product_description d WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
        'meta_title'     => isset($result['meta_title']) ? $result['meta_title'] : '',
        'seo_keyword'     => isset($result['seo_keyword']) ? $result['seo_keyword'] : '',
        'seo_h1'       => isset($result['seo_h1']) ? $result['seo_h1'] : '',
        'seo_h2'       => isset($result['seo_h2']) ? $result['seo_h2'] : '',
        'seo_h3'       => isset($result['seo_h3']) ? $result['seo_h3'] : '',
        'image_alt'       => isset($result['image_alt']) ? $result['image_alt'] : '',
        'image_title'       => isset($result['image_title']) ? $result['image_title'] : '',
				'description'      => $result['description'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'tag'              => $result['tag']
			);
		}

		return $product_description_data;
	}

	public function getProductCategories($product_id) {
		$product_category_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_category_data[] = $result['category_id'];
		}

		return $product_category_data;
	}

	public function getProductFilters($product_id) {
		$product_filter_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_filter_data[] = $result['filter_id'];
		}

		return $product_filter_data;
	}

	public function getProductAttributes($product_id) {
		$product_attribute_data = array();

		$product_attribute_query = $this->db->query("SELECT attribute_id FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' GROUP BY attribute_id");

		foreach ($product_attribute_query->rows as $product_attribute) {
			$product_attribute_description_data = array();

			$product_attribute_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

			foreach ($product_attribute_description_query->rows as $product_attribute_description) {
				$product_attribute_description_data[$product_attribute_description['language_id']] = array('text' => $product_attribute_description['text']);
			}

			$product_attribute_data[] = array(
				'attribute_id'                  => $product_attribute['attribute_id'],
				'product_attribute_description' => $product_attribute_description_data
			);
		}

		return $product_attribute_data;
	}

	public function getProductOptions($product_id) {
		$product_option_data = array();

		$product_option_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option` po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($product_option_query->rows as $product_option) {
			$product_option_value_data = array();

			$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON(pov.option_value_id = ov.option_value_id) WHERE pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' ORDER BY ov.sort_order ASC");

			foreach ($product_option_value_query->rows as $product_option_value) {
				$product_option_value_data[] = array(
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id'         => $product_option_value['option_value_id'],
					'quantity'                => $product_option_value['quantity'],
					'subtract'                => $product_option_value['subtract'],
					'price'                   => $product_option_value['price'],
					'price_prefix'            => $product_option_value['price_prefix'],
					'points'                  => $product_option_value['points'],
					'points_prefix'           => $product_option_value['points_prefix'],
					'weight'                  => $product_option_value['weight'],
					'weight_prefix'           => $product_option_value['weight_prefix']
				);
			}

			$product_option_data[] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => $product_option['value'],
				'required'             => $product_option['required']
			);
		}

		return $product_option_data;
	}

	public function getProductOptionValue($product_id, $product_option_value_id) {
		$query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getProductImages($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getProductPromo($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_promo WHERE product_id = '" . (int)$product_id . "'");

		return $query->rows;
	}

	public function getProductDiscounts($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' ORDER BY quantity, priority, price");

		return $query->rows;
	}

	public function getProductSpecials($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' ORDER BY priority, price");

		return $query->rows;
	}

	public function getProductRewards($product_id) {
		$product_reward_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_reward_data[$result['customer_group_id']] = array('points' => $result['points']);
		}

		return $product_reward_data;
	}

	public function getProductDownloads($product_id) {
		$product_download_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_download_data[] = $result['download_id'];
		}

		return $product_download_data;
	}

	public function getProductStores($product_id) {
		$product_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_store_data[] = $result['store_id'];
		}

		return $product_store_data;
	}

	public function getProductSeoUrls($product_id) {
		$product_seo_url_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $product_seo_url_data;
	}

	public function getProductLayouts($product_id) {
		$product_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $product_layout_data;
	}

	public function getProductMainCategoryId($product_id) {
		$query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' AND main_category = '1' LIMIT 1");

		return ($query->num_rows ? (int)$query->row['category_id'] : 0);
	}

	public function getProductRelated($product_id) {
		$product_related_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_related_data[] = $result['related_id'];
		}

		return $product_related_data;
	}

	public function getRecurrings($product_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = '" . (int)$product_id . "'");

		return $query->rows;
	}

	public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";

		$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
		}

		if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_quantity']) && $data['filter_quantity'] !== '') {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalProductsByTaxClassId($tax_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE tax_class_id = '" . (int)$tax_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByStockStatusId($stock_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE stock_status_id = '" . (int)$stock_status_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByWeightClassId($weight_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE weight_class_id = '" . (int)$weight_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByLengthClassId($length_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE length_class_id = '" . (int)$length_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByDownloadId($download_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_download WHERE download_id = '" . (int)$download_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByManufacturerId($manufacturer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByAttributeId($attribute_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_attribute WHERE attribute_id = '" . (int)$attribute_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByOptionId($option_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_option WHERE option_id = '" . (int)$option_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByProfileId($recurring_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_recurring WHERE recurring_id = '" . (int)$recurring_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}

	public function getNewProductId() {
		$query = $this->db->query("SHOW TABLE STATUS WHERE `Name` = '" . DB_PREFIX . "product'");

		return $query->row['Auto_increment'];
	}
}
