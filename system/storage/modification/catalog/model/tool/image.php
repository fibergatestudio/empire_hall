<?php
class ModelToolImage extends Model {

        private function compress($source, $destination) {
          $info = getimagesize($source);
          if ($info['mime'] == 'image/jpeg' || $info['mime'] == 'image/jpg')
            $image = imagecreatefromjpeg($source);

          elseif ($info['mime'] == 'image/gif')
            $image = imagecreatefromgif($source);

          elseif ($info['mime'] == 'image/png')
            $image = imagecreatefrompng($source);

          $quality = 100;

          if($this->config->get('module_oc_cache_image_compression_quality')) {
            $quality = $this->config->get('module_oc_cache_image_compression_quality');
          }

          imagejpeg($image, $destination, $quality);
        }
      
	public function resize($filename, $width, $height) {
		if (!is_file(DIR_IMAGE . $filename) || substr(str_replace('\\', '/', realpath(DIR_IMAGE . $filename)), 0, strlen(DIR_IMAGE)) != DIR_IMAGE) {
			return;
		}

		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		if('svg' == $extension) {


        if ($this->config->get('module_oc_cache_status') && $this->config->get('module_oc_cache_image_webp')) {

            $this->registry->set('webp', new wkcache\webp());

            $quality = 100;

            if ($this->config->get('module_oc_cache_compression_status')  && $this->config->get('module_oc_cache_image_compression')) {
              $quality = $this->config->get('module_oc_cache_image_compression');
            }

            $result = $this->webp->convertImage(DIR_IMAGE . $image_new,100);

            if (isset($result['success']) && $result['success'] && isset($result['data']['path'])) {

              if ($this->request->server['HTTPS']) {
                return $this->config->get('config_ssl') . 'image/' . $image_new.'.webp';
              } else {
                return $this->config->get('config_url') . 'image/' . $image_new.'.webp';
              }
            }
        }
        if($this->config->get('module_oc_cache_status') && $this->config->get('module_oc_cache_compression_status')  && $this->config->get('module_oc_cache_image_compression')) {
          $this->compress(DIR_IMAGE . $image_new, DIR_IMAGE .$image_new);
        }

      
			if ($this->request->server['HTTPS']) {
				return HTTPS_SERVER . 'image/' . $filename;
			} else {
				return HTTP_SERVER . 'image/' . $filename;
			}
		}

		$image_old = $filename;
		$image_new = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . (int)$width . 'x' . (int)$height . '.' . $extension;

		if (!is_file(DIR_IMAGE . $image_new) || (filectime(DIR_IMAGE . $image_old) > filectime(DIR_IMAGE . $image_new))) {
			list($width_orig, $height_orig, $image_type) = getimagesize(DIR_IMAGE . $image_old);

			if (!in_array($image_type, array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF))) {
				return DIR_IMAGE . $image_old;
			}

			$path = '';

			$directories = explode('/', dirname($image_new));

			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;

				if (!is_dir(DIR_IMAGE . $path)) {
					@mkdir(DIR_IMAGE . $path, 0777);
				}
			}

			if ($width_orig != $width || $height_orig != $height) {
				$image = new Image(DIR_IMAGE . $image_old);
				$image->resize($width, $height);
				$image->save(DIR_IMAGE . $image_new);
			} else {
				copy(DIR_IMAGE . $image_old, DIR_IMAGE . $image_new);
			}
		}

		$image_new = str_replace(' ', '%20', $image_new);  // fix bug when attach image on email (gmail.com). it is automatic changing space " " to +


        if ($this->config->get('module_oc_cache_status') && $this->config->get('module_oc_cache_image_webp')) {

            $this->registry->set('webp', new wkcache\webp());

            $quality = 100;

            if ($this->config->get('module_oc_cache_compression_status')  && $this->config->get('module_oc_cache_image_compression')) {
              $quality = $this->config->get('module_oc_cache_image_compression');
            }

            $result = $this->webp->convertImage(DIR_IMAGE . $image_new,100);

            if (isset($result['success']) && $result['success'] && isset($result['data']['path'])) {

              if ($this->request->server['HTTPS']) {
                return $this->config->get('config_ssl') . 'image/' . $image_new.'.webp';
              } else {
                return $this->config->get('config_url') . 'image/' . $image_new.'.webp';
              }
            }
        }
        if($this->config->get('module_oc_cache_status') && $this->config->get('module_oc_cache_compression_status')  && $this->config->get('module_oc_cache_image_compression')) {
          $this->compress(DIR_IMAGE . $image_new, DIR_IMAGE .$image_new);
        }

      
		if ($this->request->server['HTTPS']) {
			return $this->config->get('config_ssl') . 'image/' . $image_new;
		} else {
			return $this->config->get('config_url') . 'image/' . $image_new;
		}
	}

	// Function to crop an image with given dimensions. What doesn/t fit will be cut off.
	function cropsize($filename, $width, $height) {

		if (!file_exists(DIR_IMAGE . $filename) || !is_file(DIR_IMAGE . $filename)) {
			return;
		}

		$info = pathinfo($filename);
		$extension = $info['extension'];

		if('svg' == $extension) {


        if ($this->config->get('module_oc_cache_status') && $this->config->get('module_oc_cache_image_webp')) {

            $this->registry->set('webp', new wkcache\webp());

            $quality = 100;

            if ($this->config->get('module_oc_cache_compression_status')  && $this->config->get('module_oc_cache_image_compression')) {
              $quality = $this->config->get('module_oc_cache_image_compression');
            }

            $result = $this->webp->convertImage(DIR_IMAGE . $image_new,100);

            if (isset($result['success']) && $result['success'] && isset($result['data']['path'])) {

              if ($this->request->server['HTTPS']) {
                return $this->config->get('config_ssl') . 'image/' . $image_new.'.webp';
              } else {
                return $this->config->get('config_url') . 'image/' . $image_new.'.webp';
              }
            }
        }
        if($this->config->get('module_oc_cache_status') && $this->config->get('module_oc_cache_compression_status')  && $this->config->get('module_oc_cache_image_compression')) {
          $this->compress(DIR_IMAGE . $image_new, DIR_IMAGE .$image_new);
        }

      
			if ($this->request->server['HTTPS']) {
				return HTTPS_SERVER . 'image/' . $filename;
			} else {
				return HTTP_SERVER . 'image/' . $filename;
			}
		}

		$old_image = $filename;
		$new_image = 'cache/' . substr($filename, 0, strrpos($filename, '.')) . '-cr-' . $width . 'x' . $height . '.' . $extension;

		if (!file_exists(DIR_IMAGE . $new_image) || (filemtime(DIR_IMAGE . $old_image) > filemtime(DIR_IMAGE . $new_image))) {
			$path = '';

			$directories = explode('/', dirname(str_replace('../', '', $new_image)));

			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;

				if (!file_exists(DIR_IMAGE . $path)) {
					@mkdir(DIR_IMAGE . $path, 0777);
				}
			}

			$image = new Image(DIR_IMAGE . $old_image);
			$image->cropsize($width, $height);
			$image->save(DIR_IMAGE . $new_image);
		}


        if ($this->config->get('module_oc_cache_status') && $this->config->get('module_oc_cache_image_webp')) {

            $this->registry->set('webp', new wkcache\webp());

            $quality = 100;

            if ($this->config->get('module_oc_cache_compression_status')  && $this->config->get('module_oc_cache_image_compression')) {
              $quality = $this->config->get('module_oc_cache_image_compression');
            }

            $result = $this->webp->convertImage(DIR_IMAGE . $image_new,100);

            if (isset($result['success']) && $result['success'] && isset($result['data']['path'])) {

              if ($this->request->server['HTTPS']) {
                return $this->config->get('config_ssl') . 'image/' . $image_new.'.webp';
              } else {
                return $this->config->get('config_url') . 'image/' . $image_new.'.webp';
              }
            }
        }
        if($this->config->get('module_oc_cache_status') && $this->config->get('module_oc_cache_compression_status')  && $this->config->get('module_oc_cache_image_compression')) {
          $this->compress(DIR_IMAGE . $image_new, DIR_IMAGE .$image_new);
        }

      
		if ($this->request->server['HTTPS']) {
			return $this->config->get('config_ssl') . 'image/' . $new_image;
		} else {
			return $this->config->get('config_url') . 'image/' . $new_image;
		}

	}

	// Function to resize image with one given max size.
	function onesize($filename, $maxsize) {

		if (!file_exists(DIR_IMAGE . $filename) || !is_file(DIR_IMAGE . $filename)) {
			return;
		}

		$info = pathinfo($filename);
		$extension = $info['extension'];

		if('svg' == $extension) {


        if ($this->config->get('module_oc_cache_status') && $this->config->get('module_oc_cache_image_webp')) {

            $this->registry->set('webp', new wkcache\webp());

            $quality = 100;

            if ($this->config->get('module_oc_cache_compression_status')  && $this->config->get('module_oc_cache_image_compression')) {
              $quality = $this->config->get('module_oc_cache_image_compression');
            }

            $result = $this->webp->convertImage(DIR_IMAGE . $image_new,100);

            if (isset($result['success']) && $result['success'] && isset($result['data']['path'])) {

              if ($this->request->server['HTTPS']) {
                return $this->config->get('config_ssl') . 'image/' . $image_new.'.webp';
              } else {
                return $this->config->get('config_url') . 'image/' . $image_new.'.webp';
              }
            }
        }
        if($this->config->get('module_oc_cache_status') && $this->config->get('module_oc_cache_compression_status')  && $this->config->get('module_oc_cache_image_compression')) {
          $this->compress(DIR_IMAGE . $image_new, DIR_IMAGE .$image_new);
        }

      
			if ($this->request->server['HTTPS']) {
				return HTTPS_SERVER . 'image/' . $filename;
			} else {
				return HTTP_SERVER . 'image/' . $filename;
			}
		}

		//$old_image = $filename;
		//$new_image = 'cache/' . substr($filename, 0, strrpos($filename, '.')) . '-max-' . $maxsize . '.' . $extension;

		$old_image = $filename;
		$new_image = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . $maxsize . '.' . $extension;

		if (!file_exists(DIR_IMAGE . $new_image) || (filemtime(DIR_IMAGE . $old_image) > filemtime(DIR_IMAGE . $new_image))) {
			$path = '';

			$directories = explode('/', dirname(str_replace('../', '', $new_image)));

			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;

				if (!file_exists(DIR_IMAGE . $path)) {
					@mkdir(DIR_IMAGE . $path, 0777);
				}
			}

			$image = new Image(DIR_IMAGE . $old_image);
			$image->onesize($maxsize);
			$image->save(DIR_IMAGE . $new_image);
		}


        if ($this->config->get('module_oc_cache_status') && $this->config->get('module_oc_cache_image_webp')) {

            $this->registry->set('webp', new wkcache\webp());

            $quality = 100;

            if ($this->config->get('module_oc_cache_compression_status')  && $this->config->get('module_oc_cache_image_compression')) {
              $quality = $this->config->get('module_oc_cache_image_compression');
            }

            $result = $this->webp->convertImage(DIR_IMAGE . $image_new,100);

            if (isset($result['success']) && $result['success'] && isset($result['data']['path'])) {

              if ($this->request->server['HTTPS']) {
                return $this->config->get('config_ssl') . 'image/' . $image_new.'.webp';
              } else {
                return $this->config->get('config_url') . 'image/' . $image_new.'.webp';
              }
            }
        }
        if($this->config->get('module_oc_cache_status') && $this->config->get('module_oc_cache_compression_status')  && $this->config->get('module_oc_cache_image_compression')) {
          $this->compress(DIR_IMAGE . $image_new, DIR_IMAGE .$image_new);
        }

      
		if ($this->request->server['HTTPS']) {
			return $this->config->get('config_ssl') . 'image/' . $new_image;
		} else {
			return $this->config->get('config_url') . 'image/' . $new_image;
		}

	}
}
