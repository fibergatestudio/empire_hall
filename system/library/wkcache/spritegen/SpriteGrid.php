<?php
namespace wkcache\spritegen;
require_once('SpriteGridImage.php');

class SpriteGrid {

    private $spriteGd;

    private $nextId = 1;

    private $placedPictures = array();

    private $css = array();

    private $grids = array();

    private $width;

    private $height;

    private $tileWidth = 8;

    private $tileHeight = 8;

    private $acceptedFormat = array('png' => true);

    private $maxSurface = 40000; //200 * 200;

    private $gd = null;

    public function __construct() {
        $this->width = 800 / $this->tileWidth;
        $this->height = 10;

        for ($y = 0; $y < 256; $y++) {
            $this->grids[$y] = array_fill(0, $this->width+1, 0);
        }
    }

    /**
     * @param \DirectoryIterator $it
     * @return void
     */
    public function importImagesFromFolder(\DirectoryIterator $it) {
        foreach ($it as $file) {
            if ($file->isFile() && array_key_exists($file->getExtension(), $this->acceptedFormat)) {
                $img = SpriteGridImage::create($file->getFileInfo());

                if ($img->notExceedSurface($this->maxSurface))
                    $this->add($img);
            }
            unset($file);
        }
    }

    /**
     * @param SpriteGridImage $img
     * @return void
     */
    public function add(SpriteGridImage $img) {
        $img->id = $this->nextId;
        $this->nextId++;

        $img->width = ceil($img->pixelWidth / $this->tileWidth);
        $img->height = ceil($img->pixelHeight / $this->tileHeight);

        $placed = false;
        for ($y = 0; $y < $this->height && $placed === false; $y++) {
            for ($x = 0; $x < $this->width && $placed === false; $x++) {
                $img->x = $x;
                $img->y = $y;

                $placed = $this->canFit($img);

                if ($placed)
                {
                    $this->placedPictures[$img->id] = $img;
                    $this->assignToGrd($img);
                }
            }
        }
    }

    /**
     * @param SpriteGridImage $img
     * @return bool
     */
    private function canFit(SpriteGridImage $img) {
        $fit = true;

        $y1 = $img->y + $img->height;
        $x1 = $img->x + $img->width;

        if ($x1 >= $this->width)
            $fit = false;

        for ($y = $img->y; $y < $y1 && $fit === true; $y++) {
            for ($x = $img->x; $x < $x1 && $x1 <= $this->width && $fit === true; $x++) {
                $fit = $this->grids[$y][$x] === 0;
            }
        }

        return $fit;
    }

    /**
     * @param SpriteGridImage $img
     * @return void
     */
    private function assignToGrd(SpriteGridImage $img) {
        $y1 = $img->y + $img->height;
        $x1 = $img->x + $img->width;

        if ($y1 > $this->height)
            $this->height = $y1;

        for ($y = $img->y; $y <= $y1; $y++) {
            for ($x = $img->x; $x <= $x1; $x++) {
                $this->grids[$y][$x] = $img->file->getFilename();
            }
        }
    }

    /**
     * Return an True Color Image with Alpha channel.
     *
     * @return resource Gd Image
     */
    public function asGdImage() {
        /* @var $img SpriteGridImage */

        if ($this->gd)
            return $this->gd;

        $imgWidth = $this->width * $this->tileWidth;
        $imgHeight = $this->height * $this->tileHeight;
        
        $this->gd = imagecreatetruecolor($imgWidth, $imgHeight);
        imagealphablending($this->gd, false);
        $col = imagecolorallocatealpha($this->gd, 255, 255, 255, 127);
        imagefilledrectangle($this->gd, 0, 0, $imgWidth, $imgHeight, $col);
        $clrBlack = imagecolorallocate($this->gd, 0, 0, 0);

        foreach ($this->placedPictures as $img) {
            if ($img->file->getExtension() == 'png')
                $im = imagecreatefrompng($img->file->getPathName());

            if ($img->file->getExtension() == 'gif')
                $im = imagecreatefromgif($img->file->getPathName());

            $x = $img->x * $this->tileWidth;
            $y = $img->y * $this->tileHeight;

            imagealphablending($this->gd, true);
            imagecopy($this->gd, $im, $x, $y, 0, 0, $img->pixelWidth, $img->pixelHeight);
            imagedestroy($im);
        }

        imagealphablending($this->gd, false);
        return $this->gd;
    }

    /**
     * Return CSS
     *
     * @return string
     */
    public function asCss() {
        foreach ($this->placedPictures as $img) {
            $x = $img->x * $this->tileWidth;
            $y = $img->y * $this->tileHeight;
            
            $filePathname = str_replace(__DIR__ . '\\', '', $img->file->getPathname());
            $filePathname = str_replace('\\', '/', $filePathname);
            $fileNameNoExt = substr($img->file->getFileName(), 0, strrpos($img->file->getFileName(), '.'));

            $css = ".{$fileNameNoExt} { ";
            $css .= "background-image: url('sprite.png'); ";
            $css .= "background-position: -{$x}px -{$y}px; ";
            $css .= "width: {$img->pixelWidth}px; height: {$img->pixelHeight}px; ";
            $css .= "}";

            $this->css[] = $css;
        }

        return implode("\n", $this->css);
    }

    /**
     * @param $pathname
     * @return void
     */
    public function writeCss($pathname) {
        file_put_contents($pathname, $this->asCss());
    }

    /**
     * @param $pathname
     * @return void
     */
    public function writePng($pathname) {
        $gd = $this->asGdImage();
        imagesavealpha($gd, true);
        imagepng($gd, $pathname);
    }
}