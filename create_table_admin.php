<?php
// setup/create_admin_table.php - Script untuk membuat tabel admin

// Koneksi database
$host = 'localhost';
$dbname = 'db_androidali';
$username = 'root';
$password = '';

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die(json_encode([
        'success' => false,
        'message' => 'Koneksi database gagal: ' . mysqli_connect_error()
    ]));
}

// Array untuk menyimpan hasil
$results = [];

// 1. Buat tabel admin
$sql_admin = "CREATE TABLE IF NOT EXISTS admin (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role ENUM('super_admin', 'admin', 'staff') DEFAULT 'admin',
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql_admin) === TRUE) {
    $results[] = "✓ Tabel admin berhasil dibuat";
} else {
    $results[] = "✗ Error tabel admin: " . $conn->error;
}

// 2. Cek apakah sudah ada data admin
$check_admin = "SELECT * FROM admin WHERE username = 'admin'";
$result = $conn->query($check_admin);

if ($result->num_rows == 0) {
    // Insert default super admin
    $default_username = 'admin';
    $default_password = password_hash('admin123', PASSWORD_DEFAULT);
    $default_name = 'Super Administrator';
    $default_email = 'admin@campus.com';
    
    $insert_admin = "INSERT INTO admin (username, password, name, email, role, status) 
                     VALUES ('$default_username', '$default_password', '$default_name', '$default_email', 'super_admin', 'active')";
    
    if ($conn->query($insert_admin) === TRUE) {
        $results[] = "✓ Super Admin berhasil dibuat";
        $results[] = "  Username: admin";
        $results[] = "  Password: admin123";
        $results[] = "  Role: super_admin";
    } else {
        $results[] = "✗ Error membuat super admin: " . $conn->error;
    }
    
    // Insert admin tambahan
    $admin2_username = 'staff';
    $admin2_password = password_hash('staff123', PASSWORD_DEFAULT);
    $admin2_name = 'Staff Campus';
    $admin2_email = 'staff@campus.com';
    
    $insert_admin2 = "INSERT INTO admin (username, password, name, email, role, status) 
                      VALUES ('$admin2_username', '$admin2_password', '$admin2_name', '$admin2_email', 'staff', 'active')";
    
    if ($conn->query($insert_admin2) === TRUE) {
        $results[] = "✓ Staff Admin berhasil dibuat";
        $results[] = "  Username: staff";
        $results[] = "  Password: staff123";
        $results[] = "  Role: staff";
    }
} else {
    $results[] = "ℹ Admin sudah ada di database";
}

// 3. Buat tabel login_log untuk tracking
$sql_log = "CREATE TABLE IF NOT EXISTS login_log (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    admin_id INT(11),
    username VARCHAR(50),
    ip_address VARCHAR(45),
    user_agent TEXT,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('success', 'failed') DEFAULT 'success',
    FOREIGN KEY (admin_id) REFERENCES admin(id) ON DELETE CASCADE,
    INDEX idx_admin_id (admin_id),
    INDEX idx_login_time (login_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql_log) === TRUE) {
    $results[] = "✓ Tabel login_log berhasil dibuat";
} else {
    $results[] = "✗ Error tabel login_log: " . $conn->error;
}

$conn->close();

// Output hasil
?>
<!DOCTYPE html>
<html>
<head>
    <title>Setup Admin Database</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        h2 {
            color: #667eea;
            margin-top: 30px;
            margin-bottom: 15px;
        }
        
        .result-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            margin: 15px 0;
        }
        
        .result-item {
            padding: 8px 0;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
        
        .success {
            color: #27ae60;
        }
        
        .error {
            color: #e74c3c;
        }
        
        .info {
            color: #3498db;
        }
        
        .credential-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .credential-box h3 {
            color: #856404;
            margin-top: 0;
        }
        
        .credential {
            background: white;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #ffc107;
        }
        
        .credential strong {
            color: #2c3e50;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin-top: 20px;
            transition: all 0.3s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        
        .warning {
            background: #fee;
            border: 2px solid #fcc;
            padding: 15px;
            border-radius: 8px;
            color: #c33;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Setup Admin Database</h1>
        <p>Setup database untuk sistem login admin panel PPDB</p>
        
        <div class="result-box">
            <h2>📋 Hasil Setup:</h2>
            <?php foreach ($results as $result): ?>
                <?php
                $class = 'info';
                if (strpos($result, '✓') !== false) $class = 'success';
                if (strpos($result, '✗') !== false) $class = 'error';
                ?>
                <div class="result-item <?php echo $class; ?>">
                    <?php echo $result; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="credential-box">
            <h3>🔑 Default Admin Credentials</h3>
            
            <div class="credential">
                <strong>Super Admin</strong><br>
                Username: <code>admin</code><br>
                Password: <code>admin123</code><br>
                Role: <code>super_admin</code>
            </div>
            
            <div class="credential">
                <strong>Staff Admin</strong><br>
                Username: <code>staff</code><br>
                Password: <code>staff123</code><br>
                Role: <code>staff</code>
            </div>
        </div>
        
        <div class="warning">
            <strong>⚠️ PENTING:</strong><br>
            Segera ganti password default setelah login pertama kali untuk keamanan sistem!
        </div>
        
        <h2>✅ Langkah Selanjutnya:</h2>
        <ol>
            <li>Login ke admin panel dengan credentials di atas</li>
            <li>Ganti password default admin</li>
            <li>Tambah admin baru sesuai kebutuhan</li>
            <li>Hapus file ini setelah selesai setup</li>
        </ol>
        
        <a href="../android/admin/auth/login.php" class="btn">🚀 Login ke Admin Panel</a>
    </div>
</body>
</html>