<?php
require_once __DIR__ . '/inc/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: orders.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);

$st = db()->prepare('DELETE FROM zakaz WHERE id = :id');
$st->execute([':id' => $id]);

flash('ok', 'Заказ удалён.');
header('Location: orders.php');
exit;
