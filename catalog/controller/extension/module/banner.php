<?php
class ControllerExtensionModuleBanner extends Controller {
	public function index($setting) {
		static $module = 0;

		$this->load->model('design/banner');
		$this->load->model('tool/image');


        $curr_lang =  $this->language->get('code');

		$data['banners'] = array();

        if($this->cache->get('banner.banner_'.$setting['banner_id']."_".$curr_lang)){
            $results = $this->cache->get('banner.banner_'.$setting['banner_id']."_".$curr_lang);
        }else{
            $results = $this->model_design_banner->getBanner($setting['banner_id']);
            $this->cache->set('banner.banner_'.$setting['banner_id']."_".$curr_lang, $results);
        }

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
                $image_sm = $result['image_sm'];
                if (empty($image_sm) || !is_file(DIR_IMAGE . $result['image_sm'])){
                    $image_sm = $result['image'];
                }

                $image_md = $result['image_md'];
                if (empty($image_md) || !is_file(DIR_IMAGE . $result['image_md'])){
                    $image_md = $result['image'];
                }

                $image_lg = $result['image_lg'];
                if (empty($image_lg) || !is_file(DIR_IMAGE . $result['image_lg'])){
                    $image_lg = $result['image'];
                }

				$data['banners'][] = array(
					'title' => $result['title'],
					'description' => html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
					'link'  => $result['link'],
				//	'image' => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height'])
					'image' =>  HTTPS_SERVER . 'image/' . $result['image'],
                    'image_sm' => HTTPS_SERVER . '/image/' . $image_sm,
                    'image_md' => HTTPS_SERVER . '/image/' . $image_md,
                    'image_lg' => HTTPS_SERVER . '/image/' . $image_lg
				);
			}
		}

		$data['module'] = $module++;

		return $this->load->view('extension/module/banner', $data);
	}
}