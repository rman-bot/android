<?php
// config/auth.php - Authentication & Session Management
session_start();

// Fungsi untuk cek apakah admin sudah login
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
}

// Fungsi untuk redirect ke login jika belum login
function requireLogin() {
    if (!isAdminLoggedIn()) {
        header("Location: " . BASE_URL . "/admin/auth/login.php");
        exit();
    }
}

// Fungsi untuk redirect ke dashboard jika sudah login
function requireGuest() {
    if (isAdminLoggedIn()) {
        header("Location: " . BASE_URL . "/admin/pages/dashboard.php");
        exit();
    }
}

// Fungsi untuk logout
function logout() {
    session_unset();
    session_destroy();
    header("Location: " . BASE_URL . "/admin/auth/login.php");
    exit();
}

// Base URL - sesuaikan dengan path folder Anda
define('BASE_URL', '/android');

// Fungsi untuk get admin info
function getAdminInfo() {
    if (isAdminLoggedIn()) {
        return [
            'id' => $_SESSION['admin_id'],
            'username' => $_SESSION['admin_username'],
            'name' => $_SESSION['admin_name'],
            'email' => $_SESSION['admin_email'],
            'role' => $_SESSION['admin_role']
        ];
    }
    return null;
}
?>