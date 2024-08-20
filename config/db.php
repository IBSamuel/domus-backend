<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



class Database {
      // Database credentials
      private $host = "localhost";
      private $db_name = "domus";
      private $username = "root";
      private $password = "";
    // private $port = "3306";
    public $conn;

    // Get the database connection
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            if ($this->conn->connect_error) {
                throw new Exception("Connection error: " . $this->conn->connect_error);
            }
        } catch (Exception $exception) {
            // echo "Connection error. Please try again later.";
            echo $exception;
        }

        return $this->conn;
    }
}

// Usage example
$database = new Database();
$db = $database->getConnection();
?>
