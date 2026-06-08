<?php
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/image.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);

$st = db()->prepare('SELECT COUNT(*) FROM zakaz_tovar WHERE id_tovar = :id');
$st->execute([':id' => $id]);
if ((int)$st->fetchColumn() > 0) {
    flash('error', 'Нельзя удалить товар: он присутствует в одном или нескольких заказах.');
    header('Location: index.php');
    exit;
}

$st = db()->prepare('SELECT foto FROM tovar WHERE id = :id');
$st->execute([':id' => $id]);
$foto = $st->fetchColumn();

$st = db()->prepare('DELETE FROM tovar WHERE id = :id');
$st->execute([':id' => $id]);
delete_product_image($foto ?: null);

flash('ok', 'Товар удалён.');
header('Location: index.php');
exit;
