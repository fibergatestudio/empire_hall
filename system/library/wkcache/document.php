<?php
/**
 * @package		OpenCart
 * @author		Daniel Kerr
 * @copyright	Copyright (c) 2005 - 2017, OpenCart, Ltd. (https://www.opencart.com/)
 * @license		https://opensource.org/licenses/GPL-3.0
 * @link		https://www.opencart.com
*/

/**
* Document class
*/
class Document {
	private $title;
	private $ogtitle;
	private $description;
	private $ogdescription;
	private $keywords;

  // OCFilter start
  private $noindex = false;
  // OCFilter end
      
	private $ogimage;
	private $links = array();
	private $styles = array();
	private $scripts = array();
	private $tlt_metatags = array();
	
	public function addTLTMetaTag($name, $content, $type = 'name') {
		$this->tlt_metatags[$name] = array(
			'type'		=> $type,
			'name'		=> $name,
			'content'	=> $content
		);
	}
	
	public function getTLTMetaTags() {
		return $this->tlt_metatags;
	}
	/**
     * 
     *
     * @param	string	$title
     */

        private $seo_meta = '';

        public function addSeoMeta($html) {
          $this->seo_meta .= $html;
        }
        
        public function renderSeoMeta() {
          return $this->seo_meta;
        }
      

  // OCFilter start
  public function setNoindex($state = false) {
  	$this->noindex = $state;
  }

	public function isNoindex() {
		return $this->noindex;
	}
  // OCFilter end
      
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
     * 
	 * 
	 * @return	string
     */
	public function getTitle() {
		return $this->title;
	}
	/**
     *
     *
     * @param	string	$title
     */
	public function setOGTitle($title) {
		$this->ogtitle = $title;
	}

	/**
     *
	 *
	 * @return	string
     */
	public function getOGTitle() {
		return $this->ogtitle;
	}

	/**
     * 
     *
     * @param	string	$description
     */
	public function setOGDescription($description) {
		$this->ogdescription = $description;
	}

	/**
     * 
     *
     * @param	string	$description
	 * 
	 * @return	string
     */
	public function getOGDescription() {
		return $this->ogdescription;
	}

    /**
     *
     *
     * @param	string	$keywords
     */
    public function setOGImage($image) {
        $this->ogimage = $image;
    }

    /**
     *
     *
     * @return	string
     */
    public function getOGImage() {
        return $this->ogimage;
    }

/**
     *
     *
     * @param	string	$description
     */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
     *
     *
     * @param	string	$description
	 *
	 * @return	string
     */
	public function getDescription() {
		return $this->description;
	}

	/**
     * 
     *
     * @param	string	$keywords
     */
	public function setKeywords($keywords) {
		$this->keywords = $keywords;
	}

	/**
     *
	 * 
	 * @return	string
     */
	public function getKeywords() {
		return $this->keywords;
	}
	
	/**
     * 
     *
     * @param	string	$href
	 * @param	string	$rel
     */
	public function addLink($href, $rel) {
		$this->links[$href.$rel] = array(
			'href' => $href,
			'rel'  => $rel
		);
	}

	/**
     * 
	 * 
	 * @return	array
     */

  // OCFilter canonical fix start
	public function deleteLink($rel) {
    foreach ($this->links as $href => $link) {
      if ($link['rel'] == $rel) {
      	unset($this->links[$href]);
      }
    }
	}
  // OCFilter canonical fix end
      
	public function getLinks() {
		return $this->links;
	}

	/**
     * 
     *
     * @param	string	$href
	 * @param	string	$rel
	 * @param	string	$media
     */
	public function addStyle($href, $rel = 'stylesheet', $media = 'screen') {
		$this->styles[$href] = array(
			'href'  => $href,
			'rel'   => $rel,
			'media' => $media
		);
	}

	/**
     * 
	 * 
	 * @return	array
     */
	public function getStyles() {
		return $this->styles;
	}

	/**
     * 
     *
     * @param	string	$href
	 * @param	string	$postion
     */
	public function addScript($href, $postion = 'header') {
		$this->scripts[$postion][$href] = $href;
	}

	/**
     * 
     *
     * @param	string	$postion
	 * 
	 * @return	array
     */
	public function getScripts($postion = 'header') {
		if (isset($this->scripts[$postion])) {
			return $this->scripts[$postion];
		} else {
			return array();
		}
	}
}