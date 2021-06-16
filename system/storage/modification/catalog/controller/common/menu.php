<?php
class ControllerCommonMenu extends Controller {
	public function index() {
		$this->load->language('common/menu');

		// Menu
		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$data['categories'] = array();

		
                /**
                 * opencart cache code start here
                 */
                $category_cache_status = false;
                $lang_store_id = '.'.(int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id');
                if($this->config->get('module_oc_cache_status') && (isset($this->config->get('module_oc_cache_menu_layout')['status']) && $this->config->get('module_oc_cache_menu_layout')['status'])){
                    $category_cache_status = true;
                    static  $category_container = array();
                            $getCacheInstance   = $this->webkulcache->get_InstanceCache('files');
                            $CachedString       = $getCacheInstance->getItem('header_category');
                            $category_container = $CachedString->get('header_category'.$lang_store_id);
                            if(!isset($category_container['header_category'.$lang_store_id])){
                                $categories = $this->model_catalog_category->getCategories(0);
                                $category_container['header_category'.$lang_store_id] = $categories;
                                if($this->config->get('module_oc_cache_menu_layout')['expire'])
                                  $CachedString->set($category_container)->expiresAfter($this->config->get('module_oc_cache_menu_layout')['expire']);
                                else
                                  $CachedString->set($category_container);

                                $getCacheInstance->save($CachedString);
                            }else{
                                $categories = $category_container['header_category'.$lang_store_id];
                            }
                }else{
                    $categories = $this->model_catalog_category->getCategories(0);
                }
                /**
                * opencart cache code end here
                */
              

		foreach ($categories as $category) {
			if ($category['top']) {
				// Level 2
				$children_data = array();

				
                /**
                 * opencart cache code start here
                 */
                  if($this->config->get('module_oc_cache_status') && $category_cache_status){
                    if(!isset($category_container['chield_category'.$lang_store_id.'.'.$category['category_id']])){
                        $children = $this->model_catalog_category->getCategories($category['category_id']);
                        if($children){
                          foreach ($children as $key => $cat) {
                              $category_container['chield_category'.$lang_store_id.'.'.$category['category_id']][$cat['category_id']] = $children[$key];
                          }
                        }
                      if($this->config->get('module_oc_cache_menu_layout')['expire'])
                        $CachedString->set($category_container)->expiresAfter($this->config->get('module_oc_cache_menu_layout')['expire']);
                      else
                        $CachedString->set($category_container);

                        $getCacheInstance->save($CachedString);
                    }else{
                        $children = $category_container['chield_category'.$lang_store_id.'.'.$category['category_id']];
                    }
                  }else{
                    $children = $this->model_catalog_category->getCategories($category['category_id']);
                  }
                  /**
                  * opencart cache code end here
                  */
              

				foreach ($children as $child) {
					$filter_data = array(
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true
					);

					$children_data[] = array(
						'name'  => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
						'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
					);
				}

				// Level 1
				$data['categories'][] = array(
					'name'     => $category['name'],
					'children' => $children_data,
					'column'   => $category['column'] ? $category['column'] : 1,
					'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
				);
			}
		}

		return $this->load->view('common/menu', $data);
	}
}
