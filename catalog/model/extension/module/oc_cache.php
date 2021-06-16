<?php
class ModelExtensionModuleOcCache extends Model {
	public function getCustomerTimeZone() {
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, 'http://ip-api.com/json');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		$content = curl_exec($curl);
		$customer_time_zone = json_decode($content);

		curl_close($curl);
		
		return $customer_time_zone->timezone;
	}
}