<?php
// HTTP
define('HTTP_SERVER', 'http://' . $_SERVER['HTTP_HOST'] . '/admin/');
define('HTTP_CATALOG', 'http://' . $_SERVER['HTTP_HOST'] . '/');

// HTTPS
define('HTTPS_SERVER', 'http://' . $_SERVER['HTTP_HOST'] . '/admin/');
define('HTTPS_CATALOG', 'http://' . $_SERVER['HTTP_HOST'] . '/');

// DIR
define('DIR_DOC_ROOT', $_SERVER['DOCUMENT_ROOT']);


define('DIR_APPLICATION', DIR_DOC_ROOT . '/admin/');
define('DIR_SYSTEM', DIR_DOC_ROOT . '/system/');
define('DIR_IMAGE', DIR_DOC_ROOT . '/image/');
define('DIR_STORAGE', DIR_DOC_ROOT . '/system/storage/');
define('DIR_CATALOG', DIR_DOC_ROOT . '/catalog/');
define('DIR_LANGUAGE', DIR_APPLICATION . '/language/');
define('DIR_TEMPLATE', DIR_APPLICATION . '/view/template/');
define('DIR_CONFIG', DIR_SYSTEM . '/config/');
define('DIR_CACHE', DIR_STORAGE . '/cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . '/download/');
define('DIR_LOGS', DIR_STORAGE . '/logs/');
define('DIR_MODIFICATION', DIR_STORAGE . '/modification/');
define('DIR_SESSION', DIR_STORAGE . '/session/');
define('DIR_UPLOAD', DIR_STORAGE . '/upload/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'empirehall');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');

// OpenCart API
define('OPENCART_SERVER', 'https://www.opencart.com/');
