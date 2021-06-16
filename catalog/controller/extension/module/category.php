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

        $categories = $this->model_catalog_category->getCategories(0);

        foreach ($categories as $category) {
            if ($category['category_id'] != 157) {

                $children_data = array();

                $children = $this->model_catalog_category->getCategories($category['category_id']);

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