<?php
class ControllerExtensionModuleManufacturer extends Controller {
    public function index() {
        $this->load->model('catalog/manufacturer');

        $data['brands'] = array();
        $this->load->model('tool/image');
        $curr_lang =  $this->language->get('code');

        $manufacturers = $this->model_catalog_manufacturer->getManufacturers();

        foreach ($manufacturers as $result) {
            if (is_file(DIR_IMAGE . $result['image'])) {
                $data['brands'][] = array(
                    'name'   => $result['name'],
                    'href'   => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id']),
                    //'image'  => HTTPS_SERVER . 'image/' . $result['image'],
                    'image'  => $this->model_tool_image->resize($result['image'], 158, 79)
                );
            }
        }

        return $this->load->view('extension/module/manufacturer', $data);
    }
}