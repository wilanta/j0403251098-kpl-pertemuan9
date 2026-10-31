<?php
require_once __DIR__ . "/conf/conn.php";
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form Login</title>
</head>
<body>
    <form action="login-proses.php" method="post">
        <h1>Admin Log In</h1>
        <?= show_alert(); ?>
        <label for="username">Username :</label>
        <input type="text" name="username" id="username" maxlength="50" autocomplete="username" required>
        <br>
        <label for="password">Password :</label>
        <input type="password" name="password" id="password" autocomplete="current-password" required>
        <br>
        <label for="rmb">
            <input type="checkbox" name="remember" value="1" id="rmb">
            Remember Me
        </label>
        <br>
        <button type="submit" name="btn">Log In</button>
    </form>
</body>
</html>
