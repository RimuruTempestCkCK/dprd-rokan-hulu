<?php
require_once __DIR__ . '/../config.php';
$currentPage = basename($_SERVER['PHP_SELF']); // nama file saat ini
$role = $_SESSION['role'] ?? ''; // ambil role user dari session
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h2><?= ucfirst($role) ?> Panel</h2>
        <button class="toggle-btn" id="toggleBtn"><i class="fas fa-bars"></i></button>
    </div>
    <nav class="menu">
        <?php if ($role === 'admin'): ?>
            <a href="<?= BASE_URL ?>admin/dashboard_admin.php" class="menu-item <?= $currentPage == 'dashboard_admin.php' ? 'active' : '' ?>"><i class="fas fa-home"></i><span class="menu-text">Dashboard</span></a>
            <a href="<?= BASE_URL ?>admin/kelola_user.php" class="menu-item <?= $currentPage == 'kelola_user.php' ? 'active' : '' ?>"><i class="fas fa-users"></i><span class="menu-text">Kelola User</span></a>
            <a href="<?= BASE_URL ?>admin/kelola_rapat.php" class="menu-item <?= $currentPage == 'kelola_rapat.php' ? 'active' : '' ?>"><i class="fas fa-calendar"></i><span class="menu-text">Kelola Rapat</span></a>
        <?php elseif ($role === 'dewan'): ?>
            <a href="<?= BASE_URL ?>dewan/jadwal_rapat.php" class="menu-item <?= $currentPage == 'jadwal_rapat.php' ? 'active' : '' ?>"><i class="fas fa-calendar-check"></i><span class="menu-text">Jadwal Rapat</span></a>
        <?php endif; ?>
    </nav>
</aside>
