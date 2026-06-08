<?php
require_once __DIR__ . '/inc/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: orders.php');
    exit;
}

$id     = (int)($_POST['id'] ?? 0);
$isEdit = $id > 0;

$start  = $_POST['start_date'] ?? '';
$finish = trim($_POST['finish_date'] ?? '');
$kod    = $_POST['kod_polucheniya'] ?? '';

$errors = [];
if (!$_POST['id_client'])         $errors[] = 'Выберите клиента.';
if (!$_POST['id_status'])         $errors[] = 'Выберите статус заказа.';
if (!$_POST['id_punkt_vidachi'])  $errors[] = 'Выберите пункт выдачи.';
if ($start === '')                $errors[] = 'Укажите дату заказа.';
if ($finish !== '' && $finish < $start) $errors[] = 'Дата выдачи не может быть раньше даты заказа.';
if (!ctype_digit((string)$kod))   $errors[] = 'Код для получения должен быть числом.';

$tids = $_POST['tovar_id'] ?? [];
$qtys = $_POST['tovar_qty'] ?? [];
$items = [];
foreach ($tids as $i => $tid) {
    $tid = (int)$tid;
    $q = (int)($qtys[$i] ?? 0);
    if ($tid > 0 && $q > 0) {
        $items[$tid] = ($items[$tid] ?? 0) + $q;
    }
}
if (!$items) {
    $errors[] = 'Добавьте хотя бы один товар в заказ.';
}

if ($errors) {
    flash('error', implode(' ', $errors));
    header('Location: order_form.php' . ($isEdit ? "?id=$id" : ''));
    exit;
}

$fields = [
    'start_date'       => $start,
    'finish_date'      => $finish !== '' ? $finish : null,
    'id_punkt_vidachi' => (int)$_POST['id_punkt_vidachi'],
    'id_client'        => (int)$_POST['id_client'],
    'id_status'        => (int)$_POST['id_status'],
    'kod_polucheniya'  => (int)$kod,
];

$pdo = db();
try {
    $pdo->beginTransaction();
    if ($isEdit) {
        $set = implode(', ', array_map(fn($c) => "$c = :$c", array_keys($fields)));
        $st = $pdo->prepare("UPDATE zakaz SET $set WHERE id = :id");
        $st->execute($fields + [':id' => $id]);
        $pdo->prepare('DELETE FROM zakaz_tovar WHERE id_zakaz = :id')->execute([':id' => $id]);
        $zid = $id;
    } else {
        $zid = next_id('zakaz');
        $cols = implode(', ', array_merge(['id'], array_keys($fields)));
        $ph   = implode(', ', array_merge([':id'], array_map(fn($c) => ":$c", array_keys($fields))));
        $pdo->prepare("INSERT INTO zakaz ($cols) VALUES ($ph)")->execute($fields + [':id' => $zid]);
    }

    $ztId = next_id('zakaz_tovar');
    $ins = $pdo->prepare('INSERT INTO zakaz_tovar (id, id_zakaz, id_tovar, kol_vo) VALUES (:id, :z, :t, :q)');
    foreach ($items as $tid => $q) {
        $ins->execute([':id' => $ztId++, ':z' => $zid, ':t' => $tid, ':q' => $q]);
    }
    $pdo->commit();
    flash('ok', $isEdit ? 'Заказ обновлён.' : 'Заказ добавлен.');
} catch (PDOException $ex) {
    $pdo->rollBack();
    flash('error', 'Ошибка сохранения заказа: ' . $ex->getMessage());
    header('Location: order_form.php' . ($isEdit ? "?id=$id" : ''));
    exit;
}

header('Location: orders.php');
exit;
