<?php
$pageTitle = "Data Pendapatan Ojol";
require_once '../includes/header.php';
require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Handle Delete
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $delete_stmt = $conn->prepare("DELETE FROM pendapatan_ojol WHERE id = ?");
    $delete_stmt->bind_param("i", $delete_id);
    
    if ($delete_stmt->execute()) {
        echo "<script>
            Swal.fire('Berhasil!', 'Data berhasil dihapus', 'success').then(() => {
                window.location.href = 'pendapatan-ojol.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire('Error!', 'Gagal menghapus data', 'error');
        </script>";
    }
    $delete_stmt->close();
}

// Filter
$filter_user = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$filter_bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : 0;
$filter_tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

// Get list users untuk filter
$users = $conn->query("SELECT id, name, username FROM user ORDER BY name");

// Build query dengan filter
$sql = "SELECT p.*, u.name as nama_user, u.username 
        FROM pendapatan_ojol p
        LEFT JOIN user u ON p.user_id = u.id
        WHERE 1=1";

$params = [];
$types = "";

if ($filter_user > 0) {
    $sql .= " AND p.user_id = ?";
    $params[] = $filter_user;
    $types .= "i";
}

if ($filter_bulan > 0) {
    $sql .= " AND MONTH(p.tanggal_kerja) = ?";
    $params[] = $filter_bulan;
    $types .= "i";
}

if ($filter_tahun > 0) {
    $sql .= " AND YEAR(p.tanggal_kerja) = ?";
    $params[] = $filter_tahun;
    $types .= "i";
}

$sql .= " ORDER BY p.tanggal_kerja DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Hitung statistik
$total_data = 0;
$total_bersih_semua = 0;
$total_pemasukan_semua = 0;
$total_pengeluaran_semua = 0;

$data_pendapatan = [];
while ($row = $result->fetch_assoc()) {
    $total_data++;
    $total_bersih_semua += $row['total_bersih'];
    $total_pemasukan_semua += $row['total_pemasukan'];
    $total_pengeluaran_semua += $row['total_pengeluaran'];
    $data_pendapatan[] = $row;
}
?>

<!-- Filter Section -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">User</label>
                <select name="user_id" class="form-select">
                    <option value="0">-- Semua User --</option>
                    <?php while ($user = $users->fetch_assoc()): ?>
                        <option value="<?= $user['id'] ?>" <?= $filter_user == $user['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['username']) ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Bulan</label>
                <select name="bulan" class="form-select">
                    <option value="0">-- Semua Bulan --</option>
                    <?php
                    $nama_bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                    for ($i = 1; $i <= 12; $i++):
                    ?>
                        <option value="<?= $i ?>" <?= $filter_bulan == $i ? 'selected' : '' ?>>
                            <?= $nama_bulan[$i-1] ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Tahun</label>
                <select name="tahun" class="form-select">
                    <?php for ($y = date('Y'); $y >= 2024; $y--): ?>
                        <option value="<?= $y ?>" <?= $filter_tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-2"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Statistik Cards -->
<div class="row mb-3">
    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body">
                <h6 class="card-title">Total Data</h6>
                <h3><?= $total_data ?></h3>
                <small>Hari kerja</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <div class="card-body">
                <h6 class="card-title">Total Pemasukan</h6>
                <h3>Rp <?= number_format($total_pemasukan_semua, 0, ',', '.') ?></h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
            <div class="card-body">
                <h6 class="card-title">Total Pengeluaran</h6>
                <h3>Rp <?= number_format($total_pengeluaran_semua, 0, ',', '.') ?></h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
            <div class="card-body">
                <h6 class="card-title">Total Bersih</h6>
                <h3>Rp <?= number_format($total_bersih_semua, 0, ',', '.') ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Tombol Print All -->
<div class="row mb-3">
    <div class="col-md-12">
        <button class="btn btn-primary" onclick="printTable()">
            <i class="fas fa-print me-2"></i>Print Data
        </button>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Data Pendapatan Driver Ojol</h5>
        <div>
            <button class="btn btn-success btn-sm me-2" onclick="exportData()">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </button>
            <button class="btn btn-primary btn-sm" onclick="printTable()">
                <i class="fas fa-print me-2"></i>Print
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="pendapatanTable" class="table table-striped table-hover table-sm">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>User</th>
                        <th>Go-Ride</th>
                        <th>Go-Food</th>
                        <th>Go-Send</th>
                        <th>Bonus</th>
                        <th>Tips</th>
                        <th>Pemasukan</th>
                        <th>Pengeluaran</th>
                        <th class="text-success fw-bold">Bersih</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data_pendapatan as $p): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($p['tanggal_kerja'])) ?></td>
                        <td><?= htmlspecialchars($p['nama_user']) ?></td>
                        <td>
                            <?= $p['goride_jumlah'] ?>x<br>
                            <small>Rp <?= number_format($p['goride_pendapatan'], 0, ',', '.') ?></small>
                        </td>
                        <td>
                            <?= $p['gofood_jumlah'] ?>x<br>
                            <small>Rp <?= number_format($p['gofood_pendapatan'], 0, ',', '.') ?></small>
                        </td>
                        <td>
                            <?= $p['gosend_jumlah'] ?>x<br>
                            <small>Rp <?= number_format($p['gosend_pendapatan'], 0, ',', '.') ?></small>
                        </td>
                        <td>Rp <?= number_format($p['bonus'], 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($p['tips'], 0, ',', '.') ?></td>
                        <td class="text-success">Rp <?= number_format($p['total_pemasukan'], 0, ',', '.') ?></td>
                        <td class="text-danger">Rp <?= number_format($p['total_pengeluaran'], 0, ',', '.') ?></td>
                        <td class="text-primary fw-bold">Rp <?= number_format($p['total_bersih'], 0, ',', '.') ?></td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-info" onclick="viewDetail(<?= $p['id'] ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-danger" onclick="confirmDelete(<?= $p['id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Area untuk print -->
<div id="printArea" style="display:none;">
    <div style="text-align:center; margin-bottom:20px;">
        <h2>Laporan Pendapatan Driver Ojol</h2>
        <p>Tanggal Cetak: <?= date('d F Y H:i:s') ?></p>
        <?php if ($filter_user > 0): ?>
            <p>Filter User: <?= htmlspecialchars($data_pendapatan[0]['nama_user'] ?? '') ?></p>
        <?php endif; ?>
        <?php if ($filter_bulan > 0): ?>
            <p>Bulan: <?= $nama_bulan[$filter_bulan-1] ?></p>
        <?php endif; ?>
        <?php if ($filter_tahun > 0): ?>
            <p>Tahun: <?= $filter_tahun ?></p>
        <?php endif; ?>
    </div>
    
    <table border="1" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background-color:#f2f2f2;">
                <th style="padding:8px;">No</th>
                <th style="padding:8px;">Tanggal</th>
                <th style="padding:8px;">User</th>
                <th style="padding:8px;">Go-Ride</th>
                <th style="padding:8px;">Go-Food</th>
                <th style="padding:8px;">Go-Send</th>
                <th style="padding:8px;">Bonus</th>
                <th style="padding:8px;">Tips</th>
                <th style="padding:8px;">Pemasukan</th>
                <th style="padding:8px;">Pengeluaran</th>
                <th style="padding:8px;">Bersih</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($data_pendapatan as $p): ?>
            <tr>
                <td style="padding:8px;"><?= $no++ ?></td>
                <td style="padding:8px;"><?= date('d/m/Y', strtotime($p['tanggal_kerja'])) ?></td>
                <td style="padding:8px;"><?= htmlspecialchars($p['nama_user']) ?></td>
                <td style="padding:8px;">
                    <?= $p['goride_jumlah'] ?>x<br>
                    Rp <?= number_format($p['goride_pendapatan'], 0, ',', '.') ?>
                </td>
                <td style="padding:8px;">
                    <?= $p['gofood_jumlah'] ?>x<br>
                    Rp <?= number_format($p['gofood_pendapatan'], 0, ',', '.') ?>
                </td>
                <td style="padding:8px;">
                    <?= $p['gosend_jumlah'] ?>x<br>
                    Rp <?= number_format($p['gosend_pendapatan'], 0, ',', '.') ?>
                </td>
                <td style="padding:8px;">Rp <?= number_format($p['bonus'], 0, ',', '.') ?></td>
                <td style="padding:8px;">Rp <?= number_format($p['tips'], 0, ',', '.') ?></td>
                <td style="padding:8px; color:green;">Rp <?= number_format($p['total_pemasukan'], 0, ',', '.') ?></td>
                <td style="padding:8px; color:red;">Rp <?= number_format($p['total_pengeluaran'], 0, ',', '.') ?></td>
                <td style="padding:8px; font-weight:bold; color:blue;">Rp <?= number_format($p['total_bersih'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <?php if (!empty($data_pendapatan)): ?>
        <tfoot>
            <tr style="background-color:#f2f2f2; font-weight:bold;">
                <td colspan="7" style="padding:8px; text-align:right;">TOTAL:</td>
                <td style="padding:8px; color:green;">Rp <?= number_format($total_pemasukan_semua, 0, ',', '.') ?></td>
                <td style="padding:8px; color:red;">Rp <?= number_format($total_pengeluaran_semua, 0, ',', '.') ?></td>
                <td style="padding:8px; color:blue;">Rp <?= number_format($total_bersih_semua, 0, ',', '.') ?></td>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    $('#pendapatanTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
        },
        order: [[0, 'desc']]
    });
});

function viewDetail(id) {
    Swal.fire({
        title: 'Detail Pendapatan',
        html: 'Loading...',
        allowOutsideClick: false
    });

    $.get('/android/api/get-pendapatan-detail.php', { id: id }, function(response) {
        if (response.success) {
            const data = response.data;

            Swal.fire({
                title: 'Detail Pendapatan',
                html: `
                    <table class="table table-sm">
                        <tr><th>Tanggal</th><td>${data.tanggal_formatted}</td></tr>
                        <tr><th>User</th><td>${data.nama_user}</td></tr>
                        <tr><th colspan="2" class="bg-light">Pendapatan</th></tr>
                        <tr><td>Go-Ride (${data.goride_jumlah}x)</td><td>Rp ${formatRupiah(data.goride_pendapatan)}</td></tr>
                        <tr><td>Go-Food (${data.gofood_jumlah}x)</td><td>Rp ${formatRupiah(data.gofood_pendapatan)}</td></tr>
                        <tr><td>Go-Send (${data.gosend_jumlah}x)</td><td>Rp ${formatRupiah(data.gosend_pendapatan)}</td></tr>
                        <tr><td>Bonus</td><td>Rp ${formatRupiah(data.bonus)}</td></tr>
                        <tr><td>Tips</td><td>Rp ${formatRupiah(data.tips)}</td></tr>
                        <tr><th colspan="2" class="bg-light">Pengeluaran</th></tr>
                        <tr><td>Bensin</td><td>Rp ${formatRupiah(data.bensin)}</td></tr>
                        <tr><td>Parkir</td><td>Rp ${formatRupiah(data.parkir)}</td></tr>
                        <tr><td>Makan</td><td>Rp ${formatRupiah(data.makan)}</td></tr>
                        <tr><th>Total Pemasukan</th><th class="text-success">Rp ${formatRupiah(data.total_pemasukan)}</th></tr>
                        <tr><th>Total Pengeluaran</th><th class="text-danger">Rp ${formatRupiah(data.total_pengeluaran)}</th></tr>
                        <tr><th>Total Bersih</th><th class="text-primary">Rp ${formatRupiah(data.total_bersih)}</th></tr>
                    </table>
                `,
                width: 600,
                showCancelButton: true,
                confirmButtonText: 'Print',
                cancelButtonText: 'Tutup',
                showDenyButton: true,
                denyButtonText: 'Hapus',
                preConfirm: () => {
                    printDetail(data);
                },
                preDeny: () => {
                    confirmDelete(id);
                    return false; // Mencegah alert tertutup
                }
            });
        } else {
            Swal.fire('Error', response.message, 'error');
        }
    }).fail(function(xhr) {
        Swal.fire('Error', 'API tidak bisa diakses', 'error');
        console.log(xhr.responseText);
    });
}

function printDetail(data) {
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Detail Pendapatan - ${data.nama_user}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1, h2 { text-align: center; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .total { font-weight: bold; }
                .success { color: green; }
                .danger { color: red; }
                .primary { color: blue; }
                .text-right { text-align: right; }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <h1>Detail Pendapatan Driver Ojol</h1>
            <p style="text-align:center;">Tanggal Cetak: ${new Date().toLocaleDateString('id-ID', { 
                day: 'numeric', 
                month: 'long', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            })}</p>
            
            <h2>Informasi Umum</h2>
            <table>
                <tr><th>Tanggal Kerja</th><td>${data.tanggal_formatted}</td></tr>
                <tr><th>Driver</th><td>${data.nama_user}</td></tr>
                <tr><th>Username</th><td>${data.username}</td></tr>
            </table>
            
            <h2>Rincian Pendapatan</h2>
            <table>
                <tr>
                    <th>Jenis Layanan</th>
                    <th>Jumlah Order</th>
                    <th class="text-right">Pendapatan</th>
                </tr>
                <tr>
                    <td>Go-Ride</td>
                    <td>${data.goride_jumlah} order</td>
                    <td class="text-right">Rp ${formatRupiah(data.goride_pendapatan)}</td>
                </tr>
                <tr>
                    <td>Go-Food</td>
                    <td>${data.gofood_jumlah} order</td>
                    <td class="text-right">Rp ${formatRupiah(data.gofood_pendapatan)}</td>
                </tr>
                <tr>
                    <td>Go-Send</td>
                    <td>${data.gosend_jumlah} order</td>
                    <td class="text-right">Rp ${formatRupiah(data.gosend_pendapatan)}</td>
                </tr>
                <tr>
                    <td>Bonus</td>
                    <td>-</td>
                    <td class="text-right">Rp ${formatRupiah(data.bonus)}</td>
                </tr>
                <tr>
                    <td>Tips</td>
                    <td>-</td>
                    <td class="text-right">Rp ${formatRupiah(data.tips)}</td>
                </tr>
                <tr class="total">
                    <td colspan="2">Total Pemasukan</td>
                    <td class="text-right success">Rp ${formatRupiah(data.total_pemasukan)}</td>
                </tr>
            </table>
            
            <h2>Rincian Pengeluaran</h2>
            <table>
                <tr>
                    <th>Jenis Pengeluaran</th>
                    <th class="text-right">Jumlah</th>
                </tr>
                <tr>
                    <td>Bensin</td>
                    <td class="text-right">Rp ${formatRupiah(data.bensin)}</td>
                </tr>
                <tr>
                    <td>Parkir</td>
                    <td class="text-right">Rp ${formatRupiah(data.parkir)}</td>
                </tr>
                <tr>
                    <td>Makan</td>
                    <td class="text-right">Rp ${formatRupiah(data.makan)}</td>
                </tr>
                <tr class="total">
                    <td>Total Pengeluaran</td>
                    <td class="text-right danger">Rp ${formatRupiah(data.total_pengeluaran)}</td>
                </tr>
            </table>
            
            <h2>Ringkasan</h2>
            <table>
                <tr>
                    <th>Total Pemasukan</th>
                    <td class="text-right success">Rp ${formatRupiah(data.total_pemasukan)}</td>
                </tr>
                <tr>
                    <th>Total Pengeluaran</th>
                    <td class="text-right danger">Rp ${formatRupiah(data.total_pengeluaran)}</td>
                </tr>
                <tr class="total">
                    <th>Total Bersih (Pendapatan Bersih)</th>
                    <td class="text-right primary">Rp ${formatRupiah(data.total_bersih)}</td>
                </tr>
            </table>
            
            <div style="margin-top: 30px; text-align: center;">
                <p>--- Laporan selesai ---</p>
            </div>
            
            <div class="no-print" style="margin-top: 20px; text-align: center;">
                <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer;">
                    Print Laporan
                </button>
                <button onclick="window.close()" style="padding: 10px 20px; background: #dc3545; color: white; border: none; cursor: pointer; margin-left: 10px;">
                    Tutup
                </button>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
}

function printTable() {
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Laporan Pendapatan Ojol</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1, h2, p { text-align: center; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .total-row { font-weight: bold; background-color: #f8f9fa; }
                .text-right { text-align: right; }
                .text-center { text-align: center; }
                .success { color: green; }
                .danger { color: red; }
                .primary { color: blue; }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            ${document.getElementById('printArea').innerHTML}
            <div class="no-print" style="margin-top: 20px; text-align: center;">
                <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer;">
                    Print Laporan
                </button>
                <button onclick="window.close()" style="padding: 10px 20px; background: #dc3545; color: white; border: none; cursor: pointer; margin-left: 10px;">
                    Tutup
                </button>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
}

function confirmDelete(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteData(id);
        }
    });
}

// Delete Data
function deleteData(id, userName, tanggal) {
    Swal.fire({
        title: 'Hapus Data?',
        html: `Apakah Anda yakin ingin menghapus data:<br><strong>${userName}</strong><br>Tanggal: ${tanggal}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'pendapatan-ojol.php?delete_id=' + id;
        }
    });
}


function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID').format(angka);
}

function exportData() {
    // Tampilkan konfirmasi sebelum export
    Swal.fire({
        title: 'Export ke Excel?',
        html: `
            <div class="text-start">
                <p>Apakah Anda ingin mengexport data pendapatan ini ke file Excel?</p>
                <div class="alert alert-info">
                    <strong>Detail Export:</strong><br>
                    • File: Laporan_Pendapatan_Ojol_<?= date('YmdHis') ?>.xls<br>
                    • Format: Microsoft Excel (.xls)<br>
                    • Lokasi: Folder Downloads browser
                </div>
                <p class="text-muted small">File akan otomatis didownload setelah Anda konfirmasi.</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Export Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        width: 500
    }).then((result) => {
        if (result.isConfirmed) {
            // Jika user mengkonfirmasi, lakukan export
            performExport();
        }
    });
}

// Fungsi untuk melakukan export yang sebenarnya
function performExport() {
    try {
        // Convert table to Excel
        let table = document.getElementById('pendapatanTable');
        let html = table.outerHTML;
        
        // Create download link
        let url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
        let downloadLink = document.createElement("a");
        document.body.appendChild(downloadLink);
        downloadLink.href = url;
        downloadLink.download = 'Laporan_Pendapatan_Ojol_<?= date('YmdHis') ?>.xls';
        downloadLink.click();
        document.body.removeChild(downloadLink);
        
        // Tampilkan notifikasi sukses
        Swal.fire({
            title: 'Berhasil!',
            html: `
                <div class="text-start">
                    <p>Data berhasil diexport ke Excel.</p>
                    <div class="alert alert-success">
                        <strong>File telah disimpan sebagai:</strong><br>
                        <code>Laporan_Pendapatan_Ojol_<?= date('YmdHis') ?>.xls</code>
                    </div>
                    <p class="text-muted">
                        File telah otomatis didownload ke folder <strong>Downloads</strong> browser Anda.
                    </p>
                </div>
            `,
            icon: 'success',
            confirmButtonText: 'OK',
            width: 500
        });
        
    } catch (error) {
        // Tampilkan error jika terjadi masalah
        Swal.fire({
            title: 'Error!',
            text: 'Terjadi kesalahan saat mengexport data: ' + error.message,
            icon: 'error',
            confirmButtonText: 'OK'
        });
        console.error('Export error:', error);
    }
}
</script>

<?php
$database->closeConnection();
require_once '../includes/footer.php';
?>