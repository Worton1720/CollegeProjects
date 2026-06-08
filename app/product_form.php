<?php
require_once __DIR__ . '/inc/auth.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $id > 0;

if ($isEdit) {
    $st = db()->prepare('SELECT * FROM tovar WHERE id = :id');
    $st->execute([':id' => $id]);
    $tovar = $st->fetch();
    if (!$tovar) {
        flash('error', 'Товар не найден.');
        header('Location: index.php');
        exit;
    }
} else {

    $tovar = [
        'id' => null, 'artikul' => '', 'name' => '', 'id_ed_izm' => '',
        'price' => '', 'id_postavshik' => '', 'id_proizvoditel' => '',
        'id_kategoria' => '', 'skidka' => 0, 'kol_vo' => 0, 'opisanie' => '', 'foto' => '',
    ];
}

$cats     = db()->query('SELECT id, name FROM kategoria ORDER BY name')->fetchAll();
$sups     = db()->query('SELECT id, name FROM postavshik ORDER BY name')->fetchAll();
$prods    = db()->query('SELECT id, name FROM proizvoditel ORDER BY name')->fetchAll();
$units    = db()->query('SELECT id, name FROM ed_izm ORDER BY name')->fetchAll();

$pageTitle = $isEdit ? 'Редактирование товара' : 'Добавление товара';
require __DIR__ . '/inc/header.php';

$img = (!empty($tovar['foto']) && is_file(__DIR__ . '/uploads/' . $tovar['foto']))
    ? 'uploads/' . rawurlencode($tovar['foto']) : 'assets/placeholder.png';

function opt(array $rows, $selected): string
{
    $h = '';
    foreach ($rows as $r) {
        $sel = ((string)$r['id'] === (string)$selected) ? ' selected' : '';
        $h .= '<option value="' . (int)$r['id'] . "\"$sel>" . e($r['name']) . '</option>';
    }
    return $h;
}
?>
<h2 class="page-title"><?= e($pageTitle) ?></h2>
<p><a class="btn" href="index.php">← Назад к списку</a></p>

<form class="card" method="post" action="product_save.php" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= (int)($tovar['id'] ?? 0) ?>">

    <?php if ($isEdit): ?>
        <div class="field">
            <label>ID товара</label>
            <input type="text" value="<?= (int)$tovar['id'] ?>" readonly>
        </div>
    <?php endif; ?>

    <div class="field">
        <label>Фото товара</label>
        <img class="preview" id="preview" src="<?= $img ?>" alt="Фото товара">
        <input type="file" name="foto" accept="image/*" style="margin-top:8px"
               onchange="const f=this.files[0]; if(f) document.getElementById('preview').src=URL.createObjectURL(f)">
        <small style="color:#5a4326">Изображение будет приведено к 300×200 px.</small>
    </div>

    <div class="field">
        <label>Артикул *</label>
        <input type="text" name="artikul" maxlength="20" required value="<?= e($tovar['artikul']) ?>">
    </div>
    <div class="field">
        <label>Наименование товара *</label>
        <input type="text" name="name" maxlength="255" required value="<?= e($tovar['name']) ?>">
    </div>
    <div class="field">
        <label>Категория товара *</label>
        <select name="id_kategoria" required><?= opt($cats, $tovar['id_kategoria']) ?></select>
    </div>
    <div class="field">
        <label>Производитель *</label>
        <select name="id_proizvoditel" required><?= opt($prods, $tovar['id_proizvoditel']) ?></select>
    </div>
    <div class="field">
        <label>Поставщик *</label>
        <select name="id_postavshik" required><?= opt($sups, $tovar['id_postavshik']) ?></select>
    </div>
    <div class="field">
        <label>Единица измерения *</label>
        <select name="id_ed_izm" required><?= opt($units, $tovar['id_ed_izm']) ?></select>
    </div>
    <div class="field">
        <label>Цена * (₽, может включать копейки, не может быть отрицательной)</label>
        <input type="number" name="price" step="0.01" min="0" required value="<?= e((string)$tovar['price']) ?>">
    </div>
    <div class="field">
        <label>Количество на складе * (не может быть отрицательным)</label>
        <input type="number" name="kol_vo" step="1" min="0" required value="<?= e((string)$tovar['kol_vo']) ?>">
    </div>
    <div class="field">
        <label>Действующая скидка, % (0–100)</label>
        <input type="number" name="skidka" step="1" min="0" max="100" value="<?= e((string)$tovar['skidka']) ?>">
    </div>
    <div class="field">
        <label>Описание товара</label>
        <textarea name="opisanie"><?= e($tovar['opisanie']) ?></textarea>
    </div>

    <div class="actions">
        <input class="btn accent" type="submit" value="<?= $isEdit ? 'Сохранить' : 'Добавить' ?>">
        <a class="btn" href="index.php">Отмена</a>
    </div>
</form>
<?php require __DIR__ . '/inc/footer.php'; ?>
