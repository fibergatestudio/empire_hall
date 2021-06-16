<?php


class ControllerToolCacheEvent extends Controller {

    public function deleteCache() {
        if($this->customer->isLogged()) {
            $page = $this->load->library('wkcache/page');
            $this->page->clearCache();
        }
    }

}


?>