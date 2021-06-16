<?php

class ControllerCommonHome extends Controller
{
    public function index()
    {
        $this->document->setTitle(isset($this->config->get('config_meta_title')[$this->config->get('config_language_id')])?$this->config->get('config_meta_title')[$this->config->get('config_language_id')]:'');
        $this->document->setDescription(isset($this->config->get('config_meta_description')[$this->config->get('config_language_id')])?$this->config->get('config_meta_description')[$this->config->get('config_language_id')]:'');
        $this->document->setKeywords(isset($this->config->get('config_meta_keyword')[$this->config->get('config_language_id')])?$this->config->get('config_meta_keyword')[$this->config->get('config_language_id')]:'');

  // seo meta (overwrites the previously defined)
  if ($this->config->get('mlseo_enabled')) {
    $seo_meta = $this->config->get('mlseo_store');
    
    /*
    if (!empty($seo_meta[$this->config->get('config_store_id')]['analytics'])) {
      $ggAnalyticsCode = html_entity_decode($seo_meta[$this->config->get('config_store_id')]['analytics'], ENT_QUOTES, 'UTF-8');
      if (strpos($ggAnalyticsCode, 'google-site-verification')) {
        $this->document->addSeoMeta($ggAnalyticsCode."\n");
      } else if (strpos($ggAnalyticsCode, 'script')) {
        $this->document->addSeoMeta($ggAnalyticsCode."\n");
      } else {
        $this->document->addSeoMeta('<meta name="google-site-verification" content="'.$ggAnalyticsCode.'">'."\n");
      }
    }
    */
    
    if (isset($seo_meta[$this->config->get('config_store_id').$this->config->get('config_language_id')])) {
      $seo_meta = $seo_meta[$this->config->get('config_store_id').$this->config->get('config_language_id')];
    }
    
    if (!empty($seo_meta['seo_title'])) {
      ${'this'}->document->setTitle($seo_meta['seo_title']);
    } else if ($this->config->get('config_meta_title')) {
      ${'this'}->document->setTitle($this->config->get('config_meta_title'));
    } else {
      ${'this'}->document->setTitle($this->config->get('config_title'));
    }
    
    if (!empty($seo_meta['description'])) {
      ${'this'}->document->setDescription($seo_meta['description']);
    } else {
      ${'this'}->document->setDescription($this->config->get('config_meta_description'));
    }
    
    if (!empty($seo_meta['keywords'])) {
      ${'this'}->document->setKeywords($seo_meta['keywords']);
    }
    
    if (version_compare(VERSION, '2', '>=')) {
      if (!empty($seo_meta['title'])) {
        $data['heading_title'] = $data['seo_h1'] = $seo_meta['title'];
      } else if ($this->config->get('config_meta_title')) {
        $data['heading_title'] = $data['seo_h1'] = $this->config->get('config_meta_title');
      } else {
        $data['heading_title'] = $data['seo_h1'] = $this->config->get('config_title');
      }
      $data['heading_title'] = $data['seo_h1'] = !empty($seo_meta['title']) ? $seo_meta['title'] : $this->config->get('config_title');
      $data['seo_h2'] = !empty($seo_meta['h2']) ? $seo_meta['h2'] : '';
      $data['seo_h3'] = !empty($seo_meta['h3']) ? $seo_meta['h3'] : '';
    } else {
      $this->data['heading_title'] = $this->data['seo_h1'] = !empty($seo_meta['title']) ? $seo_meta['title'] : $this->config->get('config_title');
      $this->data['seo_h2'] = !empty($seo_meta['h2']) ? $seo_meta['h2'] : '';
      $this->data['seo_h3'] = !empty($seo_meta['h3']) ? $seo_meta['h3'] : '';
    }
  }
  /* now defined in header ctrl
  $this->load->model('tool/seo_package');
  
  if ($this->config->get('mlseo_opengraph')) {
    if (version_compare(VERSION, '2', '>=')) {
      $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('opengraph', 'home'));
    } else {
      $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('opengraph', 'home'));
    }
  }
  
  if ($this->config->get('mlseo_tcard')) {
    if (version_compare(VERSION, '2', '>=')) {
      $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('tcard', 'home'));
    } else {
      $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('tcard', 'home'));
    }
  }
  
  if ($this->config->get('mlseo_gpublisher')) {
    if (version_compare(VERSION, '2', '>=')) {
      $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('gpublisher', 'home'));
    } else {
      $this->document->addSeoMeta($this->model_tool_seo_package->rich_snippet('gpublisher', 'home'));
    }
  }
  */
  // end - seo meta
      

        $this->load->model('tool/image');
        $this->document->setOGTitle(isset($this->config->get('config_og_title')[$this->config->get('config_language_id')])?$this->config->get('config_og_title')[$this->config->get('config_language_id')]:'');
        $this->document->setOGDescription(isset($this->config->get('config_og_desc')[$this->config->get('config_language_id')])?$this->config->get('config_og_desc')[$this->config->get('config_language_id')]:'');
        $this->document->setOGImage(($this->config->get('config_og_image'))?$this->model_tool_image->resize($this->config->get('config_og_image'), 400, 300):'');

        if (isset($this->request->get['route'])) {
            $this->document->addLink(rtrim($this->config->get('config_url'), '/'), 'canonical');
        }
        $curr_lang =  $this->language->get('code');
        $data['catalog_link'] = $this->url->link('product/category', 'path=1', true);

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $store_ids = $this->db->query("SELECT store_id FROM " . DB_PREFIX . "store");
        $language_ids = $this->db->query("SELECT * FROM " . DB_PREFIX . "language");

        $seourl = array(
            'common/home' => '',
            'account/wishlist' => 'wishlist',
            'account/account' => 'account',
            'checkout/cart' => 'cart',
            'checkout/checkout' => 'checkout',
            'account/login' => 'login',
            'account/logout' => 'logout',
            'account/order' => 'order-history',
            'account/order/info' => 'order-information',
            'account/newsletter' => 'newsletter',
            'product/special' => 'specials',
            'affiliate/account' => 'affiliates',
            'account/voucher' => 'gift-vouchers',
            'account/recurring' => 'recurring-payments',
            'product/manufacturer' => 'brands',
            'information/contact' => 'contact-us',
            'account/return/add' => 'request-return',
            'information/sitemap' => 'sitemap',
            'account/forgotten' => 'forgot-password',
            'account/download' => 'downloads',
            'account/return' => 'returns',
            'account/transaction' => 'transactions',
            'account/register' => 'create-account',
            'product/compare' => 'compare-products',
            'product/search' => 'search',
            'account/edit' => 'edit-account',
            'account/password' => 'change-password',
            'account/address' => 'address-book',
            'account/address/edit' => 'edit-address',
            'account/address/add' => 'add-address',
            'account/address/delete' => 'delete-address',
            'account/reward' => 'reward-points',
            'affiliate/edit' => 'edit-affiliate-account',
            'affiliate/password' => 'change-affiliate-password',
            'affiliate/payment' => 'affiliate-payment-options',
            'affiliate/tracking' => 'affiliate-tracking-code',
            'affiliate/transaction' => 'affiliate-transactions',
            'affiliate/logout' => 'affiliate-logout',
            'affiliate/forgotten' => 'affiliate-forgot-password',
            'affiliate/register' => 'create-affiliate-account',
            'affiliate/login' => 'affiliate-login'
        );
        foreach ($language_ids->rows as $key => $language) {
            foreach ($store_ids->rows as $key => $store) {
                # code...

                foreach ($seourl as $query => $keyword) {
                    $qu = $this->db->query("SELECT `query` FROM " . DB_PREFIX . "seo_url WHERE `query`='" . $query . "' ");
                    //if ($qu->num_rows == 0) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url (query,store_id,language_id keyword) VALUES ('" . $this->db->escape($query) . "','" . (int)$store['store_id'] . "','" . (int)$language['language_id'] . "', '" . $this->db->escape($keyword) . "')");
                    //}
                }
            }
        }


        if($this->cache->get('home.index.getBanner_main'."_".$curr_lang)){
            $results = $this->cache->get('home.index.getBanner_main'."_".$curr_lang);
        }else{
			$this->load->model('design/banner');
            $results = $this->model_design_banner->getBanner(11);
            $this->cache->set('home.index.getBanner_main'."_".$curr_lang, $results);
        }

        if($results){
            foreach ($results as $result) {
                if (is_file(DIR_IMAGE . $result['image'])) {
                    $data['popaps_banners'] = array(
                        'title' => $result['title'],
                        'description' => html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
                        'link'  => $result['link'],
                        'image' =>  HTTPS_SERVER . 'image/' . $result['image'],
                        'title_button' =>  $result['title_button']
                    );
                }
            }
        }

        if(isset($this->session->data['popaps_login'])){
            $data['popaps_login'] = $this->session->data['popaps_login'];
        }else{
            $data['popaps_login'] = 0;
        }
        unset($this->session->data['popaps_login']);

        $this->response->setOutput($this->load->view('common/home', $data));
    }
}
