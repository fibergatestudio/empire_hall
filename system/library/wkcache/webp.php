<?php
namespace Wkcache;
/**
 * [Webp description]
 */
class Webp {

    private $source = '';

    private $dest = '';

    private $options = [];

    private function setDest($image) {
        $image = $this->removeDestinationExtension($image);
        $this->dest = $image . '.webp';;
    }

    private function getDest() {
        return  $this->dest;
    }

    private function setSource($image) {
        $this->source = $image;
    }

    private function getSource() {
        return  $this->source;
    }

    public function convert($image) {
        $this->setSource($image);
        $this->setDest($image);
        $this->runConverter($image);
    }

    private function isSource() {
        return  isset($this->source) && is_file($this->source) ? true : false;
    }

    private function isDest() {
        return  isset($this->dest) ? true : false;
    }


    private function removeDestinationExtension($imgs) {
        return preg_replace('/\\.[^.\\s]{3,4}$/', '',$imgs);
    }

    public function convertImage($path, $quality) {
       ini_set('memory_limit', '1G');
       set_time_limit(120);

       // $path = $this->removeDestinationExtension($path);

       $path = str_replace(' ', '%20', $path);

       try {
        if (!is_writable($path)) {
          throw new \Exception(sprintf('File "%s" does not exist.', $path));
        }

        $response = $this->createImage($path);

        if (!$response['success']) {
            throw new \Exception($response['message']);
        } else {
          $image = $response['data'];
        }

        $response = $this->convertColorPalette($image, $path);

        if (!$response['success']) throw new \Exception($response['message']);
        else $image = $response['data'];

        $response = $this->convertToWebp($image, $path, $quality);

        if (!$response['success']) throw new \Exception($response['message']);
        else return [
          'success' => true,
          'data'    => $response['data'],
        ];
      } catch (\Exception $e) {
        return [
          'success' => false,
          'message' => $e->getMessage(),
        ];
      }
  }

  private function createImage($path) {
    $extension = pathinfo($path, PATHINFO_EXTENSION);

    $methods   = [
      'imagecreatefromjpeg' => ['jpg', 'jpeg'],
      'imagecreatefrompng'  => ['png'],
      'imagecreatefromgif'  => ['gif'],
    ];

    try {
      foreach ($methods as $method => $extensions) {
        if (!in_array($extension, $extensions)) {
          continue;
        } else if (!function_exists($method)) {
          throw new \Exception(sprintf('Server configuration: "%s" function is not available.', $method));
        } else if (!$image = @$method($path)) {
          throw new \Exception(sprintf('"%s" is not a valid image file.', $path));
        }
      }
      if (!isset($image)) {
        throw new \Exception(sprintf('Unsupported extension "%s" for file "%s"', $extension, $path));
      }
      return [
        'success' => true,
        'data'    => $image,
      ];
    } catch (\Exception $e) {
      return [
        'success' => false,
        'message' => $e->getMessage(),
      ];
    }
  }

  private function convertColorPalette($image) {
      try {
        if (!function_exists('imageistruecolor')) {
          throw new \Exception(sprintf('Server configuration: "%s" function is not available.', 'imageistruecolor'));
        } else if (!imageistruecolor($image)) {
          if (!function_exists('imagepalettetotruecolor')) {
            throw new \Exception(sprintf('Server configuration: "%s" function is not available.', 'imagepalettetotruecolor'));
          }
          imagepalettetotruecolor($image);
        }

        return [
          'success' => true,
          'data'    => $image,
        ];
      } catch (\Exception $e) {
        return [
          'success' => false,
          'message' => $e->getMessage(),
        ];
      }
  }

  private function convertToWebp($image, $path, $quality) {
    try {
      $directory = new WKDirectory();
      $output    = $directory->getPath($path, true);

      if (!$output) {
        throw new \Exception(sprintf('An error occurred creating destination directory for "%s" file.', $path));
      } else if (!function_exists('imagewebp')) {
        throw new \Exception(sprintf('Server configuration: "%s" function is not available.', 'imagewebp'));
      } else if ((imagesx($image) > 8192) || (imagesy($image) > 8192)) {
        throw new \Exception(sprintf('Image is larger than maximum 8K resolution: "%s".', $path));
      } else if (!$success = imagewebp($image, $output, $quality)) {
        throw new \Exception(sprintf('Error occurred while converting image: "%s".', $path));
      }

      if (filesize($output) % 2 === 1) file_put_contents($output, "\0", FILE_APPEND);
      return [
        'success' => true,
        'data'    => [
          'path' => $output,
          'size' => [
            'before' => filesize($path),
            'after'  => filesize($output),
          ],
        ],
      ];
    } catch (\Exception $e) {
      return [
        'success' => false,
        'message' => $e->getMessage(),
      ];
    }
    return $image;
  }
}

class WKDirectory {
	public function getPath($path, $createDirectory = false) {
		$pathSource = $pathOutput = '';

    $newPath    = str_replace("/${pathSource}/", "/${pathOutput}/", $path) . '.webp';

		if (!$createDirectory) :
      return $newPath;
    endif;

		if (!$paths = $this->checkDirectories($newPath)) return $newPath;
		else if (!$this->makeDirectories($paths)) return null;
		else return $newPath;
	}

	private function checkDirectories($path) {
		$current = dirname($path);
		$paths   = [];
		while (!file_exists($current)) :
			$paths[] = $current;
			$current = dirname($current);
		endwhile;
		return $paths;
	}

	private function makeDirectories($paths) {
		$paths = array_reverse($paths);
		foreach ($paths as $path) {
			if (!is_writable(dirname($path))):
         return;
      endif;
			mkdir($path);
		}
		return true;
	}
}
