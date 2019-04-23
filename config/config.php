<?php

/*
* Mysql database class - only one connection allowed
*/
class config {
    private $_connection;
    private static $_instance; //The single instance
    private $_host = "127.0.0.1";
    private $_username = "root"; //edit
    private $_password = "aporose";//edit
    private $_database = "gli-test";
    /*
    Get an instance of the Database
    @return Instance
    */
    public static function getInstance() {
        if(!self::$_instance) { // If no instance then make one
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    // Constructor
    private function __construct() {
        $this->_connection = new mysqli($this->_host, $this->_username, 
            $this->_password, $this->_database);
    
        // Error handling
        if(mysqli_connect_error()) {
            trigger_error("Failed to conencto to MySQL: " . mysql_connect_error(),
                 E_USER_ERROR);
        }
    }
    // Magic method clone is empty to prevent duplication of connection
    private function __clone() { }
    // Get mysqli connection
    public function getConnection() {
        return $this->_connection;
    }

    public function createLottoDrawTable(){

       $sql = "CREATE TABLE IF NOT EXISTS `lotto_draws` (
              `id` int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `main_balls` varchar(255) DEFAULT NULL,
              `power_balls` varchar(50) DEFAULT NULL,
              `draw_time` varchar(100) DEFAULT NULL
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
            ";
       if ($this->getConnection()->query($sql) === TRUE) {
            //echo "Table lotto_draws created successfully";
        } else {
            //echo "Error creating table: " . $this->_connection->error;
        }     

    }


}
?>