<?php
$pageTitle = "landing pages";
require_once '../includes/header.php';
include '../../config.php';
mysqli_set_charset($conn, "utf8mb4");

// Cek login
if (!isset($_SESSION['admin_id'])) {
    header("Location: " . BASE_URL . "admin/auth/login.php");
    exit;
}

$data = mysqli_fetch_assoc(
  mysqli_query($conn,"
    SELECT * FROM landing
    ORDER BY id DESC
    LIMIT 1
  ")
);

if (!$data) {
  // Jika belum ada data, buat data default
  $default_data = [
    'id' => 0,
    'title' => 'Mercusuar Tabungan Pintar',
    'description' => 'Aplikasi tabungan digital terbaik untuk mengelola keuangan Anda dengan mudah dan aman',
    'hero_badge' => '🏆 Best Finance App 2025',
    'stat_user' => '50,000+',
    'stat_rating' => '4.8/5',
    'stat_support' => '24/7',
    'feature_1' => 'Tabungan Otomatis',
    'feature_2' => 'Analisis Keuangan',
    'feature_3' => 'Target Menabung',
    'step_1' => 'Daftar Akun',
    'step_2' => 'Set Target Tabungan',
    'step_3' => 'Pantau Pertumbuhan',
    'testi_1_name' => 'Rina Wijaya',
    'testi_1_text' => 'Bantu saya menabung 5 juta dalam 3 bulan!',
    'testi_2_name' => 'Andi Pratama',
    'testi_2_text' => 'Fitur analisis keuangannya sangat membantu perencanaan.',
    'testi_3_name' => 'Sari Dewi',
    'testi_3_text' => 'Aplikasi yang simpel tapi powerful untuk menabung.',
    'cta_title' => 'Mulai Menabung Hari Ini',
    'cta_desc' => 'Download aplikasi gratis dan raih tujuan finansial Anda',
    'playstore_link' => 'https://play.google.com/store/apps/details?id=com.mercusuar.tabungan',
    'apkpure_link' => 'https://apkpure.com/mercusuar-tabungan-pintar/com.mercusuar.tabungan',
    'cta_text' => 'Download Sekarang',
    'logo' => 'logo.png'
  ];
  
  for($i=1;$i<=4;$i++) {
    $default_data["faq_{$i}_q"] = "Pertanyaan FAQ {$i}?";
    $default_data["faq_{$i}_a"] = "Jawaban untuk FAQ {$i}.";
  }
  
  $data = $default_data;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Landing Page</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

<div class="px-4 md:px-6 transition-all duration-300">
  <!-- Header -->
  <div class="mb-8 animate-fade-in">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Kelola Landing Page</h1>
        <p class="text-gray-600 mt-2">Edit konten & lihat hasil di preview terpisah</p>
      </div>
      <div class="mt-4 md:mt-0">
        <div class="flex items-center gap-3">
          <span id="status-badge"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-300
                <?= !empty($data['is_active']) && $data['is_active']==1
                    ? 'bg-green-50 text-green-700'
                    : 'bg-red-50 text-red-700'; ?>">

            <i id="status-icon"
               class="fas fa-circle text-xs
               <?= !empty($data['is_active']) && $data['is_active']==1
                   ? 'text-green-500 animate-pulse-soft'
                   : 'text-red-500'; ?>">
            </i>

            <span id="status-text">
              <?= !empty($data['is_active']) && $data['is_active']==1 ? 'Aktif' : 'Nonaktif'; ?>
            </span>
          </span>

          <a href="preview.php"
             class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors flex items-center gap-2">
            <i class="fas fa-eye mr-1"></i>
            Live Preview
          </a>
        </div>
      </div>
    </div>
    <div class="h-1 w-24 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full"></div>
  </div>

  <!-- Success Message -->
  <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg animate-fade-in">
      <div class="flex items-center">
        <i class="fas fa-check-circle mr-3 text-green-600"></i>
        <div>
          <p class="font-medium">Perubahan berhasil disimpan!</p>
          <p class="text-sm mt-1">Lihat hasil di <a href="preview.php" class="underline font-medium">Live Preview</a></p>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Edit Content -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column - Quick Actions -->
    <div class="lg:col-span-1">
      <div class="form-section p-6 sticky top-24">
        <h3 class="font-bold text-gray-800 mb-6 text-lg">Quick Actions</h3>
        
        <!-- Status Toggle -->
        <div class="mb-6">
          <div class="flex items-center justify-between mb-3">
            <span class="font-medium text-gray-700">Status Landing</span>
            <div class="relative">
              <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox"
                       id="is_active_toggle"
                       class="sr-only peer"
                       <?= !empty($data['is_active']) && $data['is_active'] == 1 ? 'checked' : ''; ?>>

                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
              </label>
            </div>
          </div>
          <p class="text-sm text-gray-500">Aktifkan/nonaktifkan landing page</p>
        </div>
        
        <!-- Save Button -->
        <button type="submit" form="landing-form"
                class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold py-3.5 px-4 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 mb-4 flex items-center justify-center gap-3">
          <i class="fas fa-save text-lg"></i>
          Simpan Perubahan
        </button>
        
        <!-- Preview Button -->
        <a href="preview.php"
           class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium py-3 px-4 rounded-xl transition-all duration-300 mb-4 flex items-center justify-center gap-3">
          <i class="fas fa-eye mr-2"></i>
          Buka Live Preview
        </a>
        
        <!-- Reset Button -->
        <button type="button" onclick="confirmReset()"
                class="w-full bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-medium py-3 px-4 rounded-xl transition-all duration-300 flex items-center justify-center gap-3">
          <i class="fas fa-redo mr-2"></i>
          Reset ke Default
        </button>
        
        <!-- Stats Summary -->
        <div class="mt-8 pt-6 border-t border-gray-200">
          <h4 class="font-semibold text-gray-700 mb-4">Statistik Konten</h4>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-gray-600">Total Karakter:</span>
              <span class="font-medium" id="char-count">0</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Field Terisi:</span>
              <span class="font-medium" id="filled-count">0/31</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Terakhir Update:</span>
              <span class="font-medium"><?= isset($data['created_at']) ? date('d M Y H:i', strtotime($data['created_at'])) : '-'; ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column - Form -->
    <div class="lg:col-span-2">
      <form id="landing-form" action="update.php"
          method="post"
          enctype="multipart/form-data"
          class="space-y-6">
    
      <input type="hidden" name="id" value="<?= $data['id']; ?>">
    
      <input type="hidden"
             name="is_active"
             id="is_active"
             value="<?= !empty($data['is_active']) ? $data['is_active'] : 0; ?>">

        <!-- Hero Section -->
        <div class="form-section p-6 animate-fade-in">
          <div class="flex items-center gap-3 mb-6">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 w-8 h-8 rounded-lg flex items-center justify-center">
              <i class="fas fa-piggy-bank text-white text-sm"></i>
            </div>
            <h2 class="font-bold text-gray-800 text-xl">Hero Section</h2>
          </div>
          
          <div class="space-y-5">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-heading text-green-500 mr-2"></i>Judul Utama
              </label>
              <div class="input-group">
                <i class="fas fa-font"></i>
                <input name="title" 
                       class="w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                       value="<?= htmlspecialchars($data['title']); ?>"
                       placeholder="Contoh: Mercusuar Tabungan Pintar"
                       oninput="updateCharCount()">
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-align-left text-green-500 mr-2"></i>Deskripsi
              </label>
              <div class="input-group">
                <i class="fas fa-paragraph"></i>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors resize-none"
                          placeholder="Deskripsi singkat tentang aplikasi tabungan Anda"
                          oninput="updateCharCount()"><?= htmlspecialchars($data['description']); ?></textarea>
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-certificate text-green-500 mr-2"></i>Hero Badge
              </label>
              <div class="input-group">
                <i class="fas fa-award"></i>
                <input name="hero_badge" 
                       class="w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                       value="<?= htmlspecialchars($data['hero_badge']); ?>"
                       placeholder="Contoh: 🏆 Best Finance App 2025">
              </div>
            </div>
          </div>
        </div>

        <!-- Statistics Section -->
        <div class="form-section p-6 animate-fade-in">
          <div class="flex items-center gap-3 mb-6">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 w-8 h-8 rounded-lg flex items-center justify-center">
              <i class="fas fa-chart-line text-white text-sm"></i>
            </div>
            <h2 class="font-bold text-gray-800 text-xl">Statistik</h2>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-users text-green-500 mr-2"></i>Pengguna
              </label>
              <div class="input-group">
                <i class="fas fa-user-plus"></i>
                <input name="stat_user" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                       value="<?= $data['stat_user']; ?>"
                       placeholder="50,000+">
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-star text-yellow-500 mr-2"></i>Rating
              </label>
              <div class="input-group">
                <i class="fas fa-star-half-alt"></i>
                <input name="stat_rating" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors"
                       value="<?= $data['stat_rating']; ?>"
                       placeholder="4.8/5">
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-headset text-purple-500 mr-2"></i>Dukungan
              </label>
              <div class="input-group">
                <i class="fas fa-clock"></i>
                <input name="stat_support" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                       value="<?= $data['stat_support']; ?>"
                       placeholder="24/7">
              </div>
            </div>
          </div>
        </div>

        <!-- Features Section -->
        <div class="form-section p-6 animate-fade-in">
          <div class="flex items-center gap-3 mb-6">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 w-8 h-8 rounded-lg flex items-center justify-center">
              <i class="fas fa-bolt text-white text-sm"></i>
            </div>
            <h2 class="font-bold text-gray-800 text-xl">Fitur Utama</h2>
          </div>
          
          <div class="space-y-4">
            <?php for($i=1;$i<=3;$i++): ?>
              <div class="feature-item p-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  <i class="fas fa-check-circle text-green-500 mr-2"></i>Fitur <?= $i; ?>
                </label>
                <div class="input-group">
                  <i class="fas fa-cube"></i>
                  <input name="feature_<?= $i ?>" 
                         class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                         value="<?= htmlspecialchars($data["feature_$i"]); ?>"
                         placeholder="Deskripsi fitur <?= $i; ?>">
                </div>
              </div>
            <?php endfor; ?>
          </div>
        </div>

        <!-- How It Works -->
        <div class="form-section p-6 animate-fade-in">
          <div class="flex items-center gap-3 mb-6">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 w-8 h-8 rounded-lg flex items-center justify-center">
              <i class="fas fa-cogs text-white text-sm"></i>
            </div>
            <h2 class="font-bold text-gray-800 text-xl">Cara Kerja</h2>
          </div>
          
          <div class="space-y-4">
            <?php for($i=1;$i<=3;$i++): ?>
              <div class="p-4 bg-green-50 rounded-lg border-l-4 border-green-500">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  <i class="fas fa-step-forward text-green-500 mr-2"></i>Langkah <?= $i; ?>
                </label>
                <div class="input-group">
                  <i class="fas fa-list-ol"></i>
                  <input name="step_<?= $i ?>" 
                         class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                         value="<?= htmlspecialchars($data["step_$i"]); ?>"
                         placeholder="Deskripsi langkah <?= $i; ?>">
                </div>
              </div>
            <?php endfor; ?>
          </div>
        </div>

        <!-- Testimonials -->
        <div class="form-section p-6 animate-fade-in">
          <div class="flex items-center gap-3 mb-6">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 w-8 h-8 rounded-lg flex items-center justify-center">
              <i class="fas fa-comment-dots text-white text-sm"></i>
            </div>
            <h2 class="font-bold text-gray-800 text-xl">Testimoni</h2>
          </div>
          
          <div class="space-y-6">
            <?php for($i=1;$i<=3;$i++): ?>
              <div class="testimonial-card p-5">
                <div class="flex items-center gap-3 mb-4">
                  <div class="w-10 h-10 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-user"></i>
                  </div>
                  <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama <?= $i; ?></label>
                    <div class="input-group">
                      <i class="fas fa-user-tag"></i>
                      <input name="testi_<?= $i ?>_name" 
                             class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                             value="<?= htmlspecialchars($data["testi_{$i}_name"]); ?>"
                             placeholder="Nama pengguna">
                    </div>
                  </div>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Testimoni <?= $i; ?></label>
                  <div class="input-group">
                    <i class="fas fa-quote-left"></i>
                    <textarea name="testi_<?= $i ?>_text" rows="3"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors resize-none"
                              placeholder="Tulis testimoni di sini..."><?= htmlspecialchars($data["testi_{$i}_text"]); ?></textarea>
                  </div>
                </div>
              </div>
            <?php endfor; ?>
          </div>
        </div>

        <!-- FAQ Section -->
        <div class="form-section p-6 animate-fade-in">
          <div class="flex items-center gap-3 mb-6">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 w-8 h-8 rounded-lg flex items-center justify-center">
              <i class="fas fa-question-circle text-white text-sm"></i>
            </div>
            <h2 class="font-bold text-gray-800 text-xl">FAQ</h2>
          </div>
          
          <div class="space-y-6">
            <?php for($i=1;$i<=4;$i++): ?>
              <div class="p-5 bg-green-50 rounded-xl border border-green-100">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  <i class="fas fa-question text-green-500 mr-2"></i>Pertanyaan <?= $i; ?>
                </label>
                <div class="input-group mb-4">
                  <i class="fas fa-question-circle"></i>
                  <input name="faq_<?= $i ?>_q" 
                         class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                         value="<?= htmlspecialchars($data["faq_{$i}_q"]); ?>"
                         placeholder="Masukkan pertanyaan">
                </div>
                
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  <i class="fas fa-comment-alt text-green-500 mr-2"></i>Jawaban <?= $i; ?>
                </label>
                <div class="input-group">
                  <i class="fas fa-reply"></i>
                  <textarea name="faq_<?= $i ?>_a" rows="2"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors resize-none"
                            placeholder="Masukkan jawaban"><?= htmlspecialchars($data["faq_{$i}_a"]); ?></textarea>
                </div>
              </div>
            <?php endfor; ?>
          </div>
        </div>

        <!-- CTA Section -->
        <div class="form-section p-6 animate-fade-in">
          <div class="flex items-center gap-3 mb-6">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 w-8 h-8 rounded-lg flex items-center justify-center">
              <i class="fas fa-download text-white text-sm"></i>
            </div>
            <h2 class="font-bold text-gray-800 text-xl">Call to Action & Download Links</h2>
          </div>
          
          <div class="space-y-5">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-heading text-green-500 mr-2"></i>Judul CTA
              </label>
              <div class="input-group">
                <i class="fas fa-bullhorn"></i>
                <input name="cta_title" 
                       class="w-full px-4 py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                       value="<?= htmlspecialchars($data['cta_title']); ?>"
                       placeholder="Contoh: Mulai Menabung Hari Ini!">
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-align-left text-green-500 mr-2"></i>Deskripsi CTA
              </label>
              <div class="input-group">
                <i class="fas fa-paragraph"></i>
                <textarea name="cta_desc" rows="2"
                          class="w-full px-4 py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors resize-none"
                          placeholder="Deskripsi singkat untuk CTA"><?= htmlspecialchars($data['cta_desc']); ?></textarea>
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fab fa-google-play text-green-500 mr-2"></i>Link Google Play Store
              </label>
              <div class="input-group">
                <i class="fas fa-link"></i>
                <input name="playstore_link" 
                       class="w-full px-4 py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                       value="<?= htmlspecialchars($data['playstore_link']); ?>"
                       placeholder="https://play.google.com/store/apps/details?id=com.mercusuar.tabungan">
              </div>
            </div>
            
            <!-- APK Pure Link Field -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-download text-blue-500 mr-2"></i>Link APK Pure
              </label>
              <div class="input-group">
                <i class="fas fa-external-link-alt"></i>
                <input name="apkpure_link" 
                       class="w-full px-4 py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                       value="<?= isset($data['apkpure_link']) ? htmlspecialchars($data['apkpure_link']) : ''; ?>"
                       placeholder="https://apkpure.com/mercusuar-tabungan-pintar/com.mercusuar.tabungan">
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-mouse-pointer text-green-500 mr-2"></i>Teks Tombol
              </label>
              <div class="input-group">
                <i class="fas fa-hand-point-up"></i>
                <input name="cta_text" 
                       class="w-full px-4 py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                       value="<?= htmlspecialchars($data['cta_text']); ?>"
                       placeholder="Contoh: Download Gratis">
              </div>
            </div>
          </div>
        </div>

        <!-- Logo Upload -->
        <div class="form-section p-6 animate-fade-in">
          <div class="flex items-center gap-3 mb-6">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 w-8 h-8 rounded-lg flex items-center justify-center">
              <i class="fas fa-image text-white text-sm"></i>
            </div>
            <h2 class="font-bold text-gray-800 text-xl">Logo</h2>
          </div>
          
          <div class="space-y-4">
            <?php if (!empty($data['logo'])): ?>
              <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                <div class="w-16 h-16 bg-white border border-gray-300 rounded-lg flex items-center justify-center">
                  <i class="fas fa-image text-gray-400 text-2xl"></i>
                </div>
                <div>
                  <p class="font-medium text-gray-800">Logo Saat Ini</p>
                  <p class="text-sm text-gray-500"><?= $data['logo']; ?></p>
                </div>
              </div>
            <?php endif; ?>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-3">
                <i class="fas fa-upload text-gray-500 mr-2"></i>Upload Logo Baru
              </label>
              <div class="flex items-center gap-4">
                <input type="file" name="logo" id="logo-input" accept="image/*"
                       class="block w-full text-sm text-gray-500 
                              file:mr-4 file:py-3 file:px-4 
                              file:rounded-lg file:border-0 
                              file:text-sm file:font-semibold 
                              file:bg-gradient-to-r file:from-gray-100 file:to-gray-200 
                              file:text-gray-700 hover:file:bg-gradient-to-r 
                              hover:file:from-gray-200 hover:file:to-gray-300"
                       onchange="previewLogo(event)">
                <div class="w-20 h-20 bg-gray-100 border border-dashed border-gray-300 rounded-lg flex items-center justify-center" id="logo-preview">
                  <i class="fas fa-image text-gray-400 text-2xl"></i>
                </div>
              </div>
              <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG, GIF. Max: 2MB</p>
            </div>
          </div>
        </div>

        <!-- Final Save Button -->
        <div class="form-section p-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200">
          <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
              <h3 class="font-bold text-gray-800 text-lg">Siap Update Landing Page?</h3>
              <p class="text-gray-600 text-sm mt-1">Simpan perubahan dan lihat hasil di preview terpisah</p>
            </div>
            <div class="flex gap-3">
              <button type="submit"
                      class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold py-3.5 px-8 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center gap-3">
                <i class="fas fa-save text-lg"></i>
                Simpan & Lihat Preview
              </button>
              <a href="preview.php"
                 class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-3.5 px-6 rounded-xl transition-all duration-300 flex items-center gap-3">
                <i class="fas fa-eye mr-2"></i>
                Preview
              </a>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
    // Character count update
    function updateCharCount() {
        const inputs = document.querySelectorAll('input[type="text"], input[type="url"], textarea');
        let chars = 0, filled = 0;

        inputs.forEach(i => {
            if (i.value.trim()) {
                chars += i.value.length;
                filled++;
            }
        });

        document.getElementById('char-count').innerText = chars;
        document.getElementById('filled-count').innerText = `${filled}/${inputs.length}`;
    }

    // Status toggle
    const toggle = document.getElementById('is_active_toggle');
    const hidden = document.getElementById('is_active');
    const statusText = document.getElementById('status-text');
    const statusBadge = document.getElementById('status-badge');
    const statusIcon = document.getElementById('status-icon');

    if (toggle && hidden) {
        hidden.value = toggle.checked ? 1 : 0;

        toggle.addEventListener('change', function() {
            const aktif = this.checked ? 1 : 0;
            hidden.value = aktif;

            if (statusText) {
                statusText.innerText = aktif ? 'Aktif' : 'Nonaktif';
            }

            if (statusBadge) {
                statusBadge.className = 
                    'inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-300 animate-pop ' +
                    (aktif ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700');

                setTimeout(() => statusBadge.classList.remove('animate-pop'), 300);
            }

            if (statusIcon) {
                statusIcon.className =
                    'fas fa-circle text-xs transition-all duration-300 ' +
                    (aktif ? 'text-green-500 animate-pulse-soft' : 'text-red-500');
            }
        });
    }

    // Logo preview
    function previewLogo(event) {
        const reader = new FileReader();
        const preview = document.getElementById('logo-preview');
        
        reader.onload = function() {
            preview.innerHTML = `<img src="${reader.result}" class="w-full h-full object-contain rounded-lg">`;
        }
        
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    // Confirm reset
    function confirmReset() {
        if (confirm('Reset semua konten ke default? Perubahan yang belum disimpan akan hilang.')) {
            window.location.href = 'reset.php';
        }
    }

    // Form submission
    const form = document.getElementById('landing-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
            submitBtn.disabled = true;

            // Submit normally, will redirect to index.php?success=1
        });
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateCharCount();
        
        // Show success message if redirected from update
        if (window.location.search.includes('success=1')) {
            // Message already shown via PHP
        }
    });
</script>

</body>
</html>