<?php
class ControllerCommonForms extends Controller {
    private $error = array();

    public function newsletter() {
        $json = array();

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateNewsletter($this->request->post)) {

            $json['success'] = true;
            $this->load->language('common/footer');
            $this->load->model('catalog/forms');

            $subject = $this->language->get('text_subscribe_title');

            $this->model_catalog_forms->addSubscriber($this->request->post);

            $message = 'Email: ' . $this->request->post['subscribe_email'] . "\n";

            $mail = new Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

            $mail->setTo($this->config->get('config_email'));
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
            $mail->setSubject($subject);
            $mail->setText($message);
            $mail->send();
        }

        if (isset($this->error['subscribe_email'])) {
            $json['error']['subscribe_email'] = $this->error['subscribe_email'];
        }
        $json['test']=$this->error;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function callback() {
        $json = array();

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateCallback($this->request->post)) {

            $json['success'] = true;
            $this->load->language('common/footer');

            $subject = $this->language->get('text_callback_btn');

            $message = $this->language->get('text_form_name') . ': ' . $this->request->post['callback_name'] . "\n";
            $message .= $this->language->get('text_form_phone') . ': ' . $this->request->post['callback_phone'] . "\n";

            $mail = new Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

            $mail->setTo($this->config->get('config_email'));
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
            $mail->setSubject($subject);
            $mail->setText($message);
            $mail->send();
        }

        if (isset($this->error['callback_name'])) {
            $json['error']['callback_name'] = $this->error['callback_name'];
        }

        if (isset($this->error['callback_phone'])) {
            $json['error']['callback_phone'] = $this->error['callback_phone'];
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function product() {
        $json = array();

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateProduct($this->request->post)) {

            $json['success'] = true;
            $this->load->language('common/footer');

            $subject = $this->language->get('text_product_title');

            $message = $this->language->get('text_form_name') . ': ' . $this->request->post['product_name'] . "\n";
            $message .= $this->language->get('text_form_phone') . ': ' . $this->request->post['product_phone'] . "\n";
            $message .= 'URL: ' . $this->request->post['product_href'] . "\n";

            $mail = new Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

            $mail->setTo($this->config->get('config_email'));
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
            $mail->setSubject($subject);
            $mail->setText($message);
            $mail->send();
        }

        if (isset($this->error['product_name'])) {
            $json['error']['product_name'] = $this->error['product_name'];
        }

        if (isset($this->error['product_phone'])) {
            $json['error']['product_phone'] = $this->error['product_phone'];
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function login() {
        $json = array();

        $this->load->model('account/customer');

        if ( $this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateLogin($this->request->post) ) {
            // Unset guest
            unset($this->session->data['guest']);

            // Default Shipping Address
            $this->load->model('account/address');

            if ($this->config->get('config_tax_customer') == 'payment') {
                $this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
            }

            if ($this->config->get('config_tax_customer') == 'shipping') {
                $this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
            }

            // Wishlist
            if (isset($this->session->data['wishlist']) && is_array($this->session->data['wishlist'])) {
                $this->load->model('account/wishlist');

                foreach ($this->session->data['wishlist'] as $key => $product_id) {
                    $this->model_account_wishlist->addWishlist($product_id);

                    unset($this->session->data['wishlist'][$key]);
                }
            }

            $json['success'] = true;
            $json['to_account'] = $this->url->link('account/account', '', true);
        }

        if ( isset($this->error['email']) ) {
            $json['error']['email'] = $this->error['email'];
            $json['error']['password'] = $this->error['email'];
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    protected function validateNewsletter() {
        $this->load->model('catalog/forms');

        $check_email = $this->model_catalog_forms->getSubscriber($this->request->post['subscribe_email']);

        if ($check_email) {
            $this->error['subscribe_email'] = $this->language->get('error_email_check_newsleter');
            //
        }

        if ((utf8_strlen($this->request->post['subscribe_email']) > 96) || !filter_var($this->request->post['subscribe_email'], FILTER_VALIDATE_EMAIL)) {
            $this->error['subscribe_email'] = $this->language->get('error_email_newsleter');
            //
        }

        return !$this->error;

    }

    protected function validateCallback() {
        $this->load->language('common/footer');

        if ((utf8_strlen(trim($this->request->post['callback_name'])) < 2) || (utf8_strlen(trim($this->request->post['callback_name'])) > 32)) {
            $this->error['callback_name'] = $this->language->get('text_error_name');
        }

        if ((strlen($this->request->post['callback_phone']) < 6)) {
            $this->error['callback_phone'] = $this->language->get('text_error_phone');
        }

        return !$this->error;

    }

    protected function validateProduct() {
        $this->load->language('common/footer');

        if ((utf8_strlen(trim($this->request->post['product_name'])) < 2) || (utf8_strlen(trim($this->request->post['product_name'])) > 32)) {
            $this->error['product_name'] = $this->language->get('text_error_name');
        }

        if ((strlen($this->request->post['product_phone']) < 6)) {
            $this->error['product_phone'] = $this->language->get('text_error_phone');
        }

        return !$this->error;

    }

    protected function validateLogin() {
        // Check how many login attempts have been made.
        $login_info = $this->model_account_customer->getLoginAttempts($this->request->post['email']);

        if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
            $this->error['email'] = $this->language->get('error_attempts');
        }

        // Check if customer has been approved.
        $customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

        if ($customer_info && !$customer_info['status']) {
            $this->error['email'] = $this->language->get('error_approved');
        }

        if (!$this->error) {
            if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {
                $this->error['email'] = $this->language->get('error_login');

                $this->model_account_customer->addLoginAttempt($this->request->post['email']);
            } else {
                $this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
            }
        }

        return !$this->error;
    }
}