<?php
/**
 * API: Get Pendapatan Bulanan (Ringkasan)
 * Endpoint: /api/get-pendapatan-bulanan.php
 * Method: GET
 * Parameters: user_id, tahun (optional)
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
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

// Validasi
if ($user_id <= 0) {
    $response->error('User ID tidak valid!');
}

// Query ringkasan bulanan
$sql = "SELECT 
        YEAR(tanggal_kerja) AS tahun,
        MONTH(tanggal_kerja) AS bulan,
        COUNT(*) AS total_hari_kerja,
        SUM(goride_jumlah) AS total_goride,
        SUM(gofood_jumlah) AS total_gofood,
        SUM(gosend_jumlah) AS total_gosend,
        SUM(goride_jumlah + gofood_jumlah + gosend_jumlah) AS total_order,
        SUM(goride_pendapatan) AS total_goride_pendapatan,
        SUM(gofood_pendapatan) AS total_gofood_pendapatan,
        SUM(gosend_pendapatan) AS total_gosend_pendapatan,
        SUM(bonus) AS total_bonus,
        SUM(tips) AS total_tips,
        SUM(bensin) AS total_bensin,
        SUM(parkir) AS total_parkir,
        SUM(makan) AS total_makan,
        SUM(total_pemasukan) AS total_pemasukan_bulanan,
        SUM(total_pengeluaran) AS total_pengeluaran_bulanan,
        SUM(total_bersih) AS total_bersih_bulanan,
        AVG(total_bersih) AS rata_rata_harian
        FROM pendapatan_ojol
        WHERE user_id = ? AND YEAR(tanggal_kerja) = ?
        GROUP BY YEAR(tanggal_kerja), MONTH(tanggal_kerja)
        ORDER BY tahun DESC, bulan DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $tahun);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
$nama_bulan = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
    4 => 'April', 5 => 'Mei', 6 => 'Juni',
    7 => 'Juli', 8 => 'Agustus', 9 => 'September',
    10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

while ($row = $result->fetch_assoc()) {
    $row['nama_bulan'] = $nama_bulan[(int)$row['bulan']];
    
    // Format semua angka ke float
    $numeric_fields = [
        'total_goride_pendapatan', 'total_gofood_pendapatan', 
        'total_gosend_pendapatan', 'total_bonus', 'total_tips',
        'total_bensin', 'total_parkir', 'total_makan',
        'total_pemasukan_bulanan', 'total_pengeluaran_bulanan',
        'total_bersih_bulanan', 'rata_rata_harian'
    ];
    
    foreach ($numeric_fields as $field) {
        $row[$field] = (float)$row[$field];
    }
    
    $data[] = $row;
}

// Hitung total tahunan
$total_tahunan = 0;
$total_hari_kerja_tahun = 0;

foreach ($data as $bulan_data) {
    $total_tahunan += $bulan_data['total_bersih_bulanan'];
    $total_hari_kerja_tahun += $bulan_data['total_hari_kerja'];
}

if (count($data) > 0) {
    $response->success(true, 'Data ditemukan', [
        'data_bulanan' => $data,
        'ringkasan_tahunan' => [
            'tahun' => $tahun,
            'total_bulan_aktif' => count($data),
            'total_hari_kerja' => $total_hari_kerja_tahun,
            'total_bersih_tahun' => $total_tahunan,
            'rata_rata_bulanan' => count($data) > 0 ? $total_tahunan / count($data) : 0
        ]
    ]);
} else {
    $response->success(true, 'Belum ada data untuk tahun ini', [
        'data_bulanan' => [],
        'ringkasan_tahunan' => [
            'tahun' => $tahun,
            'total_bulan_aktif' => 0,
            'total_hari_kerja' => 0,
            'total_bersih_tahun' => 0,
            'rata_rata_bulanan' => 0
        ]
    ]);
}

$stmt->close();
$database->closeConnection();
?>