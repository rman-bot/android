<?php
require_once '../config/database.php';
require_once '../config/auth.php';

requireLogin();

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    
    // Tambah User
    if ($action === 'add') {
        $name = $database->cleanInput($_POST['name']);
        $username = $database->cleanInput($_POST['username']);
        $email = $database->cleanInput($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        // Cek username duplikat
        $check = $conn->prepare("SELECT id FROM user WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        
        if ($check->get_result()->num_rows > 0) {
            echo "<script>
                alert('Username sudah digunakan!');
                window.location.href = 'users.php';
            </script>";
            exit;
        }
        
        $stmt = $conn->prepare("INSERT INTO user (name, username, email, password, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $name, $username, $email, $password);
        
        if ($stmt->execute()) {
            echo "<script>
                alert('User berhasil ditambahkan!');
                window.location.href = 'users.php';
            </script>";
        } else {
            echo "<script>
                alert('Gagal menambahkan user!');
                window.location.href = 'users.php';
            </script>";
        }
    }
    
    // Edit User
    if ($action === 'edit') {
        $id = (int)$_POST['id'];
        $name = $database->cleanInput($_POST['name']);
        $username = $database->cleanInput($_POST['username']);
        $email = $database->cleanInput($_POST['email']);
        
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
             $stmt = $conn->prepare("UPDATE user SET name = ?, username = ?, email = ?, password = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("ssssi", $name, $username, $email, $password, $id);
        } else {
            $stmt = $conn->prepare("UPDATE user SET name = ?, username = ?, email = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("sssi", $name, $username, $email, $id);
        }
        
        if ($stmt->execute()) {
            echo "<script>
                alert('User berhasil diupdate!');
                window.location.href = 'users.php';
            </script>";
        } else {
            echo "<script>
                alert('Gagal mengupdate user!');
                window.location.href = 'users.php';
            </script>";
        }
    }
}

$database->closeConnection();
?>