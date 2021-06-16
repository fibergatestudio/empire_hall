<?php
class ControllerExtensionModuleHTML extends Controller {
	public function index($setting) { 
		if (isset($setting['module_description'][$this->config->get('config_language_id')])) {
			$data['heading_title'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['title'], ENT_QUOTES, 'UTF-8');
			$data['html'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['description'], ENT_QUOTES, 'UTF-8');
			$data['layout'] = html_entity_decode($setting['layout'], ENT_QUOTES, 'UTF-8');

            if (isset($setting['image'])) {
                $data['image'] = $this->config->get('config_url') . 'image/' . $setting['image'];
            } else {
                $data['image'] = false;
            }

			if(isset($setting['product_desc_img']) && $setting['product_desc_img']){
				$data['triggers'] = $setting['product_desc_img'][$this->config->get('config_language_id')];
                for($i = 0; $i< count($data['triggers']); $i++){
                    $data['triggers'][$i]['description'] = html_entity_decode($data['triggers'][$i]['description'], ENT_QUOTES, 'UTF-8');
                }
			} else {
				$data['triggers'] = array();
			}

			if($data['layout']){
				$tpl = $data['layout'];
			} else {
				$tpl = 'html';
			}

			$data['about_link'] = $this->url->link('information/information', 'information_id=4');

			return $this->load->view('extension/module/'.$tpl, $data);
		}
	}
}