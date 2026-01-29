<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

require_once '../config/database.php';
require_once '../utils/response.php';

$database = new Database();
$conn = $database->getConnection();
$response = new Response();

// Validasi method
$response->validateMethod('POST');

// Ambil data
$email = isset($_POST['email']) ? $_POST['email'] : '';
$password_baru = isset($_POST['password_baru']) ? $_POST['password_baru'] : '';

// Validasi
if (empty($email)) {
    $response->error('Email harus diisi!');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response->error('Format email tidak valid!');
}

if (empty($password_baru)) {
    $response->error('Password baru harus diisi!');
}

if (strlen($password_baru) < 6) {
    $response->error('Password baru minimal 6 karakter!');
}

// Cek apakah email terdaftar
$sql = "SELECT id FROM user WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $response->error('Email tidak terdaftar!', 404);
}

$user = $result->fetch_assoc();
$user_id = $user['id'];

// Hash password baru
$password_baru_hash = password_hash($password_baru, PASSWORD_DEFAULT);

// Update password
$update = $conn->prepare("UPDATE user SET password = ? WHERE id = ? AND email = ?");
$update->bind_param("sis", $password_baru_hash, $user_id, $email);

if ($update->execute()) {
    $response->success(true, 'Password berhasil diubah!');
} else {
    $response->error('Gagal mengubah password!');
}

$stmt->close();
$update->close();
$database->closeConnection();
?>