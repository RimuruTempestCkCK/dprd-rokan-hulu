<?php
$password_admin = 'admin';
$password_dewan = 'dewan';

$hash_admin = password_hash($password_admin, PASSWORD_BCRYPT);
$hash_dewan = password_hash($password_dewan, PASSWORD_BCRYPT);

echo "Admin hash: $hash_admin<br>";
echo "Dewan hash: $hash_dewan<br>";
