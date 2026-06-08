<?php
require_once __DIR__ . '/inc/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($login === '' || $password === '') {
        $error = 'Введите логин и пароль.';
    } elseif (try_login($login, $password)) {
        header('Location: index.php');
        exit;
    } else {
        $error = 'Неверный логин или пароль. Проверьте данные и попробуйте снова.';
    }
}
$pageTitle = 'Вход';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Вход — <?= e(APP_NAME) ?></title>
    <link rel="icon" href="assets/favicon.ico">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="login-wrap">
    <img class="logo" src="assets/logo.png" alt="Логотип">
    <h1><?= e(APP_NAME) ?></h1>
    <?php if ($error): ?>
        <div class="flash error"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
        <div class="field" style="text-align:left">
            <label>Логин</label>
            <input class="grid" type="text" name="login" value="<?= e($_POST['login'] ?? '') ?>"
                   style="width:100%;padding:9px;border:1px solid #DEB887;border-radius:6px" required>
        </div>
        <div class="field" style="text-align:left;margin-top:12px">
            <label>Пароль</label>
            <input type="password" name="password"
                   style="width:100%;padding:9px;border:1px solid #DEB887;border-radius:6px" required>
        </div>
        <div style="margin-top:18px;display:flex;gap:10px;justify-content:center">
            <input class="btn accent" type="submit" value="Войти">
            <a class="btn" href="index.php">Войти как гость</a>
        </div>
    </form>
</div>
</body>
</html>
