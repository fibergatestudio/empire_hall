<?php
class ControllerExtensionModuleCategory extends Controller {
    public function index() {
        $this->load->language('extension/module/category');

        if (isset($this->request->get['path'])) {
            $parts = explode('_', (string)$this->request->get['path']);
        } else {
            $parts = array();
        }

        if (isset($parts[0])) {
            $data['category_id'] = $parts[0];
        } else {
            $data['category_id'] = 0;
        }

        if (isset($parts[1])) {
            $data['child_id'] = $parts[1];
        } else {
            $data['child_id'] = 0;
        }
        if (isset($parts[2])) {
            $data['third_id'] = $parts[2];
        } else {
            $data['third_id'] = 0;
        }        if (isset($parts[3])) {
            $data['fourth_id'] = $parts[3];
        } else {
            $data['fourth_id'] = 0;
        }



        $this->load->model('catalog/category');

        $this->load->model('catalog/product');

        $data['categories'] = array();

        
        /**
          * opencart cache code start here
          */
        $category_cache_status = false;
        $lang_store_id = '.'.(int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id');
        if($this->config->get('module_oc_cache_status') && (isset($this->config->get('module_oc_cache_category')['status']) && $this->config->get('module_oc_cache_category')['status'])){
            $category_cache_status = true;
            static  $category_mod_container = array();
                    $getCacheInstance   = $this->webkulcache->get_InstanceCache('files');
                    $CachedString       = $getCacheInstance->getItem('category_module');
                    $category_mod_container = $CachedString->get('category_module'.$lang_store_id);
                    if(!isset($category_mod_container['category_module'.$lang_store_id])){
                        $categories = $this->model_catalog_category->getCategories(0);
                        $category_mod_container['category_module'.$lang_store_id] = $categories;
                        if($this->config->get('module_oc_cache_category')['expire'])
                          $CachedString->set($category_mod_container)->expiresAfter($this->config->get('module_oc_cache_category')['expire']);
                        else
                          $CachedString->set($category_mod_container);

                        $getCacheInstance->save($CachedString);
                    }else{
                        $categories = $category_mod_container['category_module'.$lang_store_id];
                    }
        }else{
            $categories = $this->model_catalog_category->getCategories(0);
        }
        /**
        * opencart cache code end here
        */
      

        foreach ($categories as $category) {
            if ($category['category_id'] != 157) {

                $children_data = array();

                
        /**
          * opencart cache code start here
          */
          if($this->config->get('module_oc_cache_status') && $category_cache_status){
            if(!isset($category_mod_container['chield_category'.$lang_store_id.'.'.$category['category_id']])){
                $children = $this->model_catalog_category->getCategories($category['category_id']);
                if($children){
                  foreach ($children as $key => $cat) {
                      $category_mod_container['chield_category'.$lang_store_id.'.'.$category['category_id']][$cat['category_id']] = $children[$key];
                  }
                }
              if($this->config->get('module_oc_cache_category')['expire'])
                $CachedString->set($category_mod_container)->expiresAfter($this->config->get('module_oc_cache_category')['expire']);
              else
                $CachedString->set($category_mod_container);

                $getCacheInstance->save($CachedString);
            }else{
                $children = $category_mod_container['chield_category'.$lang_store_id.'.'.$category['category_id']];
            }
          }else{
            $children = $this->model_catalog_category->getCategories($category['category_id']);
          }
          /**
          * opencart cache code end here
          */
      

                foreach ($children as $child) {
                    $chl_third = array();
                    $children_third = $this->model_catalog_category->getCategories($child['category_id']);

                    foreach ($children_third as $child_third) {

                        $children_data_fourth = array();
                        $childs_fourth = $this->model_catalog_category->getCategories($child_third['category_id']);

                        foreach ($childs_fourth as $fourth) {
                            $children_data_fourth[] = array(
                                'category_id' => $fourth['category_id'],
                                'name' => $fourth['name'],
                                'href' => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'] . '_' . $child_third['category_id'] . '_' . $fourth['category_id'])
                            );
                        }

                        $chl_third[] = array(
                            'category_id' => $child_third['category_id'],
                            'name' => $child_third['name'],
                            'children_fourth' => $children_data_fourth,
                            'href' => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'] . '_' . $child_third['category_id'])
                        );
                    }

                    $children_data[] = array(
                        'category_id' => $child['category_id'],
                        'name' => $child['name'],
                        'chl_third' => $chl_third,
                        'href' => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
                    );
                }

                $data['categories'][] = array(
                    'category_id' => $category['category_id'],
                    'name' => $category['name'],
                    'children' => $children_data,
                    'href' => $this->url->link('product/category', 'path=' . $category['category_id'])
                );
            }
        }



        return $this->load->view('extension/module/category', $data);
    }
}