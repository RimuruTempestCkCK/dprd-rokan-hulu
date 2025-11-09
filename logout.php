<?php
session_start();

// Hapus semua session
session_unset();
session_destroy();

// Hapus cookie jika ada
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/');
}

// Redirect ke login
header('Location: login.php');
exit();
?>
