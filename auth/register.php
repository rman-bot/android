<?php
// Set header JSON agar Android tidak menerima HTML
header("Content-Type: application/json; charset=UTF-8");

// Koneksi database
require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

$response = [];

// VALIDASI METHOD
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['success'] = false;
    $response['message'] = 'Method tidak valid';
    echo json_encode($response);
    exit;
}

// AMBIL DATA DARI ANDROIDx
$name     = isset($_POST['name']) ? $database->cleanInput($_POST['name']) : '';
$email    = isset($_POST['email']) ? $database->cleanInput($_POST['email']) : '';
$username = isset($_POST['username']) ? $database->cleanInput($_POST['username']) : '';
$password = $_POST['password'] ?? '';

// VALIDASI DATA
if (empty($name) || empty($email) || empty($username) || empty($password)) {
    $response['success'] = false;
    $response['message'] = 'Semua data wajib diisi';
    echo json_encode($response);
    exit;
}

if (strlen($name) < 3) {
    $response['success'] = false;
    $response['message'] = 'Nama minimal 3 karakter';
    echo json_encode($response);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['success'] = false;
    $response['message'] = 'Format email tidak valid';
    echo json_encode($response);
    exit;
}

if (strlen($username) < 4) {
    $response['success'] = false;
    $response['message'] = 'Username minimal 4 karakter';
    echo json_encode($response);
    exit;
}

if (strlen($password) < 6) {
    $response['success'] = false;
    $response['message'] = 'Password minimal 6 karakter';
    echo json_encode($response);
    exit;
}

// CEK DUPLIKASI USERNAME
$check = $conn->prepare("SELECT id FROM user WHERE username = ?");
$check->bind_param("s", $username);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    $response['success'] = false;
    $response['message'] = 'Username sudah digunakan';
    echo json_encode($response);
    exit;
}

// HASH PASSWORD
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// INSERT USER BARU
$stmt = $conn->prepare(
    "INSERT INTO user (name, username, email, password)
     VALUES (?, ?, ?, ?)"
);

$stmt->bind_param(
    "ssss",
    $name,
    $username,
    $email,
    $hashed_password
);

if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = 'Registrasi berhasil';
} else {
    $response['success'] = false;
    $response['message'] = 'Registrasi gagal';
}

// KIRIM RESPONSE JSON
echo json_encode($response);

// TUTUP KONEKSI
$stmt->close();
$database->closeConnection();