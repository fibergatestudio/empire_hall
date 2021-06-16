<?php
namespace Cart;

// Include composer autoloader
// require DIR_SYSTEM . '../src/autoload.php';
//require DIR_SYSTEM . '../vendor/autoload.php';

use phpFastCache\CacheManager;
use phpFastCache\Drivers\Files\Driver;
use phpFastCache\Core\phpFastCache;
use phpFastCache\Util\Directory;


class WebkulCache {
	private $CacheDriver = array();

	public function __construct($registry) {

		if(file_exists(DIR_SYSTEM . '../vendor/autoload.php')) {
			require DIR_SYSTEM . '../vendor/autoload.php';
		} else {
			echo 'Kindly run the composer file given on the root of the project';
			die();
		}
		$this->config = $registry->get('config');
	}

	public function get_InstanceCache($cacheDriver) {
		if($this->config->get('oc_cache_timezone'))
			date_default_timezone_set($this->config->get('oc_cache_timezone'));

		CacheManager::setDefaultConfig(array(
		    "path" => DIR_SYSTEM.'webkul_cache/',
		));

		if(isset($this->CacheDriver[$cacheDriver])){
			$InstanceCache = $this->CacheDriver[$cacheDriver];
		}else{
			$InstanceCache = CacheManager::getInstance($cacheDriver);
			$this->CacheDriver[$cacheDriver] =  $InstanceCache;
		}
		return $InstanceCache;
	}

	public function getCacheFileSize($cache_name = false, $cacheDriver){
		$InstanceCache 	= $this->get_InstanceCache($cacheDriver);
		$path 			= $InstanceCache->getPath();
		if($cacheDriver !== null){
			// $driverPath =  $path.'/'.$cacheDriver;
			$driverPath =  $path;
		}

		if($cache_name){
			$getCacheFileName 	= md5($cache_name);
			$getCacheFolderName = substr($getCacheFileName, 0, 2);
			$getCacheFolderName1 = substr($getCacheFileName, 2, 2);
			$path = rtrim($driverPath, '/') . '/' .$getCacheFolderName . '/' . $getCacheFolderName1;	

			return $path . '/' . $getCacheFileName . '.txt';
		}else{
			return $driverPath;
		}
	}
	public function deleteCache($cache_name = false, $cacheDriver){
		$InstanceCache 	= $this->get_InstanceCache($cacheDriver);
		$path 			= $InstanceCache->getPath();
		if($cacheDriver !== null){
			$driverPath =  $path.'/'.$cacheDriver;
		}
		foreach ($cache_name as $file_name) {
			$getCacheFileName 	= md5($file_name);
			$getCacheFolderName = substr($getCacheFileName, 0, 2);
			$path_folder 	= rtrim($driverPath, '/') . '/' .$getCacheFolderName;
			$path_file 		= $path_folder . '/' . $getCacheFileName . '.txt';
			unlink($path_folder . '/' . $getCacheFileName . '.txt');
			rmdir($path_folder);
		}
	}

	public function clearCacheFiles($cacheDriver){
		try {
			$InstanceCache 	= $this->get_InstanceCache($cacheDriver);
			$InstanceCache->getItem('product_featured')->expiresAt(new \DateTime('2001-01-01 12:30:12'));
			return $InstanceCache->clear();
		} catch(Exception $ex) {
			return $ex->getMsg();
		}
	}

	public function getStats($cacheDriver){
		$InstanceCache 	= $this->get_InstanceCache($cacheDriver);
		$path 			= $InstanceCache->getPath();
		if(file_exists($path)) {
			$filesize 		= Directory::dirSize($path);
		} else {
			$filesize = 0;
		}
		if(file_exists($path)) {
			$total_files 	= Directory::getFileCount($path);
		} else {
			$total_files = 0;
		}
		$expireTime = $InstanceCache->getItem('product_featured')->getExpirationDate();

		return array('driver' => 'Files','total_size'=> $filesize, 'total_files' => $total_files,'expire_time' => $expireTime);
	}
}
