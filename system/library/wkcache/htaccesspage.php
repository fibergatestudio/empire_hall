<?php

namespace Wkcache;

class Htaccesspage {

	private $file_type;

  private $disabled = false;

	private $expire_file_type;

	private $expire_disabled = false;

  public function __construct($registry) {
        $this->db = $registry->get('db');
        $this->config = $registry->get('config');
  }

  protected function setFileType($type) {
      $this->file_type = $type;
  }

    protected function getFileType() {
        return $this->file_type;
    }

    protected function setDisabled($sts = true) {
        $this->disabled = $sts;
    }

    protected function getDisabled() {
        return $this->disabled;
    }

    protected function resetDeflates() {
        $this->resetFiletypeDeflates();
        $this->disabled = false;
    }

  protected function resetFiletypeDeflates() {
        $this->file_type = '';
    }

  protected function getPlainDeflates() {
		return 'AddOutputFilterByType DEFLATE text/plain'.PHP_EOL;
	}

	protected function getCssDeflates($data) {
		$code = '';
		$this->setFileType("text/css");
		if($data['module_oc_cache_css_compression'] && isset($data['module_oc_cache_compression_status']) && $data['module_oc_cache_compression_status']) {
			$this->setDisabled(false);
			$enable_css = $this->getDeflateRules();
			$code .= $enable_css.PHP_EOL;
		} else {
			$this->setDisabled(true);
			$disable_css = $this->getDeflateRules();
			$code .= $disable_css.PHP_EOL;
		}
		$this->resetDeflates();
		return $code;
	}

	protected function getJsDeflates($data) {
		$code = '';
		$this->setFileType("application/javascript");
        $this->setDisabled(false);
		if($data['module_oc_cache_js_compression'] && isset($data['module_oc_cache_compression_status']) && $data['module_oc_cache_compression_status']) {
			$enable_js = $this->getDeflateRules();
			$this->setFileType("application/x-javascript");
			$enable_js .= PHP_EOL;
			$enable_js .= $this->getDeflateRules();
			$code .= $enable_js.PHP_EOL;
		} else {
			$this->setDisabled(true);
			$disable_js = $this->getDeflateRules();
			$disable_js .= PHP_EOL;
			$this->setFileType("application/x-javascript");
			$disable_js .= $this->getDeflateRules();
			$code .= $disable_js.PHP_EOL;
		}
		$this->resetDeflates();
		return $code;
	}

	protected function getImageIconDeflates($data) {
		$code = '';
		$this->setFileType("image/svg+xml");
		$this->setDisabled(false);
		if($data['module_oc_cache_image_compression'] && isset($data['module_oc_cache_compression_status']) && $data['module_oc_cache_compression_status']) {
			$enable_image_icon = $this->getDeflateRules();
		    $enable_image_icon .= PHP_EOL;
		    $this->setFileType("image/x-icon");
		    $enable_image_icon .= $this->getDeflateRules();
			$code .= $enable_image_icon.PHP_EOL;
		} else {
			$this->setDisabled(true);
			$disable_image_icon = $this->getDeflateRules();
			$disable_image_icon .= PHP_EOL;
			$this->setFileType("image/x-icon");
			$disable_image_icon .= $this->getDeflateRules();
			$code .= $disable_image_icon.PHP_EOL;
		}
		$this->resetDeflates();
		return $code;
	}

	protected function getHtmlDeflates($data) {
		$code = '';
		$this->setFileType("text/html");
		if($data['module_oc_cache_html_compression'] && isset($data['module_oc_cache_compression_status']) && $data['module_oc_cache_compression_status']) {
			$enable_html = $this->getDeflateRules();
			$code .= $enable_html.PHP_EOL;
		} else {
			$this->setDisabled(true);
            $disable_html = $this->getDeflateRules();
			$code .= $disable_html.PHP_EOL;
		}
		$this->resetDeflates();
		return $code;
	}

	protected function getXmlDeflates($data) {
		$code = '';
        $this->setFileType("application/xml");
        $this->setDisabled(false);
		if($data['module_oc_cache_xml_compression'] && isset($data['module_oc_cache_compression_status']) && $data['module_oc_cache_compression_status']) {
			$enable_xml = $this->getDeflateRules();
			$code .= $enable_xml.PHP_EOL;
		} else {
			$this->setDisabled(true);
            $disable_xml = $this->getDeflateRules();
			$code .= $disable_xml.PHP_EOL;
		}
		$this->resetDeflates();
		return $code;
	}

	protected function getXmlHtmlDeflates($data) {
		$code = '';
		$this->setFileType("application/xhtml+xml");
        $this->setDisabled(false);
		if($data['module_oc_cache_xhtml_xml_compression'] && isset($data['module_oc_cache_compression_status']) && $data['module_oc_cache_compression_status']) {
			$enable_xml_xhtml = $this->getDeflateRules();
			$code .= $enable_xml_xhtml.PHP_EOL;
		} else {
			$this->setDisabled(true);
            $disable_xml_xhtml = $this->getDeflateRules();
			$code .= $disable_xml_xhtml.PHP_EOL;
		}
		$this->resetDeflates();
		return $code;
	}

	protected function getRssXmlDeflates($data) {
		$code = '';
		$this->setFileType("application/rss+xml");
        $this->setDisabled(false);
		if($data['module_oc_cache_rss_xml_compression'] && isset($data['module_oc_cache_compression_status']) && $data['module_oc_cache_compression_status']) {
			$enable_xml_rss = $this->getDeflateRules();
			$code .= $enable_xml_rss.PHP_EOL;
		} else {
			$this->setDisabled(true);
            $disable_xml_rss = $this->getDeflateRules();
			$code .= $disable_xml_rss.PHP_EOL;
		}
		$this->resetDeflates();
		return $code;
	}
    /**
	 * create deflate module acceess rules function
	 *
	 * @param [type] $type
	 * @param boolean $disabled
	 * @return void
	 */
    private function getDeflateRules() {
        $type = $this->getFileType();
        $disabled = $this->getDisabled();
	    $basic = "AddOutputFilterByType DEFLATE ".$type;
	    if($disabled)
		   $basic = "#".$basic;
	    return $basic;
	}

	protected function getExpiresActiveOn() {
		return 'ExpiresActive On'.PHP_EOL;
	}

	protected function addComment($text = 'comment') {
		return '# '.$text .PHP_EOL;
	}

    protected function setExpiryFileType($type) {
        $this->expire_file_type = $type;
    }

    protected function getExpiryFileType() {
        return $this->expire_file_type;
    }

    protected function setExpiryDisabled($sts = true) {
        $this->expire_disabled = $sts;
    }

    protected function getExpiryDisabled() {
        return $this->expire_disabled;
	}

	protected function getImageCachingParams($data) {
		$code = '';
		if(isset($data['module_oc_cache_leverage_image']) && is_array($data['module_oc_cache_leverage_image']) && !empty($data['module_oc_cache_leverage_image']) && $data['module_oc_cache_leverage_browser_cache'] && isset($data['module_oc_cache_status']) && $data['module_oc_cache_status']) {
			$temp = $data['module_oc_cache_leverage_image'];
			$time = 1;
			if(isset($temp['time']) && $temp['time']) {
				$time = (int)$temp['time'];
			}
			$ageType = $temp['type'];
			$status = $temp['status'];
			$disabled = $status ? '' : '#';
			$images = $this->getImageTypes();
			$code = $this->genrateExpiresByTypeCachingParams($disabled,$images,$time,$ageType);
		}
		return $code;
	}

	protected function getVedioCachingParams($data) {
		$code = '';
		if(isset($data['module_oc_cache_leverage_videos']) && is_array($data['module_oc_cache_leverage_videos']) && !empty($data['module_oc_cache_leverage_videos']) && $data['module_oc_cache_leverage_browser_cache'] && isset($data['module_oc_cache_status']) && $data['module_oc_cache_status']) {
			$temp = $data['module_oc_cache_leverage_videos'];
			$time = 1;
			if(isset($temp['time']) && $temp['time']) {
				$time = (int)$temp['time'];
			}
			$ageType = $temp['type'];
			$status = $temp['status'];
			$disabled = $status ? '' : '#';
			$images = $this->getVideoTypes();
			$code = $this->genrateExpiresByTypeCachingParams($disabled,$images,$time,$ageType);
		}
		return $code;
	}

	protected function getJsCachingParams($data) {
		$code = '';
		if(isset($data['module_oc_cache_leverage_js']) && is_array($data['module_oc_cache_leverage_js']) && !empty($data['module_oc_cache_leverage_js']) && $data['module_oc_cache_leverage_browser_cache'] && isset($data['module_oc_cache_status']) && $data['module_oc_cache_status']) {
			$temp = $data['module_oc_cache_leverage_js'];
			$time = 1;
			if(isset($temp['time']) && $temp['time']) {
				$time = (int)$temp['time'];
			}
			$ageType = $temp['type'];
			$status = $temp['status'];
			$disabled = $status ? '' : '#';
			$images = $this->getJsTypes();
			$code = $this->genrateExpiresByTypeCachingParams($disabled,$images,$time,$ageType);
		}
		return $code;
	}

	protected function getCssCachingParams($data) {
		$code = '';
		if(isset($data['module_oc_cache_leverage_css']) && is_array($data['module_oc_cache_leverage_css']) && !empty($data['module_oc_cache_leverage_css']) && $data['module_oc_cache_leverage_browser_cache'] && isset($data['module_oc_cache_status']) && $data['module_oc_cache_status']) {
			$temp = $data['module_oc_cache_leverage_css'];
			$time = 1;
			if(isset($temp['time']) && $temp['time']) {
				$time = (int)$temp['time'];
			}
			$ageType = $temp['type'];
			$status = $temp['status'];
			$disabled = $status ? '' : '#';
			$images = $this->getCssTypes();
			$code = $this->genrateExpiresByTypeCachingParams($disabled,$images,$time,$ageType);
		}
		return $code;
	}

	protected function getOtherCachingParams($data) {
		$code = '';
		if(isset($data['module_oc_cache_leverage_other']) && is_array($data['module_oc_cache_leverage_other']) && !empty($data['module_oc_cache_leverage_other']) && $data['module_oc_cache_leverage_browser_cache'] && isset($data['module_oc_cache_status']) && $data['module_oc_cache_status']) {
			$temp = $data['module_oc_cache_leverage_other'];
			$time = 1;
			if(isset($temp['time']) && $temp['time']) {
				$time = (int)$temp['time'];
			}
			$ageType = $temp['type'];
			$status = $temp['status'];
			$disabled = $status ? '' : '#';
			$images = $this->getOtherTypes();
			$code = $this->genrateExpiresByTypeCachingParams($disabled,$images,$time,$ageType);
		}
		return $code;
	}

	protected function genrateExpiresByTypeCachingParams($disabled,$images,$time,$ageType){
		$code = '';
		foreach($images as $type) {
			$code .= $disabled.'ExpiresByType '.$type. ' "access plus '.$time.' '.$ageType . '"'.PHP_EOL;
		}
		return $code;
	}

	protected function getOtherTypes(){
		return  array(
			'application/pdf',
			'pplication/x-shockwave-flash'
		);
	}

	protected function getVideoTypes(){
		return  array(
			'video/mp4',
			'video/mpeg'
		);
	}

	protected function getJsTypes(){
		return array(
			'text/javascript',
			'application/javascript'
		);
	}

	protected function getCssTypes(){
		return array(
			'text/css'
		);
	}

	protected function getImageTypes(){
		return array(
			'image/jpeg',
			'image/gif',
			'image/png',
			'image/webp',
			'image/svg+xml',
			'image/x-icon',
		);
	}

	public function leverageCaching($data) {
		$htaccess_backup = '';

		$inserted = false;

		if($this->checkFileExst(DIR_CATALOG.'../.htaccess')) {
			$htaccess_backup = file_get_contents(DIR_CATALOG.'../.htaccess');
			$code = '';
			if ($fh = fopen(DIR_CATALOG.'../.htaccess', 'r')) {
				while (!feof($fh)) {
					$line = fgets($fh);
					if (preg_match('/\ExpiresByType\b/',$line)) {
						if(!$inserted) {
							$code .= $this->addComment('set expireactive as ON');
							$code .= $this->getExpiresActiveOn();
							$code .= $this->addComment('Images');
							$code .= $this->getImageCachingParams($data);
							$code .= $this->addComment('Vedios');
							$code .= $this->getVedioCachingParams($data);
							$code .= $this->addComment('Javascript');
							$code .= $this->getJsCachingParams($data);
							$code .= $this->addComment('CSS');
							$code .= $this->getCssCachingParams($data);
							$code .= $this->addComment('Others');
                            $code .= $this->getOtherCachingParams($data);
							$inserted = true;
						}
						continue;
					}
					$code .= $line;
				}
				if(!$inserted) {
					$code .= PHP_EOL.'<IfModule mod_expires.c>'.PHP_EOL;
					$code .= $this->addComment('set expireactive as ON');
					$code .= $this->getExpiresActiveOn();
					$code .= $this->addComment('Images');
					$code .= $this->getImageCachingParams($data);
					$code .= $this->addComment('Vedios');
					$code .= $this->getVedioCachingParams($data);
					$code .= $this->addComment('Javascript');
					$code .= $this->getJsCachingParams($data);
					$code .= $this->addComment('CSS');
					$code .= $this->getCssCachingParams($data);
					$code .= $this->addComment('Others');
					$code .= $this->getOtherCachingParams($data);
					$code .= '</IfModule>'.PHP_EOL;
					$inserted = true;
				}
				fclose($fh);
			}
			file_put_contents(DIR_CATALOG.'../.htaccess_backup', $htaccess_backup);
			file_put_contents(DIR_CATALOG.'../.htaccess', $code);
		}
	}

  public function gzipCompression($data) {
		$htaccess_backup = '';
		$inserted = false;

		if(file_exists(DIR_CATALOG.'../.htaccess')) {
			$htaccess_backup = file_get_contents(DIR_CATALOG.'../.htaccess');
			$code = '';
			if ($fh = fopen(DIR_CATALOG.'../.htaccess', 'r')) {
				while (!feof($fh)) {
					$line = fgets($fh);
					if (preg_match('/\DEFLATE\b/',$line)) {
						if(!$inserted) {
							$code .= $this->getPlainDeflates();
							$code .= $this->getCssDeflates($data);
                            $code .= $this->getJsDeflates($data);
							$code .= $this->getHtmlDeflates($data);
							$code .= $this->getImageIconDeflates($data);
							$code .= $this->getXmlDeflates($data);
							$code .= $this->getXmlHtmlDeflates($data);
							$code .= $this->getRssXmlDeflates($data);
							$inserted = true;
						}
						continue;
					}
					$code .= $line;
				}
				if(!$inserted) {
					$code .= PHP_EOL.'<IfModule mod_deflate.c>'.PHP_EOL;
					$code .= $this->getPlainDeflates();
					$code .= $this->getCssDeflates($data);
					$code .= $this->getJsDeflates($data);
					$code .= $this->getHtmlDeflates($data);
					$code .= $this->getImageIconDeflates($data);
					$code .= $this->getXmlDeflates($data);
					$code .= $this->getXmlHtmlDeflates($data);
					$code .= $this->getRssXmlDeflates($data);
					$code .= '</IfModule>'.PHP_EOL;
					$inserted = true;
				}
				fclose($fh);
			}
			file_put_contents(DIR_CATALOG.'../.htaccess_backup', $htaccess_backup);
			file_put_contents(DIR_CATALOG.'../.htaccess', $code);
		}
	}

	public function checkFileExst($filepath) {
        return is_file($filepath) ? file_exists(DIR_CATALOG.'../.htaccess') : false;
	}

	public function getFileContents($filepath) {
        return $this->checkFileExst($filepath) ? file_get_contents($filepath) : '';
	}

	public function getHtaccessPath() {
        return DIR_CATALOG.'../.htaccess';
	}

	public function getBackupHtaccessPath() {
        return DIR_CATALOG.'../.htaccess_backup';
	}

    protected function getReseverHtaaccessCode() {
		$code = '';
		$code .='<IfModule mod_headers.c>'.PHP_EOL;
		$code .= 'RewriteCond "%{HTTP:Accept-encoding}" "gzip"'.PHP_EOL;
		$code .= 'RewriteCond "%{REQUEST_FILENAME}\.gz" -s'.PHP_EOL;
		$code .= 'RewriteRule "^(.*)\.css" "$1\.css\.gz" [QSA]'.PHP_EOL;
		$code .= 'RewriteCond "%{HTTP:Accept-encoding}" "gzip"'.PHP_EOL;
		$code .= 'RewriteCond "%{REQUEST_FILENAME}\.gz" -s'.PHP_EOL;
		$code .= 'RewriteRule "^(.*)\.js" "$1\.js\.gz" [QSA]'.PHP_EOL;
		$code .= 'RewriteRule "\.css\.gz$" "-" [T=text/css,E=no-gzip:1]'.PHP_EOL;
		$code .= 'RewriteRule "\.js\.gz$" "-" [T=text/javascript,E=no-gzip:1]'.PHP_EOL;
		$code .='<FilesMatch "(\.js\.gz|\.css\.gz)$">'.PHP_EOL;
		$code .= 'Header append Content-Encoding gzip'.PHP_EOL;
		$code .= 'Header append Vary Accept-Encoding'.PHP_EOL;
		$code .='</FilesMatch>'.PHP_EOL;
		$code .='</IfModule>'.PHP_EOL;
		return $code;
	}

	public function serveGzipData($status = false) {
		$htaccess_backup = '';

		$filepath = $this->getHtaccessPath();
		$status_check = true;

		if($this->checkFileExst($filepath)) {
			$htaccess_backup = file_get_contents($filepath);
			$code = '';
			if ($fh = fopen($filepath, 'r')) {
				while (!feof($fh)) {
					$line = fgets($fh, 4096);
					if (preg_match('/\RewriteCond\b/',$line) || preg_match('/\RewriteRule\b/',$line) || preg_match('/\HTTP:Accept-encoding\b/',$line)) {
						$status_check = false;
					}
					if (preg_match('/\Header append\b/',$line) || preg_match('/\Content-Encoding gzip\b/',$line) || preg_match('/\Vary Accept-Encoding\b/',$line)) {
						$status_check = false;
					}
					$code .= $line;
				}
			}
			fclose($fh);


			if($status_check) {
               $code .= $this->getReseverHtaaccessCode();
			}

			$backup_path_file = $this->getBackupHtaccessPath();

			file_put_contents($backup_path_file, $htaccess_backup);
			file_put_contents($filepath, $code);
		}
	}

}
