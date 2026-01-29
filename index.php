<?php
include 'config.php';
/* ===============================
   SET CHARSET (WAJIB UNTUK EMOJI)
================================ */
mysqli_set_charset($conn, "utf8mb4");

/* ===============================
   VISITOR TRACKING (FIXED)
================================ */
$ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$date = date('Y-m-d');

// Debug (akan muncul di HTML comment)
echo "<!-- DEBUG: IP = $ip, Date = $date -->";

// Gunakan INSERT IGNORE yang lebih aman
$sql = "INSERT IGNORE INTO visitors (ip_address, visit_date) 
        VALUES (?, ?)";

$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ss", $ip, $date);
    $result = mysqli_stmt_execute($stmt);
    
    if ($result) {
        $affected = mysqli_stmt_affected_rows($stmt);
        if ($affected > 0) {
            echo "<!-- DEBUG: New visitor added -->";
        } else {
            echo "<!-- DEBUG: Already visited today -->";
        }
    } else {
        echo "<!-- DEBUG: Error: " . mysqli_error($conn) . " -->";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "<!-- DEBUG: Prepare failed -->";
}

/* ===============================
   AMBIL DATA LANDING (AKTIF)
================================ */
$data = mysqli_fetch_assoc(
  mysqli_query($conn,"
    SELECT * FROM landing
    WHERE is_active = 1
    ORDER BY id DESC
    LIMIT 1
  ")
);

if (!$data) {
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Maintenance</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-50 to-pink-50">
  <div class="bg-white/80 backdrop-blur-lg p-10 rounded-3xl shadow-2xl text-center max-w-md border border-white/20">
    <div class="text-6xl mb-6">🎮</div>
    <h1 class="text-3xl font-black mb-4 bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
      Landing Page Sedang Diperbarui
    </h1>
    <p class="text-gray-600 mb-8">
      Halaman sedang dalam perawatan.<br>
      Kami akan kembali segera dengan fitur yang lebih seru!
    </p>
    <a href="/" class="px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-full hover:shadow-lg hover:shadow-purple-300 transition-all font-bold">
      Refresh Halaman
    </a>
  </div>
</body>
</html>
<?php
exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($data['title']); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="<?= htmlspecialchars($data['description']); ?>">

<!-- Open Graph Meta Tags -->
<meta property="og:title" content="<?= htmlspecialchars($data['title']); ?>">
<meta property="og:description" content="<?= htmlspecialchars($data['description']); ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="https://<?= $_SERVER['HTTP_HOST']; ?>">

<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="assets/logos/<?= htmlspecialchars($data['logo']); ?>">

<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<script>
tailwind.config = {
  theme: {
    fontFamily: {
      'poppins': ['Poppins', 'sans-serif'],
      'inter': ['Inter', 'sans-serif'],
    },
    extend: {
      colors: { 
        primary: '#1216e4', 
        secondary: '#ecaa48',
        accent: '#d60606',
        dark: '#1F2937',
      },
      animation: {
        'float': 'float 6s ease-in-out infinite',
        'pulse-glow': 'pulse-glow 2s ease-in-out infinite',
        'spin-slow': 'spin 3s linear infinite',
        'bounce-slow': 'bounce 2s infinite',
      },
      keyframes: {
        float: {
          '0%, 100%': { transform: 'translateY(0px)' },
          '50%': { transform: 'translateY(-20px)' },
        },
        'pulse-glow': {
          '0%, 100%': { opacity: 1 },
          '50%': { opacity: 0.7, boxShadow: '0 0 20px rgba(139, 92, 246, 0.5)' },
        }
      }
    }
  }
}
</script>

<style>
  :root {
    --color-primary: #2544f1;
    --color-secondary: #ec4887;
    --color-accent: #85911b;
  }
  
  html {
    scroll-behavior: smooth;
  }
  
  body {
    font-family: 'Inter', sans-serif;
  }
  
  h1, h2, h3, h4, h5, h6 {
    font-family: 'Poppins', sans-serif;
  }
  
  .gradient-text {
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }
  
  .gradient-bg {
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
  }
  
  .gradient-border {
    border: double 3px transparent;
    border-radius: 20px;
    background-image: linear-gradient(white, white), 
                      linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
    background-origin: border-box;
    background-clip: padding-box, border-box;
  }
  
  .glass-effect {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.2);
  }
  
  .feature-card {
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    overflow: hidden;
  }
  
  .feature-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 25px 50px -12px rgba(139, 92, 246, 0.25);
  }
  
  .feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.7s;
  }
  
  .feature-card:hover::before {
    left: 100%;
  }
  
  .pulse-button {
    animation: pulse-glow 2s infinite;
  }
  
  .floating-element {
    animation: float 6s ease-in-out infinite;
  }
  
  .text-shadow {
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
  }
  
  /* Scrollbar styling */
  ::-webkit-scrollbar {
    width: 8px;
  }
  
  ::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
  }
  
  ::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
    border-radius: 10px;
  }
  
  ::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #7C3AED 0%, #DB2777 100%);
  }
  
  /* Particle effect */
  .particle {
    position: absolute;
    border-radius: 50%;
    background: rgba(139, 92, 246, 0.1);
    pointer-events: none;
  }
</style>
</head>

<body class="font-inter bg-gradient-to-b from-gray-50 to-white text-gray-800 overflow-x-hidden">

<!-- ================= NAVBAR ================= -->
<nav id="navbar" class="fixed top-0 left-0 right-0 glass-effect z-50 transition-all duration-500 py-4">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
    <div class="flex items-center gap-3">
      <div class="relative">
        <img src="assets/logos/<?= htmlspecialchars($data['logo']); ?>"
             class="w-12 h-12 object-contain rounded-2xl shadow-lg gradient-border">
        <div class="absolute -top-1 -right-1 w-3 h-3 bg-gradient-to-r from-green-400 to-green-500 rounded-full animate-pulse"></div>
      </div>
      <div>
        <strong class="text-xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
          <?= htmlspecialchars($data['title']); ?>
        </strong>
        <p class="text-xs text-gray-500">Fun & Utility App</p>
      </div>
    </div>
    
    <div class="flex items-center gap-4">
      <a href="#features" class="hidden md:inline-block px-4 py-2 text-gray-700 hover:text-primary font-medium transition-colors">
        Fitur
      </a>
      <a href="#testimonials" class="hidden md:inline-block px-4 py-2 text-gray-700 hover:text-primary font-medium transition-colors">
        Testimoni
      </a>
      <a href="#faq" class="hidden md:inline-block px-4 py-2 text-gray-700 hover:text-primary font-medium transition-colors">
        FAQ
      </a>
      
      <a href="<?= htmlspecialchars($data['playstore_link']); ?>"
         target="_blank"
         class="px-6 py-3 bg-gradient-to-r from-primary to-secondary text-white font-bold rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 pulse-button">
         <i class="fas fa-download mr-2"></i><?= htmlspecialchars($data['cta_text']); ?>
      </a>
    </div>
  </div>
</nav>

<!-- ================= HERO ================= -->
<section class="pt-36 pb-24 relative overflow-hidden">
  <!-- Animated Background -->
  <div class="absolute inset-0 bg-gradient-to-br from-purple-50 via-pink-50 to-blue-50"></div>
  <div class="absolute top-10 left-10 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float"></div>
  <div class="absolute bottom-10 right-10 w-72 h-72 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float" style="animation-delay: 2s"></div>
  <div class="absolute top-1/2 left-1/3 w-64 h-64 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float" style="animation-delay: 4s"></div>
  
  <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid lg:grid-cols-2 gap-12 items-center">
      <div class="text-left">
        <?php if($data['hero_badge']): ?>
        <div class="inline-flex items-center gap-2 mb-8 px-5 py-2 glass-effect rounded-full text-sm font-semibold w-fit">
          <span class="w-2 h-2 bg-gradient-to-r from-green-400 to-green-500 rounded-full animate-pulse"></span>
          <?= htmlspecialchars($data['hero_badge']); ?>
        </div>
        <?php endif; ?>

        <h1 class="text-5xl md:text-7xl font-black mb-6 leading-tight">
          <span class="gradient-text text-shadow"><?= htmlspecialchars($data['title']); ?></span>
        </h1>

        <p class="text-xl text-gray-600 mb-10 leading-relaxed max-w-2xl">
          <?= nl2br(htmlspecialchars($data['description'])); ?>
        </p>

        <div class="mb-12">
          <a href="<?= htmlspecialchars($data['playstore_link']); ?>"
             target="_blank"
             class="inline-flex items-center gap-4 px-10 py-5 bg-gradient-to-r from-primary to-secondary text-white font-bold rounded-full shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 pulse-button group max-w-md mx-auto md:mx-0">
            <i class="fab fa-google-play text-3xl"></i>
            <div class="text-left">
              <div class="text-lg font-bold">Download Sekarang</div>
              <div class="text-sm font-normal opacity-90">Tersedia di Google Play Store</div>
            </div>
            <i class="fas fa-arrow-right text-xl group-hover:translate-x-2 transition-transform ml-4"></i>
          </a>
        </div>

        <div class="grid grid-cols-3 gap-6">
          <div class="text-center p-4 bg-white/50 backdrop-blur-sm rounded-2xl shadow-sm border border-white/20">
            <div class="text-3xl font-black gradient-text mb-2"><?= htmlspecialchars($data['stat_user']); ?></div>
            <div class="text-sm font-medium text-gray-600">Pengguna Aktif</div>
          </div>
          <div class="text-center p-4 bg-white/50 backdrop-blur-sm rounded-2xl shadow-sm border border-white/20">
            <div class="text-3xl font-black gradient-text mb-2">⭐ <?= htmlspecialchars($data['stat_rating']); ?></div>
            <div class="text-sm font-medium text-gray-600">Rating</div>
          </div>
          <div class="text-center p-4 bg-white/50 backdrop-blur-sm rounded-2xl shadow-sm border border-white/20">
            <div class="text-3xl font-black gradient-text mb-2"><?= htmlspecialchars($data['stat_support']); ?></div>
            <div class="text-sm font-medium text-gray-600">Dukungan</div>
          </div>
        </div>
      </div>

      <div class="relative flex justify-center">
        <div class="relative floating-element">
          <div class="absolute -inset-4 bg-gradient-to-r from-primary to-secondary rounded-3xl blur-xl opacity-30"></div>
          <img src="assets/logos/<?= htmlspecialchars($data['logo']); ?>"
               class="relative w-96 h-96 object-contain drop-shadow-2xl rounded-2xl">
        </div>
        
        <!-- Floating elements around logo -->
        <div class="absolute top-10 right-0 w-16 h-16 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl flex items-center justify-center shadow-xl animate-bounce-slow">
          <i class="fas fa-gamepad text-white text-xl"></i>
        </div>
        <div class="absolute bottom-20 left-0 w-16 h-16 bg-gradient-to-br from-green-400 to-teal-500 rounded-2xl flex items-center justify-center shadow-xl animate-bounce-slow" style="animation-delay: 0.5s">
          <i class="fas fa-calculator text-white text-xl"></i>
        </div>
        <div class="absolute top-1/2 -right-4 w-12 h-12 bg-gradient-to-br from-pink-400 to-rose-500 rounded-xl flex items-center justify-center shadow-xl animate-spin-slow">
          <i class="fas fa-star text-white"></i>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ================= FEATURES ================= -->
<section id="features" class="py-24 bg-gradient-to-b from-white to-gray-50 relative">
  <div class="absolute top-0 left-0 w-full h-20 bg-gradient-to-b from-transparent to-white"></div>
  
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center mb-20">
      <span class="inline-block px-4 py-2 bg-gradient-to-r from-primary/10 to-secondary/10 text-primary rounded-full font-bold mb-4">
        ✨ FITUR UNGGULAN
      </span>
      <h2 class="text-4xl md:text-5xl font-black mb-6 gradient-text">Hiburan & Utilitas dalam Satu Aplikasi</h2>
      <p class="text-xl text-gray-600 max-w-3xl mx-auto">Gabungkan keseruan Truth or Dare dengan kemudahan menghitung gaji karyawan</p>
    </div>

    <div class="grid md:grid-cols-3 gap-8">
      <!-- Feature 1: Truth or Dare -->
      <div class="feature-card bg-white p-8 rounded-3xl shadow-lg border border-gray-100">
        <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mb-6 shadow-lg mx-auto">
          <i class="fas fa-theater-masks text-white text-3xl"></i>
        </div>
        <h3 class="text-2xl font-bold mb-4 text-center text-gray-800"><?= htmlspecialchars($data['feature_1']); ?></h3>
        <p class="text-gray-600 text-center leading-relaxed">Berbagai kategori tantangan seru untuk mengisi waktu bersama teman dan keluarga</p>
        <div class="mt-6 flex justify-center">
          <span class="px-4 py-1 bg-purple-100 text-purple-600 rounded-full text-sm font-medium">Hiburan</span>
        </div>
      </div>

      <!-- Feature 2: Kalkulator Gaji -->
      <div class="feature-card bg-white p-8 rounded-3xl shadow-lg border border-gray-100 relative">
        <div class="absolute -top-3 right-6 bg-gradient-to-r from-green-500 to-teal-500 text-white px-4 py-1 rounded-full text-sm font-bold">
          NEW
        </div>
        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center mb-6 shadow-lg mx-auto">
          <i class="fas fa-calculator text-white text-3xl"></i>
        </div>
        <h3 class="text-2xl font-bold mb-4 text-center text-gray-800"><?= htmlspecialchars($data['feature_2']); ?></h3>
        <p class="text-gray-600 text-center leading-relaxed">Hitung gaji karyawan dengan mudah sesuai dengan peraturan yang berlaku</p>
        <div class="mt-6 flex justify-center">
          <span class="px-4 py-1 bg-blue-100 text-blue-600 rounded-full text-sm font-medium">Utilitas</span>
        </div>
      </div>

      <!-- Feature 3: Koleksi Tantangan -->
      <div class="feature-card bg-white p-8 rounded-3xl shadow-lg border border-gray-100">
        <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-yellow-500 rounded-2xl flex items-center justify-center mb-6 shadow-lg mx-auto">
          <i class="fas fa-trophy text-white text-3xl"></i>
        </div>
        <h3 class="text-2xl font-bold mb-4 text-center text-gray-800"><?= htmlspecialchars($data['feature_3']); ?></h3>
        <p class="text-gray-600 text-center leading-relaxed">Ratusan tantangan unik yang diperbarui secara berkala untuk pengalaman berbeda setiap kali</p>
        <div class="mt-6 flex justify-center">
          <span class="px-4 py-1 bg-orange-100 text-orange-600 rounded-full text-sm font-medium">Update</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ================= CARA KERJA ================= -->
<section class="py-24 bg-gradient-to-br from-primary/5 to-secondary/5 relative overflow-hidden">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center mb-20">
      <span class="inline-block px-4 py-2 bg-gradient-to-r from-primary/10 to-secondary/10 text-primary rounded-full font-bold mb-4">
        🚀 CARA KERJA
      </span>
      <h2 class="text-4xl md:text-5xl font-black mb-6 gradient-text">Mulai dalam 3 Langkah Mudah</h2>
      <p class="text-xl text-gray-600 max-w-3xl mx-auto">Dapatkan pengalaman terbaik dengan panduan sederhana ini</p>
    </div>

    <div class="grid md:grid-cols-3 gap-8 relative">
      <!-- Progress Line -->
      <div class="hidden md:block absolute top-12 left-1/4 right-1/4 h-2 bg-gradient-to-r from-primary/20 via-secondary/20 to-accent/20 rounded-full"></div>
      <div class="hidden md:block absolute top-12 left-1/4 w-1/4 h-2 bg-gradient-to-r from-primary to-secondary rounded-full"></div>

      <?php 
      $steps = [
        ['num' => '1', 'color' => 'from-purple-500 to-pink-500', 'icon' => 'fa-download', 'title' => 'Download App', 'desc' => $data['step_1']],
        ['num' => '2', 'color' => 'from-blue-500 to-cyan-500', 'icon' => 'fa-user-plus', 'title' => 'Setup Akun', 'desc' => $data['step_2']],
        ['num' => '3', 'color' => 'from-green-500 to-teal-500', 'icon' => 'fa-play-circle', 'title' => 'Mulai Gunakan', 'desc' => $data['step_3']]
      ];
      
      foreach ($steps as $step): 
      ?>
      <div class="relative">
        <div class="w-24 h-24 bg-gradient-to-br <?= $step['color'] ?> rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl relative z-10 transform hover:scale-110 transition-transform duration-300">
          <span class="text-3xl font-black text-white"><?= $step['num'] ?></span>
          <div class="absolute -top-2 -right-2 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg">
            <i class="fas <?= $step['icon'] ?> text-primary text-lg"></i>
          </div>
        </div>
        <div class="bg-white p-8 rounded-3xl shadow-lg border border-gray-100 text-center">
          <h3 class="text-2xl font-bold mb-4"><?= $step['title'] ?></h3>
          <p class="text-gray-600 text-lg"><?= htmlspecialchars($step['desc']) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ================= TESTIMONI ================= -->
<section id="testimonials" class="py-24 bg-white relative">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-20">
      <span class="inline-block px-4 py-2 bg-gradient-to-r from-primary/10 to-secondary/10 text-primary rounded-full font-bold mb-4">
        💬 TESTIMONI
      </span>
      <h2 class="text-4xl md:text-5xl font-black mb-6 gradient-text">Kata Pengguna</h2>
      <p class="text-xl text-gray-600 max-w-3xl mx-auto">Lihat apa kata mereka yang sudah menggunakan aplikasi kami</p>
    </div>

    <div class="grid md:grid-cols-3 gap-8">
      <?php 
      $testimonials = [
        ['name' => $data['testi_1_name'], 'text' => $data['testi_1_text'], 'color' => 'from-purple-500 to-pink-500', 'role' => 'Pengguna Aktif'],
        ['name' => $data['testi_2_name'], 'text' => $data['testi_2_text'], 'color' => 'from-blue-500 to-cyan-500', 'role' => 'Pengguna Aktif'],
        ['name' => $data['testi_3_name'], 'text' => $data['testi_3_text'], 'color' => 'from-green-500 to-teal-500', 'role' => 'Pengguna Aktif']
      ];
      
      foreach ($testimonials as $testi): 
      ?>
      <div class="bg-gradient-to-br from-white to-gray-50 p-8 rounded-3xl shadow-xl border border-gray-100 transition-all duration-300 hover:shadow-2xl hover:border-primary/20">
        <div class="flex gap-1 mb-6">
          <?php for($i=0; $i<5; $i++): ?>
          <i class="fas fa-star text-yellow-400"></i>
          <?php endfor; ?>
        </div>
        <p class="text-gray-700 mb-8 text-lg leading-relaxed italic relative">
          <i class="fas fa-quote-left text-primary/20 text-3xl absolute -top-2 -left-2"></i>
          "<?= htmlspecialchars($testi['text']); ?>"
        </p>
        <div class="flex items-center gap-4">
          <div class="w-14 h-14 bg-gradient-to-br <?= $testi['color'] ?> rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg">
            <?= strtoupper(substr($testi['name'], 0, 1)); ?>
          </div>
          <div>
            <strong class="block text-lg font-bold"><?= htmlspecialchars($testi['name']); ?></strong>
            <span class="text-sm text-gray-500"><?= $testi['role']; ?></span>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ================= FAQ ================= -->
<section id="faq" class="py-24 bg-gradient-to-b from-gray-50 to-white relative">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-16">
      <span class="inline-block px-4 py-2 bg-gradient-to-r from-primary/10 to-secondary/10 text-primary rounded-full font-bold mb-4">
        ❓ FAQ
      </span>
      <h2 class="text-4xl md:text-5xl font-black mb-6 gradient-text">Pertanyaan Umum</h2>
      <p class="text-xl text-gray-600 max-w-3xl mx-auto">Temukan jawaban atas pertanyaan yang sering diajukan</p>
    </div>

    <div class="space-y-6">
      <?php for($i=1;$i<=4;$i++): ?>
        <?php if($data["faq_{$i}_q"]): ?>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-xl">
          <button class="faq-question w-full text-left p-8 flex items-center justify-between group">
            <div class="flex items-center gap-4">
              <div class="w-10 h-10 bg-gradient-to-r from-primary/10 to-secondary/10 rounded-xl flex items-center justify-center">
                <i class="fas fa-question text-primary"></i>
              </div>
              <h3 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($data["faq_{$i}_q"]); ?></h3>
            </div>
            <i class="fas fa-chevron-down text-gray-400 group-hover:text-primary transition-transform duration-300"></i>
          </button>
          <div class="faq-answer px-8 pb-8 hidden">
            <div class="pl-14 border-l-2 border-primary/20">
              <p class="text-gray-600 leading-relaxed"><?= nl2br(htmlspecialchars($data["faq_{$i}_a"])); ?></p>
            </div>
          </div>
        </div>
        <?php endif; ?>
      <?php endfor; ?>
    </div>
  </div>
</section>

<!-- ================= CTA ================= -->
<section class="py-32 relative overflow-hidden bg-gradient-to-br from-primary via-purple-600 to-secondary">
  <!-- Animated Background -->
  <div class="absolute inset-0">
    <div class="absolute top-0 left-0 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
  </div>
  
  <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
    <h2 class="text-4xl md:text-6xl font-black mb-6 text-white">
      <?= htmlspecialchars($data['cta_title']); ?>
    </h2>
    <p class="text-xl md:text-2xl mb-12 text-white/90 max-w-3xl mx-auto">
      <?= htmlspecialchars($data['cta_desc']); ?>
    </p>

    <div class="flex flex-col sm:flex-row gap-6 justify-center">
      <a href="<?= htmlspecialchars($data['playstore_link']); ?>"
         target="_blank"
         class="px-12 py-5 bg-white text-primary font-bold rounded-full shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center gap-4 group">
        <i class="fab fa-google-play text-3xl"></i>
        <div class="text-left">
          <div class="text-2xl font-black">Download Sekarang</div>
          <div class="text-sm font-normal opacity-90">Gratis di Google Play</div>
        </div>
        <i class="fas fa-arrow-right text-xl group-hover:translate-x-2 transition-transform"></i>
      </a>
      
      <a href="#features"
         class="px-12 py-5 glass-effect text-white font-bold rounded-full border-2 border-white/30 hover:border-white/60 transition-all duration-300 transform hover:scale-105 flex items-center justify-center gap-4">
        <i class="fas fa-info-circle text-2xl"></i>
        <span class="text-lg">Lihat Fitur Lainnya</span>
      </a>
    </div>
    
    <p class="mt-8 text-white/70">
      <i class="fas fa-shield-alt mr-2"></i>100% Aman • <i class="fas fa-sync-alt mr-2 ml-4"></i>Update Berkala • <i class="fas fa-headset mr-2 ml-4"></i>Support 24/7
    </p>
  </div>
</section>

<!-- ================= FOOTER ================= -->
<footer class="bg-gray-900 text-white py-12">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row justify-between items-center gap-8">
      <div class="flex items-center gap-4">
        <div class="relative">
          <img src="assets/logos/<?= htmlspecialchars($data['logo']); ?>"
               class="w-12 h-12 object-contain rounded-xl shadow-lg">
          <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-gradient-to-r from-green-400 to-green-500 rounded-full animate-pulse"></div>
        </div>
        <div>
          <strong class="text-xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
            <?= htmlspecialchars($data['title']); ?>
          </strong>
          <p class="text-gray-400 text-sm">Hiburan & Utilitas dalam Genggaman</p>
        </div>
      </div>
      
      <div class="flex items-center gap-6">
        <a href="#features" class="text-gray-400 hover:text-white transition-colors">Fitur</a>
        <a href="#testimonials" class="text-gray-400 hover:text-white transition-colors">Testimoni</a>
        <a href="#faq" class="text-gray-400 hover:text-white transition-colors">FAQ</a>
        <a href="<?= htmlspecialchars($data['playstore_link']); ?>" 
           target="_blank" 
           class="px-6 py-2 bg-gradient-to-r from-primary to-secondary rounded-full font-bold hover:shadow-lg transition-all">
           Download
        </a>
      </div>
    </div>
    
    <div class="mt-8 pt-8 border-t border-gray-800 text-center text-gray-400">
      <p>© <?= date('Y'); ?> <?= htmlspecialchars($data['title']); ?>. All rights reserved.</p>
      <p class="mt-2 text-sm">Dikembangkan dengan ❤️ untuk pengalaman hiburan & utilitas terbaik</p>
    </div>
  </div>
</footer>

<!-- JavaScript -->
<script>
// FAQ Toggle
document.querySelectorAll('.faq-question').forEach(button => {
  button.addEventListener('click', () => {
    const answer = button.nextElementSibling;
    const icon = button.querySelector('i.fa-chevron-down');
    
    answer.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
  });
});

// Navbar scroll effect
window.addEventListener('scroll', () => {
  const navbar = document.getElementById('navbar');
  if (window.scrollY > 50) {
    navbar.style.background = 'rgba(255, 255, 255, 0.95)';
    navbar.style.backdropFilter = 'blur(12px)';
    navbar.style.boxShadow = '0 4px 30px rgba(0, 0, 0, 0.1)';
  } else {
    navbar.style.background = 'rgba(255, 255, 255, 0.15)';
    navbar.style.boxShadow = 'none';
  }
});

// Animate elements on scroll
const observerOptions = {
  threshold: 0.1,
  rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('animate-fade-in-up');
    }
  });
}, observerOptions);

// Observe elements to animate
document.querySelectorAll('.feature-card, .faq-question').forEach(el => {
  observer.observe(el);
});

// Add rotate class for FAQ arrows
document.head.insertAdjacentHTML('beforeend', `
  <style>
    .rotate-180 {
      transform: rotate(180deg);
    }
    .animate-fade-in-up {
      animation: fadeInUp 0.6s ease-out forwards;
    }
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
`);
</script>
</body>
</html>