<?php
$pageTitle = "Dashboard";
require_once '../includes/header.php';
require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Hitung statistik
$totalUsers = $conn->query("SELECT COUNT(*) as count FROM user")->fetch_assoc()['count'];
$totalAdmins = $conn->query("SELECT COUNT(*) as count FROM admin WHERE status = 'active'")->fetch_assoc()['count'];
$todayUsers = $conn->query("SELECT COUNT(*) as count FROM user WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['count'];

// User terbaru
$recentUsers = $conn->query("SELECT * FROM user ORDER BY id DESC LIMIT 5");
?>

<div class="row">
    <!-- Statistik Cards -->
    <div class="col-md-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0">Total User</p>
                    <h3><?= $totalUsers ?></h3>
                </div>
                <i class="fas fa-users fa-3x" style="opacity: 0.3;"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0">Total Admin</p>
                    <h3><?= $totalAdmins ?></h3>
                </div>
                <i class="fas fa-user-shield fa-3x" style="opacity: 0.3;"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0">User daftar Hari Ini</p>
                    <h3><?= $todayUsers ?></h3>
                </div>
                <i class="fas fa-user-plus fa-3x" style="opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</div>

<!-- User Terbaru -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">User Terbaru</h5>
        <a href="users.php" class="btn btn-sm btn-primary">Lihat Semua</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Tanggal Daftar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $recentUsers->fetch_assoc()): ?>
                    <tr>
                        <td><?= $user['Id'] ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= date('d M Y', strtotime($user['created_at'] ?? 'now')) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Info Admin -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informasi Admin</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="150"><strong>Nama</strong></td>
                        <td><?= htmlspecialchars($admin['name']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Username</strong></td>
                        <td><?= htmlspecialchars($admin['username']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email</strong></td>
                        <td><?= htmlspecialchars($admin['email']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Role</strong></td>
                        <td><span class="badge bg-primary"><?= ucfirst($admin['role']) ?></span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="users.php" class="btn btn-outline-primary">
                        <i class="fas fa-users me-2"></i>Kelola User
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="pendapatan-ojol.php" class="btn btn-outline-primary">
                        <i class="fas fa-motorcycle"></i>Pendapatan Ojol
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$database->closeConnection();
require_once '../includes/footer.php';
?>