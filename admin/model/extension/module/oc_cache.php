<?php

class ModelExtensionModuleOccache extends Model {
    
    public function createTables() {
        $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX ."file_combine` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `type` varchar(4) NOT NULL,
            `file_name` varchar(100) NOT NULL,
            `file_path` varchar(500) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1") ;  
    }

    public function truncateTable() {
        $this->db->query("truncate `".DB_PREFIX."file_combine` ");
    }

}

?>