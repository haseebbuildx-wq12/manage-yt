<?php
// require __DIR__ . '/../app/bootstrap.php';
require __DIR__ . '/app/bootstrap.php';
use App\Core\Database;

$name = 'Admin';
$email = 'hbx@gmail.com';   // <-- yahan apna email dalein
$password = 'Haseeb4158@'; // <-- yahan apna password dalein

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = Database::pdo()->prepare(
    'INSERT INTO users (name, email, password_hash, role, status) VALUES (:n, :e, :h, "admin", "active")'
);
$stmt->execute(['n' => $name, 'e' => $email, 'h' => $hash]);
echo "Admin user created!";