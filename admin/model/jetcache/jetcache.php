<?php
/* All rights reserved belong to the module, the module developers http://opencartadmin.com */
// https://opencartadmin.com © 2011-2019 All Rights Reserved
// Distribution, without the author's consent is prohibited
// Commercial license
class ModelJetcacheJetcache extends Model
{
	public function getProductsId($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "jetcache_product_cache WHERE product_id = '" . (int)$product_id ."'");
		if ($query->rows) {
			return $query->rows;
		} else {
			return false;
		}
	}

	public function removeCachefile($cachefile) {
		$sql = "DELETE FROM " . DB_PREFIX . "jetcache_product_cache WHERE filecache = '" . $this->db->escape($cachefile) . "'";
		$this->db->query($sql);
	}

}
