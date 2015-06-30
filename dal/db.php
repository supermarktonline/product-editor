<?php

class DB {
    public $db;
    
    const RET_VALUE = 0;
    const RET_OBJ = 1;
    const RET_ASSOC = 2;
    
    public function __construct() {
            try {
                
                $options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC);

                $this->db = new PDO('pgsql:dbname='.DB_NAME.';host='.DB_HOST.';port='.DB_PORT,DB_USER,DB_PASSWORD,$options);
                
                if(!$this->db) {
                    Tool::log("Database not available.",'emergency');
                }
                
            } catch(PDOException $e)  {
                Tool::log("Database not available: ".$e->getMessage(),'emergency');
            }
    }

    public function __destruct() {
            return $this->db = null;
    }
    
    public function getDB() {
        return $this->db;
    }

}