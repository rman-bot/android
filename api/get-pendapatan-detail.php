<?php
/**
 * API: Get Detail Pendapatan by ID
 * Endpoint: /api/get-pendapatan-detail.php
 * Method: GET
 * Parameters: id
 */

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

require_once '../config/database.php';
require_once '../utils/response.php';

$database = new Database();
$conn = $database->getConnection();
$response = new Response();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $response->error('ID tidak valid!');
    exit;
}

$sql = "SELECT p.*, u.name as nama_user, u.username, u.email 
        FROM pendapatan_ojol p
        LEFT JOIN user u ON p.user_id = u.id
        WHERE p.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    
    // Format data
    $data['tanggal_formatted'] = date('d F Y', strtotime($data['tanggal_kerja']));
    $data['hari'] = date('l', strtotime($data['tanggal_kerja']));
    
    // Convert ke float
    $numeric_fields = [
        'goride_pendapatan', 'gofood_pendapatan', 'gosend_pendapatan',
        'bonus', 'tips', 'bensin', 'parkir', 'makan',
        'total_pemasukan', 'total_pengeluaran', 'total_bersih'
    ];
    
    foreach ($numeric_fields as $field) {
        $data[$field] = (float)$data[$field];
    }
    
    $response->success(true, 'Data ditemukan', $data);
} else {
    $response->error('Data tidak ditemukan', 404);
}

$stmt->close();
$database->closeConnection();
?>