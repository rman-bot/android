<?php
/**
 * API: Get Pendapatan User
 * Endpoint: /api/get-pendapatan.php
 * Method: GET
 * Parameters: user_id, bulan (optional), tahun (optional)
 */

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

require_once '../config/database.php';
require_once '../utils/response.php';

$database = new Database();
$conn = $database->getConnection();
$response = new Response();

// Ambil parameter
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : 0;
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : 0;

// Validasi user_id
if ($user_id <= 0) {
    $response->error('User ID tidak valid!');
}

// Build query
$sql = "SELECT p.*, u.name as nama_user, u.username 
        FROM pendapatan_ojol p
        LEFT JOIN user u ON p.user_id = u.id
        WHERE p.user_id = ?";

$params = [$user_id];
$types = "i";

// Filter bulan dan tahun jika ada
if ($bulan > 0 && $tahun > 0) {
    $sql .= " AND MONTH(p.tanggal_kerja) = ? AND YEAR(p.tanggal_kerja) = ?";
    $params[] = $bulan;
    $params[] = $tahun;
    $types .= "ii";
} elseif ($tahun > 0) {
    $sql .= " AND YEAR(p.tanggal_kerja) = ?";
    $params[] = $tahun;
    $types .= "i";
}

$sql .= " ORDER BY p.tanggal_kerja DESC";

// Prepare dan execute
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
$total_bersih_keseluruhan = 0;
$total_pemasukan_keseluruhan = 0;
$total_pengeluaran_keseluruhan = 0;

while ($row = $result->fetch_assoc()) {
    // Format tanggal untuk tampilan
    $row['tanggal_formatted'] = date('d/m/Y', strtotime($row['tanggal_kerja']));
    $row['hari'] = date('l', strtotime($row['tanggal_kerja']));
    
    // Format angka
    $row['goride_pendapatan'] = (float)$row['goride_pendapatan'];
    $row['gofood_pendapatan'] = (float)$row['gofood_pendapatan'];
    $row['gosend_pendapatan'] = (float)$row['gosend_pendapatan'];
    $row['bonus'] = (float)$row['bonus'];
    $row['tips'] = (float)$row['tips'];
    $row['bensin'] = (float)$row['bensin'];
    $row['parkir'] = (float)$row['parkir'];
    $row['makan'] = (float)$row['makan'];
    $row['total_pemasukan'] = (float)$row['total_pemasukan'];
    $row['total_pengeluaran'] = (float)$row['total_pengeluaran'];
    $row['total_bersih'] = (float)$row['total_bersih'];
    
    // Hitung total keseluruhan
    $total_bersih_keseluruhan += $row['total_bersih'];
    $total_pemasukan_keseluruhan += $row['total_pemasukan'];
    $total_pengeluaran_keseluruhan += $row['total_pengeluaran'];
    
    $data[] = $row;
}

// Response
if (count($data) > 0) {
    $response->success(true, 'Data ditemukan', [
        'pendapatan' => $data,
        'ringkasan' => [
            'total_hari_kerja' => count($data),
            'total_pemasukan' => $total_pemasukan_keseluruhan,
            'total_pengeluaran' => $total_pengeluaran_keseluruhan,
            'total_bersih' => $total_bersih_keseluruhan,
            'rata_rata_harian' => count($data) > 0 ? $total_bersih_keseluruhan / count($data) : 0
        ]
    ]);
} else {
    $response->success(true, 'Belum ada data pendapatan', [
        'pendapatan' => [],
        'ringkasan' => [
            'total_hari_kerja' => 0,
            'total_pemasukan' => 0,
            'total_pengeluaran' => 0,
            'total_bersih' => 0,
            'rata_rata_harian' => 0
        ]
    ]);
}

$stmt->close();
$database->closeConnection();
?>