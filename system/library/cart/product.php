<?php

namespace Cart;

class Product extends \Controller
{
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->config = $registry->get('config');
        $this->customer = $registry->get('customer');
        $this->session = $registry->get('session');
        $this->db = $registry->get('db');
        $this->load = $registry->get('load');
        $this->tax = $registry->get('tax');
        $this->weight = $registry->get('weight');
        $this->url = $registry->get('url');
    }


    public function getProduct($data)
    {

        if (isset($data['product']['product_id'])) {


            if (isset($data['product']['image']) && is_file(DIR_IMAGE . $data['product']['image'])) {
                $image = $this->model_tool_image->resize($data['product']['image'], $data['width'], $data['height']);
				//$image = HTTPS_SERVER . 'image/' . $data['product']['image'];
            } else {
                $image = $this->model_tool_image->resize('placeholder.png', $data['width'], $data['height']);
            }

            if ($this->customer->isLogged() || (!$this->config->get('config_customer_price') && empty($data['product']['hide_price']))) {
                $price = $this->currency->format($this->tax->calculate($data['product']['price'], $data['product']['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $price = false;
            }
            //    $price = false;
            $percent = 0;

            if ((float)$data['product']['special'] && empty($data['product']['hide_price'])) {
                $special = $this->currency->format($this->tax->calculate($data['product']['special'], $data['product']['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                $fractionNum = $data['product']['price'] / 100;
                $fractionNum2 = $data['product']['special'] / $fractionNum;
                $percent = abs(round(100 - $fractionNum2));
            } else {
                $special = false;
            }

            $specialEnd = $this->model_catalog_product->getProductSpecialData($data['product']['product_id']);

            if ($specialEnd['date_end'] && time() < strtotime($specialEnd['date_end'])) {
                $specialEnd = $specialEnd['date_end'];
            } else {
                $specialEnd = false;
            }

            if ($this->config->get('config_tax')) {
                $tax = $this->currency->format((float)$data['product']['special'] ? $data['product']['special'] : $data['product']['price'], $this->session->data['currency']);
            } else {
                $tax = false;
            }

            if ($this->config->get('config_review_status')) {
                $rating = $data['product']['rating'];
            } else {
                $rating = false;
            }

            $promoProduct = $this->model_catalog_product->getPromoProduct($data['product']['product_id']);

            $promos = array();

            if ($promoProduct) {
                foreach ($promoProduct as $item) {
                    if ((strtotime(date('Y-m-d')) >= strtotime($item['date_start'])) && (strtotime(date('Y-m-d')) <= strtotime($item['date_end'])) || (($item['date_start'] == '0000-00-00') && ($item['date_end'] == '0000-00-00'))) {
                        $promos[] = $item;
                    }
                }
            }

            // Wishlist
            $is_wishlist = false;
            if ($this->customer->isLogged()) {
                $this->load->model('account/wishlist');
                $customer_wishlist = $this->model_account_wishlist->getWishlistProductId($data['product']['product_id']);

                if (in_array($data['product']['product_id'], $customer_wishlist)) {
                    $is_wishlist = true; //custom
                }
            } else {
                if (isset($this->session->data['wishlist'])) {
                    if (in_array($data['product']['product_id'], $this->session->data['wishlist'])) {
                        $is_wishlist = true; //custom
                    }
                }
            }
            $res = $this->model_catalog_product->getProductCollectionId($data['product']['product_id']);

            mb_internal_encoding('UTF-8');
            $string_name = strip_tags($data['product']['name']);
            $string_name = rtrim($string_name, "!,.-`");


            if ($res) {

                $products = array(
                    'product_id' => $data['product']['product_id'],
                    'thumb' => $image,
                    'image' => $image,
                    'collection' => $res['collection_name'],
                    //'collection_href' => $this->url->link('product/manufacturer/infoCollection', 'manufacturer_id=' . $data['product']['manufacturer_id'] . '&collection_id=' . $res['collection_id'] . '&collection_item_id=' . $res['collection_item_id']),
                    'collection_href' => $this->url->link('product/manufacturer/infoCollection', 'manufacturer_id=' . $data['product']['manufacturer_id'] . '&collection_item_id=' . $res['collection_item_id']),
                    'manufacturer_id' => $data['product']['manufacturer_id'],
                    'manufacturer' => $data['product']['manufacturer'],
                    'manufacturer_href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $data['product']['manufacturer_id']),
                    'name' => $string_name,//mb_substr($string_name, 0, 61, 'utf-8') . (mb_strlen($string_name) > 61 ? '...' : ''),
                    //  'name'        => iconv_substr ($data['product']['name'], 0 , 64 , "UTF-8" ),
                    'minimum' => $data['product']['minimum'],
                    'model' => $data['product']['model'],
                    'quantity' => $data['product']['quantity'],
                    'description' => utf8_substr(strip_tags(html_entity_decode($data['product']['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
                    //'description_short' => strip_tags(html_entity_decode($data['product']['description_short'], ENT_QUOTES, 'UTF-8')),
                    'price' => $price,
                    'promos' => $promos,
                    'percent' => $percent,
                    'special' => $special,
                    'specialEnd' => $specialEnd,
                    //  'manufacturer' => $data['product']['manufacturer'],
                    'tax' => $tax,
                    'rating' => $rating,
                    'is_wishlist' => $is_wishlist,
                    'href' => $this->url->link('product/product', 'product_id=' . $data['product']['product_id']),
                );
            } else {
                $products = array(
                    'product_id' => $data['product']['product_id'],
                    'thumb' => $image,
                    'image' => $image,
                    'collection' => '',
                    'collection_href' => '',
                    'manufacturer_id' => $data['product']['manufacturer_id'],
                    'manufacturer' => $data['product']['manufacturer'],
                    'manufacturer_href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $data['product']['manufacturer_id']),
                    'name' => $string_name,//mb_substr($string_name, 0, 61, 'utf-8') . (mb_strlen($string_name) > 61 ? '...' : ''),
                    'minimum' => $data['product']['minimum'],
                    'model' => $data['product']['model'],
                    'quantity' => $data['product']['quantity'],
                    'description' => utf8_substr(strip_tags(html_entity_decode($data['product']['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
                    //'description_short' => strip_tags(html_entity_decode($data['product']['description_short'], ENT_QUOTES, 'UTF-8')),
                    'price' => $price,
                    'promos' => $promos,
                    'percent' => $percent,
                    'special' => $special,
                    'specialEnd' => $specialEnd,
                    //   'manufacturer' => $data['product']['manufacturer'],
                    'tax' => $tax,
                    'rating' => $rating,
                    'is_wishlist' => $is_wishlist,
                    'href' => $this->url->link('product/product', 'product_id=' . $data['product']['product_id']),
                );
            }
//echo '<pre style="display: none">';
//print_r($products);
//echo '</pre>';


            return $products;
        } else {
            return false;
        }
    }

}