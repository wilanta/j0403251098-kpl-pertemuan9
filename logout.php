<?php
require_once __DIR__ . "/conf/conn.php";

$login->logout();
create_alert('success', 'Anda sudah logout dari sistem', 'index.php');
