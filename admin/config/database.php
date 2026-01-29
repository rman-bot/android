<?php
// config/database.php - File konfigurasi database
class Database {
    private $host = 'localhost';
    private $db_name = 'db_androidali';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            $this->conn->set_charset("utf8mb4");
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
        } catch (Exception $e) {
            die("Database connection failed: " . $e->getMessage());
        }

        return $this->conn;
    }

    public function cleanInput($data) {
        if ($this->conn === null) {
            $this->getConnection();
        }
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $this->conn->real_escape_string($data);
    }

    public function closeConnection() {
        if ($this->conn !== null) {
            $this->conn->close();
        }
    }
}
?>