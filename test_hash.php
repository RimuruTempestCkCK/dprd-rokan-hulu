<?php
require 'koneksi.php';

$stmt = $pdo->query("SELECT username, password FROM users");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<b>{$row['username']}</b>: {$row['password']}<br>";
}

echo "<hr>";

$input = 'admin';
$hash = '$2y$10$EUIgb3y53qox3KJm5y6d2um5boEwTtQWtvkrRLxY1yU3HwlqZkZpC';
echo password_verify($input, $hash) ? '✅ cocok' : '❌ salah';
