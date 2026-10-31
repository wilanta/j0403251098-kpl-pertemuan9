<?php
// Koneksi database dan library aplikasi.
ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax',
]);
session_start();

$user = "root";
$pass = "";
$database = "adv_login";
$host = "localhost";

$db = new PDO(
    "mysql:host=$host;dbname=$database;charset=utf8mb4",
    $user,
    $pass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

require_once __DIR__ . "/library.php";
require_once __DIR__ . "/ClassLogin.php";
$login = new Login();
