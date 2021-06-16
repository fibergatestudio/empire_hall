<?php
class ModelCatalogInspiration extends Model {
    public function addInspiration($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "inspiration SET sort_order = '" . (int)$data['sort_order'] . "'");

        $inspiration_id = $this->db->getLastId();

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "inspiration SET image = '" . $this->db->escape($data['image']) . "' WHERE inspiration_id = '" . (int)$inspiration_id . "'");
        }

        if (isset($data['image2'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "inspiration SET image2 = '" . $this->db->escape($data['image2']) . "' WHERE inspiration_id = '" . (int)$inspiration_id . "'");
        }

        foreach ($data['inspiration_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "inspiration_description SET inspiration_id = '" . (int)$inspiration_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', subtext = '" . $this->db->escape($value['subtext']) . "'");
        }

        // SEO URL
        if (isset($data['inspiration_seo_url'])) {
            foreach ($data['inspiration_seo_url'] as $store_id => $language) {
                foreach ($language as $language_id => $keyword) {
                    if (!empty($keyword)) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'inspiration_id=" . (int)$inspiration_id . "', keyword = '" . $this->db->escape($keyword) . "'");
                    }
                }
            }
        }

        if (isset($data['inspiration_related'])) {
            foreach ($data['inspiration_related'] as $product_id) {
                $this->db->query("DELETE FROM " . DB_PREFIX . "inspiration_related WHERE inspiration_id = '" . (int)$inspiration_id . "' AND product_id = '" . (int)$product_id . "'");
                $this->db->query("INSERT INTO " . DB_PREFIX . "inspiration_related SET inspiration_id = '" . (int)$inspiration_id . "', product_id = '" . (int)$product_id . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "inspiration_related WHERE inspiration_id = '" . (int)$inspiration_id . "' AND product_id = '" . (int)$product_id . "'");
                $this->db->query("INSERT INTO " . DB_PREFIX . "inspiration_related SET inspiration_id = '" . (int)$inspiration_id . "', product_id = '" . (int)$product_id . "'");
            }
        }

        $this->cache->delete('inspiration');

        return $inspiration_id;
    }

    public function editInspiration($inspiration_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "inspiration SET sort_order = '" . (int)$data['sort_order'] . "' WHERE inspiration_id = '" . (int)$inspiration_id . "'");

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "inspiration SET image = '" . $this->db->escape($data['image']) . "' WHERE inspiration_id = '" . (int)$inspiration_id . "'");
        }

        if (isset($data['image2'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "inspiration SET image2 = '" . $this->db->escape($data['image2']) . "' WHERE inspiration_id = '" . (int)$inspiration_id . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "inspiration_description WHERE inspiration_id = '" . (int)$inspiration_id . "'");

        foreach ($data['inspiration_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "inspiration_description SET inspiration_id = '" . (int)$inspiration_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', subtext = '" . $this->db->escape($value['subtext']) . "'");
        }

        $this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'inspiration_id=" . (int)$inspiration_id . "'");

        if (isset($data['inspiration_seo_url'])) {
            foreach ($data['inspiration_seo_url'] as $store_id => $language) {
                foreach ($language as $language_id => $keyword) {
                    if (!empty($keyword)) {
                        $this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'inspiration_id=" . (int)$inspiration_id . "', keyword = '" . $this->db->escape($keyword) . "'");
                    } else {
                        if($language_id == 1) {
                            $seo_url = $data['inspiration_description'][2]['name'] . '_en';
                            if(isset($data['inspiration_description'][1]['name']) && $data['inspiration_description'][1]['name'] != $data['inspiration_description'][2]['name']) {
                                $seo_url = $data['inspiration_description'][1]['name'];
                            }
                        }
                        if($language_id == 2) {
                            $seo_url = $data['inspiration_description'][2]['name'];
                        }
                        if($language_id == 3) {
                            $seo_url = $data['inspiration_description'][2]['name'] . '_ua';
                            if(isset($data['inspiration_description'][3]['name']) && $data['inspiration_description'][3]['name'] != $data['inspiration_description'][2]['name']) {
                                $seo_url = $data['inspiration_description'][3]['name'];
                            }
                        }

                        $this->translit->setSeoURL('inspiration_id', $language_id, (int)$inspiration_id, $seo_url);
                    }
                }
            }
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "inspiration_related WHERE product_id = '" . (int)$inspiration_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "inspiration_related WHERE inspiration_id = '" . (int)$inspiration_id . "'");

        if (isset($data['inspiration_related'])) {
            foreach ($data['inspiration_related'] as $related_id) {
                $this->db->query("DELETE FROM " . DB_PREFIX . "inspiration_related WHERE inspiration_id = '" . (int)$inspiration_id . "' AND product_id = '" . (int)$related_id . "'");
                $this->db->query("INSERT INTO " . DB_PREFIX . "inspiration_related SET inspiration_id = '" . (int)$inspiration_id . "', product_id = '" . (int)$related_id . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "inspiration_related WHERE inspiration_id = '" . (int)$inspiration_id . "' AND product_id = '" . (int)$related_id . "'");
                $this->db->query("INSERT INTO " . DB_PREFIX . "inspiration_related SET inspiration_id = '" . (int)$inspiration_id . "', product_id = '" . (int)$related_id . "'");
            }
        }

        $this->cache->delete('inspiration');
    }

    public function deleteInspiration($inspiration_id) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "inspiration` WHERE inspiration_id = '" . (int)$inspiration_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "inspiration_description` WHERE inspiration_id = '" . (int)$inspiration_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "inspiration_related` WHERE inspiration_id = '" . (int)$inspiration_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "inspiration_to_store` WHERE inspiration_id = '" . (int)$inspiration_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'inspiration_id=" . (int)$inspiration_id . "'");

        $this->cache->delete('inspiration');
    }

    public function getInspiration($inspiration_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "inspiration m LEFT JOIN " . DB_PREFIX . "inspiration_description md ON (m.inspiration_id = md.inspiration_id) WHERE m.inspiration_id = '" . (int)$inspiration_id . "' AND md.language_id = '" . (int)$this->config->get('config_language_id') . "'");

        return $query->row;
    }

    public function getInspirations($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "inspiration m LEFT JOIN " . DB_PREFIX . "inspiration_description md ON (m.inspiration_id = md.inspiration_id) WHERE md.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        $sort_data = array(
            'name',
            'sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
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

    public function getInspirationDescriptions($inspiration_id) {
        $inspiration_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "inspiration_description WHERE inspiration_id = '" . (int)$inspiration_id . "'");

        foreach ($query->rows as $result) {
            $inspiration_description_data[$result['language_id']] = array(
                'name'             => $result['name'],
                'subtext'          => $result['subtext'],
                'description'      => $result['description']
            );
        }

        return $inspiration_description_data;
    }

    public function getProductRelated($inspiration_id) {
        $product_related_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "inspiration_related WHERE inspiration_id = '" . (int)$inspiration_id . "'");

        foreach ($query->rows as $result) {
            $product_related_data[] = $result['product_id'];
        }

        return $product_related_data;
    }

    public function getInspirationSeoUrls($inspiration_id) {
        $inspiration_seo_url_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'inspiration_id=" . (int)$inspiration_id . "'");

        foreach ($query->rows as $result) {
            $inspiration_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
        }

        return $inspiration_seo_url_data;
    }

    public function getTotalInspirations() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "inspiration");

        return $query->row['total'];
    }
}
