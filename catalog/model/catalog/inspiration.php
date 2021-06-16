<?php
class ModelCatalogInspiration extends Model {
    public function getInspiration($inspiration_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "inspiration m LEFT JOIN " . DB_PREFIX . "inspiration_description md 
        ON (m.inspiration_id = md.inspiration_id) WHERE m.inspiration_id = '" . (int)$inspiration_id . "' AND md.language_id = '" . (int)$this->config->get('config_language_id') . "'");

        return $query->row;
    }

    public function getInspirations() {
            $inspiration_data = $this->cache->get('inspiration.' . (int)$this->config->get('config_store_id'));

            if (!$inspiration_data) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "inspiration m LEFT JOIN " . DB_PREFIX . "inspiration_description md ON (m.inspiration_id = md.inspiration_id) WHERE md.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY m.sort_order ASC");

                $inspiration_data = $query->rows;

                $this->cache->set('inspiration.' . (int)$this->config->get('config_store_id'), $inspiration_data);
            }

            return $inspiration_data;
        }
    
}