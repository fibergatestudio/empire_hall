<?php



/**
 * @package        OpenCart
 * @author        Daniel Kerr
 * @copyright    Copyright (c) 2005 - 2017, OpenCart, Ltd. (https://www.opencart.com/)
 * @license        https://opensource.org/licenses/GPL-3.0
 * @link        https://www.opencart.com
 */

/**
 * Pagination class
 */
class PaginationFront
{
    public $total = 0;
    public $page = 1;
    public $limit = 20;
    public $num_links = 3;
    public $url = '';
    public $prev_txt = '';
    public $last_txt = '';
    public $text_first = '<svg width="50" height="12" viewBox="0 0 50 12" xmlns="http://www.w3.org/2000/svg"><path d="M6.04651 12L7.10465 10.95L2.87209 6.75L50 6.75L50 5.25L2.87209 5.25L7.10465 1.05L6.04651 -3.84254e-06L5.24537e-07 6L6.04651 12Z" /></svg>';
    public $text_last = '<svg width="50" height="12" viewBox="0 0 50 12" xmlns="http://www.w3.org/2000/svg"><path d="M43.9535 0L42.8953 1.05L47.1279 5.25H0V6.75H47.1279L42.8953 10.95L43.9535 12L50 6L43.9535 0Z" /></svg>';
    public $text_next = '&gt;';
    public $text_prev = '&lt;';
    /**
     *
     *
     * @return    text
     */
    public function render()
    {
        $total = $this->total;

        if ($this->page < 1) {
            $page = 1;
        } else {
            $page = $this->page;
        }

        if (!(int)$this->limit) {
            $limit = 10;
        } else {
            $limit = $this->limit;
        }

        $num_links = $this->num_links;
        $num_pages = ceil($total / $limit);

        $this->url = str_replace('%7Bpage%7D', '{page}', $this->url);

        $output = '<ul class="pagination">';

//		if ($page > 1) {
//			$output .= '<li><a href="' . str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $this->url) . '">' . $this->text_first . $this->prev_txt . '</a></li>';

        if ($page == 1 || $page == 2) {
            if ($page == 1){
                $output .= '<li class="first"><a onclick="return false;" href="' . str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $this->url) . '">' . $this->text_first . $this->prev_txt . '</a></li>';
            }
           if ($page == 2){
            $output .= '<li class="first"><a href="' . str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $this->url) . '">' . $this->text_first . $this->prev_txt . '</a></li>';
        }
        } else {
            $output .= '<li class="first"><a href="' . str_replace('{page}', $page - 1, $this->url) . '">' . $this->text_first . $this->prev_txt . '</a></li>';
        }
//		}

        // first
        if ($page == 1) {
            $output .= '<li><span><small>1</small></span></li>';
        } else {
            $output .= '<li><a href="' . str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $this->url) . '"><small>1</small></a></li>';
        }

        if ($num_pages > 4 && $page > 2) {
            $output .= '<li class="dots"></li>';
        }

        if ($num_pages > 1) {
            if ($num_pages <= $num_links + 1) {
                $start = 2;

                if ($page == $num_pages) {
                    $end = $num_pages;
                } else {
                    $end = $num_pages - 1;
                }
            } else {
                $start = $page - floor($num_links / 2);
                $end = $page + floor($num_links / 2);

                if ($start < 1) {
                    $end += abs($start) + 1;
                    $start = 1;
                }

                if ($end > $num_pages) {
                    $start -= ($end - $num_pages);
                    $end = $num_pages;
                }

                // start
                if ($page <= ($num_pages - $num_links)) {
                    $start += 1;
                    $end += 1;
                } elseif ($page == $num_pages - 1) {
                    $start -= 1;
                    $end -= 1;
                } elseif ($page == $num_pages) {
                    $start -= 1;
                }
            }

            for ($i = $start; $i <= $end; $i++) {
                if ($page == $i) {
                    $output .= '<li><span><small>' . $i . '</small></span></li>';
                } else {
                    if ($i === 1) {
                        $output .= '<li><a href="' . str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $this->url) . '"><small>' . $i . '</small></a></li>';
                    } else {
                        $output .= '<li><a href="' . str_replace('{page}', $i, $this->url) . '"><small>' . $i . '</small></a></li>';
                    }
                }
            }
        }

        if ($num_pages > 4 && $page < ($num_pages - $num_links)) {
            $output .= '<li class="dots"></li>';
        }

        if ($page < $num_pages) {
            $output .= '<li><a href="' . str_replace('{page}', $num_pages, $this->url) . '"><small>' . $num_pages . '</small></a></li>';
        }

        // last
        if ($page == $num_pages) {
          //  $output .= '<li class="last"><a href="' . str_replace('{page}', $page, $this->url) . '"><small>' . $this->last_txt . $this->text_last . '</small></a></li>';
        } else {
            $output .= '<li class="last"><a href="' . str_replace('{page}', $page + 1, $this->url) . '">' . $this->last_txt . $this->text_last . '</a></li>';
        }

        $output .= '</ul>';

        if ($num_pages > 1) {
            return $output;
        } else {
            return '';
        }
    }
}

