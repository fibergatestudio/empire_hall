<?php
class ModelCatalogForms extends Model {
    public function addSubscriber($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "newsletter SET email = '" . $this->db->escape($data['subscribe_email']) . "', date_added = NOW()");
    }

    public function getSubscriber($email) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "newsletter WHERE email = '" . $email . "'");

        return $query->rows;
    }
}
