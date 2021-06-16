<?php
class Translit {
	private $data = array();

	public function __construct($registry) {
		$this->db = $registry->get('db');
	}

	public function setSeoURL($url_type, $language_id, $element_id, $element_name) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE `query` = '" . $url_type . "=" . $element_id . "' and `language_id` = '" . $language_id . "'");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET `language_id` = '" . $language_id . "', `query` = '" . $url_type . "=" . $element_id ."', `keyword`='" . $this->transString($element_name) . "'");
	}
	private function transString($aString) {
		$rus = array(" ", "'", "/", "\\", "*", "-", "+", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "+", "[", "]", "{", "}", "~", ";", ":", "'", "\"", "<", ">", ",", ".", "?", "А", "Б", "В", "Г", "Д", "Е", "Є", "З", "И", "І", "Ї", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ъ", "Ы", "Ь", "Э", "а", "б", "в", "г", "д", "е", "є", "з", "и", "і", "ї", "й", "к", "л", "м", "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ъ", "ы", "ь", "э", "ё",  "ж",  "ц",  "ч",  "ш",  "щ",   "ю",  "я",  "Ё",  "Ж",  "Ц",  "Ч",  "Ш",  "Щ",   "Ю",  "Я", "É", "Ï", "È");
		$lat = array("-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-",  "-", "-", "-", "-", "-", "-", "a", "b", "v", "g", "d", "e", "e", "z", "i", "i", "i", "y", "k", "l", "m", "n", "o", "p", "r", "s", "t", "u", "f", "h", "",  "i", "",  "e", "a", "b", "v", "g", "d", "e", "e", "z", "i", "i", "i", "j", "k", "l", "m", "n", "o", "p", "r", "s", "t", "u", "f", "h", "",  "i", "",  "e", "yo", "zh", "ts", "ch", "sh", "sch", "yu", "ya", "yo", "zh", "ts", "ch", "sh", "sch", "yu", "ya", "e", "i", "e");

		$string = str_replace($rus, $lat, $aString);

		while (mb_strpos($string, '--')) {
			$string = str_replace('--', '-', $string);
		}

		$string = strtolower(trim($string, '-'));
		$string = $this->recursUrlInspector($string);
		return $string;
	}
	private function recursUrlInspector($string, $num=0) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE `keyword` = '" . $string . "'");

        if ($query->num_rows) {
			$num++;
			return $this->recursUrlInspector($string.$num, $num);
		}
		else {
			return $string;
		}
	}
}
