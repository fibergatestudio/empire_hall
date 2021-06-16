<?php
namespace Wkcache;
/**
* Document class
*/
class Wkdocument {
	private $title;
	private $ogtitle;
	private $description;
	private $ogdescription;
	private $keywords;
	private $ogimage;
	private $links = array();
	private $styles = array();
	private $scripts = array();
    private $tlt_metatags = array();


    private $twittercard;
    private $twitterimage;
    private $twittertitle;
    private $twitterdescription;
    private $articleauthor;

    public function setTwitterCard($twittercard) {
        $this->twittercard = $twittercard;
    }

    public function setTwitterImage($twitterimage) {
        $this->twitterimage= $twitterimage;
    }

    public function setTwitterTitile($twittertitle) {
        $this->twittertitle= $twittertitle;
    }

    public function setTwitterDescription($twitterdescription) {
        $this->twitterdescription= $twitterdescription;
    }

    public function setArticleAuthor($articleauthor) {
        $this->articleauthor = $articleauthor;
    }

    public function getTwitterCard() {
        return $this->twittercard;
    }

    public function getTwitterImage() {
        return $this->twitterimage;
    }

    public function getTwitterTitile() {
        return $this->twittertitle;
    }

    public function getTwitterDescription() {
        return $this->twitterdescription;
    }

    public function getArticleAuthor() {
        return $this->articleauthor;
    }

	public function __construct($registry) {
		if (!is_dir(DIR_APPLICATION. 'view/wkcache/assets/')) {

			mkdir(DIR_APPLICATION. 'view/wkcache/assets/', 0777, true);
		}

		$this->config = $registry->get('config');
	}

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
		$this->links[$href] = array(
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

	public function getStyles($styles = array()) {

		$styles = array_merge($this->styles, $styles);

        $minify = true;

		// if ($minify) {
		// 	$styles = array(
		// 		Minifier::minifyStyles($styles),
		// 	);
		// }

		$inline = true;

		if ($inline) {

			foreach ($styles as &$style) {
				if (is_file($style['href'])) {
					$style['content'] = file_get_contents($style['href']);
				}
			}
		}

		return $styles;
	}

	public function addScript($href, $position = 'header') {
		$this->scripts[$position][$href] = $href;
	}

	public function htmlLoad($html) {
		return Minifier::minifyHTML($html);

	}

	public function getScripts($position = 'header', $scripts = array()) {
		
		if($this->config->get('module_oc_cache_minify_js')) {
			$minify = true;
		} else {
			$minify = false;
		}

		// $this->addScript('catalog/view/javascript/jquery/jquery-2.1.1.min.js', 'header');
	    $this->addScript('catalog/view/javascript/lazy.js','footer');
		   
        if (isset($this->scripts[$position])) {
            $scripts = array_merge($this->scripts[$position], $scripts);

            if (!$minify) {
                return $scripts;
            }

            return array(
                Minifier::minifyScripts($scripts),
            );
		} else {
			return array();
		}
	}
}
