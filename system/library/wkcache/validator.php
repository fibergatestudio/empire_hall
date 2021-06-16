<?php
namespace Wkcache;

class Validator {
    /**
     * Constructor function of this library
     * @param [type] $registry [registry object]
     */
    public function __construct($registry) {
		$this->config 	    = $registry->get('config');
		$this->db 		      = $registry->get('db');
		$this->request 	    = $registry->get('request');
		$this->session    	= $registry->get('session');
    }
    /**
     * Check the comporser is installed or not function
     *
     * @param [type] $image
     * @return void
     */
    public function chkComposer($lang = '') {
        if (file_exists(DIR_APPLICATION . '../vendor/autoload.php'))
           return true;
        return false;
    }

    public function getComposer(){
        if(!$this->chkComposer()){
            die('<h2>Please install composer for using this module first and then reinstall the module</h2>');
        }
    }
    
    /**
     * function to check the user group exist
     * @param [type] $user_group_id [user group id]
     * @return boolean
     */
    public function checkUserGroup($user_group_id = 0) {
        
        $query = $this->db->query("SELECT user_group_id FROM " . DB_PREFIX . "user_group WHERE user_group_id='" . (int)$user_group_id . "'")->row;

        if(!empty($query) && isset($query['user_group_id']) && $query['user_group_id']) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function to check selected order status id is exist or not
     * @param [type] $order_status_id [order status id]
     * @return boolean
     */
    public function checkOrderStatus($order_status_id = 0) {
        
        $query = $this->db->query("SELECT order_status_id FROM " . DB_PREFIX . "order_status WHERE order_status_id='" . (int)$order_status_id . "'")->row;

        if(!empty($query) && isset($query['order_status_id']) && $query['order_status_id']) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * function to check if status is different from enabled or disabled
     * @param [type] $status [status value]
     * @return boolean
     */
    public function checkStatus($status = 0) {
        if($status > 1 || $status < 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * function to check whetet store id is exist or not
     * @param [type] $store_id [store id]
     * @return boolean
    */
    public function checkStore($store_id = 0) {
        
        if($store_id) {
            $query = $this->db->query("SELECT store_id FROM " . DB_PREFIX . "store WHERE store_id='" . (int)$store_id . "'")->row;
            if(!empty($query) && isset($query['store_id']) && $query['store_id']) {
                return true;
            } else {
                return false;
            }

        } elseif ($store_id == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function to check language exist or not 
     * @param [type] $language_id [language id]
     * @return boolean
    */
    public function checkLangauge($language_id = 0) {
        $query = $this->db->query("SELECT language_id FROM " . DB_PREFIX .  "language WHERE language_id='" . (int)$language_id . "' AND status='1'")->row;
        if(!empty($query) && isset($query['language_id']) && $query['language_id']) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * function to check user id exist in database
     *
     * @param [type] $user_id
     * @return boolean
     */
    public function checkUserID($user_id = 0) {
        $query = $this->db->query("SELECT u.user_id FROM " . DB_PREFIX . "user u WHERE u.user_id='" . (int)$user_id . "'")->row;
        if(!empty($query) && isset($query['user_id']) && $query['user_id']) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function to check the country exist or not
     *
     * @param integer $country_id
     * @return boolean
     */
    public function checkCountry($country_id = 0) {

        $query = $this->db->query("SELECT country_id FROM " . DB_PREFIX . "country WHERE country_id='" . (int)$country_id . "' AND status='1'")->row;

        if(!empty($query) && isset($query['country_id']) && $query['country_id']) {
            return true;
        } else {
            return false;
        } 
    }

    /**
     * function to check the zone exist or not
     *
     * @param [type] $zone_id
     * @return boolean
     */
    public function checkZone($zone_id = 0, $country_id = 0) {

        $query = $this->db->query("SELECT zone_id FROM " . DB_PREFIX . "zone WHERE zone_id='" . (int)$zone_id . "' AND status='1' AND country_id='" . (int)$country_id . "'")->row;

        if(!empty($query) && isset($query['zone_id']) && $query['zone_id']) {
            return true;
        } else {
            return false;
        }         
    }
    
    /**
     * function to Check the percentage type
     *
     * @param Type $var
     * @return boolean
     */
    public function checkPercentageType($percentage_type = '') {
        
        if($percentage_type) {
            if($percentage_type == 'f' || $percentage_type == 'p') {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Function to check the valid price perfix
     *
     * @param Type $var
     * @return boolean
     */
    public function checkPricePrefix($price_prefix = '') {

        if($price_prefix) {
            if($price_prefix == '-' || $price_prefix == '+') {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        } 
    }
    
    /**
     * Function to check if a variable is a number or not, include both decimal point and integer numbers
     *
     * @param string $number
     * @return void
     */
    public function checkIsNumber($value = '') {
        return (bool) preg_match("/^[0-9]*$/", $value);
    }

    public function checkRoute($route = '',$registry) {
        $url_route = '';
        $url_method = 'index';
        $claenRoute = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$route);
        $parts = explode('/', $claenRoute);
        while ($parts) {
            $file = DIR_CATALOG . 'controller/' . implode('/', $parts) . '.php';
           
			if (is_file($file)) {
			    $url_route = implode('/',$parts);
				break;
			} else {
				$url_method = array_pop($parts);
            }
        }
        if ($url_route) {
            $file  = DIR_CATALOG . 'controller/' .  $url_route . '.php';	
            $class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '',  $url_route);
            if (is_file($file)) {
                include_once($file);
                $controller = new $class($registry);
                if(method_exists($controller,$url_method) && is_callable(array($controller,$url_method))) {
                    return true;
                }
            }
        }
        return false;
    }

    public function checkColor($value = '') {
        return (bool) preg_match("/^#[a-zA-Z0-9]{6}$/", $value);
    }

    public function isAlphaNumeric($value) {
        return (bool) preg_match("/^[\p{L}\p{Nd}]+$/u", $value);
    }

    public function lengthRange($value, $limit=array()) {
        $min = isset($limit['min']) ? $limit['min'] : 1;
        $max = isset($limit['max']) ? $limit['max'] : 255;
        if ((utf8_strlen($value) < $min) || (utf8_strlen($value) > $max)) {
           return false;
        }
        return true;
    }

    public function isAlpha($value) {
            return (bool) preg_match("/^[\p{L}]+$/u", $value);
    }

    public function isArray($value) {
            return is_array($value);
    }

    public function isAssocArray($value) {
        return [] === $value ? false : array_keys($value) !== range(0, count($value) - 1);
    }

    public function isBool($value) {
        return is_bool($value);
    }

    public function isEmail($value) {
        return false !== \filter_var($value, FILTER_VALIDATE_EMAIL);;
    }

    public function isNotEmpty($value) {
            return strlen(trim($value)) > 0;
    }

    public function isNotExceedMaxLength($value, $constraint = 25) {
            return strlen($value) >= $constraint;
    }

    public function isNotExceedMinLength($value, $constraint = 10) {
            return strlen($value) <= $constraint;
    }

    public function isString($value) {
            return $this->_isNotEmpty($value) ? false: is_string($value);
    }

    public function isMax($value, $constraint) {
            return $value >= $constraint;
    }

    public function isMin($value, $constraint)	{
            return $value <= $constraint;
    }

    public function isInt($value)	{
            return is_int($value);
    }

    public function isUrl($value) {
            return filter_var($value, FILTER_VALIDATE_URL);
    }

    public function notEmpty($value)	{
            return is_string($value) ? (bool)trim($value) : (bool)$value;
    }
    
    public function _containsString ($sub_string = '' ,$string = '') {
        return preg_match($sub_string , $string) ? TRUE : FALSE;
    }
       
}
