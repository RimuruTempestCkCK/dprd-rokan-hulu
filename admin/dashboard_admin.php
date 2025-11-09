<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Ambil nama admin dari session
$admin_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
$current_time = date('H');
$greeting = '';

if ($current_time < 12) {
    $greeting = 'Selamat Pagi';
} elseif ($current_time < 15) {
    $greeting = 'Selamat Siang';
} elseif ($current_time < 18) {
    $greeting = 'Selamat Sore';
} else {
    $greeting = 'Selamat Malam';
}

// Koneksi database untuk statistik
require_once '../koneksi.php';

// Hitung jumlah data
$total_rapat = $pdo->query("SELECT COUNT(*) FROM rapat")->fetchColumn();
$rapat_berlangsung = $pdo->query("SELECT COUNT(*) FROM rapat WHERE status = 'Berlangsung'")->fetchColumn();
$rapat_selesai = $pdo->query("SELECT COUNT(*) FROM rapat WHERE status = 'Selesai'")->fetchColumn();
$rapat_terjadwal = $pdo->query("SELECT COUNT(*) FROM rapat WHERE status = 'Terjadwal'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - DPR RI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../style_dashboard.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../layout/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Search..." id="searchInput"></div>
                <div class="header-right">
                    <div class="user-profile"><div class="user-avatar">AD</div><span>Admin</span></div>
                    <a href="../logout.php" class="btn-logout" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
                </div>
            </header>

            <!-- Content -->
            <main class="content">
                <h1 class="page-title"><?= $greeting ?>, <?= htmlspecialchars($admin_name) ?>! 👋</h1>

                <!-- Welcome Info Box -->
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px; padding: 30px; color: white; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                        <div style="flex: 1; min-width: 250px;">
                            <h2 style="font-size: 28px; margin-bottom: 15px; font-weight: 700;">Selamat Datang di Dashboard DPR RI</h2>
                            <p style="font-size: 16px; opacity: 0.95; line-height: 1.8; margin-bottom: 20px;">
                                Sistem Manajemen Rapat DPR RI memungkinkan Anda untuk mengelola seluruh data rapat, agenda, dan informasi penting lainnya dengan mudah dan efisien.
                            </p>
                            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                                <a href="kelola_rapat.php" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); padding: 12px 25px; border-radius: 8px; text-decoration: none; color: white; font-weight: 600; border: 2px solid rgba(255,255,255,0.3); transition: all 0.3s;">
                                    <i class="fas fa-calendar-plus"></i> Kelola Rapat
                                </a>
                                <a href="../index.php" target="_blank" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); padding: 12px 25px; border-radius: 8px; text-decoration: none; color: white; font-weight: 600; border: 2px solid rgba(255,255,255,0.3); transition: all 0.3s;">
                                    <i class="fas fa-globe"></i> Lihat Website
                                </a>
                            </div>
                        </div>
                        <div style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); padding: 25px; border-radius: 12px; text-align: center; min-width: 200px;">
                            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;" id="currentDate"></div>
                            <div style="font-size: 32px; font-weight: 700;" id="currentTime"></div>
                        </div>
                    </div>
                </div>

                <!-- Info Cards -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 30px;">
                    <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); border-left: 4px solid #667eea;">
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <h3 style="font-size: 18px; color: #2c3e50; margin: 0;">Kelola Rapat</h3>
                        </div>
                        <p style="color: #7f8c8d; line-height: 1.8; margin: 0;">
                            Anda dapat membuat, mengedit, dan menghapus jadwal rapat DPR RI. Pastikan semua informasi tercatat dengan lengkap.
                        </p>
                    </div>

                    <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); border-left: 4px solid #f093fb;">
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #f093fb, #f5576c); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3 style="font-size: 18px; color: #2c3e50; margin: 0;">Monitoring Real-time</h3>
                        </div>
                        <p style="color: #7f8c8d; line-height: 1.8; margin: 0;">
                            Pantau status rapat secara real-time, dari terjadwal, berlangsung, hingga selesai dengan laporan hasil.
                        </p>
                    </div>

                    <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); border-left: 4px solid #4facfe;">
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #4facfe, #00f2fe); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h3 style="font-size: 18px; color: #2c3e50; margin: 0;">Keamanan Data</h3>
                        </div>
                        <p style="color: #7f8c8d; line-height: 1.8; margin: 0;">
                            Sistem dilengkapi keamanan tingkat tinggi untuk melindungi data dan memastikan akses hanya untuk admin.
                        </p>
                    </div>
                </div>

                <!-- Stats Grid -->
                <h2 style="font-size: 24px; color: #2c3e50; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-chart-pie"></i> Statistik Rapat
                </h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Total Rapat</h3>
                            <div class="value"><?= $total_rapat ?></div>
                            <div class="change positive">
                                <i class="fas fa-calendar-alt"></i> Semua Rapat
                            </div>
                        </div>
                        <div class="stat-icon blue">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Berlangsung</h3>
                            <div class="value"><?= $rapat_berlangsung ?></div>
                            <div class="change positive">
                                <i class="fas fa-play-circle"></i> Sedang Aktif
                            </div>
                        </div>
                        <div class="stat-icon orange">
                            <i class="fas fa-play-circle"></i>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Selesai</h3>
                            <div class="value"><?= $rapat_selesai ?></div>
                            <div class="change positive">
                                <i class="fas fa-check-circle"></i> Rapat Selesai
                            </div>
                        </div>
                        <div class="stat-icon green">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Terjadwal</h3>
                            <div class="value"><?= $rapat_terjadwal ?></div>
                            <div class="change positive">
                                <i class="fas fa-clock"></i> Akan Datang
                            </div>
                        </div>
                        <div class="stat-icon purple">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <!-- <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); margin-top: 30px;">
                    <h2 style="font-size: 24px; color: #2c3e50; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-bolt"></i> Aksi Cepat
                    </h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <a href="rapat.php" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 15px 25px; border-radius: 10px; text-decoration: none; display: flex; align-items: center; gap: 12px; font-weight: 600; transition: all 0.3s;">
                            <i class="fas fa-calendar-plus" style="font-size: 20px;"></i>
                            Tambah Rapat Baru
                        </a>
                        <a href="rapat.php" style="background: linear-gradient(135deg, #f093fb, #f5576c); color: white; padding: 15px 25px; border-radius: 10px; text-decoration: none; display: flex; align-items: center; gap: 12px; font-weight: 600; transition: all 0.3s;">
                            <i class="fas fa-list" style="font-size: 20px;"></i>
                            Lihat Semua Rapat
                        </a>
                        <a href="../index.php" target="_blank" style="background: linear-gradient(135deg, #4facfe, #00f2fe); color: white; padding: 15px 25px; border-radius: 10px; text-decoration: none; display: flex; align-items: center; gap: 12px; font-weight: 600; transition: all 0.3s;">
                            <i class="fas fa-globe" style="font-size: 20px;"></i>
                            Buka Website
                        </a>
                        <a href="../logout.php" style="background: linear-gradient(135deg, #fa709a, #fee140); color: white; padding: 15px 25px; border-radius: 10px; text-decoration: none; display: flex; align-items: center; gap: 12px; font-weight: 600; transition: all 0.3s;">
                            <i class="fas fa-cog" style="font-size: 20px;"></i>
                            Pengaturan
                        </a>
                    </div>
                </div> -->
            </main>
        </div>
    </div>

    <script src="../script.js"></script>
    <script>
        // Update waktu real-time
        function updateTime() {
            const now = new Date();
            
            const dateOptions = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            };
            const dateStr = now.toLocaleDateString('id-ID', dateOptions);
            
            const timeStr = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            
            document.getElementById('currentDate').textContent = dateStr;
            document.getElementById('currentTime').textContent = timeStr;
        }

        updateTime();
        setInterval(updateTime, 1000);
    </script>
</body>
</html>