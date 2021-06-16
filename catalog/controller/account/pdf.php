<?php
class ControllerAccountPdf extends Controller {
    public function index() {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/download', '', true);

            $this->response->redirect($this->url->link('common/home', '', true));
        }

        $this->load->language('account/pdf');
        $this->load->model('account/customer');
        $this->load->model('tool/image');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_account'),
            'href' => $this->url->link('account/account', '', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('account/download', '', true)
        );

        $data['catalogs'] = array();

        $results = $this->model_account_customer->getPdfCatalogs();

        if ($results) {
            foreach ($results as $result) {
                if(is_file(DIR_DOWNLOAD . $result['mask'])) {

                    if (is_file(DIR_IMAGE . $result['image'])) {
                        $image = $this->model_tool_image->resize($result['image'], 452, 320);
                    } else {
                        $image = $this->model_tool_image->resize('placeholder.png', 452, 320);
                    }

                    $data['catalogs'][] = array(
                        'title'      => $result['name'],
                        'image'      => $image,
                        'href'       => DIR_FILES . $result['mask']
                    );
                }
            }
        }

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('account/pdf', $data));
    }
}