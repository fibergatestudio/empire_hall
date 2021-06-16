<?php

class ModelModuleMainimport extends Model
{
    public function setProduct($product)
    {
        if (isset($product['1c_id'])&& $product['1c_id'] && $product['1c_id']!='=#N/A') {
            $query = $this->db->query("SELECT `optprod_id` FROM " . DB_PREFIX . "opt_product WHERE `1c_id` = '" . $product['1c_id'] . "'");
//            echo '<pre>';
//            print_r($product);
//             echo '</pre>';
           //  exit();




            if (isset($query->num_rows)&&$query->num_rows) {
                $this->db->query("UPDATE " . DB_PREFIX . "opt_product SET
                    images = '" . $product['images'] . "',
                    sort = '" . $product['sort'] . "',
                    artikul = '" . $product['artikul'] . "',
                    manufacture = '" . $this->db->escape($product['manufacture']) . "',
                    collection = '" . $this->db->escape($product['collection']) . "',
                    `length` = '" . $product['length'] . "',
                    width = '" . $product['width'] . "',
                    height = '" . $product['height'] . "',
                    diameter = '" . $product['diameter'] . "',
                    weight = '" . $product['weight'] . "',
                    volume = '" . $product['volume'] . "',
                    `name` = '" . $this->db->escape($product['name']) . "',
                    categories = '" . $this->db->escape($product['categories']) . "',
                    countries = '" . $this->db->escape($product['countries']) . "',
                    sizeunit = '" . $product['sizeunit'] . "',
                    volumeunit = '" . $product['volumeunit'] . "',
                    features = '" . $this->db->escape($product['features']) . "',
                    attr = '" . $this->db->escape($product['attr']) . "',
                    attr2 = '" . $this->db->escape($product['new_mass_attr']) . "'
                    WHERE optprod_id = '" . (int)$query->row['optprod_id'] . "'");

                return $query->row['optprod_id'];
            } else {
                $this->db->query("INSERT INTO " . DB_PREFIX . "opt_product SET
                    `sort` = '" . $product['sort'] . "',
                    `1c_id` = '" . $product['1c_id'] . "',
                    images = '" . $product['images'] . "',
                    artikul = '" . $product['artikul'] . "',
                    manufacture = '" . $this->db->escape($product['manufacture']) . "',
                    collection = '" . $this->db->escape($product['collection']) . "',
                    `length` = '" . $product['length'] . "',
                    width = '" . $product['width'] . "',
                    height = '" . $product['height'] . "',
                    diameter = '" . $product['diameter'] . "',
                    weight = '" . $product['weight'] . "',
                    volume = '" . $product['volume'] . "',
                    `name` = '" . $this->db->escape($product['name']) . "',
                    categories = '" . $this->db->escape($product['categories']) . "',
                    countries = '" . $this->db->escape($product['countries']) . "',
                    sizeunit = '" . $product['sizeunit'] . "',
                    volumeunit = '" . $product['volumeunit'] . "',
                    features = '" . $this->db->escape($product['features']) . "',
                    attr2 = '" . $this->db->escape($product['new_mass_attr']) . "',
                    attr = '" . $this->db->escape($product['attr']) . "'");

                return $this->db->getLastId();
            }
        }
    }

    public function getAllFields()
    {
        $query = $this->db->query("SELECT COUNT(optprod_id) as count FROM " . DB_PREFIX . "opt_product");
        if ($query->row['count']) {
            return $query->row['count'];
        } else {
            return false;
        }
    }

    public function getFields()
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "opt_product");
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return false;
        }
    }

    public function getProdIdById1c($product_1c)
    {
        $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product_to_1c WHERE 1c_id = '" . $product_1c . "'");



        if ($query->num_rows) {
            return $query->row['product_id'];
        } else {
            return false;
        }
    }

    public function getManufactureID($manufacture)
    {
        $query = $this->db->query("SELECT manufacturer_id FROM " . DB_PREFIX . "manufacturer_description WHERE `name` = '" . $this->db->escape($manufacture) . "' LIMIT 1");
        if ($query->num_rows) {
            return $query->row['manufacturer_id'];
        } else {

            $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer SET sort_order = '1', image = '', image2 = '', image3 = ''");
            $manufacturer_id = $this->db->getLastId();

            foreach ($this->getAllLanguage() as $lang) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_description SET manufacturer_id = '" . $manufacturer_id . "', language_id = '" . $lang['language_id'] . "', `name` = '" . $this->db->escape($manufacture) . "', subtext = '', description = ''");
            }

            $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_to_store SET manufacturer_id = '" . $manufacturer_id . "', store_id = '0'");

            foreach ($this->getAllLanguage() as $lang) {

                if($lang['language_id'] == 1) {
                    $seo_url = $manufacture . '_en';
                }
                if($lang['language_id'] == 2) {
                    $seo_url = $manufacture;
                }
                if($lang['language_id'] == 3) {
                    $seo_url = $manufacture . '_ua';
                }

                $this->translit->setSeoURL('manufacturer_id', $lang['language_id'], (int)$manufacturer_id, $seo_url);
            }

            return $manufacturer_id;
        }
    }


    public function getAllLanguage() {
        $query = $this->db->query("SELECT language_id FROM " . DB_PREFIX . "language");

        return $query->rows;
    }

    public function getAttributeID($name)
    {
        $query = $this->db->query("SELECT attribute_id FROM " . DB_PREFIX . "attribute_description WHERE name = '" . $this->db->escape($name) . "' LIMIT 1");
        if ($query->num_rows) {
            return $query->row['attribute_id'];
        } else {

            $this->db->query("INSERT INTO " . DB_PREFIX . "attribute SET sort_order = '1', attribute_group_id = '7'");
            $attribute_id = $this->db->getLastId();

            foreach ($this->getAllLanguage() as $lang) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "attribute_description SET attribute_id = '" . $attribute_id . "', language_id = '" . $lang['language_id'] . "', `name` = '" . $this->db->escape($name) . "'");
            }

            return $attribute_id;
        }
    }

    public function getCategory($name, $parent, $lvl,$name_array)
    {
        $query = $this->db->query("SELECT c.category_id FROM " . DB_PREFIX . "category_description cd LEFT JOIN oc_category c ON (c.category_id = cd.category_id) WHERE cd.name = '" . $this->db->escape($name) . "' AND c.parent_id = '" . $parent . "' LIMIT 1");
        if ($query->num_rows) {


            foreach ($this->getAllLanguage() as $lang) {
                $this->db->query("UPDATE " . DB_PREFIX . "category_description SET `name` = '" . $this->db->escape($name_array[$lang['language_id']]) . "' WHERE category_id='".$query->row['category_id']."' and `language_id`='".$lang['language_id']."'");
            }
            return $query->row['category_id'];
        } else {

            $this->db->query("INSERT INTO " . DB_PREFIX . "category SET 
                                        image = '', 
                                        parent_id = '" . $parent . "', 
                                        top = '0', 
                                        `column` = '1', 
                                        sort_order = '1',
                                        `status` = '1', 
                                        date_added = NOW(), 
                                        date_modified = NOW()");
            $category_id = $this->db->getLastId();

            foreach ($this->getAllLanguage() as $lang) {

                if($lang['language_id'] == 1) {
                    $seo_url = $name . '_en';
                }
                if($lang['language_id'] == 2) {
                    $seo_url = $name;
                }
                if($lang['language_id'] == 3) {
                    $seo_url = $name . '_ua';
                }

                $this->translit->setSeoURL('category_id', $lang['language_id'], (int)$category_id, $seo_url);
            }

            //   $this->log->write($name_array);
            foreach ($this->getAllLanguage() as $lang) {

                //   $this->log->write($lang);
                $this->db->query("INSERT INTO " . DB_PREFIX . "category_description SET 
                                            category_id = '" . $category_id . "', 
                                            language_id = '" . $lang['language_id'] . "', 
                                            `name` = '" . $this->db->escape($name_array[$lang['language_id']]) . "',
                                            h1 = '',
                                            description = '',
                                            meta_title = '',
                                            meta_description = '',
                                            meta_keyword = ''");
            }

            /*SEO - URL*/
            if (isset($name) && $name) {
                $this->translit->setSeoURL('category_id', 1, (int)$category_id, $name . '_en');
                $this->translit->setSeoURL('category_id', 2, (int)$category_id, $name);
                $this->translit->setSeoURL('category_id', 3, (int)$category_id, $name . '_ua');
            }

            /* category_path */
            if ($lvl == 0) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "category_path SET 
                                        category_id = '" . $category_id . "', 
                                        path_id = '" . $parent . "', 
                                        `level` = '" . $lvl . "'");
            } else if ($lvl == 1) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "category_path SET 
                                        category_id = '" . $category_id . "', 
                                        path_id = '" . $category_id . "', 
                                        `level` = '" . $lvl . "'");
                $this->db->query("INSERT INTO " . DB_PREFIX . "category_path SET 
                                        category_id = '" . $category_id . "', 
                                        path_id = '" . $parent . "', 
                                        `level` = '0'");
            } else if ($lvl == 2) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "category_path SET 
                                        category_id = '" . $category_id . "', 
                                        path_id = '" . $category_id . "', 
                                        `level` = '" . $lvl . "'");
                $this->db->query("INSERT INTO " . DB_PREFIX . "category_path SET 
                                        category_id = '" . $category_id . "', 
                                        path_id = '" . $parent . "', 
                                        `level` = '1'");
                $grandparent_id = $this->getParentIdByCategoryId($parent);
                if ($grandparent_id) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "category_path SET 
                                        category_id = '" . $category_id . "', 
                                        path_id = '" . $grandparent_id . "', 
                                        `level` = '0'");
                }
            } else if ($lvl == 3) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "category_path SET 
                                        category_id = '" . $category_id . "', 
                                        path_id = '" . $category_id . "', 
                                        `level` = '" . $lvl . "'");
                $this->db->query("INSERT INTO " . DB_PREFIX . "category_path SET 
                                        category_id = '" . $category_id . "', 
                                        path_id = '" . $parent . "', 
                                        `level` = '2'");
                $grandparent_id = $this->getParentIdByCategoryId($parent);
                if ($grandparent_id) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "category_path SET 
                                        category_id = '" . $category_id . "', 
                                        path_id = '" . $grandparent_id . "', 
                                        `level` = '1'");
                }
                $ggrandparent_id = $this->getParentIdByCategoryId($grandparent_id);
                $this->db->query("INSERT INTO " . DB_PREFIX . "category_path SET 
                                        category_id = '" . $category_id . "', 
                                        path_id = '" . $ggrandparent_id . "', 
                                        `level` = '0'");
            }

            /* category_to_layout */
            $this->db->query("INSERT INTO " . DB_PREFIX . "category_to_layout SET 
                                        category_id = '" . $category_id . "', 
                                        store_id = '0', 
                                        layout_id = '0'");

            $this->db->query("INSERT INTO " . DB_PREFIX . "category_to_store SET 
                                        category_id = '" . $category_id . "', 
                                        store_id = '0'");

            return $category_id;
        }
    }

    public function getParentIdByCategoryId($category_id)
    {
        $query = $this->db->query("SELECT parent_id FROM " . DB_PREFIX . "category WHERE category_id = '" . $category_id . "'");
        if ($query->num_rows) {
            return $query->row['parent_id'];
        } else {
            return false;
        }
    }

    public function delTempProd($optprod_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "opt_product WHERE optprod_id = '" . (int)$optprod_id . "'");
    }

    public function updateProduct($product_id, $data)
    {



        $image = isset($data['images'][0]) ? $data['images'][0] : NULL;
        $this->db->query("UPDATE " . DB_PREFIX . "product SET sort_order='".$data['sort']."', model = '" . $this->db->escape($data['artikul']) . "', manufacturer_id = '" . $this->db->escape($data['manufacture']) . "', image = '" . $image . "', status = '1' WHERE product_id = '" . (int)$product_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
        if (isset($data['images'][0])) {
            $i = 0;
            foreach ($data['images'] as $product_image) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $product_image . "', sort_order = '" . $i . "'");
                $i++;
            }
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "collection_item_products WHERE product_id = '" . (int)$product_id . "'");
        if ($data['collection']) {
//            $queries = $this->db->query("SELECT `collection_item_id` FROM " . DB_PREFIX . "collection_item WHERE `collection_item_id`='".$data['collection']."' ");
//            if ($queries->num_rows==0) {
//              if(isset($data['name_collect'])&&!empty($data['name_collect'])){
//                $this->db->query("INSERT INTO " . DB_PREFIX . "collection_item set `status`='1',`image`='',`sort_order`='0',`date_added`=NOW(),`date_modified`=NOW() ");
//                $this->db->query("INSERT INTO " . DB_PREFIX . "collection_item_description set `collection_item_id`='".(int)$data['collection']."',`language_id`='1',`name`='" . $this->db->escape($data['name_collect']) . "' ");
//                $this->db->query("INSERT INTO " . DB_PREFIX . "collection_item_description set `collection_item_id`='".(int)$data['collection']."',`language_id`='2',`name`='" . $this->db->escape($data['name_collect']) . "' ");
//                $this->db->query("INSERT INTO " . DB_PREFIX . "collection_item_description set `collection_item_id`='".(int)$data['collection']."',`language_id`='3',`name`='" . $this->db->escape($data['name_collect']) . "' ");
//                $this->db->query("INSERT INTO " . DB_PREFIX . "collection_item_to_category set `collection_item_id`='".(int)$data['collection']."',`collection_id`='".(int)$data['collection']."',`main_category`='0' ");
//            }
//            }
//
//            $collection_item_id = $this->db->getLastId();
//
            $this->db->query("INSERT INTO " . DB_PREFIX . "collection_item_products SET product_id = '" . $product_id . "', collection_item_id = '" . $data['collection'] . "'");
        }

        foreach ($data['name'] as $language_id => $value) {
            $str = htmlspecialchars($value, ENT_QUOTES);
            $this->db->query("UPDATE " . DB_PREFIX . "product_description SET name = '" . $str . "' WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$language_id . "'");
        }
        foreach ($data['features'] as $language_id => $value) {
            $str = htmlspecialchars($value, ENT_QUOTES);
            $this->db->query("UPDATE " . DB_PREFIX . "product_description SET description = '" . $str . "' WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$language_id . "'");
        }

//        $this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' ");
//        if (!empty($data['attr2'])) {
//            foreach ($data['attr2'] as $attribute_id => $value) {
//
//                foreach ($value as $language_id => $attribute_value) {
//
//                    foreach ($attribute_value as $attr_new){
//
//                        foreach ($attr_new as $attr){
//                            $this->log->write("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($attr) . "'");
//                            $this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($attr) . "'");
//                        }
//
//
//                    }
//                   // $this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($attribute_value) . "'");
//                }
//            }
//        }

        if (isset($data['categories'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
            foreach ($data['categories'] as $category_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
                $lastCat = $category_id;
            }

            $this->db->query("UPDATE " . DB_PREFIX . "product_to_category SET main_category = 1 WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$lastCat . "'");
        }

        if (!empty($data['attr'])) {
            foreach ($data['attr'] as $attribute_id => $value) {
                $this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$attribute_id . "'");

                foreach ($value as $language_id => $attribute_value) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($attribute_value) . "'");
                }
            }
        }

        // SEO URL
        if (isset($data['name'])) {
            foreach ($data['name'] as $language_id => $keyword) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `language_id` = '" . $language_id . "' AND `query` = 'product_id=" . $product_id . "' LIMIT 1");
                if (!$query->num_rows) {
                    if ($language_id == 1) {
                        $seo_url = $data['name'][2] . '_en';
                        if (isset($data['name'][1]) && $data['name'][1] != $data['name'][2]) {
                            $seo_url = $data['name'][1];
                        }
                    }

                    if ($language_id == 2) {
                        $seo_url = $data['name'][2];
                    }

                    if ($language_id == 3) {
                        $seo_url = $data['name'][2] . '_ua';
                        if (isset($data['name'][3]) && $data['name'][3] != $data['name'][2]) {
                            $seo_url = $data['name'][3];
                        }
                    }

                    $this->translit->setSeoURL('product_id', $language_id, (int)$product_id, $seo_url);
                }
            }
        }
    }

    public function addProduct($data)
    {

        $image = isset($data['images'][0]) ? $data['images'][0] : NULL;

        $this->db->query("INSERT INTO " . DB_PREFIX . "product SET 
            sort_order='".$data['sort']."',
            model = '" . $this->db->escape($data['artikul']) . "', 
            sku = '', video = '', upc = '', `location` = '', quantity = '1', minimum = '1', subtract = '0', stock_status_id = '6', date_available = NOW(), 
            manufacturer_id = '" . $data['manufacture'] . "', 
            shipping = '1', price = '0', points = '0', image = '" . $image . "',
            weight = '0', weight_class_id = '1', length = '0', width = '0', height = '0', length_class_id = '1', 
            status = '1', tax_class_id = '0',  date_added = NOW(), date_modified = NOW()");

        $product_id = $this->db->getLastId();

        if ($data['collection']) {

                $this->db->query("INSERT INTO " . DB_PREFIX . "collection_item set `status`='1',`sort_order`='0',`date_added`=NOW(),`date_modified`=NOW() ");
                 $collection_item_id = $this->db->getLastId();
                $this->db->query("INSERT INTO " . DB_PREFIX . "collection_item_to_category set `collection_item_id`='".(int)$collection_item_id."',`collection_id`='".(int)$data['collection']."',`main_category`='0' ");
                 $this->db->query("INSERT INTO " . DB_PREFIX . "collection_item_description set `collection_item_id`='".(int)$collection_item_id."',`language_id`='2',`name`='" . $this->db->escape($data['name']) . "' ");

                  $this->db->query("INSERT INTO " . DB_PREFIX . "collection_item_products SET product_id = '" . $product_id . "', collection_item_id = '" . $data['collection'] . "'");
        }

        $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_1c SET product_id = '" . $product_id . "', 1c_id = '" . $data['1c_id'] . "'");

        foreach ($data['name'] as $language_id => $value) {
            $str = htmlspecialchars($value, ENT_QUOTES);
            $this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', `name` = '" . $str . "', description = '', tag = '', meta_title = '" . $str . "', meta_description = '', meta_keyword = ''");
        }

        foreach ($data['features'] as $language_id => $value) {
            $this->db->query("UPDATE " . DB_PREFIX . "product_description SET description = '" . $this->db->escape($value) . "' WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$language_id . "'");
        }

        $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '0'");

        if (!empty($data['attr'])) {
            foreach ($data['attr'] as $attribute_id => $value) {
                foreach ($value as $language_id => $attribute_value) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($attribute_value) . "'");
                }
            }
        }

        if (isset($data['images'][0])) {
            $i = 0;
            foreach ($data['images'] as $product_image) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $product_image . "', sort_order = '" . $i . "'");
                $i++;
            }
        }

        if (isset($data['categories'])) {
            foreach ($data['categories'] as $category_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
                $lastCat = $category_id;
            }

            $this->db->query("UPDATE " . DB_PREFIX . "product_to_category SET main_category = 1 WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$lastCat . "'");
        }

        $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '0', layout_id = '0'");

        // SEO URL
        if (isset($data['name'])) {
            foreach ($data['name'] as $language_id => $keyword) {
                if($language_id == 1) {
                    $seo_url = $data['name'][2] . '_en';
                    if(isset($data['name'][1]) && $data['name'][1] != $data['name'][2]) {
                        $seo_url = $data['name'][1];
                    }
                }

                if($language_id == 2) {
                    $seo_url = $data['name'][2];
                }

                if($language_id == 3) {
                    $seo_url = $data['name'][2] . '_ua';
                    if(isset($data['name'][3]) && $data['name'][3] != $data['name'][2]) {
                        $seo_url = $data['name'][3];
                    }
                }

                $this->translit->setSeoURL('product_id', $language_id, (int)$product_id, $seo_url);
            }
        }

        return $product_id;
    }



    public function setCollectionItem($id_collect,$categories,$manufacture_id,$collect_name){

        $queries_item = $this->db->query("SELECT `collection_item_id` FROM " . DB_PREFIX . "collection_item WHERE `name_collection`='" . $this->db->escape($collect_name) . "' and `manufacture_id`='".$manufacture_id."' ");

        if(!isset($queries_item->row['collection_item_id'])){
            //   $this->log->write($collect_name);
            //
            $this->db->query("INSERT INTO " . DB_PREFIX . "collection_item set `status`='1',`image`='',`sort_order`='0',`date_added`=NOW(),`date_modified`=NOW(),`name_collection`='" . $this->db->escape($collect_name) . "',`manufacture_id`='".$manufacture_id."' ");
            $id = $this->db->getLastId();

            foreach ($this->getAllLanguage() as $lang) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "collection_item_description set `collection_item_id`='" . (int)$id . "',`language_id`='" . (int)$lang['language_id'] . "',`name`='" . $this->db->escape($collect_name) . "' ");
            }
            $id_collect_item =  $id;


            $this->db->query("INSERT INTO `" . DB_PREFIX . "collection_item_to_category`  set `collection_item_id`='".(int)$id."', `collection_id`='".$id_collect."', `main_category`='0' ");
            return $id_collect_item;
        }else{
            return $queries_item->row['collection_item_id'];
        }





    }

    public function  setCollections($id_collect,$categories,$manufacture_id,$collect_name){


        foreach ($this->getAllLanguage() as $lang) {

            $query = $this->db->query("SELECT `collection_id` FROM " . DB_PREFIX . "collection WHERE `manufacturer_id`='".$manufacture_id."' and `name_collection`='".$this->db->escape($categories[1][2])."' ");

            if(isset($query->row['collection_id'])) {
                // $this->log->write($query);
                $query2 = $this->db->query("SELECT `collection_id` FROM " . DB_PREFIX . "collection_description WHERE `collection_id`='" . (int)$query->row['collection_id'] . "' and `language_id`='" . (int)$lang['language_id'] . "' and `name`='" . $this->db->escape($categories[1][$lang['language_id']]) . "' ");
                if(!isset($query2->row['collection_id'])) {
                    //$this->log->write("INSERT INTO " . DB_PREFIX . "collection_description set `collection_id`='" . (int)$query->row['collection_id'] . "',`language_id`='" . (int)$lang['language_id'] . "',`name`='" . $this->db->escape($categories[1][$lang['language_id']]) . "',`description`='',`meta_title`='',`meta_description`='',`meta_keyword`=''");
                    $this->db->query("INSERT INTO " . DB_PREFIX . "collection_description set `collection_id`='" . (int)$query->row['collection_id'] . "',`language_id`='" . (int)$lang['language_id'] . "',`name`='" . $this->db->escape($categories[1][$lang['language_id']]) . "',`description`='',`meta_title`='',`meta_description`='',`meta_keyword`=''");

                }
                $id_collect = $query->row['collection_id'];

            }



        }


        return $id_collect;

    }

    public function getCollectionId($name,$manufacturer_id,$categories)
    {


        if(isset($name)&&!empty($name)){
            $query = $this->db->query("SELECT `collection_id` FROM " . DB_PREFIX . "collection WHERE  `manufacturer_id`='".(int)$manufacturer_id."' and  `name_collection`='" . trim($this->db->escape($categories[1][2])) . "'");

            //  $this->log->write($query);
            if (isset($query->row['collection_id'])) {
                return   $query->row['collection_id'];
            }else{

                $this->db->query("INSERT INTO " . DB_PREFIX . "collection set `parent_id`=0,`sort_order`='0',`status`='1',`manufacturer_id`='".(int)$manufacturer_id."',`date_added`=NOW(),`date_modified`=NOW(),`name_collection`='" . trim($this->db->escape($categories[1][2])) . "',`cat_collection`='" . trim($this->db->escape($name)) . "' ");

                $collection_id= $this->db->getLastId();
                $this->db->query("INSERT INTO " . DB_PREFIX . "collection_path set `collection_id`='".(int)$collection_id."',`path_id`='".(int)$collection_id."',`level`='0' ");
                return $collection_id;
            }
        }else{
            return false;
        }


    }
}
