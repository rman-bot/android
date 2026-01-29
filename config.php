<?php
// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = mysqli_connect("localhost","root","","db_androidali");
if(!$conn){
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");

// Start session jika belum
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/* ==== BASE URL PROJECT ==== */


// Function untuk log activity
function logActivity($conn, $admin_id, $action, $description, $details = null) {
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    
    $query = "INSERT INTO admin_logs (admin_id, action, description, details, ip_address, user_agent, referer) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, 'issssss', 
        $admin_id, $action, $description, $details, $ip_address, $user_agent, $referer);
    
    return mysqli_stmt_execute($stmt);
}
?>