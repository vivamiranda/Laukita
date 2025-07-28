<?php

session_start();
if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
    header("Location: index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Ganti dengan validasi database jika sudah ada
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['user'] = [
            'username' => $username,
            'role' => 'admin'
        ];
        header("Location: index.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - GotongID</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .login-container { max-width: 400px; margin: 80px auto; padding: 32px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #eee; }
        .login-container h2 { text-align: center; margin-bottom: 24px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 6px; }
        .form-group input { width: 100%; padding: 8px; }
        .btn-login { width: 100%; padding: 10px; background: #ff6b35; color: #fff; border: none; border-radius: 4px; font-weight: bold; }
        .error-msg { color: red; text-align: center; margin-bottom: 12px; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login Admin</h2>
        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit" class="btn-login">Login</button>
        </form>
    </div>
</body>
</html>