<?php
class ControllerExtensionModuleCategoryHome extends Controller {
    public function index() {
        $this->load->language('extension/module/category_home');

        $this->load->model('catalog/category');

        $this->load->model('catalog/product');

        $curr_lang =  $this->language->get('code');

        $data['categories'] = array();
        $data['categories_link'] = $this->url->link('product/category', 'path=1');


        if($this->cache->get('module.category_home.getCategories'."_".$curr_lang)){
            $categories = $this->cache->get('module.category_home.getCategories'."_".$curr_lang);
        }else{
            $categories = $this->model_catalog_category->getCategories(0);
            $this->cache->set('module.category_home.getCategories'."_".$curr_lang, $categories);
        }

        foreach ($categories as $category) {
            if($this->cache->get('module.category_home.children'.$category['category_id']."_".$curr_lang)){
                $children = $this->cache->get('module.category_home.children'.$category['category_id']."_".$curr_lang);
            }else{
                $children = $this->model_catalog_category->getCategories($category['category_id']);
                $this->cache->set('module.category_home.children'.$category['category_id']."_".$curr_lang, $children);
            }

            foreach($children as $child) {

                if (is_file(DIR_IMAGE . $child['image'])) {
                    $image = $this->model_tool_image->cropsize($child['image'], 430, 524);
                } else {
                    $image = $this->model_tool_image->cropsize('placeholder.png', 430, 524);
                }

                $data['categories'][] = array(
                    'category_id' => $child['category_id'],
                    'name'        => $child['name'],
                    'top'        => $child['top'],
                    'image'       => $image,
                    'href'        => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
                );

            }
      
        }

        return $this->load->view('extension/module/category_home', $data);
    }
}