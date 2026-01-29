<?php
/**
 * API: Insert Pendapatan Harian
 * Endpoint: /api/insert-pendapatan.php
 * Method: POST
 */

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

require_once '../config/database.php';
require_once '../utils/response.php';

$database = new Database();
$conn = $database->getConnection();
$response = new Response();

// Validasi method
$response->validateMethod('POST');

// Ambil data dari Android
$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
$tanggal_kerja = isset($_POST['tanggal_kerja']) ? $_POST['tanggal_kerja'] : '';

// Data Go-Ride
$goride_jumlah = isset($_POST['goride_jumlah']) ? (int)$_POST['goride_jumlah'] : 0;
$goride_pendapatan = isset($_POST['goride_pendapatan']) ? (float)$_POST['goride_pendapatan'] : 0;

// Data Go-Food
$gofood_jumlah = isset($_POST['gofood_jumlah']) ? (int)$_POST['gofood_jumlah'] : 0;
$gofood_pendapatan = isset($_POST['gofood_pendapatan']) ? (float)$_POST['gofood_pendapatan'] : 0;

// Data Go-Send
$gosend_jumlah = isset($_POST['gosend_jumlah']) ? (int)$_POST['gosend_jumlah'] : 0;
$gosend_pendapatan = isset($_POST['gosend_pendapatan']) ? (float)$_POST['gosend_pendapatan'] : 0;

// Tambahan
$bonus = isset($_POST['bonus']) ? (float)$_POST['bonus'] : 0;
$tips = isset($_POST['tips']) ? (float)$_POST['tips'] : 0;

// Pengeluaran
$bensin = isset($_POST['bensin']) ? (float)$_POST['bensin'] : 0;
$parkir = isset($_POST['parkir']) ? (float)$_POST['parkir'] : 0;
$makan = isset($_POST['makan']) ? (float)$_POST['makan'] : 0;

// Validasi data wajib
if ($user_id <= 0) {
    $response->error('User ID tidak valid!');
}

if (empty($tanggal_kerja)) {
    $response->error('Tanggal kerja harus diisi!');
}

// Validasi format tanggal
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_kerja)) {
    $response->error('Format tanggal tidak valid! Gunakan YYYY-MM-DD');
}

// Validasi user exists
$checkUser = $conn->prepare("SELECT id FROM user WHERE id = ?");
$checkUser->bind_param("i", $user_id);
$checkUser->execute();
if ($checkUser->get_result()->num_rows == 0) {
    $response->error('User tidak ditemukan!', 404);
}

// Hitung total otomatis
$total_pemasukan = $goride_pendapatan + $gofood_pendapatan + $gosend_pendapatan + $bonus + $tips;
$total_pengeluaran = $bensin + $parkir + $makan;
$total_bersih = $total_pemasukan - $total_pengeluaran;

// Cek apakah sudah ada data untuk tanggal ini
$checkDate = $conn->prepare("SELECT id FROM pendapatan_ojol WHERE user_id = ? AND tanggal_kerja = ?");
$checkDate->bind_param("is", $user_id, $tanggal_kerja);
$checkDate->execute();
$existingData = $checkDate->get_result();

if ($existingData->num_rows > 0) {
    // UPDATE jika sudah ada
    $sql = "UPDATE pendapatan_ojol SET 
            goride_jumlah = ?,
            goride_pendapatan = ?,
            gofood_jumlah = ?,
            gofood_pendapatan = ?,
            gosend_jumlah = ?,
            gosend_pendapatan = ?,
            bonus = ?,
            tips = ?,
            bensin = ?,
            parkir = ?,
            makan = ?,
            total_pemasukan = ?,
            total_pengeluaran = ?,
            total_bersih = ?,
            updated_at = NOW()
            WHERE user_id = ? AND tanggal_kerja = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ididididddddddis",
        $goride_jumlah, $goride_pendapatan,
        $gofood_jumlah, $gofood_pendapatan,
        $gosend_jumlah, $gosend_pendapatan,
        $bonus, $tips,
        $bensin, $parkir, $makan,
        $total_pemasukan, $total_pengeluaran, $total_bersih,
        $user_id, $tanggal_kerja
    );
    
    $actionMessage = "Data pendapatan berhasil diupdate!";
} else {
    // INSERT jika belum ada
    $sql = "INSERT INTO pendapatan_ojol (
            user_id, tanggal_kerja,
            goride_jumlah, goride_pendapatan,
            gofood_jumlah, gofood_pendapatan,
            gosend_jumlah, gosend_pendapatan,
            bonus, tips,
            bensin, parkir, makan,
            total_pemasukan, total_pengeluaran, total_bersih
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "isididididdddddd",
        $user_id, $tanggal_kerja,
        $goride_jumlah, $goride_pendapatan,
        $gofood_jumlah, $gofood_pendapatan,
        $gosend_jumlah, $gosend_pendapatan,
        $bonus, $tips,
        $bensin, $parkir, $makan,
        $total_pemasukan, $total_pengeluaran, $total_bersih
    );
    
    $actionMessage = "Data pendapatan berhasil disimpan!";
}

// Eksekusi query
if ($stmt->execute()) {
    $response->success(true, $actionMessage, [
        'id' => $existingData->num_rows > 0 ? $existingData->fetch_assoc()['id'] : $conn->insert_id,
        'tanggal_kerja' => $tanggal_kerja,
        'total_pemasukan' => $total_pemasukan,
        'total_pengeluaran' => $total_pengeluaran,
        'total_bersih' => $total_bersih
    ]);
} else {
    $response->error('Gagal menyimpan data: ' . $stmt->error);
}

$stmt->close();
$database->closeConnection();
?>