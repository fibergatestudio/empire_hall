<?php
class ModelCatalogCollection extends Model {
    public function getCollectionCategory($category_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "collection c 
	    LEFT JOIN " . DB_PREFIX . "collection_description cd 
	    ON (c.collection_id = cd.collection_id) 
	    WHERE c.collection_id = '" . (int)$category_id ."' AND 
	    cd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

        return $query->row;
    }

    public function getCollectionsItem($collection_item_id, $category_id, $manufacturer_id) {
        $query = $this->db->query("SELECT cid.name, ci.collection_item_id FROM " . DB_PREFIX . "collection_item_to_category citc 
		LEFT JOIN " . DB_PREFIX . "collection_item ci 
		ON (citc.collection_item_id = ci.collection_item_id) 
		LEFT JOIN " . DB_PREFIX . "collection_item_description cid 
		ON (cid.collection_item_id = ci.collection_item_id) 
		LEFT JOIN " . DB_PREFIX . "collection c 
		ON (citc.collection_id = c.collection_id) 
		WHERE ci.collection_item_id = '" . (int)$collection_item_id . "' 
		AND citc.collection_id = '" . (int)$category_id . "' 
		AND cid.language_id = '" . (int)$this->config->get('config_language_id') . "' 
		AND ci.status = '1' 
		AND c.manufacturer_id = '" . (int)$manufacturer_id . "'");

        return $query->row;
    }
}
