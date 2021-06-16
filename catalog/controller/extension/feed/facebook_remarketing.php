<?php
/**
 * Класс CSV экспорта
 */
class ControllerExtensionFeedFacebookRemarketing extends Controller {
	private $shop = array();
	private $currencies = array();
	private $categories = array();
	private $offers = array();
	private $from_charset = 'utf-8';
	private $eol = "\n";

	public function index() {
		if ($this->config->get('feed_facebook_remarketing_status')) {
		
			ini_set("memory_limit", "2048M");

			if (!($allowed_categories = $this->config->get('feed_facebook_remarketing_categories'))) exit();

			$this->load->model('export/facebook_remarketing');
			$this->load->model('localisation/currency');
			$this->load->model('catalog/product');
			$this->load->model('tool/image');
			
			// Валюты
			// TODO: Добавить возможность настраивать проценты в админке.
			$offers_currency = $this->config->get('feed_facebook_remarketing_currency');
			if (!$this->currency->has($offers_currency)) exit();

			$decimal_place = $this->currency->getDecimalPlace($offers_currency);

			$shop_currency = $this->config->get('config_currency');

			$currencies = $this->model_localisation_currency->getCurrencies();

			$supported_currencies = array('RUR', 'RUB', 'USD', 'BYR', 'KZT', 'EUR', 'UAH');

			$currencies = array_intersect_key($currencies, array_flip($supported_currencies));

			// Категории
			$categories = $this->model_export_facebook_remarketing->getCategory();

			foreach ($categories as $category) {
				$this->setCategory($category['name'], $category['category_id'], $category['parent_id']);
			}

			// Товарные предложения
			$in_stock_id = $this->config->get('feed_facebook_remarketing_in_stock'); // id статуса товара "В наличии"
			$out_of_stock_id = $this->config->get('feed_facebook_remarketing_out_of_stock'); // id статуса товара "Нет на складе"
			$vendor_required = false; // true - только товары у которых задан производитель, необходимо для 'vendor.model' 
			$products = $this->model_export_facebook_remarketing->getProduct($allowed_categories, $out_of_stock_id, $vendor_required);
			
			$file = $_SERVER['DOCUMENT_ROOT'] . '/facebook_remarketing.csv';

			$contentCSV = '"id","description","image_link","link","title","price","brand","custom_label_0","availability","condition","additional_image_link","google_product_category"';
			$contentCSV = mb_convert_encoding($contentCSV, 'UTF-8', 'UTF-8') . "\n";
			
			foreach ($products as $product) {
				if ((($product['image'] && file_exists(DIR_IMAGE . $product['image']) && $product['image'] != 'catalog/products/')) && (trim($this->prepareField($product['description']))) && ($product['manufacturer'])) {
					$row = array();
					
					// Атрибуты товарного предложения
					$row[] = '"' . str_replace('"', '""', trim($product['product_id'])) . '"';
					
					$row[] = '"' . str_replace('"', '""', trim($this->prepareField($product['description']))) . '"';
					
					if ($product['image']) {
						$picture = $this->model_tool_image->resize($product['image'], 600, 600);
					} else {
						$picture = '';
					}
					$row[] = '"' . str_replace('"', '""', trim($picture)) . '"';
					
					$url = $this->url->link('product/product', 'path=' . $this->getPath($product['category_id']) . '&product_id=' . $product['product_id']);
					$row[] = '"' . str_replace('"', '""', trim($url)) . '"';
					
					$row[] = '"' . str_replace('"', '""', trim($this->prepareField($product['name']))) . '"';
					
					$price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $shop_currency);
					$row[] = '"' . str_replace('"', '""', trim($price)) . '"';
					
					$row[] = '"' . str_replace('"', '""', trim($this->prepareField($product['manufacturer']))) . '"';
					
					$row[] = '"' . str_replace('"', '""', trim($this->prepareField($product['category_name']))) . '"';
					
					$row[] = '"' . str_replace('"', '""', trim($this->prepareField('in stock'))) . '"';
					
					$row[] = '"' . str_replace('"', '""', trim($this->prepareField('new'))) . '"';
					
					$additional_images = '';
					$images = $this->model_catalog_product->getProductImages($product['product_id']);
					$j = 0;
					foreach ($images as $result) {
						if ($result['image'] && file_exists(DIR_IMAGE . $result['image']) && $result['image'] != 'catalog/products/') {
							$additional_images .= (($j > 0) ? ',' : '') . $this->model_tool_image->resize($result['image'], 600, 600);
							$j++;
							if ($j > 9) break;
						}
					}
					$row[] = '"' . str_replace('"', '""', trim($additional_images)) . '"';
					
					$google_category = $this->model_export_facebook_remarketing->getCategoryGoogle($product['category_id']);
					if ($google_category) {
						$google_product_category = $google_category['facebook_remarketing_category_id'];
					} else {
						$google_product_category = '';
					}
					$row[] = '"' . str_replace('"', '""', trim($this->prepareField($google_product_category))) . '"';
					
					$contentCSV .= mb_convert_encoding(implode(',', $row), 'UTF-8', 'UTF-8') . "\n";
				}
			}

			@unlink($file);
			if(!$handle = fopen($file, 'a'))
			{
				echo "Cannot open to file";
				exit;
			}

			if(fwrite($handle, $contentCSV) === false)
			{
				echo "Cannot write to file";
				exit;
			}
			
			fclose($handle);
			
			/*if (file_exists($file))
			{	
				header('Content-type: application/stream-download');//'octet/stream';//octet-stream
				header('Content-Transfer-Encoding: Binary');
				header('Content-length: '.filesize($file) );
				header('Content-disposition: attachment; filename="' . basename($file) . '";');
				header('Expires: 0');
				header('Cache-Control: private');
				header('Pragma: public');
				header('Connection: close');
				ob_clean();
				flush();
					
				readfile($file);
				exit;
			}*/
		}
	}

	/**
	 * Категории товаров
	 *
	 * @param string $name - название рубрики
	 * @param int $id - id рубрики
	 * @param int $parent_id - id родительской рубрики
	 * @return bool
	 */
	private function setCategory($name, $id, $parent_id = 0) {
		$id = (int)$id;
		if ($id < 1 || trim($name) == '') {
			return false;
		}
		if ((int)$parent_id > 0) {
			$this->categories[$id] = array(
				'id'=>$id,
				'parentId'=>(int)$parent_id,
				'name'=>$this->prepareField($name)
			);
		} else {
			$this->categories[$id] = array(
				'id'=>$id,
				'name'=>$this->prepareField($name)
			);
		}

		return true;
	}

	/**
	 * Подготовка текстового поля в соответствии с требованиями Яндекса
	 * Запрещаем любые html-тэги, стандарт XML не допускает использования в текстовых данных
	 * непечатаемых символов с ASCII-кодами в диапазоне значений от 0 до 31 (за исключением
	 * символов с кодами 9, 10, 13 - табуляция, перевод строки, возврат каретки). Также этот
	 * стандарт требует обязательной замены некоторых символов на их символьные примитивы.
	 * @param string $text
	 * @return string
	 */
	protected function prepareField($field) {
		$field = htmlspecialchars_decode($field);
		//Убираем не UTF-8 символы
		//@todo использовать github.com/neitanod/forceutf8 для их конвертации
		$field = mb_convert_encoding($field, 'UTF-8', 'UTF-8');
		if (strpos($field, '<![CDATA[') === 0) {
			return trim($field);
		}
		$field = strip_tags($field);
		//$from = array('&nbsp;', '&', '"', '>', '<', '\'');
		//$to = array(' ', '&amp;', '&quot;', '&gt;', '&lt;', '&apos;');
		
		$from = array('&nbsp;', '"', '&#039;');
		$to = array(' ', "'", "'");
		
		$field = str_replace($from, $to, $field);
		/**
		if ($this->from_charset != 'UTF-8') {
			$field = iconv($this->from_charset, 'UTF-8//IGNORE', $field);
		}
		**/
		$field = preg_replace('#[\x00-\x08\x0B-\x0C\x0E-\x1F]+#is', ' ', $field);

		return trim($field);
	}

	protected function getPath($category_id, $current_path = '') {
		if (isset($this->categories[$category_id])) {
			$this->categories[$category_id]['export'] = 1;

			if (!$current_path) {
				$new_path = $this->categories[$category_id]['id'];
			} else {
				$new_path = $this->categories[$category_id]['id'] . '_' . $current_path;
			}	

			if (isset($this->categories[$category_id]['parentId'])) {
				return $this->getPath($this->categories[$category_id]['parentId'], $new_path);
			} else {
				return $new_path;
			}

		}
	}

	function filterCategory($category) {
		return isset($category['export']);
	}
}
?>