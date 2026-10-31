<?php
class Login
{
    public $db;

    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    private function cookie_options($expires)
    {
        return [
            'expires' => $expires,
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ];
    }

    public function cek_login()
    {
        // Token harus aktif, belum kedaluwarsa, dan cocok dengan identitas klien.
        $token = $_COOKIE['adv_token'] ?? '';
        if (!is_string($token) || !preg_match('/^[a-f0-9]{40}$/D', $token)) {
            return false;
        }

        $cek = $this->db->prepare(
            "SELECT * FROM tb_admin_log WHERE token = ? AND expired > ? AND stat = 1"
        );
        $cek->execute([$token, date('Y-m-d H:i:s')]);
        $row = $cek->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return false;
        }

        if ($row['ip'] !== ($_SERVER['REMOTE_ADDR'] ?? '') ||
            $row['useragent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
            return false;
        }

        $get_admin = $this->db->prepare("SELECT * FROM tb_admin WHERE user = ?");
        $get_admin->execute([$row['username']]);
        $admin = $get_admin->fetch(PDO::FETCH_ASSOC);
        if (!$admin) {
            return false;
        }

        return [
            'username' => $admin['user'],
            'name' => $admin['name'],
            'email' => $admin['email'],
            'priviledge' => $admin['priviledge'],
        ];
    }

    public function salah_login_action($username)
    {
        // NULL untuk DATETIME kosong agar kompatibel dengan strict SQL mode.
        $save = $this->db->prepare(
            "INSERT INTO tb_admin_log (tgl, expired, token, username, ip, useragent, stat)
             VALUES (?, NULL, '', ?, ?, ?, 0)"
        );
        $save->execute([
            date('Y-m-d H:i:s'),
            $username,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? '',
        ]);
        return true;
    }

    public function cek_salah_login($limit = 5)
    {
        $cek = $this->db->prepare(
            "SELECT COUNT(*) FROM tb_admin_log WHERE stat = 0 AND ip = ?"
        );
        $cek->execute([$_SERVER['REMOTE_ADDR'] ?? '']);
        return (int) $cek->fetchColumn() < $limit;
    }

    public function true_login($username, $expired)
    {
        session_regenerate_id(true);
        $expires = strtotime($expired !== 0 ? $expired : '+6 hours');
        $expireddb = date('Y-m-d H:i:s', $expires);
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        // 20 byte acak menghasilkan token 40 karakter sesuai struktur tabel modul.
        $token = bin2hex(random_bytes(20));

        $upd = $this->db->prepare(
            "UPDATE tb_admin_log SET stat = 9
             WHERE stat = 0 AND token = '' AND ip = ? AND useragent = ?"
        );
        $upd->execute([$ip, $useragent]);

        $save = $this->db->prepare(
            "INSERT INTO tb_admin_log (tgl, expired, token, username, ip, useragent, stat)
             VALUES (?, ?, ?, ?, ?, ?, 1)"
        );
        $save->execute([
            date('Y-m-d H:i:s'), $expireddb, $token, $username, $ip, $useragent,
        ]);

        setcookie('adv_token', $token, $this->cookie_options($expired !== 0 ? $expires : 0));
        return true;
    }

    public function logout()
    {
        $token = $_COOKIE['adv_token'] ?? '';
        if (is_string($token) && $token !== '') {
            $upd = $this->db->prepare("UPDATE tb_admin_log SET expired = ? WHERE token = ?");
            $upd->execute([date('Y-m-d H:i:s'), $token]);
        }

        unset($_COOKIE['adv_token']);
        setcookie('adv_token', '', $this->cookie_options(time() - 3600));
        $_SESSION = [];
        session_regenerate_id(true);
        return true;
    }

    public function login_redir()
    {
        if (!$this->cek_login()) {
            header('Location: index.php');
            exit();
        }
    }
}
