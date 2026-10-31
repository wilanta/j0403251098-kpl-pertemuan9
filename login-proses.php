<?php
require_once __DIR__ . "/conf/conn.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['btn'])) {
    header('Location: index.php');
    exit();
}

if (!$login->cek_salah_login()) {
    create_alert(
        'error',
        'Mohon maaf Anda tidak dapat login lagi karena kesalahan login Anda terlalu banyak. Hubungi Administrator untuk informasi lebih lanjut',
        'index.php'
    );
}

$username = is_string($_POST['username'] ?? null) ? $_POST['username'] : '';
$password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

$cek = $db->prepare("SELECT * FROM tb_admin WHERE user = ?");
$cek->execute([$username]);
$row = $cek->fetch(PDO::FETCH_ASSOC);

if ($row && password_verify($password, $row['pass'])) {
    $expired = ($_POST['remember'] ?? '') === '1' ? '+1 year' : 0;
    $login->true_login($username, $expired);
    create_alert('success', 'Log In Berhasil', 'index.php');
} else {
    $login->salah_login_action(substr($username, 0, 50));
    create_alert('error', 'Username atau password tersebut salah', 'index.php');
}
