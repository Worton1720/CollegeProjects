<?php
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/image.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id      = (int)($_POST['id'] ?? 0);
$isEdit  = $id > 0;
$artikul = trim($_POST['artikul'] ?? '');
$name    = trim($_POST['name'] ?? '');
$price   = $_POST['price'] ?? '';
$kol_vo  = $_POST['kol_vo'] ?? '';
$skidka  = (int)($_POST['skidka'] ?? 0);

$errors = [];
if ($artikul === '')                       $errors[] = 'Укажите артикул.';
if ($name === '')                          $errors[] = 'Укажите наименование товара.';
if (!is_numeric($price) || $price < 0)     $errors[] = 'Цена должна быть числом и не может быть отрицательной.';
if (!ctype_digit((string)$kol_vo) && (int)$kol_vo < 0) $errors[] = 'Количество не может быть отрицательным.';
if ((int)$kol_vo < 0)                      $errors[] = 'Количество не может быть отрицательным.';
if ($skidka < 0 || $skidka > 100)          $errors[] = 'Скидка должна быть в диапазоне 0–100 %.';

$st = db()->prepare('SELECT id FROM tovar WHERE artikul = :a AND id <> :id');
$st->execute([':a' => $artikul, ':id' => $id]);
if ($st->fetch()) {
    $errors[] = 'Товар с таким артикулом уже существует.';
}

if ($errors) {
    flash('error', implode(' ', $errors));
    header('Location: product_form.php' . ($isEdit ? "?id=$id" : ''));
    exit;
}

$oldFoto = null;
if ($isEdit) {
    $st = db()->prepare('SELECT foto FROM tovar WHERE id = :id');
    $st->execute([':id' => $id]);
    $oldFoto = $st->fetchColumn() ?: null;
}
$foto = $oldFoto;
if (!empty($_FILES['foto']['name'])) {
    try {
        $foto = save_product_image($_FILES['foto'], $artikul);
        if ($oldFoto && $oldFoto !== $foto) {
            delete_product_image($oldFoto);
        }
    } catch (RuntimeException $ex) {
        flash('error', 'Изображение: ' . $ex->getMessage());
        header('Location: product_form.php' . ($isEdit ? "?id=$id" : ''));
        exit;
    }
}

$fields = [
    'artikul'         => $artikul,
    'name'            => $name,
    'id_ed_izm'       => (int)$_POST['id_ed_izm'],
    'price'           => (float)$price,
    'id_postavshik'   => (int)$_POST['id_postavshik'],
    'id_proizvoditel' => (int)$_POST['id_proizvoditel'],
    'id_kategoria'    => (int)$_POST['id_kategoria'],
    'skidka'          => $skidka,
    'kol_vo'          => (int)$kol_vo,
    'opisanie'        => trim($_POST['opisanie'] ?? ''),
    'foto'            => $foto,
];

try {
    if ($isEdit) {
        $set = implode(', ', array_map(fn($c) => "$c = :$c", array_keys($fields)));
        $st = db()->prepare("UPDATE tovar SET $set WHERE id = :id");
        $st->execute($fields + [':id' => $id]);
        flash('ok', 'Товар обновлён.');
    } else {
        $newId = next_id('tovar');
        $cols = implode(', ', array_merge(['id'], array_keys($fields)));
        $ph   = implode(', ', array_merge([':id'], array_map(fn($c) => ":$c", array_keys($fields))));
        $st = db()->prepare("INSERT INTO tovar ($cols) VALUES ($ph)");
        $st->execute($fields + [':id' => $newId]);
        flash('ok', 'Товар добавлен.');
    }
} catch (PDOException $ex) {
    flash('error', 'Ошибка сохранения: ' . $ex->getMessage());
    header('Location: product_form.php' . ($isEdit ? "?id=$id" : ''));
    exit;
}

header('Location: index.php');
exit;
