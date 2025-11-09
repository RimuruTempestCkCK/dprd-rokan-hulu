<?php
// Ambil protocol
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";

// BASE_URL menunjuk ke folder /dpr/ (root project)
define('BASE_URL', $protocol . $_SERVER['HTTP_HOST'] . '/dpr/');
