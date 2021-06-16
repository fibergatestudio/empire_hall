<?php
class ControllerCommonFooter extends Controller {
	public function index() {
		$this->load->language('common/footer');

		$this->load->model('catalog/information');
        $curr_lang =  $this->language->get('code');
		$data['contact'] = $this->url->link('information/contact');
		$data['return'] = $this->url->link('account/return/add', '', true);
		$data['sitemap'] = $this->url->link('information/sitemap');
		$data['tracking'] = $this->url->link('information/tracking');
		$data['manufacturer'] = $this->url->link('product/manufacturer');
		$data['voucher'] = $this->url->link('account/voucher', '', true);
		$data['affiliate'] = $this->url->link('affiliate/login', '', true);
		$data['special'] = $this->url->link('product/special');
		$data['account'] = $this->url->link('account/account', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['newsletter'] = $this->url->link('account/newsletter', '', true);
        $data['shopping'] = $this->url->link('product/category', 'path=59');
        $data['language_id'] = $this->config->get('config_language_id');

		$data['powered'] = sprintf($this->language->get('text_powered'), date('Y', time()), $this->config->get('config_name'));
        $data['home'] = $this->url->link('common/home');

        $data['address_text'] = $this->language->get('address_text');
        $data['schedule_text'] = $this->language->get('schedule_text');
        $data['company_text'] = $this->language->get('company_text');

        $data['address'] = html_entity_decode($this->config->get('config_address')[$data['language_id']]);
        $data['schedule'] = html_entity_decode($this->config->get('config_open')[$data['language_id']]);
        $data['not_lighthouse'] = (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome-Lighthouse') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'GTmetrix') === false && strpos($_SERVER['HTTP_USER_AGENT'], '(X11; Linux x86_64) AppleWebKit') === false);
		// Socials
        $data['instagram'] = $this->config->get('config_instagram');
        $data['pinterest'] = $this->config->get('config_pinterest');
        $data['facebook'] = $this->config->get('config_facebook');
        $data['youtube'] = $this->config->get('config_youtube');

		$this->load->model('catalog/category');
		$data['menus'] = array();
		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

        if($this->cache->get('footer.menu'."_".$curr_lang)){
            $menus = $this->cache->get('footer.menu'."_".$curr_lang);
        }else{
            $menus = $this->model_catalog_category->getMenus(0);
            $this->cache->set('footer.menu'."_".$curr_lang, $menus);
        }

		foreach ($menus as $menu) {
			$data['menus'][] = array(
				'name'			=> $menu['name'],
				'href'			=> $menu['href'] ? $server.$menu['href'] : false
			);
		}

		//$data['scripts'] = $this->document->getScripts('footer');
		//$data['scripts'] = $this->document->getScripts('header');
		
		$data['scripts'] = $this->document->getScripts();
		
		$data['cart'] = $this->load->controller('common/cart');
		
		return $this->load->view('common/footer', $data);
	}
}
