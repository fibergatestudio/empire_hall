<?php
class ControllerExtensionModuleInstagram extends Controller {
    public function index($setting) {
		/*
        $this->load->model('tool/image');

        $this->load->language('extension/module/instagram');

        $data['heading_title'] = $setting['title'][$this->config->get('config_language_id')];

        $data['instagram_link'] = $this->config->get('config_instagram');

        $data['user_id'] = $setting['user_id'];
        $data['token'] = $setting['token'];
        $data['width'] = $setting['width'];
        $data['height'] = $setting['height'];
        $data['hashtag'] = $setting['hashtag'];
        $data['count'] = $setting['qty'];
        $data['images'] = $this->cache->get('insta_images');
        if (!$data['images']) {
            $instagram_cnct = curl_init(); // инициализация cURL подключения
            curl_setopt($instagram_cnct, CURLOPT_URL, "https://graph.instagram.com/me/media?access_token=" . $data['token']);
            curl_setopt($instagram_cnct, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($instagram_cnct, CURLOPT_TIMEOUT, 9);
            curl_setopt($instagram_cnct, CURLOPT_CONNECTTIMEOUT, 9);
            $media = json_decode(curl_exec($instagram_cnct));

            foreach (array_slice($media->data, 0, $data['count']) as $id) {

                $item = curl_init();
                curl_setopt($item, CURLOPT_URL, "https://graph.instagram.com/" . $id->id . "?fields=media_url,caption,permalink&access_token=" . $data['token']);
                curl_setopt($item, CURLOPT_HEADER, false);
                curl_setopt($item, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($instagram_cnct, CURLOPT_TIMEOUT, 9);
                curl_setopt($instagram_cnct, CURLOPT_CONNECTTIMEOUT, 9);

                $img[$id->id] = json_decode(curl_exec($item));
            }


            foreach ($img as $val) {

                $data['images'][] = array(
                    'link' => $val->permalink,
                    'img' => $val->media_url,
                    'description' => $val->caption
                );
            }
            curl_close($item);
            curl_close($instagram_cnct);
            $this->cache->set('insta_images', $data['images']);
        }

        
		*/
		
		return $this->load->view('extension/module/instagram', $data);
    }
}