<?php
$pageTitle = "landing pages";
// preview.php - Live Preview Terpisah
require_once '../includes/header.php';
include '../../config.php';
mysqli_set_charset($conn, "utf8mb4");

// Cek login
if (!isset($_SESSION['admin_id'])) {
    header("Location: " . BASE_URL . "admin/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Live Preview - Mercusuar Tabungan Pintar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --primary: #10b981;
            --secondary: #059669;
        }
        
        .preview-container {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        
        .device-mockup {
            background: white;
            border-radius: 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            border: 12px solid #0f172a;
        }
        
        .device-header {
            background: #0f172a;
            padding: 1rem;
            border-bottom: 2px solid #1e293b;
        }
        
        .device-content {
            height: calc(100vh - 280px);
            min-height: 600px;
        }
        
        .control-panel {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .refresh-btn {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Back button styling */
        .back-btn {
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            transform: translateX(-5px);
        }
        
        /* Animation for device switching */
        .device-switch-animation {
            transition: all 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-900">


<div class="px-4 md:px-6 transition-all duration-300">
    <!-- Header dengan Back Button -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-6">
            <!-- Back Button -->
            <a href="landing.php" 
               class="back-btn inline-flex items-center gap-2 px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-lg transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke Editor</span>
            </a>
            
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-white">Live Preview</h1>
                <p class="text-green-300 mt-1">Pratinjau real-time landing page</p>
            </div>
            
            <div class="flex gap-3">
                <button onclick="refreshPreview()" 
                        class="refresh-btn px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center gap-2">
                    <i class="fas fa-redo"></i>
                    <span>Refresh</span>
                </button>
                <button onclick="openInNewTab()" 
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center gap-2">
                    <i class="fas fa-external-link-alt"></i>
                    <span>Buka di Tab Baru</span>
                </button>
            </div>
        </div>
        <div class="h-1 w-24 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full"></div>
    </div>

    <!-- Success Message jika dari redirect save -->
    <?php if (isset($_GET['saved']) && $_GET['saved'] == 1): ?>
    <div class="mb-6 bg-green-900/30 border border-green-700 text-green-300 px-4 py-3 rounded-lg animate-fade-in">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-3 text-green-400"></i>
            <div>
                <p class="font-medium">Perubahan berhasil disimpan!</p>
                <p class="text-sm mt-1">Landing page telah diperbarui. Klik Refresh untuk melihat perubahan.</p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Preview Container -->
    <div class="preview-container p-6">
        <!-- Control Panel -->
        <div class="control-panel rounded-xl p-4 mb-6">
            <div class="flex flex-wrap justify-between items-center gap-4">
                <div class="flex items-center gap-4">
                    <!-- Live Status -->
                    <div class="flex items-center gap-2 text-white">
                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="font-medium">Live Preview Aktif</span>
                    </div>
                    
                    <!-- Refresh Time -->
                    <div class="flex items-center gap-2 text-green-300">
                        <i class="fas fa-clock"></i>
                        <span id="refresh-time">Terakhir refresh: -</span>
                    </div>
                    
                    <!-- Page Status -->
                    <div class="flex items-center gap-2">
                        <span class="text-gray-400">Status:</span>
                        <span id="page-status" class="px-2 py-1 bg-green-900/50 text-green-300 rounded text-sm">Loading...</span>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <!-- Device Size Selector -->
                    <div class="flex bg-gray-800 rounded-lg p-1">
                        <button onclick="setDeviceSize('mobile')" 
                                class="device-btn px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white transition-colors"
                                data-size="mobile">
                            <i class="fas fa-mobile-alt"></i>
                            <span class="hidden sm:inline"> Mobile</span>
                        </button>
                        <button onclick="setDeviceSize('tablet')" 
                                class="device-btn px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white transition-colors"
                                data-size="tablet">
                            <i class="fas fa-tablet-alt"></i>
                            <span class="hidden sm:inline"> Tablet</span>
                        </button>
                        <button onclick="setDeviceSize('desktop')" 
                                class="device-btn px-3 py-2 rounded-md text-sm font-medium bg-green-600 text-white"
                                data-size="desktop">
                            <i class="fas fa-desktop"></i>
                            <span class="hidden sm:inline"> Desktop</span>
                        </button>
                    </div>
                    
                    <!-- Auto Refresh Toggle -->
                    <div class="flex items-center gap-2 text-white">
                        <span class="text-sm hidden sm:inline">Auto-refresh:</span>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="auto-refresh" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Device Preview -->
        <div class="flex justify-center device-switch-animation">
            <div id="device-container" class="device-mockup w-full max-w-4xl">
                <!-- Device Chrome -->
                <div class="device-header">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                            </div>
                            <div class="text-white text-sm font-medium">
                                preview.mercusuar-tabungan.com
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-gray-400 text-sm">
                                <i class="fas fa-wifi mr-1"></i>
                                <span>Online</span>
                            </div>
                            <div class="text-gray-400 text-sm">
                                <i class="fas fa-battery-three-quarters mr-1"></i>
                                <span>100%</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Preview Content -->
                <div class="device-content">
                    <iframe id="previewFrame"
                            src="../../index.php?preview=1&t=<?= time(); ?>"
                            class="w-full h-full border-0"
                            title="Live Preview Landing Page"></iframe>
                </div>
            </div>
        </div>

        <!-- Stats Panel -->
        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white/5 p-4 rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-300"></i>
                    </div>
                    <div>
                        <p class="text-green-200 text-sm">Status</p>
                        <p class="text-white font-bold" id="status-indicator">Active</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white/5 p-4 rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tachometer-alt text-blue-300"></i>
                    </div>
                    <div>
                        <p class="text-green-200 text-sm">Load Time</p>
                        <p class="text-white font-bold" id="load-time">0.5s</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white/5 p-4 rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-sync-alt text-purple-300"></i>
                    </div>
                    <div>
                        <p class="text-green-200 text-sm">Auto Refresh</p>
                        <p class="text-white font-bold" id="auto-refresh-status">30s</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white/5 p-4 rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-eye text-yellow-300"></i>
                    </div>
                    <div>
                        <p class="text-green-200 text-sm">View Mode</p>
                        <p class="text-white font-bold" id="view-mode">Desktop</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 flex flex-wrap justify-center gap-4">
            <button onclick="refreshPreview()" 
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-redo"></i>
                Refresh Preview
            </button>
            
            <button onclick="copyPreviewUrl()" 
                    class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-copy"></i>
                Copy URL Preview
            </button>
            
            <a href="../../index.php" target="_blank" 
               class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-globe"></i>
                Buka Live Site
            </a>
            
            <a href="index.php" 
               class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-edit"></i>
                Kembali ke Editor
            </a>
        </div>
    </div>
</div>

<script>
    // Device size state
    let currentDeviceSize = 'desktop';
    let autoRefreshInterval = null;
    let lastRefreshTime = new Date();
    
    // Update refresh time display
    function updateRefreshTime() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit',
            second: '2-digit'
        });
        document.getElementById('refresh-time').textContent = `Terakhir refresh: ${timeStr}`;
        lastRefreshTime = now;
    }
    
    // Set device size
    function setDeviceSize(size) {
        currentDeviceSize = size;
        const container = document.getElementById('device-container');
        const viewMode = document.getElementById('view-mode');
        
        // Update all device buttons
        document.querySelectorAll('.device-btn').forEach(btn => {
            btn.classList.remove('bg-green-600', 'text-white');
            btn.classList.add('text-gray-300');
        });
        
        // Activate clicked button
        event.target.classList.add('bg-green-600', 'text-white');
        event.target.classList.remove('text-gray-300');
        
        // Add animation class
        container.parentElement.classList.add('device-switch-animation');
        
        // Set container width with transition
        setTimeout(() => {
            switch(size) {
                case 'mobile':
                    container.className = 'device-mockup w-full max-w-sm mx-auto';
                    viewMode.textContent = 'Mobile';
                    break;
                case 'tablet':
                    container.className = 'device-mockup w-full max-w-2xl mx-auto';
                    viewMode.textContent = 'Tablet';
                    break;
                case 'desktop':
                    container.className = 'device-mockup w-full max-w-4xl mx-auto';
                    viewMode.textContent = 'Desktop';
                    break;
            }
            
            // Remove animation class after transition
            setTimeout(() => {
                container.parentElement.classList.remove('device-switch-animation');
            }, 300);
        }, 10);
    }
    
    // Refresh preview
    function refreshPreview() {
        const frame = document.getElementById('previewFrame');
        const randomParam = new Date().getTime();
        frame.src = frame.src.split('?')[0] + '?preview=1&t=' + randomParam;
        
        // Update UI
        const statusIndicator = document.getElementById('status-indicator');
        const pageStatus = document.getElementById('page-status');
        statusIndicator.textContent = 'Refreshing...';
        statusIndicator.className = 'text-yellow-400 font-bold';
        pageStatus.textContent = 'Loading...';
        pageStatus.className = 'px-2 py-1 bg-yellow-900/50 text-yellow-300 rounded text-sm';
        
        // Simulate load time measurement
        const startTime = performance.now();
        
        frame.onload = function() {
            const loadTime = performance.now() - startTime;
            document.getElementById('load-time').textContent = `${(loadTime / 1000).toFixed(1)}s`;
            
            statusIndicator.textContent = 'Active';
            statusIndicator.className = 'text-green-400 font-bold';
            pageStatus.textContent = 'Loaded';
            pageStatus.className = 'px-2 py-1 bg-green-900/50 text-green-300 rounded text-sm';
            updateRefreshTime();
        };
        
        // Error handling
        frame.onerror = function() {
            statusIndicator.textContent = 'Error';
            statusIndicator.className = 'text-red-400 font-bold';
            pageStatus.textContent = 'Failed to load';
            pageStatus.className = 'px-2 py-1 bg-red-900/50 text-red-300 rounded text-sm';
        };
    }
    
    // Open in new tab
    function openInNewTab() {
        const previewUrl = document.getElementById('previewFrame').src;
        window.open(previewUrl, '_blank');
    }
    
    // Copy preview URL
    function copyPreviewUrl() {
        const previewUrl = document.getElementById('previewFrame').src.replace('?preview=1', '');
        navigator.clipboard.writeText(previewUrl);
        
        // Show toast notification
        showNotification('URL berhasil disalin ke clipboard!');
    }
    
    // Show notification
    function showNotification(message) {
        // Remove existing toast
        const existingToast = document.querySelector('.preview-toast');
        if (existingToast) existingToast.remove();
        
        const toast = document.createElement('div');
        toast.className = 'preview-toast fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg animate-fade-in z-50';
        toast.innerHTML = `
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
            setTimeout(() => toast.remove(), 300);
        }, 2000);
    }
    
    // Toggle auto refresh
    document.getElementById('auto-refresh').addEventListener('change', function() {
        if (this.checked) {
            startAutoRefresh();
        } else {
            stopAutoRefresh();
        }
    });
    
    // Start auto refresh
    function startAutoRefresh() {
        if (autoRefreshInterval) clearInterval(autoRefreshInterval);
        autoRefreshInterval = setInterval(refreshPreview, 30000); // 30 seconds
        document.getElementById('auto-refresh-status').textContent = '30s';
    }
    
    // Stop auto refresh
    function stopAutoRefresh() {
        if (autoRefreshInterval) clearInterval(autoRefreshInterval);
        autoRefreshInterval = null;
        document.getElementById('auto-refresh-status').textContent = 'Off';
    }
    
    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Set initial device size
        setDeviceSize('desktop');
        
        // Start auto refresh
        startAutoRefresh();
        
        // Initial refresh time
        updateRefreshTime();
        
        // Load time measurement
        const startTime = performance.now();
        const frame = document.getElementById('previewFrame');
        
        frame.onload = function() {
            const loadTime = performance.now() - startTime;
            document.getElementById('load-time').textContent = `${(loadTime / 1000).toFixed(1)}s`;
            updateRefreshTime();
        };
        
        // Check iframe status periodically
        setInterval(() => {
            try {
                const frame = document.getElementById('previewFrame');
                const isLoaded = frame.contentDocument.readyState === 'complete';
                const statusIndicator = document.getElementById('status-indicator');
                const pageStatus = document.getElementById('page-status');
                
                if (isLoaded) {
                    statusIndicator.textContent = 'Active';
                    statusIndicator.className = 'text-green-400 font-bold';
                    pageStatus.textContent = 'Loaded';
                    pageStatus.className = 'px-2 py-1 bg-green-900/50 text-green-300 rounded text-sm';
                } else {
                    statusIndicator.textContent = 'Loading...';
                    statusIndicator.className = 'text-yellow-400 font-bold';
                    pageStatus.textContent = 'Loading...';
                    pageStatus.className = 'px-2 py-1 bg-yellow-900/50 text-yellow-300 rounded text-sm';
                }
            } catch (e) {
                // Cross-origin error, ignore
            }
        }, 5000);
        
        // Check if we have saved parameter
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('saved') && urlParams.get('saved') === '1') {
            // Auto refresh after save
            setTimeout(refreshPreview, 1000);
        }
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + R to refresh
        if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
            e.preventDefault();
            refreshPreview();
        }
        
        // Escape to go back to editor
        if (e.key === 'Escape') {
            window.location.href = 'index.php';
        }
        
        // Number keys for device switching
        if (e.key === '1') setDeviceSize('mobile');
        if (e.key === '2') setDeviceSize('tablet');
        if (e.key === '3') setDeviceSize('desktop');
    });
</script>

</body>
</html>