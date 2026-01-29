<?php
// auth/login.php - API untuk login user

require_once '../config/database.php';
require_once '../utils/response.php';

$database = new Database();
$conn = $database->getConnection();
$response = new Response();

// Validasi method POST
$response->validateMethod('POST');

// Ambil data dari POST
$username = $database->cleanInput($_POST['username']);
$password = $_POST['password']; // Tidak di-clean karena akan di-hash

// Validasi data wajib
if (empty($username) || empty($password)) {
    $response->error('Username dan password harus diisi!');
}

// Query user berdasarkan username
$sql = "SELECT id, username, password, email FROM user WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Verifikasi password
    if (password_verify($password, $user['password'])) {
        // Login berhasil - jangan kirim password
        unset($user['password']);
        
        $response->success(true, 'Login berhasil!', [
            'user' => $user,
            'token' => bin2hex(random_bytes(32)) // Generate simple token
        ]);
    } else {
        $response->error('Password salah!', 401);
    }
} else {
    $response->error('Username tidak ditemukan!', 404);
}

$stmt->close();
$database->closeConnection();
?>