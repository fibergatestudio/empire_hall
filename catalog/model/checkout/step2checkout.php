<?php
class ModelCheckoutStep2checkout extends Model {

	public function getCountries() {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country`");

		if ($query->num_rows) {
			return $query->rows;
		} else {
			return array();
		}		
	}

	public function getZones($country_id) {
		$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE country_id = '" . (int)$country_id . "'");

		if ($zone_query->num_rows) {
			return $zone_query->rows;
		} else {
			return array();
		}
	}

	public function getZoneById($zone_id) {
		$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$zone_id . "'");

		if ($zone_query->num_rows) {
			return $zone_query->row;
		} else {
			return false;
		}	
	}	

	public function getCountryNameById($country_id) {
		$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$country_id . "'");

		if ($zone_query->num_rows) {
			return $zone_query->row;
		} else {
			return false;
		}	
	}
	
	public function editOrderMethods($data) {
		
		$order_id = $data['order_id'];

		if(isset($data['payment_method']) && isset($data['shipping_method'])){
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET "
					. "payment_method = '" . $this->db->escape($data['payment_method']['title']) . "', "
					. "payment_code = '" . $this->db->escape($data['payment_method']['code']) . "', "
					. "shipping_method = '" . $this->db->escape($data['shipping_method']['title']) . "', "
					. "shipping_code = '" . $this->db->escape($data['shipping_method']['code']) . "', "
					. "date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
		}
	}
	
	public function editOrderNPost($data) {
		$order_id = $data['order_id'];
		$city_name = '';
		$warehouse_name = '';
		$br = '';
		if(isset($data['shipping_method'])){
			if(isset($this->session->data['shipping_address']['city_ref']) && $this->session->data['shipping_address']['city_ref']){
				$city_name_sql = $this->db->query("SELECT DescriptionRu FROM `" . DB_PREFIX . "cities` WHERE Ref = '" . $this->session->data['shipping_address']['city_ref'] . "'");
				if ($city_name_sql->num_rows) {
					$city_name = $city_name_sql->row['DescriptionRu'];
					$br = ' : ';
				}					
			}
			if(isset($this->session->data['shipping_address']['warehouse_ref']) && $this->session->data['shipping_address']['warehouse_ref']){
				$warehouse_name_sql = $this->db->query("SELECT DescriptionRu FROM `" . DB_PREFIX . "novaposhta_warehouses` WHERE Ref = '" . $this->session->data['shipping_address']['warehouse_ref'] . "'");
				if ($warehouse_name_sql->num_rows) {
					$warehouse_name = $warehouse_name_sql->row['DescriptionRu'];
				}						
			}
		}
		$adr = $city_name . $br . $warehouse_name;
		
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET shipping_address_1 = '" . $this->db->escape($adr) . "' WHERE order_id = '" . (int)$order_id . "'");
		return true;
	}
	
	public function editOrderMessage($order_id, $message) {
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET comment = '" . $this->db->escape($message) . "' WHERE order_id = '" . (int)$order_id . "'");
		return true;
	}
}