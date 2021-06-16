<?php
namespace Wkcache;
/**
* Document class
*/
class Wkdocument {
	private $title;
	private $description;
	private $keywords;
	private $links = array();
	private $styles = array();
	private $scripts = array();
	public function __construct($registry) {
		if (!is_dir(DIR_APPLICATION. 'view/wkcache/assets/')) {

			mkdir(DIR_APPLICATION. 'view/wkcache/assets/', 0777, true);
		}

		$this->config = $registry->get('config');	
	}
	/**
     *
     *
     * @param	string	$title
     */
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
