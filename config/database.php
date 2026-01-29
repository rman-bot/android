<?php
// config/database.php - File konfigurasi database
class Database {
    private $host = 'localhost';
    private $db_name = 'db_androidali';
    private $username = 'root';
    private $password = ''; // Kosongkan jika tidak ada password
    private $conn;

    // Koneksi ke database
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            $this->conn->set_charset("utf8mb4");
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Koneksi database gagal: ' . $e->getMessage()
            ]);
            exit();
        }

        return $this->conn;
    }

    // Fungsi untuk membersihkan input
    public function cleanInput($data) {
        if ($this->conn === null) {
            $this->getConnection();
        }
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $this->conn->real_escape_string($data);
    }

    // Tutup koneksi
    public function closeConnection() {
        if ($this->conn !== null) {
            $this->conn->close();
        }
    }
}

// Set header untuk JSON response
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
?>