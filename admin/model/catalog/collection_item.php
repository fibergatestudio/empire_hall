<?php
class ModelCatalogCollectionItem extends Model {
    public function addCollectionItem($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "collection_item SET status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_added = NOW(), date_modified = NOW()");

        $collection_item_id = $this->db->getLastId();

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "collection_item SET image = '" . $this->db->escape($data['image']) . "' WHERE collection_item_id = '" . (int)$collection_item_id . "'");
        }

        foreach ($data['collection_item_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "collection_item_description SET collection_item_id = '" . (int)$collection_item_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
        }

        if (isset($data['collection_item_category'])) {
            foreach ($data['collection_item_category'] as $category_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "collection_item_to_category SET collection_item_id = '" . (int)$collection_item_id . "', collection_id = '" . (int)$category_id . "'");
            }
        }

        if (isset($data['collection_item_product'])) {
            foreach ($data['collection_item_product'] as $product_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "collection_item_products SET collection_item_id = '" . (int)$collection_item_id . "', product_id = '" . (int)$product_id . "'");
            }
        }

        // SEO URL
        if (isset($data['collection_item_seo_url'])) {
            foreach ($data['collection_item_seo_url'] as $store_id => $language) {
                foreach ($language as $language_id => $keyword) {
                    if (!empty($keyword)) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'collection_item_id=" . (int)$collection_item_id . "', keyword = '" . $this->db->escape($keyword) . "'");
                    } else {
                        if($language_id == 1) {
                            $seo_url = $data['collection_item_description'][2]['name'] . '_en';
                            if(isset($data['collection_item_description'][1]['name']) && $data['collection_item_description'][1]['name'] != $data['collection_item_description'][2]['name']) {
                                $seo_url = $data['collection_item_description'][1]['name'];
                            }
                        }
                        if($language_id == 2) {
                            $seo_url = $data['collection_item_description'][2]['name'];
                        }
                        if($language_id == 3) {
                            $seo_url = $data['collection_item_description'][2]['name'] . '_ua';
                            if(isset($data['collection_item_description'][3]['name']) && $data['collection_item_description'][3]['name'] != $data['collection_item_description'][2]['name']) {
                                $seo_url = $data['collection_item_description'][3]['name'];
                            }
                        }

                        $this->translit->setSeoURL('collection_item_id', $language_id, (int)$collection_item_id, $seo_url);
                    }
                }
            }
        }

        $this->cache->delete('collection_item');

        return $collection_item_id;
    }

    public function editCollectionItem($collection_item_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "collection_item SET status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_modified = NOW() WHERE collection_item_id = '" . (int)$collection_item_id . "'");

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "collection_item SET image = '" . $this->db->escape($data['image']) . "' WHERE collection_item_id = '" . (int)$collection_item_id . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "collection_item_description WHERE collection_item_id = '" . (int)$collection_item_id . "'");

        foreach ($data['collection_item_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "collection_item_description SET collection_item_id = '" . (int)$collection_item_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "collection_item_to_category WHERE collection_item_id = '" . (int)$collection_item_id . "'");

        if (isset($data['collection_item_category'])) {
            foreach ($data['collection_item_category'] as $category_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "collection_item_to_category SET collection_item_id = '" . (int)$collection_item_id . "', collection_id = '" . (int)$category_id . "'");
            }
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "collection_item_products WHERE collection_item_id = '" . (int)$collection_item_id . "'");

        if (isset($data['collection_item_product'])) {
            foreach ($data['collection_item_product'] as $product_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "collection_item_products SET collection_item_id = '" . (int)$collection_item_id . "', product_id = '" . (int)$product_id . "'");
            }
        }

        // SEO URL
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'collection_item_id=" . (int)$collection_item_id . "'");

        if (isset($data['collection_item_seo_url'])) {
            foreach ($data['collection_item_seo_url']as $store_id => $language) {
                foreach ($language as $language_id => $keyword) {
                    if (!empty($keyword)) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'collection_item_id=" . (int)$collection_item_id . "', keyword = '" . $this->db->escape($keyword) . "'");
                    } else {
                        if($language_id == 1) {
                            $seo_url = $data['collection_item_description'][2]['name'] . '_en';
                            if(isset($data['collection_item_description'][1]['name']) && $data['collection_item_description'][1]['name'] != $data['collection_item_description'][2]['name']) {
                                $seo_url = $data['collection_item_description'][1]['name'];
                            }
                        }
                        if($language_id == 2) {
                            $seo_url = $data['collection_item_description'][2]['name'];
                        }
                        if($language_id == 3) {
                            $seo_url = $data['collection_item_description'][2]['name'] . '_ua';
                            if(isset($data['collection_item_description'][3]['name']) && $data['collection_item_description'][3]['name'] != $data['collection_item_description'][2]['name']) {
                                $seo_url = $data['collection_item_description'][3]['name'];
                            }
                        }

                        $this->translit->setSeoURL('collection_item_id', $language_id, (int)$collection_item_id, $seo_url);
                    }
                }
            }
        }

        $this->cache->delete('collection_item');
    }

    public function copyCollectionItem($collection_item_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "collection_item p WHERE p.collection_item_id = '" . (int)$collection_item_id . "'");

        if ($query->num_rows) {
            $data = $query->row;
            $data['status'] = '1';

            $data['collection_item_description'] = $this->getCollectionItemDescriptions($collection_item_id);
            $data['collection_item_category'] = $this->getCollectionItemCategories($collection_item_id);
            $data['collection_item_product'] = $this->getCollectionItemProducts($collection_item_id);

            $this->addCollectionItem($data);
        }
    }

    public function deleteCollectionItem($collection_item_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "collection_item WHERE collection_item_id = '" . (int)$collection_item_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "collection_item_description WHERE collection_item_id = '" . (int)$collection_item_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "collection_item_to_category WHERE collection_item_id = '" . (int)$collection_item_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "collection_item_products WHERE collection_item_id = '" . (int)$collection_item_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'collection_item_id=" . (int)$collection_item_id . "'");

        $this->cache->delete('collection_item');
    }

    public function getCollectionItem($collection_item_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "collection_item p LEFT JOIN " . DB_PREFIX . "collection_item_description pd ON (p.collection_item_id = pd.collection_item_id) WHERE p.collection_item_id = '" . (int)$collection_item_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

        return $query->row;
    }

    public function getCollectionItems($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "collection_item p LEFT JOIN " . DB_PREFIX . "collection_item_description pd ON (p.collection_item_id = pd.collection_item_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
        }

        $sql .= " GROUP BY p.collection_item_id";

        $sort_data = array(
            'pd.name',
            'p.status',
            'p.sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY pd.name";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getCollectionItemsByCategoryId($category_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "collection_item p LEFT JOIN " . DB_PREFIX . "collection_item_description pd ON (p.collection_item_id = pd.collection_item_id) LEFT JOIN " . DB_PREFIX . "collection_item_to_category p2c ON (p.collection_item_id = p2c.collection_item_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2c.collection_item_id = '" . (int)$category_id . "' ORDER BY pd.name ASC");

        return $query->rows;
    }

    public function getCollectionItemDescriptions($collection_item_id) {
        $collection_item_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "collection_item_description WHERE collection_item_id = '" . (int)$collection_item_id . "'");

        foreach ($query->rows as $result) {
            $collection_item_description_data[$result['language_id']] = array(
                'name'             => $result['name'],
                'description'      => $result['description'],
                'meta_title'       => $result['meta_title'],
                'meta_description' => $result['meta_description'],
                'meta_keyword'     => $result['meta_keyword']
            );
        }

        return $collection_item_description_data;
    }

    public function getCollectionItemCategories($collection_item_id) {
        $collection_item_category_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "collection_item_to_category WHERE collection_item_id = '" . (int)$collection_item_id . "'");

        foreach ($query->rows as $result) {
            $collection_item_category_data[] = $result['collection_id'];
        }

        return $collection_item_category_data;
    }

    public function getCollectionItemProducts($collection_item_id) {
        $collection_item_product_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "collection_item_products WHERE collection_item_id = '" . (int)$collection_item_id . "'");

        foreach ($query->rows as $result) {
            $collection_item_product_data[] = $result['product_id'];
        }

        return $collection_item_product_data;
    }

    public function getCollectionItemSeoUrls($collection_item_id) {
        $collection_item_seo_url_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'collection_item_id=" . (int)$collection_item_id . "'");

        foreach ($query->rows as $result) {
            $collection_item_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
        }

        return $collection_item_seo_url_data;
    }

    public function getTotalCollectionItems($data = array()) {
        $sql = "SELECT COUNT(DISTINCT p.collection_item_id) AS total FROM " . DB_PREFIX . "collection_item p LEFT JOIN " . DB_PREFIX . "collection_item_description pd ON (p.collection_item_id = pd.collection_item_id)";

        $sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getNewCollectionItemId() {
        $query = $this->db->query("SHOW TABLE STATUS WHERE `Name` = '" . DB_PREFIX . "collection_item'");

        return $query->row['Auto_increment'];
    }
}
