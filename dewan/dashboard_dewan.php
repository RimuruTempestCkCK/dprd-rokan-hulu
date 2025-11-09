<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'dewan') {
    header('Location: ../login.php');
    exit();
}

// Ambil nama dewan dari session
$dewan_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Dewan';
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
?>



<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dewan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../style_dashboard.css">
        <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f6f8; margin: 0; padding: 0; }
        /* .main-content { padding: 50px; } */
        .page-title { font-size: 28px; font-weight: 600; color: #2c3e50; margin-bottom: 30px; }
        .welcome-box { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            border-radius: 15px; 
            padding: 40px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.15); 
            text-align: left; 
            max-width: 800px;
            margin: auto;
            position: relative;
            overflow: hidden;
        }
        .welcome-box h2 { font-size: 32px; margin-bottom: 15px; font-weight: 700; }
        .welcome-box p { font-size: 16px; line-height: 1.8; opacity: 0.95; margin-bottom: 25px; }
        .btn-primary { 
            display: inline-block; 
            padding: 14px 28px; 
            background: #fff; 
            color: #667eea; 
            font-weight: 600; 
            border-radius: 8px; 
            text-decoration: none; 
            transition: all 0.3s; 
            border: 2px solid rgba(255,255,255,0.3);
        }
        .btn-primary:hover { 
            background: rgba(255,255,255,0.9); 
            color: #5a67d8; 
        }
        /* Optional: Icon inside button */
        .btn-primary i { margin-right: 8px; }
        /* Optional: Small decorative circles */
        .welcome-box::before, .welcome-box::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            opacity: 0.15;
            z-index: 0;
        }
        .welcome-box::before {
            width: 200px; height: 200px; background: #fff; top: -50px; right: -50px;
        }
        .welcome-box::after {
            width: 150px; height: 150px; background: #fff; bottom: -30px; left: -30px;
        }
        .welcome-box * { position: relative; z-index: 1; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../layout/sidebar.php'; ?>

        

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header class="header"> 
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search..." id="searchInput">
                    </div>
                    <div class="header-right">
                        <!-- <button class="notification-btn" id="notificationBtn">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge">5</span>
                        </button> -->

                        <div class="user-profile" id="userProfile">
                            <div class="user-avatar">AD</div>
                            <span>Dewan</span>
                        </div>

                        <!-- Tombol Logout -->
                        <a href="../logout.php" class="btn-logout" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
            </header>

            <!-- Content -->
            <main class="content">
                <h1 class="page-title"><?= $greeting ?>, <?= htmlspecialchars($dewan_name) ?>! 👋</h1>

                <div class="welcome-box">
                    <h2>Selamat Datang di Dashboard Dewan</h2>
                    <p>Di dashboard ini, Anda dapat melihat seluruh jadwal rapat DPRD, status rapat yang sedang berlangsung, serta informasi terkait agenda penting secara cepat dan profesional.</p>
                    <a href="jadwal_rapat.php" class="btn-primary"><i class="fas fa-calendar-alt"></i> Lihat Rapat</a>
                </div>
            </main>
        </div>
    </div>

    <script src="../script.js"></script>
</body>
</html>