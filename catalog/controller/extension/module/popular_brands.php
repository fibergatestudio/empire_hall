<?php
class ControllerExtensionModulePopularBrands extends Controller {
    public function index($setting) {

        $this->load->model('catalog/manufacturer');

        $this->load->model('tool/image');

        $data['brands'] = array();

        if (!$setting['limit']) {
            $setting['limit'] = 4;
        }

        $module_brands = array();

        if (!empty($setting['brand'])) {
            $brands = array_slice($setting['brand'], 0, (int)$setting['limit']);

            foreach ($brands as $brand_id) {
                $brand_info = $this->model_catalog_manufacturer->getManufacturer($brand_id);

                if (isset($brand_info['image2']) && is_file(DIR_IMAGE . $brand_info['image2'])) {
                    $image = HTTPS_SERVER . 'image/' . $brand_info['image2'];
                } else {
                    $image = $this->model_tool_image->resize('placeholder.png', 158, 79);
                }
if($brand_info){


                $data['brands'][] = array(
                    'name'  => (isset($brand_info['name'])?$brand_info['name']:''),
                    'image' => $image,
                    'subtext' => (isset($brand_info['subtext'])?html_entity_decode($brand_info['subtext']):''),
                    'href'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $brand_info['manufacturer_id'])
                );
            }

                if ($brand_info) {
                    $module_brands[] = $brand_info;
                }
            }
        }

        if ($module_brands) {
            return $this->load->view('extension/module/popular_brands', $data);
        }
    }
}