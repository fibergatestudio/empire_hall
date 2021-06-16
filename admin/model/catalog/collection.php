<?php
class ModelCatalogCollection extends Model {
    public function addCollection($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "collection SET parent_id = '" . (int)$data['parent_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', status = '" . (int)$data['status'] . "', date_modified = NOW(), date_added = NOW()");

        $collection_id = $this->db->getLastId();

        foreach ($data['collection_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "collection_description SET collection_id = '" . (int)$collection_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
        }

        // MySQL Hierarchical Data Closure Table Pattern
        $level = 0;

        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "collection_path` WHERE collection_id = '" . (int)$data['parent_id'] . "' ORDER BY `level` ASC");

        foreach ($query->rows as $result) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "collection_path` SET `collection_id` = '" . (int)$collection_id . "', `path_id` = '" . (int)$result['path_id'] . "', `level` = '" . (int)$level . "'");

            $level++;
        }

        $this->db->query("INSERT INTO `" . DB_PREFIX . "collection_path` SET `collection_id` = '" . (int)$collection_id . "', `path_id` = '" . (int)$collection_id . "', `level` = '" . (int)$level . "'");

        if (isset($data['collection_seo_url'])) {
            foreach ($data['collection_seo_url'] as $store_id => $language) {
                foreach ($language as $language_id => $keyword) {
                    if (!empty($keyword)) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'collection_id=" . (int)$collection_id . "', keyword = '" . $this->db->escape($keyword) . "'");
                    } else {
                        if($language_id == 1) {
                            $seo_url = $data['collection_description'][2]['name'] . '_en';
                            if(isset($data['collection_description'][1]['name']) && $data['collection_description'][1]['name'] != $data['collection_description'][2]['name']) {
                                $seo_url = $data['collection_description'][1]['name'];
                            }
                        }
                        if($language_id == 2) {
                            $seo_url = $data['collection_description'][2]['name'];
                        }
                        if($language_id == 3) {
                            $seo_url = $data['collection_description'][2]['name'] . '_ua';
                            if(isset($data['collection_description'][3]['name']) && $data['collection_description'][3]['name'] != $data['collection_description'][2]['name']) {
                                $seo_url = $data['collection_description'][3]['name'];
                            }
                        }

                        $this->translit->setSeoURL('collection_id', $language_id, (int)$collection_id, $seo_url);
                    }
                }
            }
        }

        $this->cache->delete('collection');

        return $collection_id;
    }

    public function editCollection($collection_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "collection SET parent_id = '" . (int)$data['parent_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', status = '" . (int)$data['status'] . "', date_modified = NOW() WHERE collection_id = '" . (int)$collection_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "collection_description WHERE collection_id = '" . (int)$collection_id . "'");

        foreach ($data['collection_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "collection_description SET collection_id = '" . (int)$collection_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
        }

        // MySQL Hierarchical Data Closure Table Pattern
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "collection_path` WHERE path_id = '" . (int)$collection_id . "' ORDER BY level ASC");

        if ($query->rows) {
            foreach ($query->rows as $collection_path) {
                // Delete the path below the current one
                $this->db->query("DELETE FROM `" . DB_PREFIX . "collection_path` WHERE collection_id = '" . (int)$collection_path['collection_id'] . "' AND level < '" . (int)$collection_path['level'] . "'");

                $path = array();

                // Get the nodes new parents
                $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "collection_path` WHERE collection_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

                foreach ($query->rows as $result) {
                    $path[] = $result['path_id'];
                }

                // Get whats left of the nodes current path
                $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "collection_path` WHERE collection_id = '" . (int)$collection_path['collection_id'] . "' ORDER BY level ASC");

                foreach ($query->rows as $result) {
                    $path[] = $result['path_id'];
                }

                // Combine the paths with a new level
                $level = 0;

                foreach ($path as $path_id) {
                    $this->db->query("REPLACE INTO `" . DB_PREFIX . "collection_path` SET collection_id = '" . (int)$collection_path['collection_id'] . "', `path_id` = '" . (int)$path_id . "', level = '" . (int)$level . "'");

                    $level++;
                }
            }
        } else {
            // Delete the path below the current one
            $this->db->query("DELETE FROM `" . DB_PREFIX . "collection_path` WHERE collection_id = '" . (int)$collection_id . "'");

            // Fix for records with no paths
            $level = 0;

            $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "collection_path` WHERE collection_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

            foreach ($query->rows as $result) {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "collection_path` SET collection_id = '" . (int)$collection_id . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

                $level++;
            }

            $this->db->query("REPLACE INTO `" . DB_PREFIX . "collection_path` SET collection_id = '" . (int)$collection_id . "', `path_id` = '" . (int)$collection_id . "', level = '" . (int)$level . "'");
        }

        // SEO URL
        $this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'collection_id=" . (int)$collection_id . "'");

        if (isset($data['collection_seo_url'])) {
            foreach ($data['collection_seo_url'] as $store_id => $language) {
                foreach ($language as $language_id => $keyword) {
                    if (!empty($keyword)) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'collection_id=" . (int)$collection_id . "', keyword = '" . $this->db->escape($keyword) . "'");
                    } else {
                        if($language_id == 1) {
                            $seo_url = $data['collection_description'][2]['name'] . '_en';
                            if(isset($data['collection_description'][1]['name']) && $data['collection_description'][1]['name'] != $data['collection_description'][2]['name']) {
                                $seo_url = $data['collection_description'][1]['name'];
                            }
                        }
                        if($language_id == 2) {
                            $seo_url = $data['collection_description'][2]['name'];
                        }
                        if($language_id == 3) {
                            $seo_url = $data['collection_description'][2]['name'] . '_ua';
                            if(isset($data['collection_description'][3]['name']) && $data['collection_description'][3]['name'] != $data['collection_description'][2]['name']) {
                                $seo_url = $data['collection_description'][3]['name'];
                            }
                        }

                        $this->translit->setSeoURL('collection_id', $language_id, (int)$collection_id, $seo_url);
                    }
                }
            }
        }

        $this->cache->delete('collection');
    }

    public function deleteCollection($collection_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "collection_path WHERE collection_id = '" . (int)$collection_id . "'");

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "collection_path WHERE path_id = '" . (int)$collection_id . "'");

        foreach ($query->rows as $result) {
            $this->deleteCollection($result['collection_id']);
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "collection WHERE collection_id = '" . (int)$collection_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "collection_description WHERE collection_id = '" . (int)$collection_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "collection_item_to_category WHERE collection_id = '" . (int)$collection_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'collection_id=" . (int)$collection_id . "'");

        $this->cache->delete('collection');
    }

    public function repairCollections($parent_id = 0) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "collection WHERE parent_id = '" . (int)$parent_id . "'");

        foreach ($query->rows as $collection) {
            // Delete the path below the current one
            $this->db->query("DELETE FROM `" . DB_PREFIX . "collection_path` WHERE collection_id = '" . (int)$collection['collection_id'] . "'");

            // Fix for records with no paths
            $level = 0;

            $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "collection_path` WHERE collection_id = '" . (int)$parent_id . "' ORDER BY level ASC");

            foreach ($query->rows as $result) {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "collection_path` SET collection_id = '" . (int)$collection['collection_id'] . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

                $level++;
            }

            $this->db->query("REPLACE INTO `" . DB_PREFIX . "collection_path` SET collection_id = '" . (int)$collection['collection_id'] . "', `path_id` = '" . (int)$collection['collection_id'] . "', level = '" . (int)$level . "'");

            $this->repairCollections($collection['collection_id']);
        }
    }

    public function getCollection($collection_id) {
        $query = $this->db->query("SELECT DISTINCT *, (SELECT GROUP_CONCAT(cd1.name ORDER BY level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') FROM " . DB_PREFIX . "collection_path cp LEFT JOIN " . DB_PREFIX . "collection_description cd1 ON (cp.path_id = cd1.collection_id AND cp.collection_id != cp.path_id) WHERE cp.collection_id = c.collection_id AND cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY cp.collection_id) AS path FROM " . DB_PREFIX . "collection c LEFT JOIN " . DB_PREFIX . "collection_description cd2 ON (c.collection_id = cd2.collection_id) WHERE c.collection_id = '" . (int)$collection_id . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'");

        return $query->row;
    }

    public function getCollections($data = array()) {
        $sql = "SELECT cp.collection_id AS collection_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, c1.parent_id, c1.sort_order FROM " . DB_PREFIX . "collection_path cp LEFT JOIN " . DB_PREFIX . "collection c1 ON (cp.collection_id = c1.collection_id) LEFT JOIN " . DB_PREFIX . "collection c2 ON (cp.path_id = c2.collection_id) LEFT JOIN " . DB_PREFIX . "collection_description cd1 ON (cp.path_id = cd1.collection_id) LEFT JOIN " . DB_PREFIX . "collection_description cd2 ON (cp.collection_id = cd2.collection_id) WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND cd2.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        $sql .= " GROUP BY cp.collection_id";

        $sort_data = array(
            'name',
            'sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY sort_order";
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

    public function getCollectionDescriptions($collection_id) {
        $collection_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "collection_description WHERE collection_id = '" . (int)$collection_id . "'");

        foreach ($query->rows as $result) {
            $collection_description_data[$result['language_id']] = array(
                'name'             => $result['name'],
                'meta_title'       => $result['meta_title'],
                'meta_description' => $result['meta_description'],
                'meta_keyword'     => $result['meta_keyword'],
                'description'      => $result['description']
            );
        }

        return $collection_description_data;
    }

    public function getCollectionPath($collection_id) {
        $query = $this->db->query("SELECT collection_id, path_id, level FROM " . DB_PREFIX . "collection_path WHERE collection_id = '" . (int)$collection_id . "'");

        return $query->rows;
    }

    public function getCollectionSeoUrls($collection_id) {
        $collection_seo_url_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'collection_id=" . (int)$collection_id . "'");

        foreach ($query->rows as $result) {
            $collection_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
        }

        return $collection_seo_url_data;
    }

    public function getTotalCollections() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "collection");

        return $query->row['total'];
    }

    public function getNewCollectionId() {
        $query = $this->db->query("SHOW TABLE STATUS WHERE `Name` = '" . DB_PREFIX . "collection'");

        return $query->row['Auto_increment'];
    }
}
