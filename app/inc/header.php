<?php

require_once __DIR__ . '/auth.php';
$pageTitle = $pageTitle ?? APP_NAME;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> — <?= e(APP_NAME) ?></title>
    <link rel="icon" href="assets/favicon.ico">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <img class="logo" src="assets/logo.png" alt="Логотип <?= e(APP_NAME) ?>">
    <h1><?= e(APP_NAME) ?></h1>
    <nav>
        <a class="btn" href="index.php">Товары</a>
        <?php if (can_manage()): ?>
            <a class="btn" href="orders.php">Заказы</a>
        <?php endif; ?>
    </nav>
    <span class="spacer"></span>
    <div class="userbox">
        <?php if (is_guest()): ?>
            <a class="btn accent" href="login.php">Вход</a>
        <?php else: ?>
            <div><?= e(current_user()['fio']) ?></div>
            <div class="role"><?= e(role_name()) ?> · <a href="logout.php">Выход</a></div>
        <?php endif; ?>
    </div>
</header>
<main>
<?php

if (!empty($_SESSION['flash'])) {
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    echo '<div class="flash ' . e($f['type']) . '">' . e($f['text']) . '</div>';
}
?>
