<?php
require_once __DIR__ . "/conf/conn.php";

$login_status = $login->cek_login();
if ($login_status) {
    header("Location: home.php");
    exit();
} else {
    include __DIR__ . "/login.php";
}
